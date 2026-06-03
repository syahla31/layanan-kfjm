<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class CustomRegisterController extends Controller
{
    // ... (Bagian showRegister tidak perlu diubah) ...
    public function showRegisterPelatihan()
    {
        return view('auth.custom-register', [
            'type' => 'pelatihan',
            'title' => 'Daftar Akun Lembaga Pelatihan',
            'desc' => 'Buat akun baru untuk mengajukan dokumen pelatihan.',
            'color_theme' => 'blue',
            'icon' => 'fa-chalkboard-teacher'
        ]);
    }

    public function showRegisterUji()
    {
        return view('auth.custom-register', [
            'type' => 'uji',
            'title' => 'Daftar Akun Lembaga Uji',
            'desc' => 'Registrasi untuk LUK, Lab Dosimetri, dan Lab lainnya.',
            'color_theme' => 'teal',
            'icon' => 'fa-flask'
        ]);
    }

    public function showRegisterSinarX()
    {
        return view('auth.custom-register', [
            'type' => 'sinarx',
            'title' => 'Daftar Akun Pemohon Amandemen Sertifikat',
            'desc' => 'Registrasi pemohon untuk amandemen sertifikat Sinar-X.',
            'color_theme' => 'orange',
            'icon' => 'fa-radiation'
        ]);
    }

    public function register(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'register_type' => ['required', 'string'],
            'kode_instansi' => ['nullable', 'string'],
            'surat_kuasa' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        // 2. Buat User Baru (PAKAI CARA MANUAL / NEW OBJECT)
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);

        // Data Kunci
        $user->category = $request->register_type;
        $user->role = 'user';
        $user->status = 'pending';
        $user->kode_instansi = $request->kode_instansi;

        // Handle Secure Upload Surat Kuasa
        if ($request->hasFile('surat_kuasa')) {
            $path = $request->file('surat_kuasa')->store('surat_kuasa', 'local');
            $user->surat_kuasa_path = $path;
        }

        // 3. Simpan ke Database
        $user->save();

        // 4. Redirect KEMBALI KE HALAMAN REGISTER (Agar popup muncul)
        return redirect()->back()
            ->with('success', 'Registrasi berhasil! Akun Anda sedang menunggu verifikasi Admin.');
    }
}
