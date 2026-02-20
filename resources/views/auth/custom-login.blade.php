<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login {{ $title }} | SI-MUTU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    animation: { 
                        'fade-in': 'fadeIn 0.5s ease-out',
                        'pop-in': 'popIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards' 
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        popIn: {
                            '0%': { opacity: '0', transform: 'scale(0.9)' },
                            '100%': { opacity: '1', transform: 'scale(1)' },
                        }
                    }
                }
            },
            safelist: [
                'bg-blue-600', 'bg-blue-700', 'bg-blue-100', 'text-blue-600', 'from-blue-600', 'to-blue-800', 'focus:ring-blue-500', 'bg-blue-400', 'border-blue-500',
                'bg-teal-600', 'bg-teal-700', 'bg-teal-100', 'text-teal-600', 'from-teal-600', 'to-teal-800', 'focus:ring-teal-500', 'bg-teal-400', 'border-teal-500',
                'bg-orange-600', 'bg-orange-700', 'bg-orange-100', 'text-orange-600', 'from-orange-600', 'to-orange-800', 'focus:ring-orange-500', 'bg-orange-400', 'border-orange-500',
                'bg-red-600', 'bg-red-700', 'bg-red-100', 'text-red-600', 'from-red-600', 'to-red-800', 'focus:ring-red-500', 'bg-red-400', 'border-red-500',
            ]
        }
    </script>
</head>
<body class="bg-slate-50 dark:bg-slate-900 font-sans text-slate-800 dark:text-slate-100 min-h-[100dvh] flex items-center justify-center relative overflow-x-hidden transition-colors duration-300 py-6 px-4 sm:px-6 lg:px-8">

    <!-- Tombol Dark Mode -->
    <button onclick="toggleTheme()" class="absolute top-4 right-4 z-50 p-2.5 rounded-full bg-white/90 dark:bg-slate-800/90 backdrop-blur shadow-lg border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-yellow-400 hover:scale-110 active:scale-95 transition-all focus:outline-none">
        <i id="theme-icon" class="fas fa-moon text-lg w-5 h-5 flex items-center justify-center"></i>
    </button>

    <!-- Background Decoration -->
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
        <div class="absolute top-[-50px] left-[-50px] w-48 h-48 md:w-[600px] md:h-[600px] rounded-full mix-blend-multiply dark:mix-blend-color-dodge filter blur-3xl opacity-30 bg-{{ $color_theme }}-400 animate-pulse"></div>
        <div class="absolute bottom-[-50px] right-[-50px] w-48 h-48 md:w-[600px] md:h-[600px] rounded-full mix-blend-multiply dark:mix-blend-color-dodge filter blur-3xl opacity-30 bg-{{ $color_theme }}-400 animate-pulse" style="animation-delay: 2s;"></div>
    </div>

    <!-- MAIN LOGIN CARD -->
    <div class="w-full max-w-[900px] bg-white dark:bg-slate-800 rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row z-10 animate-fade-in border border-slate-100 dark:border-slate-700 relative">
        
        <!-- BAGIAN KIRI: Info & Warna Utama -->
        <div class="w-full md:w-5/12 p-8 md:p-12 flex flex-col justify-center md:justify-between text-white relative overflow-hidden bg-gradient-to-br from-{{ $color_theme }}-600 to-{{ $color_theme }}-800 text-center md:text-left">
            <div class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none overflow-hidden">
                <i class="fas fa-atom absolute -top-4 -left-4 text-6xl md:text-[10rem] transform rotate-12"></i>
                <i class="fas fa-fingerprint absolute bottom-4 right-4 md:bottom-10 md:right-10 text-6xl md:text-[8rem] transform -rotate-12"></i>
            </div>

            <div class="relative z-10">
                <div class="flex items-center justify-center md:justify-start gap-2 mb-4 md:mb-6">
                    <div class="w-8 h-8 bg-white/20 backdrop-blur rounded-lg flex items-center justify-center shadow-inner">
                        <i class="fas fa-shield-alt text-white text-sm"></i>
                    </div>
                    <span class="font-bold tracking-widest text-xs md:text-sm uppercase opacity-90 shadow-black drop-shadow-md">SI-MUTU DKKN</span>
                </div>
                
                <h2 class="text-2xl md:text-4xl font-extrabold mb-3 md:mb-4 leading-tight shadow-black drop-shadow-sm">{{ $title }}</h2>
                
                <p class="text-white text-opacity-90 text-sm leading-relaxed hidden xs:block">
                    {{ $desc }}
                </p>
            </div>

            <div class="relative z-10 mt-8 hidden md:block">
                <div class="flex items-center gap-3 text-xs opacity-75 hover:opacity-100 transition-opacity cursor-help">
                    <i class="fas fa-info-circle"></i>
                    <span>Butuh bantuan akses? Hubungi Admin.</span>
                </div>
            </div>
        </div>

        <!-- BAGIAN KANAN: Form Login -->
        <div class="w-full md:w-7/12 p-6 md:p-12 bg-white dark:bg-slate-800 flex flex-col justify-center">
            <div class="text-center mb-6">
                <div class="w-16 h-16 rounded-full mx-auto flex items-center justify-center mb-4 bg-{{ $color_theme }}-50 dark:bg-slate-700 text-{{ $color_theme }}-600 dark:text-{{ $color_theme }}-400 shadow-sm">
                    <i class="fas {{ $icon }} text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white">Selamat Datang</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Silakan masuk ke akun Anda</p>
            </div>

            <!-- MODAL POPUP ERROR (Gantikan Alert Biasa) -->
            @if ($errors->any())
                <!-- Backdrop -->
                <div id="errorModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-[2px] p-4 transition-opacity duration-300">
                    <!-- Modal Card -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-sm md:max-w-md overflow-hidden transform scale-90 animate-pop-in border-2 border-red-100 dark:border-red-900/50">
                        
                        <!-- Header with Icon -->
                        <div class="bg-red-50 dark:bg-red-900/20 p-6 text-center border-b border-red-100 dark:border-red-900/30 relative overflow-hidden">
                            <!-- Background Pattern Icon -->
                            <i class="fas fa-exclamation-triangle absolute -right-6 -bottom-6 text-8xl text-red-100 dark:text-red-900/40 opacity-50 transform -rotate-12"></i>
                            
                            <div class="w-20 h-20 bg-red-100 dark:bg-red-800/40 rounded-full flex items-center justify-center mx-auto mb-3 shadow-inner ring-4 ring-white dark:ring-slate-700 relative z-10">
                                <i class="fas fa-ban text-4xl text-red-500 dark:text-red-400 animate-pulse"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-red-600 dark:text-red-400 relative z-10">Akses Ditolak!</h3>
                        </div>
                        
                        <!-- Body Message -->
                        <div class="p-6 text-center relative z-10">
                            <ul class="text-slate-600 dark:text-slate-300 text-sm md:text-base font-medium space-y-2 leading-relaxed">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Footer Action -->
                        <div class="p-4 bg-slate-50 dark:bg-slate-900/50 flex justify-center border-t border-slate-100 dark:border-slate-700">
                            <button type="button" onclick="closeErrorModal()" class="w-full py-3 px-6 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold shadow-lg shadow-red-200 dark:shadow-none transition-all transform active:scale-95 flex items-center justify-center gap-2">
                                <i class="fas fa-check-circle"></i>
                                Saya Mengerti
                            </button>
                        </div>
                    </div>
                </div>
            @endif
            
            @if (session('success'))
                <div class="mb-5 p-3 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-600 dark:text-green-300 rounded-lg text-sm flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="login_type" value="{{ $type }}">

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5 ml-1">Email Instansi</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 group-focus-within:text-{{ $color_theme }}-500 transition-colors">
                            <i class="far fa-envelope"></i>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}" 
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-{{ $color_theme }}-500 focus:border-{{ $color_theme }}-500 outline-none transition-all text-base md:text-sm shadow-sm" 
                            placeholder="nama@instansi.co.id" required autofocus>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5 ml-1">Password</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 group-focus-within:text-{{ $color_theme }}-500 transition-colors">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password" id="passwordInput"
                            class="w-full pl-10 pr-10 py-3 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-{{ $color_theme }}-500 focus:border-{{ $color_theme }}-500 outline-none transition-all text-base md:text-sm shadow-sm" 
                            placeholder="••••••••" required>
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer focus:outline-none">
                            <i class="far fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center text-slate-600 dark:text-slate-400 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-{{ $color_theme }}-600 focus:ring-{{ $color_theme }}-500 mr-2">
                        Ingat Saya
                    </label>
                    <a href="#" class="font-medium text-{{ $color_theme }}-600 hover:text-{{ $color_theme }}-500 hover:underline">Lupa Password?</a>
                </div>

                <button type="submit" class="w-full py-3.5 px-4 rounded-xl text-white font-bold text-sm shadow-lg hover:shadow-xl focus:ring-4 focus:ring-opacity-50 transition-all transform active:scale-[0.98] bg-gradient-to-r from-{{ $color_theme }}-600 to-{{ $color_theme }}-700 hover:from-{{ $color_theme }}-500 hover:to-{{ $color_theme }}-600 focus:ring-{{ $color_theme }}-500">
                    Masuk ke Sistem
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

            <div class="mt-8 text-center pt-6 border-t border-slate-100 dark:border-slate-700">
                <a href="{{ route('portal') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white transition-colors group p-2">
                    <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i> Kembali ke Portal
                </a>
            </div>
        </div>
    </div>

    <!-- SCRIPT -->
    <script>
        function closeErrorModal() {
            const modal = document.getElementById('errorModal');
            if(modal) {
                modal.classList.add('opacity-0');
                setTimeout(() => {
                    modal.style.display = 'none';
                }, 300);
            }
        }

        // Toggle Dark Mode
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

        // Toggle Password Visibility
        function togglePassword() {
            const input = document.getElementById('passwordInput');
            const icon = document.getElementById('eyeIcon');
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = "password";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>