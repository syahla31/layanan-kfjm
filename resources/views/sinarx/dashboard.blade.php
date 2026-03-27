<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Sinar-X | SI-MUTU</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif']
                    },
                    colors: {
                        primary: '#ea580c',
                        orangeMain: '#c2410c',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        @keyframes modalShow {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(10px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .animate-modal {
            animation: modalShow 0.25s ease-out forwards;
        }

        .animate-pulse-slow {
            animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        .banner-gradient {
            background: linear-gradient(135deg, #431407 0%, #ea580c 50%, #7c2d12 100%);
        }

        .glass-overlay {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
        }

        /* Notif Indicator Animation */
        .notif-ping {
            position: absolute;
            top: 0;
            right: 0;
            height: 9px;
            width: 9px;
            background-color: #ef4444;
            border-radius: 9999px;
            border: 2px solid white;
            animation: notifPulse 1.5s ease-in-out infinite;
        }

        @keyframes notifPulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.3);
                opacity: 0.8;
            }
        }

        .bell-unread {
            color: #ea580c !important;
        }

        .bell-read {
            color: #94a3b8 !important;
        }

        .dropdown-shadow {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04), 0 0 0 1px rgba(0, 0, 0, 0.05);
        }

        /* Hover Interaction Effects for Stats Cards */
        .stat-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body
    class="bg-slate-50 text-slate-800 antialiased selection:bg-orange-100 selection:text-orange-900 overflow-hidden text-left">

    @php
        use App\Models\SinarxSubmission;
        use Illuminate\Support\Facades\Auth;

        $mySubmissions = collect();
        try {
            $mySubmissions = SinarxSubmission::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get();
        } catch (\Exception $e) {
        }

        $myPending = $mySubmissions->where('status', 'pending')->count();
        $myApproved = $mySubmissions->where('status', 'approved')->count();
        $myRejected = $mySubmissions->where('status', 'rejected')->count();

        $recentNotifications = $mySubmissions->where('status', 'approved')->take(5);

        $chartData = [
            $mySubmissions
                ->whereBetween('created_at', [now()->subMonths(5)->startOfMonth(), now()->subMonths(5)->endOfMonth()])
                ->count(),
            $mySubmissions
                ->whereBetween('created_at', [now()->subMonths(4)->startOfMonth(), now()->subMonths(4)->endOfMonth()])
                ->count(),
            $mySubmissions
                ->whereBetween('created_at', [now()->subMonths(3)->startOfMonth(), now()->subMonths(3)->endOfMonth()])
                ->count(),
            $mySubmissions
                ->whereBetween('created_at', [now()->subMonths(2)->startOfMonth(), now()->subMonths(2)->endOfMonth()])
                ->count(),
            $mySubmissions
                ->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
                ->count(),
            $mySubmissions->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
        ];
    @endphp

    <div class="flex h-screen overflow-hidden bg-slate-50 relative text-left">

        <div id="sidebarOverlay" onclick="toggleSidebar()"
            class="fixed inset-0 z-40 hidden lg:hidden glass-overlay transition-opacity duration-300 opacity-0"></div>

        <aside id="sidebar"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-2xl lg:shadow-none transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto flex flex-col h-full border-r border-slate-200">
            @include('components.sinarx-sidebar')
        </aside>

        <div class="flex-1 flex flex-col h-screen overflow-hidden relative w-full text-left">

            <div
                class="lg:hidden bg-white/90 backdrop-blur-md border-b border-slate-200 px-4 py-3 flex items-center justify-between z-20 sticky top-0 shadow-sm">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()"
                        class="p-2 -ml-2 text-slate-500 hover:text-orange-600 rounded-lg focus:outline-none">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <span class="font-bold text-slate-800 text-sm tracking-wide text-left">SI-SINAR X</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <button onclick="toggleMobileNotif()" id="mobileNotifBtn"
                            class="relative w-8 h-8 flex items-center justify-center text-slate-400">
                            <i class="fas fa-bell" id="mobileBellIcon"></i>
                            <span id="mobileNotifBadge" class="hidden notif-ping"></span>
                        </button>
                        <!-- Mobile Notif Dropdown -->
                        <div id="mobileNotifDropdown"
                            class="hidden absolute right-0 mt-3 w-[88vw] max-w-sm bg-white rounded-2xl dropdown-shadow overflow-hidden z-[100] border border-slate-100 animate-modal"
                            style="right: -2.5rem;">
                            <div class="p-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                                <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400">Update BALIS
                                </h4>
                                <button onclick="toggleMobileNotif()" class="text-slate-400 text-xs"><i
                                        class="fas fa-times"></i></button>
                            </div>
                            <div class="max-h-[60vh] overflow-y-auto no-scrollbar bg-white" id="mobileNotifContent">
                                <!-- diisi JS dari desktop dropdown -->
                            </div>
                        </div>
                    </div>
                    <div
                        class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 text-xs font-bold border border-orange-200">
                        {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                    </div>
                </div>
            </div>

            <div
                class="hidden lg:flex items-center justify-between sticky top-0 z-30 bg-white/80 backdrop-blur-md border-b border-slate-200 px-8 py-4 text-left">
                <div class="text-left">
                    <h1 class="text-xl font-black text-slate-800 tracking-tight text-left">Dashboard Unit</h1>
                    <p class="text-xs text-slate-400 font-medium text-left">Layanan Amandemen Sertifikat Uji Kesesuaian
                    </p>
                </div>

                <div class="flex items-center gap-6">
                    <div class="relative inline-block text-left">
                        <button onclick="toggleNotifDropdown()" id="notifButton"
                            class="relative group p-2.5 rounded-xl hover:bg-slate-100 transition-all focus:outline-none">
                            <i
                                class="fas fa-bell text-slate-400 group-hover:text-orange-600 text-xl transition-colors"></i>
                            @if ($recentNotifications->count() > 0)
                                <span id="desktopNotifBadge"
                                    class="absolute top-2 right-2.5 w-2.5 h-2.5 bg-rose-500 border-2 border-white rounded-full"></span>
                            @endif
                        </button>

                        <div id="notifDropdown"
                            class="hidden absolute right-0 mt-3 w-80 bg-white rounded-2xl dropdown-shadow overflow-hidden animate-modal z-[100] border border-slate-100">
                            <div
                                class="p-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center text-left">
                                <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400">Update BALIS
                                </h4>
                                <span
                                    class="bg-orange-100 text-orange-600 text-[9px] font-black px-2 py-0.5 rounded-full">{{ $recentNotifications->count() }}
                                    Terdata</span>
                            </div>
                            <div class="max-h-[320px] overflow-y-auto no-scrollbar bg-white">
                                @forelse($recentNotifications as $notif)
                                    <div onclick="openPreviewModal({{ json_encode($notif->no_sertifikat) }}, {{ json_encode($notif->no_registrasi) }}, {{ json_encode($notif->bagian_diperbaiki) }}, {{ json_encode($notif->ketidaksesuaian) }}, {{ json_encode($notif->data_sesuai) }}, {{ json_encode($notif->updated_at->format('d M Y H:i')) }})"
                                        class="p-4 border-b border-slate-50 hover:bg-orange-50/40 transition-colors cursor-pointer group text-left">
                                        <div class="flex gap-3 text-left items-start">
                                            <div
                                                class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5 group-hover:scale-110 transition-transform">
                                                <i class="fas fa-check-circle text-sm"></i>
                                            </div>
                                            <div class="text-left">
                                                <p class="text-xs font-bold text-slate-800 leading-snug text-left">
                                                    Sertifikat <span
                                                        class="text-orange-600 font-black">{{ $notif->no_sertifikat }}</span>
                                                    berhasil diperbarui.</p>
                                                <div class="flex items-center gap-2 mt-1.5 text-left">
                                                    <span
                                                        class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter">{{ $notif->updated_at->diffForHumans() }}</span>
                                                    <span
                                                        class="text-[8px] bg-emerald-50 text-emerald-600 px-1.5 py-0.5 rounded font-black uppercase tracking-widest">Klik
                                                        Detail</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-10 text-center opacity-40">
                                        <i class="fas fa-bell-slash text-2xl mb-2 text-slate-300"></i>
                                        <p class="text-[10px] font-black uppercase tracking-widest">Belum ada data
                                            diselesaikan</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="h-8 w-[1px] bg-slate-200"></div>

                    <div class="flex items-center gap-3 text-left">
                        <div class="text-right">
                            <p class="text-xs font-black text-slate-800 uppercase tracking-tight">
                                {{ Auth::user()->name ?? 'Unit Pengguna' }}</p>
                            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest text-right">
                                Administrator Fasilitas</p>
                        </div>
                        <div
                            class="w-10 h-10 rounded-xl bg-orange-600 flex items-center justify-center text-white font-bold shadow-lg shadow-orange-600/20 active:scale-95 transition-transform cursor-pointer">
                            {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                        </div>
                    </div>
                </div>
            </div>

            <main
                class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-6 lg:p-8 space-y-6 md:space-y-8 no-scrollbar scroll-smooth text-left">

                <!-- BANNER -->
                <div
                    class="banner-gradient rounded-[2rem] p-6 md:p-10 text-white relative overflow-hidden shadow-xl animate-fade-in text-left">
                    <div class="absolute right-0 top-0 p-4 opacity-10 pointer-events-none hidden lg:block">
                        <i class="fas fa-radiation text-[12rem] rotate-12"></i>
                    </div>

                    <div class="relative z-10 max-w-2xl text-left">
                        <span
                            class="inline-block bg-orange-400/20 text-orange-300 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border border-orange-400/30 mb-4">
                            Sistem Layanan BAPETEN
                        </span>
                        <h2 class="text-2xl md:text-4xl font-extrabold mb-4 leading-tight text-left">Kelola Amandemen
                            Sertifikat <br class="hidden md:block text-left">Dalam Satu Dashboard.</h2>
                        <p class="text-orange-50/70 leading-relaxed mb-8 text-xs md:text-sm font-medium text-left">
                            Ajukan perbaikan data administrasi sertifikat uji kesesuaian dan pantau sinkronisasi BALIS
                            secara real-time melalui dashboard interaktif ini.
                        </p>

                        <div class="flex flex-wrap gap-3 md:gap-4 text-left">
                            <button onclick="openModal('add')"
                                class="bg-white text-orange-900 px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-orange-50 transition-all flex items-center gap-2 shadow-lg active:scale-95">
                                <i class="fas fa-plus-circle text-orange-600"></i> Buat Pengajuan
                            </button>
                            <a href="#tabelRiwayat"
                                class="bg-orange-800/40 backdrop-blur-md border border-orange-400/30 text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-orange-800 transition-all flex items-center gap-2 active:scale-95">
                                <i class="fas fa-history"></i> Lihat Riwayat
                            </a>
                        </div>
                    </div>
                </div>

                <!-- STATS (Interaktif & Diperbarui) -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-8 text-left">
                    <div class="lg:col-span-8 space-y-6 md:space-y-8 text-left">
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 text-left">
                            <!-- Card Total -->
                            <div
                                class="stat-card bg-white p-5 rounded-[2rem] border border-slate-200 group cursor-default">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center mb-4 group-hover:bg-slate-900 group-hover:text-white transition-all duration-500 shadow-sm border border-slate-100">
                                    <i class="fas fa-file-invoice text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-black text-slate-800 tracking-tight">
                                        {{ $mySubmissions->count() }}</h3>
                                    <div class="flex items-center gap-1.5">
                                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Total
                                        </p>
                                        <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
                                        <span class="text-[8px] font-black text-slate-300">Berkas</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Card Proses -->
                            <div
                                class="stat-card bg-white p-5 rounded-[2rem] border border-slate-200 group cursor-default">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center mb-4 group-hover:bg-amber-500 group-hover:text-white transition-all duration-500 shadow-sm border border-amber-100/50">
                                    <i
                                        class="fas fa-sync-alt text-xl group-hover:rotate-180 transition-transform duration-700"></i>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-black text-slate-800 tracking-tight">{{ $myPending }}
                                    </h3>
                                    <div class="flex items-center gap-1.5">
                                        <p class="text-[9px] font-bold text-amber-500 uppercase tracking-widest">Proses
                                        </p>
                                        <span class="flex h-1.5 w-1.5 relative">
                                            <span
                                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                            <span
                                                class="relative inline-flex rounded-full h-1.5 w-1.5 bg-amber-500"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <!-- Card Selesai -->
                            <div
                                class="stat-card bg-white p-5 rounded-[2rem] border border-slate-200 group cursor-default">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center mb-4 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-500 shadow-sm border border-emerald-100/50">
                                    <i class="fas fa-check-double text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-black text-slate-800 tracking-tight">{{ $myApproved }}
                                    </h3>
                                    <div class="flex items-center gap-1.5">
                                        <p class="text-[9px] font-bold text-emerald-500 uppercase tracking-widest">
                                            Selesai</p>
                                        <i class="fas fa-cloud-upload-alt text-[8px] text-emerald-300"></i>
                                    </div>
                                </div>
                            </div>
                            <!-- Card Revisi -->
                            <div
                                class="stat-card bg-white p-5 rounded-[2rem] border border-slate-200 group cursor-default">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center mb-4 group-hover:bg-rose-500 group-hover:text-white transition-all duration-500 shadow-sm border border-rose-100/50">
                                    <i class="fas fa-exclamation-circle text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-black text-rose-600 tracking-tight">{{ $myRejected }}
                                    </h3>
                                    <p class="text-[9px] font-bold text-rose-400 uppercase tracking-widest">Revisi</p>
                                </div>
                            </div>
                        </div>

                        <!-- GRAFIK (Visualisasi Diperbagus) -->
                        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm text-left">
                            <div
                                class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8 text-left">
                                <div class="text-left">
                                    <h3 class="font-black text-slate-800 text-xl tracking-tight text-left">Grafik
                                        Pengajuan</h3>
                                    <p class="text-xs text-slate-400 font-medium text-left mt-1">Pantau volume
                                        amandemen 6 bulan terakhir</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="flex -space-x-2">
                                        <div
                                            class="w-8 h-8 rounded-full border-2 border-white bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-400">
                                            BM</div>
                                        <div
                                            class="w-8 h-8 rounded-full border-2 border-white bg-orange-100 flex items-center justify-center text-[10px] font-bold text-orange-600">
                                            JK</div>
                                    </div>
                                    <div class="h-8 w-[1px] bg-slate-100 mx-1"></div>
                                    <span
                                        class="bg-emerald-50 text-emerald-600 px-3 py-1.5 rounded-xl text-[10px] font-black uppercase border border-emerald-100 flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Sinkron BALIS
                                    </span>
                                </div>
                            </div>
                            <div id="performanceChart" class="w-full -ml-3"></div>
                        </div>
                    </div>

                    <div class="lg:col-span-4 space-y-6 md:space-y-8 text-left">
                        <div
                            class="bg-white rounded-[1.5rem] border border-slate-200 shadow-sm flex flex-col overflow-hidden text-left">
                            <div
                                class="px-6 py-5 border-b border-slate-100 flex justify-between items-center text-left">
                                <h3 class="font-bold text-slate-800 text-sm text-left">Update BALIS</h3>
                                <i class="fas fa-bell text-orange-300 text-xs text-left"></i>
                            </div>
                            <div class="p-2 space-y-1 text-left">
                                @forelse($recentNotifications as $act)
                                    <div onclick="openPreviewModal({{ json_encode($act->no_sertifikat) }}, {{ json_encode($act->no_registrasi) }}, {{ json_encode($act->bagian_diperbaiki) }}, {{ json_encode($act->ketidaksesuaian) }}, {{ json_encode($act->data_sesuai) }}, {{ json_encode($act->updated_at->format('d M Y H:i')) }})"
                                        class="p-3 flex gap-4 hover:bg-orange-50/30 rounded-xl transition-all group relative text-left cursor-pointer">
                                        <div class="shrink-0 text-left">
                                            <div
                                                class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 text-left">
                                                <i class="fas fa-check text-xs text-left"></i>
                                            </div>
                                        </div>
                                        <div class="min-w-0 flex-1 text-left">
                                            <p class="text-[11px] font-bold text-slate-800 truncate text-left">
                                                {{ $act->no_sertifikat }}</p>
                                            <p
                                                class="text-[9px] text-emerald-600 font-bold uppercase mt-0.5 flex items-center gap-1 text-left">
                                                <i class="fas fa-cloud-upload-alt text-[8px] text-left"></i> Selesai
                                            </p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="py-12 text-center opacity-30 text-center">
                                        <i class="fas fa-inbox text-3xl mb-2 text-center"></i>
                                        <p class="text-[10px] font-black uppercase tracking-widest text-center">Tidak
                                            ada update</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                        <div
                            class="bg-orange-800 rounded-[1.5rem] p-6 text-white text-left relative overflow-hidden shadow-lg shadow-orange-900/20">
                            <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                            <div class="relative z-10">
                                <h5 class="font-bold text-sm mb-2 flex items-center gap-2"><i
                                        class="fas fa-headset text-orange-200"></i> Bantuan</h5>
                                <p class="text-[11px] text-orange-100/80 leading-relaxed mb-4">Hubungi tim administrasi
                                    BALIS melalui Pusat Kendali jika ada kendala.</p><button
                                    class="w-full py-2 bg-white/10 hover:bg-white text-white hover:text-orange-800 border border-white/20 hover:border-white rounded-xl text-[9px] font-black uppercase tracking-widest transition-all">Hubungi
                                    Admin</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TABEL DATA -->
                <div id="tabelRiwayat"
                    class="bg-white border border-slate-200 rounded-[2.5rem] shadow-sm overflow-hidden mt-2 mb-10 text-left">
                    <div
                        class="px-8 py-7 border-b border-slate-100 flex items-center justify-between bg-white/50 text-left">
                        <div class="flex items-center gap-3 text-left">
                            <span class="w-2 h-6 bg-orange-600 rounded-full text-left"></span>
                            <h3 class="font-black text-lg text-slate-800 tracking-tight text-left">Riwayat Pengajuan
                            </h3>
                        </div>
                        <div
                            class="flex items-center gap-2 px-3 py-1.5 bg-slate-100 rounded-lg text-[10px] font-black text-slate-500 text-left">
                            <div class="w-2 h-2 bg-emerald-500 rounded-full text-left"></div> TERDATA DI BALIS
                        </div>
                    </div>
                    <div class="overflow-x-auto no-scrollbar text-left">
                        <table class="w-full text-sm text-left min-w-[1000px]">
                            <thead
                                class="text-[10px] text-slate-400 uppercase bg-slate-50/50 border-b border-slate-100 font-black tracking-widest text-left">
                                <tr>
                                    <th class="px-8 py-5 text-left">Waktu</th>
                                    <th class="px-6 py-5 text-left">Identitas Sertifikat</th>
                                    <th class="px-6 py-5 w-[35%] text-left">Alasan / Perihal</th>
                                    <th class="px-6 py-4 text-center">Status</th>
                                    <th class="px-8 py-5 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-left">
                                @forelse($mySubmissions as $item)
                                    <tr
                                        class="hover:bg-slate-50 transition-colors group {{ $item->status == 'approved' ? 'bg-emerald-50/10' : '' }} text-left">
                                        <td class="px-8 py-6 text-left">
                                            <span
                                                class="font-bold text-slate-800 text-sm block mb-0.5 text-left">{{ $item->created_at->format('d M Y') }}</span>
                                            <span
                                                class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter text-left">{{ $item->created_at->format('H:i') }}
                                                WIB</span>
                                        </td>
                                        <td class="px-6 py-6 whitespace-nowrap text-left">
                                            <div class="flex flex-col gap-1.5 text-left">
                                                <span
                                                    class="bg-slate-900 text-white text-[10px] font-black px-2.5 py-1 rounded-lg w-fit text-left">SER:
                                                    {{ $item->no_sertifikat }}</span>
                                                <span
                                                    class="bg-slate-50 text-slate-500 text-[10px] font-black px-2.5 py-1 rounded-lg border border-slate-200 w-fit tracking-tighter text-left text-left">REG:
                                                    {{ $item->no_registrasi }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-6 text-left">
                                            <div class="relative text-left">
                                                <p class="text-xs text-slate-500 italic leading-relaxed line-clamp-2 text-left"
                                                    id="reason-{{ $item->id }}">"{{ $item->alasan_amandemen }}"
                                                </p>
                                                @if (strlen($item->alasan_amandemen) > 80)
                                                    <button onclick="toggleReason({{ $item->id ?? 0 }})"
                                                        id="btn-{{ $item->id }}"
                                                        class="text-[10px] font-black text-orange-600 mt-2 hover:underline flex items-center gap-1 uppercase tracking-tighter text-left">
                                                        <span>Lihat Detail</span> <i
                                                            class="fas fa-chevron-down text-[8px] transition-transform text-left"
                                                            id="icon-{{ $item->id }}"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-6 text-center whitespace-nowrap text-center">
                                            @if ($item->status == 'pending')
                                                <div
                                                    class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-600 text-[10px] font-black px-4 py-1.5 rounded-full border border-amber-100 uppercase tracking-tighter text-center">
                                                    <span
                                                        class="w-1.5 h-1.5 bg-amber-400 rounded-full animate-pulse text-center"></span>
                                                    PROSES</div>
                                            @elseif($item->status == 'approved')
                                                <div class="flex flex-col items-center gap-1 text-center">
                                                    <span
                                                        class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-600 text-[10px] font-black px-4 py-1.5 rounded-full border border-emerald-100 uppercase tracking-tighter text-center"><i
                                                            class="fas fa-check text-center"></i> SELESAI</span>
                                                    <span
                                                        class="text-[8px] font-black text-emerald-500 uppercase tracking-widest text-center"><i
                                                            class="fas fa-cloud-upload-alt text-[7px] text-center"></i>
                                                        Updated BALIS</span>
                                                </div>
                                            @else
                                                <div
                                                    class="inline-flex items-center gap-1.5 bg-rose-50 text-rose-600 text-[10px] font-black px-4 py-1.5 rounded-full border border-rose-100 uppercase tracking-tighter text-center">
                                                    <i class="fas fa-times text-center"></i> REVISI</div>
                                            @endif
                                        </td>
                                        <td class="px-8 py-6 text-center whitespace-nowrap">
                                            <div class="flex items-center justify-center min-w-[120px] mx-auto">
                                                @if ($item->status == 'pending')
                                                    <button
                                                        onclick="openCancelModal({{ $item->id ?? 'null' }}, {{ json_encode($item->no_sertifikat) }})"
                                                        class="flex items-center gap-1.5 text-xs font-black text-slate-300 hover:text-rose-600 transition-all uppercase tracking-widest group">
                                                        <i
                                                            class="fas fa-trash-alt text-[10px] group-hover:scale-110 transition-transform"></i>
                                                        <span>Batal</span>
                                                    </button>
                                                @elseif($item->status == 'rejected')
                                                    <button
                                                        onclick="openModal('edit', {{ $item->id ?? 'null' }}, {{ json_encode($item->no_sertifikat) }}, {{ json_encode($item->no_registrasi) }}, {{ json_encode($item->alasan_amandemen) }}, {{ json_encode($item->nomor_surat) }}, {{ json_encode($item->bagian_diperbaiki) }}, {{ json_encode($item->ketidaksesuaian) }}, {{ json_encode($item->data_sesuai) }})"
                                                        class="bg-orange-600 text-white px-5 py-2 rounded-xl text-[10px] font-black shadow-lg hover:bg-orange-700 active:scale-95 transition-all uppercase tracking-widest">
                                                        Perbaiki
                                                    </button>
                                                @else
                                                    <button
                                                        onclick="openPreviewModal({{ json_encode($item->no_sertifikat) }}, {{ json_encode($item->no_registrasi) }}, {{ json_encode($item->bagian_diperbaiki) }}, {{ json_encode($item->ketidaksesuaian) }}, {{ json_encode($item->data_sesuai) }}, {{ json_encode($item->updated_at->format('d M Y H:i')) }})"
                                                        class="flex items-center gap-1.5 text-emerald-600 hover:text-emerald-700 text-[10px] font-black uppercase tracking-widest group transition-all">
                                                        <i
                                                            class="fas fa-search text-[10px] group-hover:scale-110 transition-transform"></i>
                                                        <span
                                                            class="border-b-2 border-emerald-100 group-hover:border-emerald-500">Preview</span>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5"
                                            class="py-24 text-center text-slate-300 italic text-center">Belum ada data
                                            pengajuan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- MODAL PREVIEW -->
    <div id="previewModal" class="fixed inset-0 z-[250] hidden flex items-center justify-center p-4 text-left">
        <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-md" onclick="closePreviewModal()"></div>
        <div
            class="relative bg-white rounded-[2.8rem] max-w-lg w-full shadow-2xl animate-modal overflow-hidden flex flex-col border border-white/20 text-left text-left">
            <div
                class="bg-emerald-600 px-8 py-7 flex justify-between items-center text-white shrink-0 relative z-10 text-left">
                <div class="flex items-center gap-4 text-left">
                    <div
                        class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center border border-white/10 shrink-0 text-left">
                        <i class="fas fa-check-double text-lg text-left"></i>
                    </div>
                    <div class="text-left">
                        <h3 class="font-black text-xl tracking-tight leading-none text-left">Data Amandemen Selesai
                        </h3>
                        <p class="text-[9px] font-bold uppercase tracking-widest text-emerald-100 mt-1.5 text-left">
                            Sinkronisasi BALIS Berhasil</p>
                    </div>
                </div>
                <button onclick="closePreviewModal()"
                    class="w-10 h-10 rounded-full hover:bg-white/10 transition-all flex items-center justify-center text-left"><i
                        class="fas fa-times text-left"></i></button>
            </div>

            <div class="p-8 space-y-6 bg-slate-50/50 text-left">
                <div class="grid grid-cols-2 gap-4 text-left">
                    <div class="p-4 bg-white rounded-2xl border border-slate-100 shadow-sm text-left">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 text-left">No.
                            Sertifikat</p>
                        <p id="prevNoSertif" class="text-xs font-bold text-slate-800 tracking-tight text-left"></p>
                    </div>
                    <div class="p-4 bg-white rounded-2xl border border-slate-100 shadow-sm text-left">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 text-left">No.
                            Registrasi</p>
                        <p id="prevNoReg" class="text-xs font-bold text-slate-800 tracking-tight text-left"></p>
                    </div>
                </div>

                <div class="p-5 bg-white rounded-[2rem] border border-slate-100 shadow-sm space-y-4 text-left">
                    <div class="flex items-center gap-2 mb-2 text-left">
                        <span class="w-1.5 h-3 bg-emerald-500 rounded-full text-left"></span>
                        <p class="text-[10px] font-black text-slate-600 uppercase tracking-widest text-left"
                            id="prevBagian">BAGIAN YANG DIUBAH</p>
                    </div>
                    <div class="grid grid-cols-1 gap-4 text-left">
                        <div class="p-4 bg-rose-50/50 rounded-xl border border-rose-100 text-left">
                            <p class="text-[8px] font-black text-rose-400 uppercase tracking-widest mb-1 text-left">
                                Sebelumnya (Salah)</p>
                            <p id="prevSalah" class="text-xs text-slate-500 italic text-left"></p>
                        </div>
                        <div class="p-4 bg-emerald-50/50 rounded-xl border border-emerald-100 text-left">
                            <p class="text-[8px] font-black text-emerald-500 uppercase tracking-widest mb-1 text-left">
                                Menjadi (Benar)</p>
                            <p id="prevBenar" class="text-xs font-bold text-slate-800 text-left"></p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between items-center px-2 text-left">
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest text-left">Waktu Sinkron:
                        <span id="prevWaktu" class="text-slate-600 text-left"></span></p>
                    <a href="https://balis.bapeten.go.id" target="_blank"
                        class="text-[9px] font-black text-emerald-600 uppercase hover:underline text-left">BALIS Portal
                        <i class="fas fa-external-link-alt ml-1 text-left"></i></a>
                </div>
            </div>

            <div class="p-6 bg-white border-t border-slate-100 text-center">
                <button onclick="closePreviewModal()"
                    class="w-full bg-slate-900 text-white font-black py-4 rounded-2xl shadow-xl transition active:scale-95 uppercase tracking-widest text-xs text-center text-center">Tutup
                    Preview</button>
            </div>
        </div>
    </div>

    <!-- MODAL FORMULIR LENGKAP -->
    <div id="submissionModal" class="fixed inset-0 z-[200] hidden flex items-center justify-center p-4 text-left">
        <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-md" onclick="closeModal()"></div>
        <div
            class="relative bg-white rounded-[2.8rem] max-w-2xl w-full shadow-2xl animate-modal overflow-hidden flex flex-col max-h-[92vh] border border-white/20 text-left">
            <div
                class="bg-gradient-to-r from-[#7c2d12] to-[#ea580c] px-8 py-7 flex justify-between items-center text-white shrink-0 shadow-lg relative z-10 text-left">
                <div class="flex items-center gap-4 text-left">
                    <div
                        class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center border border-white/10 shrink-0 text-left">
                        <i class="fas fa-file-signature text-lg text-left"></i>
                    </div>
                    <div class="text-left">
                        <h3 class="font-black text-xl tracking-tight leading-none text-left" id="modalTitle">Pengajuan
                            Amandemen</h3>
                        <p class="text-[9px] font-bold uppercase tracking-widest text-orange-200 mt-1.5 text-left">
                            Formulir Resmi SI-SINAR X</p>
                    </div>
                </div>
                <button onclick="closeModal()"
                    class="w-10 h-10 rounded-full hover:bg-white/10 transition-all flex items-center justify-center text-left"><i
                        class="fas fa-times text-left"></i></button>
            </div>

            <form id="submissionForm" method="POST" enctype="multipart/form-data"
                action="{{ url('/sinarx/submission') }}" class="flex flex-col flex-1 overflow-hidden text-left">
                @csrf
                <div id="methodField" class="text-left"></div>
                <div
                    class="flex-1 overflow-y-auto p-8 md:p-10 space-y-8 no-scrollbar bg-slate-50/30 text-left text-slate-800 text-left">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-left">
                        <div class="space-y-2 text-left">
                            <label
                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1 text-left">No.
                                Sertifikat</label>
                            <input type="text" name="no_sertifikat" id="inputNoSertif"
                                class="w-full bg-white border border-slate-200 rounded-2xl px-5 py-4 text-sm font-bold focus:border-orange-500 outline-none transition-all shadow-sm text-left text-left"
                                placeholder="Contoh: 12345/UKES/..." required>
                        </div>
                        <div class="space-y-2 text-left">
                            <label
                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1 text-left">No.
                                Registrasi</label>
                            <input type="text" name="no_registrasi" id="inputNoRegistrasi"
                                class="w-full bg-white border border-slate-200 rounded-2xl px-5 py-4 text-sm font-bold focus:border-orange-500 outline-none transition-all shadow-sm text-left text-left"
                                placeholder="Contoh: REG-2024-..." required>
                        </div>
                    </div>

                    <div class="space-y-2 text-left text-left">
                        <label
                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1 text-left">Nomor
                            Surat Permohonan Unit</label>
                        <input type="text" name="nomor_surat" id="inputNomorSurat"
                            class="w-full bg-white border border-slate-200 rounded-2xl px-5 py-4 text-sm font-bold focus:border-orange-500 outline-none transition-all shadow-sm text-left text-left"
                            placeholder="Contoh: 024/RS/II/2026" required>
                    </div>

                    <div class="space-y-2 text-left text-left">
                        <label
                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1 text-left">Perihal
                            / Alasan Amandemen</label>
                        <textarea name="alasan_amandemen" id="inputAlasan" rows="2"
                            class="w-full bg-white border border-slate-200 rounded-2xl px-5 py-4 text-sm font-medium focus:border-orange-500 outline-none transition-all shadow-sm resize-none text-left text-left"
                            placeholder="Jelaskan alasan perubahan..." required></textarea>
                    </div>

                    <div
                        class="p-8 bg-orange-50/50 rounded-[2.5rem] border border-orange-100 space-y-6 text-left text-left">
                        <div class="flex items-center gap-3 mb-2 text-left">
                            <span class="w-1.5 h-4 bg-orange-500 rounded-full text-left"></span>
                            <p class="text-[10px] font-black text-orange-600 uppercase tracking-[0.2em] text-left">
                                Detail Perbaikan Sertifikat</p>
                        </div>
                        <div class="space-y-2 text-left">
                            <label
                                class="text-[10px] font-black text-slate-500 uppercase tracking-wide text-left">Bagian
                                yang diperbaiki</label>
                            <input type="text" name="bagian_diperbaiki" id="inputBagian"
                                class="w-full bg-white border border-slate-200 rounded-xl px-5 py-3.5 text-sm font-bold focus:border-orange-500 outline-none transition shadow-sm text-left text-left"
                                placeholder="Contoh: Nama Fasilitas">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-left text-left">
                            <div class="space-y-2 text-left">
                                <label
                                    class="text-[10px] font-black text-slate-500 uppercase tracking-wide text-rose-500 text-left">Ketidaksesuaian
                                    (Salah)</label>
                                <textarea name="ketidaksesuaian" id="inputSalah" rows="3"
                                    class="w-full bg-white border border-slate-200 rounded-xl px-5 py-3.5 text-sm outline-none focus:border-rose-300 transition shadow-sm text-left text-left"
                                    placeholder="Data yang salah..."></textarea>
                            </div>
                            <div class="space-y-2 text-left">
                                <label
                                    class="text-[10px] font-black text-slate-500 uppercase tracking-wide text-emerald-600 text-left">Data
                                    yang Sesuai (Benar)</label>
                                <textarea name="data_sesuai" id="inputBenar" rows="3"
                                    class="w-full bg-white border border-slate-200 rounded-xl px-5 py-3.5 text-sm font-bold outline-none focus:border-emerald-300 transition shadow-sm text-left text-left"
                                    placeholder="Data yang benar..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2 pb-2 text-left text-left text-left">
                        <label
                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1 text-left">Bukti
                            Dokumen PDF (Max 10MB)</label>
                        <input type="file" name="file_upload" id="fileInput"
                            class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-orange-600 file:text-white hover:file:bg-orange-700 text-left text-left">
                    </div>
                </div>

                <div
                    class="p-8 md:p-10 border-t border-slate-100 bg-white shrink-0 flex flex-col sm:flex-row gap-4 text-center text-center text-center">
                    <button type="submit"
                        class="flex-[2] bg-orange-600 hover:bg-orange-700 text-white font-black py-4 rounded-[1.25rem] shadow-xl shadow-orange-600/20 transition active:scale-95 uppercase tracking-widest text-xs text-center text-center">Kirim
                        Permohonan</button>
                    <button type="button" onclick="closeModal()"
                        class="flex-1 bg-slate-100 text-slate-400 font-black py-4 rounded-[1.25rem] transition active:scale-95 uppercase tracking-widest text-xs text-center text-center">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL BATALKAN -->
    <div id="cancelModal" class="fixed inset-0 z-[210] hidden flex items-center justify-center p-4 text-center">
        <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-md text-center" onclick="closeCancelModal()"></div>
        <div
            class="relative bg-white rounded-[2.5rem] p-10 max-w-sm w-full shadow-2xl text-center animate-modal border border-white/20 text-slate-800 text-center">
            <div
                class="w-20 h-20 bg-rose-50 text-rose-500 rounded-3xl flex items-center justify-center mx-auto mb-6 border border-rose-100 relative shadow-inner text-center">
                <i class="fas fa-trash-alt text-3xl text-center"></i>
            </div>
            <h3 class="text-2xl font-black mb-3 tracking-tight text-center text-center">Batalkan Pengajuan?</h3>
            <p class="text-slate-400 text-xs mb-10 leading-relaxed px-2 text-center text-center text-center">Data
                pengajuan sertifikat <span id="cancelInfo"
                    class="font-black text-slate-800 underline decoration-orange-500 text-center"></span> akan dihapus
                permanen.</p>
            <form id="cancelForm" method="POST" class="space-y-3 text-center text-center">
                @csrf @method('DELETE')
                <button type="submit"
                    class="w-full bg-rose-600 hover:bg-rose-700 text-white font-black py-4 rounded-2xl shadow-xl shadow-rose-600/30 transition active:scale-95 uppercase tracking-widest text-xs text-center text-center text-center">Ya,
                    Batalkan</button>
                <button type="button" onclick="closeCancelModal()"
                    class="w-full text-slate-400 hover:text-slate-600 font-black py-2.5 transition text-[10px] uppercase tracking-widest text-center text-center text-center">Kembali</button>
            </form>
        </div>
    </div>

    <script>
        const APP_USER_ID = "{{ Auth::id() }}";
        const NOTIF_KEY = `sinarx_menu_read_${APP_USER_ID}`;

        // ─── Badge & Bell State ───────────────────────────────────────
        function isNotifRead() {
            return localStorage.getItem(NOTIF_KEY) === "true";
        }

        function markNotifRead() {
            localStorage.setItem(NOTIF_KEY, "true");
            updateBellVisuals();
        }

        function updateBellVisuals() {
            const read = isNotifRead();

            // Desktop
            const desktopBadge = document.getElementById('desktopNotifBadge');
            const desktopBell = document.querySelector('#notifButton i');
            if (desktopBadge) desktopBadge.classList.toggle('hidden', read);
            if (desktopBell) desktopBell.className =
                `fas fa-bell text-xl transition-colors ${read ? 'bell-read' : 'bell-unread'}`;

            // Mobile
            const mobileBadge = document.getElementById('mobileNotifBadge');
            const mobileBell = document.getElementById('mobileBellIcon');
            if (mobileBadge) mobileBadge.classList.toggle('hidden', read);
            if (mobileBell) mobileBell.className = `fas fa-bell ${read ? 'bell-read' : 'bell-unread'}`;
        }

        document.addEventListener('DOMContentLoaded', updateBellVisuals);

        // ─── Desktop Notif Dropdown ───────────────────────────────────
        function toggleNotifDropdown() {
            const d = document.getElementById('notifDropdown');
            const isHidden = d.classList.contains('hidden');
            d.classList.toggle('hidden');
            if (isHidden) {
                markNotifRead();
                const closeHandler = (e) => {
                    const btn = document.getElementById('notifButton');
                    if (!d.contains(e.target) && btn && !btn.contains(e.target)) {
                        d.classList.add('hidden');
                        document.removeEventListener('mousedown', closeHandler);
                    }
                };
                document.addEventListener('mousedown', closeHandler);
            }
        }

        // ─── Mobile Notif Dropdown ────────────────────────────────────
        function toggleMobileNotif() {
            const d = document.getElementById('mobileNotifDropdown');
            const isHidden = d.classList.contains('hidden');

            // Clone isi dari desktop dropdown supaya konten sama
            if (isHidden) {
                const desktopContent = document.querySelector('#notifDropdown .max-h-\\[320px\\]');
                const mobileContent = document.getElementById('mobileNotifContent');
                if (desktopContent && mobileContent) {
                    mobileContent.innerHTML = desktopContent.innerHTML;
                }
                markNotifRead();

                const closeHandler = (e) => {
                    const btn = document.getElementById('mobileNotifBtn');
                    if (!d.contains(e.target) && btn && !btn.contains(e.target)) {
                        d.classList.add('hidden');
                        document.removeEventListener('mousedown', closeHandler);
                    }
                };
                document.addEventListener('mousedown', closeHandler);
            }
            d.classList.toggle('hidden');
        }

        // ─── Preview Modal ────────────────────────────────────────────
        function openPreviewModal(noSertif, noReg, bagian, salah, benar, waktu) {
            document.getElementById('prevNoSertif').innerText = noSertif;
            document.getElementById('prevNoReg').innerText = noReg;
            document.getElementById('prevBagian').innerText = (bagian || 'DATA SERTIFIKAT').toUpperCase();
            document.getElementById('prevSalah').innerText = salah || '-';
            document.getElementById('prevBenar').innerText = benar || '-';
            document.getElementById('prevWaktu').innerText = waktu;
            const modal = document.getElementById('previewModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.getElementById('notifDropdown')?.classList.add('hidden');
            document.getElementById('mobileNotifDropdown')?.classList.add('hidden');
        }

        function closePreviewModal() {
            const modal = document.getElementById('previewModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // ─── Sidebar ──────────────────────────────────────────────────
        function toggleSidebar() {
            const s = document.getElementById('sidebar');
            const o = document.getElementById('sidebarOverlay');
            s.classList.toggle('-translate-x-full');
            if (!s.classList.contains('-translate-x-full')) {
                o.classList.remove('hidden');
                setTimeout(() => o.classList.add('opacity-100'), 10);
            } else {
                o.classList.remove('opacity-100');
                setTimeout(() => o.classList.add('hidden'), 300);
            }
        }

        // ─── Chart ────────────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', function() {
            var options = {
                series: [{
                    name: 'Total Pengajuan',
                    data: @json($chartData)
                }],
                chart: {
                    type: 'area',
                    height: 280,
                    toolbar: {
                        show: false
                    },
                    fontFamily: 'Plus Jakarta Sans',
                    dropShadow: {
                        enabled: true,
                        top: 8,
                        left: 0,
                        blur: 12,
                        opacity: 0.1,
                        color: '#ea580c'
                    }
                },
                colors: ['#ea580c'],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 4,
                    lineCap: 'round'
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.45,
                        opacityTo: 0.05,
                        stops: [0, 90, 100],
                        colorStops: [{
                            offset: 0,
                            color: '#ea580c',
                            opacity: 0.4
                        }, {
                            offset: 100,
                            color: '#ffffff',
                            opacity: 0
                        }]
                    }
                },
                markers: {
                    size: 5,
                    colors: ['#ffffff'],
                    strokeColors: '#ea580c',
                    strokeWidth: 3,
                    hover: {
                        size: 8
                    }
                },
                xaxis: {
                    categories: ['5 Bulan Lalu', '4 Bulan Lalu', '3 Bulan Lalu', '2 Bulan Lalu', 'Bulan Lalu',
                        'Bulan Ini'
                    ],
                    labels: {
                        style: {
                            colors: '#94a3b8',
                            fontSize: '11px',
                            fontWeight: 600
                        }
                    },
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#94a3b8',
                            fontSize: '11px',
                            fontWeight: 600
                        }
                    }
                },
                grid: {
                    borderColor: '#f1f5f9',
                    strokeDashArray: 6,
                    padding: {
                        top: 10,
                        right: 10,
                        bottom: 0,
                        left: 10
                    }
                },
                tooltip: {
                    theme: 'light',
                    y: {
                        formatter: val => val + " Pengajuan"
                    },
                    style: {
                        fontSize: '12px'
                    }
                }
            };
            new ApexCharts(document.querySelector("#performanceChart"), options).render();
        });

        // ─── Modal Form ───────────────────────────────────────────────
        function openModal(mode, id = null, no_sertif = '', no_reg = '', alasan = '', nomor_surat = '', bagian = '', salah =
            '', benar = '') {
            const modal = document.getElementById('submissionModal');
            const form = document.getElementById('submissionForm');
            const methodField = document.getElementById('methodField');
            document.getElementById('inputNoSertif').value = no_sertif || '';
            document.getElementById('inputNoRegistrasi').value = no_reg || '';
            document.getElementById('inputAlasan').value = alasan || '';
            document.getElementById('inputNomorSurat').value = nomor_surat || '';
            document.getElementById('inputBagian').value = bagian || '';
            document.getElementById('inputSalah').value = salah || '';
            document.getElementById('inputBenar').value = benar || '';
            modal.classList.remove('hidden');
            if (mode === 'add') {
                document.getElementById('modalTitle').innerText = 'Pengajuan Amandemen';
                form.action = "{{ url('/sinarx/submission') }}";
                methodField.innerHTML = '';
            } else {
                document.getElementById('modalTitle').innerText = 'Perbaiki Amandemen';
                form.action = "{{ url('/sinarx/submission') }}/" + (id || '');
                methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            }
        }

        function closeModal() {
            document.getElementById('submissionModal').classList.add('hidden');
        }

        function openCancelModal(id, no) {
            document.getElementById('cancelInfo').innerText = no || '';
            document.getElementById('cancelForm').action = "{{ url('/sinarx/submission') }}/" + (id || '');
            document.getElementById('cancelModal').classList.remove('hidden');
            document.getElementById('cancelModal').classList.add('flex');
        }

        function closeCancelModal() {
            document.getElementById('cancelModal').classList.add('hidden');
            document.getElementById('cancelModal').classList.remove('flex');
        }

        function toggleReason(id) {
            if (!id) return;
            const p = document.getElementById('reason-' + id);
            const btn = document.getElementById('btn-' + id);
            const icon = document.getElementById('icon-' + id);
            if (!p) return;
            p.classList.toggle('line-clamp-2');
            if (btn) btn.querySelector('span').innerText = p.classList.contains('line-clamp-2') ? 'Lihat Detail' :
                'Sembunyikan';
            icon?.classList.toggle('rotate-180');
        }
    </script>
</body>

</html>