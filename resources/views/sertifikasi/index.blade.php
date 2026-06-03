<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan Sertifikasi | SI-MUTU DKKN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        primary: '#7c3aed', 
                        secondary: '#fbbf24',
                    },
                    animation: {
                        'blob': 'blob 7s infinite',
                        'fade-in-up': 'fadeInUp 0.8s ease-out forwards',
                    },
                    keyframes: {
                        blob: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                            '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                            '100%': { transform: 'translate(0px, 0px) scale(1)' },
                        },
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .glass-nav {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .dark .glass-nav {
            background: rgba(15, 23, 42, 0.7);
        }
        .card-hover:hover {
            transform: translateY(-8px) scale(1.01);
            box-shadow: 0 25px 50px -12px rgba(124, 58, 237, 0.15);
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 font-sans text-slate-800 dark:text-slate-100 min-h-screen flex flex-col transition-colors duration-500 overflow-x-hidden">

    <!-- THEME TOGGLE BUTTON -->
    <button onclick="toggleTheme()" class="fixed top-4 right-4 z-[70] p-3 rounded-2xl bg-white/80 dark:bg-slate-800/80 backdrop-blur-md shadow-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-yellow-400 hover:rotate-12 transition-all active:scale-90 focus:outline-none" aria-label="Toggle Theme">
        <i id="theme-icon" class="fas fa-moon text-xl w-6 h-6 flex items-center justify-center"></i>
    </button>

    <!-- Navbar Interaktif -->
    <nav class="glass-nav border-b border-slate-200 dark:border-slate-800 py-4 md:py-6 px-4 md:px-8 fixed w-full z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3 md:gap-6">
                <!-- Tombol Kembali -->
                <a href="{{ route('portal') }}" class="text-xs md:text-sm font-bold text-slate-500 hover:text-purple-600 dark:text-slate-400 dark:hover:text-purple-400 transition-all flex items-center gap-2 group bg-slate-100 dark:bg-slate-800 p-2 md:p-2.5 rounded-xl">
                    <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i> <span class="hidden xs:block">Kembali</span>
                </a>

                <div class="h-6 w-px bg-slate-200 dark:bg-slate-700"></div>

                <!-- Logo -->
                <div class="flex items-center gap-2 md:gap-3">
                    <div class="w-9 h-9 md:w-10 md:h-10 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-purple-200 dark:shadow-none animate-pulse">
                        <i class="fas fa-certificate text-lg"></i>
                    </div>
                    <div class="flex flex-col">
                        <h1 class="font-extrabold text-sm md:text-lg tracking-tight text-slate-900 dark:text-white leading-none">Sertifikasi <span class="text-purple-600">DKKN</span></h1>
                        <span class="text-[9px] md:text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">SI-MUTU BAPETEN</span>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Konten Utama -->
    <main class="flex-grow flex items-center justify-center pt-28 pb-16 px-4 relative">
        
        <!-- Background Hiasan Interaktif -->
        <div class="absolute top-0 left-0 w-full h-full pointer-events-none overflow-hidden opacity-40">
            <div class="absolute top-[10%] right-[10%] w-72 h-72 md:w-[500px] md:h-[500px] bg-purple-300 dark:bg-purple-900/30 rounded-full mix-blend-multiply filter blur-[80px] md:blur-[120px] animate-blob"></div>
            <div class="absolute bottom-[10%] left-[10%] w-72 h-72 md:w-[500px] md:h-[500px] bg-blue-300 dark:bg-blue-900/30 rounded-full mix-blend-multiply filter blur-[80px] md:blur-[120px] animate-blob animation-delay-2000"></div>
        </div>

        <div class="max-w-5xl w-full z-10">
            <div class="text-center mb-10 md:mb-16 animate-fade-in-up">
                <span class="inline-block px-4 py-1.5 mb-4 text-[10px] md:text-xs font-black uppercase tracking-[0.2em] text-purple-600 bg-purple-100 dark:bg-purple-900/40 dark:text-purple-400 rounded-full">
                    Layanan Publik
                </span>
                <h2 class="text-3xl md:text-5xl font-black text-slate-900 dark:text-white mb-6 tracking-tight">Layanan Lisensi Petugas</h2>
                <p class="text-sm md:text-lg text-slate-500 dark:text-slate-400 max-w-2xl mx-auto leading-relaxed px-4">
                    Portal terintegrasi untuk informasi jadwal ujian dan registrasi akun petugas proteksi radiasi melalui sistem <span class="text-purple-600 font-bold">BALIS</span>.
                </p>
            </div>

            <!-- Grid Kartu Pilihan -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8 px-2 md:px-0">
                
                <!-- KARTU 1: LIHAT JADWAL -->
                <a href="https://balis-pekerja.bapeten.go.id/frontend/web/site/faq" target="_blank" class="group card-hover relative bg-white dark:bg-slate-800/50 backdrop-blur-sm rounded-[2rem] p-8 md:p-10 shadow-xl border border-slate-100 dark:border-slate-700/50 transition-all duration-500 overflow-hidden animate-fade-in-up">
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-purple-50 dark:bg-purple-900/20 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                    
                    <div class="relative z-10">
                        <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-indigo-600 text-white rounded-2xl flex items-center justify-center text-3xl mb-8 shadow-lg shadow-purple-200 dark:shadow-none group-hover:rotate-6 transition-transform">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h3 class="text-2xl md:text-3xl font-extrabold text-slate-900 dark:text-white mb-4">Jadwal Uji Kompetensi</h3>
                        <p class="text-sm md:text-base text-slate-500 dark:text-slate-400 mb-10 leading-relaxed">
                            Cek jadwal pelaksanaan ujian sertifikasi terkini, lokasi ujian, dan kuota yang tersedia di seluruh Indonesia.
                        </p>
                        <div class="flex items-center text-purple-600 dark:text-purple-400 font-extrabold text-sm md:text-base uppercase tracking-wider group-hover:gap-4 transition-all gap-2">
                            Lihat Jadwal <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                </a>

                <!-- KARTU 2: REGISTRASI BARU -->
                <a href="https://balis.bapeten.go.id/frontend2/public/index.php/new-registrasi" target="_blank" class="group card-hover relative bg-white dark:bg-slate-800/50 backdrop-blur-sm rounded-[2rem] p-8 md:p-10 shadow-xl border border-slate-100 dark:border-slate-700/50 transition-all duration-500 overflow-hidden animate-fade-in-up" style="animation-delay: 150ms;">
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-blue-50 dark:bg-blue-900/20 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                    
                    <div class="relative z-10">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-cyan-600 text-white rounded-2xl flex items-center justify-center text-3xl mb-8 shadow-lg shadow-blue-200 dark:shadow-none group-hover:-rotate-6 transition-transform">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <h3 class="text-2xl md:text-3xl font-extrabold text-slate-900 dark:text-white mb-4">Registrasi Akun Baru</h3>
                        <p class="text-sm md:text-base text-slate-500 dark:text-slate-400 mb-10 leading-relaxed">
                            Belum memiliki akun BALIS? Lakukan pendaftaran akun baru untuk Ujian Sertifikasi Penguji Berkualifikasi (PB) dan Sertifikasi Tenaga Ahli (TA).
                        </p>
                        <div class="flex items-center text-blue-600 dark:text-blue-400 font-extrabold text-sm md:text-base uppercase tracking-wider group-hover:gap-4 transition-all gap-2">
                            Daftar Sekarang <i class="fas fa-external-link-alt"></i>
                        </div>
                    </div>
                </a>

            </div>

            <!-- Tip Info -->
            <div class="mt-12 md:mt-16 text-center animate-fade-in-up" style="animation-delay: 300ms;">
                <div class="inline-flex flex-col md:flex-row items-center gap-3 px-6 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-[1.5rem] md:rounded-full text-xs md:text-sm shadow-sm">
                    <div class="flex items-center gap-2 text-amber-500 font-bold">
                        <i class="fas fa-info-circle"></i>
                        <span>Pemberitahuan:</span>
                    </div>
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Anda akan diarahkan ke portal eksternal BALIS BAPETEN.</span>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 py-8 text-center px-6">
        <p class="text-[10px] md:text-xs text-slate-400 dark:text-slate-500 font-bold uppercase tracking-[0.2em] leading-loose">
            &copy; 2026 Direktorat Keteknikan dan Kesiapsiagaan Nuklir <br class="md:hidden"> <span class="hidden md:inline mx-2">•</span> BAPETEN Indonesia
        </p>
    </footer>

    <script>
        // Smooth transitions for interaction
        document.querySelectorAll('a[target="_blank"]').forEach(el => {
            el.addEventListener('click', (e) => {
                // Bisa ditambahkan tracker atau loading state jika perlu
            });
        });

        // Theme Switcher Logic
        function toggleTheme() {
            const html = document.documentElement;
            const icon = document.getElementById('theme-icon');
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                icon.classList.replace('fa-sun', 'fa-moon');
                localStorage.setItem('theme', 'light');
            } else {
                html.classList.add('dark');
                icon.classList.replace('fa-moon', 'fa-sun');
                localStorage.setItem('theme', 'dark');
            }
        }
        
        // Initial Theme Check
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            const icon = document.getElementById('theme-icon');
            if(icon) icon.classList.replace('fa-moon', 'fa-sun');
        }
    </script>
</body>
</html>