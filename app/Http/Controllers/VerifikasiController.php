<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VerifikasiController extends Controller
{
    /**
     * Tampilan USER: Daftar Verifikasi/Sertifikasi dari Admin (Pelatihan)
     */
    public function index()
    {
        $verifikasis = Submission::where('user_id', Auth::id())
            ->where('category', 'pelatihan')
            ->where('type', 'like', '%Verifikasi%')
            ->orderBy('created_at', 'desc')
            ->with('files')
            ->get();

        return view('pelatihan.verifikasi', compact('verifikasis'));
    }

    /**
     * Tampilan ADMIN: Manajemen Verifikasi (Pelatihan)
     */
    public function adminIndex()
    {
        $data = Submission::with(['user', 'files'])
            ->where('category', 'pelatihan')
            ->where('type', 'like', '%Verifikasi%')
            ->orderBy('created_at', 'desc')
            ->get();

        $users = User::where('role', 'user')->where('category', 'pelatihan')->get();

        return view('pelatihan.verifikasi_admin', compact('data', 'users'));
    }

    /**
     * ADMIN: Simpan Data Verifikasi Baru (Pelatihan)
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'admin_file' => 'required|mimes:pdf|max:2048', // Max 2MB
        ]);

        $adminFilePath = null;
        if ($request->hasFile('admin_file')) {
            $file = $request->file('admin_file');
            $adminFilePath = $file->storeAs(
                'submissions/verifikasi/admin', 
                'VERIFIKASI_' . time() . '_' . $file->getClientOriginalName(), 
                'public'
            );
        }

        // 1. Simpan Parent
        $submission = Submission::create([
            'user_id' => $request->user_id,
            'category' => 'pelatihan',
            'type' => 'Verifikasi',
            'title' => $request->title,
            'admin_file' => $adminFilePath,
            'admin_note' => $request->admin_note,
            'file_path' => '-', // Placeholder agar tidak error SQL 1364
            'status' => 'pending',
        ]);

        // 2. Simpan History Awal (Inisiasi Admin)
        SubmissionFile::create([
            'submission_id' => $submission->id,
            'version' => 0,
            'file_path' => '-', // Placeholder agar tidak error SQL 1364
            'file_name' => '-',
            'admin_file' => $adminFilePath,
            'admin_note' => $request->admin_note,
        ]);

        return redirect()->back()->with('success', 'Dokumen Verifikasi Pelatihan berhasil dikirim.');
    }

    /**
     * Tampilan USER: Daftar Verifikasi dari Admin (Lembaga Uji)
     */
    public function ujiIndex()
    {
        $verifikasis = Submission::where('user_id', Auth::id())
            ->where('category', 'uji')
            ->where('type', 'like', '%Verifikasi%')
            ->orderBy('created_at', 'desc')
            ->with('files')
            ->get();

        return view('uji.verifikasi', compact('verifikasis'));
    }

    /**
     * Tampilan ADMIN: Manajemen Verifikasi (Lembaga Uji)
     */
    public function adminUjiIndex()
    {
        $verifikasis = Submission::with(['user', 'files'])
            ->where('category', 'uji')
            ->where('type', 'like', '%Verifikasi%')
            ->orderBy('created_at', 'desc')
            ->get();

        $users = User::where('role', 'user')->where('category', 'uji')->get();

        return view('uji.verifikasi_admin', compact('verifikasis', 'users'));
    }

    /**
     * ADMIN: Simpan Data Verifikasi Baru (Lembaga Uji)
     */
    public function storeAdmin(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'admin_file' => 'required|mimes:pdf|max:2048', // Max 2MB
            'category' => 'required|in:pelatihan,uji',
        ]);

        $filePath = null;
        if ($request->hasFile('admin_file')) {
            $filePath = $request->file('admin_file')->store('verifikasi/admin', 'public');
        }

        // 1. Simpan Header Utama
        $submission = Submission::create([
            'user_id' => $request->user_id,
            'category' => $request->category,
            'type' => 'Verifikasi',
            'title' => $request->title,
            'status' => 'pending',
            'file_path' => '-', // Placeholder agar tidak error SQL 1364
            'admin_file' => $filePath,
            'admin_note' => $request->admin_note,
        ]);

        // 2. Simpan Riwayat Versi 0
        SubmissionFile::create([
            'submission_id' => $submission->id,
            'version' => 0,
            'file_path' => '-', // Placeholder agar tidak error SQL 1364
            'file_name' => '-',
            'admin_file' => $filePath,
            'admin_note' => $request->admin_note,
        ]);

        return back()->with('success', 'Instruksi verifikasi berhasil dikirim.');
    }

    /**
     * Halaman Respon untuk User
     */
    public function ujiRespon($id)
    {
        $verifikasi = Submission::with('files')->findOrFail($id);

        if ($verifikasi->user_id !== Auth::id()) {
            return abort(403, 'Akses tidak diizinkan.');
        }

        return view('uji.verifikasi_respon', compact('verifikasi'));
    }
}