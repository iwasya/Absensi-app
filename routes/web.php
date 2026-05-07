<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Atasan\ApprovalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\Petugas\AbsensiController;
use App\Http\Controllers\Petugas\CutiController;
use App\Http\Controllers\Petugas\TugasController;
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
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/home', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/{id}/read', [NotifikasiController::class, 'read'])->name('notifikasi.read');

    Route::prefix('petugas')->name('petugas.')->middleware('role:petugas')->group(function () {
        Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
        Route::post('/absensi/masuk', [AbsensiController::class, 'masuk'])->name('absensi.masuk');
        Route::post('/absensi/pulang', [AbsensiController::class, 'pulang'])->name('absensi.pulang');

        Route::get('/cuti', [CutiController::class, 'index'])->name('cuti.index');
        Route::post('/cuti', [CutiController::class, 'store'])->name('cuti.store');

        Route::get('/tugas', [TugasController::class, 'index'])->name('tugas.index');
        Route::get('/tugas/input', [TugasController::class, 'input'])->name('tugas.input');
        Route::get('/tugas/laporan', [TugasController::class, 'laporan'])->name('tugas.laporan');
        Route::get('/tugas/kalender', [TugasController::class, 'kalender'])->name('tugas.kalender');
        Route::post('/tugas', [TugasController::class, 'store'])->name('tugas.store');
    });

    Route::prefix('atasan')->name('atasan.')->middleware('role:atasan')->group(function () {
        Route::get('/absensi', [ApprovalController::class, 'absensi'])->name('absensi.index');
        Route::get('/cuti', [ApprovalController::class, 'cuti'])->name('cuti.index');
        Route::post('/cuti/{id}/approve', [ApprovalController::class, 'approveCuti'])->name('cuti.approve');
        Route::post('/cuti/{id}/reject', [ApprovalController::class, 'rejectCuti'])->name('cuti.reject');
        Route::get('/tugas', [ApprovalController::class, 'tugas'])->name('tugas.index');
        Route::post('/tugas/{id}/approve', [ApprovalController::class, 'approveTugas'])->name('tugas.approve');
        Route::post('/tugas/{id}/reject', [ApprovalController::class, 'rejectTugas'])->name('tugas.reject');
    });

    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/users', [AdminController::class, 'users'])->name('users.index');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');

        Route::get('/tempat', [AdminController::class, 'tempat'])->name('tempat.index');
        Route::post('/tempat', [AdminController::class, 'storeTempat'])->name('tempat.store');
        Route::put('/tempat/{id}', [AdminController::class, 'updateTempat'])->name('tempat.update');
        Route::delete('/tempat/{id}', [AdminController::class, 'deleteTempat'])->name('tempat.delete');

        Route::get('/periode', [AdminController::class, 'periode'])->name('periode.index');
        Route::post('/periode', [AdminController::class, 'storePeriode'])->name('periode.store');
        Route::put('/periode/{id}', [AdminController::class, 'updatePeriode'])->name('periode.update');

        Route::get('/kalender', [AdminController::class, 'kalender'])->name('kalender.index');
        Route::post('/kalender', [AdminController::class, 'storeKalender'])->name('kalender.store');
        Route::delete('/kalender/{id}', [AdminController::class, 'deleteKalender'])->name('kalender.delete');

        Route::get('/sanksi', [AdminController::class, 'sanksi'])->name('sanksi.index');
        Route::post('/sanksi', [AdminController::class, 'storeSanksi'])->name('sanksi.store');
        Route::delete('/sanksi/{id}', [AdminController::class, 'deleteSanksi'])->name('sanksi.delete');

        Route::get('/logs', [AdminController::class, 'logs'])->name('logs.index');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
