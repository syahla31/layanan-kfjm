<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Lembaga | SI-SINAR X</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: {
                        primary: '#ea580c',
                        orangeMain: '#c2410c',
                    },
                    animation: {
                        'float': 'float 4s ease-in-out infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-5px)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(12px);
        }

        .row-item:hover { background-color: #fffaf8; }
        .stat-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border: 1px solid #f1f5f9; }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 30px -10px rgba(249, 115, 22, 0.15);
            border-color: #ffedd5;
        }

        .modal-overlay {
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(5px);
        }
        
        .avatar-ring {
            box-shadow: 0 0 0 4px rgba(234, 88, 12, 0.1), 0 0 20px rgba(234, 88, 12, 0.2);
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 antialiased overflow-hidden text-[13px] md:text-sm selection:bg-orange-100 selection:text-orange-900">

    @php
        use App\Models\User;
        use Illuminate\Support\Facades\Auth;
        
        if (!isset($users)) {
            $users = User::where('role', 'user')->where('category', 'sinarx')->orderBy('name', 'asc')->get();
        }
        $totalLembaga = $users->count();
    @endphp

    <div class="flex h-screen overflow-hidden bg-slate-50 relative">

        <!-- === MOBILE OVERLAY === -->
        <div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 z-40 hidden lg:hidden modal-overlay transition-opacity duration-300 opacity-0"></div>

        <!-- === SIDEBAR WRAPPER === -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-2xl lg:shadow-none transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto flex flex-col h-full border-r border-slate-200">
            @include('components.sinarx-sidebar')
        </aside>

        <!-- WRAPPER KONTEN -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden relative w-full">

            <!-- === HEADER MOBILE === -->
            <div class="lg:hidden bg-white/90 backdrop-blur-md border-b border-slate-200 px-5 py-3 flex items-center justify-between z-20 sticky top-0 shadow-sm">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="p-2 -ml-2 text-slate-500 hover:text-orange-600 hover:bg-slate-100 rounded-lg transition-colors">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                    <span class="font-bold text-slate-800 text-xs tracking-wide uppercase">SI-SINAR X</span>
                </div>
                <div class="w-8 h-8 rounded-full bg-orange-600 flex items-center justify-center text-white text-[10px] font-bold">
                    {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                </div>
            </div>

            <!-- === HEADER DESKTOP === -->
            <div class="hidden lg:block sticky top-0 z-10 bg-white/80 backdrop-blur-md border-b border-slate-200">
                @include('components.sinarx-header', [
                    'title' => 'Data Lembaga',
                    'subtitle' => 'Manajemen Database Unit Layanan Sinar-X'
                ])
            </div>

            <main class="flex-1 overflow-y-auto p-5 md:p-8 space-y-7 no-scrollbar scroll-smooth">

                <!-- STATS & SEARCH SECTION -->
                <div class="flex flex-col xl:flex-row gap-6 items-center"> <!-- items-center agar sejajar lurus secara vertikal -->
                    <!-- Stat Cards Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 w-full xl:w-auto xl:min-w-[500px]">
                        <div class="bg-white p-5 rounded-[2.2rem] shadow-sm flex items-center gap-4 stat-card h-[88px]"> <!-- Tinggi tetap ditambahkan untuk presisi -->
                            <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center shrink-0">
                                <i class="fas fa-hospital text-lg"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Total Lembaga</p>
                                <h3 class="text-xl font-black text-slate-800">{{ $totalLembaga }} <span class="text-[10px] text-slate-300 ml-1">UNIT</span></h3>
                            </div>
                        </div>

                        <div class="bg-white p-5 rounded-[2.2rem] shadow-sm flex items-center gap-4 stat-card h-[88px]">
                            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center shrink-0">
                                <i class="fas fa-check-double text-lg"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Koneksi Sistem</p>
                                <div class="flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span class="text-[11px] font-bold text-emerald-600">Terhubung</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Search Bar Fixed Alignment -->
                    <div class="relative group w-full xl:flex-1 flex items-center h-[88px]"> <!-- Kontainer luar disesuaikan tingginya -->
                        <div class="absolute inset-y-0 left-0 flex items-center pl-6 pointer-events-none text-slate-300 group-focus-within:text-orange-500 transition-colors z-10">
                            <i class="fas fa-search text-base"></i>
                        </div>
                        <input type="text" id="searchInput" onkeyup="filterTable()"
                            class="block w-full pl-14 pr-6 bg-white border border-slate-200 rounded-[1.8rem] text-xs font-bold focus:outline-none focus:border-orange-500 focus:ring-4 focus:ring-orange-500/5 transition-all shadow-sm placeholder:text-slate-300 h-full"
                            placeholder="Cari nama instansi atau kode unit...">
                    </div>
                </div>

                <!-- TABLE SECTION -->
                <div class="bg-white border border-slate-200 rounded-[2.5rem] shadow-sm overflow-hidden mb-10">
                    <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-7 bg-orange-600 rounded-full"></div>
                            <h3 class="font-black text-base text-slate-800 tracking-tight">Database Unit Terdaftar</h3>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto no-scrollbar">
                        <table class="w-full text-xs text-left text-slate-600 min-w-[800px]" id="lembagaTable">
                            <thead class="text-[9px] text-slate-400 uppercase bg-slate-50/50 border-b border-slate-100 font-black tracking-[0.2em]">
                                <tr>
                                    <th class="px-10 py-5 w-20 text-center">ID</th>
                                    <th class="px-6 py-5">Identitas Lembaga</th>
                                    <th class="px-6 py-5 text-center">Kode Unik</th>
                                    <th class="px-6 py-5 text-center">Status</th>
                                    <th class="px-10 py-5 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($users as $index => $u)
                                    <tr class="row-item transition-all duration-200 group">
                                        <td class="px-10 py-6 text-center text-slate-300 font-mono">{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}</td>
                                        <td class="px-6 py-6">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center text-[11px] font-black uppercase group-hover:bg-orange-600 group-hover:text-white transition-all border border-slate-100 group-hover:border-orange-500">
                                                    {{ substr($u->name, 0, 1) }}
                                                </div>
                                                <span class="font-bold text-slate-800 text-sm tracking-tight group-hover:text-orange-600 transition-colors">{{ $u->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-6 text-center">
                                            <span class="bg-slate-100 text-slate-600 px-3 py-1.5 rounded-lg text-[10px] font-black font-mono border border-slate-200">
                                                {{ $u->kode_instansi ?? '---' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-6 text-center">
                                            <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-600 text-[9px] font-black px-3 py-1.5 rounded-full border border-emerald-100 uppercase tracking-wider">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                            </span>
                                        </td>
                                        <td class="px-10 py-6 text-center">
                                            <button onclick="showDetail('{{ e($u->name) }}', '{{ $u->kode_instansi }}', '{{ $u->email }}', '{{ $u->created_at->format('d M Y') }}')"
                                                class="w-9 h-9 rounded-2xl bg-white border border-slate-200 text-slate-400 hover:text-orange-600 hover:border-orange-200 hover:shadow-lg transition-all flex items-center justify-center active:scale-90 mx-auto">
                                                <i class="fas fa-search-plus text-xs"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-8 py-24 text-center text-slate-400 italic font-medium">Data lembaga belum tersedia di sistem.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="pb-10 text-center text-[10px] text-slate-400 font-bold uppercase tracking-widest opacity-50">
                    &copy; 2026 Sistem Informasi Sinar-X • Dashboard Kendali Pusat
                </div>
            </main>
        </div>
    </div>

    <!-- === MODAL DETAIL === -->
    <div id="detailModal" class="fixed inset-0 z-[200] hidden items-center justify-center p-4 overflow-hidden">
        <div class="absolute inset-0 modal-overlay opacity-0 transition-opacity duration-300" id="modalBackdrop" onclick="closeModal()"></div>
        
        <div class="relative w-full max-w-md opacity-0 transform translate-y-10 transition-all duration-400" id="modalContainer">
            <div class="bg-white rounded-[2.5rem] shadow-[0_20px_50px_-12px_rgba(15,23,42,0.3)] overflow-hidden border border-white/50 glass-card">
                
                <div class="relative bg-gradient-to-br from-[#1e293b] via-[#0f172a] to-[#431407] px-6 py-10 text-center overflow-hidden">
                    <div class="absolute -right-16 -top-16 w-48 h-48 bg-orange-600/10 rounded-full blur-[60px]"></div>
                    
                    <button onclick="closeModal()" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white transition-all flex items-center justify-center border border-white/10 z-20">
                        <i class="fas fa-times text-xs"></i>
                    </button>

                    <div class="flex flex-col items-center gap-4 z-10 relative">
                        <div class="w-5 h-5 rounded-[1.5rem] bg-slate-900/80 backdrop-blur-md flex items-center justify-center text-white text-sm font-black avatar-ring animate-float border border-orange-500/30 shadow-xl shrink-0" id="modalAvatar">
                        </div>
                        
                        <div class="space-y-2">
                            <span class="px-3 py-1 rounded-full bg-[#f97316] text-white text-[8px] font-black uppercase tracking-widest shadow-lg inline-block">Unit Sinar-X</span>
                            <h4 class="text-white text-xl font-black leading-tight tracking-tight break-words" id="modalName">Nama Lembaga</h4>
                        </div>
                    </div>
                </div>

                <div class="p-6 md:p-8 space-y-6">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-100">
                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1.5">ID Instansi</p>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-fingerprint text-orange-500 text-[10px]"></i>
                                <span class="font-black text-slate-800 text-xs font-mono tracking-tighter" id="modalCode">RS-999</span>
                            </div>
                        </div>
                        <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-100">
                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Sertifikasi</p>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-check-circle text-emerald-500 text-[10px]"></i>
                                <span class="font-black text-emerald-600 text-[10px] uppercase tracking-wider">Valid</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 px-1">
                        <div class="flex items-center gap-4 group">
                            <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 shrink-0 group-hover:bg-orange-50 group-hover:text-orange-600 transition-all">
                                <i class="fas fa-envelope text-sm"></i>
                            </div>
                            <div class="grow">
                                <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Email Akses</p>
                                <p class="text-slate-800 font-bold text-xs break-all" id="modalEmail">admin@mail.com</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 group">
                            <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 shrink-0 group-hover:bg-orange-50 group-hover:text-orange-600 transition-all">
                                <i class="fas fa-calendar-alt text-sm"></i>
                            </div>
                            <div class="grow">
                                <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Registrasi</p>
                                <p class="text-slate-800 font-bold text-xs" id="modalJoin">14 Mar 2026</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-emerald-50/50 p-4 rounded-2xl border border-emerald-500/10 flex items-start gap-3">
                        <i class="fas fa-info-circle text-emerald-500 text-[10px] mt-0.5"></i>
                        <p class="text-[10px] text-slate-500 font-medium leading-relaxed italic">
                            Identitas terverifikasi. Lembaga memiliki izin akses amandemen sertifikat elektronik.
                        </p>
                    </div>

                    <button onclick="closeModal()" class="w-full py-4 bg-[#0f172a] hover:bg-orange-600 text-white rounded-[1.2rem] font-black text-[10px] uppercase tracking-[0.4em] transition-all shadow-lg active:scale-95">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                setTimeout(() => { overlay.classList.add('opacity-100'); }, 10);
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.remove('opacity-100');
                setTimeout(() => { overlay.classList.add('hidden'); }, 300);
            }
        }

        function filterTable() {
            const filter = document.getElementById("searchInput").value.toUpperCase();
            const tr = document.getElementById("lembagaTable").getElementsByTagName("tr");
            for (let i = 1; i < tr.length; i++) {
                const tdName = tr[i].getElementsByTagName("td")[1];
                const tdCode = tr[i].getElementsByTagName("td")[2];
                if (tdName || tdCode) {
                    const txt = (tdName.textContent || tdName.innerText) + (tdCode.textContent || tdCode.innerText);
                    tr[i].style.display = txt.toUpperCase().indexOf(filter) > -1 ? "" : "none";
                }
            }
        }

        function showDetail(name, code, email, joinDate) {
            document.getElementById('modalName').innerText = name;
            document.getElementById('modalCode').innerText = code || '---';
            document.getElementById('modalEmail').innerText = email;
            document.getElementById('modalJoin').innerText = joinDate;
            document.getElementById('modalAvatar').innerText = name.charAt(0);
            
            const modal = document.getElementById('detailModal');
            const backdrop = document.getElementById('modalBackdrop');
            const container = document.getElementById('modalContainer');

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            setTimeout(() => {
                backdrop.classList.add('opacity-100');
                container.classList.remove('opacity-0', 'translate-y-10');
                container.classList.add('opacity-100', 'translate-y-0');
            }, 20);
        }

        function closeModal() {
            const backdrop = document.getElementById('modalBackdrop');
            const container = document.getElementById('modalContainer');
            container.classList.remove('opacity-100', 'translate-y-0');
            container.classList.add('opacity-0', 'translate-y-10');
            backdrop.classList.remove('opacity-100');
            setTimeout(() => {
                document.getElementById('detailModal').classList.add('hidden');
                document.getElementById('detailModal').classList.remove('flex');
            }, 300);
        }
    </script>
</body>
</html>