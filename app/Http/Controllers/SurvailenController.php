<?php

namespace App\Http\Controllers;

use App\Models\SurvailenSubmission;
use App\Models\SurvailenDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SurvailenController extends Controller
{
    /**
     * USER: Halaman Dashboard Survailen
     */
    public function index()
    {
        $user = Auth::user();

        // Ambil pengajuan aktif (yang belum selesai)
        $activeSubmission = SurvailenSubmission::with('details')
            ->where('user_id', $user->id)
            ->where('category', $user->category)
            ->where('status', '!=', 'completed')
            ->latest()
            ->first();

        // Riwayat pengajuan yang sudah selesai
        $survailens = SurvailenSubmission::where('user_id', $user->id)
            ->where('category', $user->category)
            ->where('status', 'completed')
            ->orderBy('updated_at', 'desc')
            ->get();

        $viewPath = $user->category == 'uji' ? 'uji.survailen' : 'pelatihan.survailen';
        return view($viewPath, compact('activeSubmission', 'survailens'));
    }

    /**
     * ADMIN: Halaman Antrian Verifikasi
     */
    public function adminIndex()
    {
        $user = Auth::user();

        $audits = SurvailenSubmission::with(['user', 'details'])
            ->where('category', $user->category)
            ->orderBy('created_at', 'desc')
            ->get();

        $viewPath = $user->category == 'uji' ? 'uji.survailen_admin' : 'pelatihan.survailen_admin';
        return view($viewPath, compact('audits'));
    }

    /**
     * TAHAP 1 - USER: Simpan Self Assessment (Mulai Pengajuan Baru)
     */
    public function storeSelfAssessment(Request $request)
    {
        $request->validate([
            'scores' => 'required|array'
        ]);

        try {
            $user = Auth::user();

            // Cek apakah ada pengajuan yang masih berjalan
            $exists = SurvailenSubmission::where('user_id', $user->id)
                ->where('status', '!=', 'completed')
                ->exists();

            if ($exists) {
                return back()->withErrors(['error' => 'Anda masih memiliki pengajuan aktif yang belum selesai.']);
            }

            SurvailenSubmission::create([
                'user_id' => $user->id,
                'category' => $user->category,
                'status' => 'uploading',
                'title' => 'Survailen ' . strtoupper($user->category) . ' - ' . now()->format('d M Y'),
                'self_assessment_scores' => json_encode($request->scores),
            ]);

            return back()->with('success', 'Penilaian mandiri tersimpan! Silakan lengkapi dokumen pendukung.');
        } catch (\Exception $e) {
            Log::error("Gagal simpan self assessment: " . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan sistem saat menyimpan data.']);
        }
    }

    /**
     * TAHAP 2 - USER: Unggah Dokumen Terperinci
     */
    public function storeDocuments(Request $request, $id)
    {
        $submission = SurvailenSubmission::where('user_id', Auth::id())
            ->where('status', 'uploading') // Hanya bisa upload jika status masih uploading
            ->findOrFail($id);

        $rules = [
            'file_oss'               => 'required|mimes:pdf|max:2048',
            'file_mou'               => 'nullable|mimes:pdf|max:2048',
            'file_izin_lainnya'      => 'required|mimes:pdf|max:2048',
            'file_manual_mutu'       => 'required|mimes:pdf|max:2048',
            'file_prosedur_pelatihan'=> 'required|mimes:pdf|max:2048',
            'file_pantau_mutu'       => 'required|mimes:pdf|max:2048',
            'file_rekaman_lainnya'   => 'nullable|mimes:pdf|max:2048',
            'file_lapkin'            => 'required|mimes:pdf|max:2048',
            'file_kak'               => 'required|mimes:pdf|max:2048',
            'file_daftar_manajemen'  => 'required|mimes:pdf|max:2048',
            'file_daftar_pengajar'   => 'required|mimes:pdf|max:2048',
            'file_daftar_sarana'     => 'required|mimes:pdf|max:2048',
            'file_daftar_prasarana'  => 'required|mimes:pdf|max:2048',
            'file_kurikulum'         => 'required|mimes:pdf|max:2048',
            'file_modul'             => 'required|mimes:pdf|max:2048',
            'file_bahan_ajar'        => 'required|mimes:pdf|max:2048',
        ];

        // Jika sudah ada detail, beberapa file mungkin jadi nullable (opsional jika hanya update)
        if ($submission->details) {
            foreach ($rules as $key => $rule) {
                $rules[$key] = str_replace('required', 'nullable', $rule);
            }
        }

        $request->validate($rules);

        try {
            DB::beginTransaction();

            $folder = 'survailen/' . $submission->category . '/' . Auth::id();
            $fileKeys = array_keys($rules);
            $detailData = [];

            // Ambil data detail lama untuk pengecekan hapus file
            $oldDetails = $submission->details;

            foreach ($fileKeys as $key) {
                if ($request->hasFile($key)) {
                    // Hapus file lama jika ada
                    if ($oldDetails && $oldDetails->$key) {
                        Storage::disk('public')->delete($oldDetails->$key);
                    }
                    // Simpan file baru
                    $detailData[$key] = $request->file($key)->store($folder, 'public');
                }
            }

            // Update atau Create detail
            $submission->details()->updateOrCreate(
                ['survailen_submission_id' => $submission->id],
                $detailData
            );

            // Jika tombol yang ditekan adalah 'Kirim Ke Verifikasi'
            if ($request->submit_action == 'final') {
                $submission->update(['status' => 'verification']);
                $msg = 'Seluruh dokumen berhasil dikirim untuk verifikasi!';
            } else {
                $msg = 'Draft dokumen berhasil disimpan!';
            }

            DB::commit();
            return back()->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal upload berkas detail: " . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal mengunggah dokumen: ' . $e->getMessage()]);
        }
    }

    /**
     * TAHAP 3 - ADMIN: Evaluasi Akhir
     */
    public function evaluate(Request $request, $id)
    {
        $submission = SurvailenSubmission::findOrFail($id);

        // Validasi: Scores wajib, File wajib (kecuali sudah ada), Note jadi nullable/opsional
        $request->validate([
            'scores'           => 'required|array',
            'admin_note'       => 'nullable|string', 
            'admin_file'       => $submission->admin_file ? 'nullable|mimes:pdf|max:2048' : 'required|mimes:pdf|max:2048',
            'certificate_file' => $submission->certificate_file ? 'nullable|mimes:pdf|max:2048' : 'required|mimes:pdf|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $weights = [
                'legalitas' => 10, 'mutu' => 20, 'rekaman' => 20,
                'kinerja' => 5, 'sdm' => 10, 'sarpras' => 15, 'kurikulum' => 20,
            ];

            $totalWeightedScore = 0;
            foreach ($request->scores as $key => $score) {
                if (isset($weights[$key])) {
                    $totalWeightedScore += ($score * $weights[$key]);
                }
            }

            $percentage = ($totalWeightedScore / 400) * 100;
            
            // Logika Predikat
            $predikat = 'D';
            if ($percentage >= 85) $predikat = 'A';
            elseif ($percentage >= 70) $predikat = 'B';
            elseif ($percentage >= 55) $predikat = 'C';

            $updateData = [
                'evaluator_scores' => json_encode($request->scores),
                'final_score'      => $percentage,
                'predikat'         => $predikat,
                'status'           => 'completed',
                'admin_note'       => $request->admin_note ?? '-', // Isi default jika kosong
            ];

            // Simpan File 1: Laporan Hasil Survailen (LHS)
            if ($request->hasFile('admin_file')) {
                if ($submission->admin_file) Storage::disk('public')->delete($submission->admin_file);
                $updateData['admin_file'] = $request->file('admin_file')->store('survailen/hasil', 'public');
            }

            // Simpan File 2: Sertifikat Akreditasi
            if ($request->hasFile('certificate_file')) {
                if ($submission->certificate_file) Storage::disk('public')->delete($submission->certificate_file);
                $updateData['certificate_file'] = $request->file('certificate_file')->store('survailen/sertifikat', 'public');
            }

            $submission->update($updateData);

            DB::commit();
            return back()->with('success', 'Penilaian berhasil disimpan! Predikat: ' . $predikat);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Evaluasi Gagal: " . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal menyimpan penilaian: ' . $e->getMessage()]);
        }
    }

    /**
     * GLOBAL: Hapus Data
     */
    public function destroy($id)
    {
        $submission = SurvailenSubmission::with('details')->findOrFail($id);
        
        // Pastikan hanya owner atau admin yang bisa hapus
        if (Auth::id() !== $submission->user_id && Auth::user()->role !== 'admin') {
            abort(403);
        }

        try {
            DB::beginTransaction();

            // 1. Hapus file-file di tabel detail
            if ($submission->details) {
                $detailsArray = $submission->details->toArray();
                foreach ($detailsArray as $key => $path) {
                    if (str_starts_with($key, 'file_') && $path) {
                        Storage::disk('public')->delete($path);
                    }
                }
            }

            // 2. Hapus file admin
            if ($submission->admin_file) {
                Storage::disk('public')->delete($submission->admin_file);
            }

            // 3. Hapus folder jika kosong (optional)
            // Storage::disk('public')->deleteDirectory('survailen/' . $submission->category . '/' . $submission->user_id);

            $submission->delete(); 

            DB::commit();
            return back()->with('success', 'Data survailen dan berkas terkait berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }
}