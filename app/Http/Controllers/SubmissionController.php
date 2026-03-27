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
            'file_upload' => 'required|mimes:pdf|max:2048', // Max 2MB
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
        // Cari data milik user yang sedang login
        $submission = Submission::where('user_id', Auth::id())->findOrFail($id);

        // 1. LOGIKA UPLOAD REVISI (Jika ada file yang diupload)
        if ($request->hasFile('file_upload')) {
            $request->validate([
                'file_upload' => 'required|mimes:pdf|max:2048', // Max 2MB
            ]);

            $file = $request->file('file_upload');
            $path = $file->store('documents', 'public');
            $nextVersion = $submission->files()->max('version') + 1;

            SubmissionFile::create([
                'submission_id' => $submission->id,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'version' => $nextVersion,
                'user_note' => $request->user_note ?? 'Revisi ke-' . ($nextVersion - 1)
            ]);

            $submission->forceFill([
                'file_path' => $path,
                'status' => 'pending',
                'admin_note' => null,
                'admin_file' => null,
                'title' => $request->title ?? $submission->title
            ])->save();

            return back()->with('success', 'Dokumen revisi berhasil diupload.');
        }
        
        // 2. LOGIKA KONFIRMASI TINDAK LANJUT
        // OTOMATIS JADI SELESAI: Kita ubah status langsung ke 'approved'
        // agar di tampilan Blade terbaca sebagai "Selesai".
        $submission->forceFill([
            'title' => $request->title ?? $submission->title,
            'status' => 'approved', 
        ])->save();

        return back()->with('success', 'Tindak lanjut berhasil dikonfirmasi. Status sekarang: Selesai.');
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
        $submission = Submission::findOrFail($id);

        // Aturan validasi berbeda tergantung tipe dokumen
        $rules = [
            'admin_note' => 'nullable|string|max:1000',
            'admin_file' => 'nullable|file|mimes:pdf|max:2048',
        ];

        if ($submission->type === 'KAK') {
            $rules['admin_file'] = 'required|file|mimes:pdf|max:2048';
        }

        $request->validate($rules, [
            'admin_file.required' => 'File PDF wajib disertakan untuk pengembalian dokumen KAK.',
            'admin_file.max'      => 'Ukuran file tidak boleh melebihi 2 MB.',
            'admin_file.mimes'    => 'File harus berformat PDF.',
        ]);

        $this->processAdminAction($request, $id, 'rejected');
        return back()->with('success', 'Dokumen dikembalikan untuk revisi.');
    }

    private function processAdminAction($request, $id, $status)
    {
        $submission = Submission::findOrFail($id);
        $submission->status = $status;
        $submission->admin_note = $request->admin_note;

        $latestFile = $submission->files()->orderBy('version', 'desc')->first();

        if ($request->hasFile('admin_file')) {
            $path = $request->file('admin_file')->store('responses', 'public');
            $submission->admin_file = $path;

            if ($latestFile) {
                $latestFile->admin_file = $path;
                $latestFile->admin_note = $request->admin_note;
                $latestFile->save();
            }
        } else {
            if ($latestFile) {
                $latestFile->admin_note = $request->admin_note;
                $latestFile->save();
            }
        }

        $submission->save();
    }
}