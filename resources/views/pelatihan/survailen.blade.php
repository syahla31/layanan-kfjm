<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survailen Berkala | SI-MUTU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Outfit', 'sans-serif'] },
                    animation: { 
                        'pop-in': 'popIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards',
                        'fade-in-up': 'fadeInUp 0.5s ease-out forwards'
                    },
                    keyframes: {
                        popIn: {
                            '0%': { opacity: '0', transform: 'scale(0.9) translateY(10px)' },
                            '100%': { opacity: '1', transform: 'scale(1) translateY(0)' },
                        },
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(15px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #f8fafc; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        
        .glass-overlay {
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(4px);
        }

        .score-radio:checked + label {
            background-color: #2563eb;
            color: white;
            border-color: #2563eb;
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.25);
        }
        
        .score-label { transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); }
        .modal-backdrop { backdrop-filter: blur(10px); background-color: rgba(15, 23, 42, 0.3); }
        .no-scrollbar::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased overflow-hidden">

    @php
        $status = $activeSubmission ? $activeSubmission->status : 'none'; 
    @endphp

    {{-- === MODAL BERHASIL === --}}
    @if(session('success'))
    <div id="successModal" class="fixed inset-0 z-[120] flex items-center justify-center p-4 text-center">
        <div class="absolute inset-0 modal-backdrop" onclick="closeModal('successModal')"></div>
        <div class="relative bg-white rounded-[2.5rem] w-full max-w-sm p-8 shadow-2xl animate-pop-in border border-white">
            <div class="w-16 h-16 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center mx-auto mb-6 text-2xl shadow-inner border border-emerald-100/50">
                <i class="fas fa-circle-check"></i>
            </div>
            <h3 class="text-xl font-black text-slate-900 uppercase tracking-tighter mb-2">Berhasil!</h3>
            <p class="text-slate-500 text-[11px] font-medium leading-relaxed mb-8 px-2">
                {{ session('success') }}
            </p>
            <button onclick="closeModal('successModal')" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-emerald-600 transition-all shadow-xl active:scale-95">
                Lanjutkan
            </button>
        </div>
    </div>
    @endif

    {{-- === MODAL ERROR / VALIDASI === --}}
    <div id="errorModal" class="fixed inset-0 z-[130] hidden items-center justify-center p-4 text-center">
        <div class="absolute inset-0 modal-backdrop" onclick="closeModal('errorModal')"></div>
        <div class="relative bg-white rounded-[2.5rem] w-full max-w-md p-8 shadow-2xl animate-pop-in border border-white text-center">
            <div class="w-16 h-16 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center mx-auto mb-6 text-2xl shadow-inner border border-rose-100/50">
                <i class="fas fa-circle-exclamation"></i>
            </div>
            <h3 class="text-xl font-black text-slate-900 uppercase tracking-tighter mb-2">Dokumen Belum Lengkap</h3>
            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-4">Mohon lengkapi berkas berikut:</p>
            <div class="bg-slate-50 rounded-2xl p-4 mb-8 text-left max-h-48 overflow-y-auto border border-slate-100 no-scrollbar">
                <ul id="errorList" class="space-y-2">
                    @if($errors->any())
                        @foreach ($errors->all() as $error)
                            <li class="text-[11px] font-bold text-rose-600 flex items-start gap-2">
                                <i class="fas fa-caret-right mt-1 shrink-0"></i> <span>{{ $error }}</span>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>
            <button onclick="closeModal('errorModal')" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-rose-600 transition-all shadow-xl active:scale-95">
                Mengerti, Saya Lengkapi
            </button>
        </div>
    </div>

    {{-- === MODAL KONFIRMASI KIRIM === --}}
    <div id="confirmFinalModal" class="fixed inset-0 z-[120] hidden flex items-center justify-center p-4 text-center">
        <div class="absolute inset-0 modal-backdrop" onclick="closeConfirmModal()"></div>
        <div class="relative bg-white rounded-[2.5rem] w-full max-w-sm p-8 shadow-2xl animate-pop-in border border-white text-center">
            <div class="w-16 h-16 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center mx-auto mb-6 text-2xl shadow-inner border border-amber-100/50">
                <i class="fas fa-triangle-exclamation"></i>
            </div>
            <h3 class="text-xl font-black text-slate-900 uppercase tracking-tighter mb-2">Kirim Pengajuan?</h3>
            <p class="text-slate-500 text-[11px] font-medium leading-relaxed mb-8 px-2">
                Pastikan seluruh dokumen sudah benar. Data yang sudah dikirim <span class="text-rose-500 font-bold">tidak dapat diubah kembali</span> selama verifikasi.
            </p>
            <div class="flex gap-3">
                <button onclick="closeConfirmModal()" class="flex-1 bg-slate-100 text-slate-600 py-4 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-slate-200 transition-all">
                    Batal
                </button>
                <button onclick="submitFinalFormNow()" class="flex-1 bg-blue-600 text-white py-4 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-slate-900 transition-all shadow-xl active:scale-95">
                    Ya, Kirim
                </button>
            </div>
        </div>
    </div>

    <div class="flex h-screen overflow-hidden">
        
        <!-- MOBILE OVERLAY -->
        <div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 z-40 hidden lg:hidden glass-overlay transition-opacity duration-300"></div>

        <!-- SIDEBAR -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-[#1e3a8a] transform -translate-x-full transition-transform duration-300 lg:translate-x-0 lg:static flex flex-col h-full border-r border-blue-900/20">
            @include('components.pelatihan-sidebar')
        </aside>

        <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
            
            <!-- MOBILE HEADER BAR -->
            <div class="lg:hidden bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between sticky top-0 z-30 shadow-sm">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="p-2 text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <span class="font-bold text-slate-800 text-sm tracking-tight uppercase">SI-MUTU <span class="text-blue-600">DKKN</span></span>
                </div>
                <div class="w-8 h-8 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 text-xs font-bold border border-blue-200 shadow-sm">
                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                </div>
            </div>

            <!-- DESKTOP HEADER -->
            <div class="hidden lg:block">
                @include('components.pelatihan-header', [
                    'title' => 'Survailen Berkala',
                    'subtitle' => 'Manajemen Akreditasi Lembaga'
                ])
            </div>

            <!-- MAIN CONTENT -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-5 lg:p-10 no-scrollbar relative text-left">
                
                <div class="max-w-5xl mx-auto">
                    
                    {{-- Judul Halaman Mobile --}}
                    <div class="lg:hidden mb-6">
                        <h1 class="text-2xl font-black text-slate-900 uppercase tracking-tighter leading-tight">Survailen Berkala</h1>
                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-[0.2em]">Kualitas & Akreditasi</p>
                    </div>

                    {{-- 1. VIEW: TIDAK ADA DATA --}}
                    @if($status == 'none')
                        <div class="bg-white rounded-[2.5rem] p-10 lg:p-20 text-center border border-slate-100 shadow-xl shadow-slate-200/40 animate-fade-in-up overflow-hidden relative">
                            <div class="relative z-10 text-center">
                                <div class="w-16 h-16 lg:w-24 lg:h-24 bg-blue-50 text-blue-600 rounded-3xl flex items-center justify-center mx-auto mb-8 text-2xl lg:text-4xl border border-blue-100 shadow-inner">
                                    <i class="fas fa-history"></i>
                                </div>
                                <h2 class="text-xl lg:text-3xl font-black text-slate-900 uppercase tracking-tighter mb-4">Pengajuan Baru</h2>
                                <p class="text-slate-400 text-[11px] lg:text-base max-w-md mx-auto font-medium mb-10 leading-relaxed px-4">
                                    Lakukan penilaian mandiri (Self Assessment) terlebih dahulu sebelum mengunggah dokumen pendukung akreditasi.
                                </p>
                                <button type="button" onclick="document.getElementById('startSection').classList.remove('hidden'); document.getElementById('startSection').scrollIntoView({behavior: 'smooth'})" 
                                    class="bg-slate-900 text-white px-10 py-5 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-blue-600 transition-all shadow-xl active:scale-95 group mx-auto flex items-center gap-3">
                                    Mulai Sekarang <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Section: Self Assessment --}}
                        <div id="startSection" class="hidden mt-10 bg-white rounded-[2.5rem] p-6 lg:p-10 shadow-2xl border border-slate-100 animate-fade-in-up">
                            <div class="mb-10 flex flex-col items-center sm:flex-row sm:items-center gap-5 text-center sm:text-left">
                                <div class="w-14 h-14 bg-blue-600 text-white rounded-2xl flex items-center justify-center text-xl shadow-lg shrink-0 shadow-blue-200"><i class="fas fa-clipboard-list"></i></div>
                                <div>
                                    <h3 class="text-xl lg:text-2xl font-black text-slate-900 uppercase tracking-tight">1. Self Assessment</h3>
                                    <p class="text-[10px] lg:text-[11px] text-slate-400 font-bold uppercase tracking-widest mt-1">Berikan nilai sesuai kondisi nyata lembaga Anda</p>
                                </div>
                            </div>

                            <form action="{{ route('survailen.store.self') }}" method="POST" class="space-y-4">
                                @csrf
                                @php
                                    $components = [
                                        'legalitas' => ['label' => 'Legalitas & Izin', 'bobot' => '10%'],
                                        'mutu' => ['label' => 'Sistem Manajemen Mutu', 'bobot' => '20%'],
                                        'rekaman' => ['label' => 'Rekaman Implementasi', 'bobot' => '20%'],
                                        'kinerja' => ['label' => 'Kinerja & Pelaporan', 'bobot' => '5%'],
                                        'sdm' => ['label' => 'Sumber Daya Manusia', 'bobot' => '10%'],
                                        'sarpras' => ['label' => 'Sarana & Prasarana', 'bobot' => '15%'],
                                        'kurikulum' => ['label' => 'Kurikulum & Modul', 'bobot' => '20%']
                                    ];
                                @endphp
                                <div class="grid grid-cols-1 gap-4">
                                    @foreach($components as $key => $data)
                                    <div class="flex flex-col items-center sm:flex-row sm:items-center justify-between p-6 px-8 rounded-[2rem] border border-slate-100 hover:border-blue-200 hover:bg-blue-50/30 transition-all bg-white shadow-sm group">
                                        <div class="mb-5 sm:mb-0 text-center sm:text-left">
                                            <span class="block font-black text-slate-800 text-sm lg:text-base uppercase tracking-tight group-hover:text-blue-600 transition-colors">{{ $data['label'] }}</span>
                                            <span class="text-[10px] font-bold text-slate-400 uppercase mt-1 inline-block bg-slate-50 px-2 py-0.5 rounded">Estimasi Bobot: {{ $data['bobot'] }}</span>
                                        </div>
                                        <div class="flex gap-3 justify-center">
                                            @for($i=1; $i<=4; $i++)
                                                <input type="radio" name="scores[{{ $key }}]" value="{{ $i }}" id="s_{{ $key }}_{{ $i }}" class="hidden score-radio" required>
                                                <label for="s_{{ $key }}_{{ $i }}" class="score-label w-11 h-11 lg:w-12 lg:h-12 flex items-center justify-center rounded-2xl border-2 border-slate-100 bg-white text-slate-400 font-black text-sm lg:text-base cursor-pointer hover:bg-slate-50 shadow-sm">{{ $i }}</label>
                                            @endfor
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="pt-10 flex justify-center sm:justify-end">
                                    <button type="submit" class="w-full sm:w-auto bg-blue-600 text-white font-black px-12 py-5 rounded-[1.5rem] shadow-xl shadow-blue-100 hover:bg-slate-900 transition-all text-[11px] uppercase tracking-widest flex items-center justify-center gap-2 active:scale-95">
                                        Simpan & Lanjutkan <i class="fas fa-chevron-right text-[10px]"></i>
                                    </button>
                                </div>
                            </form>
                        </div>

                    {{-- 2. VIEW: UPLOAD / VERIFIKASI --}}
                    @else
                        <div class="bg-white rounded-[2.5rem] p-6 lg:p-10 shadow-xl border border-blue-50 animate-fade-in-up relative overflow-hidden mb-12">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10 border-b border-slate-100 text-left">
                                <div class="text-left">
                                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-600 text-white text-[9px] font-black uppercase tracking-widest mb-4 shadow-lg shadow-blue-100">
                                        <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-blue-100"></span></span>
                                        {{ $status == 'uploading' ? 'Lengkapi Dokumen' : 'Sedang Diverifikasi' }}
                                    </div>
                                    <h2 class="text-2xl lg:text-3xl font-black text-slate-900 uppercase tracking-tighter">{{ $activeSubmission->title }}</h2>
                                    <p class="text-slate-400 text-[11px] font-bold uppercase tracking-widest mt-1">Dibuat Pada: {{ $activeSubmission->created_at->format('d M Y') }}</p>
                                </div>
                            </div>

                            @if($status == 'uploading')
                                <form id="documentUploadForm" action="{{ route('survailen.store.docs', $activeSubmission->id) }}" method="POST" enctype="multipart/form-data" class="space-y-12">
                                    @csrf
                                    <input type="hidden" name="submit_action" id="formSubmitAction" value="draft">
                                    
                                    @php
                                        $fileGroups = [
                                            'Legalitas & Administrasi' => ['file_oss' => 'NIB / Izin OSS', 'file_mou' => 'MoU Kerjasama (Opsional)', 'file_izin_lainnya' => 'Izin Pendukung'],
                                            'Sistem Manajemen Mutu' => ['file_manual_mutu' => 'Manual Mutu', 'file_prosedur_pelatihan' => 'SOP Pelatihan'],
                                            'Monitoring & Rekaman' => ['file_pantau_mutu' => 'Hasil Pantau Mutu', 'file_rekaman_lainnya' => 'Rekaman Implementasi (Opsional)'],
                                            'Kinerja & Rencana' => ['file_lapkin' => 'Laporan Kinerja', 'file_kak' => 'KAK Kegiatan'],
                                            'Sumber Daya Manusia' => ['file_daftar_manajemen' => 'Daftar Manajemen', 'file_daftar_pengajar' => 'Daftar Pengajar'],
                                            'Sarana & Prasarana' => ['file_daftar_sarana' => 'Daftar Sarana', 'file_daftar_prasarana' => 'Daftar Gedung'],
                                            'Kurikulum & Materi' => ['file_kurikulum' => 'Dokumen Kurikulum', 'file_modul' => 'Modul Pelatihan', 'file_bahan_ajar' => 'Bahan Ajar']
                                        ];
                                    @endphp

                                    @foreach($fileGroups as $groupLabel => $files)
                                        <div class="space-y-5">
                                            <h4 class="text-[11px] lg:text-xs font-black text-slate-400 uppercase tracking-[0.3em] border-l-4 border-blue-600 pl-4 leading-none text-left">{{ $groupLabel }}</h4>
                                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 text-left">
                                                @foreach($files as $field => $label)
                                                    <div class="relative group">
                                                        @php 
                                                            $hasFile = $activeSubmission->details && $activeSubmission->details->$field; 
                                                            $isOptional = str_contains($label, 'Opsional');
                                                        @endphp
                                                        <div id="box_{{ $field }}" class="{{ $hasFile ? 'bg-emerald-50 border-emerald-200' : 'bg-slate-50 border-slate-200' }} border-2 border-dashed rounded-3xl p-6 lg:p-8 transition-all group-hover:border-blue-400 group-hover:bg-white flex flex-col items-center text-center h-full relative overflow-hidden shadow-sm">
                                                            <div class="w-12 h-12 lg:w-14 lg:h-14 {{ $hasFile ? 'bg-emerald-500 text-white shadow-emerald-100' : 'bg-white text-blue-500 shadow-slate-100' }} rounded-2xl flex items-center justify-center mb-4 shadow-lg transition-all group-hover:scale-110 group-hover:rotate-3">
                                                                <i class="fas {{ $hasFile ? 'fa-check' : 'fa-file-pdf' }} text-base lg:text-xl"></i>
                                                            </div>
                                                            <span class="text-sm lg:text-base font-black text-slate-800 uppercase tracking-tight leading-tight mb-2">
                                                                {{ $label }} @if(!$isOptional && !$hasFile) <span class="text-rose-500">*</span> @endif
                                                            </span>
                                                            <p id="label_{{ $field }}" class="text-[11px] lg:text-[12px] {{ $hasFile ? 'text-emerald-600' : 'text-slate-400' }} font-bold truncate max-w-full italic px-2">
                                                                {{ $hasFile ? 'Sudah Terunggah' : 'PDF (Maks. 2MB)' }}
                                                            </p>
                                                        </div>
                                                        <input type="file" name="{{ $field }}" 
                                                            data-label="{{ $label }}"
                                                            data-has-file="{{ $hasFile ? 'true' : 'false' }}"
                                                            data-optional="{{ $isOptional ? 'true' : 'false' }}"
                                                            onchange="updateFileName(this, '{{ $field }}')" class="absolute inset-0 opacity-0 cursor-pointer" accept=".pdf">
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach

                                    <div class="flex flex-col sm:flex-row gap-5 pt-10 border-t border-slate-100">
                                        <button type="button" onclick="submitAsDraft()" class="flex-1 bg-slate-100 text-slate-700 py-5 px-8 rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] hover:bg-slate-200 transition-all flex items-center justify-center gap-3">
                                            <i class="fas fa-save"></i> Simpan Draft
                                        </button>
                                        <button type="button" onclick="openConfirmFinalModal()" class="flex-1 bg-blue-600 text-white py-5 px-8 rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] shadow-xl shadow-blue-100 hover:bg-slate-900 transition-all flex items-center justify-center gap-3 active:scale-95">
                                            <i class="fas fa-paper-plane"></i> Kirim Verifikasi
                                        </button>
                                    </div>
                                </form>
                            @else
                                <div class="py-20 lg:py-24 text-center bg-blue-50/20 rounded-[3rem] border border-blue-100/50">
                                    <div class="relative inline-block mb-8">
                                        <div class="absolute inset-0 bg-blue-400 rounded-full animate-ping opacity-10"></div>
                                        <div class="relative w-16 h-16 lg:w-20 lg:h-20 bg-white rounded-3xl flex items-center justify-center text-blue-600 text-2xl lg:text-3xl shadow-xl border border-blue-50"><i class="fas fa-user-shield"></i></div>
                                    </div>
                                    <h3 class="text-xl lg:text-3xl font-black text-slate-900 uppercase tracking-tighter leading-none mb-4 text-center">Verifikasi Berjalan</h3>
                                    <p class="text-slate-400 text-[11px] lg:text-base max-w-sm mx-auto font-medium leading-relaxed px-6 text-center">Asesor BAPETEN sedang melakukan audit dokumen. Hasil akreditasi akan terbit di bagian arsip jika sudah selesai.</p>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- ARSIP AKREDITASI --}}
                    <div class="mt-20 space-y-10 pb-24 text-left">
                        <div class="flex items-center gap-6 text-left">
                            <h3 class="text-[11px] lg:text-[12px] font-black text-slate-400 uppercase tracking-[0.5em] whitespace-nowrap text-left">Arsip Akreditasi</h3>
                            <div class="flex-1 h-px bg-slate-200/80"></div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-left">
                            @forelse($survailens as $hist)
                                <div class="group bg-white p-6 lg:p-8 rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-2xl transition-all relative overflow-hidden text-left">
                                    <div class="flex flex-row items-center gap-6 text-left">
                                        <div class="w-16 h-16 {{ $hist->predikat == 'A' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-blue-50 text-blue-600 border-blue-100' }} rounded-2xl flex items-center justify-center text-3xl font-black shadow-inner border shrink-0 group-hover:scale-110 transition-transform">
                                            {{ $hist->predikat ?? '?' }}
                                        </div>
                                        <div class="flex-1 text-left">
                                            <h4 class="font-black text-slate-900 uppercase tracking-tight text-sm lg:text-base leading-tight mb-2 text-left">{{ $hist->title }}</h4>
                                            <div class="flex flex-wrap items-center gap-3 text-left">
                                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest text-left">{{ $hist->updated_at->format('d M Y') }}</p>
                                                <span class="w-1 h-1 rounded-full bg-slate-200"></span>
                                                <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest text-left">{{ number_format($hist->final_score, 1) }}%</p>
                                            </div>
                                        </div>
                                        <button type="button" data-submission='@json($hist)' onclick='showDetailModal(this, event)' class="w-11 h-11 lg:w-12 lg:h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 hover:bg-slate-900 hover:text-white transition-all shadow-sm active:scale-90">
                                            <i class="fas fa-eye text-base"></i>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full py-20 text-center bg-slate-50/50 rounded-[3rem] border border-dashed border-slate-200 text-center">
                                    <p class="text-slate-300 italic text-[11px] font-bold uppercase tracking-widest text-center">Belum Ada Riwayat Akreditasi</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="mt-8 text-center text-[10px] font-bold text-slate-300 uppercase tracking-widest">
                    &copy; {{ date('Y') }} Sistem Informasi Mutu BAPETEN
                </div>
            </main>
        </div>
    </div>

    {{-- MODAL DETAIL RIWAYAT --}}
    <div id="detailModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-3 lg:p-4 text-left">
        <div class="absolute inset-0 modal-backdrop" onclick="closeDetailModal()"></div>
        <div class="relative bg-white rounded-[2.5rem] w-full max-w-4xl max-h-[92vh] flex flex-col shadow-2xl overflow-hidden animate-pop-in border border-white">
            
            {{-- Header Modal --}}
            <div class="p-6 lg:p-10 flex justify-between items-start shrink-0 border-b border-slate-50 text-left">
                <div class="text-left">
                    <h3 id="modalTitle" class="text-xl lg:text-2xl font-black text-slate-900 uppercase tracking-tighter leading-tight text-left">Detail Survailen</h3>
                    <p id="modalDate" class="text-[9px] lg:text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em] mt-2 text-left"></p>
                </div>
                <button type="button" onclick="closeDetailModal()" class="w-11 h-11 lg:w-12 lg:h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 hover:bg-rose-50 hover:text-rose-500 transition-all active:scale-90"><i class="fas fa-times text-lg"></i></button>
            </div>

            <div class="flex-1 overflow-y-auto px-6 lg:px-12 py-8 lg:py-10 space-y-12 no-scrollbar text-left">
                
                {{-- STATS CARD AREA --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 lg:gap-8">
                    {{-- Predikat --}}
                    <div class="bg-slate-50/50 p-6 rounded-[2rem] border border-slate-100 flex flex-row sm:flex-col justify-between sm:justify-center items-center group">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest sm:mb-3 text-left">Predikat</p>
                        <h4 id="modalPredikat" class="text-4xl lg:text-6xl font-black text-blue-600 leading-none group-hover:scale-110 transition-transform">-</h4>
                    </div>
                    {{-- Skor --}}
                    <div class="bg-slate-50/50 p-6 rounded-[2rem] border border-slate-100 flex flex-row sm:flex-col justify-between sm:justify-center items-center">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest sm:mb-3 text-left">Skor Final</p>
                        <h4 id="modalScore" class="text-2xl lg:text-3xl font-black text-slate-800 leading-none">0%</h4>
                    </div>
                    {{-- Status --}}
                    <div class="bg-slate-50/50 p-6 rounded-[2rem] border border-slate-100 flex flex-row sm:flex-col justify-between sm:justify-center items-center">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest sm:mb-4 text-left text-left">Status</p>
                        <span class="px-5 py-2 bg-emerald-500 text-white rounded-full font-black text-[9px] uppercase tracking-widest shadow-lg shadow-emerald-100 text-center">Lulus</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 text-left">
                    {{-- Rincian Tabel --}}
                    <div class="space-y-5 text-left">
                        <h5 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.3em] pl-2 text-left text-left">Rincian Skor Kategori</h5>
                        <div class="bg-white rounded-[2rem] border border-slate-100 overflow-hidden shadow-sm text-left">
                            <table class="w-full text-[11px] lg:text-[12px] text-left border-collapse">
                                <tbody id="modalTableBody" class="divide-y divide-slate-50 text-left"></tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Berkas Hasil --}}
                    <div class="space-y-6 text-left">
                        <div id="modalFiles" class="space-y-5 text-left">
                            <h5 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.3em] pl-2 text-center sm:text-left text-left text-left">Dokumen Resmi</h5>
                            <div class="grid grid-cols-1 gap-4 text-left">
                                <a id="btnLHS" target="_blank" class="flex items-center justify-between p-5 bg-slate-900 text-white rounded-3xl hover:bg-blue-600 transition-all active:scale-95 shadow-xl shadow-slate-200">
                                    <span class="text-[10px] font-bold uppercase tracking-widest flex items-center gap-4 text-left"><i class="fas fa-file-pdf text-base"></i> Laporan (LHS)</span>
                                    <i class="fas fa-download text-[8px] opacity-50"></i>
                                </a>
                                <a id="btnCert" target="_blank" class="flex items-center justify-between p-5 bg-amber-500 text-white rounded-3xl hover:bg-amber-600 transition-all active:scale-95 shadow-xl shadow-amber-100">
                                    <span class="text-[10px] font-bold uppercase tracking-widest flex items-center gap-4 text-left"><i class="fas fa-award text-base"></i> Sertifikat</span>
                                    <i class="fas fa-download text-[10px] opacity-50"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        {{-- ON PAGE LOAD: Check for Server Errors --}}
        window.addEventListener('DOMContentLoaded', (event) => {
            const errorList = document.getElementById('errorList');
            if (errorList && errorList.children.length > 0) {
                document.getElementById('errorModal').classList.remove('hidden');
                document.getElementById('errorModal').classList.add('flex');
            }
        });

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            document.getElementById(id).classList.remove('flex');
        }

        function openConfirmFinalModal() { 
            // CLIENT SIDE VALIDATION BEFORE CONFIRMATION
            const form = document.getElementById('documentUploadForm');
            const fileInputs = form.querySelectorAll('input[type="file"]');
            let missingFields = [];

            fileInputs.forEach(input => {
                const isOptional = input.getAttribute('data-optional') === 'true';
                const hasFileAlready = input.getAttribute('data-has-file') === 'true';
                const label = input.getAttribute('data-label').replace(' (Opsional)', '');

                // Jika wajib, belum ada di DB, dan input masih kosong
                if (!isOptional && !hasFileAlready && input.files.length === 0) {
                    missingFields.push(label);
                }
            });

            if (missingFields.length > 0) {
                // Tampilkan Modal Peringatan
                const errorList = document.getElementById('errorList');
                errorList.innerHTML = '';
                missingFields.forEach(field => {
                    const li = document.createElement('li');
                    li.className = "text-[11px] font-bold text-rose-600 flex items-start gap-2";
                    li.innerHTML = `<i class="fas fa-caret-right mt-1 shrink-0"></i> <span>Dokumen <b>${field}</b> wajib diunggah</span>`;
                    errorList.appendChild(li);
                });
                document.getElementById('errorModal').classList.remove('hidden');
                document.getElementById('errorModal').classList.add('flex');
            } else {
                // Jika Lengkap, Tampilkan Modal Konfirmasi
                const modal = document.getElementById('confirmFinalModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        function closeConfirmModal() { 
            const modal = document.getElementById('confirmFinalModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function submitFinalFormNow() {
            document.getElementById('formSubmitAction').value = 'final';
            document.getElementById('documentUploadForm').submit();
        }

        function submitAsDraft() {
            document.getElementById('formSubmitAction').value = 'draft';
            document.getElementById('documentUploadForm').submit();
        }

        const weightConfigs = {
            'legalitas': { label: 'Legalitas', bobot: 10, icon: 'fa-building' },
            'mutu': { label: 'Manajemen Mutu', bobot: 20, icon: 'fa-check-double' },
            'rekaman': { label: 'Rekaman Sistem', bobot: 20, icon: 'fa-history' },
            'kinerja': { label: 'Kinerja', bobot: 5, icon: 'fa-chart-line' },
            'sdm': { label: 'Sumber Daya Manusia', bobot: 10, icon: 'fa-users' },
            'sarpras': { label: 'Sarana Prasarana', bobot: 15, icon: 'fa-tools' },
            'kurikulum': { label: 'Kurikulum', bobot: 20, icon: 'fa-book-open' }
        };

        const storageBase = "{{ \Storage::disk('public')->url('') }}".replace(/\/$/, "");

        function showDetailModal(btn, event) {
            if (event) event.preventDefault();
            try {
                const item = JSON.parse(btn.getAttribute('data-submission'));
                const scores = item.evaluator_scores ? JSON.parse(item.evaluator_scores) : {};
                
                document.getElementById('modalTitle').innerText = item.title;
                document.getElementById('modalDate').innerText = 'Validitas Terakhir: ' + new Date(item.updated_at).toLocaleDateString('id-ID', {day:'numeric', month:'long', year:'numeric'});
                document.getElementById('modalPredikat').innerText = item.predikat || 'D';
                document.getElementById('modalScore').innerText = parseFloat(item.final_score).toFixed(1) + '%';

                const tableBody = document.getElementById('modalTableBody');
                tableBody.innerHTML = '';
                Object.keys(weightConfigs).forEach(key => {
                    const score = scores[key] || 0;
                    tableBody.innerHTML += `
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-5 flex items-center gap-4 text-left text-left">
                                <div class="w-9 h-9 bg-slate-100 text-slate-500 rounded-xl flex items-center justify-center shrink-0">
                                    <i class="fas ${weightConfigs[key].icon} text-[12px]"></i>
                                </div>
                                <div class="text-left">
                                    <span class="block font-black text-slate-800 uppercase text-[11px] tracking-tight text-left">${weightConfigs[key].label}</span>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest text-left">Bobot: ${weightConfigs[key].bobot}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <div class="w-9 h-9 inline-flex items-center justify-center rounded-xl bg-blue-600 text-white font-black text-[12px] shadow-lg shadow-blue-100">${score}</div>
                            </td>
                        </tr>
                    `;
                });

                const btnLHS = document.getElementById('btnLHS');
                if(item.admin_file) {
                    btnLHS.href = storageBase + '/' + item.admin_file.replace(/^\//, "");
                    btnLHS.classList.remove('hidden');
                    btnLHS.classList.add('flex');
                } else { btnLHS.classList.remove('flex'); btnLHS.classList.add('hidden'); }

                const btnCert = document.getElementById('btnCert');
                if(item.certificate_file) {
                    btnCert.href = storageBase + '/' + item.certificate_file.replace(/^\//, "");
                    btnCert.classList.remove('hidden');
                    btnCert.classList.add('flex');
                } else { btnCert.classList.remove('flex'); btnCert.classList.add('hidden'); }

                document.getElementById('detailModal').classList.remove('hidden');
                document.getElementById('detailModal').classList.add('flex');
                document.body.style.overflow = 'hidden';
            } catch (err) { console.error(err); }
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function updateFileName(input, fieldId) {
            const label = document.getElementById('label_' + fieldId);
            const box = document.getElementById('box_' + fieldId);
            if (input.files && input.files[0]) {
                label.innerText = input.files[0].name;
                label.className = "text-[11px] lg:text-[12px] text-blue-600 font-black mt-2 truncate max-w-full text-center";
                box.className = "bg-blue-50 border-blue-400 border-2 border-dashed rounded-[2rem] p-6 lg:p-8 transition-all flex flex-col items-center text-center h-full shadow-lg shadow-blue-100/50";
            }
        }
    </script>
</body>
</html>