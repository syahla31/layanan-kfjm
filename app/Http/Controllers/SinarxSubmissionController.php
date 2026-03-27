<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SinarxSubmission;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class SinarxSubmissionController extends Controller
{
    // ==========================================
    // LOGIKA USER (RUMAH SAKIT / KLINIK)
    // ==========================================

    public function store(Request $request)
    {
        // 1. Validasi Input Lengkap (Termasuk field baru Nota Dinas)
        $request->validate([
            'no_sertifikat'    => 'required|string|max:255',
            'no_registrasi'    => 'required|string|max:255',
            'nomor_surat'      => 'required|string|max:255',
            'alasan_amandemen' => 'required|string',
            'bagian_diperbaiki'=> 'nullable|string|max:255',
            'ketidaksesuaian'  => 'nullable|string',
            'data_sesuai'      => 'nullable|string',
            'file_upload'      => 'required|mimes:pdf|max:2048', // Max 2MB
        ]);

        // 2. Simpan File PDF
        $file = $request->file('file_upload');
        $path = $file->store('dokumen_sinarx', 'public');

        // 3. Simpan ke Database dengan data lengkap
        SinarxSubmission::create([
            'user_id'           => Auth::id(),
            'no_sertifikat'     => $request->no_sertifikat,
            'no_registrasi'     => $request->no_registrasi,
            'nomor_surat'       => $request->nomor_surat,
            'alasan_amandemen'  => $request->alasan_amandemen,
            'bagian_diperbaiki' => $request->bagian_diperbaiki,
            'ketidaksesuaian'   => $request->ketidaksesuaian,
            'data_sesuai'       => $request->data_sesuai,
            'file_path'         => $path,
            'status'            => 'pending'
        ]);

        return back()->with('success', 'Permohonan amandemen berhasil diajukan.');
    }

    public function update(Request $request, $id)
    {
        $submission = SinarxSubmission::where('user_id', Auth::id())->findOrFail($id);

        // Validasi data pembaruan
        $request->validate([
            'no_sertifikat'    => 'required|string|max:255',
            'no_registrasi'    => 'required|string|max:255',
            'nomor_surat'      => 'required|string|max:255',
            'alasan_amandemen' => 'required|string',
            'bagian_diperbaiki'=> 'nullable|string|max:255',
            'ketidaksesuaian'  => 'nullable|string',
            'data_sesuai'      => 'nullable|string',
            'file_upload'      => 'nullable|mimes:pdf|max:2048', // Max 2MB
        ]);

        // Handle pembaruan file jika ada
        if ($request->hasFile('file_upload')) {
            if ($submission->file_path && Storage::disk('public')->exists($submission->file_path)) {
                Storage::disk('public')->delete($submission->file_path);
            }
            $submission->file_path = $request->file('file_upload')->store('dokumen_sinarx', 'public');
        }

        // Update seluruh field data
        $submission->fill([
            'no_sertifikat'     => $request->no_sertifikat,
            'no_registrasi'     => $request->no_registrasi,
            'nomor_surat'       => $request->nomor_surat,
            'alasan_amandemen'  => $request->alasan_amandemen,
            'bagian_diperbaiki' => $request->bagian_diperbaiki,
            'ketidaksesuaian'   => $request->ketidaksesuaian,
            'data_sesuai'       => $request->data_sesuai,
            'status'            => 'pending', // Balikkan ke pending untuk dicek ulang
            'admin_note'        => null,
        ])->save();

        return back()->with('success', 'Data amandemen berhasil diperbarui dan dikirim ulang.');
    }

    public function destroy($id)
    {
        $submission = SinarxSubmission::where('user_id', Auth::id())->findOrFail($id);
        
        if ($submission->file_path && Storage::disk('public')->exists($submission->file_path)) {
            Storage::disk('public')->delete($submission->file_path);
        }
        
        $submission->delete();

        return back()->with('success', 'Permohonan amandemen dibatalkan.');
    }

    // ==========================================
    // LOGIKA ADMIN (VERIFIKASI & VALIDASI)
    // ==========================================

    public function approve(Request $request, $id)
    {
        $submission = SinarxSubmission::findOrFail($id);
        
        $submission->status = 'approved';
        // Pesan sukses otomatis jika disetujui
        $submission->admin_note = $request->admin_note ?? 'Permohonan amandemen telah disetujui. Data sertifikat Anda akan segera diperbarui dalam sistem.';
        $submission->save();

        return back()->with('success', 'Permohonan amandemen berhasil disetujui.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_note' => 'required|string' 
        ]);

        $submission = SinarxSubmission::findOrFail($id);
        
        $submission->status = 'rejected';
        $submission->admin_note = $request->admin_note;
        $submission->save();

        return back()->with('success', 'Catatan revisi telah dikirim ke Unit/Rumah Sakit.');
    }
}