<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LapkinController extends Controller
{
    /**
     * Menampilkan daftar Laporan Kinerja.
     */
    public function index()
    {
        // Ambil data tanpa relasi 'files' karena tidak ada versioning
        $submissions = Submission::where('user_id', Auth::id())
            ->where('type', 'Laporan Kinerja')
            ->orderBy('created_at', 'desc')
            ->get();

        // Statistik
        $stats = [
            'total' => $submissions->count(),
            'pending' => $submissions->where('status', 'pending')->count(),
            'approved' => $submissions->where('status', 'approved')->count(),
            'rejected' => $submissions->where('status', 'rejected')->count(),
        ];

        return view('pelatihan.lapkin', [
            'submissions' => $submissions,
            'stats' => $stats
        ]);
    }

    /**
     * Menyimpan Laporan Kinerja baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file_upload' => 'required|mimes:pdf|max:10240', // Max 10MB
            'periode' => 'required|string',
            'tahun' => 'required|numeric',
        ]);

        $title = "Laporan Kinerja " . $request->periode . " " . $request->tahun;
        
        // 1. Upload File User
        $path = null;
        if ($request->hasFile('file_upload')) {
            $file = $request->file('file_upload');
            $path = $file->storeAs(
                'submissions/lapkin', 
                time() . '_' . $file->getClientOriginalName(), 
                'public'
            );
        }

        // 2. Simpan ke Database (Tabel Submissions saja)
        $submission = new Submission();
        $submission->user_id = Auth::id();
        $submission->category = Auth::user()->category ?? 'pelatihan';
        $submission->type = 'Laporan Kinerja';
        $submission->title = $title;
        $submission->file_path = $path; // File disimpan langsung di sini
        $submission->status = 'pending';
        $submission->save();

        return redirect()->back()->with('success', 'Laporan Kinerja berhasil dikirim.');
    }
}