<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login {{ $title }} | SI-MUTU</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    animation: { 
                        'fade-in': 'fadeIn 0.5s ease-out forwards',
                        'pop-in': 'popIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards' 
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        popIn: {
                            '0%': { opacity: '0', transform: 'scale(0.8) translateY(20px)' },
                            '100%': { opacity: '1', transform: 'scale(1) translateY(0)' },
                        }
                    }
                }
            },
            safelist: [
                'bg-blue-600', 'bg-blue-700', 'bg-blue-100', 'text-blue-600', 'from-blue-600', 'to-blue-800', 'focus:ring-blue-500', 'bg-blue-400', 'border-blue-500', 'text-blue-200',
                'bg-teal-600', 'bg-teal-700', 'bg-teal-100', 'text-teal-600', 'from-teal-600', 'to-teal-800', 'focus:ring-teal-500', 'bg-teal-400', 'border-teal-500', 'text-teal-200',
                'bg-orange-600', 'bg-orange-700', 'bg-orange-100', 'text-orange-600', 'from-orange-600', 'to-orange-800', 'focus:ring-orange-500', 'bg-orange-400', 'border-orange-500', 'text-orange-200',
                'bg-red-600', 'bg-red-700', 'bg-red-100', 'text-red-600', 'from-red-600', 'to-red-800', 'focus:ring-red-500', 'bg-red-400', 'border-red-500', 'text-red-200',
            ]
        }
    </script>
    <style>
        body { opacity: 0; transition: opacity 0.5s ease; }
        body.is-ready { opacity: 1; }
        .swiper-pagination-bullet { background: white !important; opacity: 0.5; }
        .swiper-pagination-bullet-active { opacity: 1; width: 20px; border-radius: 5px; }
        .glass-modal {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .dark .glass-modal {
            background: rgba(15, 23, 42, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 font-sans text-slate-800 dark:text-slate-100 min-h-[100dvh] flex items-center justify-center relative overflow-x-hidden py-6 px-4 sm:px-6 lg:px-8">

    <!-- Theme Toggle -->
    <button onclick="toggleTheme()" class="absolute top-4 right-4 z-50 p-2.5 rounded-full bg-white/90 dark:bg-slate-800/90 backdrop-blur shadow-lg border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-yellow-400 hover:scale-110 active:scale-95 transition-all focus:outline-none">
        <i id="theme-icon" class="fas fa-moon text-lg"></i>
    </button>

    <!-- Background Decoration -->
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
        <div class="absolute top-[-50px] left-[-50px] w-48 h-48 md:w-[600px] md:h-[600px] rounded-full mix-blend-multiply dark:mix-blend-color-dodge filter blur-3xl opacity-20 bg-{{ $color_theme }}-400 animate-pulse"></div>
        <div class="absolute bottom-[-50px] right-[-50px] w-48 h-48 md:w-[600px] md:h-[600px] rounded-full mix-blend-multiply dark:mix-blend-color-dodge filter blur-3xl opacity-20 bg-{{ $color_theme }}-400 animate-pulse" style="animation-delay: 2s;"></div>
    </div>

    <!-- Error Modal (Modernized) -->
    @if ($errors->any())
        <div id="errorModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/40 backdrop-blur-sm p-4 animate-fade-in">
            <div class="glass-modal rounded-[2.5rem] w-full max-w-sm overflow-hidden animate-pop-in shadow-2xl">
                <div class="p-10 text-center">
                    <div class="w-20 h-20 bg-red-500/10 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-shield-virus text-4xl text-red-600 animate-pulse"></i>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-2 tracking-tight">AKSES DITOLAK</h3>
                    <div class="text-sm font-medium text-slate-500 dark:text-slate-400 leading-relaxed">
                        @foreach ($errors->all() as $error) 
                            <p>{{ $error }}</p> 
                        @endforeach
                    </div>
                </div>
                <div class="p-6 pt-0">
                    <button type="button" onclick="closeErrorModal()" class="w-full py-4 bg-red-600 hover:bg-red-700 text-white rounded-2xl font-bold shadow-xl shadow-red-600/20 transition-all active:scale-95">
                        Saya Mengerti
                    </button>
                </div>
            </div>
        </div>
    @endif

    <div class="w-full max-w-[1000px] bg-white dark:bg-slate-800 rounded-3xl shadow-2xl overflow-hidden flex flex-col md:flex-row z-10 animate-fade-in border border-slate-100 dark:border-slate-700 relative">
        
        <!-- Left Side: Branding -->
        <div class="w-full md:w-5/12 p-8 md:p-10 flex flex-col justify-between text-white relative overflow-hidden bg-gradient-to-br from-{{ $color_theme }}-600 to-{{ $color_theme }}-800">
            <div class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none">
                <i class="fas fa-atom absolute -top-10 -left-10 text-[12rem] transform rotate-12"></i>
            </div>

            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-lg p-2">
                        <img src="{{ asset('image/logo.svg') }}" alt="Logo BAPETEN" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <h1 class="font-black tracking-tighter text-xl leading-none uppercase">BAPETEN</h1>
                        <p class="text-[10px] uppercase tracking-widest opacity-80 mt-1 font-bold">DKKN SI-MUTU</p>
                    </div>
                </div>
                
                <h2 class="text-2xl md:text-3xl font-extrabold mb-2 leading-tight drop-shadow-md">{{ $title }}</h2>
                <p class="text-white/80 text-sm italic mb-8">"{{ $desc }}"</p>

                <div class="swiper mySwiper w-full h-52 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 overflow-hidden shadow-inner px-4 py-6">
                    <div class="swiper-wrapper">
                        <!-- Swiper Content Logic -->
                        @if($type == 'uji')
                            <div class="swiper-slide flex flex-col items-center text-center">
                                <i class="fas fa-microscope text-4xl mb-3 text-blue-200"></i>
                                <h4 class="font-bold text-sm mb-1 uppercase">Uji Kesesuaian</h4>
                                <p class="text-[11px] opacity-90 px-4">Memastikan pesawat sinar-X memenuhi standar keselamatan radiasi medik.</p>
                            </div>
                        @elseif($type == 'pelatihan')
                            <div class="swiper-slide flex flex-col items-center text-center">
                                <i class="fas fa-chalkboard-user text-4xl mb-3 text-teal-200"></i>
                                <h4 class="font-bold text-sm mb-1 uppercase">PPR, Keahlian & PKZR</h4>
                                <p class="text-[11px] opacity-90 px-4">Penyelenggaraan kursus dan pelatihan standar untuk proteksi radiasi serta pemenuhan kompetensi petugas.</p>
                            </div>
                        @endif
                        <div class="swiper-slide flex flex-col items-center text-center">
                            <i class="fas fa-search-location text-4xl mb-3 text-white/80"></i>
                            <h4 class="font-bold text-sm mb-1 uppercase">Kegiatan Survailen</h4>
                            <p class="text-[11px] opacity-90 px-4">Pemantauan kepatuhan standar melalui survailen luring maupun daring.</p>
                        </div>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
            <div class="relative z-10 mt-6 pt-6 border-t border-white/10">
                <div class="flex items-center gap-3 text-[11px] opacity-70">
                    <i class="fas fa-info-circle"></i>
                    <span>Sistem Informasi Penjaminan Mutu DKKN BAPETEN.</span>
                </div>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="w-full md:w-7/12 p-8 md:p-12 bg-white dark:bg-slate-800 flex flex-col justify-center">
            <div class="text-center mb-8">
                <div class="w-16 h-16 rounded-2xl mx-auto flex items-center justify-center mb-4 bg-{{ $color_theme }}-50 dark:bg-slate-700 text-{{ $color_theme }}-600 dark:text-{{ $color_theme }}-400 shadow-inner">
                    <i class="fas {{ $icon }} text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-slate-800 dark:text-white">Selamat Datang</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Silakan masuk ke akun Anda</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="login_type" value="{{ $type }}">
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Email Instansi</label>
                    <div class="relative group mt-1.5">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 group-focus-within:text-{{ $color_theme }}-500">
                            <i class="far fa-envelope"></i>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}" class="w-full pl-11 pr-4 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 focus:ring-2 focus:ring-{{ $color_theme }}-500 outline-none transition-all" placeholder="nama@instansi.co.id" required autofocus>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Kata Sandi</label>
                    <div class="relative group mt-1.5">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 group-focus-within:text-{{ $color_theme }}-500">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password" id="passwordInput" class="w-full pl-11 pr-12 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 focus:ring-2 focus:ring-{{ $color_theme }}-500 outline-none transition-all" placeholder="••••••••" required>
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400"><i class="far fa-eye" id="eyeIcon"></i></button>
                    </div>
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center text-slate-600 dark:text-slate-400 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-{{ $color_theme }}-600 focus:ring-{{ $color_theme }}-500 mr-2"> Ingat Saya
                    </label>
                    <a href="#" class="font-bold text-{{ $color_theme }}-600 hover:underline">Lupa Password?</a>
                </div>

                <button type="submit" class="w-full py-4 rounded-2xl text-white font-bold shadow-xl transition-all transform active:scale-[0.98] bg-gradient-to-r from-{{ $color_theme }}-600 to-{{ $color_theme }}-700 hover:brightness-110">
                    Masuk ke Sistem <i class="fas fa-sign-in-alt ml-2"></i>
                </button>
            </form>

            @if($type !== 'internal')
                <div class="text-center mt-6">
                    <p class="text-sm text-slate-500 dark:text-slate-400">Belum punya akun?</p>
                    <a href="{{ route('register.' . $type) }}" class="inline-block mt-1 text-sm font-bold text-{{ $color_theme }}-600 hover:text-{{ $color_theme }}-700 hover:underline transition-colors">
                        Daftar Akun Baru
                    </a>
                </div>
            @else
                <div class="text-center mt-6 p-3 bg-slate-50 dark:bg-slate-700/50 rounded-lg border border-dashed border-slate-200 dark:border-slate-600">
                    <span class="text-xs text-slate-500 dark:text-slate-400 italic">
                        <i class="fas fa-lock mr-1"></i> Akses khusus Administrator.
                    </span>
                </div>
            @endif

            <div class="mt-8 text-center pt-6 border-t border-slate-100">
                <a href="{{ route('portal') }}" class="inline-flex items-center text-sm font-bold text-slate-400 hover:text-slate-800 transition-all group">
                    <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i> Kembali ke Portal
                </a>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('load', () => {
            document.body.classList.add('is-ready');
            new Swiper(".mySwiper", {
                spaceBetween: 30,
                centeredSlides: true,
                autoplay: { delay: 4000, disableOnInteraction: false },
                pagination: { el: ".swiper-pagination", clickable: true },
            });
        });

        function closeErrorModal() {
            const modal = document.getElementById('errorModal');
            modal.classList.add('opacity-0');
            modal.children[0].classList.add('scale-95');
            setTimeout(() => { modal.style.display = 'none'; }, 400);
        }

        function toggleTheme() {
            const html = document.documentElement;
            const icon = document.getElementById('theme-icon');
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                icon.className = 'fas fa-moon text-lg';
                localStorage.setItem('theme', 'light');
            } else {
                html.classList.add('dark');
                icon.className = 'fas fa-sun text-lg';
                localStorage.setItem('theme', 'dark');
            }
        }
        
        function togglePassword() {
            const input = document.getElementById('passwordInput');
            const icon = document.getElementById('eyeIcon');
            if (input.type === "password") { input.type = "text"; icon.className = 'far fa-eye-slash'; } 
            else { input.type = "password"; icon.className = 'far fa-eye'; }
        }

        (function init() {
            const savedTheme = localStorage.getItem('theme');
            const icon = document.getElementById('theme-icon');
            if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
                if(icon) icon.className = 'fas fa-sun text-lg';
            }
        })();
    </script>
</body>
</html>