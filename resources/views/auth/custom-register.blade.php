<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar {{ $title }} | SI-MUTU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    animation: { 
                        'fade-in': 'fadeIn 0.5s ease-out',
                        'scale-up': 'scaleUp 0.3s ease-out'
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        scaleUp: {
                            '0%': { opacity: '0', transform: 'scale(0.95)' },
                            '100%': { opacity: '1', transform: 'scale(1)' },
                        }
                    }
                }
            },
            safelist: [
                'bg-blue-600', 'bg-blue-700', 'bg-blue-100', 'text-blue-600', 'from-blue-600', 'to-blue-800', 'focus:ring-blue-500', 'bg-blue-400', 'border-blue-500',
                'bg-teal-600', 'bg-teal-700', 'bg-teal-100', 'text-teal-600', 'from-teal-600', 'to-teal-800', 'focus:ring-teal-500', 'bg-teal-400', 'border-teal-500',
                'bg-orange-600', 'bg-orange-700', 'bg-orange-100', 'text-orange-600', 'from-orange-600', 'to-orange-800', 'focus:ring-orange-500', 'bg-orange-400', 'border-orange-500',
            ]
        }
    </script>
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 font-sans text-slate-800 dark:text-slate-100 min-h-screen flex items-center justify-center relative overflow-x-hidden transition-colors duration-300 py-6 md:py-10">

    <button onclick="toggleTheme()" class="fixed top-4 right-4 z-50 p-3 rounded-full bg-white dark:bg-slate-800 shadow-lg border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-yellow-400 hover:scale-110 transition-transform focus:outline-none">
        <i id="theme-icon" class="fas fa-moon text-xl w-6 h-6 flex items-center justify-center"></i>
    </button>

    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute top-[-10%] left-[-10%] w-[300px] md:w-[600px] h-[300px] md:h-[600px] rounded-full mix-blend-multiply dark:mix-blend-color-dodge filter blur-3xl opacity-20 bg-{{ $color_theme }}-400"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[300px] md:w-[600px] h-[300px] md:h-[600px] rounded-full mix-blend-multiply dark:mix-blend-color-dodge filter blur-3xl opacity-20 bg-{{ $color_theme }}-400"></div>
    </div>

    <div class="w-full max-w-5xl bg-white dark:bg-slate-800 rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row m-4 z-10 animate-fade-in border border-slate-100 dark:border-slate-700 min-h-[600px] md:h-[auto] md:max-h-[90vh]">
        
        <div class="w-full md:w-5/12 p-8 md:p-10 flex flex-col justify-between text-white relative overflow-hidden bg-gradient-to-br from-{{ $color_theme }}-600 to-{{ $color_theme }}-800">
            <div class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none">
                <i class="fas fa-atom absolute -top-10 -left-10 text-[6rem] md:text-[10rem]"></i>
                <i class="fas fa-fingerprint absolute bottom-10 right-10 text-[5rem] md:text-[8rem]"></i>
            </div>

            <div class="relative z-10">
                <div class="flex items-center gap-2 mb-6">
                    <div class="w-8 h-8 bg-white/20 backdrop-blur rounded-lg flex items-center justify-center">
                        <i class="fas fa-shield-alt text-white"></i>
                    </div>
                    <span class="font-bold tracking-widest text-xs md:text-sm uppercase opacity-90">SI-MUTU DKKN</span>
                </div>
                <h2 class="text-2xl md:text-3xl font-extrabold mb-4 leading-tight">{{ $title }}</h2>
                <p class="text-white text-opacity-90 text-sm leading-relaxed max-w-md">
                    {{ $desc }}
                </p>
            </div>

            <div class="relative z-10 mt-8 md:mt-10">
                <p class="text-xs opacity-75 mb-3">Sudah punya akun?</p>
                <a href="{{ route('login.' . $type) }}" class="inline-block px-6 py-2.5 rounded-lg bg-white/20 hover:bg-white/30 text-white font-semibold text-sm transition-all border border-white/30 active:scale-95">
                    Login di sini
                </a>
            </div>
        </div>

        <div class="w-full md:w-7/12 p-6 md:p-12 bg-white dark:bg-slate-800 overflow-y-auto custom-scrollbar">
            <div class="text-center mb-6 md:mb-8">
                <div class="w-14 h-14 md:w-16 md:h-16 rounded-full mx-auto flex items-center justify-center mb-4 bg-{{ $color_theme }}-100 dark:bg-slate-700 text-{{ $color_theme }}-600 dark:text-{{ $color_theme }}-400">
                    <i class="fas {{ $icon }} text-xl md:text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white">Buat Akun Baru</h3>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm">
                    <ul class="list-disc pl-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="register_type" value="{{ $type }}">

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Instansi / Perusahaan</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-{{ $color_theme }}-500 focus:border-{{ $color_theme }}-500 outline-none transition-all text-sm" placeholder="Contoh: PT. Sumber Waras" required autofocus>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Kode Instansi (Jika Ada)</label>
                    <input type="text" name="kode_instansi" value="{{ old('kode_instansi') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-{{ $color_theme }}-500 focus:border-{{ $color_theme }}-500 outline-none transition-all text-sm" placeholder="Contoh: LUK-001">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email Resmi</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-{{ $color_theme }}-500 focus:border-{{ $color_theme }}-500 outline-none transition-all text-sm" placeholder="email@instansi.com" required>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Password</label>
                        <input type="password" name="password" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-{{ $color_theme }}-500 focus:border-{{ $color_theme }}-500 outline-none transition-all text-sm" placeholder="******" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-{{ $color_theme }}-500 focus:border-{{ $color_theme }}-500 outline-none transition-all text-sm" placeholder="******" required>
                    </div>
                </div>

                <button type="submit" class="w-full py-3.5 px-4 mt-6 rounded-lg text-white font-bold text-sm shadow-md hover:shadow-lg focus:ring-4 focus:ring-opacity-50 transition-all transform active:scale-[0.98] bg-{{ $color_theme }}-600 hover:bg-{{ $color_theme }}-700 focus:ring-{{ $color_theme }}-500">
                    Daftar Sekarang
                </button>

                <div class="md:hidden text-center mt-6 pt-6 border-t border-slate-100 dark:border-slate-700">
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Sudah punya akun? 
                        <a href="{{ route('login.' . $type) }}" class="text-{{ $color_theme }}-600 dark:text-{{ $color_theme }}-400 font-bold">Login</a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <div id="success-modal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 hidden">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
        
        <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-sm w-full p-8 text-center animate-scale-up border border-slate-200 dark:border-slate-700">
            <div class="w-20 h-20 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-check-circle text-4xl"></i>
            </div>
            
            <h4 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Registrasi Berhasil!</h4>
            <p id="success-message" class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed mb-8">
                Tolong tunggu maksimal 2 x 24 jam untuk verifikasi akun Anda.
            </p>
            
            <button onclick="closeModal()" class="w-full py-3 px-4 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold text-sm hover:opacity-90 transition-all active:scale-95 shadow-lg">
                Mengerti
            </button>
        </div>
    </div>

    <script>
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
        
        function closeModal() {
            const modal = document.getElementById('success-modal');
            modal.classList.add('hidden');
        }

        function showModal() {
            const modal = document.getElementById('success-modal');
            modal.classList.remove('hidden');
        }

        // Cek preference tema saat load
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            const icon = document.getElementById('theme-icon');
            if(icon) {
                icon.classList.replace('fa-moon', 'fa-sun');
            }
        }

        // MUNCULKAN MODAL JIKA ADA FLASH SESSION SUCCESS
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                showModal();
            @endif
        });
    </script>
</body>
</html>