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
                        indigoMain: '#4f46e5',
                    }
                }
            }
        }
    </script>
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .modal-backdrop-blur { backdrop-filter: blur(4px); background-color: rgba(15, 23, 42, 0.6); }
        .card-stat-hover:hover { transform: translateY(-4px); transition: all 0.3s ease; }
        .banner-gradient { background: linear-gradient(135deg, #0f172a 0%, #0f766e 50%, #1e1b4b 100%); }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-800 antialiased selection:bg-teal-100 selection:text-teal-900">

    @php
        use App\Models\Submission;
        use Illuminate\Support\Facades\Auth;

        // Fetch data ringkasan untuk user yang login dengan kategori 'uji'
        $allSubmissions = Submission::where('user_id', Auth::id())
                            ->where('category', 'uji')
                            ->get();
        
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

    <div class="flex h-screen overflow-hidden">
        <!-- SIDEBAR -->
        @include('components.uji-sidebar')

        <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
            
            <!-- HEADER -->
            @include('components.uji-header', [
                'title' => 'Dashboard Utama',
                'subtitle' => 'Ringkasan data real-time Lembaga Uji Anda'
            ])

            <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-8 space-y-6 md:space-y-8 no-scrollbar">
                
                <!-- WELCOME BANNER (FIXED STRUCTURE) -->
                <div class="banner-gradient rounded-[2rem] p-6 md:p-10 text-white relative overflow-hidden shadow-xl animate-fade-in">
                    <div class="absolute right-0 top-0 p-4 opacity-10 pointer-events-none hidden lg:block">
                        <i class="fas fa-microscope text-[12rem] rotate-12"></i>
                    </div>
                    
                    <div class="relative z-10 max-w-2xl text-left">
                        <span class="inline-block bg-teal-400/20 text-teal-300 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border border-teal-400/30 mb-4">
                            Sistem Informasi Jaminan Mutu
                        </span>
                        <h2 class="text-2xl md:text-4xl font-extrabold mb-4 leading-tight">Kelola Seluruh Berkas <br class="hidden md:block">Dalam Satu Dashboard.</h2>
                        <p class="text-teal-50/70 leading-relaxed mb-8 text-xs md:text-sm font-medium">
                            Pantau progres laporan tahunan, respon instruksi survailen, dan cek status verifikasi penunjukan secara langsung dan transparan melalui integrasi pangkalan data DKKN.
                        </p>
                        
                        <div class="flex flex-wrap gap-3 md:gap-4">
                            <a href="{{ url('/uji/laporan') }}" class="bg-white text-teal-900 px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-teal-50 transition-all flex items-center gap-2 shadow-lg active:scale-95">
                                <i class="fas fa-file-upload text-teal-600"></i> Kelola Laporan
                            </a>
                            <a href="{{ url('/uji/survailen') }}" class="bg-teal-800/40 backdrop-blur-md border border-teal-400/30 text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-teal-800 transition-all flex items-center gap-2 active:scale-95">
                                <i class="fas fa-search-location"></i> Respon Survailen
                            </a>
                        </div>
                    </div>
                </div>

                <!-- STATS & MAIN CONTENT WRAPPER -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-8">
                    
                    <!-- LEFT COLUMN: STATS & CHARTS (8 cols) -->
                    <div class="lg:col-span-8 space-y-6 md:space-y-8">
                        
                        <!-- SUMMARY CARDS (3 PILLARS) -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-6">
                            <!-- Card 1 -->
                            <div class="bg-white p-5 rounded-[1.5rem] border border-slate-200 shadow-sm transition-all card-stat-hover flex items-center gap-4 text-left">
                                <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center border border-teal-100 shrink-0">
                                    <i class="fas fa-file-invoice text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none">Laporan</p>
                                    <h3 class="text-2xl font-black text-slate-800 mt-1">{{ $countLapkin }}</h3>
                                    <p class="text-[9px] text-slate-400 font-bold uppercase mt-0.5">Total Dokumen</p>
                                </div>
                            </div>

                            <!-- Card 2 -->
                            <div class="bg-white p-5 rounded-[1.5rem] border border-slate-200 shadow-sm transition-all card-stat-hover flex items-center gap-4 text-left">
                                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100 shrink-0">
                                    <i class="fas fa-tasks text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none">Survailen</p>
                                    <h3 class="text-2xl font-black text-slate-800 mt-1">{{ $countSurvailenAction }}</h3>
                                    <p class="text-[9px] text-indigo-500 font-bold uppercase mt-0.5">Butuh Aksi</p>
                                </div>
                            </div>

                            <!-- Card 3 -->
                            <div class="bg-white p-5 rounded-[1.5rem] border border-slate-200 shadow-sm transition-all card-stat-hover flex items-center gap-4 text-left">
                                <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center border border-rose-100 shrink-0">
                                    <i class="fas fa-certificate text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none">Verifikasi</p>
                                    <h3 class="text-2xl font-black text-rose-600 mt-1">{{ $countVerifikasiAction }}</h3>
                                    <p class="text-[9px] text-rose-400 font-bold uppercase mt-0.5">Perlu Revisi</p>
                                </div>
                            </div>
                        </div>

                        <!-- PERFORMANCE CHART -->
                        <div class="bg-white p-6 rounded-[1.5rem] border border-slate-200 shadow-sm text-left">
                            <div class="flex justify-between items-center mb-8">
                                <div>
                                    <h3 class="font-bold text-slate-800 text-base md:text-lg">Grafik Aktivitas</h3>
                                    <p class="text-xs text-slate-400 font-medium">Monitoring pengiriman berkas rutin</p>
                                </div>
                                <span class="bg-teal-50 text-teal-600 px-3 py-1 rounded-lg text-[9px] font-black uppercase border border-teal-100">Live Data</span>
                            </div>
                            <div id="performanceChart" class="w-full"></div>
                        </div>

                    </div>

                    <!-- RIGHT COLUMN: ACTIVITIES (4 cols) -->
                    <div class="lg:col-span-4 space-y-6 md:space-y-8">
                        
                        <!-- RECENT ACTIVITY LIST -->
                        <div class="bg-white rounded-[1.5rem] border border-slate-200 shadow-sm flex flex-col text-left overflow-hidden">
                            <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center">
                                <h3 class="font-bold text-slate-800 text-sm">Aktivitas Terkini</h3>
                                <i class="fas fa-history text-slate-300 text-xs"></i>
                            </div>
                            <div class="p-2 space-y-1">
                                @forelse($recentActivities as $act)
                                <div class="p-3 flex gap-4 hover:bg-slate-50 rounded-xl transition-all group">
                                    <div class="shrink-0">
                                        @php
                                            $icon = match(true) {
                                                str_contains(strtolower($act->type), 'laporan') => ['fa-file-invoice', 'bg-teal-50 text-teal-600'],
                                                $act->type == 'Survailen' => ['fa-search-location', 'bg-indigo-50 text-indigo-600'],
                                                default => ['fa-certificate', 'bg-amber-50 text-amber-600']
                                            };
                                        @endphp
                                        <div class="w-9 h-9 rounded-lg {{ $icon[1] }} flex items-center justify-center group-hover:scale-110 transition-transform">
                                            <i class="fas {{ $icon[0] }} text-xs"></i>
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-[11px] font-bold text-slate-800 truncate">{{ $act->title }}</p>
                                        <div class="flex items-center justify-between mt-0.5">
                                            <span class="text-[9px] text-slate-400">{{ $act->updated_at->diffForHumans() }}</span>
                                            <span class="text-[8px] font-black uppercase px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 tracking-tighter">{{ $act->status }}</span>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="py-12 text-center opacity-30">
                                    <i class="fas fa-inbox text-3xl mb-2"></i>
                                    <p class="text-[10px] font-black uppercase tracking-widest">Kosong</p>
                                </div>
                                @endforelse
                            </div>
                            <div class="p-4 bg-slate-50 border-t border-slate-100">
                                <a href="{{ url('/uji/laporan') }}" class="text-[9px] font-black text-indigo-600 uppercase tracking-widest flex items-center justify-center gap-2 hover:gap-3 transition-all">
                                    Kelola Berkas <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>

                        <!-- HELP CARD (BETTER SPACING) -->
                        <div class="bg-indigo-600 rounded-[1.5rem] p-6 text-white relative overflow-hidden shadow-lg shadow-indigo-200 text-left">
                            <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                            <div class="relative z-10">
                                <h5 class="font-bold text-sm mb-2 flex items-center gap-2">
                                    <i class="fas fa-headset text-indigo-200"></i> Bantuan Teknis
                                </h5>
                                <p class="text-[11px] text-indigo-100/80 leading-relaxed mb-4 font-medium">
                                    Jika terdapat kendala pengunggahan atau data tidak muncul, segera hubungi Admin Unit DKKN.
                                </p>
                                <button class="w-full py-2 bg-white/10 hover:bg-white text-white hover:text-indigo-600 border border-white/20 hover:border-white rounded-xl text-[9px] font-black uppercase tracking-widest transition-all">
                                    Hubungi Admin
                                </button>
                            </div>
                        </div>

                    </div>

                </div>

                <!-- FOOTER -->
                <div class="pt-8 pb-4 text-center opacity-30">
                    <p class="text-[9px] font-black uppercase tracking-[0.4em]">&copy; 2026 SI-MUTU DKKN | BAPETEN</p>
                </div>

            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var options = {
                series: [{
                    name: 'Dokumen',
                    data: @json($chartData)
                }],
                chart: {
                    type: 'area',
                    height: 250,
                    toolbar: { show: false },
                    fontFamily: 'Plus Jakarta Sans, sans-serif',
                },
                colors: ['#0f766e'],
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2.5 },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.35,
                        opacityTo: 0.02,
                        stops: [0, 90, 100]
                    }
                },
                xaxis: {
                    categories: ['5 bln lalu', '4 bln lalu', '3 bln lalu', '2 bln lalu', 'Bulan lalu', 'Sekarang'],
                    labels: { style: { colors: '#94a3b8', fontWeight: 600, fontSize: '9px' } },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    labels: { style: { colors: '#94a3b8', fontWeight: 600, fontSize: '9px' } }
                },
                grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
                tooltip: { theme: 'light' }
            };

            var chart = new ApexCharts(document.querySelector("#performanceChart"), options);
            chart.render();
        });

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        }

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