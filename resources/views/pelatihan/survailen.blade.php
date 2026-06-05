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
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    },
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
        ::-webkit-scrollbar-thumb { background: #bfdbfe; border-radius: 10px; }
        
        .score-radio:checked + label {
            background-color: #2563eb;
            color: white;
            border-color: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px -3px rgba(37, 99, 235, 0.3);
        }
        
        .score-label { transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
        .modal-backdrop { backdrop-filter: blur(12px); background-color: rgba(30, 58, 138, 0.4); }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        
        .file-card { transition: all 0.3s ease; }
        .file-card:hover { border-color: #3b82f6; background-color: #f0f7ff; transform: translateY(-2px); }

        .glass-overlay {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
        }

        @media (max-width: 1024px) {
            .content-area { width: 100vw !important; }
        }
    </style>
</head>
<body class="bg-[#f4f7ff] text-slate-800 antialiased overflow-hidden text-left">

    @php
        $status = $activeSubmission ? $activeSubmission->status : 'none'; 
        
        $components = [
            'file_legalitas' => [
                'label' => 'Aspek Legalitas & Perizinan', 
                'bobot' => '10%', 
                'icon' => 'fa-building',
                'hint'  => 'Izin OSS/NIB , Akta Pendirian, MOU & Surat Izin Operasional.'
            ],
            'file_mutu'      => [
                'label' => 'Sistem Manajemen Mutu (SMM)', 
                'bobot' => '20%', 
                'icon' => 'fa-check-double',
                'hint'  => 'Manual Mutu, Kebijakan Mutu, & Prosedur Instruksi Kerja.'
            ],
            'file_rekaman'   => [
                'label' => 'Rekaman & Laporan Implementasi', 
                'bobot' => '20%', 
                'icon' => 'fa-history',
                'hint'  => 'Laporan Tahunan, Rekaman Teknis dan Rekaman Mutu.'
            ],
            'file_kinerja'   => [
                'label' => 'Laporan Kinerja & KAK', 
                'bobot' => '5%', 
                'icon' => 'fa-chart-line',
                'hint'  => 'Laporan Kinerja Tahunan & KAK program terbaru.'
            ],
            'file_sdm'       => [
                'label' => 'Sumber Daya Manusia (SDM)', 
                'bobot' => '10%', 
                'icon' => 'fa-users',
                'hint'  => 'Data Personil (KTP, Sertifikat Pelatihan/Kompetensi) dan SK Personil.'
            ],
            'file_sarpras'   => [
                'label' => 'Sarana & Prasarana Penunjang', 
                'bobot' => '15%', 
                'icon' => 'fa-tools',
                'hint'  => 'Daftar Inventaris, Foto Sarpras, Denah Lokasi, dan Sertifikat Kalibrasi Alat.'
            ],
            'file_kurikulum' => [
                'label' => 'Kurikulum, Modul & Bahan Ajar', 
                'bobot' => '20%', 
                'icon' => 'fa-book-open',
                'hint'  => 'Silabus, Kurikulum, Modul Pelatihan, Bahan Tayang, dan Acu Silang.'
            ],
        ];
    @endphp

    <x-success-popup />

    <div class="flex h-screen overflow-hidden w-full">
        
        <!-- === MOBILE OVERLAY === -->
        <div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 z-40 hidden lg:hidden glass-overlay transition-opacity duration-300"></div>

        <!-- === SIDEBAR WRAPPER === -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-2xl lg:shadow-none transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto flex flex-col h-full border-r border-slate-200">
            @include('components.pelatihan-sidebar')
        </aside>

        <div class="flex-1 flex flex-col h-screen overflow-hidden relative min-w-0 content-area">
            
            <!-- === MOBILE HEADER BAR === -->
            <div class="lg:hidden bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between sticky top-0 z-30 shadow-sm shrink-0">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="p-2 text-slate-600 hover:text-primary-600 hover:bg-slate-100 rounded-lg transition-colors">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <span class="font-bold text-slate-800 text-sm tracking-wide uppercase">SI-MUTU <span class="text-primary-600">Survailen</span></span>
                </div>
                <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 text-[10px] font-bold border border-primary-200">
                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                </div>
            </div>

            <!-- Header Desktop -->
            <div class="hidden lg:block shrink-0">
                @include('components.pelatihan-header', ['title' => 'Survailen Berkala', 'subtitle' => 'Audit Mutu & Akreditasi'])
            </div>

            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-8 lg:p-12 no-scrollbar text-left">
                <div class="max-w-5xl mx-auto space-y-8 md:space-y-12">

                    {{-- === 1. TAMPILAN UTAMA (STATUS: NONE) === --}}
                    @if($status == 'none')
                        <div id="welcomeCard" class="bg-white rounded-[2rem] p-8 md:p-16 text-center border border-blue-100 shadow-xl shadow-blue-900/5 animate-fade-in-up">
                            <div class="w-20 h-20 md:w-32 md:h-32 bg-primary-50 text-primary-600 rounded-[2rem] flex items-center justify-center mx-auto mb-8 text-4xl md:text-6xl border border-primary-100 shadow-inner">
                                <i class="fas fa-file-signature"></i>
                            </div>
                            <h2 class="text-xl md:text-4xl font-extrabold text-primary-900 tracking-tight mb-4 text-center">Pengajuan Survailen</h2>
                            <p class="text-slate-500 text-xs md:text-lg max-w-lg mx-auto font-medium mb-10 leading-relaxed text-center">
                                Silakan mulai proses survailen berkala dengan mengisi <span class="text-primary-600 font-bold">Penilaian Mandiri</span> dan melengkapi berkas pendukung.
                            </p>
                            <button type="button" onclick="showAssessmentForm()" 
                                class="bg-primary-600 text-white px-10 py-5 rounded-2xl font-bold text-xs md:text-sm uppercase tracking-widest hover:bg-primary-700 transition-all shadow-lg active:scale-95 flex items-center gap-4 mx-auto">
                                Mulai Sekarang <i class="fas fa-arrow-right text-[10px]"></i>
                            </button>
                        </div>

                        <div id="assessmentSection" class="hidden bg-white rounded-[2rem] p-5 md:p-12 shadow-2xl border border-blue-50 animate-fade-in-up">
                            <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-5 border-b border-slate-100 pb-8">
                                <div class="flex items-center gap-5">
                                    <div class="w-16 h-16 bg-primary-600 text-white rounded-2xl flex items-center justify-center text-2xl shadow-lg shadow-primary-200"><i class="fas fa-tasks"></i></div>
                                    <div class="text-left">
                                        <h3 class="text-xl md:text-2xl font-extrabold text-primary-900 uppercase tracking-tight">1. Penilaian Mandiri</h3>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Berikan estimasi skor sesuai kondisi lembaga saat ini</p>
                                    </div>
                                </div>
                                <!-- Button Toggle Guide -->
                                <button type="button" onclick="toggleScoreGuide()" class="flex items-center gap-3 px-6 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-600 hover:bg-primary-50 hover:border-primary-200 hover:text-primary-700 transition-all group shrink-0">
                                    <i id="guideIcon" class="fas fa-info-circle text-primary-500 group-hover:scale-110 transition-transform"></i>
                                    <span class="text-xs font-black uppercase tracking-widest">Lihat Panduan Skor</span>
                                </button>
                            </div>

                            <!-- === SKEMA SKOR LEGEND (COLLAPSIBLE - PERSINGKAT) === -->
                            <div id="scoreGuide" class="hidden overflow-hidden mb-10 p-6 bg-slate-50/50 rounded-[2rem] border border-slate-100 animate-fade-in-up">
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                                    <!-- Skor 4 -->
                                    <div class="bg-white p-5 rounded-2xl border-l-4 border-emerald-500 shadow-sm">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="w-8 h-8 flex items-center justify-center bg-emerald-100 text-emerald-700 rounded-lg font-black text-sm">4</span>
                                            <span class="text-xs font-black text-emerald-600 uppercase tracking-tight">Sangat Memenuhi</span>
                                        </div>
                                        <p class="text-[11px] text-slate-500 leading-snug font-semibold uppercase tracking-tight">Bukti sangat kuat & lengkap.</p>
                                    </div>
                                    <!-- Skor 3 -->
                                    <div class="bg-white p-5 rounded-2xl border-l-4 border-blue-500 shadow-sm">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="w-8 h-8 flex items-center justify-center bg-blue-100 text-blue-700 rounded-lg font-black text-sm">3</span>
                                            <span class="text-xs font-black text-blue-600 uppercase tracking-tight">Memenuhi</span>
                                        </div>
                                        <p class="text-[11px] text-slate-500 leading-snug font-semibold uppercase tracking-tight">Bukti memadai & sesuai.</p>
                                    </div>
                                    <!-- Skor 2 -->
                                    <div class="bg-white p-5 rounded-2xl border-l-4 border-amber-500 shadow-sm">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="w-8 h-8 flex items-center justify-center bg-amber-100 text-amber-700 rounded-lg font-black text-sm">2</span>
                                            <span class="text-xs font-black text-amber-600 uppercase tracking-tight">Cukup Memenuhi</span>
                                        </div>
                                        <p class="text-[11px] text-slate-500 leading-snug font-semibold uppercase tracking-tight">Bukti kurang / sebagian.</p>
                                    </div>
                                    <!-- Skor 1 -->
                                    <div class="bg-white p-5 rounded-2xl border-l-4 border-rose-500 shadow-sm">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="w-8 h-8 flex items-center justify-center bg-rose-100 text-rose-700 rounded-lg font-black text-sm">1</span>
                                            <span class="text-xs font-black text-rose-600 uppercase tracking-tight">Kurang Memenuhi</span>
                                        </div>
                                        <p class="text-[11px] text-slate-500 leading-snug font-semibold uppercase tracking-tight">Bukti tidak ada / minim.</p>
                                    </div>
                                </div>
                            </div>

                            <form action="{{ route('survailen.store.self') }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="category" value="{{ auth()->user()->category }}">

                                <div class="grid grid-cols-1 gap-3">
                                    @foreach($components as $key => $data)
                                    <div class="flex flex-col md:flex-row items-center justify-between p-5 md:px-8 rounded-3xl border border-slate-100 hover:border-primary-300 hover:bg-primary-50/30 transition-all bg-slate-50/50 group">
                                        <div class="mb-4 md:mb-0 text-center md:text-left flex items-center gap-4">
                                            <div class="hidden md:flex w-10 h-10 bg-white rounded-xl items-center justify-center text-primary-600 shadow-sm group-hover:bg-primary-600 group-hover:text-white transition-colors">
                                                <i class="fas {{ $data['icon'] }} text-sm"></i>
                                            </div>
                                            <div class="text-left">
                                                <span class="block font-bold text-slate-800 text-sm md:text-base tracking-tight group-hover:text-primary-700 text-left">{{ $data['label'] }}</span>
                                                <span class="text-[10px] font-bold text-primary-500 uppercase tracking-widest mt-0.5 inline-block">Bobot: {{ $data['bobot'] }}</span>
                                            </div>
                                        </div>
                                        <div class="flex gap-2 md:gap-3">
                                            @for($i=1; $i<=4; $i++)
                                                <input type="radio" name="scores[{{ $key }}]" value="{{ $i }}" id="s_{{ $key }}_{{ $i }}" class="hidden score-radio" required>
                                                <label for="s_{{ $key }}_{{ $i }}" class="score-label w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-xl md:rounded-2xl border-2 border-white bg-white text-slate-400 font-black text-sm cursor-pointer shadow-sm">{{ $i }}</label>
                                            @endfor
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <div class="pt-10 flex justify-center">
                                    <button type="submit" class="w-full md:w-auto bg-primary-900 text-white font-bold px-12 py-5 rounded-2xl shadow-xl hover:bg-primary-600 transition-all text-xs uppercase tracking-widest active:scale-95">
                                        Simpan & Lanjut <i class="fas fa-chevron-right ml-2"></i>
                                    </button>
                                </div>
                            </form>
                        </div>

                    {{-- === 2. TAMPILAN UPLOAD (STATUS: UPLOADING) === --}}
                    @elseif($status == 'uploading')
                        <div class="bg-white rounded-[2.5rem] p-6 md:p-12 shadow-xl border border-blue-50 animate-fade-in-up">
                            <div class="mb-10 text-left">
                                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary-600 text-white text-[10px] font-bold uppercase tracking-widest mb-6 shadow-md shadow-primary-200">
                                    <i class="fas fa-file-upload"></i> Unggah Dokumen
                                </div>
                                <h2 class="text-2xl md:text-3xl font-extrabold text-primary-900 tracking-tight">2. Berkas Desk Evaluation</h2>
                                <p class="text-slate-500 text-xs md:text-sm font-medium mt-2 leading-relaxed">
                                    Unggah bukti fisik pendukung. Anda dapat mengunggah <span class="text-primary-600 font-bold underline decoration-primary-200 decoration-2 underline-offset-4">beberapa PDF sekaligus</span> per kategori.
                                </p>
                            </div>

                            <form action="{{ route('survailen.store.docs', $activeSubmission->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($components as $key => $data)
                                    <div class="relative group">
                                        <div class="file-card bg-slate-50 border-2 border-dashed border-slate-200 rounded-3xl p-5 flex items-center justify-between">
                                            <div class="flex items-center gap-4 overflow-hidden">
                                                <div class="w-10 h-10 md:w-12 h-12 bg-white rounded-xl md:rounded-2xl flex items-center justify-center text-primary-500 shadow-sm shrink-0 border border-slate-100 group-hover:rotate-12 transition-transform">
                                                    <i class="fas {{ $data['icon'] }} text-base md:text-lg"></i>
                                                </div>
                                                <div class="overflow-hidden flex flex-col">
                                                    <p id="label_{{ $key }}" class="text-[10px] md:text-xs font-bold text-slate-700 uppercase truncate pr-4 text-left">{{ $data['label'] }}</p>
                                                    
                                                    <!-- Hint yang muncul saat hover kartu -->
                                                    <div class="h-0 group-hover:h-auto overflow-hidden opacity-0 group-hover:opacity-100 transform translate-y-1 group-hover:translate-y-0 transition-all duration-300">
                                                        <p class="text-[9px] text-primary-600 font-bold mt-1 leading-tight italic text-left uppercase tracking-tight">
                                                            <i class="fas fa-info-circle mr-1"></i> {{ $data['hint'] }}
                                                        </p>
                                                    </div>
                                                    
                                                    <p id="count_{{ $key }}" class="text-[9px] md:text-[10px] font-bold text-slate-400 uppercase mt-1 tracking-wider text-left">Pilih Berkas PDF...</p>
                                                </div>
                                            </div>
                                            <div class="w-8 h-8 md:w-9 h-9 rounded-xl bg-primary-900 text-white flex items-center justify-center text-[10px] shrink-0 shadow-lg group-hover:bg-primary-600 transition-colors">
                                                <i class="fas fa-plus"></i>
                                            </div>
                                        </div>
                                        <input type="file" name="files[{{ $key }}][]" multiple 
                                               onchange="handleFilesSelect(this, 'label_{{ $key }}', 'count_{{ $key }}', '{{ $data['label'] }}')" 
                                               class="absolute inset-0 opacity-0 cursor-pointer" accept=".pdf" required>
                                    </div>
                                    @endforeach
                                </div>

                                <div class="pt-10 flex justify-center">
                                    <button type="submit" class="w-full md:w-auto bg-primary-600 text-white font-bold px-12 md:px-16 py-5 rounded-2xl shadow-xl shadow-primary-100 hover:bg-primary-900 transition-all active:scale-95 text-xs uppercase tracking-widest">
                                        Kirim Verifikasi <i class="fas fa-paper-plane ml-3"></i>
                                    </button>
                                </div>
                            </form>
                        </div>

                    {{-- === 3. TAMPILAN VERIFIKASI (STATUS: VERIFICATION) === --}}
                    @elseif($status == 'verification')
                        <div class="bg-white rounded-[2.5rem] md:rounded-[3rem] p-10 md:p-24 text-center shadow-2xl shadow-blue-900/5 border border-blue-50 animate-fade-in-up">
                            <div class="relative inline-block mb-10">
                                <div class="absolute inset-0 bg-primary-400 rounded-full animate-ping opacity-10"></div>
                                <div class="relative w-24 h-24 md:w-32 md:h-32 bg-primary-50 rounded-[2.5rem] md:rounded-[3rem] flex items-center justify-center border border-primary-100 shadow-inner">
                                    <i class="fas fa-user-shield text-4xl md:text-5xl text-primary-600"></i>
                                </div>
                            </div>
                            <h3 class="text-xl md:text-3xl font-extrabold text-primary-900 tracking-tight mb-4 text-center">Audit Desk Evaluation</h3>
                            <p class="text-slate-500 text-xs md:text-lg font-medium max-w-lg mx-auto leading-relaxed text-center">
                                Tim Asesor sedang melakukan verifikasi dokumen Anda. Hasil akhir dan sertifikat akan terbit secara otomatis setelah evaluasi selesai.
                            </p>
                        </div>

                    {{-- === 4. TAMPILAN SELESAI (STATUS: COMPLETED) === --}}
                    @elseif($status == 'completed')
                        <div class="space-y-8 animate-fade-in-up">
                            <div class="bg-primary-900 rounded-[2.5rem] md:rounded-[3rem] p-10 md:p-16 text-white text-center relative overflow-hidden shadow-2xl shadow-primary-200">
                                <div class="absolute -top-10 -right-10 opacity-5 rotate-12"><i class="fas fa-award text-[15rem] md:text-[20rem]"></i></div>
                                
                                <p class="text-[10px] md:text-xs font-bold uppercase tracking-[0.3em] text-primary-300 mb-6 text-center">Predikat Hasil Survailen</p>
                                <h2 class="text-7xl md:text-8xl font-black tracking-tighter leading-none mb-4 text-center">{{ $activeSubmission->predikat ?? 'A' }}</h2>
                                
                                <div class="mt-10 flex flex-wrap justify-center gap-4">
                                    <div class="bg-white/10 backdrop-blur-md px-6 md:px-8 py-4 md:py-5 rounded-3xl border border-white/20 shadow-xl">
                                        <p class="text-[9px] md:text-[10px] font-bold uppercase opacity-60 tracking-wider">Skor Akhir</p>
                                        <p class="text-2xl md:text-3xl font-black text-blue-100">{{ number_format($activeSubmission->final_score, 0) }}%</p>
                                    </div>
                                    <button type="button" data-submission='@json($activeSubmission)' onclick='showDetailModal(this)' 
                                            class="bg-white text-primary-900 px-8 md:px-10 py-4 md:py-5 rounded-3xl font-bold text-[10px] md:text-xs uppercase tracking-widest hover:bg-blue-50 transition-all shadow-xl flex items-center gap-3">
                                        <i class="fas fa-chart-pie"></i> Rincian Nilai
                                    </button>
                                </div>
                            </div>

                            @if($activeSubmission->admin_file || $activeSubmission->certificate_file)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                                @if($activeSubmission->admin_file)
                                <a href="{{ asset('storage/' . $activeSubmission->admin_file) }}" target="_blank" class="bg-white p-6 md:p-7 rounded-[2rem] border border-slate-100 flex items-center justify-between group hover:border-primary-400 transition-all shadow-sm">
                                    <div class="flex items-center gap-4 md:gap-5">
                                        <div class="w-12 h-12 md:w-14 md:h-14 bg-primary-50 text-primary-600 rounded-2xl flex items-center justify-center text-xl md:text-2xl group-hover:bg-primary-600 group-hover:text-white transition-all"><i class="fas fa-file-pdf"></i></div>
                                        <span class="text-[10px] md:text-xs font-bold uppercase tracking-widest text-slate-700">Laporan Hasil</span>
                                    </div>
                                    <i class="fas fa-download text-slate-300 group-hover:text-primary-600 transition-colors mr-2"></i>
                                </a>
                                @endif

                                @if($activeSubmission->certificate_file)
                                <a href="{{ asset('storage/' . $activeSubmission->certificate_file) }}" target="_blank" class="bg-white p-6 md:p-7 rounded-[2rem] border border-slate-100 flex items-center justify-between group hover:border-amber-400 transition-all shadow-sm">
                                    <div class="flex items-center gap-4 md:gap-5">
                                        <div class="w-12 h-12 md:w-14 md:h-14 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center text-xl md:text-2xl group-hover:bg-amber-600 group-hover:text-white transition-all"><i class="fas fa-award"></i></div>
                                        <span class="text-[10px] md:text-xs font-bold uppercase tracking-widest text-slate-700">Sertifikat</span>
                                    </div>
                                    <i class="fas fa-download text-slate-300 group-hover:text-amber-600 transition-colors mr-2"></i>
                                </a>
                                @endif
                            </div>
                            @endif
                        </div>
                    @endif

                    {{-- === ARSIP / RIWAYAT === --}}
                    @if($survailens->count() > 0)
                    <div class="mt-16 md:mt-20 space-y-8 pb-10">
                        <div class="flex items-center gap-4 md:gap-6">
                            <h3 class="text-[9px] md:text-[11px] font-black text-primary-400 uppercase tracking-[0.3em] md:tracking-[0.5em] whitespace-nowrap">Arsip Akreditasi</h3>
                            <div class="flex-1 h-px bg-slate-200"></div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-5">
                            @foreach($survailens as $hist)
                            <div class="group bg-white p-5 md:p-6 rounded-[1.5rem] md:rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-xl hover:border-primary-200 transition-all relative overflow-hidden flex items-center gap-4 md:gap-5">
                                <div class="w-14 h-14 md:w-16 md:h-16 bg-primary-50 text-primary-800 border border-primary-100 rounded-xl md:rounded-2xl flex items-center justify-center text-xl md:text-2xl font-black shrink-0 group-hover:scale-105 transition-transform shadow-inner">
                                    {{ $hist->predikat ?? '?' }}
                                </div>
                                <div class="flex-1 text-left overflow-hidden">
                                    <h4 class="font-bold text-primary-900 uppercase tracking-tight text-xs md:text-sm leading-tight truncate text-left">{{ $hist->title }}</h4>
                                    <p class="text-[9px] md:text-[10px] font-bold text-slate-400 uppercase mt-2 tracking-widest text-left">{{ $hist->updated_at->format('d M Y') }} • Skor: {{ number_format($hist->final_score, 0) }}%</p>
                                </div>
                                <button type="button" data-submission='@json($hist)' onclick='showDetailModal(this)' class="w-10 h-10 md:w-12 md:h-12 bg-slate-50 text-slate-400 rounded-xl md:rounded-2xl flex items-center justify-center hover:bg-primary-900 hover:text-white transition-all active:scale-90 shadow-sm shrink-0"><i class="fas fa-eye text-sm md:text-base"></i></button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>
            </main>
        </div>
    </div>

    {{-- MODAL DETAIL --}}
    <div id="detailModal" class="fixed inset-0 z-[300] hidden items-center justify-center p-4">
        <div class="absolute inset-0 modal-backdrop" onclick="closeDetailModal()"></div>
        <div class="relative bg-white rounded-[2rem] md:rounded-[2.5rem] w-full max-w-4xl max-h-[92vh] flex flex-col shadow-2xl overflow-hidden animate-pop-in border-[8px] md:border-[12px] border-white text-left">
            <div class="bg-primary-900 p-6 md:p-8 text-white flex justify-between items-center shrink-0">
                <div class="text-left">
                    <h3 class="text-xl md:text-2xl font-black uppercase tracking-tight text-left">Detail Hasil Penilaian</h3>
                    <p id="modalTitleDisplay" class="text-[10px] md:text-[11px] text-blue-300 font-bold uppercase tracking-widest mt-1 italic text-left"></p>
                </div>
                <button onclick="closeDetailModal()" class="w-10 h-10 md:w-12 md:h-12 bg-white/10 rounded-xl md:rounded-2xl flex items-center justify-center hover:bg-white text-white hover:text-primary-900 transition-all"><i class="fas fa-times"></i></button>
            </div>
            
            <div class="flex-1 overflow-y-auto p-5 md:p-8 space-y-6 no-scrollbar text-left">
                <div id="modalFilesContainer" class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4 hidden"></div>

                <div class="bg-white rounded-[1.5rem] md:rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left min-w-[500px]">
                            <thead class="bg-slate-50 text-slate-400 font-bold uppercase text-[9px] md:text-[10px] tracking-widest border-b">
                                <tr>
                                    <th class="px-6 md:px-8 py-4 md:py-5 text-left">Kategori Instrumen</th>
                                    <th class="px-4 md:px-6 py-4 md:py-5 text-center">Skor</th>
                                    <th class="px-6 md:px-8 py-4 md:py-5 text-left">Review Asesor</th>
                                </tr>
                            </thead>
                            <tbody id="modalTableBody" class="divide-y divide-slate-50 text-left"></tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-primary-600 p-6 md:p-10 rounded-[1.5rem] md:rounded-[2.5rem] text-white flex flex-col items-center justify-center text-center shadow-xl shadow-primary-100 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-5 pointer-events-none"><i class="fas fa-check-double text-7xl md:text-9xl"></i></div>
                    <p class="text-[9px] font-bold uppercase tracking-[0.2em] opacity-70 mb-1">Skor Akhir Akumulasi</p>
                    <h4 id="modalScoreDisplay" class="text-2xl md:text-4xl font-black leading-none tracking-tight">0%</h4>
                    <div id="modalPredikatDisplay" class="bg-white/20 backdrop-blur-md text-white px-5 py-1.5 rounded-full text-[10px] font-bold uppercase mt-3 tracking-widest border border-white/20">-</div>
                    <div id="modalAdminNoteContainer" class="mt-6 w-full max-w-lg border-t border-white/20 pt-5 hidden">
                        <p class="text-[9px] font-bold uppercase tracking-[0.3em] opacity-60 mb-2">Catatan Verifikator</p>
                        <p id="modalAdminNoteDisplay" class="text-[11px] md:text-xs italic leading-relaxed text-blue-50 bg-white/5 p-4 rounded-xl border border-white/10 text-left"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const weightConfigs = @json($components);
        const storageBaseUrl = "{{ asset('storage/') }}/";

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        function toggleScoreGuide() {
            const guide = document.getElementById('scoreGuide');
            const icon = document.getElementById('guideIcon');
            const isHidden = guide.classList.toggle('hidden');
            
            if (!isHidden) {
                guide.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                icon.classList.replace('fa-info-circle', 'fa-times-circle');
            } else {
                icon.classList.replace('fa-times-circle', 'fa-info-circle');
            }
        }

        function showAssessmentForm() {
            const card = document.getElementById('welcomeCard');
            const section = document.getElementById('assessmentSection');
            card.classList.add('opacity-0', 'scale-95');
            setTimeout(() => {
                card.classList.add('hidden');
                section.classList.remove('hidden');
                section.scrollIntoView({ behavior: 'smooth' });
            }, 300);
        }

        function handleFilesSelect(input, labelId, countId, original) {
            const label = document.getElementById(labelId);
            const count = document.getElementById(countId);
            if (input.files && input.files.length > 0) {
                label.innerText = original;
                label.classList.add('text-primary-600', 'font-black');
                count.innerText = input.files.length + " FILE TERPILIH (SIAP UNGGAH)";
                count.classList.replace('text-slate-400', 'text-primary-600');
                input.parentElement.classList.add('border-primary-400', 'bg-primary-50/50');
            } else {
                label.innerText = original;
                label.classList.remove('text-primary-600', 'font-black');
                count.innerText = "PILIH BERKAS PDF";
                count.classList.replace('text-primary-600', 'text-slate-400');
                input.parentElement.classList.remove('border-primary-400', 'bg-primary-50/50');
            }
        }

        function showDetailModal(btn) {
            const data = JSON.parse(btn.getAttribute('data-submission'));
            const evalScores = data.evaluator_scores ? JSON.parse(data.evaluator_scores) : {};
            const evalComments = data.evaluator_comments ? JSON.parse(data.evaluator_comments) : {};

            document.getElementById('modalTitleDisplay').innerText = data.title;
            document.getElementById('modalScoreDisplay').innerText = Math.round(parseFloat(data.final_score)) + "%";
            document.getElementById('modalPredikatDisplay').innerText = "Predikat " + (data.predikat || "D");

            const noteContainer = document.getElementById('modalAdminNoteContainer');
            const noteDisplay = document.getElementById('modalAdminNoteDisplay');
            if (data.admin_note) {
                noteContainer.classList.remove('hidden');
                noteDisplay.innerText = data.admin_note;
            } else {
                noteContainer.classList.add('hidden');
            }

            const filesContainer = document.getElementById('modalFilesContainer');
            filesContainer.innerHTML = '';
            let hasFiles = false;

            if (data.admin_file) {
                hasFiles = true;
                filesContainer.innerHTML += `
                    <a href="${storageBaseUrl}${data.admin_file}" target="_blank" class="bg-blue-50 p-4 rounded-2xl border border-blue-100 flex items-center justify-between group hover:bg-primary-600 hover:text-white transition-all shadow-sm">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-file-medical text-lg"></i>
                            <span class="text-[10px] font-bold uppercase tracking-tight">Laporan Hasil Survailen</span>
                        </div>
                        <i class="fas fa-external-link-alt text-xs opacity-50"></i>
                    </a>
                `;
            }

            if (data.certificate_file) {
                hasFiles = true;
                filesContainer.innerHTML += `
                    <a href="${storageBaseUrl}${data.certificate_file}" target="_blank" class="bg-amber-50 p-4 rounded-2xl border border-amber-100 flex items-center justify-between group hover:bg-amber-600 hover:text-white transition-all shadow-sm text-amber-900">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-award text-lg"></i>
                            <span class="text-[10px] font-bold uppercase tracking-tight">Sertifikat Akreditasi</span>
                        </div>
                        <i class="fas fa-external-link-alt text-xs opacity-50"></i>
                    </a>
                `;
            }

            filesContainer.classList.toggle('hidden', !hasFiles);

            const tableBody = document.getElementById('modalTableBody');
            tableBody.innerHTML = '';

            Object.keys(weightConfigs).forEach(key => {
                const score = evalScores[key] || '-';
                const comment = evalComments[key] || '<span class="text-slate-300 italic">Tidak ada catatan asesor</span>';
                
                tableBody.innerHTML += `
                    <tr class="hover:bg-slate-50 transition-colors text-left">
                        <td class="px-6 md:px-8 py-4 md:py-5 text-left">
                            <div class="flex items-center gap-3 md:gap-4 text-left">
                                <div class="hidden sm:flex w-9 h-9 md:w-10 md:h-10 bg-primary-50 text-primary-600 rounded-xl items-center justify-center shrink-0 border border-primary-100">
                                    <i class="fas ${weightConfigs[key].icon} text-[10px] md:text-xs"></i>
                                </div>
                                <span class="font-bold text-slate-700 tracking-tight text-[10px] md:text-[11px] text-left uppercase">${weightConfigs[key].label}</span>
                            </div>
                        </td>
                        <td class="px-4 md:px-6 py-4 md:py-5 text-center">
                            <span class="w-8 h-8 md:w-10 md:h-10 inline-flex items-center justify-center rounded-lg md:rounded-xl bg-primary-900 text-white font-black text-[10px] md:text-xs shadow-md">
                                ${score}
                            </span>
                        </td>
                        <td class="px-6 md:px-8 py-4 md:py-5 text-left">
                            <p class="text-[10px] md:text-[11px] font-medium text-slate-500 leading-relaxed text-left">${comment}</p>
                        </td>
                    </tr>
                `;
            });

            document.getElementById('detailModal').classList.replace('hidden', 'flex');
            document.body.style.overflow = 'hidden';
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.replace('flex', 'hidden');
            document.body.style.overflow = '';
        }
    </script>
</body>
</html>
