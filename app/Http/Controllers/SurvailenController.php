<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\SubmissionFile; 
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SurvailenController extends Controller
{
    public function index()
    {
        $survailens = Submission::where('user_id', Auth::id())
            ->where('type', 'Survailen')
            ->orderBy('created_at', 'desc')
            ->with('files') 
            ->get();

        return view('pelatihan.survailen', compact('survailens'));
    }

    public function adminIndex()
    {
        $audits = Submission::with(['user', 'files']) 
            ->where('category', 'pelatihan')
            ->where('type', 'Survailen')
            ->orderBy('created_at', 'desc')
            ->get();

        $users = User::where('role', 'user')->where('category', 'pelatihan')->get();

        return view('pelatihan.survailen_admin', compact('audits', 'users'));
    }

    // ================== BAGIAN LEMBAGA UJI (BARU) ==================
    
    // 1. Tampilan untuk User Lembaga Uji
    public function indexUji()
    {
        $survailens = Submission::where('user_id', Auth::id())
            ->where('category', 'uji') // Filter KHUSUS UJI
            ->where('type', 'Survailen')
            ->orderBy('created_at', 'desc')
            ->with('files') 
            ->get();

        // Arahkan ke view folder 'uji'
        return view('uji.survailen', compact('survailens')); 
    }

    // 2. Tampilan untuk Admin Uji
    public function adminIndexUji()
    {
        $audits = Submission::with(['user', 'files']) 
            ->where('category', 'uji') // Filter KHUSUS UJI
            ->where('type', 'Survailen')
            ->orderBy('created_at', 'desc')
            ->get();

        // Ambil hanya user kategori 'uji'
        $users = User::where('role', 'user')->where('category', 'uji')->get();

        // Arahkan ke view folder 'uji'
        return view('uji.survailen_admin', compact('audits', 'users'));
    }

    // ================== PROSES SIMPAN (UPDATE) ==================
    
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'admin_file' => 'required|mimes:pdf|max:10240',
            'category' => 'required|in:pelatihan,uji', // Validasi kategori
        ]);

        $adminFilePath = null;
        $adminFileName = null;

        if ($request->hasFile('admin_file')) {
            $file = $request->file('admin_file');
            $adminFileName = $file->getClientOriginalName();
            // Simpan di folder sesuai kategori agar rapi
            $folder = 'submissions/survailen/' . $request->category . '/admin';
            
            $adminFilePath = $file->storeAs(
                $folder, 
                'SURVAILEN_ADMIN_' . time() . '_' . $adminFileName, 
                'public'
            );
        }

        // 1. Simpan ke Parent (Submissions)
        $submission = new Submission();
        $submission->user_id = $request->user_id;
        
        // PENTING: Ambil kategori dari input form (hidden input)
        $submission->category = $request->category; 
        
        $submission->type = 'Survailen';
        $submission->title = $request->title;
        $submission->admin_file = $adminFilePath;
        $submission->admin_note = $request->admin_note;
        $submission->file_path = null; 
        $submission->status = 'pending'; 
        $submission->save();

        // 2. Simpan ke History
        $history = new SubmissionFile();
        $history->submission_id = $submission->id;
        $history->version = 0; 
        $history->file_path = '-'; 
        $history->file_name = '-';
        $history->admin_file = $adminFilePath; 
        $history->admin_note = $request->admin_note;
        $history->save();

        return redirect()->back()->with('success', 'Surat Audit berhasil dikirim ke Lembaga ' . ucfirst($request->category));
    }
}