<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>SI-MUTU DKKN | BAPETEN</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        bapeten: {
                            blue: '#0054a6',
                            gold: '#c9a050',
                            dark: '#003366'
                        }
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'pulse-slow': 'pulse 10s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'fade-in': 'fadeIn 1s ease-out forwards',
                        'slide-up': 'slideUp 0.8s ease-out forwards',
                        'slow-pan': 'slowPan 30s linear infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(30px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        slowPan: {
                            '0%': { backgroundPosition: '0% 0%' },
                            '100%': { backgroundPosition: '100% 100%' },
                        }
                    }
                }
            }
        }
    </script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            opacity: 0; 
            transition: opacity 0.8s ease-in-out; 
        }
        body.is-ready { opacity: 1; }

        ::-webkit-scrollbar { width: 0px; }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: inset 0 0 20px rgba(255, 255, 255, 0.2);
        }
        .dark .glass-card {
            background: rgba(15, 23, 42, 0.65);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: inset 0 0 20px rgba(255, 255, 255, 0.02);
        }

        /* Subtle Blueprint Grid */
        .bg-pattern {
            background-image: radial-gradient(circle at 1px 1px, currentColor 1px, transparent 1px);
            background-size: 40px 40px;
        }

        .menu-gradient-1 { background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%); }
        .menu-gradient-2 { background: linear-gradient(135deg, #10b981 0%, #065f46 100%); }
        .menu-gradient-3 { background: linear-gradient(135deg, #8b5cf6 0%, #5b21b6 100%); }
        .menu-gradient-4 { background: linear-gradient(135deg, #f59e0b 0%, #b45309 100%); }
        
        .carousel-item {
            display: none;
            animation: fadeIn 0.5s ease-in-out;
        }
        .carousel-item.active {
            display: block;
        }
    </style>

    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
</head>
<body class="bg-[#fcfdfe] dark:bg-[#020617] text-slate-900 dark:text-slate-100 transition-colors duration-500 overflow-x-hidden">

    <button onclick="toggleTheme()" class="fixed top-6 right-6 z-50 p-3 rounded-2xl bg-white dark:bg-slate-800 shadow-xl border border-slate-200 dark:border-slate-700 hover:scale-110 active:scale-95 transition-all group">
        <i id="theme-icon" class="fas fa-moon text-xl text-slate-600 dark:text-yellow-400"></i>
    </button>

    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute inset-0 bg-pattern text-slate-200 dark:text-slate-800/40 opacity-40"></div>
        
        <div class="absolute top-[-10%] left-[-5%] w-[40%] h-[40%] bg-blue-500/10 dark:bg-blue-600/10 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-[40%] h-[40%] bg-indigo-500/10 dark:bg-indigo-600/10 rounded-full blur-[120px]"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-gradient-to-tr from-transparent via-blue-50/20 dark:via-transparent to-transparent"></div>

        <div class="absolute top-20 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-slate-200 dark:via-slate-800 to-transparent"></div>
        <div class="absolute bottom-40 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-slate-200 dark:via-slate-800 to-transparent"></div>
    </div>

    <div id="docModal" class="fixed inset-0 z-[60] hidden opacity-0 transition-opacity duration-300">
        <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-md" onclick="toggleModal()"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[90%] max-w-4xl glass-card rounded-[2.5rem] p-6 md:p-10 shadow-2xl overflow-hidden">
            <button onclick="toggleModal()" class="absolute top-6 right-6 text-slate-400 hover:text-red-500 transition-colors z-30">
                <i class="fas fa-times text-2xl"></i>
            </button>
            <div class="mb-8 text-left">
                <h2 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white mb-2 italic">Gallery Dokumentasi KFJM</h2>
                <div class="h-1.5 w-24 bg-blue-600 rounded-full"></div>
            </div>
            <div class="relative rounded-3xl overflow-hidden aspect-video bg-slate-200 dark:bg-slate-800 shadow-inner group/carousel">
                <div id="carousel-content" class="h-full">
                    <div class="carousel-item active h-full relative">
                        <img src="image/foto1.jpeg" class="absolute inset-0 w-full h-full object-cover" alt="Dokumentasi 1">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent z-10"></div>
                        <div class="absolute bottom-8 left-8 right-8 z-20 text-white text-left">
                            <span class="px-3 py-1 bg-blue-600 rounded-full text-[10px] font-bold uppercase tracking-wider mb-2 inline-block">Konsinyering Evaluasi</span>
                            <h4 class="text-xl md:text-2xl font-bold">Kegiatan Konsinyering Evaluasi LHU</h4>
                            <p class="text-sm text-slate-300 mt-1">
                                Koordinasi dan pembahasan evaluasi Laporan Hasil Uji (LHU) guna mendukung percepatan penyelesaian proses evaluasi dan penerbitan sertifikat uji kesesuaian.
                            </p>
                        </div>
                    </div>
                    <div class="carousel-item h-full relative">
                        <img src="image/foto2.jpeg" class="absolute inset-0 w-full h-full object-cover" alt="Dokumentasi 2">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent z-10"></div>
                        <div class="absolute bottom-8 left-8 right-8 z-20 text-white text-left">
                            <span class="px-3 py-1 bg-blue-600 rounded-full text-[10px] font-bold uppercase tracking-wider mb-2 inline-block">Sertifikasi</span>
                            <h4 class="text-xl md:text-2xl font-bold">Pengujian Praktik Penguji Berkualifikasi</h4>
                            <p class="text-sm text-slate-300 mt-1">
                                Pelaksanaan pengujian praktik guna memastikan kompetensi dan kualifikasi penguji sesuai dengan ketentuan yang berlaku.
                            </p>
                        </div>
                    </div>
                    <div class="carousel-item h-full relative">
                        <img src="image/foto3.jpeg" class="absolute inset-0 w-full h-full object-cover" alt="Dokumentasi 3">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent z-10"></div>
                        <div class="absolute bottom-8 left-8 right-8 z-20 text-white text-left">
                            <span class="px-3 py-1 bg-blue-600 rounded-full text-[10px] font-bold uppercase tracking-wider mb-2 inline-block">Verifikasi</span>
                            <h4 class="text-xl md:text-2xl font-bold">Kegiatan Verifikasi</h4>
                            <p class="text-sm text-slate-300 mt-1">
                                Pelaksanaan kegiatan verifikasi untuk memastikan kesesuaian dokumen, data, and persyaratan sesuai ketentuan yang berlaku.
                            </p>
                        </div>
                    </div>
                </div>
                <button onclick="prevSlide()" class="absolute left-4 top-1/2 -translate-y-1/2 z-30 w-12 h-12 rounded-full bg-black/40 backdrop-blur-sm text-white hover:bg-white hover:text-blue-600 transition-all opacity-0 group-hover/carousel:opacity-100">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button onclick="nextSlide()" class="absolute right-4 top-1/2 -translate-y-1/2 z-30 w-12 h-12 rounded-full bg-black/40 backdrop-blur-sm text-white hover:bg-white hover:text-blue-600 transition-all opacity-0 group-hover/carousel:opacity-100">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            <div class="flex justify-center gap-2 mt-6">
                <div class="carousel-dot w-8 h-2 rounded-full bg-blue-600 transition-all duration-300"></div>
                <div class="carousel-dot w-2 h-2 rounded-full bg-slate-300 dark:bg-slate-700 transition-all duration-300"></div>
                <div class="carousel-dot w-2 h-2 rounded-full bg-slate-300 dark:bg-slate-700 transition-all duration-300"></div>
            </div>
        </div>
    </div>

    <div id="privacyModal" class="fixed inset-0 z-[60] hidden opacity-0 transition-opacity duration-300">
        <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-md" onclick="togglePrivacyModal()"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[90%] max-w-2xl glass-card rounded-[2.5rem] p-6 md:p-8 shadow-2xl overflow-hidden max-h-[85vh] flex flex-col">
            <button onclick="togglePrivacyModal()" class="absolute top-6 right-6 text-slate-400 hover:text-red-500 transition-colors z-30">
                <i class="fas fa-times text-2xl"></i>
            </button>
            <div class="mb-6 text-left shrink-0">
                <h2 class="text-2xl font-black text-slate-900 dark:text-white mb-2 italic">Kebijakan Privasi SI-MUTU</h2>
                <div class="h-1.5 w-16 bg-blue-600 rounded-full"></div>
            </div>
            <div class="overflow-y-auto pr-2 text-left text-sm text-slate-600 dark:text-slate-300 space-y-4 leading-relaxed">
                <p>Direktorat Keteknikan dan Kesiapsiagaan Nuklir (DKKN) BAPETEN berkomitmen penuh untuk melindungi data dan informasi digital milik seluruh pengguna aplikasi SI-MUTU DKKN.</p>
                
                <div class="space-y-1">
                    <h4 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="fas fa-database text-blue-500 text-xs"></i> 1. Pengumpulan Data
                    </h4>
                    <p class="pl-5 text-slate-500 dark:text-slate-400 text-xs">Sistem mengumpulkan informasi kelembagaan, berkas legalitas, sertifikasi personel, data teknis Sinar-X, serta data Laporan Hasil Uji (LHU) yang diunggah secara sadar oleh pengguna terdaftar.</p>
                </div>

                <div class="space-y-1">
                    <h4 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="fas fa-shield-alt text-blue-500 text-xs"></i> 2. Keamanan & Perlindungan
                    </h4>
                    <p class="pl-5 text-slate-500 dark:text-slate-400 text-xs">Seluruh data yang tersimpan dalam sistem dilindungi menggunakan protokol enkripsi standar dan hanya digunakan untuk kepentingan pengawasan, validasi jaminan mutu, serta standar keselamatan radiasi nasional.</p>
                </div>

                <div class="space-y-1">
                    <h4 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="fas fa-handshake text-blue-500 text-xs"></i> 3. Pengungkapan Pihak Ketiga
                    </h4>
                    <p class="pl-5 text-slate-500 dark:text-slate-400 text-xs">BAPETEN tidak akan menjual, menyewakan, atau membocorkan data spesifik lembaga/personel kepada pihak luar tanpa persetujuan tertulis atau di luar koridor hukum pengawasan ketenaganukliran Indonesia.</p>
                </div>
                
                <blockquote class="border-l-4 border-blue-600 bg-blue-50/50 dark:bg-blue-950/30 p-3 rounded-r-xl text-xs text-slate-500 dark:text-slate-400">
                    Dengan menggunakan sistem informasi ini, Anda menyatakan setuju atas ketentuan pengolahan data demi mendukung integrasi keselamatan nuklir nasional.
                </blockquote>
            </div>
        </div>
    </div>

    <main class="relative z-10 min-h-screen flex flex-col items-center px-4 pt-12 pb-8">
        
        <header class="w-full max-w-4xl flex flex-col items-center mb-12 text-center animate-fade-in">
            <div class="flex flex-col items-center mb-10 group/logo">
                <div class="relative w-24 h-24 md:w-32 md:h-32 mb-6 drop-shadow-2xl animate-float cursor-help transition-all duration-700 group-hover/logo:scale-105">
                    <div class="absolute inset-0 bg-blue-500/20 rounded-full blur-2xl scale-75 group-hover/logo:scale-125 transition-transform duration-700"></div>
                    <img src="image/logo.svg" alt="Logo BAPETEN" class="relative z-10 w-full h-full object-contain">
                </div>
                <div class="space-y-1 text-center">
                    <h2 class="text-sm md:text-base font-bold tracking-[0.3em] text-slate-500 dark:text-slate-400 uppercase">Badan Pengawas Tenaga Nuklir</h2>
                    <h1 class="text-5xl md:text-7xl font-black tracking-tight text-slate-900 dark:text-white leading-tight">
                        SI-MUTU <span class="bg-gradient-to-r from-blue-600 via-indigo-500 to-blue-600 bg-clip-text text-transparent">DKKN</span>
                    </h1>
                </div>
            </div>

            <p class="max-w-2xl text-base md:text-xl text-slate-600 dark:text-slate-400 font-medium leading-relaxed mb-12">
                Sistem Informasi Jaminan Mutu Ketenaganukliran untuk standar keselamatan radiasi nasional.
            </p>

            <div class="w-full max-w-4xl glass-card rounded-3xl p-1 md:p-2 border border-white/50 dark:border-white/5 shadow-2xl animate-slide-up flex flex-col md:flex-row items-center divide-y md:divide-y-0 md:divide-x divide-slate-200 dark:divide-slate-700/50">
                
                <div class="w-full md:w-auto px-6 py-3 flex items-center gap-4 justify-center md:justify-start">
                    <div class="w-10 h-10 rounded-2xl bg-blue-600/10 text-blue-600 flex items-center justify-center">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="text-left">
                        <div id="realtime-clock" class="text-lg font-black tracking-tight tabular-nums text-slate-800 dark:text-slate-100">00:00:00</div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase">Waktu Indonesia</div>
                    </div>
                </div>

                <div class="w-full md:flex-1 px-6 py-3 flex items-center gap-4 justify-center md:justify-start">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-600/10 text-emerald-600 flex items-center justify-center">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="text-left">
                        <div class="text-lg font-black tracking-tight tabular-nums text-slate-800 dark:text-slate-100">
                            {{ $totalUsers ?? 0 }} <span class="text-xs font-normal text-slate-500 dark:text-slate-400">Pengguna</span>
                        </div>
                        <div class="text-[10px] font-bold text-emerald-500 uppercase flex items-center gap-1">
                            Akun Terdaftar <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>

                <button onclick="toggleModal()" class="w-full md:w-auto group/doc px-8 py-4 bg-transparent hover:bg-slate-50 dark:hover:bg-white/5 transition-colors flex items-center gap-4 justify-center rounded-b-3xl md:rounded-r-3xl md:rounded-bl-none">
                    <div class="text-right hidden md:block">
                        <div class="text-sm font-black text-blue-600 dark:text-blue-400 tracking-tight group-hover/doc:translate-x-[-4px] transition-transform">Dokumentasi KFJM</div>
                        <div class="text-[10px] font-bold text-slate-400 group-hover/doc:translate-x-[-4px] transition-transform uppercase">Lihat Galeri</div>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center shadow-lg shadow-blue-500/30 group-hover/doc:scale-110 transition-transform">
                        <i class="fas fa-camera-retro"></i>
                    </div>
                    <span class="md:hidden font-black text-blue-600">Dokumentasi KFJM</span>
                </button>
            </div>
        </header>

        <section class="w-full max-w-6xl grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8 mb-20 animate-slide-up" style="animation-delay: 200ms">
            
            <a href="{{ route('login.pelatihan') }}" class="group relative flex flex-col items-center p-8 rounded-[2.5rem] glass-card hover:shadow-2xl hover:shadow-blue-500/10 hover:-translate-y-2 transition-all duration-500 overflow-hidden text-center">
                <div class="w-20 h-20 rounded-3xl menu-gradient-1 flex items-center justify-center text-white text-3xl mb-6 shadow-lg shadow-blue-500/30 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <h3 class="text-xl font-bold mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors tracking-tight">Lembaga Pelatihan</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 font-medium leading-relaxed">Survailen & Pelaporan Kegiatan Pelatihan Ketenaganukliran</p>
                <div class="mt-8 flex items-center text-blue-600 font-bold text-xs gap-2 opacity-0 group-hover:opacity-100 transition-all translate-y-2 group-hover:translate-y-0 duration-300 uppercase tracking-widest">
                    Masuk Lembaga Pelatihan <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <a href="{{ route('login.uji') }}" class="group relative flex flex-col items-center p-8 rounded-[2.5rem] glass-card hover:shadow-2xl hover:shadow-emerald-500/10 hover:-translate-y-2 transition-all duration-500 overflow-hidden text-center">
                <div class="w-20 h-20 rounded-3xl menu-gradient-2 flex items-center justify-center text-white text-3xl mb-6 shadow-lg shadow-emerald-500/30 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500">
                    <i class="fas fa-flask"></i>
                </div>
                <h3 class="text-xl font-bold mb-2 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors tracking-tight">Lembaga Uji</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 font-medium leading-relaxed">Surveilan dan Pelaporan Kegiatan Lembaga Uji Ketenaganukliran</p>
                <div class="mt-8 flex items-center text-emerald-600 font-bold text-xs gap-2 opacity-0 group-hover:opacity-100 transition-all translate-y-2 group-hover:translate-y-0 duration-300 uppercase tracking-widest">
                    Masuk Lembaga Uji <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <a href="{{ route('sertifikasi.index') }}" class="group relative flex flex-col items-center p-8 rounded-[2.5rem] glass-card hover:shadow-2xl hover:shadow-purple-500/10 hover:-translate-y-2 transition-all duration-500 overflow-hidden text-center">
                <div class="w-20 h-20 rounded-3xl menu-gradient-3 flex items-center justify-center text-white text-3xl mb-6 shadow-lg shadow-purple-500/30 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500">
                    <i class="fas fa-award"></i>
                </div>
                <h3 class="text-xl font-bold mb-2 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors tracking-tight">Sertifikasi</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 font-medium leading-relaxed">Personel & Integrasi Database Jadwal</p>
                <div class="mt-8 flex items-center text-purple-600 font-bold text-xs gap-2 opacity-0 group-hover:opacity-100 transition-all translate-y-2 group-hover:translate-y-0 duration-300 uppercase tracking-widest">
                    Masuk Sertifikasi <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <a href="{{ route('login.sinarx') }}" class="group relative flex flex-col items-center p-8 rounded-[2.5rem] glass-card hover:shadow-2xl hover:shadow-orange-500/10 hover:-translate-y-2 transition-all duration-500 overflow-hidden text-center">
                <div class="w-20 h-20 rounded-3xl menu-gradient-4 flex items-center justify-center text-white text-3xl mb-6 shadow-lg shadow-orange-500/30 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500">
                    <i class="fas fa-microscope"></i>
                </div>
                <h3 class="text-xl font-bold mb-2 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors tracking-tight">Amandemen Sertifikat</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 font-medium leading-relaxed">Perubahan Data & Ketentuan Teknis Sinar-X</p>
                <div class="mt-8 flex items-center text-orange-600 font-bold text-xs gap-2 opacity-0 group-hover:opacity-100 transition-all translate-y-2 group-hover:translate-y-0 duration-300 uppercase tracking-widest">
                    Ajukan Amandemen <i class="fas fa-arrow-right"></i>
                </div>
            </a>

        </section>

        <section class="w-full max-w-6xl mb-20 animate-slide-up" style="animation-delay: 300ms">
            <div class="glass-card rounded-[2.5rem] p-6 md:p-8 shadow-xl border border-white/50 dark:border-white/5 overflow-hidden">
                
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 text-left">
                    <div class="flex items-center gap-3 flex-wrap">
                        <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight italic flex items-center gap-3">
                            <i class="fas fa-map-marked-alt text-blue-600 dark:text-blue-400"></i>
                            Peta Sebaran Lembaga LU & LP
                        </h2>
                        <a href="https://peta-lu-lp.netlify.app/" target="_blank" 
                           class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-blue-50 dark:bg-slate-800 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-slate-700 hover:scale-110 hover:bg-blue-600 hover:text-white dark:hover:bg-blue-600 dark:hover:text-white transition-all shadow-sm" 
                           title="Buka di Tab Baru">
                            <i class="fas fa-external-link-alt text-xs"></i>
                        </a>
                    </div>
                </div>

                <div class="w-full rounded-3xl overflow-hidden shadow-inner border border-slate-200 dark:border-slate-800 aspect-[4/3] sm:aspect-[16/10] md:h-[600px] bg-slate-100 dark:bg-slate-900">
                    <iframe 
                        src="https://peta-lu-lp.netlify.app/" 
                        title="Peta Sebaran SI-MUTU BAPETEN"
                        class="w-full h-full border-0"
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>

            </div>
        </section>
        <footer class="w-full max-w-4xl flex flex-col items-center text-center space-y-8 animate-fade-in delay-500">
            <div class="text-xs md:text-sm text-slate-500 dark:text-slate-400 font-medium space-y-4">
                <div class="flex flex-wrap justify-center items-center gap-4 text-[11px] md:text-xs">
                    
                    <button type="button" onclick="togglePrivacyModal()" class="px-4 py-2 bg-slate-500/5 hover:bg-blue-600/10 text-slate-400 hover:text-blue-400 rounded-xl border border-slate-700/40 hover:border-blue-500/30 transition-all font-bold flex items-center gap-1.5 uppercase tracking-tighter">
                        <i class="fas fa-shield-alt text-[10px]"></i> Kebijakan Privasi
                    </button>
                    
                    <a href="https://wa.me/6281288463770?text=Halo%2C%20saya%20ingin%20menanyakan%20mengenai%20sistem%20informasi%20jaminan%20mutu" target="_blank" class="px-4 py-2 bg-slate-500/5 hover:bg-emerald-600/10 text-slate-400 hover:text-emerald-400 rounded-xl border border-slate-700/40 hover:border-emerald-500/30 transition-all font-bold flex items-center gap-1.5 uppercase tracking-tighter">
                        <i class="fab fa-whatsapp text-[12px]"></i> Bantuan Layanan
                    </a>
                    
                    <a href="{{ asset('manual-book.pdf') }}" target="_blank" class="px-4 py-2 bg-slate-500/5 hover:bg-amber-500/10 text-slate-400 hover:text-[#c9a050] rounded-xl border border-slate-700/40 hover:border-amber-500/40 transition-all font-bold flex items-center gap-1.5 uppercase tracking-tighter hover:shadow-[0_0_15px_rgba(201,160,80,0.3)]">
                        <i class="fas fa-book-open text-[10px]"></i> Buku Panduan
                    </a>
                                                    
                    <a href="{{ route('login.internal') }}" class="px-4 py-2 bg-slate-500/5 hover:bg-blue-600/15 text-slate-400 hover:text-blue-400 rounded-xl border border-slate-700/40 hover:border-blue-500/50 transition-all font-bold flex items-center gap-1.5 uppercase tracking-tighter hover:shadow-[0_0_15px_rgba(37,99,235,0.4)]">
                        <i class="fas fa-user-lock text-[10px]"></i> Login Internal
                    </a>
                </div>
                <p>&copy; 2026 <strong>BAPETEN</strong> - Direktorat Keteknikan dan Kesiapsiagaan Nuklir - Kelompok Fungsi Jaminan Mutu</p>
            </div>
        </footer>
    </main>

    <script>
        window.addEventListener('load', () => {
            document.body.classList.add('is-ready');
        });

        function updateClock() {
            const now = new Date();
            const clockEl = document.getElementById('realtime-clock');
            if(clockEl) clockEl.textContent = now.toLocaleTimeString('id-ID', { hour12: false });
        }
        setInterval(updateClock, 1000);
        updateClock();

        function toggleTheme() {
            const html = document.documentElement;
            const icon = document.getElementById('theme-icon');
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                if(icon) icon.classList.replace('fa-sun', 'fa-moon');
                localStorage.setItem('theme', 'light');
            } else {
                html.classList.add('dark');
                if(icon) icon.classList.replace('fa-moon', 'fa-sun');
                localStorage.setItem('theme', 'dark');
            }
        }
        
        function toggleModal() {
            const modal = document.getElementById('docModal');
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
                setTimeout(() => modal.classList.add('opacity-100'), 10);
                startCarousel();
            } else {
                modal.classList.remove('opacity-100');
                setTimeout(() => modal.classList.add('hidden'), 300);
                stopCarousel();
            }
        }

        function togglePrivacyModal() {
            const modal = document.getElementById('privacyModal');
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
                setTimeout(() => modal.classList.add('opacity-100'), 10);
            } else {
                modal.classList.remove('opacity-100');
                setTimeout(() => modal.classList.add('hidden'), 300);
            }
        }

        let currentSlide = 0;
        let slideInterval;
        const slides = document.querySelectorAll('.carousel-item');
        const dots = document.querySelectorAll('.carousel-dot');

        function showSlide(index) {
            if(!slides.length) return;
            slides.forEach(s => s.classList.remove('active'));
            dots.forEach(d => {
                d.classList.remove('bg-blue-600', 'w-8');
                d.classList.add('bg-slate-300', 'dark:bg-slate-700', 'w-2');
            });
            currentSlide = (index + slides.length) % slides.length;
            slides[currentSlide].classList.add('active');
            dots[currentSlide].classList.remove('bg-slate-300', 'dark:bg-slate-700', 'w-2');
            dots[currentSlide].classList.add('bg-blue-600', 'w-8');
        }

        function nextSlide() { showSlide(currentSlide + 1); }
        function prevSlide() { showSlide(currentSlide - 1); }
        function startCarousel() { stopCarousel(); slideInterval = setInterval(nextSlide, 5000); }
        function stopCarousel() { clearInterval(slideInterval); }

        (function init() {
            const icon = document.getElementById('theme-icon');
            if(icon && document.documentElement.classList.contains('dark')) {
                icon.classList.replace('fa-moon', 'fa-sun');
            }
        })();
    </script>
</body>
</html>