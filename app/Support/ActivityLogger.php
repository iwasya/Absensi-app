<?php

namespace App\Support;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log(
        Request $request,
        string $aktivitas,
        string $modul,
        ?int $referenceId = null,
        ?string $referenceType = null,
        string $status = 'success',
        ?string $catatan = null
    ): void {
        ActivityLog::create([
            'id_user' => Auth::id(),
            'aktivitas' => $aktivitas,
            'modul' => $modul,
            'reference_id' => $referenceId,
            'reference_type' => $referenceType,
            'status' => $status,
            'catatan' => $catatan,
            'ip_address' => $request->ip(),
            'device' => substr((string) $request->userAgent(), 0, 150),
        ]);
    }
}
