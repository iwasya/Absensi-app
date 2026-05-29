<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\Periode;
use App\Models\User;
use App\Services\CutiReplacementService;
use App\Support\ActivityLogger;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Mengelola pengajuan cuti petugas, validasi kuota cuti,
 * pengiriman notifikasi, riwayat cuti, dan cetak dokumen cuti.
 */
class CutiController extends Controller
{
    public function index(Request $request, CutiReplacementService $replacementService): View
    {
        $periodes = Periode::orderByDesc('tanggal_mulai')->get();
        $selectedPeriode = $periodes->firstWhere('id_periode', (int) $request->query('id_periode'));
        $items = Cuti::with('pengganti')->where('id_user', $request->user()->id_user);

        if ($selectedPeriode) {
            $items->where(function ($query) use ($selectedPeriode) {
                $query->where('id_periode', $selectedPeriode->id_periode)
                    ->orWhereBetween('tanggal_mulai', [
                        $selectedPeriode->tanggal_mulai->toDateString(),
                        $selectedPeriode->tanggal_selesai->toDateString(),
                    ]);
            });
        }

        $user = $request->user();

        return view('petugas.cuti', [
            'items' => $items->latest('id_cuti')->paginate($request->get("per_page", 15))->withQueryString(),
            'periodeAktif' => Periode::aktif(),
            'periodes' => $periodes,
            'selectedPeriode' => $selectedPeriode,
            'petugasList' => $replacementService->replacementCandidatesFor($user),
            'replacementRequests' => $replacementService->pendingRequestsFor($user),
            'liburKompensasiTersedia' => $replacementService->availableCreditCountFor($user),
            'cutiTerpakaiTahunIni' => $this->jumlahCutiTahunan($request->user()->id_user, now()->year),
            'batasCutiTahunan' => 12,
        ]);
    }

    public function store(Request $request, CutiReplacementService $replacementService): RedirectResponse
    {
        $validated = $request->validate([
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'jenis_cuti' => ['required', 'in:Tahunan,Besar,Sakit,Kompensasi'],
            'alasan' => ['required', 'string'],
            'alasan_lainnya' => ['nullable', 'string'],
            'alamat_cuti' => ['required', 'string'],
            'id_pengganti' => ['required', 'exists:users,id_user'],
            'dokumen' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:4096'],
        ]);

        if (($validated['jenis_cuti'] === 'Sakit' || $validated['alasan'] === 'Sakit') && ! $request->hasFile('dokumen')) {
            return back()->withInput()->with('error', 'Cuti sakit wajib melampirkan bukti dokumen.');
        }

        $user = $request->user();
        $startDate = Carbon::parse($validated['tanggal_mulai'])->startOfDay();
        $endDate = Carbon::parse($validated['tanggal_selesai'])->startOfDay();
        $pengganti = User::with('role')->findOrFail($validated['id_pengganti']);

        if ($error = $replacementService->validateNewRequest($user, $pengganti, $startDate, $endDate, $validated['jenis_cuti'])) {
            return back()->withInput()->with('error', $error);
        }

        $tahunCuti = Carbon::parse($validated['tanggal_mulai'])->year;
        $jumlahCutiTahunan = $this->jumlahCutiTahunan($user->id_user, $tahunCuti);

        if ($validated['jenis_cuti'] !== 'Kompensasi' && $jumlahCutiTahunan >= 12) {
            return back()
                ->withInput()
                ->with('error', "Kuota cuti tahun {$tahunCuti} sudah habis. Maksimal 12 kali pengajuan cuti per tahun.");
        }

        $dokumenPath = $request->hasFile('dokumen')
            ? $request->file('dokumen')->store('cuti_dokumen', 'public')
            : null;

        $cuti = Cuti::create([
            'id_user' => $user->id_user,
            'id_periode' => optional(Periode::aktif())->id_periode,
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'jenis_cuti' => $validated['jenis_cuti'],
            'alasan' => $validated['alasan'],
            'alasan_lainnya' => $validated['alasan_lainnya'] ?? null,
            'alamat_cuti' => $validated['alamat_cuti'],
            'id_pengganti' => $validated['id_pengganti'],
            'replacement_status' => 'pending',
            'dokumen_path' => $dokumenPath,
            'admin_status' => 'pending',
            'status' => 'pending',
        ]);

        $replacementService->notifyReplacementRequested($cuti);

        ActivityLogger::log($request, 'Mengajukan cuti', 'cuti', $cuti->id_cuti, Cuti::class);

        return back()->with('success', 'Pengajuan cuti berhasil dikirim ke petugas pengganti untuk dikonfirmasi.');
    }

    public function acceptReplacement(Request $request, int $id, CutiReplacementService $replacementService): RedirectResponse
    {
        $cuti = Cuti::with(['user', 'pengganti'])->findOrFail($id);
        $user = $request->user();

        if ((int) $cuti->id_pengganti !== (int) $user->id_user) {
            abort(403);
        }

        if ($error = $replacementService->accept($cuti, $user)) {
            return back()->with('error', $error);
        }

        ActivityLogger::log($request, 'Menerima sebagai pengganti cuti', 'cuti', $cuti->id_cuti, Cuti::class);

        return back()->with('success', 'Kamu menerima sebagai pengganti cuti. Pengajuan diteruskan ke proses approval.');
    }

    public function rejectReplacement(Request $request, int $id, CutiReplacementService $replacementService): RedirectResponse
    {
        $validated = $request->validate([
            'replacement_note' => ['nullable', 'string', 'max:500'],
        ]);

        $cuti = Cuti::with('user')->findOrFail($id);
        $user = $request->user();

        if ((int) $cuti->id_pengganti !== (int) $user->id_user) {
            abort(403);
        }

        if ($error = $replacementService->reject($cuti, $user, $validated['replacement_note'] ?? null)) {
            return back()->with('error', $error);
        }

        ActivityLogger::log($request, 'Menolak sebagai pengganti cuti', 'cuti', $cuti->id_cuti, Cuti::class);

        return back()->with('success', 'Permintaan pengganti cuti ditolak.');
    }

    public function print(int $id): View
    {
        $cuti = Cuti::with(['user.tempatTugas', 'pengganti', 'approver'])->findOrFail($id);
        $user = auth()->user();

        // Authorization: only owner, atasan, or admin can view the print
        if ($cuti->id_user !== $user->id_user && !$user->isAtasan() && !$user->isAdmin()) {
            abort(403);
        }

        return view('petugas.cuti_print', compact('cuti'));
    }

    private function jumlahCutiTahunan(int $userId, int $year): int
    {
        return Cuti::where('id_user', $userId)
            ->whereYear('tanggal_mulai', $year)
            ->whereIn('status', ['pending', 'approve'])
            ->where('jenis_cuti', '!=', 'Kompensasi')
            ->count();
    }
}
