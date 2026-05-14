<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Atasan\ApprovalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\Petugas\AbsensiController;
use App\Http\Controllers\Petugas\CutiController;
use App\Http\Controllers\Petugas\TugasController;
use App\Http\Controllers\Petugas\SanksiController as PetugasSanksiController;
use App\Http\Controllers\Atasan\SanksiController as AtasanSanksiController;
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

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/home', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/{id}/read', [NotifikasiController::class, 'read'])->name('notifikasi.read');
    Route::post('/notifikasi/read-all', [NotifikasiController::class, 'readAll'])->name('notifikasi.read-all');

    Route::post('/set-periode', [\App\Http\Controllers\Controller::class, 'setPeriode'])->name('set.periode');

    Route::get('/absensi/{id}/detail', [\App\Http\Controllers\Petugas\AbsensiController::class, 'show'])->name('absensi.detail');
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->middleware('throttle:10,1')->name('profile.update');
    Route::post('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->middleware('throttle:5,1')->name('profile.password');

    Route::prefix('petugas')->name('petugas.')->middleware('role:petugas')->group(function () {
        Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
        Route::get('/absensi/print', [AbsensiController::class, 'print'])->name('absensi.print');
        Route::post('/absensi/masuk', [AbsensiController::class, 'masuk'])->middleware('throttle:10,1')->name('absensi.masuk');
        Route::post('/absensi/pulang', [AbsensiController::class, 'pulang'])->middleware('throttle:10,1')->name('absensi.pulang');

        Route::get('/cuti', [CutiController::class, 'index'])->name('cuti.index');
        Route::get('/cuti/{id}/print', [CutiController::class, 'print'])->name('cuti.print');
        Route::post('/cuti', [CutiController::class, 'store'])->middleware('throttle:10,1')->name('cuti.store');

        Route::get('/tugas', [TugasController::class, 'index'])->name('tugas.index');
        Route::get('/tugas/input', [TugasController::class, 'input'])->name('tugas.input');
        Route::get('/tugas/laporan', [TugasController::class, 'laporan'])->name('tugas.laporan');
        Route::get('/tugas/laporan/print', [TugasController::class, 'printLaporan'])->name('tugas.laporan.print');
        Route::get('/tugas/kalender', [TugasController::class, 'kalender'])->name('tugas.kalender');
        Route::post('/tugas', [TugasController::class, 'store'])->middleware('throttle:20,1')->name('tugas.store');
        
        Route::get('/sanksi', [PetugasSanksiController::class, 'index'])->name('sanksi.index');
    });

    Route::prefix('atasan')->name('atasan.')->middleware('role:atasan')->group(function () {
        Route::get('/absensi', [ApprovalController::class, 'absensi'])->name('absensi.index');
        Route::get('/absensi/print', [ApprovalController::class, 'printAbsensi'])->name('absensi.print');
        Route::get('/cuti', [ApprovalController::class, 'cuti'])->name('cuti.index');
        Route::post('/cuti/{id}/approve', [ApprovalController::class, 'approveCuti'])->middleware('throttle:30,1')->name('cuti.approve');
        Route::post('/cuti/{id}/reject', [ApprovalController::class, 'rejectCuti'])->middleware('throttle:30,1')->name('cuti.reject');
        Route::get('/tugas', [ApprovalController::class, 'tugas'])->name('tugas.index');
        Route::post('/tugas/{id}/approve', [ApprovalController::class, 'approveTugas'])->middleware('throttle:30,1')->name('tugas.approve');
        Route::post('/tugas/{id}/reject', [ApprovalController::class, 'rejectTugas'])->middleware('throttle:30,1')->name('tugas.reject');

        Route::get('/kalender', [ApprovalController::class, 'kalender'])->name('kalender.index');
        Route::get('/sanksi', [AtasanSanksiController::class, 'index'])->name('sanksi.index');
        Route::get('/sanksi/print', [AtasanSanksiController::class, 'print'])->name('sanksi.print');
        Route::post('/sanksi', [AtasanSanksiController::class, 'store'])->middleware('throttle:20,1')->name('sanksi.store');
        Route::delete('/sanksi/{id}', [AtasanSanksiController::class, 'delete'])->middleware('throttle:10,1')->name('sanksi.delete');
    });

    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/users', [AdminController::class, 'users'])->name('users.index');
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
        Route::post('/periode', [AdminController::class, 'storePeriode'])->middleware('throttle:20,1')->name('periode.store');
        Route::put('/periode/{id}', [AdminController::class, 'updatePeriode'])->middleware('throttle:20,1')->name('periode.update');
        Route::delete('/periode/{id}', [AdminController::class, 'deletePeriode'])->middleware('throttle:10,1')->name('periode.delete');

        Route::get('/kalender', [AdminController::class, 'kalender'])->name('kalender.index');
        Route::post('/kalender', [AdminController::class, 'storeKalender'])->middleware('throttle:20,1')->name('kalender.store');
        Route::delete('/kalender/{id}', [AdminController::class, 'deleteKalender'])->middleware('throttle:10,1')->name('kalender.delete');

        Route::get('/sanksi', [AdminController::class, 'sanksi'])->name('sanksi.index');

        Route::get('/buka-absen', [AdminController::class, 'bukaAksesAbsen'])->name('buka-absen.index');
        Route::post('/buka-absen', [AdminController::class, 'storeAksesAbsen'])->middleware('throttle:20,1')->name('buka-absen.store');

        Route::get('/data-sensitif', [AdminController::class, 'dataSensitif'])->name('data-sensitif.index');
        Route::post('/data-sensitif', [AdminController::class, 'updateDataSensitif'])->middleware('throttle:10,1')->name('data-sensitif.update');

        Route::get('/pengaturan', [AdminController::class, 'pengaturan'])->name('pengaturan.index');
        Route::post('/pengaturan', [AdminController::class, 'storePengaturan'])->middleware('throttle:10,1')->name('pengaturan.store');

        Route::get('/logs', [AdminController::class, 'logs'])->name('logs.index');
        Route::get('/logs/export', [AdminController::class, 'exportLogs'])->name('logs.export');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
