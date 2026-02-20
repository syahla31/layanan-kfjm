<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>SI-MUTU DKKN | Portal Layanan</title>
    
    <!-- 1. Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- 2. Load Tailwind Config (Inline) -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'spin-slow': 'spin 12s linear infinite',
                        'fade-in-up': 'fadeInUp 0.8s ease-out forwards',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
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

    <!-- 3. Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        /* Custom scrollbar untuk mobile feel yang lebih bersih */
        ::-webkit-scrollbar { width: 0px; background: transparent; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 font-sans text-gray-800 dark:text-gray-100 min-h-[100dvh] flex flex-col">

    <!-- THEME TOGGLE BUTTON -->
    <button onclick="toggleTheme()" class="fixed top-4 right-4 z-50 p-2.5 rounded-full bg-white/80 dark:bg-slate-800/80 backdrop-blur shadow-lg border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-yellow-400 hover:scale-110 active:scale-95 transition-all focus:outline-none">
        <i id="theme-icon" class="fas fa-moon text-lg w-5 h-5 flex items-center justify-center"></i>
    </button>

    <!-- BAGIAN 1: PORTAL HALAMAN DEPAN -->
    <!-- Changed min-h-screen to min-h-[100dvh] for mobile browsers -->
    <div id="landing-portal" class="flex-grow flex flex-col justify-between bg-gradient-to-br from-blue-50 via-white to-indigo-50 dark:from-slate-950 dark:via-slate-900 dark:to-blue-950 text-slate-800 dark:text-slate-100 relative overflow-hidden">
        
        <!-- Background Pattern (Adjusted size for mobile) -->
        <div class="absolute top-0 left-0 w-full h-full pointer-events-none overflow-hidden">
            <div class="absolute top-[-10%] left-[-5%] w-[300px] md:w-[500px] h-[300px] md:h-[500px] bg-blue-200 dark:bg-blue-900 rounded-full mix-blend-multiply dark:mix-blend-color-dodge filter blur-3xl opacity-30 animate-float"></div>
            <div class="absolute bottom-[-10%] right-[-5%] w-[300px] md:w-[600px] h-[300px] md:h-[600px] bg-purple-200 dark:bg-indigo-900 rounded-full mix-blend-multiply dark:mix-blend-color-dodge filter blur-3xl opacity-30 animate-float" style="animation-delay: 2s"></div>
            
            <!-- Floating Icons - Responsive Sizes -->
            <i class="fas fa-atom absolute top-10 left-10 text-6xl md:text-8xl text-blue-300 dark:text-slate-600 opacity-20 dark:opacity-20 animate-spin-slow"></i>
            <i class="fas fa-microscope absolute bottom-24 left-1/4 text-4xl md:text-6xl text-indigo-300 dark:text-slate-600 opacity-20 dark:opacity-20 animate-float"></i>
            <i class="fas fa-file-contract absolute top-1/3 right-10 text-5xl md:text-7xl text-teal-300 dark:text-slate-600 opacity-20 dark:opacity-20 animate-float" style="animation-delay: 1s"></i>
        </div>

        <!-- Wrapper Konten Utama -->
        <div class="z-10 flex-grow flex flex-col items-center justify-center px-4 w-full max-w-7xl mx-auto py-8 md:py-10">
            
            <!-- Header Section -->
            <div class="mb-8 md:mb-16 opacity-0 animate-fade-in-up text-center w-full">
                <div class="inline-flex items-center justify-center p-2 md:p-3 rounded-2xl bg-white dark:bg-slate-800 shadow-xl shadow-blue-100/50 dark:shadow-none mb-4 md:mb-6 border border-blue-50 dark:border-slate-700">
                     <div class="bg-gradient-to-br from-blue-600 to-indigo-700 w-12 h-12 md:w-16 md:h-16 rounded-xl flex items-center justify-center text-white shadow-inner">
                        <i class="fas fa-shield-alt text-2xl md:text-3xl"></i>
                     </div>
                </div>
                <!-- Responsive Text Sizes -->
                <h1 class="text-3xl md:text-6xl font-extrabold tracking-tight mb-2 md:mb-3 text-slate-900 dark:text-white drop-shadow-sm">
                    SI-MUTU <span class="text-blue-600 dark:text-blue-400">DKKN</span>
                </h1>
                <p class="text-slate-500 dark:text-slate-400 text-sm md:text-2xl font-medium tracking-wide px-4">
                    Sistem Informasi Jaminan Mutu Ketenaganukliran
                </p>
                
                <div class="flex justify-center mt-6 md:mt-8">
                    <span class="px-4 py-1.5 md:px-5 md:py-2 rounded-full bg-blue-50 dark:bg-slate-800 text-blue-700 dark:text-blue-300 text-xs md:text-base font-bold border border-blue-100 dark:border-slate-700 flex items-center gap-2 shadow-sm">
                        <span class="relative flex h-2.5 w-2.5 md:h-3 md:w-3">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2.5 w-2.5 md:h-3 md:w-3 bg-blue-600 dark:bg-blue-400"></span>
                        </span>
                        Silakan Pilih Layanan
                    </span>
                </div>
            </div>

            <!-- GRID MENU - Optimized for Mobile -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-16 justify-items-center relative z-20 w-full max-w-5xl px-2 md:px-0">
                
                <!-- Menu 1 -->
                <a href="{{ route('login.pelatihan') }}" class="menu-card group flex flex-col items-center w-full opacity-0 animate-fade-in-up delay-100 active:scale-95 transition-transform duration-200">
                    <!-- Ukuran lingkaran disesuaikan: w-24 (mobile) w-48 (desktop) -->
                    <div class="w-24 h-24 sm:w-32 sm:h-32 md:w-48 md:h-48 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 shadow-lg md:shadow-xl shadow-blue-200 dark:shadow-none flex items-center justify-center border-[3px] md:border-4 border-white dark:border-slate-800 ring-2 md:ring-4 ring-blue-50 dark:ring-slate-700 group-hover:ring-blue-200 dark:group-hover:ring-blue-900 group-hover:scale-105 transition-all duration-300 relative overflow-hidden">
                        <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity"></div>
                        <i class="fas fa-chalkboard-teacher text-3xl sm:text-4xl md:text-6xl text-white drop-shadow-md group-hover:rotate-3 transition-transform"></i>
                    </div>
                    <div class="mt-3 md:mt-6 text-center w-full">
                        <h3 class="text-sm md:text-xl font-bold text-slate-800 dark:text-slate-100 group-hover:text-blue-700 dark:group-hover:text-blue-400 transition-colors leading-tight">Lembaga Pelatihan</h3>
                        <p class="text-[10px] md:text-sm text-slate-500 dark:text-slate-400 mt-1 md:mt-2 font-medium leading-tight">Survailen & Laporan</p>
                    </div>
                </a>

                <!-- Menu 2 -->
                <a href="{{ route('login.uji') }}" class="menu-card group flex flex-col items-center w-full opacity-0 animate-fade-in-up delay-200 active:scale-95 transition-transform duration-200">
                    <div class="w-24 h-24 sm:w-32 sm:h-32 md:w-48 md:h-48 rounded-full bg-gradient-to-br from-teal-500 to-teal-700 shadow-lg md:shadow-xl shadow-teal-200 dark:shadow-none flex items-center justify-center border-[3px] md:border-4 border-white dark:border-slate-800 ring-2 md:ring-4 ring-teal-50 dark:ring-slate-700 group-hover:ring-teal-200 dark:group-hover:ring-teal-900 group-hover:scale-105 transition-all duration-300 relative overflow-hidden">
                        <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity"></div>
                        <i class="fas fa-flask text-3xl sm:text-4xl md:text-6xl text-white drop-shadow-md group-hover:rotate-3 transition-transform"></i>
                    </div>
                    <div class="mt-3 md:mt-6 text-center w-full">
                        <h3 class="text-sm md:text-xl font-bold text-slate-800 dark:text-slate-100 group-hover:text-teal-700 dark:group-hover:text-teal-400 transition-colors leading-tight">Lembaga Uji</h3>
                        <p class="text-[10px] md:text-sm text-slate-500 dark:text-slate-400 mt-1 md:mt-2 font-medium leading-tight">LUK & Dosimetri</p>
                    </div>
                </a>

                <!-- Menu 3 -->
                <a href="{{ route('sertifikasi.index') }}" class="menu-card group flex flex-col items-center w-full opacity-0 animate-fade-in-up delay-300 active:scale-95 transition-transform duration-200">
                    <div class="w-24 h-24 sm:w-32 sm:h-32 md:w-48 md:h-48 rounded-full bg-gradient-to-br from-purple-600 to-purple-800 shadow-lg md:shadow-xl shadow-purple-200 dark:shadow-none flex items-center justify-center border-[3px] md:border-4 border-white dark:border-slate-800 ring-2 md:ring-4 ring-purple-50 dark:ring-slate-700 group-hover:ring-purple-200 dark:group-hover:ring-purple-900 group-hover:scale-105 transition-all duration-300 relative overflow-hidden">
                        <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity"></div>
                        <i class="fas fa-certificate text-3xl sm:text-4xl md:text-6xl text-white drop-shadow-md group-hover:rotate-3 transition-transform"></i>
                    </div>
                    <div class="mt-3 md:mt-6 text-center w-full">
                        <h3 class="text-sm md:text-xl font-bold text-slate-800 dark:text-slate-100 group-hover:text-purple-700 dark:group-hover:text-purple-400 transition-colors leading-tight">Sertifikasi</h3>
                        <p class="text-[10px] md:text-sm text-slate-500 dark:text-slate-400 mt-1 md:mt-2 font-medium leading-tight">Jadwal & Integrasi</p>
                    </div>
                </a>

                <!-- Menu 4 -->
                <a href="{{ route('login.sinarx') }}" class="menu-card group flex flex-col items-center w-full opacity-0 animate-fade-in-up delay-400 active:scale-95 transition-transform duration-200">
                    <div class="w-24 h-24 sm:w-32 sm:h-32 md:w-48 md:h-48 rounded-full bg-gradient-to-br from-orange-500 to-orange-700 shadow-lg md:shadow-xl shadow-orange-200 dark:shadow-none flex items-center justify-center border-[3px] md:border-4 border-white dark:border-slate-800 ring-2 md:ring-4 ring-orange-50 dark:ring-slate-700 group-hover:ring-orange-200 dark:group-hover:ring-orange-900 group-hover:scale-105 transition-all duration-300 relative overflow-hidden">
                        <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity"></div>
                        <i class="fas fa-radiation text-3xl sm:text-4xl md:text-6xl text-white drop-shadow-md group-hover:rotate-3 transition-transform"></i>
                    </div>
                    <div class="mt-3 md:mt-6 text-center w-full">
                        <h3 class="text-sm md:text-xl font-bold text-slate-800 dark:text-slate-100 group-hover:text-orange-700 dark:group-hover:text-orange-400 transition-colors leading-tight">Uji Kesesuaian</h3>
                        <p class="text-[10px] md:text-sm text-slate-500 dark:text-slate-400 mt-1 md:mt-2 font-medium leading-tight">Amandemen KT</p>
                    </div>
                </a>

            </div>
        </div>

        <!-- Footer Section -->
        <div class="z-10 w-full text-center py-4 md:py-6 text-[10px] md:text-sm text-slate-400 dark:text-slate-500 border-t border-slate-200/50 dark:border-slate-800 bg-white/50 dark:bg-slate-900/50 backdrop-blur-sm">
            &copy; 2026 Direktorat Keteknikan dan Kesiapsiagaan Nuklir - BAPETEN
            <br>
            <a href="{{ route('login.internal') }}" class="text-[10px] md:text-xs text-slate-300 hover:text-slate-500 mt-1 md:mt-2 inline-block p-2">Login Internal</a>
        </div>
    </div>

    <!-- Script Logic -->
    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const icon = document.getElementById('theme-icon');
            
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
                localStorage.setItem('theme', 'light');
            } else {
                html.classList.add('dark');
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
                localStorage.setItem('theme', 'dark');
            }
        }
        
        // Init Theme
        (function initTheme() {
            const savedTheme = localStorage.getItem('theme');
            const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (savedTheme === 'dark' || (!savedTheme && systemDark)) {
                document.documentElement.classList.add('dark');
                const icon = document.getElementById('theme-icon');
                if(icon) {
                    icon.classList.remove('fa-moon');
                    icon.classList.add('fa-sun');
                }
            }
        })();
    </script>
</body>
</html>