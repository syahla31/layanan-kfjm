<?php

namespace App\Http\Controllers;

use App\Models\SurvailenSubmission;
use App\Models\SurvailenFile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class SurvailenController extends Controller
{
    /**
     * USER: Halaman Dashboard Survailen
     */
    public function index()
    {
        $user = Auth::user();

        // Load submission aktif beserta relasi file-filenya
        $activeSubmission = SurvailenSubmission::with('files')
            ->where('user_id', $user->id)
            ->where('category', $user->category)
            ->where('status', '!=', 'completed')
            ->latest()
            ->first();

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

        // Ambil semua pengajuan berdasarkan kategori admin (pelatihan/uji)
        $audits = SurvailenSubmission::with(['user', 'files'])
            ->where('category', $user->category)
            ->orderBy('created_at', 'desc')
            ->get();

        $viewPath = $user->category == 'uji' ? 'uji.survailen_admin' : 'pelatihan.survailen_admin';
        return view($viewPath, compact('audits'));
    }

    /**
     * TAHAP 1 - USER: Simpan Self Assessment
     */
    public function storeSelfAssessment(Request $request)
    {
        $request->validate(['scores' => 'required|array']);

        try {
            $user = Auth::user();
            SurvailenSubmission::create([
                'user_id' => $user->id,
                'category' => $user->category,
                'status' => 'uploading',
                'title' => 'Survailen ' . ucfirst($user->category) . ' - ' . now()->format('d M Y'),
                'self_assessment_scores' => json_encode($request->scores),
            ]);

            return back()->with('success', 'Penilaian mandiri tersimpan! Silakan lanjut unggah dokumen pendukung.');
        } catch (\Exception $e) {
            Log::error("Gagal simpan self assessment: " . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal menyimpan data penilaian mandiri: ' . $e->getMessage()]);
        }
    }

    /**
     * TAHAP 2 - USER: Unggah Dokumen Multi-Upload (Masuk ke tabel survailen_files)
     */
    public function storeDocuments(Request $request, $id)
    {
        $submission = SurvailenSubmission::where('user_id', Auth::id())->findOrFail($id);

        // Validasi array files
        $request->validate([
            'files.*.*' => 'required|mimes:pdf|max:10240',
        ], [
            'files.*.*.mimes' => 'Semua berkas harus berformat PDF.',
            'files.*.*.max'   => 'Ukuran berkas tidak boleh lebih dari 10MB.',
        ]);

        try {
            $uploadedFiles = $request->file('files');

            if ($uploadedFiles && is_array($uploadedFiles)) {
                foreach ($uploadedFiles as $categoryKey => $fileArray) {
                    if (is_array($fileArray)) {
                        foreach ($fileArray as $file) {
                            $folder = 'survailen/' . $submission->category . '/' . Auth::id() . '_' . time();
                            $path = $file->store($folder, 'public');

                            SurvailenFile::create([
                                'survailen_submission_id' => $submission->id,
                                'category_key'            => $categoryKey,
                                'file_path'               => $path,
                                'file_name'               => $file->getClientOriginalName(),
                            ]);
                        }
                    }
                }
            } else {
                return back()->withErrors(['error' => 'Tidak ada file yang diterima oleh sistem.']);
            }

            $submission->update(['status' => 'verification']);

            return back()->with('success', 'Seluruh berkas berhasil diunggah dan disimpan!');
        } catch (\Exception $e) {
            Log::error("Gagal upload berkas multi: " . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan file: ' . $e->getMessage()]);
        }
    }

    /**
     * TAHAP 3 - ADMIN: Penilaian Asesor
     */
    public function evaluate(Request $request, $id)
    {
        $submission = SurvailenSubmission::with('user')->findOrFail($id);

        $request->validate([
            'scores'   => 'required|array',
            'comments' => 'nullable|array',
            'admin_note' => 'required|string',
            'chairman_name' => 'nullable|string',
            'chairman_nip' => 'nullable|string',
        ]);

        try {
            // 1. Kalkulasi Skor (Sesuai logic Anda sebelumnya)
            $weights = [
                'file_legalitas' => 10, 'file_mutu' => 20, 'file_rekaman' => 20,
                'file_kinerja' => 5, 'file_sdm' => 10, 'file_sarpras' => 15, 'file_kurikulum' => 20
            ];

            $totalWeightedScore = 0;
            foreach ($request->scores as $key => $score) {
                if (isset($weights[$key])) {
                    $totalWeightedScore += ($score * $weights[$key]);
                }
            }

            $percentage = ($totalWeightedScore / 400) * 100;
            if ($percentage >= 85) $predikat = 'A';
            elseif ($percentage >= 70) $predikat = 'B';
            elseif ($percentage >= 55) $predikat = 'C';
            else $predikat = 'D';

            $signatureFileName = Str::slug($request->chairman_name) . '.png';
            $signaturePathOnDisk = public_path('image/signatures/' . $signatureFileName);
            
            // Cek apakah file fisik tanda tangan ada di server
            $signaturePathForPdf = file_exists($signaturePathOnDisk) ? $signaturePathOnDisk : null;
            // 2. GENERATE LAPORAN (LHS) OTOMATIS
            $dataLhs = [
                'nama_perusahaan' => $submission->user->name,
                'tgl_buat'        => $submission->created_at->isoFormat('D MMMM Y'),
                'tgl_tanggapan'   => Carbon::now()->isoFormat('D MMMM Y'),
                'category'        => $submission->category,
                'scores'          => $request->scores,
                'comments'        => $request->comments,
                'final_score'     => $percentage,
                'predikat'        => $predikat,
                'admin_note'      => $request->admin_note,
                'chairman_name'   => $request->chairman_name,
                'chairman_nip'    => $request->chairman_nip,
                'signature_path'  => $signaturePathForPdf
            ];

            $pdfLhs = Pdf::loadView('pelatihan.lhs_pdf', $dataLhs);
            $lhsPath = 'survailen/evaluasi/' . $submission->id . '/LHS_' . Str::slug($submission->user->name) . '.pdf';
            Storage::disk('public')->put($lhsPath, $pdfLhs->output());

            // 3. GENERATE SERTIFIKAT (Draft)
            $viewSertifikat = $submission->category == 'uji' ? 'uji.sertifikat_template' : 'pelatihan.sertifikat_template';
            $pdfSertifikat = Pdf::loadView($viewSertifikat, [
                'nama_user' => $submission->user->name,
                'predikat'  => $predikat,
                'tanggal'   => Carbon::now()->isoFormat('D MMMM Y'),
                'category'  => $submission->category
            ])->setPaper('A4', 'landscape');

            $sertifikatPath = 'survailen/evaluasi/' . $submission->id . '/Sertifikat_' . Str::slug($submission->user->name) . '.pdf';
            Storage::disk('public')->put($sertifikatPath, $pdfSertifikat->output());

            // 4. UPDATE DATA (Status tetap 'verification', is_published = false)
            $submission->update([
                'evaluator_scores'   => json_encode($request->scores),
                'evaluator_comments' => json_encode(array_filter($request->comments ?? [])),
                'final_score'        => $percentage,
                'predikat'           => $predikat,
                'admin_note'         => $request->admin_note,
                'admin_file'         => $lhsPath, // Laporan otomatis masuk sini
                'certificate_file'   => $sertifikatPath,
                'is_published'       => false, // MASIH DRAFT
                'chairman_name'      => $request->chairman_name,
                'chairman_nip'       => $request->chairman_nip,
                'status'             => 'verification' // Tetap di antrean admin
            ]);

            return back()->with('success', 'Draft laporan & sertifikat berhasil dibuat. Silakan tinjau kembali sebelum dikirim.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal membuat draft: ' . $e->getMessage()]);
        }
    }

     /**
     * TAHAP 4 - ADMIN: Publish Laporan & Sertifikat ke User
     */

    public function publish($id)
    {
        $submission = SurvailenSubmission::findOrFail($id);
        
        $submission->update([
            'is_published' => true,
            'status'       => 'completed' // Pindah ke riwayat selesai
        ]);

        return back()->with('success', 'Laporan berhasil dikirim ke user!');
    }

    /**
     * GLOBAL: Hapus Data & File Fisik
     */
    public function destroy($id)
    {
        $submission = SurvailenSubmission::with('files')->findOrFail($id);
        
        foreach ($submission->files as $file) {
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
            $file->delete();
        }

        if ($submission->admin_file) Storage::disk('public')->delete($submission->admin_file);
        if ($submission->certificate_file) Storage::disk('public')->delete($submission->certificate_file);

        $submission->delete();
        return back()->with('success', 'Data survailen berhasil dihapus.');
    }

    public function generateSertifikat($submission_id)
    {
        $submission = SurvailenSubmission::with('user')->findOrFail($submission_id);

        $data = [
            'nama_user' => $submission->user->name,
            'predikat'  => $submission->predikat,
            'tanggal'   => Carbon::now('Asia/Jakarta')->isoFormat('D MMMM Y')
        ];

        $pdf = Pdf::loadView('pelatihan.sertifikat_template', $data)
                ->setPaper('a4', 'landscape');

        return $pdf->stream('Sertifikat_' . $submission->user->name . '.pdf');
    }
}