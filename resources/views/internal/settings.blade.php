<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfigurasi Sistem | SI-MUTU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }

        /* Custom scrollbar */
        main::-webkit-scrollbar { width: 4px; }
        main::-webkit-scrollbar-track { background: transparent; }
        main::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 overflow-hidden" x-data="{ sidebarOpen: false }">

    <div class="flex h-screen w-full overflow-hidden">

        <!-- SIDEBAR -->
        @include('components.internal-sidebar')

        <!-- KONTEN UTAMA -->
        <div class="flex-1 flex flex-col min-w-0 bg-slate-50">

            <!-- HEADER -->
            <header class="bg-white border-b border-slate-200 h-20 flex items-center justify-between px-6 lg:px-10 shrink-0 relative z-30">
                <div class="flex items-center gap-5">
                    <!-- Tombol Hamburger Mobile -->
                    <button @click="sidebarOpen = true"
                        class="lg:hidden w-11 h-11 flex items-center justify-center rounded-2xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-900 tracking-tight leading-tight">Konfigurasi Sistem</h2>
                        <div class="flex items-center gap-2 mt-1">
                            <i class="fas fa-sliders-h text-[10px] text-red-500"></i>
                            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-[0.15em] leading-none">Parameter Operasional SI-MUTU</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="hidden sm:block text-right mr-2">
                        <p class="text-xs font-bold text-slate-800 leading-none">Admin Internal</p>
                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mt-1">Sesi: Terotorisasi</p>
                    </div>
                    <button class="w-11 h-11 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 border border-slate-200 hover:text-red-600 hover:border-red-100 hover:bg-red-50 transition-all">
                        <i class="far fa-bell text-lg"></i>
                    </button>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6 lg:p-10">
                <div class="max-w-4xl mx-auto space-y-8 pb-12">

                    <!-- 1. PENGUMUMAN DASHBOARD -->
                    <div class="bg-white rounded-[2.5rem] border border-slate-200 p-8 shadow-sm relative overflow-hidden group">
                        <!-- Decor Ikon -->
                        <div class="absolute top-0 right-0 p-12 opacity-[0.03] -mr-16 -mt-8 transition-transform group-hover:scale-110 duration-700">
                            <i class="fas fa-bullhorn text-[15rem]"></i>
                        </div>

                        <div class="relative z-10">
                            <div class="flex items-center gap-4 mb-8">
                                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-lg shadow-inner">
                                    <i class="fas fa-bullhorn"></i>
                                </div>
                                <div>
                                    <h3 class="font-black text-slate-900 text-sm uppercase tracking-widest leading-none">Pengumuman Dashboard</h3>
                                    <p class="text-[10px] text-slate-400 font-bold mt-2 uppercase tracking-wider leading-none">Informasi untuk pengguna eksternal</p>
                                </div>
                            </div>

                            <form class="space-y-6">
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Isi Pesan Pengumuman</label>
                                    <textarea rows="4" 
                                        class="w-full bg-slate-50 border border-slate-200 rounded-[1.5rem] p-6 text-sm font-medium text-slate-700 focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all placeholder:text-slate-300"
                                        placeholder="Tuliskan pesan maintenance atau informasi penting lainnya di sini..."></textarea>
                                </div>

                                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 pt-2">
                                    <label class="flex items-center gap-4 cursor-pointer group/toggle">
                                        <div class="relative w-12 h-6 bg-slate-200 rounded-full group-hover/toggle:bg-slate-300 transition-colors">
                                            <input type="checkbox" class="sr-only peer" checked>
                                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-all peer-checked:translate-x-6 peer-checked:bg-red-600"></div>
                                        </div>
                                        <span class="text-xs font-black text-slate-600 uppercase tracking-widest">Tampilkan di Dashboard User</span>
                                    </label>

                                    <button type="button" 
                                        class="w-full sm:w-auto bg-slate-900 hover:bg-red-600 text-white px-8 py-3.5 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg active:scale-95 transition-all">
                                        Simpan Pengumuman
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- 2. MODE MAINTENANCE -->
                    <div class="bg-white rounded-[2.5rem] border border-slate-200 p-8 shadow-sm relative overflow-hidden group">
                        <!-- Decor Ikon -->
                        <div class="absolute top-0 right-0 p-12 opacity-[0.03] -mr-16 -mt-8 transition-transform group-hover:scale-110 duration-700">
                            <i class="fas fa-tools text-[15rem]"></i>
                        </div>

                        <div class="relative z-10">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center text-lg shadow-inner">
                                        <i class="fas fa-tools"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-black text-slate-900 text-sm uppercase tracking-widest leading-none">Mode Pemeliharaan</h3>
                                        <div class="flex items-center gap-2 mt-2">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest leading-none">Status: Online</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex-1 md:max-w-sm">
                                    <p class="text-[11px] text-slate-400 font-medium leading-relaxed italic">
                                        Saat diaktifkan, seluruh akses pengguna eksternal akan dibatasi dan dialihkan ke halaman maintenance. Admin internal tetap memiliki akses penuh.
                                    </p>
                                </div>

                                <div class="shrink-0">
                                    <button type="button" 
                                        class="bg-white border-2 border-slate-900 text-slate-900 hover:bg-slate-900 hover:text-white px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest active:scale-95 transition-all">
                                        Aktifkan Maintenance
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>
</body>

</html>