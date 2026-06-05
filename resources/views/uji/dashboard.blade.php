<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Lembaga Uji | SI-MUTU Pro</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: {
                        primary: '#0f766e',
                        tealDark: '#115e59',
                        indigoMain: '#4f46e5',
                    },
                    boxShadow: {
                        'premium': '0 10px 30px -5px rgba(0, 0, 0, 0.03), 0 1px 3px -1px rgba(0, 0, 0, 0.02)',
                        'card-hover': '0 20px 40px -5px rgba(15, 118, 110, 0.07), 0 1px 3px -1px rgba(0, 0, 0, 0.02)'
                    }
                }
            }
        }
    </script>
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .modal-backdrop-blur { backdrop-filter: blur(4px); background-color: rgba(15, 23, 42, 0.6); }
        .card-stat-hover { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .card-stat-hover:hover { transform: translateY(-6px); }
        .banner-gradient { background: linear-gradient(135deg, #0f172a 0%, #115e59 60%, #1e1b4b 100%); }
        .glass-overlay { background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(6px); }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-teal-100 selection:text-teal-900 font-sans">

    @php
        use App\Models\Submission;
        use Illuminate\Support\Facades\Auth;
        use Carbon\Carbon;

        $now = Carbon::now();

        // Fetch data ringkasan untuk user yang login dengan kategori 'uji'
        $allSubmissions = collect(); // Default empty collection untuk safety view
        try {
            $allSubmissions = Submission::where('user_id', Auth::id())
                                ->where('category', 'uji')
                                ->get();
        } catch(\Exception $e) {}
        
        // 1. Laporan Tahunan
        $lapkinSubmissions = $allSubmissions->filter(fn($s) => str_contains(strtolower($s->type), 'laporan'));
        
        // 2. Survailen
        $survailenSubmissions = $allSubmissions->where('type', 'Survailen');
        
        // 3. Verifikasi
        $verifikasiSubmissions = $allSubmissions->where('type', 'Verifikasi');

        // Statistik
        $countLapkin = $lapkinSubmissions->count();
        $countSurvailenAction = $survailenSubmissions->where('status', 'pending')->count();
        $countVerifikasiAction = $verifikasiSubmissions->where('status', 'rejected')->count();
        
        // Aktivitas Terbaru
        $recentActivities = $allSubmissions->sortByDesc('updated_at')->take(5);

        // Pengingat (Survailen & Verifikasi yang belum disetujui)
        $reminders = $allSubmissions->filter(function($item) {
            return in_array($item->type, ['Survailen', 'Verifikasi']) && $item->status != 'approved';
        });

        // Chart Data
        $chartData = [
            $allSubmissions->whereBetween('created_at', [now()->subMonths(5)->startOfMonth(), now()->subMonths(5)->endOfMonth()])->count(),
            $allSubmissions->whereBetween('created_at', [now()->subMonths(4)->startOfMonth(), now()->subMonths(4)->endOfMonth()])->count(),
            $allSubmissions->whereBetween('created_at', [now()->subMonths(3)->startOfMonth(), now()->subMonths(3)->endOfMonth()])->count(),
            $allSubmissions->whereBetween('created_at', [now()->subMonths(2)->startOfMonth(), now()->subMonths(2)->endOfMonth()])->count(),
            $allSubmissions->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])->count(),
            $allSubmissions->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
        ];
    @endphp

    <div class="flex h-screen overflow-hidden bg-slate-50">
        
        <div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 z-40 hidden lg:hidden glass-overlay transition-opacity duration-300 opacity-0"></div>

        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-2xl lg:shadow-none transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto flex flex-col h-full border-r border-slate-200/80">
            @include('components.uji-sidebar')
        </aside>

        <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
            
            <div class="lg:hidden bg-white/90 backdrop-blur-md border-b border-slate-200/80 px-5 py-3.5 flex items-center justify-between z-20 sticky top-0 shadow-sm">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="p-2 -ml-2 text-slate-500 hover:text-primary hover:bg-slate-100 rounded-xl transition-colors focus:outline-none">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                    
                    <div class="flex items-center gap-2.5">
                        <div class="w-8.5 h-8.5 rounded-xl bg-primary flex items-center justify-center text-white shadow-md shadow-teal-700/20">
                            <i class="fas fa-flask text-xs"></i>
                        </div>
                        <span class="font-extrabold text-slate-800 text-sm tracking-wide">SI-LAB UJI</span>
                    </div>
                </div>

                <div class="w-8.5 h-8.5 rounded-full bg-teal-50 flex items-center justify-center text-primary text-xs font-bold border border-teal-200/60 ring-2 ring-teal-500/10">
                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                </div>
            </div>

            <div class="hidden lg:block sticky top-0 z-10 bg-white/80 backdrop-blur-md border-b border-slate-200/80">
                @include('components.uji-header', [
                    'title' => 'Dashboard Utama',
                    'subtitle' => 'Ringkasan data real-time Lembaga Uji Anda'
                ])
            </div>

            <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-8 space-y-6 md:space-y-8 no-scrollbar scroll-smooth">
                
                <div class="banner-gradient rounded-[2rem] p-6 md:p-10 text-white relative overflow-hidden shadow-xl shadow-teal-950/10">
                    <div class="absolute right-0 top-0 p-4 opacity-10 pointer-events-none hidden lg:block">
                        <i class="fas fa-microscope text-[14rem] rotate-12 translate-x-10 -translate-y-10"></i>
                    </div>
                    
                    <div class="relative z-10 max-w-2xl text-left">
                        <span class="inline-block bg-teal-400/20 text-teal-200 px-3.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border border-teal-400/30 mb-5">
                            Sistem Informasi Jaminan Mutu
                        </span>
                        <h2 class="text-2xl md:text-4xl font-extrabold mb-4 leading-snug tracking-tight">Kelola Seluruh Berkas <br class="hidden md:block">Dalam Satu Dashboard.</h2>
                        <p class="text-teal-100/80 leading-relaxed mb-8 text-xs md:text-sm font-normal max-w-xl">
                            Pantau progres laporan tahunan, respon instruksi survailen, dan cek status verifikasi penunjukan secara langsung dan transparan melalui integrasi pangkalan data DKKN.
                        </p>
                        
                        <div class="flex flex-wrap gap-3.5">
                            <a href="{{ url('/uji/laporan') }}" class="bg-white text-teal-950 px-6 py-3 rounded-xl font-semibold text-xs hover:bg-teal-50 transition-all flex items-center gap-2 shadow-md shadow-slate-900/10 active:scale-95">
                                <i class="fas fa-file-upload text-primary text-sm"></i> Kelola Laporan
                            </a>
                            <a href="{{ url('/uji/survailen') }}" class="bg-white/10 backdrop-blur-md border border-white/20 text-white px-6 py-3 rounded-xl font-semibold text-xs hover:bg-white/20 transition-all flex items-center gap-2 active:scale-95">
                                <i class="fas fa-search-location text-sm"></i> Respon Survailen
                            </a>
                        </div>
                    </div>
                </div>

                @if($reminders->count() > 0)
                <section class="space-y-4">
                    <div class="flex items-center justify-between px-1">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2.5">
                            <span class="flex h-2.5 w-2.5 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span>
                            </span>
                            Perlu Perhatian Segera
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @foreach($reminders as $item)
                            @php
                                $dateCreated = Carbon::parse($item->created_at);
                                $deadline = $dateCreated->copy()->addDays(30);
                                $daysRemaining = $now->diffInDays($deadline, false);
                                $isOverdue = $daysRemaining < 0;
                                
                                // Mapping aman kelas warna Tailwind agar JIT tidak miss-compile
                                $colorMap = [
                                    'rose' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-600', 'btn' => 'bg-rose-600 hover:bg-rose-700', 'shadow' => 'shadow-rose-100', 'bar' => 'bg-rose-500', 'border' => 'border-rose-100'],
                                    'amber' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'btn' => 'bg-amber-600 hover:bg-amber-700', 'shadow' => 'shadow-amber-100', 'bar' => 'bg-amber-500', 'border' => 'border-amber-100'],
                                    'blue' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'btn' => 'bg-blue-600 hover:bg-blue-700', 'shadow' => 'shadow-blue-100', 'bar' => 'bg-blue-500', 'border' => 'border-blue-100']
                                ];
                                $cType = $isOverdue ? 'rose' : ($daysRemaining <= 7 ? 'amber' : 'blue');
                                $cls = $colorMap[$cType];
                            @endphp
                            <div class="group bg-white border border-slate-200/70 rounded-2xl shadow-premium hover:shadow-card-hover hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                                <div class="p-5 flex flex-col h-full">
                                    <div class="flex justify-between items-start mb-4 gap-4">
                                        <div class="flex items-center gap-3.5 min-w-0">
                                            <div class="w-12 h-12 rounded-xl {{ $cls['bg'] }} {{ $cls['text'] }} flex items-center justify-center text-lg shrink-0 border {{ $cls['border'] }}">
                                                <i class="fas {{ $item->type == 'Survailen' ? 'fa-search-plus' : 'fa-clipboard-check' }}"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <h4 class="font-bold text-slate-800 text-sm group-hover:text-primary transition-colors line-clamp-1">{{ $item->title }}</h4>
                                                <span class="text-[10px] font-extrabold uppercase tracking-wider {{ $cls['text'] }}">{{ $item->type }}</span>
                                            </div>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ $isOverdue ? 'Terlambat' : 'Sisa Waktu' }}</span>
                                            <span class="text-sm font-extrabold text-slate-900">{{ abs($daysRemaining) }} Hari</span>
                                        </div>
                                    </div>

                                    <div class="w-full h-2 bg-slate-100 rounded-full mb-4 overflow-hidden">
                                        @php $percent = $isOverdue ? 100 : max(10, ((30 - $daysRemaining) / 30) * 100); @endphp
                                        <div class="h-full {{ $cls['bar'] }} rounded-full transition-all duration-1000" style="width: {{ $percent }}%"></div>
                                    </div>

                                    <div class="mt-auto pt-2 flex items-center justify-between border-t border-slate-50">
                                        <span class="text-xs text-slate-400 font-medium">Batas: {{ $deadline->format('d M Y') }}</span>
                                        <a href="{{ $item->type == 'Survailen' ? route('survailen.uji.index') : route('uji.verifikasi') }}" class="px-4 py-2 {{ $cls['btn'] }} text-white text-xs font-semibold rounded-xl transition-colors flex items-center gap-2 shadow-md {{ $cls['shadow'] }}">
                                            Tanggapi <i class="fas fa-arrow-right text-[10px]"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 md:grid-cols-1">
                    
                    <div class="lg:col-span-8 space-y-6 md:space-y-8">
                        
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                            <div class="bg-white p-5 rounded-2xl border border-slate-200/70 shadow-premium card-stat-hover hover:shadow-card-hover flex items-center gap-4 text-left">
                                <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center border border-teal-100/50 shrink-0">
                                    <i class="fas fa-file-invoice text-xl"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none">Laporan</p>
                                    <h3 class="text-2xl font-extrabold text-slate-800 mt-1.5 leading-none">{{ $countLapkin }}</h3>
                                    <p class="text-[10px] text-slate-400 font-semibold uppercase mt-1">Total Berkas</p>
                                </div>
                            </div>

                            <div class="bg-white p-5 rounded-2xl border border-slate-200/70 shadow-premium card-stat-hover hover:shadow-card-hover flex items-center gap-4 text-left">
                                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100/50 shrink-0">
                                    <i class="fas fa-tasks text-xl"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none">Survailen</p>
                                    <h3 class="text-2xl font-extrabold text-slate-800 mt-1.5 leading-none">{{ $countSurvailenAction }}</h3>
                                    <p class="text-[10px] text-indigo-500 font-semibold uppercase mt-1">Butuh Aksi</p>
                                </div>
                            </div>

                            <div class="bg-white p-5 rounded-2xl border border-slate-200/70 shadow-premium card-stat-hover hover:shadow-card-hover flex items-center gap-4 text-left">
                                <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center border border-rose-100/50 shrink-0">
                                    <i class="fas fa-certificate text-xl"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none">Verifikasi</p>
                                    <h3 class="text-2xl font-extrabold text-rose-600 mt-1.5 leading-none">{{ $countVerifikasiAction }}</h3>
                                    <p class="text-[10px] text-rose-400 font-semibold uppercase mt-1">Perlu Revisi</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-2xl border border-slate-200/70 shadow-premium text-left">
                            <div class="flex justify-between items-center mb-6">
                                <div>
                                    <h3 class="font-bold text-slate-800 text-base md:text-lg">Grafik Aktivitas Berkas</h3>
                                    <p class="text-xs text-slate-400 font-medium">Monitoring tren pengiriman berkas rutin</p>
                                </div>
                                <span class="bg-teal-50 text-teal-700 px-3 py-1 rounded-lg text-[10px] font-bold uppercase border border-teal-100/70 tracking-wider">Live</span>
                            </div>
                            <div id="performanceChart" class="w-full"></div>
                        </div>

                    </div>

                    <div class="lg:col-span-4 space-y-6 md:space-y-8">
                        
                        <div class="bg-white rounded-2xl border border-slate-200/70 shadow-premium flex flex-col text-left overflow-hidden">
                            <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                                <h3 class="font-bold text-slate-800 text-sm">Aktivitas Terkini</h3>
                                <i class="fas fa-history text-slate-400 text-xs"></i>
                            </div>
                            <div class="p-3 space-y-1">
                                @forelse($recentActivities as $act)
                                <div class="p-3 flex gap-3.5 hover:bg-slate-50 rounded-xl transition-all group border border-transparent hover:border-slate-100">
                                    <div class="shrink-0">
                                        @php
                                            $icon = match(true) {
                                                str_contains(strtolower($act->type), 'laporan') => ['fa-file-invoice', 'bg-teal-50 text-teal-600 border-teal-100/50'],
                                                $act->type == 'Survailen' => ['fa-search-location', 'bg-indigo-50 text-indigo-600 border-indigo-100/50'],
                                                default => ['fa-certificate', 'bg-amber-50 text-amber-600 border-amber-100/50']
                                            };
                                        @endphp
                                        <div class="w-9 h-9 rounded-lg {{ $icon[1] }} flex items-center justify-center group-hover:scale-105 transition-transform border">
                                            <i class="fas {{ $icon[0] }} text-xs"></i>
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-bold text-slate-800 truncate">{{ $act->title }}</p>
                                        <div class="flex items-center justify-between mt-1 gap-2">
                                            <span class="text-[11px] text-slate-400 font-medium">{{ $act->updated_at->diffForHumans() }}</span>
                                            <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 border border-slate-200/40 tracking-tight">{{ $act->status }}</span>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="py-12 text-center text-slate-300">
                                    <i class="fas fa-inbox text-4xl mb-2.5 opacity-40"></i>
                                    <p class="text-xs font-bold uppercase tracking-widest opacity-60">Belum Ada Aktivitas</p>
                                </div>
                                @endforelse
                            </div>
                            <div class="p-4 bg-slate-50/50 border-t border-slate-100 text-center">
                                <a href="{{ url('/uji/laporan') }}" class="text-xs font-bold text-indigo-600 uppercase tracking-wider flex items-center justify-center gap-1.5 hover:gap-2.5 transition-all">
                                    Kelola Berkas Lengkap <i class="fas fa-arrow-right text-[10px]"></i>
                                </a>
                            </div>
                        </div>

                        <div class="bg-indigo-900 rounded-2xl p-6 text-white relative overflow-hidden shadow-lg shadow-indigo-950/10 text-left">
                            <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
                            <div class="relative z-10">
                                <h5 class="font-bold text-sm mb-2 flex items-center gap-2">
                                    <i class="fas fa-headset text-indigo-300"></i> Bantuan Teknis
                                </h5>
                                <p class="text-xs text-indigo-100/80 leading-relaxed mb-5 font-normal">
                                    Jika terdapat kendala pengunggahan atau ketidaksesuaian data, silakan hubungi tim administrator Unit DKKN.
                                </p>
                                <button class="w-full py-2.5 bg-white/10 hover:bg-white text-white hover:text-indigo-950 border border-white/15 hover:border-white rounded-xl text-xs font-semibold tracking-wide transition-all active:scale-[0.98]">
                                    Hubungi Admin
                                </button>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="mt-12 text-center text-xs text-slate-400 font-medium tracking-wide">
                    &copy; 2026 Sistem Informasi Jaminan Mutu Ketenaganukliran
                </div>
                
            </main>
        </div>
    </div>

    <script>
        // === LOGIKA SIDEBAR RESPONSIVE ===
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            if (sidebar.classList.contains('-translate-x-full')) {
                // Buka Sidebar
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                setTimeout(() => {
                    overlay.classList.remove('opacity-0');
                    overlay.classList.add('opacity-100');
                }, 10);
            } else {
                // Tutup Sidebar
                sidebar.classList.add('-translate-x-full');
                overlay.classList.remove('opacity-100');
                overlay.classList.add('opacity-0');
                setTimeout(() => {
                    overlay.classList.add('hidden');
                }, 300);
            }
        }

        // Script chart
        document.addEventListener('DOMContentLoaded', function () {
            var options = {
                series: [{
                    name: 'Dokumen',
                    data: @json($chartData)
                }],
                chart: {
                    type: 'area',
                    height: 260,
                    toolbar: { show: false },
                    fontFamily: 'Plus Jakarta Sans, sans-serif',
                    sparkline: { enabled: false }
                },
                colors: ['#0f766e'],
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 3 },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.3,
                        opacityTo: 0.01,
                        stops: [0, 95, 100]
                    }
                },
                xaxis: {
                    categories: ['5 bln lalu', '4 bln lalu', '3 bln lalu', '2 bln lalu', 'Bulan lalu', 'Sekarang'],
                    labels: { style: { colors: '#94a3b8', fontWeight: 600, fontSize: '10px' } },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    labels: { style: { colors: '#94a3b8', fontWeight: 600, fontSize: '10px' } }
                },
                grid: { borderColor: '#f1f5f9', strokeDashArray: 5, padding: { top: 10, bottom: 0 } },
                tooltip: { theme: 'light' }
            };

            var chart = new ApexCharts(document.querySelector("#performanceChart"), options);
            chart.render();
        });

        function closeNotification(id) {
            const modal = document.getElementById(id);
            if(modal) {
                modal.style.opacity = '0';
                setTimeout(() => { modal.classList.add('hidden'); }, 300);
            }
        }
    </script>
</body>
</html>