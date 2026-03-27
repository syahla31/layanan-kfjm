<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KtunDelivery;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class KtunDeliveryController extends Controller
{
    /**
     * ADMIN: Halaman utama pengiriman KTUN (Dinamis berdasarkan kategori)
     */
    public function indexAdmin(Request $request)
    {
        // Mendeteksi kategori dari URL (uji atau pelatihan)
        $category = $request->is('*pelatihan*') ? 'pelatihan' : 'uji';
        
        // Ambil instansi berdasarkan kategori untuk dropdown
        $labs = User::where('category', $category)->where('role', 'user')->get();
        
        // Ambil riwayat pengiriman berdasarkan kategori
        $deliveries = KtunDelivery::with('user')
            ->where('category', $category)
            ->latest()
            ->get();

        $viewName = ($category == 'pelatihan') ? 'pelatihan.ktun_admin' : 'uji.ktun_admin';
        return view($viewName, compact('labs', 'deliveries', 'category'));
    }

    /**
     * USER: Halaman untuk melihat KTUN yang diterima
     */
    public function indexUser()
    {
        $category = Auth::user()->category;
        
        // Ambil semua pengiriman untuk user yang sedang login
        $deliveries = KtunDelivery::where('user_id', Auth::id())->latest()->get();

        $viewName = ($category == 'pelatihan') ? 'pelatihan.ktun' : 'uji.ktun';
        return view($viewName, compact('deliveries'));
    }

    // Metode store, submitSurvey, dan destroy tetap sama karena sudah menggunakan logic dinamis
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'file_pengantar' => 'required|mimes:pdf|max:2048',
            'file_ktun' => 'required|mimes:pdf|max:2048',
            'file_kwintansi' => 'required|mimes:pdf|max:2048',
        ]);

        $user = User::findOrFail($request->user_id);

        KtunDelivery::create([
            'user_id' => $user->id,
            'category' => $user->category, // Mengambil kategori otomatis dari user tujuan
            'file_surat_pengantar' => $request->file('file_pengantar')->store('ktun/pengantar', 'public'),
            'file_ktun' => $request->file('file_ktun')->store('ktun/dokumen', 'public'),
            'file_kwintansi' => $request->file('file_kwintansi')->store('ktun/kwintansi', 'public'),
            'is_survey_filled' => false,
        ]);

        return back()->with('success', 'Paket dokumen KTUN berhasil dikirim ke instansi tujuan.');
    }

    public function submitSurvey(Request $request, $id)
    {
        $delivery = KtunDelivery::where('user_id', Auth::id())->findOrFail($id);
        $delivery->update([
            'is_survey_filled' => true,
            'survey_confirmed_at' => now(),
        ]);
        return back()->with('success', 'Terima kasih! Akses dokumen KTUN Anda sekarang telah terbuka.');
    }

    public function destroy($id)
    {
        $delivery = KtunDelivery::findOrFail($id);
        Storage::disk('public')->delete([
            $delivery->file_surat_pengantar,
            $delivery->file_ktun,
            $delivery->file_kwintansi
        ]);
        $delivery->delete();
        return back()->with('success', 'Data pengiriman berhasil dihapus.');
    }
}