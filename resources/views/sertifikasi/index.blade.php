<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan Sertifikasi | SI-MUTU DKKN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#7c3aed', // Purple theme
                        secondary: '#fbbf24',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 dark:bg-slate-900 font-sans text-slate-800 dark:text-slate-100 min-h-screen flex flex-col transition-colors duration-300">

    <!-- THEME TOGGLE BUTTON (Disamakan dengan Portal & Login) -->
    <!-- Posisi fixed di pojok kanan atas, z-index 60 agar di atas navbar -->
    <button onclick="toggleTheme()" class="fixed top-4 right-4 z-[60] p-3 rounded-full bg-white dark:bg-slate-800 shadow-lg border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-yellow-400 hover:scale-110 transition-transform focus:outline-none">
        <i id="theme-icon" class="fas fa-moon text-xl w-6 h-6 flex items-center justify-center"></i>
    </button>

    <!-- Navbar Sederhana -->
    <nav class="bg-white dark:bg-slate-800 shadow-sm border-b border-slate-200 dark:border-slate-700 py-7 px-6 fixed w-full z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            
            <!-- BAGIAN KIRI: Tombol Kembali & Logo -->
            <div class="flex items-center gap-6">
                <!-- Tombol Kembali -->
                <a href="{{ route('portal') }}" class="text-sm font-medium text-slate-500 hover:text-purple-600 dark:text-slate-400 transition-colors flex items-center gap-2 group">
                    <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i> Kembali
                </a>

                <!-- Pembatas Vertikal -->
                <div class="h-6 w-px bg-slate-200 dark:bg-slate-700 hidden sm:block"></div>

                <!-- Logo & Judul -->
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-purple-600 rounded-lg flex items-center justify-center text-white">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h1 class="font-bold text-lg tracking-wide text-slate-800 dark:text-white hidden sm:block">Sertifikasi <span class="text-purple-600">DKKN</span></h1>
                </div>
            </div>

            <!-- BAGIAN KANAN: Kosong (Karena tombol theme sudah dipindah ke fixed position) -->
            <div></div>
        </div>
    </nav>

    <!-- Konten Utama -->
    <main class="flex-grow flex items-center justify-center pt-20 pb-10 px-4 relative overflow-hidden">
        
        <!-- Background Hiasan -->
        <div class="absolute top-0 left-0 w-full h-full pointer-events-none overflow-hidden opacity-30">
            <div class="absolute top-[-10%] right-[-5%] w-[500px] h-[500px] bg-purple-200 dark:bg-purple-900 rounded-full mix-blend-multiply filter blur-3xl opacity-50"></div>
            <div class="absolute bottom-[-10%] left-[-5%] w-[500px] h-[500px] bg-blue-200 dark:bg-blue-900 rounded-full mix-blend-multiply filter blur-3xl opacity-50"></div>
        </div>

        <div class="max-w-5xl w-full z-10">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 dark:text-white mb-4">Layanan Lisensi Petugas</h2>
                <p class="text-slate-500 dark:text-slate-400 max-w-2xl mx-auto">
                    Portal terintegrasi untuk informasi jadwal ujian dan registrasi akun petugas proteksi radiasi melalui sistem BALIS.
                </p>
            </div>

            <!-- Grid Kartu Pilihan -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <!-- KARTU 1: LIHAT JADWAL -->
                <a href="https://balis-pekerja.bapeten.go.id/frontend/web/site/faq" target="_blank" class="group relative bg-white dark:bg-slate-800 rounded-2xl p-8 shadow-xl hover:shadow-2xl border border-slate-100 dark:border-slate-700 transition-all duration-300 hover:-translate-y-2 overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <i class="fas fa-calendar-alt text-9xl text-purple-600"></i>
                    </div>
                    
                    <div class="relative z-10">
                        <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900/50 text-purple-600 dark:text-purple-400 rounded-2xl flex items-center justify-center text-3xl mb-6 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white mb-3">Jadwal Uji Kompetensi</h3>
                        <p class="text-slate-500 dark:text-slate-400 mb-8 leading-relaxed">
                            Cek jadwal pelaksanaan ujian sertifikasi terkini, lokasi ujian, dan kuota yang tersedia di seluruh Indonesia.
                        </p>
                        <span class="inline-flex items-center text-purple-600 dark:text-purple-400 font-bold group-hover:gap-2 transition-all">
                            Lihat Jadwal <i class="fas fa-arrow-right ml-2"></i>
                        </span>
                    </div>
                </a>

                <!-- KARTU 2: REGISTRASI BARU -->
                <a href="https://balis.bapeten.go.id/frontend2/public/index.php/new-registrasi" target="_blank" class="group relative bg-white dark:bg-slate-800 rounded-2xl p-8 shadow-xl hover:shadow-2xl border border-slate-100 dark:border-slate-700 transition-all duration-300 hover:-translate-y-2 overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <i class="fas fa-user-plus text-9xl text-blue-600"></i>
                    </div>
                    
                    <div class="relative z-10">
                        <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 rounded-2xl flex items-center justify-center text-3xl mb-6 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white mb-3">Registrasi Akun Baru</h3>
                        <p class="text-slate-500 dark:text-slate-400 mb-8 leading-relaxed">
                            Belum memiliki akun BALIS? Lakukan pendaftaran akun baru untuk Ujian Sertifikasi PENGUJI BERKUALIFIKASI (PB)
                        </p>
                        <span class="inline-flex items-center text-blue-600 dark:text-blue-400 font-bold group-hover:gap-2 transition-all">
                            Daftar Sekarang <i class="fas fa-external-link-alt ml-2"></i>
                        </span>
                    </div>
                </a>

            </div>

            <div class="mt-12 text-center">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-full text-sm text-yellow-700 dark:text-yellow-400">
                    <i class="fas fa-info-circle"></i>
                    <span>Anda akan diarahkan ke website eksternal BALIS BAPETEN</span>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 py-6 text-center text-sm text-slate-500 dark:text-slate-400">
        &copy; 2026 Direktorat Keteknikan dan Kesiapsiagaan Nuklir - BAPETEN
    </footer>

    <script>
        // Script Dark Mode (Konsisten dengan halaman lain)
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
        
        // Auto Load Theme
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