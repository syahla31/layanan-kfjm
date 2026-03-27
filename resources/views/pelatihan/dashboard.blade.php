<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Lembaga | SI-MUTU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none;  scrollbar-width: none; }
        
        /* Custom subtle shadow */
        .shadow-soft { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.04), 0 4px 6px -2px rgba(0, 0, 0, 0.02); }
        .glass-sidebar { background: #0f172a; } /* Slate 900 */
        
        .transition-all { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        
        /* Pulse for urgency */
        @keyframes subtle-pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        .animate-subtle { animation: subtle-pulse 2s infinite; }
        
        /* Smooth bounce for icons */
        @keyframes bounce-slow {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }
        .animate-bounce-slow { animation: bounce-slow 3s ease-in-out infinite; }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-800 antialiased">

    <!-- DATA FETCHING LOGIC -->
    @php
        use App\Models\Submission;
        use App\Models\KtunDelivery;
        use Illuminate\Support\Facades\Auth;
        use Carbon\Carbon;
        
        $userId = Auth::id();
        $now = Carbon::now();

        // Ambil Data KTUN (Baru)
        $ktunDeliveries = KtunDelivery::where('user_id', $userId)->get();
        $alertKtun = $ktunDeliveries->where('is_survey_filled', false)->count();

        // Ambil Semua Data Submission
        if (!isset($allSubmissions)) {
            $allSubmissions = Submission::where('user_id', $userId)
                                        ->orderBy('created_at', 'desc')
                                        ->get();
        }

        // MENGGABUNGKAN SUBMISSION DENGAN KTUN UNTUK TABEL AKTIVITAS
        $mappedKtun = $ktunDeliveries->map(function($item) {
            // Kita tambahkan properti virtual agar strukturnya sama dengan Submission untuk keperluan looping tabel
            $item->type = 'KTUN';
            $item->title = 'Paket Dokumen Penetapan'; 
            $item->status = $item->is_survey_filled ? 'approved' : 'pending';
            return $item;
        });

        // Gabungkan dan urutkan berdasarkan created_at terbaru
        $mergedActivities = $allSubmissions->concat($mappedKtun)->sortByDesc('created_at');

        $reminders = $allSubmissions->filter(function($item) {
            // Filter dokumen yang butuh perhatian (Survailen/Verifikasi yang belum selesai)
            return in_array($item->type, ['Survailen', 'Verifikasi']) && $item->status != 'approved';
        });

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

        // Ambil 5 data terbaru dari hasil penggabungan
        $recentData = $mergedActivities->take(5);
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
                    <button onclick="toggleSidebar()" class="p-2 text-slate-500 hover:text-blue-600 hover:bg-slate-100 rounded-lg transition-colors">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white shadow-sm">
                            <i class="fas fa-chalkboard-teacher text-sm"></i>
                        </div>
                        <span class="font-bold text-slate-800 text-sm tracking-wide">SI-PELATIHAN</span>
                    </div>
                </div>

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
                <section class="relative group">
                    <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-3xl blur opacity-10 group-hover:opacity-20 transition duration-1000"></div>
                    <div class="relative bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-soft flex flex-col md:flex-row items-center justify-between gap-6 overflow-hidden">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-blue-50 rounded-full -mr-32 -mt-32 mix-blend-multiply filter blur-3xl opacity-50"></div>
                        
                        <div class="relative z-10 space-y-2">
                            <span class="px-3 py-1 bg-blue-50 text-blue-600 text-[10px] font-bold uppercase tracking-wider rounded-full">Status Dashboard</span>
                            <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">
                                Halo, {{ Auth::user()->name ?? 'User' }}! <span class="inline-block animate-bounce-slow">👋</span>
                            </h2>
                            <p class="text-slate-500 text-sm md:text-base max-w-lg leading-relaxed">
                                Selamat datang kembali. Anda memiliki <span class="text-amber-600 font-bold">{{ $reminders->count() }} dokumen</span> yang memerlukan tindak lanjut segera.
                            </p>
                        </div>

                        <div class="relative z-10 flex gap-4 w-full md:w-auto">
                            <div class="flex-1 md:flex-none bg-blue-600 text-white p-4 rounded-2xl shadow-lg shadow-blue-200 flex flex-col items-center justify-center min-w-[120px]">
                                <span class="text-3xl font-black">{{ $stats['total'] }}</span>
                                <span class="text-[10px] font-bold uppercase tracking-widest opacity-80 mt-1">Total Doc</span>
                            </div>
                            <div class="flex-1 md:flex-none bg-emerald-500 text-white p-4 rounded-2xl shadow-lg shadow-emerald-200 flex flex-col items-center justify-center min-w-[120px]">
                                <span class="text-3xl font-black">{{ $stats['approved'] }}</span>
                                <span class="text-[10px] font-bold uppercase tracking-widest opacity-80 mt-1">Selesai</span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- REMINDERS (DOKUMEN PENTING) -->
                @if($reminders->count() > 0)
                <section class="space-y-4">
                    <div class="flex items-center justify-between px-2">
                        <h3 class="text-sm font-black text-slate-500 uppercase tracking-[0.15em] flex items-center gap-3">
                            <span class="flex h-3 w-3 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500"></span>
                            </span>
                            Perlu Perhatian
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @foreach($reminders as $item)
                            @php
                                $dateCreated = Carbon::parse($item->created_at);
                                $deadline = $dateCreated->copy()->addDays(30);
                                $daysRemaining = $now->diffInDays($deadline, false);
                                $isOverdue = $daysRemaining < 0;
                                $colorClass = $isOverdue ? 'rose' : ($daysRemaining <= 7 ? 'amber' : 'blue');
                            @endphp
                            <div class="group bg-white border border-slate-100 rounded-2xl shadow-soft hover:shadow-xl hover:-translate-y-1 transition-all overflow-hidden">
                                <div class="p-5 flex flex-col h-full">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-12 h-12 rounded-xl bg-{{ $colorClass }}-50 text-{{ $colorClass }}-600 flex items-center justify-center text-lg shadow-inner">
                                                <i class="fas {{ $item->type == 'Survailen' ? 'fa-search-plus' : 'fa-clipboard-check' }}"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-slate-800 text-sm group-hover:text-{{ $colorClass }}-600 transition-colors line-clamp-1">{{ $item->title }}</h4>
                                                <span class="text-[10px] font-bold text-{{ $colorClass }}-500 uppercase tracking-tighter">{{ $item->type }}</span>
                                            </div>
                                        </div>
                                        <div class="text-right flex flex-col">
                                            <span class="text-[10px] font-black text-{{ $colorClass }}-600 uppercase">{{ $isOverdue ? 'Terlambat' : 'Sisa Hari' }}</span>
                                            <span class="text-sm font-extrabold text-slate-900">{{ abs($daysRemaining) }} Hari</span>
                                        </div>
                                    </div>

                                    <div class="w-full h-1.5 bg-slate-100 rounded-full mb-4 overflow-hidden">
                                        @php $percent = $isOverdue ? 100 : max(10, ((30 - $daysRemaining) / 30) * 100); @endphp
                                        <div class="h-full bg-{{ $colorClass }}-500 rounded-full transition-all duration-1000" style="width: {{ $percent }}%"></div>
                                    </div>

                                    <div class="mt-auto pt-2 flex items-center justify-between">
                                        <span class="text-[10px] text-slate-400 font-medium">Batas: {{ $deadline->format('d M Y') }}</span>
                                        <a href="{{ $item->type == 'Survailen' ? route('survailen.index') : route('verifikasi.index') }}" class="px-5 py-2 bg-{{ $colorClass }}-600 hover:bg-{{ $colorClass }}-700 text-white text-[10px] font-bold rounded-lg transition-colors flex items-center gap-2 shadow-lg shadow-{{ $colorClass }}-200">
                                            TANGGAPI <i class="fas fa-arrow-right text-[8px]"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
                @endif

                <!-- MENU LAYANAN (QUICK ACCESS) -->
                <section>
                    <div class="flex items-center justify-between mb-6 px-2">
                        <h3 class="text-sm font-black text-slate-500 uppercase tracking-[0.15em] flex items-center gap-3">
                            <i class="fas fa-layer-group text-blue-400"></i> Menu Layanan
                        </h3>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
                        @php
                            $menus = [
                                ['title' => 'Perencanaan (KAK)', 'icon' => 'fa-map', 'color' => 'blue', 'count' => $countKAK, 'desc' => 'Upload kerangka acuan kerja tahunan.', 'route' => url('/pelatihan/kak')],
                                ['title' => 'Laporan Kinerja', 'icon' => 'fa-chart-pie', 'color' => 'emerald', 'count' => $countLapkin, 'desc' => 'Lapor realisasi kegiatan berkala.', 'route' => route('lapkin.index')],
                                ['title' => 'Survailen', 'icon' => 'fa-binoculars', 'color' => 'amber', 'count' => $alertSurvailen, 'desc' => 'Tindak lanjut temuan audit.', 'route' => route('survailen.index')],
                                ['title' => 'Verifikasi', 'icon' => 'fa-certificate', 'color' => 'purple', 'count' => $alertVerifikasi, 'desc' => 'Dokumen sertifikasi dan SK.', 'route' => route('verifikasi.index')],
                                ['title' => 'Dokumen KTUN', 'icon' => 'fa-file-signature', 'color' => 'rose', 'count' => $alertKtun, 'desc' => 'Unduh SK dan dokumen penetapan.', 'route' => route('pelatihan.ktun')],
                            ];
                        @endphp

                        @foreach($menus as $menu)
                        <a href="{{ $menu['route'] }}" class="group relative bg-white p-6 rounded-3xl border border-slate-100 shadow-soft hover:shadow-xl hover:-translate-y-1 transition-all">
                            <div class="absolute top-0 right-0 p-4">
                                <div class="w-8 h-8 rounded-full bg-{{ $menu['color'] }}-50 flex items-center justify-center">
                                    <i class="fas fa-chevron-right text-{{ $menu['color'] }}-400 text-xs group-hover:translate-x-1 transition-transform"></i>
                                </div>
                            </div>
                            <div class="w-14 h-14 rounded-2xl bg-{{ $menu['color'] }}-50 text-{{ $menu['color'] }}-600 flex items-center justify-center text-2xl mb-6 shadow-inner transition-transform group-hover:scale-110 group-hover:rotate-3">
                                <i class="fas {{ $menu['icon'] }}"></i>
                            </div>
                            <h4 class="font-bold text-slate-800 mb-2 group-hover:text-{{ $menu['color'] }}-600 transition-colors text-sm">{{ $menu['title'] }}</h4>
                            <p class="text-[11px] text-slate-500 leading-relaxed mb-4">{{ $menu['desc'] }}</p>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-black px-2.5 py-1 bg-{{ $menu['color'] }}-100 text-{{ $menu['color'] }}-700 rounded-full uppercase tracking-wider">
                                    {{ $menu['count'] }} {{ in_array($menu['color'], ['amber', 'purple', 'rose']) ? 'Butuh Aksi' : 'Berkas' }}
                                </span>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </section>

                <!-- AKTIVITAS TERBARU -->
                <section>
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-soft overflow-hidden">
                        <div class="px-6 md:px-8 py-6 border-b border-slate-50 flex justify-between items-center bg-white">
                            <h3 class="font-bold text-slate-800 flex items-center gap-3">
                                <i class="fas fa-history text-blue-500"></i> Aktivitas Terbaru
                            </h3>
                            <button class="px-4 py-2 text-xs font-bold text-blue-600 hover:bg-blue-50 rounded-xl transition-all">
                                Lihat Semua
                            </button>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left border-collapse">
                                <thead class="hidden md:table-header-group text-[10px] text-slate-400 uppercase font-black bg-slate-50/50">
                                    <tr>
                                        <th class="px-8 py-5 tracking-[0.1em]">Waktu Aktivitas</th>
                                        <th class="px-6 py-5 tracking-[0.1em]">Kategori</th>
                                        <th class="px-6 py-5 tracking-[0.1em]">Detail Dokumen</th>
                                        <th class="px-8 py-5 tracking-[0.1em] text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100/50">
                                    @forelse($recentData as $item)
                                    @php
                                        $catColor = match($item->type) {
                                            'KAK' => 'blue', 'Laporan Kinerja' => 'emerald', 
                                            'Survailen' => 'amber', 'Verifikasi' => 'purple', 
                                            'KTUN' => 'rose', default => 'slate'
                                        };
                                    @endphp
                                    <!-- Zigzag hanya di mobile (odd/even), di desktop solid putih (md:bg-white) -->
                                    <tr class="flex flex-col md:table-row odd:bg-white even:bg-slate-100/60 md:even:bg-white hover:bg-blue-50/70 transition-all duration-200 group p-5 md:p-0">
                                        
                                        <!-- Mobile Card Layout -->
                                        <td class="md:hidden flex justify-between items-start mb-3">
                                            <span class="px-2.5 py-1 bg-{{ $catColor }}-50 text-{{ $catColor }}-600 border border-{{ $catColor }}-100 text-[10px] font-bold rounded-lg uppercase shadow-sm">
                                                {{ $item->type }}
                                            </span>
                                            <div class="inline-block">
                                                @if($item->status == 'approved')
                                                    <span class="text-[11px] font-bold text-emerald-600 flex items-center gap-1.5">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Selesai
                                                    </span>
                                                @elseif($item->status == 'rejected')
                                                    <span class="text-[11px] font-bold text-rose-600 flex items-center gap-1.5">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span> Revisi
                                                    </span>
                                                @else
                                                    <span class="text-[11px] font-bold text-blue-600 flex items-center gap-1.5">
                                                        <i class="fas fa-circle-notch fa-spin text-[10px]"></i> Proses
                                                    </span>
                                                @endif
                                            </div>
                                        </td>

                                        <td class="md:hidden mb-4">
                                            <h4 class="font-bold text-slate-800 text-base leading-snug group-hover:text-blue-600 transition-colors">
                                                {{ $item->title }}
                                            </h4>
                                        </td>

                                        <!-- Desktop Columns -->
                                        <td class="md:px-8 md:py-5 flex items-center md:table-cell order-last md:order-first">
                                            <div class="flex items-center md:flex-col gap-2 md:gap-0 text-slate-400 md:text-inherit">
                                                <i class="far fa-clock md:hidden text-xs"></i>
                                                <span class="font-bold text-slate-700 text-xs md:text-sm">{{ $item->created_at->format('d M Y') }}</span>
                                                <span class="text-[10px] text-slate-400 md:block ml-1 md:ml-0">{{ $item->created_at->format('H:i') }} WIB</span>
                                            </div>
                                        </td>

                                        <td class="hidden md:table-cell md:px-6 md:py-5">
                                            <span class="px-2.5 py-1 bg-{{ $catColor }}-50 text-{{ $catColor }}-600 border border-{{ $catColor }}-100 text-[10px] font-bold rounded-lg uppercase shadow-sm">
                                                {{ $item->type }}
                                            </span>
                                        </td>

                                        <td class="hidden md:table-cell md:px-6 md:py-5">
                                            <span class="font-semibold text-slate-600 line-clamp-1 max-w-xs group-hover:translate-x-1 transition-transform inline-block" title="{{ $item->title }}">
                                                {{ $item->title }}
                                            </span>
                                        </td>

                                        <td class="hidden md:table-cell md:px-8 md:py-5 text-center">
                                            <div class="inline-block">
                                                @if($item->status == 'approved')
                                                    <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100 shadow-sm">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> {{ $item->type == 'KTUN' ? 'Terbuka' : 'Disetujui' }}
                                                    </span>
                                                @elseif($item->status == 'rejected')
                                                    <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-[11px] font-bold bg-rose-50 text-rose-600 border border-rose-100 shadow-sm">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span> Revisi
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-[11px] font-bold bg-blue-50 text-blue-600 border border-blue-100 shadow-sm">
                                                        <i class="fas {{ $item->type == 'KTUN' ? 'fa-lock' : 'fa-circle-notch fa-spin' }} text-[10px]"></i> Proses
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="px-8 py-20 text-center text-slate-400">
                                            <i class="fas fa-folder-open text-4xl mb-3 opacity-20"></i>
                                            <p class="font-bold">Belum ada aktivitas</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- FOOTER -->
                <div class="mt-8 text-center text-xs text-slate-400">
                    &copy; 2026 Sistem Informasi Jaminan Mutu Ketenaganukliran
                </div>

            </main>
        </div>
    </div>

    <!-- SIDEBAR SCRIPTS -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        }
    </script>
</body>
</html>