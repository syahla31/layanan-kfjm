<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Sinar-X | SI-MUTU</title>
    
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
                        primary: '#ea580c', /* orange-600 */
                        orangeMain: '#c2410c', /* orange-700 */
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        @keyframes modalShow {
            from { opacity: 0; transform: scale(0.95) translateY(10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .animate-modal { animation: modalShow 0.25s ease-out forwards; }
        .animate-pulse-slow { animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
        
        .modal-backdrop-blur { backdrop-filter: blur(4px); background-color: rgba(15, 23, 42, 0.6); }
        .card-stat-hover:hover { transform: translateY(-4px); transition: all 0.3s ease; }
        
        /* Gradient khusus tema Sinar-X */
        .banner-gradient { background: linear-gradient(135deg, #431407 0%, #ea580c 50%, #7c2d12 100%); }
        .glass-overlay {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-orange-100 selection:text-orange-900 overflow-hidden">

    @php
        use App\Models\SinarxSubmission;
        use Illuminate\Support\Facades\Auth;
        
        $mySubmissions = collect();
        try {
            if (!isset($mySubmissions) || $mySubmissions->isEmpty()) {
                $mySubmissions = SinarxSubmission::where('user_id', Auth::id())
                                    ->orderBy('created_at', 'desc')
                                    ->get();
            }
        } catch(\Exception $e) {}

        $myPending = $mySubmissions->where('status', 'pending')->count();
        $myApproved = $mySubmissions->where('status', 'approved')->count();
        $myRejected = $mySubmissions->where('status', 'rejected')->count();

        // Aktivitas Terbaru (Ambil 5 teratas)
        $recentActivities = $mySubmissions->take(5);

        // Data dummy untuk chart jika data riil per bulan kosong, agar visual terbentuk
        $chartData = [
            $mySubmissions->whereBetween('created_at', [now()->subMonths(5)->startOfMonth(), now()->subMonths(5)->endOfMonth()])->count(),
            $mySubmissions->whereBetween('created_at', [now()->subMonths(4)->startOfMonth(), now()->subMonths(4)->endOfMonth()])->count(),
            $mySubmissions->whereBetween('created_at', [now()->subMonths(3)->startOfMonth(), now()->subMonths(3)->endOfMonth()])->count(),
            $mySubmissions->whereBetween('created_at', [now()->subMonths(2)->startOfMonth(), now()->subMonths(2)->endOfMonth()])->count(),
            $mySubmissions->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])->count(),
            $mySubmissions->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
        ];
    @endphp

    <div class="flex h-screen overflow-hidden bg-slate-50 relative">
        
        <!-- === MOBILE OVERLAY === -->
        <div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 z-40 hidden lg:hidden glass-overlay transition-opacity duration-300 opacity-0"></div>

        <!-- === SIDEBAR WRAPPER (Responsive) === -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-2xl lg:shadow-none transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto flex flex-col h-full border-r border-slate-200">
            @include('components.sinarx-sidebar')
        </aside>

        <!-- MAIN CONTENT -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden relative w-full">
            
            <!-- === HEADER MOBILE === -->
            <div class="lg:hidden bg-white/90 backdrop-blur-md border-b border-slate-200 px-4 py-3 flex items-center justify-between z-20 sticky top-0 shadow-sm">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="p-2 -ml-2 text-slate-500 hover:text-orange-600 hover:bg-slate-100 rounded-lg transition-colors focus:outline-none">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-orange-600 flex items-center justify-center text-white shadow-sm">
                            <i class="fas fa-radiation text-sm animate-pulse-slow"></i>
                        </div>
                        <span class="font-bold text-slate-800 text-sm tracking-wide">SI-SINAR X</span>
                    </div>
                </div>
                <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 text-xs font-bold border border-orange-200">
                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                </div>
            </div>

            <!-- === HEADER DESKTOP === -->
            <div class="hidden lg:block sticky top-0 z-10 bg-white/80 backdrop-blur-md border-b border-slate-200">
                @include('components.sinarx-header', [
                    'title' => 'Dashboard Unit',
                    'subtitle' => 'Layanan Amandemen Sertifikat Uji Kesesuaian'
                ])
            </div>

            <!-- SCROLLABLE CONTENT -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-6 lg:p-8 space-y-6 md:space-y-8 no-scrollbar scroll-smooth">
                
                <!-- SUCCESS POP-UP -->
                @if (session('success'))
                <div id="successModal" class="fixed inset-0 z-[200] flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="this.parentElement.remove()"></div>
                    <div class="bg-white rounded-[2.5rem] p-8 md:p-10 max-w-sm w-full shadow-2xl relative z-10 text-center animate-modal">
                        <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-check text-4xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 mb-2">Berhasil!</h3>
                        <p class="text-slate-500 text-sm mb-6">{{ session('success') }}</p>
                        <button onclick="this.closest('#successModal').remove()" class="w-full bg-orange-600 text-white font-bold py-3 rounded-xl hover:bg-orange-700 transition-all shadow-lg active:scale-95 uppercase tracking-widest text-xs">Oke, Lanjutkan</button>
                    </div>
                </div>
                @endif
                
                <!-- WELCOME BANNER (Disamakan dengan SI-LAB UJI) -->
                <div class="banner-gradient rounded-[2rem] p-6 md:p-10 text-white relative overflow-hidden shadow-xl animate-fade-in">
                    <div class="absolute right-0 top-0 p-4 opacity-10 pointer-events-none hidden lg:block">
                        <i class="fas fa-radiation text-[12rem] rotate-12"></i>
                    </div>
                    
                    <div class="relative z-10 max-w-2xl text-left">
                        <span class="inline-block bg-orange-400/20 text-orange-300 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border border-orange-400/30 mb-4">
                            Sistem Layanan BAPETEN
                        </span>
                        <h2 class="text-2xl md:text-4xl font-extrabold mb-4 leading-tight">Kelola Amandemen Sertifikat <br class="hidden md:block">Dalam Satu Dashboard.</h2>
                        <p class="text-orange-50/70 leading-relaxed mb-8 text-xs md:text-sm font-medium">
                            Ajukan perbaikan data administrasi sertifikat uji kesesuaian, pantau progres validasi secara real-time, dan kelola seluruh riwayat permohonan dengan lebih mudah dan transparan.
                        </p>
                        
                        <div class="flex flex-wrap gap-3 md:gap-4">
                            <button onclick="openModal('add')" class="bg-white text-orange-900 px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-orange-50 transition-all flex items-center gap-2 shadow-lg active:scale-95">
                                <i class="fas fa-plus-circle text-orange-600"></i> Buat Pengajuan
                            </button>
                            <a href="#tabelRiwayat" class="bg-orange-800/40 backdrop-blur-md border border-orange-400/30 text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-orange-800 transition-all flex items-center gap-2 active:scale-95">
                                <i class="fas fa-history"></i> Lihat Riwayat
                            </a>
                        </div>
                    </div>
                </div>

                <!-- STATS & MAIN CONTENT WRAPPER -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-8">
                    
                    <!-- LEFT COLUMN: STATS & CHARTS (8 cols) -->
                    <div class="lg:col-span-8 space-y-6 md:space-y-8">
                        
                        <!-- SUMMARY CARDS (4 Item untuk Sinar-X) -->
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-4">
                            <div class="bg-white p-4 md:p-5 rounded-[1.5rem] border border-slate-200 shadow-sm transition-all card-stat-hover flex flex-col items-start gap-3 text-left">
                                <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-600 flex items-center justify-center border border-slate-100 shrink-0">
                                    <i class="fas fa-file-invoice text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl md:text-2xl font-black text-slate-800">{{ $mySubmissions->count() }}</h3>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-0.5">Total</p>
                                </div>
                            </div>
                            <div class="bg-white p-4 md:p-5 rounded-[1.5rem] border border-slate-200 shadow-sm transition-all card-stat-hover flex flex-col items-start gap-3 text-left">
                                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-100 shrink-0">
                                    <i class="fas fa-sync-alt text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl md:text-2xl font-black text-slate-800">{{ $myPending }}</h3>
                                    <p class="text-[9px] font-black text-amber-500 uppercase tracking-widest mt-0.5">Proses</p>
                                </div>
                            </div>
                            <div class="bg-white p-4 md:p-5 rounded-[1.5rem] border border-slate-200 shadow-sm transition-all card-stat-hover flex flex-col items-start gap-3 text-left">
                                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 shrink-0">
                                    <i class="fas fa-check-double text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl md:text-2xl font-black text-slate-800">{{ $myApproved }}</h3>
                                    <p class="text-[9px] font-black text-emerald-500 uppercase tracking-widest mt-0.5">Selesai</p>
                                </div>
                            </div>
                            <div class="bg-white p-4 md:p-5 rounded-[1.5rem] border border-slate-200 shadow-sm transition-all card-stat-hover flex flex-col items-start gap-3 text-left">
                                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center border border-rose-100 shrink-0">
                                    <i class="fas fa-exclamation-circle text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl md:text-2xl font-black text-rose-600">{{ $myRejected }}</h3>
                                    <p class="text-[9px] font-black text-rose-400 uppercase tracking-widest mt-0.5">Revisi</p>
                                </div>
                            </div>
                        </div>

                        <!-- PERFORMANCE CHART -->
                        <div class="bg-white p-6 rounded-[1.5rem] border border-slate-200 shadow-sm text-left">
                            <div class="flex justify-between items-center mb-8">
                                <div>
                                    <h3 class="font-bold text-slate-800 text-base md:text-lg">Grafik Pengajuan</h3>
                                    <p class="text-xs text-slate-400 font-medium">Monitoring permohonan amandemen bulanan</p>
                                </div>
                                <span class="bg-orange-50 text-orange-600 px-3 py-1 rounded-lg text-[9px] font-black uppercase border border-orange-100">Live Data</span>
                            </div>
                            <div id="performanceChart" class="w-full"></div>
                        </div>

                    </div>

                    <!-- RIGHT COLUMN: ACTIVITIES & HELP (4 cols) -->
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
                                            $icon = match($act->status) {
                                                'approved' => ['fa-check-double', 'bg-emerald-50 text-emerald-600'],
                                                'rejected' => ['fa-exclamation-triangle', 'bg-rose-50 text-rose-600'],
                                                default => ['fa-sync-alt animate-spin-slow', 'bg-amber-50 text-amber-600']
                                            };
                                        @endphp
                                        <div class="w-9 h-9 rounded-lg {{ $icon[1] }} flex items-center justify-center group-hover:scale-110 transition-transform">
                                            <i class="fas {{ $icon[0] }} text-xs"></i>
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-[11px] font-bold text-slate-800 truncate">{{ $act->no_sertifikat }}</p>
                                        <div class="flex items-center justify-between mt-0.5">
                                            <span class="text-[9px] text-slate-400">{{ $act->created_at->diffForHumans() }}</span>
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
                                <a href="#tabelRiwayat" class="text-[9px] font-black text-orange-600 uppercase tracking-widest flex items-center justify-center gap-2 hover:gap-3 transition-all">
                                    Lihat Selengkapnya <i class="fas fa-arrow-down"></i>
                                </a>
                            </div>
                        </div>

                        <!-- HELP CARD -->
                        <div class="bg-orange-800 rounded-[1.5rem] p-6 text-white relative overflow-hidden shadow-lg shadow-orange-900/20 text-left">
                            <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                            <div class="relative z-10">
                                <h5 class="font-bold text-sm mb-2 flex items-center gap-2">
                                    <i class="fas fa-headset text-orange-200"></i> Bantuan Teknis
                                </h5>
                                <p class="text-[11px] text-orange-100/80 leading-relaxed mb-4 font-medium">
                                    Jika terdapat kendala pengunggahan atau ketidaksesuaian data sertifikat, segera hubungi Pusat Kendali.
                                </p>
                                <button class="w-full py-2 bg-white/10 hover:bg-white text-white hover:text-orange-800 border border-white/20 hover:border-white rounded-xl text-[9px] font-black uppercase tracking-widest transition-all">
                                    Hubungi Admin
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- TABEL DATA (Full Width di Bawah Grid) -->
                <div id="tabelRiwayat" class="bg-white border border-slate-200 rounded-[2.5rem] shadow-sm overflow-hidden mt-2 mb-10">
                    <div class="px-8 py-7 border-b border-slate-100 flex items-center gap-3 bg-white/50">
                        <span class="w-2 h-6 bg-orange-600 rounded-full"></span>
                        <h3 class="font-black text-lg text-slate-800 tracking-tight">Riwayat Pengajuan</h3>
                    </div>
                    <div class="overflow-x-auto no-scrollbar">
                        <table class="w-full text-sm text-left min-w-[1000px]">
                            <thead class="text-[10px] text-slate-400 uppercase bg-slate-50/50 border-b border-slate-100 font-black tracking-widest">
                                <tr>
                                    <th class="px-8 py-5">Waktu</th>
                                    <th class="px-6 py-5">Identitas Sertifikat</th>
                                    <th class="px-6 py-5 w-[35%]">Alasan / Perihal</th>
                                    <th class="px-6 py-4 text-center">Status</th>
                                    <th class="px-8 py-5 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($mySubmissions as $item)
                                <tr class="hover:bg-slate-50 transition-colors group">
                                    <td class="px-8 py-6">
                                        <span class="font-bold text-slate-800 text-sm block mb-0.5">{{ $item->created_at->format('d M Y') }}</span>
                                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tight">{{ $item->created_at->format('H:i') }} WIB</span>
                                    </td>
                                    <td class="px-6 py-6 whitespace-nowrap">
                                        <div class="flex flex-col gap-1.5">
                                            <span class="bg-orange-50 text-orange-600 text-[10px] font-black px-2.5 py-1 rounded-lg border border-orange-100 w-fit">SER: {{ $item->no_sertifikat }}</span>
                                            <span class="bg-slate-50 text-slate-500 text-[10px] font-black px-2.5 py-1 rounded-lg border border-slate-200 w-fit">REG: {{ $item->no_registrasi }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-6">
                                        <div class="relative">
                                            <p class="text-xs text-slate-500 italic leading-relaxed line-clamp-2" id="reason-{{ $item->id }}">
                                                "{{ $item->alasan_amandemen }}"
                                            </p>
                                            @if(strlen($item->alasan_amandemen) > 80)
                                            <button onclick="toggleReason({{ $item->id }})" id="btn-{{ $item->id }}" class="text-[10px] font-black text-orange-600 mt-2 hover:underline flex items-center gap-1 uppercase tracking-tighter">
                                                <span>Lihat Detail</span>
                                                <i class="fas fa-chevron-down text-[8px] transition-transform" id="icon-{{ $item->id }}"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-6 text-center whitespace-nowrap">
                                        @if($item->status == 'pending')
                                            <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-600 text-[10px] font-black px-4 py-1.5 rounded-full border border-amber-100 uppercase tracking-tighter"><span class="w-1.5 h-1.5 bg-amber-400 rounded-full animate-pulse"></span> PROSES</span>
                                        @elseif($item->status == 'approved')
                                            <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-600 text-[10px] font-black px-4 py-1.5 rounded-full border border-emerald-100 uppercase tracking-tighter"><i class="fas fa-check"></i> SELESAI</span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 bg-rose-50 text-rose-600 text-[10px] font-black px-4 py-1.5 rounded-full border border-rose-100 uppercase tracking-tighter"><i class="fas fa-times"></i> REVISI</span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-6 text-center whitespace-nowrap">
                                        @if($item->status == 'pending')
                                            <button onclick="openCancelModal({{ $item->id }}, '{{ $item->no_sertifikat }}')" class="text-xs font-black text-slate-300 hover:text-rose-600 transition-all flex items-center justify-center gap-2 mx-auto active:scale-90 uppercase tracking-widest">
                                                <i class="fas fa-trash-alt text-[10px]"></i> Batal
                                            </button>
                                        @elseif($item->status == 'rejected')
                                            <button onclick="openModal('edit', {{ $item->id }}, '{{ $item->no_sertifikat }}', '{{ $item->no_registrasi }}', '{{ addslashes($item->alasan_amandemen) }}', '{{ $item->nomor_surat }}', '{{ $item->bagian_diperbaiki }}', '{{ addslashes($item->ketidaksesuaian) }}', '{{ addslashes($item->data_sesuai) }}')" class="bg-orange-600 text-white px-5 py-2.5 rounded-xl text-[10px] font-black shadow-lg hover:bg-orange-700 active:scale-95 transition-all uppercase tracking-widest">Perbaiki</button>
                                        @else
                                            <a href="https://balis.bapeten.go.id" target="_blank" class="text-blue-600 hover:underline text-[10px] font-black flex items-center justify-center gap-1 uppercase tracking-widest">
                                                <i class="fas fa-external-link-alt"></i> BALIS
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="py-24 text-center text-slate-300 italic font-medium">Belum ada data pengajuan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-8 text-center text-xs text-slate-400">
                    &copy; 2026 Sistem Informasi Jaminan Mutu Ketenaganukliran
                </div>
            </main>
        </div>
    </div>

    <!-- MODAL FORMULIR (Sama dengan sebelumnya) -->
    <div id="submissionModal" class="fixed inset-0 z-[200] hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-md" onclick="closeModal()"></div>
        <div class="relative bg-white rounded-[2.8rem] max-w-2xl w-full shadow-2xl animate-modal overflow-hidden flex flex-col max-h-[92vh] border border-white/20">
            
            <div class="bg-gradient-to-r from-[#7c2d12] to-[#9a3412] px-8 py-7 flex justify-between items-center text-white shrink-0 shadow-lg relative z-10 border-b border-white/5">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center border border-white/10">
                        <i class="fas fa-file-signature text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-xl tracking-tight leading-none" id="modalTitle">Pengajuan Amandemen</h3>
                        <p class="text-[9px] font-bold uppercase tracking-widest text-orange-200/70 mt-1.5">Formulir Resmi BAPETEN</p>
                    </div>
                </div>
                <button onclick="closeModal()" class="w-10 h-10 rounded-full hover:bg-white/10 transition-all flex items-center justify-center border border-white/10"><i class="fas fa-times"></i></button>
            </div>
            
            <form id="submissionForm" method="POST" enctype="multipart/form-data" action="{{ url('/sinarx/submission') }}" class="flex flex-col flex-1 overflow-hidden">
                @csrf
                <div id="methodField"></div>
                
                <div class="flex-1 overflow-y-auto p-8 md:p-10 space-y-8 no-scrollbar bg-slate-50/30 text-left">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">No. Sertifikat</label>
                            <input type="text" name="no_sertifikat" id="inputNoSertif" class="w-full bg-white border border-slate-200 rounded-2xl px-5 py-4 text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none shadow-sm placeholder:text-slate-300" placeholder="Contoh: 12345/UKES/..." required>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">No. Registrasi</label>
                            <input type="text" name="no_registrasi" id="inputNoRegistrasi" class="w-full bg-white border border-slate-200 rounded-2xl px-5 py-4 text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none shadow-sm placeholder:text-slate-300" placeholder="Contoh: REG-2024-..." required>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Nomor Surat Permohonan Unit</label>
                        <input type="text" name="nomor_surat" id="inputNomorSurat" class="w-full bg-white border border-slate-200 rounded-2xl px-5 py-4 text-sm font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none shadow-sm placeholder:text-slate-300" placeholder="Contoh: 024/RS/II/2026" required>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Perihal / Alasan Amandemen</label>
                        <textarea name="alasan_amandemen" id="inputAlasan" rows="2" class="w-full bg-white border border-slate-200 rounded-2xl px-5 py-4 text-sm font-medium focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none shadow-sm resize-none" placeholder="Jelaskan secara singkat alasan perubahan..." required></textarea>
                    </div>

                    <div class="p-8 bg-orange-50/50 rounded-[2.5rem] border border-orange-100 space-y-6">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="w-1.5 h-4 bg-orange-500 rounded-full"></span>
                            <p class="text-[10px] font-black text-orange-600 uppercase tracking-[0.2em]">Detail Perbaikan Sertifikat</p>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-wide">Bagian yang diperbaiki</label>
                            <input type="text" name="bagian_diperbaiki" id="inputBagian" class="w-full bg-white border border-slate-200 rounded-xl px-5 py-3.5 text-sm font-bold focus:border-orange-500 outline-none transition shadow-sm" placeholder="Contoh: Nama Fasilitas">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-500 uppercase tracking-wide text-rose-500">Ketidaksesuaian (Salah)</label>
                                <textarea name="ketidaksesuaian" id="inputSalah" rows="3" class="w-full bg-white border border-slate-200 rounded-xl px-5 py-3.5 text-sm outline-none focus:border-rose-300 transition shadow-sm" placeholder="Data yang salah..."></textarea>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-500 uppercase tracking-wide text-emerald-600">Data yang Sesuai (Benar)</label>
                                <textarea name="data_sesuai" id="inputBenar" rows="3" class="w-full bg-white border border-slate-200 rounded-xl px-5 py-3.5 text-sm font-bold outline-none focus:border-emerald-300 transition shadow-sm" placeholder="Data yang benar..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2 pb-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Bukti Dokumen PDF (Max 10MB)</label>
                        <label id="fileDropZone" class="flex items-center justify-between px-6 py-4 border-2 border-dashed border-slate-200 rounded-[1.5rem] cursor-pointer hover:border-orange-500 transition-all bg-white group shadow-sm">
                            <div class="flex items-center gap-4 overflow-hidden">
                                <div id="fileStatusIcon" class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center transition-colors group-hover:bg-orange-100 group-hover:text-orange-600 shadow-inner">
                                    <i class="fas fa-file-pdf text-lg"></i>
                                </div>
                                <span id="fileNameDisplay" class="text-xs font-bold text-slate-500 truncate max-w-[240px]">Klik untuk mengunggah berkas PDF</span>
                            </div>
                            <span class="bg-slate-900 text-white text-[9px] font-black px-4 py-2.5 rounded-xl shadow-lg shrink-0 uppercase tracking-widest group-hover:bg-orange-600 transition-colors">Telusuri</span>
                            <input type="file" name="file_upload" id="fileInput" class="hidden" accept=".pdf" onchange="handleFileSelect(this)">
                        </label>
                    </div>
                </div>

                <div class="p-8 md:p-10 border-t border-slate-100 bg-white/95 backdrop-blur-xl shrink-0 flex flex-col sm:flex-row gap-4">
                    <button type="submit" class="flex-[2] bg-orange-600 hover:bg-orange-700 text-white font-black py-4 rounded-[1.25rem] shadow-2xl shadow-orange-600/30 transition active:scale-95 uppercase tracking-[0.15em] text-xs">
                        Kirim Permohonan
                    </button>
                    <button type="button" onclick="closeModal()" class="flex-1 bg-slate-100 text-slate-400 hover:text-slate-600 hover:bg-slate-200 font-black py-4 rounded-[1.25rem] transition active:scale-95 uppercase tracking-[0.15em] text-xs">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL BATALKAN -->
    <div id="cancelModal" class="fixed inset-0 z-[210] hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-md" onclick="closeCancelModal()"></div>
        <div class="relative bg-white rounded-[2.5rem] p-10 max-w-sm w-full shadow-2xl text-center animate-modal border border-white/20">
            <div class="w-20 h-20 bg-rose-50 text-rose-500 rounded-3xl flex items-center justify-center mx-auto mb-6 border border-rose-100 relative shadow-inner">
                <i class="fas fa-trash-alt text-3xl"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-800 mb-3 tracking-tight">Batalkan Pengajuan?</h3>
            <p class="text-slate-400 text-xs mb-10 leading-relaxed px-2 font-medium">Data pengajuan untuk sertifikat <span id="cancelInfo" class="font-black text-slate-800 underline decoration-orange-500 decoration-2"></span> akan dihapus permanen dari antrean.</p>
            <form id="cancelForm" method="POST" class="space-y-3">
                @csrf @method('DELETE')
                <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-black py-4 rounded-2xl shadow-xl shadow-rose-600/30 transition active:scale-95 uppercase tracking-widest text-xs">Ya, Batalkan</button>
                <button type="button" onclick="closeCancelModal()" class="w-full text-slate-400 hover:text-slate-600 font-black py-2.5 transition text-[10px] uppercase tracking-widest">Kembali</button>
            </form>
        </div>
    </div>

    <script>
        // === LOGIKA SIDEBAR RESPONSIVE ===
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                setTimeout(() => {
                    overlay.classList.remove('opacity-0');
                    overlay.classList.add('opacity-100');
                }, 10);
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.remove('opacity-100');
                overlay.classList.add('opacity-0');
                setTimeout(() => { overlay.classList.add('hidden'); }, 300);
            }
        }

        // === LOGIKA CHART ===
        document.addEventListener('DOMContentLoaded', function () {
            var options = {
                series: [{
                    name: 'Pengajuan',
                    data: @json($chartData)
                }],
                chart: {
                    type: 'area',
                    height: 250,
                    toolbar: { show: false },
                    fontFamily: 'Plus Jakarta Sans, sans-serif',
                },
                colors: ['#ea580c'], // Menggunakan warna orange-600
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

        // === FUNGSI TAMBAHAN SINAR-X ===
        function toggleReason(id) {
            const p = document.getElementById('reason-' + id);
            const btn = document.getElementById('btn-' + id);
            const icon = document.getElementById('icon-' + id);
            p.classList.toggle('line-clamp-2');
            btn.querySelector('span').innerText = p.classList.contains('line-clamp-2') ? 'Lihat Detail' : 'Sembunyikan';
            icon?.classList.toggle('rotate-180');
        }

        function handleFileSelect(input) {
            const fileNameDisplay = document.getElementById('fileNameDisplay');
            const fileStatusIcon = document.getElementById('fileStatusIcon');
            const fileDropZone = document.getElementById('fileDropZone');
            if (input.files && input.files.length > 0) {
                fileNameDisplay.innerText = input.files[0].name;
                fileNameDisplay.classList.add('text-orange-600', 'font-black');
                fileStatusIcon.innerHTML = '<i class="fas fa-check"></i>';
                fileStatusIcon.classList.add('bg-emerald-100', 'text-emerald-600', 'border-emerald-200');
                fileDropZone.classList.add('border-emerald-500', 'bg-emerald-50/30');
            }
        }

        function openModal(mode, id = null, no_sertif = '', no_reg = '', alasan = '', nomor_surat = '', bagian = '', salah = '', benar = '') {
            const modal = document.getElementById('submissionModal');
            const form = document.getElementById('submissionForm');
            const methodField = document.getElementById('methodField');

            document.getElementById('inputNoSertif').value = no_sertif;
            document.getElementById('inputNoRegistrasi').value = no_reg;
            document.getElementById('inputAlasan').value = alasan;
            document.getElementById('inputNomorSurat').value = nomor_surat;
            document.getElementById('inputBagian').value = bagian;
            document.getElementById('inputSalah').value = salah;
            document.getElementById('inputBenar').value = benar;

            modal.classList.remove('hidden');
            const modalBody = modal.querySelector('.overflow-y-auto');
            if(modalBody) modalBody.scrollTop = 0;

            if (mode === 'add') {
                document.getElementById('modalTitle').innerText = 'Pengajuan Amandemen';
                form.action = "{{ url('/sinarx/submission') }}";
                methodField.innerHTML = ''; 
            } else {
                document.getElementById('modalTitle').innerText = 'Perbaiki Amandemen';
                form.action = "{{ url('/sinarx/submission') }}/" + id;
                methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            }
        }

        function closeModal() { document.getElementById('submissionModal').classList.add('hidden'); }
        
        function openCancelModal(id, no) {
            document.getElementById('cancelInfo').innerText = no;
            document.getElementById('cancelForm').action = "{{ url('/sinarx/submission') }}/" + id;
            document.getElementById('cancelModal').classList.remove('hidden');
            document.getElementById('cancelModal').classList.add('flex');
        }
        
        function closeCancelModal() { 
            document.getElementById('cancelModal').classList.add('hidden'); 
            document.getElementById('cancelModal').classList.remove('flex');
        }
    </script>
</body>
</html>