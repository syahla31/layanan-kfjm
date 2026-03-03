<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\CustomLoginController;
use App\Http\Controllers\Auth\CustomRegisterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LapkinController;
use App\Http\Controllers\SurvailenController;
use App\Http\Controllers\VerifikasiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\SinarxSubmissionController;

/*
|--------------------------------------------------------------------------
| Web Routes - SI-MUTU Pro
|--------------------------------------------------------------------------
*/

// 1. HALAMAN PUBLIK / DEPAN
Route::get('/', function () {
    return view('welcome');
})->name('portal');

Route::get('/sertifikasi', function () {
    return view('sertifikasi.index');
})->name('sertifikasi.index');

// 2. RUTE LOGIN & REGISTER (TAMPILAN BERDASARKAN KATEGORI)
// Admin Pusat
Route::get('/admin/login', [CustomLoginController::class, 'loginInternal'])->name('login.internal');

// Modul Pelatihan
Route::get('/pelatihan/login', [CustomLoginController::class, 'loginPelatihan'])->name('login.pelatihan');
Route::get('/pelatihan/register', [CustomRegisterController::class, 'showRegisterPelatihan'])->name('register.pelatihan');

// Modul Lembaga Uji
Route::get('/uji/login', [CustomLoginController::class, 'loginUji'])->name('login.uji');
Route::get('/uji/register', [CustomRegisterController::class, 'showRegisterUji'])->name('register.uji');

// Modul Sinar-X
Route::get('/sinarx/login', [CustomLoginController::class, 'loginSinarX'])->name('login.sinarx');
Route::get('/sinarx/register', [CustomRegisterController::class, 'showRegisterSinarX'])->name('register.sinarx');


// =========================================================================
// 3. RUTE TERPROTEKSI (SETELAH LOGIN)
// =========================================================================
Route::middleware(['auth', 'verified'])->group(function () {

    // Redirect Default berdasarkan kategori user
    Route::get('/dashboard', function () {
        $user = auth()->user();
        return redirect($user->category . '/dashboard');
    })->name('dashboard');

    // --- A. INTERNAL / SUPER ADMIN ---
    Route::prefix('internal')->group(function () {
        Route::get('/dashboard', function () {
            return view('internal.dashboard');
        });
        Route::get('/users', function () {
            return view('internal.users');
        });
        Route::get('/settings', function () {
            return view('internal.settings');
        });
        Route::get('/logs', function () {
            return view('internal.activity_log');
        });

        // Aksi Persetujuan User Baru
        Route::post('/approve/{id}', [DashboardController::class, 'approveUser'])->name('internal.approve');
        Route::post('/internal/reject/{id}', [DashboardController::class, 'rejectUser'])->name('internal.reject');
    });

    // --- B. MODUL PELATIHAN ---
    Route::prefix('pelatihan')->group(function () {
        // Dashboard (Summary / Admin Dashboard)
        Route::get('/dashboard', function () {
            if (auth()->user()->role == 'admin') {
                return view('pelatihan.dashboard_admin');
            }
            return view('pelatihan.dashboard');
        })->name('pelatihan.dashboard');

        Route::get('/kak', function () {
            return view('pelatihan.kak');
        });

        // Laporan Kinerja (LAPKIN)
        Route::get('/lapkin', [LapkinController::class, 'index'])->name('lapkin.index');
        Route::post('/lapkin', [LapkinController::class, 'store'])->name('lapkin.store');

        // Survailen Pelatihan
        Route::get('/survailen', [SurvailenController::class, 'index'])->name('survailen.index');
        Route::get('/survailen/manage', [SurvailenController::class, 'adminIndex'])->name('survailen.admin');

        // Verifikasi Pelatihan
        Route::get('/verifikasi', [VerifikasiController::class, 'index'])->name('verifikasi.index');
        Route::get('/verifikasi/manage', [VerifikasiController::class, 'adminIndex'])->name('verifikasi.admin');
        Route::post('/verifikasi/store', [VerifikasiController::class, 'store'])->name('verifikasi.store');

        // Master Data & Riwayat
        Route::get('/lembaga', function () {
            return view('pelatihan.lembaga_admin');
        })->name('lembaga.admin');
        Route::get('/history', function () {
            if (auth()->user()->role == 'admin') {
                return view('pelatihan.history');
            }
            return redirect('/pelatihan/dashboard');
        });
    });

    // --- C. MODUL LEMBAGA UJI ---
    Route::prefix('uji')->group(function () {
        // 1. Dashboard Utama (Summary / Ringkasan)
        Route::get('/dashboard', function () {
            if (auth()->user()->role == 'admin') {
                return view('uji.dashboard_admin');
            }
            return view('uji.dashboard');
        })->name('uji.dashboard');

        // 2. Laporan Tahunan (Tabel & Input Dokumen)
        Route::get('/laporan', function () {
            // Halaman ini berisi tabel Laporan Tahunan yang sebelumnya ada di dashboard
            return view('uji.laporan');
        })->name('uji.laporan');

        // 3. Survailen Uji
        Route::get('/survailen', [SurvailenController::class, 'indexUji'])->name('survailen.uji.index');
        Route::get('/survailen/manage', [SurvailenController::class, 'adminIndexUji'])->name('survailen.uji.admin');

        // 4. Verifikasi Penunjukan Uji (User Side)
        Route::get('/verifikasi', [VerifikasiController::class, 'ujiIndex'])->name('uji.verifikasi');
        Route::get('/verifikasi/respon/{id}', [VerifikasiController::class, 'ujiRespon'])->name('uji.verifikasi.respon');

        // 5. Verifikasi Penunjukan Uji (Admin Side)
        Route::get('/verifikasiAdmin', [VerifikasiController::class, 'adminUjiIndex'])->name('uji.verifikasi_admin');
        Route::post('/verifikasiAdmin/store', [VerifikasiController::class, 'storeAdmin'])->name('uji.verifikasi_admin.store');

        // Master Data & Riwayat
        Route::get('/lembaga', function () {
            return view('uji.lembaga_admin');
        })->name('lembaga.adminUji');
        
        Route::get('/history', function () {
            if (auth()->user()->role == 'admin') {
                return view('uji.history');
            }
            return redirect('/uji/dashboard');
        });
    });

    // --- D. MODUL SINAR-X ---
    Route::prefix('sinarx')->group(function () {
        Route::get('/dashboard', function () {
            if (auth()->user()->role == 'admin') {
                return view('sinarx.dashboard_admin');
            }
            return view('sinarx.dashboard');
        })->name('sinarx.dashboard');

        // =======================================================
        // TAMBAHKAN RUTE-RUTE INI UNTUK MENANGANI FORM SINAR-X
        // =======================================================

        // Aksi User (Rumah Sakit / Klinik)
        Route::post('/submission', [SinarxSubmissionController::class, 'store'])->name('sinarx.submission.store');
        Route::put('/submission/{id}', [SinarxSubmissionController::class, 'update'])->name('sinarx.submission.update');
        Route::delete('/submission/{id}', [SinarxSubmissionController::class, 'destroy'])->name('sinarx.submission.destroy');

        // Aksi Admin (Setuju / Tolak Amandemen)
        Route::post('/submission/approve/{id}', [SinarxSubmissionController::class, 'approve'])->name('sinarx.submission.approve');
        Route::post('/submission/reject/{id}', [SinarxSubmissionController::class, 'reject'])->name('sinarx.submission.reject');

        Route::get('/lembaga', function () {
            return view('sinarx.lembaga');
        })->name('lembaga.adminSinarx');
    });

    // --- E. GLOBAL SUBMISSION ACTIONS (CRUD UNTUK SEMUA MODUL) ---
    Route::post('/submission/store', [SubmissionController::class, 'store'])->name('submission.store');
    Route::put('/submission/update/{id}', [SubmissionController::class, 'update'])->name('submission.update');
    Route::delete('/submission/delete/{id}', [SubmissionController::class, 'destroy'])->name('submission.destroy');

    // Alur Verifikasi Admin (Approve/Reject)
    Route::post('/submission/approve/{id}', [SubmissionController::class, 'approve'])->name('submission.approve');
    Route::post('/submission/reject/{id}', [SubmissionController::class, 'reject'])->name('submission.reject');

    // Store khusus Survailen
    Route::post('/submission/survailen/store', [SurvailenController::class, 'store'])->name('survailen.store');
});

// 4. PROFILE ROUTES
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

// 5. AUTHENTICATION OVERRIDE
Route::post('/register', [CustomRegisterController::class, 'register'])->middleware('guest')->name('register');
Route::post('/login', [CustomLoginController::class, 'login'])->name('login');
Route::post('/logout', [CustomLoginController::class, 'logout'])->middleware('auth')->name('logout');