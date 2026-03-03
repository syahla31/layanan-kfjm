<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\SubmissionFile; 
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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

    public function indexUji()
    {
        $survailens = Submission::where('user_id', Auth::id())
            ->where('category', 'uji')
            ->where('type', 'Survailen')
            ->orderBy('created_at', 'desc')
            ->with('files') 
            ->get();

        return view('uji.survailen', compact('survailens')); 
    }

    public function adminIndexUji()
    {
        $audits = Submission::with(['user', 'files']) 
            ->where('category', 'uji')
            ->where('type', 'Survailen')
            ->orderBy('created_at', 'desc')
            ->get();

        $users = User::where('role', 'user')->where('category', 'uji')->get();

        return view('uji.survailen_admin', compact('audits', 'users'));
    }

    public function store(Request $request)
    {
        // Pastikan nama field di sini SAMA dengan di atribut 'name' pada HTML
        $request->validate([
            'user_id'    => 'required|exists:users,id',
            'title'      => 'required|string|max:255',
            'admin_file' => 'required|mimes:pdf|max:10240',
            'category'   => 'required|in:pelatihan,uji', 
        ]);

        try {
            $adminFilePath = null;

            if ($request->hasFile('admin_file')) {
                $file = $request->file('admin_file');
                $originalName = $file->getClientOriginalName();
                $folder = 'submissions/survailen/' . $request->category . '/admin';
                
                // Simpan file
                $adminFilePath = $file->storeAs(
                    $folder, 
                    'SURVAILEN_ADMIN_' . time() . '_' . $originalName, 
                    'public'
                );
            }

            // 1. Simpan ke table Submissions
            $submission = new Submission();
            $submission->user_id    = $request->user_id;
            $submission->category   = $request->category; 
            $submission->type       = 'Survailen';
            $submission->title      = $request->title;
            $submission->admin_file = $adminFilePath;
            $submission->admin_note = $request->admin_note;
            $submission->status     = 'pending'; 
            $submission->save();

            // 2. Simpan ke table SubmissionFiles (History)
            $history = new SubmissionFile();
            $history->submission_id = $submission->id;
            $history->version       = 0; 
            $history->file_path     = '-'; 
            $history->file_name     = '-';
            $history->admin_file    = $adminFilePath; 
            $history->admin_note    = $request->admin_note;
            $history->save();

            return redirect()->back()->with('success', 'Surat Audit berhasil dikirim ke Lembaga.');

        } catch (\Exception $e) {
            Log::error("Gagal simpan survailen: " . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }
}