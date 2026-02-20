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
                    animation: { 'fade-in': 'fadeIn 0.5s ease-out' },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
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
</head>
<body class="bg-slate-50 dark:bg-slate-900 font-sans text-slate-800 dark:text-slate-100 min-h-screen flex items-center justify-center relative overflow-hidden transition-colors duration-300 py-10">

    <!-- Tombol Dark Mode -->
    <button onclick="toggleTheme()" class="fixed top-4 right-4 z-50 p-3 rounded-full bg-white dark:bg-slate-800 shadow-lg border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-yellow-400 hover:scale-110 transition-transform focus:outline-none">
        <i id="theme-icon" class="fas fa-moon text-xl w-6 h-6 flex items-center justify-center"></i>
    </button>

    <!-- Background Decoration -->
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[600px] h-[600px] rounded-full mix-blend-multiply dark:mix-blend-color-dodge filter blur-3xl opacity-20 bg-{{ $color_theme }}-400"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[600px] h-[600px] rounded-full mix-blend-multiply dark:mix-blend-color-dodge filter blur-3xl opacity-20 bg-{{ $color_theme }}-400"></div>
    </div>

    <!-- MAIN CARD -->
    <div class="w-full max-w-5xl bg-white dark:bg-slate-800 rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row m-4 z-10 animate-fade-in border border-slate-100 dark:border-slate-700">
        
        <!-- BAGIAN KIRI: Info -->
        <div class="md:w-5/12 p-10 flex flex-col justify-between text-white relative overflow-hidden bg-gradient-to-br from-{{ $color_theme }}-600 to-{{ $color_theme }}-800">
            <div class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none">
                <i class="fas fa-atom absolute -top-10 -left-10 text-[10rem]"></i>
                <i class="fas fa-fingerprint absolute bottom-10 right-10 text-[8rem]"></i>
            </div>

            <div class="relative z-10">
                <div class="flex items-center gap-2 mb-6">
                    <div class="w-8 h-8 bg-white/20 backdrop-blur rounded-lg flex items-center justify-center">
                        <i class="fas fa-shield-alt text-white"></i>
                    </div>
                    <span class="font-bold tracking-widest text-sm uppercase opacity-90">SI-MUTU DKKN</span>
                </div>
                <h2 class="text-3xl font-extrabold mb-4 leading-tight">{{ $title }}</h2>
                <p class="text-white text-opacity-90 text-sm leading-relaxed">
                    {{ $desc }}
                </p>
            </div>

            <div class="relative z-10 mt-10">
                <p class="text-xs opacity-75 mb-2">Sudah punya akun?</p>
                <a href="{{ route('login.' . $type) }}" class="inline-block px-6 py-2 rounded-lg bg-white/20 hover:bg-white/30 text-white font-semibold text-sm transition-colors border border-white/30">
                    Login di sini
                </a>
            </div>
        </div>

        <!-- BAGIAN KANAN: Form Register -->
        <div class="md:w-7/12 p-8 md:p-12 bg-white dark:bg-slate-800 overflow-y-auto max-h-[90vh]">
            <div class="text-center mb-8">
                <div class="w-16 h-16 rounded-full mx-auto flex items-center justify-center mb-4 bg-{{ $color_theme }}-100 dark:bg-slate-700 text-{{ $color_theme }}-600 dark:text-{{ $color_theme }}-400">
                    <i class="fas {{ $icon }} text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white">Buat Akun Baru</h3>
            </div>

            <!-- Tampilkan Error -->
            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded text-sm">
                    <ul class="list-disc pl-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- 
                PENTING: Action mengarah ke rute 'register'.
                Karena kita sudah override di routes/web.php, ini akan memanggil CustomRegisterController.
            -->
            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf
                
                <!-- ========================================================================= -->
                <!-- INPUT KUNCI: register_type (Menyimpan 'pelatihan', 'uji', atau 'sinarx') -->
                <!-- Nilai ini dikirim ke Controller dan disimpan ke kolom 'category' di DB   -->
                <!-- ========================================================================= -->
                <input type="hidden" name="register_type" value="{{ $type }}">

                <!-- Nama Instansi / User -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Instansi / Perusahaan</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-{{ $color_theme }}-500 focus:border-{{ $color_theme }}-500 outline-none transition-all text-sm" placeholder="Contoh: PT. Sumber Waras" required autofocus>
                </div>

                <!-- Kode Instansi (Opsional) -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Kode Instansi (Jika Ada)</label>
                    <input type="text" name="kode_instansi" value="{{ old('kode_instansi') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-{{ $color_theme }}-500 focus:border-{{ $color_theme }}-500 outline-none transition-all text-sm" placeholder="Contoh: LUK-001">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email Resmi</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-{{ $color_theme }}-500 focus:border-{{ $color_theme }}-500 outline-none transition-all text-sm" placeholder="email@instansi.com" required>
                </div>

                <!-- Password -->
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

                <button type="submit" class="w-full py-3 px-4 mt-6 rounded-lg text-white font-bold text-sm shadow-md hover:shadow-lg focus:ring-4 focus:ring-opacity-50 transition-all transform active:scale-95 bg-{{ $color_theme }}-600 hover:bg-{{ $color_theme }}-700 focus:ring-{{ $color_theme }}-500">
                    Daftar Sekarang
                </button>
            </form>
        </div>
    </div>

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
        
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            const icon = document.getElementById('theme-icon');
            if(icon) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            }
        }
    </script>
</body>
</html>