<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminSiftController;
use App\Http\Controllers\Atasan\ApprovalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\Petugas\AbsensiController;
use App\Http\Controllers\Petugas\CutiController;
use App\Http\Controllers\Petugas\ReguController;
use App\Http\Controllers\Petugas\TugasController;
use App\Http\Controllers\Petugas\SanksiController as PetugasSanksiController;
use App\Http\Controllers\Atasan\SanksiController as AtasanSanksiController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware('prevent.back.history')->get('/', function () {
    return redirect('/login');
});

Route::view('/maintenance', 'errors.503')->name('maintenance.notice');


// Route autentikasi untuk pengguna yang belum login

Route::middleware(['guest', 'prevent.back.history'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
});

Route::middleware(['auth', 'prevent.back.history'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/home', function () {
        return redirect('/dashboard');
    });

    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/{id}/read', [NotifikasiController::class, 'read'])->name('notifikasi.read');
    Route::post('/notifikasi/read-all', [NotifikasiController::class, 'readAll'])->name('notifikasi.read-all');

    Route::post('/set-periode', [\App\Http\Controllers\Controller::class, 'setPeriode'])->name('set.periode');

    Route::get('/absensi/{id}/detail', [\App\Http\Controllers\Petugas\AbsensiController::class, 'show'])->name('absensi.detail');
    Route::get('/absensi/{absensi}/foto/{type}', [\App\Http\Controllers\Petugas\AbsensiController::class, 'photo'])->whereIn('type', ['masuk', 'pulang'])->name('absensi.photo');
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->middleware('throttle:10,1')->name('profile.update');
    Route::post('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->middleware('throttle:5,1')->name('profile.password');

    Route::prefix('petugas')->name('petugas.')->middleware('role:petugas')->group(function () {
        Route::get('/regu', [ReguController::class, 'index'])->name('regu.index');
        Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
        Route::get('/absensi/print', [AbsensiController::class, 'print'])->name('absensi.print');
        Route::post('/absensi/masuk', [AbsensiController::class, 'masuk'])->middleware('throttle:10,1')->name('absensi.masuk');
        Route::post('/absensi/pulang', [AbsensiController::class, 'pulang'])->middleware('throttle:10,1')->name('absensi.pulang');
        Route::post('/absensi/verifikasi-wajah', [AbsensiController::class, 'verifyFace'])->middleware('throttle:10,1')->name('absensi.verify-face');
        Route::post('/absensi/request-masuk', [AbsensiController::class, 'requestMasukApprovalHariIni'])->middleware('throttle:5,1')->name('absensi.request-masuk-today');
        Route::post('/absensi/{id}/request-masuk', [AbsensiController::class, 'requestMasukApproval'])->middleware('throttle:5,1')->name('absensi.request-masuk');
        Route::post('/absensi/{id}/request-pulang', [AbsensiController::class, 'requestPulangApproval'])->middleware('throttle:5,1')->name('absensi.request-pulang');
        Route::get('/approval-regu', [AbsensiController::class, 'approvalRegu'])->name('approval-regu.index');
        Route::post('/approval-regu/{id}/forward-masuk', [AbsensiController::class, 'forwardMasukApproval'])->middleware('throttle:30,1')->name('approval-regu.forward-masuk');
        Route::post('/approval-regu/{id}/reject-masuk', [AbsensiController::class, 'rejectMasukApprovalByKetua'])->middleware('throttle:30,1')->name('approval-regu.reject-masuk');
        Route::post('/approval-regu/{id}/forward', [AbsensiController::class, 'forwardPulangApproval'])->middleware('throttle:30,1')->name('approval-regu.forward');
        Route::post('/approval-regu/{id}/reject', [AbsensiController::class, 'rejectPulangApprovalByKetua'])->middleware('throttle:30,1')->name('approval-regu.reject');

        Route::get('/cuti', [CutiController::class, 'index'])->name('cuti.index');
        Route::get('/cuti/{id}/print', [CutiController::class, 'print'])->name('cuti.print');
        Route::post('/cuti', [CutiController::class, 'store'])->middleware('throttle:10,1')->name('cuti.store');
        Route::post('/cuti/{id}/pengganti/terima', [CutiController::class, 'acceptReplacement'])->middleware('throttle:20,1')->name('cuti.pengganti.terima');
        Route::post('/cuti/{id}/pengganti/tolak', [CutiController::class, 'rejectReplacement'])->middleware('throttle:20,1')->name('cuti.pengganti.tolak');

        Route::get('/tugas', [TugasController::class, 'index'])->name('tugas.index');
        Route::get('/tugas/input', [TugasController::class, 'input'])->name('tugas.input');
        Route::get('/tugas/laporan', [TugasController::class, 'laporan'])->name('tugas.laporan');
        Route::get('/tugas/laporan/print', [TugasController::class, 'printLaporan'])->name('tugas.laporan.print');
        Route::get('/tugas/kalender', [TugasController::class, 'kalender'])->name('tugas.kalender');
        Route::post('/tugas', [TugasController::class, 'store'])->middleware('throttle:20,1')->name('tugas.store');
        
        Route::get('/sanksi', [PetugasSanksiController::class, 'index'])->name('sanksi.index');
        Route::post('/sanksi/{id}/acknowledge', [PetugasSanksiController::class, 'acknowledge'])->middleware('throttle:20,1')->name('sanksi.acknowledge');
    });

    Route::prefix('atasan')->name('atasan.')->middleware('role:atasan')->group(function () {
        Route::get('/absensi', [ApprovalController::class, 'absensi'])->name('absensi.index');
        Route::get('/absensi/print', [ApprovalController::class, 'printAbsensi'])->name('absensi.print');
        Route::post('/absensi/{id}/approve-masuk', [ApprovalController::class, 'approveMasuk'])->middleware('throttle:30,1')->name('absensi.approve-masuk');
        Route::post('/absensi/{id}/reject-masuk', [ApprovalController::class, 'rejectMasuk'])->middleware('throttle:30,1')->name('absensi.reject-masuk');
        Route::post('/absensi/{id}/approve-pulang', [ApprovalController::class, 'approvePulang'])->middleware('throttle:30,1')->name('absensi.approve-pulang');
        Route::post('/absensi/{id}/reject-pulang', [ApprovalController::class, 'rejectPulang'])->middleware('throttle:30,1')->name('absensi.reject-pulang');
        Route::get('/cuti', [ApprovalController::class, 'cuti'])->name('cuti.index');
        Route::post('/cuti/{id}/approve', [ApprovalController::class, 'approveCuti'])->middleware('throttle:30,1')->name('cuti.approve');
        Route::post('/cuti/{id}/reject', [ApprovalController::class, 'rejectCuti'])->middleware('throttle:30,1')->name('cuti.reject');
        Route::get('/tugas', [ApprovalController::class, 'tugas'])->name('tugas.index');
        Route::get('/tugas/export', [ApprovalController::class, 'exportTugas'])->name('tugas.export');
        Route::post('/tugas/{id}/approve', [ApprovalController::class, 'approveTugas'])->middleware('throttle:30,1')->name('tugas.approve');
        Route::post('/tugas/{id}/reject', [ApprovalController::class, 'rejectTugas'])->middleware('throttle:30,1')->name('tugas.reject');
        Route::post('/tugas/{id}/remind', [ApprovalController::class, 'remindTugas'])->middleware('throttle:30,1')->name('tugas.remind');

        Route::get('/regu', [ApprovalController::class, 'regu'])->name('regu.index');
        Route::post('/regu', [ApprovalController::class, 'storeRegu'])->middleware('throttle:20,1')->name('regu.store');
        Route::post('/regu/update-operasional', [ApprovalController::class, 'updateReguOperasional'])->middleware('throttle:30,1')->name('regu.update-operasional');
        Route::post('/regu/ketua', [ApprovalController::class, 'setKetuaRegu'])->middleware('throttle:20,1')->name('regu.ketua');

        Route::get('/kalender', [ApprovalController::class, 'kalender'])->name('kalender.index');
        Route::get('/sanksi', [AtasanSanksiController::class, 'index'])->name('sanksi.index');
        Route::get('/sanksi/print', [AtasanSanksiController::class, 'print'])->name('sanksi.print');
        Route::post('/sanksi', [AtasanSanksiController::class, 'store'])->middleware('throttle:20,1')->name('sanksi.store');
        Route::delete('/sanksi/{id}', [AtasanSanksiController::class, 'delete'])->middleware('throttle:10,1')->name('sanksi.delete');
    });

    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/users', [AdminController::class, 'users'])->name('users.index');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
        Route::get('/users/import', [AdminController::class, 'showImportUsers'])->name('users.import.form');
        Route::get('/users/filter', [AdminController::class, 'filterUsers'])->name('users.filter');
        Route::post('/users', [AdminController::class, 'storeUser'])->middleware('throttle:20,1')->name('users.store');
        Route::put('/users/{id}', [AdminController::class, 'updateUser'])->middleware('throttle:20,1')->name('users.update');
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->middleware('throttle:10,1')->name('users.delete');
        Route::delete('/users-bulk', [AdminController::class, 'bulkDeleteUsers'])->middleware('throttle:10,1')->name('users.bulk-delete');
        Route::get('/users/template', [AdminController::class, 'downloadUsersTemplate'])->name('users.template');
        Route::post('/users/import', [AdminController::class, 'importUsers'])->middleware('throttle:5,1')->name('users.import');

        Route::get('/tempat', [AdminController::class, 'tempat'])->name('tempat.index');
        Route::post('/tempat', [AdminController::class, 'storeTempat'])->middleware('throttle:20,1')->name('tempat.store');
        Route::put('/tempat/{id}', [AdminController::class, 'updateTempat'])->middleware('throttle:20,1')->name('tempat.update');
        Route::delete('/tempat/{id}', [AdminController::class, 'deleteTempat'])->middleware('throttle:10,1')->name('tempat.delete');

        Route::get('/periode', [AdminController::class, 'periode'])->name('periode.index');
        Route::get('/periode/export', [AdminController::class, 'exportPeriode'])->name('periode.export');
        Route::post('/periode', [AdminController::class, 'storePeriode'])->middleware('throttle:20,1')->name('periode.store');
        Route::put('/periode/{id}', [AdminController::class, 'updatePeriode'])->middleware('throttle:20,1')->name('periode.update');
        Route::delete('/periode/{id}', [AdminController::class, 'deletePeriode'])->middleware('throttle:10,1')->name('periode.delete');

        Route::get('/kalender', [AdminController::class, 'kalender'])->name('kalender.index');
        Route::post('/kalender', [AdminController::class, 'storeKalender'])->middleware('throttle:20,1')->name('kalender.store');
        Route::delete('/kalender/{id}', [AdminController::class, 'deleteKalender'])->middleware('throttle:10,1')->name('kalender.delete');

        Route::get('/cuti', [AdminController::class, 'cuti'])->name('cuti.index');
        Route::get('/cuti/export', [AdminController::class, 'exportCuti'])->name('cuti.export');
        Route::get('/sanksi', [AdminController::class, 'sanksi'])->name('sanksi.index');

        Route::get('/sift', [AdminSiftController::class, 'index'])->name('sift.index');
        Route::post('/sift/shift', [AdminSiftController::class, 'storeShift'])->middleware('throttle:20,1')->name('sift.store-shift');
        Route::put('/sift/shift/{id}', [AdminSiftController::class, 'updateShift'])->middleware('throttle:20,1')->name('sift.update-shift');
        Route::post('/sift/shift/{id}/toggle', [AdminSiftController::class, 'toggleShift'])->middleware('throttle:20,1')->name('sift.toggle-shift');
        Route::delete('/sift/shift/{id}', [AdminSiftController::class, 'destroyShift'])->middleware('throttle:10,1')->name('sift.destroy-shift');
        Route::post('/sift/assign', [AdminSiftController::class, 'assignShift'])->middleware('throttle:30,1')->name('sift.assign');
        Route::post('/sift/bulk', [AdminSiftController::class, 'bulkAssignShift'])->middleware('throttle:10,1')->name('sift.bulk-assign');
        Route::get('/sift/export', [AdminSiftController::class, 'export'])->name('sift.export');
        Route::post('/absensi/{id}/approve-pulang', [AdminController::class, 'approvePulangAbsensi'])->middleware('throttle:30,1')->name('absensi.approve-pulang');

        Route::get('/buka-absen', [AdminController::class, 'bukaAksesAbsen'])->name('buka-absen.index');
        Route::post('/buka-absen', [AdminController::class, 'storeAksesAbsen'])->middleware('throttle:20,1')->name('buka-absen.store');

        Route::get('/data-sensitif', [AdminController::class, 'dataSensitif'])->name('data-sensitif.index');
        Route::post('/data-sensitif', [AdminController::class, 'updateDataSensitif'])->middleware('throttle:10,1')->name('data-sensitif.update');

        Route::get('/pengaturan', [AdminController::class, 'pengaturan'])->name('pengaturan.index');
        Route::post('/pengaturan', [AdminController::class, 'storePengaturan'])->middleware('throttle:10,1')->name('pengaturan.store');

        Route::get('/logs', [AdminController::class, 'logs'])->name('logs.index');
        Route::get('/logs/export', [AdminController::class, 'exportLogs'])->name('logs.export');

        Route::post('/clear-cache', function () {
            Artisan::call('optimize:clear');

            return response('Cache cleared successfully.');
        })->middleware('throttle:3,1')->name('clear-cache');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
