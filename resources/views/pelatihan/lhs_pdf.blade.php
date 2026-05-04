<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Hasil Survailen - BAPETEN</title>
    <style>
        /* Pengaturan Halaman untuk PDF */
        @page {
            size: A4;
            margin: 1.0cm 1.2cm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        /* Header / Kop Surat */
        .kop-surat {
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
            position: relative;
        }

        .logo-container {
            position: absolute;
            left: 0;
            top: 0;
        }

        .logo-bapeten {
            width: 60px;
            height: auto;
        }

        .header-content {
            text-align: center;
            margin-left: 70px;
        }

        .header-content h1 {
            margin: 0;
            font-size: 14px;
            color: #000;
            font-weight: bold;
        }

        .header-content h2 {
            margin: 0;
            font-size: 12px;
            color: #000;
            font-weight: normal;
        }

        .header-content p {
            margin: 2px 0 0 0;
            font-size: 8px;
            color: #444;
        }

        .si-mutu-title {
            font-weight: bold;
            color: #004a99;
            margin-top: 4px;
            font-size: 10px;
            letter-spacing: 0.5px;
        }

        /* Judul Dokumen */
        .doc-title {
            text-align: center;
            margin: 15px 0;
        }

        .doc-title h3 {
            margin: 0;
            font-size: 12px;
            text-decoration: underline;
            font-weight: bold;
        }

        /* Seksi Informasi */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .info-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .label {
            width: 130px;
            font-weight: bold;
            color: #555;
        }

        .separator {
            width: 10px;
            text-align: center;
        }

        /* Tabel Data Penilaian */
        .section-title {
            background-color: #004a99;
            color: white;
            padding: 5px 10px;
            font-weight: bold;
            font-size: 9px;
            margin-bottom: 0;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .data-table th {
            background-color: #f0f4f8;
            color: #333;
            font-size: 7.5px; 
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px 2px;
            border: 1px solid #cbd5e0;
            text-align: center;
            white-space: normal; 
            word-wrap: break-word;
        }

        /* Lebar Kolom Kunci */
        .col-no { width: 4% !important; }
        .col-aspek { width: 18% !important; }
        .col-skor { width: 6% !important; }
        .col-catatan { width: 72% !important; }

        .data-table td {
            border: 1px solid #cbd5e0;
            padding: 7px 5px;
            font-size: 9.5px;
            vertical-align: top;
            word-wrap: break-word;
        }

        /* CSS UNTUK MENJAGA MULTILEVEL LIST (INDENTASI SPASI) */
        .preserve-text {
            white-space: pre-wrap; /* Menjaga spasi di depan dan baris baru */
            word-break: break-word;
            text-align: left;
        }

        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }

        /* Ringkasan & Predikat */
        .summary-container {
            margin-top: 15px;
        }

        .summary-box {
            width: 250px;
            float: left;
            padding: 8px;
            border: 1px solid #004a99;
            background-color: #f8fafc;
        }

        .summary-row {
            margin-bottom: 3px;
        }

        .summary-label {
            font-weight: bold;
            color: #444;
            font-size: 9px;
        }

        .summary-value {
            font-size: 11px;
            font-weight: bold;
            color: #004a99;
        }

        .predikat-badge {
            display: inline-block;
            padding: 2px 6px;
            background-color: #2f855a;
            color: white;
            border-radius: 2px;
            font-size: 9px;
            font-weight: bold;
        }

        /* Kesimpulan */
        .note-section {
            margin-top: 15px;
            border: 1px solid #e2e8f0;
            padding: 10px;
            background-color: #fff;
            clear: both;
        }

        .note-title {
            font-weight: bold;
            font-size: 9px;
            color: #004a99;
            margin-bottom: 4px;
            text-transform: uppercase;
            border-bottom: 1px solid #edf2f7;
            padding-bottom: 2px;
        }

        .note-content {
            font-style: italic;
            color: #2d3748;
            line-height: 1.5;
            white-space: pre-wrap;
            text-align: left;
        }

        /* Footer & Signature */
        .footer {
            margin-top: 30px;
            width: 100%;
        }

        .footer-left {
            float: left;
            width: 60%;
            font-size: 7.5px;
            color: #718096;
            margin-top: 45px;
        }

        .signature-area {
            float: right;
            width: 230px;
            text-align: center;
        }

        .signature-space {
            height: 65px;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>

    <div class="kop-surat clearfix">
        <div class="logo-container">
            <img src="{{ public_path('image/logo.svg') }}" class="logo-bapeten" alt="Logo">
        </div>
        <div class="header-content">
            <h1>BADAN PENGAWAS TENAGA NUKLIR</h1>
            <h2>REPUBLIK INDONESIA</h2>
            <p>Jl. Gajah Mada No. 8, Jakarta Pusat, 10120 | www.bapeten.go.id</p>
            <div class="si-mutu-title">SISTEM INFORMASI JAMINAN MUTU (SI-MUTU)</div>
        </div>
    </div>

    <div class="doc-title">
        <h3>LAPORAN HASIL SURVAILEN (LHS)</h3>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Nama Lembaga</td>
            <td class="separator">:</td>
            <td class="font-bold">{{ $nama_perusahaan }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Pengajuan</td>
            <td class="separator">:</td>
            <td>{{ $tgl_buat }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Evaluasi</td>
            <td class="separator">:</td>
            <td>{{ $tgl_tanggapan }}</td>
        </tr>
    </table>

    <div class="section-title">Hasil Penilaian Aspek Penjaminan Mutu</div>
    <table class="data-table">
        <thead>
            <tr>
                <th class="col-no">NO</th>
                <th class="col-aspek">ASPEK<br>PENILAIAN</th>
                <th class="col-skor">SKOR</th>
                <th class="col-catatan">CATATAN / REKOMENDASI KHUSUS</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($scores as $key => $score)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td class="font-bold" style="color: #2d3748; line-height: 1.1;">
                    {{ strtoupper(str_replace(['file_', '_'], ['', ' '], $key)) }}
                </td>
                <td class="text-center font-bold">{{ $score }}</td>
                <td>
                    @if(!empty($comments[$key]))
                        <div class="preserve-text">{!! nl2br(e($comments[$key])) !!}</div>
                    @else
                        <span style="color: #cbd5e1; font-style: italic;">- Tidak ada catatan khusus -</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-container clearfix">
        <div class="summary-box">
            <div class="summary-row">
                <span class="summary-label">Persentase Akhir:</span>
                <span class="summary-value">{{ $final_score }}%</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Predikat:</span>
                <span class="predikat-badge">{{ $predikat }}</span>
            </div>
        </div>
    </div>

    @if(!empty($admin_note))
    <div class="note-section">
        <div class="note-title">Kesimpulan & Rekomendasi Umum Asesor</div>
        <div class="note-content">{!! nl2br(e($admin_note)) !!}</div>
    </div>
    @endif

    <div class="footer clearfix">
        <div class="footer-left">
            <p>Dokumen ini dihasilkan secara otomatis oleh Sistem SI-MUTU BAPETEN.</p>
            <p>Waktu Cetak: {{ date('d/m/Y H:i') }} WIB</p>
        </div>
        <div class="signature-area">
            <p>Jakarta, {{ $tgl_tanggapan }}</p>
            <p><strong>Ketua Tim Surveilan</strong></p>
            
            <div class="signature-space" style="height: 65px; position: relative;">
                @if(isset($signature_path) && $signature_path)
                    <img src="{{ $signature_path }}" style="height: 60px; width: auto;">
                @endif
            </div>
            
            <p style="margin-bottom: 0;"><strong>( {{ $chairman_name }} )</strong></p>
            <p style="margin-top: 2px; font-size: 9px;">NIP. {{ $chairman_nip }}</p>
        </div>
    </div>

</body>
</html>