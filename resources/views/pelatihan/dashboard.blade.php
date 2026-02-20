<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Lembaga | SI-MUTU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none;  scrollbar-width: none; }
        .table-row-hover:hover td { background-color: #f8fafc; }
        
        /* Glass overlay for mobile */
        .glass-overlay {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <!-- DATA FETCHING -->
    @php
        use App\Models\Submission;
        use Illuminate\Support\Facades\Auth;
        
        $userId = Auth::id();

        // Ambil Semua Data User Ini
        if (!isset($allSubmissions)) {
            $allSubmissions = Submission::where('user_id', $userId)
                                        ->orderBy('created_at', 'desc')
                                        ->get();
        }

        // Statistik Status
        $stats = [
            'total' => $allSubmissions->count(),
            'pending' => $allSubmissions->where('status', 'pending')->count(),
            'approved' => $allSubmissions->where('status', 'approved')->count(),
            'rejected' => $allSubmissions->where('status', 'rejected')->count(),
        ];

        // Hitung Per Kategori untuk Badge Menu
        $countKAK = $allSubmissions->where('type', 'KAK')->count();
        $countLapkin = $allSubmissions->where('type', 'Laporan Kinerja')->count();
        $alertSurvailen = $allSubmissions->where('type', 'Survailen')->filter(fn($i) => !$i->file_path || $i->status == 'rejected')->count();
        $alertVerifikasi = $allSubmissions->where('type', 'Verifikasi')->filter(fn($i) => !$i->user_note && $i->status != 'approved')->count();

        // 5 Data Terakhir untuk Tabel
        $recentData = $allSubmissions->take(5);
    @endphp

    <div class="flex h-screen overflow-hidden bg-slate-50">
        
        <!-- === MOBILE OVERLAY === -->
        <div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 z-40 hidden md:hidden glass-overlay transition-opacity duration-300"></div>

        <!-- === SIDEBAR WRAPPER (Unified & Responsive) === -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-blue-900 shadow-2xl md:shadow-none transform -translate-x-full transition-transform duration-300 ease-in-out md:translate-x-0 md:static md:inset-auto flex flex-col h-full border-r border-blue-800/50">
            @include('components.pelatihan-sidebar')
        </aside>

        <div class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50 relative w-full">
            
            <!-- === MOBILE HEADER (Button Kiri) === -->
            <div class="md:hidden bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between z-20 sticky top-0 shadow-sm">
                <div class="flex items-center gap-3">
                    <!-- Tombol Hamburger di Kiri -->
                    <button onclick="toggleSidebar()" class="p-2 text-slate-500 hover:text-blue-600 hover:bg-slate-100 rounded-lg transition-colors">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    
                    <!-- Logo/Brand -->
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white shadow-sm">
                            <i class="fas fa-chalkboard-teacher text-sm"></i>
                        </div>
                        <span class="font-bold text-slate-800 text-sm tracking-wide">SI-PELATIHAN</span>
                    </div>
                </div>

                <!-- Profile Icon Kanan -->
                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-xs font-bold border border-blue-200">
                    {{ substr(Auth::user()->name ?? 'L', 0, 1) }}
                </div>
            </div>

            <!-- HEADER DESKTOP -->
            <div class="hidden md:block">
                @include('components.pelatihan-header', [
                    'title' => 'Dashboard Lembaga',
                    'subtitle' => 'Ringkasan aktivitas dan status penjaminan mutu'
                ])
            </div>

            <!-- MAIN CONTENT -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-6 lg:p-8 space-y-6 md:space-y-8">
                
                <!-- WELCOME BANNER -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-6 text-white shadow-lg shadow-blue-200 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 -mr-10 -mt-10 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl group-hover:scale-110 transition-transform duration-700"></div>
                    <div class="absolute bottom-0 left-0 -ml-10 -mb-10 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl"></div>
                    
                    <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <h2 class="text-2xl font-bold mb-1">Halo, {{ Auth::user()->name ?? 'Lembaga' }}! 👋</h2>
                            <p class="text-blue-100 text-sm max-w-xl">
                                Selamat datang di Sistem Informasi Penjaminan Mutu. Pantau status pengajuan KAK, Laporan Kinerja, dan respon Survailen Anda di sini.
                            </p>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm border border-white/20 p-3 rounded-xl flex items-center gap-3 shadow-inner">
                            <div class="text-right">
                                <p class="text-xs text-blue-100">Status Akun</p>
                                <p class="font-bold text-sm text-emerald-300">Aktif & Terverifikasi</p>
                            </div>
                            <div class="h-8 w-8 rounded-full bg-emerald-400 flex items-center justify-center text-blue-900 shadow-md">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STATISTIK GRID -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <!-- Total -->
                    <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Total Dokumen</p>
                        <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['total'] }}</h3>
                    </div>
                    <!-- Proses -->
                    <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm border-l-4 border-l-amber-400 hover:shadow-md transition-shadow">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Sedang Proses</p>
                        <h3 class="text-2xl font-bold text-amber-500 mt-1">{{ $stats['pending'] }}</h3>
                    </div>
                    <!-- Revisi -->
                    <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm border-l-4 border-l-rose-400 hover:shadow-md transition-shadow">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Perlu Revisi</p>
                        <h3 class="text-2xl font-bold text-rose-500 mt-1">{{ $stats['rejected'] }}</h3>
                    </div>
                    <!-- Selesai -->
                    <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm border-l-4 border-l-emerald-400 hover:shadow-md transition-shadow">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Disetujui</p>
                        <h3 class="text-2xl font-bold text-emerald-500 mt-1">{{ $stats['approved'] }}</h3>
                    </div>
                </div>

                <!-- MENU LAYANAN (QUICK ACCESS) -->
                <div>
                    <h3 class="font-bold text-slate-800 text-lg mb-4 flex items-center gap-2">
                        <i class="fas fa-th-large text-slate-400"></i> Menu Layanan
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                        
                        <!-- 1. KAK -->
                        <a href="{{ url('/pelatihan/kak') }}" class="group bg-white p-5 rounded-2xl shadow-sm border border-slate-200 hover:border-indigo-400 hover:shadow-md transition-all relative overflow-hidden">
                            <div class="flex justify-between items-start mb-4">
                                <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl group-hover:scale-110 transition-transform">
                                    <i class="fas fa-project-diagram text-xl"></i>
                                </div>
                                <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-2 py-1 rounded-lg">{{ $countKAK }} File</span>
                            </div>
                            <h4 class="font-bold text-slate-700">Perencanaan (KAK)</h4>
                            <p class="text-xs text-slate-500 mt-1">Upload kerangka acuan kerja tahunan.</p>
                        </a>

                        <!-- 2. Lapkin -->
                        <a href="{{ route('lapkin.index') }}" class="group bg-white p-5 rounded-2xl shadow-sm border border-slate-200 hover:border-teal-400 hover:shadow-md transition-all relative overflow-hidden">
                            <div class="flex justify-between items-start mb-4">
                                <div class="p-3 bg-teal-50 text-teal-600 rounded-xl group-hover:scale-110 transition-transform">
                                    <i class="fas fa-file-invoice text-xl"></i>
                                </div>
                                <span class="bg-teal-100 text-teal-700 text-xs font-bold px-2 py-1 rounded-lg">{{ $countLapkin }} File</span>
                            </div>
                            <h4 class="font-bold text-slate-700">Laporan Kinerja</h4>
                            <p class="text-xs text-slate-500 mt-1">Lapor realisasi kegiatan secara berkala.</p>
                        </a>

                        <!-- 3. Survailen -->
                        <a href="{{ route('survailen.index') }}" class="group bg-white p-5 rounded-2xl shadow-sm border border-slate-200 hover:border-amber-400 hover:shadow-md transition-all relative overflow-hidden">
                            <div class="flex justify-between items-start mb-4">
                                <div class="p-3 bg-amber-50 text-amber-600 rounded-xl group-hover:scale-110 transition-transform">
                                    <i class="fas fa-clipboard-check text-xl"></i>
                                </div>
                                @if($alertSurvailen > 0)
                                    <span class="bg-rose-100 text-rose-600 text-xs font-bold px-2 py-1 rounded-lg animate-pulse">{{ $alertSurvailen }} Perlu Aksi</span>
                                @else
                                    <span class="bg-slate-100 text-slate-500 text-xs font-bold px-2 py-1 rounded-lg">Aman</span>
                                @endif
                            </div>
                            <h4 class="font-bold text-slate-700">Survailen</h4>
                            <p class="text-xs text-slate-500 mt-1">Tindak lanjut temuan audit pengawasan.</p>
                        </a>

                        <!-- 4. Verifikasi -->
                        <a href="{{ route('verifikasi.index') }}" class="group bg-white p-5 rounded-2xl shadow-sm border border-slate-200 hover:border-purple-400 hover:shadow-md transition-all relative overflow-hidden">
                            <div class="flex justify-between items-start mb-4">
                                <div class="p-3 bg-purple-50 text-purple-600 rounded-xl group-hover:scale-110 transition-transform">
                                    <i class="fas fa-certificate text-xl"></i>
                                </div>
                                @if($alertVerifikasi > 0)
                                    <span class="bg-blue-100 text-blue-600 text-xs font-bold px-2 py-1 rounded-lg">{{ $alertVerifikasi }} Baru</span>
                                @else
                                    <span class="bg-slate-100 text-slate-500 text-xs font-bold px-2 py-1 rounded-lg">Aman</span>
                                @endif
                            </div>
                            <h4 class="font-bold text-slate-700">Verifikasi</h4>
                            <p class="text-xs text-slate-500 mt-1">Dokumen sertifikasi dan SK dari admin.</p>
                        </a>

                    </div>
                </div>

                <!-- RECENT ACTIVITY TABLE -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-white">
                        <h3 class="font-bold text-slate-800 flex items-center gap-2">
                            <i class="fas fa-history text-slate-400"></i> Aktivitas Terbaru
                        </h3>
                        <a href="#" class="text-xs font-bold text-blue-600 hover:text-blue-700 hover:underline">Lihat Semua</a>
                    </div>
                    
                    <div class="overflow-x-auto no-scrollbar">
                        <table class="w-full text-sm text-left text-slate-600 min-w-[700px] md:min-w-0">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-6 py-4 font-bold tracking-wider">Tanggal</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Jenis</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Judul Dokumen</th>
                                    <th class="px-6 py-4 font-bold tracking-wider text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($recentData as $item)
                                <tr class="table-row-hover transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="font-bold text-slate-700">{{ $item->created_at->format('d M Y') }}</span>
                                        <span class="text-xs text-slate-400 ml-1">{{ $item->created_at->format('H:i') }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $badgeClass = match($item->type) {
                                                'KAK' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
                                                'Laporan Kinerja' => 'bg-teal-50 text-teal-700 border-teal-100',
                                                'Survailen' => 'bg-amber-50 text-amber-700 border-amber-100',
                                                'Verifikasi' => 'bg-purple-50 text-purple-700 border-purple-100',
                                                default => 'bg-slate-50 text-slate-700 border-slate-100'
                                            };
                                        @endphp
                                        <span class="{{ $badgeClass }} border text-[10px] font-bold px-2 py-0.5 rounded uppercase">
                                            {{ $item->type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-medium text-slate-800 line-clamp-1" title="{{ $item->title }}">
                                            {{ $item->title }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($item->status == 'approved')
                                            <span class="text-emerald-600 font-bold text-xs flex items-center justify-center gap-1"><i class="fas fa-check-circle"></i> Selesai</span>
                                        @elseif($item->status == 'rejected')
                                            <span class="text-rose-600 font-bold text-xs flex items-center justify-center gap-1"><i class="fas fa-exclamation-circle"></i> Revisi</span>
                                        @elseif($item->type == 'Verifikasi' && !$item->user_note)
                                            <span class="text-purple-600 font-bold text-xs flex items-center justify-center gap-1"><i class="fas fa-bell animate-bounce"></i> Perlu Cek</span>
                                        @elseif($item->type == 'Survailen' && !$item->file_path)
                                            <span class="text-amber-600 font-bold text-xs flex items-center justify-center gap-1"><i class="fas fa-exclamation-circle"></i> Perlu Respon</span>
                                        @else
                                            <span class="text-blue-500 font-bold text-xs flex items-center justify-center gap-1"><i class="fas fa-spinner fa-spin text-[10px]"></i> Proses</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                        <div class="flex flex-col items-center">
                                            <i class="far fa-folder-open text-3xl mb-2 text-slate-200"></i>
                                            <p>Belum ada aktivitas dokumen.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script>
        // === SIDEBAR TOGGLE ===
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            if (sidebar.classList.contains('-translate-x-full')) {
                // Open Sidebar
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            } else {
                // Close Sidebar
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        }
    </script>
</body>
</html>