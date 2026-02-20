<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CustomLoginController extends Controller
{
    // =================================================================
    // BAGIAN 1: MENAMPILKAN HALAMAN LOGIN (GET Request)
    // =================================================================

    public function loginPelatihan()
    {
        return view('auth.custom-login', [
            'type' => 'pelatihan',
            'title' => 'Portal Lembaga Pelatihan',
            'desc' => 'Akses layanan Survailen, Verifikasi, dan pelaporan KAK khusus untuk Lembaga Pelatihan.',
            'color_theme' => 'blue', 
            'icon' => 'fa-chalkboard-teacher'
        ]);
    }

    public function loginUji()
    {
        return view('auth.custom-login', [
            'type' => 'uji',
            'title' => 'Portal Lembaga Uji',
            'desc' => 'Login khusus untuk LUK, Lab Dosimetri, dan Laboratorium Ketenaganukliran lainnya.',
            'color_theme' => 'teal', 
            'icon' => 'fa-flask'
        ]);
    }

    public function loginSinarX()
    {
        return view('auth.custom-login', [
            'type' => 'sinarx',
            'title' => 'Portal Keandalan Sinar-X',
            'desc' => 'Layanan pengajuan amandemen sertifikat uji kesesuaian pesawat sinar-X.',
            'color_theme' => 'orange', 
            'icon' => 'fa-radiation'
        ]);
    }

    public function loginInternal()
    {
        return view('auth.custom-login', [
            'type' => 'internal', // Sesuai kategori di database seeder
            'title' => 'Portal Admin Pusat',
            'desc' => 'Akses khusus untuk Administrator dan Evaluator Internal DKKN BAPETEN.',
            'color_theme' => 'red', // Tema warna Merah
            'icon' => 'fa-user-shield'
        ]);
    }

    // =================================================================
    // BAGIAN 2: PROSES LOGIN (POST Request)
    // =================================================================

    public function login(Request $request)
    {
        // 0. Paksa Logout dulu untuk membersihkan sesi lama
        Auth::logout();

        // 1. Validasi Input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'login_type' => 'required' 
        ]);

        // 2. Cari User Berdasarkan Email
        $user = User::where('email', $request->email)->first();

        // 3. Cek: Apakah user ketemu?
        if (!$user) {
            return back()->withErrors([
                'email' => 'Email tidak terdaftar dalam sistem.',
            ])->withInput();
        }

        // 4. Cek: Apakah password benar?
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'email' => 'Password yang Anda masukkan salah.',
            ])->withInput();
        }

        // 5. CEK STATUS: Apakah akun sudah diaktifkan Admin?
        if ($user->status !== 'active') {
            return back()->withErrors([
                'email' => 'Akun Anda belum aktif. Mohon tunggu proses verifikasi Admin.',
            ])->withInput();
        }

        // 6. PENJAGA PINTU: Cek category dengan TRIM (Hapus spasi & lowercase)
        // Memastikan user 'pelatihan' tidak masuk lewat portal 'uji'
        $categoryUser = trim(strtolower($user->category));
        $pintuLogin = trim(strtolower($request->login_type));

        if ($categoryUser !== $pintuLogin) {
            return back()->withErrors([
                'email' => 'AKSES DITOLAK: Akun ini terdaftar di layanan "' . strtoupper($categoryUser) . '", dilarang masuk lewat portal "' . strtoupper($pintuLogin) . '".',
            ])->withInput();
        }

        // 7. Jika Lolos Semua Pengecekan -> Login Manual
        Auth::login($user, $request->remember);
        $request->session()->regenerate();

        // 8. Arahkan ke dashboard sesuai category
        return redirect()->intended($user->category . '/dashboard');
    }

    // =================================================================
    // BAGIAN 3: LOGOUT
    // =================================================================

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Kembali ke halaman portal depan
        return redirect()->route('portal');
    }
}