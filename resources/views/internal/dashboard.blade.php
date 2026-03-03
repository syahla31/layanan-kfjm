<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Verifikasi | SI-MUTU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        
        /* Smooth scroll untuk main area */
        main::-webkit-scrollbar { width: 4px; }
        main::-webkit-scrollbar-track { background: transparent; }
        main::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    </style>
</head>

@php
    use App\Models\User;
    
    // Logika Database
    if(!isset($pendingUsers)) {
        $pendingUsers = User::where('status', 'pending')->orderBy('created_at', 'desc')->get();
    }

    if(!isset($historyUsers)) {
        $historyUsers = User::where('status', 'active')->where('role', '!=', 'admin')->orderBy('updated_at', 'desc')->take(5)->get();
    }
@endphp

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
                        <h2 class="text-xl font-extrabold text-slate-900 tracking-tight leading-tight">Verifikasi Pendaftaran</h2>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-[0.15em] leading-none">Antrian Persetujuan Aktif</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center gap-4">
                    <div class="hidden sm:block text-right mr-2">
                        <p class="text-xs font-bold text-slate-800 leading-none">Admin Internal</p>
                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mt-1">Bapeten DKKN</p>
                    </div>
                    <button class="w-11 h-11 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 border border-slate-200 hover:text-red-600 hover:border-red-100 hover:bg-red-50 transition-all">
                        <i class="far fa-bell text-lg"></i>
                    </button>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6 lg:p-10">
                
                <!-- Pesan Sukses -->
                @if (session('success'))
                    <div class="mb-8 p-5 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-3xl flex items-center gap-4 shadow-sm animate-in fade-in slide-in-from-top-4 duration-500">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500 flex items-center justify-center text-white shadow-lg shadow-emerald-500/20">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <p class="font-bold text-xs uppercase tracking-widest leading-none">Persetujuan Berhasil</p>
                            <p class="text-xs opacity-70 mt-1">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Petunjuk Verifikasi Card (Dibuat Lebih Halus) -->
                <div class="bg-white rounded-[2rem] border border-slate-200 p-8 mb-10 shadow-sm relative overflow-hidden group">
                    <!-- Background Pattern Decor -->
                    <div class="absolute top-0 right-0 p-12 opacity-[0.03] -mr-16 -mt-8 transition-transform group-hover:scale-110 duration-700">
                        <i class="fas fa-info-circle text-[15rem]"></i>
                    </div>
                    
                    <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center gap-8">
                        <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-3xl flex items-center justify-center text-3xl text-white shadow-xl shadow-blue-500/20">
                            <i class="fas fa-id-card-clip"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-black text-slate-900 text-lg uppercase tracking-wider leading-tight">Petunjuk Verifikasi</h3>
                            <p class="text-slate-500 text-sm mt-2 leading-relaxed max-w-2xl">
                                Pastikan melakukan validasi menyeluruh terhadap <span class="text-blue-600 font-bold">Dokumen Instansi</span> sebelum memberikan persetujuan. Setiap akun yang diaktifkan akan menerima notifikasi otomatis via email.
                            </p>
                            <div class="flex gap-4 mt-4">
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-bold text-blue-600 px-3 py-1 bg-blue-50 rounded-full">
                                    <i class="fas fa-check-circle"></i> Validasi NIB
                                </span>
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-bold text-indigo-600 px-3 py-1 bg-indigo-50 rounded-full">
                                    <i class="fas fa-check-circle"></i> Cek Kategori
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Antrian -->
                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden mb-10">
                    <div class="px-8 py-7 border-b border-slate-100 flex justify-between items-center bg-white">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-6 bg-red-600 rounded-full"></div>
                            <h3 class="font-black text-slate-800 tracking-widest text-xs uppercase">Antrian Registrasi (Pending)</h3>
                        </div>
                        <div class="bg-amber-50 text-amber-600 border border-amber-100 text-[10px] font-black px-4 py-2 rounded-2xl uppercase tracking-[0.1em] shadow-sm shadow-amber-900/5">
                            {{ $pendingUsers->count() }} Menunggu
                        </div>
                    </div>

                    @if($pendingUsers->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left whitespace-nowrap">
                                <thead class="text-[10px] text-slate-400 uppercase bg-slate-50/50 font-black tracking-widest border-b border-slate-100">
                                    <tr>
                                        <th class="px-8 py-5">Identitas Instansi</th>
                                        <th class="px-8 py-5">Modul Layanan</th>
                                        <th class="px-8 py-5 text-right">Opsi Verifikasi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    @foreach($pendingUsers as $user)
                                    <tr class="hover:bg-slate-50/80 transition-all group">
                                        <td class="px-8 py-6">
                                            <div class="flex items-center gap-5">
                                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-500 flex items-center justify-center text-sm font-black border border-slate-200 group-hover:bg-red-50 group-hover:text-red-600 group-hover:border-red-100 transition-all duration-300">
                                                    {{ substr($user->name, 0, 2) }}
                                                </div>
                                                <div>
                                                    <span class="font-bold text-slate-900 block text-base leading-tight group-hover:text-red-600 transition-colors">{{ $user->name }}</span>
                                                    <span class="text-[11px] text-slate-400 font-medium mt-1 block">{{ $user->email }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6">
                                            <span class="px-4 py-1.5 bg-slate-50 text-slate-600 text-[10px] font-black rounded-xl border border-slate-200 uppercase tracking-widest group-hover:bg-white group-hover:border-slate-300 transition-colors">
                                                {{ $user->category }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-6 text-right">
                                            <form action="{{ url('/internal/approve/' . $user->id) }}" method="POST" class="inline-block">
                                                @csrf 
                                                <button class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-red-900/10 active:scale-95 transition-all flex items-center gap-2 ml-auto group/btn">
                                                    <i class="fas fa-check-circle group-hover/btn:scale-125 transition-transform"></i> Aktifkan Akun
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="py-24 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center text-slate-300 mb-6 border-4 border-white shadow-inner">
                                    <i class="fas fa-check-double text-3xl"></i>
                                </div>
                                <h4 class="text-sm font-black uppercase tracking-widest text-slate-800">Antrian Bersih</h4>
                                <p class="text-xs text-slate-400 mt-2 font-medium">Semua pendaftaran telah diproses.</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- History Persetujuan -->
                @if($historyUsers->count() > 0)
                <div class="flex items-center gap-3 mb-6 ml-2">
                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                    <h4 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.25em]">Aktivitas Terkini</h4>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-16">
                    @foreach($historyUsers as $user)
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-200 flex items-center gap-5 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base font-black border border-emerald-100 shadow-sm shadow-emerald-900/5 shrink-0">
                            {{ substr($user->name, 0, 2) }}
                        </div>
                        <div class="overflow-hidden">
                            <p class="font-bold text-slate-900 truncate text-sm leading-tight">{{ $user->name }}</p>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Disetujui {{ $user->updated_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </main>
        </div>
    </div>
</body>
</html>