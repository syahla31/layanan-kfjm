<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\CustomLoginController;
use App\Http\Controllers\Auth\CustomRegisterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LapkinController;
use App\Http\Controllers\SurvailenController;
use App\Http\Controllers\VerifikasiController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\KtunDeliveryController;
use App\Http\Controllers\SinarxSubmissionController;

/*
|--------------------------------------------------------------------------
| Web Routes - SI-MUTU Pro
|--------------------------------------------------------------------------
|
| Di sini adalah tempat pendaftaran rute web untuk aplikasi SI-MUTU.
| Rute dikelompokkan berdasarkan kategori layanan (Pelatihan, Uji, Sinar-X)
| dan peran pengguna (Admin/Internal vs User/Lembaga).
|
*/

// =========================================================================
// 1. HALAMAN PUBLIK / PORTAL DEPAN
// =========================================================================
Route::get('/', function () {
    return view('welcome');
})->name('portal');

Route::get('/sertifikasi', function () {
    return view('sertifikasi.index');
})->name('sertifikasi.index');


// =========================================================================
// 2. RUTE OTENTIKASI (LOGIN & REGISTER)
// =========================================================================

// --- Portal Login Admin Pusat ---
Route::get('/admin/login', [CustomLoginController::class, 'loginInternal'])->name('login.internal');

// --- Modul Pelatihan ---
Route::get('/pelatihan/login', [CustomLoginController::class, 'loginPelatihan'])->name('login.pelatihan');
Route::get('/pelatihan/register', [CustomRegisterController::class, 'showRegisterPelatihan'])->name('register.pelatihan');

// --- Modul Lembaga Uji ---
Route::get('/uji/login', [CustomLoginController::class, 'loginUji'])->name('login.uji');
Route::get('/uji/register', [CustomRegisterController::class, 'showRegisterUji'])->name('register.uji');

// --- Modul Sinar-X ---
Route::get('/sinarx/login', [CustomLoginController::class, 'loginSinarX'])->name('login.sinarx');
Route::get('/sinarx/register', [CustomRegisterController::class, 'showRegisterSinarX'])->name('register.sinarx');


// =========================================================================
// 3. RUTE TERPROTEKSI (MEMERLUKAN LOGIN & VERIFIKASI)
// =========================================================================
Route::middleware(['auth', 'verified'])->group(function () {

    // --- Redirect Otomatis Berdasarkan Kategori Setelah Login ---
    Route::get('/dashboard', function () {
        $user = auth()->user();
        return redirect($user->category . '/dashboard');
    })->name('dashboard');

    // ---------------------------------------------------------------------
    // A. KATEGORI: INTERNAL (SUPER ADMIN / ADMIN PUSAT)
    // ---------------------------------------------------------------------
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

        // Aksi Manajemen User (Approval & Export)
        Route::get('/export-users', [DashboardController::class, 'exportUsers'])->name('internal.users.export');
        Route::post('/approve/{id}', [DashboardController::class, 'approveUser'])->name('internal.approve');
        Route::post('/reject/{id}', [DashboardController::class, 'rejectUser'])->name('internal.reject');
    });

    // ---------------------------------------------------------------------
    // B. KATEGORI: PELATIHAN
    // ---------------------------------------------------------------------
    Route::prefix('pelatihan')->group(function () {
        // Dashboard
        Route::get('/dashboard', function () {
            return auth()->user()->role == 'admin'
                ? view('pelatihan.dashboard_admin')
                : view('pelatihan.dashboard');
        })->name('pelatihan.dashboard');

        // Dokumen KAK
        Route::get('/kak', function () {
            return view('pelatihan.kak');
        })->name('pelatihan.kak');

        // Laporan Kinerja (LAPKIN)
        Route::get('/lapkin', [LapkinController::class, 'index'])->name('lapkin.index');
        Route::post('/lapkin/store', [LapkinController::class, 'store'])->name('lapkin.store');

        // Survailen Pelatihan (Alur Baru: User Upload 8 File)
        // Survailen Pelatihan (Desk Evaluation)
        Route::get('/survailen', [SurvailenController::class, 'index'])->name('survailen.index');
        Route::get('/survailen/manage', [SurvailenController::class, 'adminIndex'])->name('survailen.admin');
        Route::post('/survailen/evaluate/{id}', [SurvailenController::class, 'evaluate'])->name('survailen.evaluate');
        Route::get('/survailen/sertifikat/{id}', [SurvailenController::class, 'generateSertifikat'])->name('survailen.generate-sertifikat');

        // Verifikasi Pelatihan
        Route::get('/verifikasi', [VerifikasiController::class, 'index'])->name('verifikasi.index');
        Route::get('/verifikasi/manage', [VerifikasiController::class, 'adminIndex'])->name('verifikasi.admin');
        Route::post('/verifikasi/store', [VerifikasiController::class, 'store'])->name('verifikasi.store');

        // Master Data Pelatihan
        Route::get('/lembaga', function () {
            return view('pelatihan.lembaga_admin');
        })->name('lembaga.admin');
        Route::get('/history', function () {
            return auth()->user()->role == 'admin' ? view('pelatihan.history') : redirect()->route('pelatihan.dashboard');
        })->name('pelatihan.history');

        // Pengiriman KTUN Pelatihan
        Route::get('/ktun-admin', [KtunDeliveryController::class, 'indexAdmin'])->name('pelatihan.ktun_admin');
        Route::get('/dokumen-ktun', [KtunDeliveryController::class, 'indexUser'])->name('pelatihan.ktun');
    });

    // ---------------------------------------------------------------------
    // C. KATEGORI: LEMBAGA UJI
    // ---------------------------------------------------------------------
    Route::prefix('uji')->group(function () {
        // Dashboard
        Route::get('/dashboard', function () {
            return auth()->user()->role == 'admin'
                ? view('uji.dashboard_admin')
                : view('uji.dashboard');
        })->name('uji.dashboard');

        // Laporan Tahunan
        Route::get('/laporan', function () {
            return view('uji.laporan');
        })->name('uji.laporan');

        // Survailen Uji (Alur Baru: Tabel Khusus)
        Route::get('/survailen', [SurvailenController::class, 'index'])->name('survailen.uji.index');
        Route::get('/survailen/manage', [SurvailenController::class, 'adminIndex'])->name('survailen.uji.admin');

        // Verifikasi Penunjukan (User & Admin)
        Route::get('/verifikasi', [VerifikasiController::class, 'ujiIndex'])->name('uji.verifikasi');
        Route::get('/verifikasi/respon/{id}', [VerifikasiController::class, 'ujiRespon'])->name('uji.verifikasi.respon');
        Route::get('/verifikasiAdmin', [VerifikasiController::class, 'adminUjiIndex'])->name('uji.verifikasi_admin');
        Route::post('/verifikasiAdmin/store', [VerifikasiController::class, 'storeAdmin'])->name('uji.verifikasi_admin.store');

        // Master Data Uji
        Route::get('/lembaga', function () {
            return view('uji.lembaga_admin');
        })->name('lembaga.adminUji');
        Route::get('/history', function () {
            return auth()->user()->role == 'admin' ? view('uji.history') : redirect()->route('uji.dashboard');
        })->name('uji.history');

        // Pengiriman KTUN Uji
        Route::get('/ktun-admin', [KtunDeliveryController::class, 'indexAdmin'])->name('uji.ktun_admin');
        Route::get('/dokumen-ktun', [KtunDeliveryController::class, 'indexUser'])->name('uji.ktun');
    });

    // ---------------------------------------------------------------------
    // D. KATEGORI: SINAR-X
    // ---------------------------------------------------------------------
    Route::prefix('sinarx')->group(function () {
        Route::get('/dashboard', function () {
            return auth()->user()->role == 'admin'
                ? view('sinarx.dashboard_admin')
                : view('sinarx.dashboard');
        })->name('sinarx.dashboard');

        // Fitur Amandemen Sinar-X (User Actions)
        Route::get('/submission', [SinarxSubmissionController::class, 'index'])->name('sinarx.submission.index');
        Route::post('/submission', [SinarxSubmissionController::class, 'store'])->name('sinarx.submission.store');
        Route::put('/submission/{id}', [SinarxSubmissionController::class, 'update'])->name('sinarx.submission.update');
        Route::delete('/submission/{id}', [SinarxSubmissionController::class, 'destroy'])->name('sinarx.submission.destroy');

        // Validasi Amandemen (Admin Actions)
        Route::post('/submission/approve/{id}', [SinarxSubmissionController::class, 'approve'])->name('sinarx.submission.approve');
        Route::post('/submission/reject/{id}', [SinarxSubmissionController::class, 'reject'])->name('sinarx.submission.reject');

        Route::get('/lembaga', function () {
            return view('sinarx.lembaga');
        })->name('lembaga.adminSinarx');
    });

    // ---------------------------------------------------------------------
    // E. AKSI GLOBAL: KTUN DELIVERY & SURVAILEN (TABEL KHUSUS)
    // ---------------------------------------------------------------------

    // Logika KTUN (3 File & Unlock via Survey)
    Route::post('/ktun/send', [KtunDeliveryController::class, 'store'])->name('ktun.store');
    Route::post('/ktun/survey/unlock/{id}', [KtunDeliveryController::class, 'submitSurvey'])->name('ktun.survey');
    Route::delete('/ktun/delete/{id}', [KtunDeliveryController::class, 'destroy'])->name('ktun.destroy');

    // Tahap 1: Simpan Self Assessment (User)
    Route::post('/survailen/store-self', [SurvailenController::class, 'storeSelfAssessment'])->name('survailen.store.self');

    // Tahap 2: Unggah 8 Dokumen (User)
    Route::post('/survailen/store-docs/{id}', [SurvailenController::class, 'storeDocuments'])->name('survailen.store.docs');

    // Tahap 3: Evaluasi/Verifikasi Asesor (Admin)
    Route::post('/survailen/evaluate-process/{id}', [SurvailenController::class, 'evaluate'])->name('survailen.evaluate');

    // Hapus Pengajuan
    Route::delete('/survailen/remove-data/{id}', [SurvailenController::class, 'destroy'])->name('survailen.destroy');

    // ---------------------------------------------------------------------
    // F. AKSI GLOBAL: SUBMISSION CRUD (UNTUK LAPKIN, VERIFIKASI, DLL)
    // ---------------------------------------------------------------------
    // --- E. GLOBAL SUBMISSION ACTIONS (CRUD UNTUK SEMUA MODUL) ---
    Route::post('/submission/store', [SubmissionController::class, 'store'])->name('submission.store');
    Route::put('/submission/update/{id}', [SubmissionController::class, 'update'])->name('submission.update');
    Route::delete('/submission/delete/{id}', [SubmissionController::class, 'destroy'])->name('submission.destroy');
    Route::post('/submission/approve/{id}', [SubmissionController::class, 'approve'])->name('submission.approve');
    Route::post('/submission/reject/{id}', [SubmissionController::class, 'reject'])->name('submission.reject');
});


// =========================================================================
// 4. RUTE PROFIL PENGGUNA
// =========================================================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/destroy', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// =========================================================================
// 5. AUTHENTICATION CORE & OVERRIDE
// =========================================================================
require __DIR__ . '/auth.php';

// Override default Laravel Breeze Auth untuk mendukung Multi-Portal
Route::post('/register', [CustomRegisterController::class, 'register'])->middleware('guest')->name('register');
Route::post('/login', [CustomLoginController::class, 'login'])->name('login');
Route::post('/logout', [CustomLoginController::class, 'logout'])->middleware('auth')->name('logout');