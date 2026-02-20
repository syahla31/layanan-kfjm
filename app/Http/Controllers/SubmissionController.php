<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Submission;
use App\Models\SubmissionFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'title' => 'required',
            'file_upload' => 'required|mimes:pdf|max:10240',
        ]);

        $user = Auth::user();
        $file = $request->file('file_upload');
        $path = $file->store('documents', 'public');

        $submission = Submission::create([
            'user_id' => $user->id,
            'category' => $user->category,
            'type' => $request->type,
            'title' => $request->title,
            'file_path' => $path,
            'status' => 'pending'
        ]);

        SubmissionFile::create([
            'submission_id' => $submission->id,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'version' => 1,
            'user_note' => 'Dokumen awal'
        ]);

        return back()->with('success', 'Dokumen berhasil diajukan.');
    }

    public function update(Request $request, $id)
    {
        $submission = Submission::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'file_upload' => 'nullable|mimes:pdf|max:10240',
        ]);

        if ($request->hasFile('file_upload')) {
            $file = $request->file('file_upload');
            $path = $file->store('documents', 'public');
            $nextVersion = $submission->files()->max('version') + 1;

            SubmissionFile::create([
                'submission_id' => $submission->id,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'version' => $nextVersion,
                'user_note' => 'Revisi ke-' . ($nextVersion - 1)
            ]);

            $submission->file_path = $path;
            $submission->status = 'pending';
            $submission->admin_note = null;
            $submission->admin_file = null; 
            $submission->save();

            return back()->with('success', 'Dokumen revisi berhasil diupload.');
        }
        
        $submission->update(['title' => $request->title]);
        return back()->with('success', 'Judul diperbarui.');
    }

    public function destroy($id)
    {
        $submission = Submission::where('user_id', Auth::id())->findOrFail($id);
        foreach($submission->files as $file) {
            if(Storage::disk('public')->exists($file->file_path)) Storage::disk('public')->delete($file->file_path);
            if($file->admin_file && Storage::disk('public')->exists($file->admin_file)) Storage::disk('public')->delete($file->admin_file);
        }
        $submission->delete();
        return back()->with('success', 'Pengajuan dihapus.');
    }

    public function approve(Request $request, $id)
    {
        $this->processAdminAction($request, $id, 'approved');
        return back()->with('success', 'Dokumen disetujui.');
    }

    public function reject(Request $request, $id)
    {
        $this->processAdminAction($request, $id, 'rejected');
        return back()->with('success', 'Dokumen dikembalikan untuk revisi.');
    }

    private function processAdminAction($request, $id, $status)
    {
        $submission = Submission::findOrFail($id);
        $submission->status = $status;
        $submission->admin_note = $request->admin_note;

        // Ambil File Versi Terakhir dari Riwayat
        $latestFile = $submission->files()->orderBy('version', 'desc')->first();

        // Proses Upload File Admin
        if ($request->hasFile('admin_file')) {
            $path = $request->file('admin_file')->store('responses', 'public');
            
            // 1. Simpan di Induk (Agar tombol utama berubah)
            $submission->admin_file = $path;

            // 2. Simpan di Riwayat Versi Terakhir (Agar muncul di modal history)
            if ($latestFile) {
                $latestFile->admin_file = $path;
                $latestFile->admin_note = $request->admin_note; // Simpan juga catatannya
                $latestFile->save();
            }
        } else {
            // Jika Admin cuma kasih catatan tanpa file, simpan catatannya di riwayat juga
            if ($latestFile) {
                $latestFile->admin_note = $request->admin_note;
                $latestFile->save();
            }
        }

        $submission->save();
    }
}