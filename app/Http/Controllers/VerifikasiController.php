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
    /**
 * ADMIN: Simpan Data Verifikasi Baru (Dinamis untuk Pelatihan & Lembaga Uji)
 */
    public function store(Request $request)
    {
        // 1. Validasi kiriman data (Sekarang menyertakan aturan untuk category)
        $request->validate([
            'user_id'    => 'required|exists:users,id',
            'title'      => 'required|string|max:255',
            'admin_file' => 'required|mimes:pdf|max:2048', // Max 2MB
            'category'   => 'required|in:pelatihan,uji',  // <── Memastikan isi hanya antara 'pelatihan' atau 'uji'
        ]);

        // 2. Handle upload file PDF secara dinamis berdasarkan folder kategori
        $adminFilePath = null;
        if ($request->hasFile('admin_file')) {
            $file = $request->file('admin_file');
            
            // Memisahkan folder penyimpanan sesuai kategori agar rapi
            $folderTarget = $request->category === 'uji' ? 'verifikasi/admin' : 'submissions/verifikasi/admin';
            
            $adminFilePath = $file->storeAs(
                $folderTarget, 
                'VERIFIKASI_' . time() . '_' . $file->getClientOriginalName(), 
                'public'
            );
        }

        // 3. Simpan ke table Submissions (Menggunakan category dari form input)
        $submission = Submission::create([
            'user_id'    => $request->user_id,
            'category'   => $request->category, // <── SEKARANG DINAMIS (Membaca dari form)
            'type'       => 'Verifikasi',
            'title'      => $request->title,
            'admin_file' => $adminFilePath,
            'admin_note' => $request->admin_note,
            'file_path'  => '-', 
            'status'     => 'pending',
        ]);

        // 4. Simpan Riwayat Versi 0 ke table SubmissionFile
        SubmissionFile::create([
            'submission_id' => $submission->id,
            'version'       => 0,
            'file_path'     => '-', 
            'file_name'     => '-',
            'admin_file'    => $adminFilePath,
            'admin_note'    => $request->admin_note,
        ]);

        return redirect()->back()->with('success', 'Dokumen Verifikasi ' . ucfirst($request->category) . ' berhasil dikirim.');
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