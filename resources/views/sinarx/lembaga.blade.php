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
        
        @keyframes modalEntry {
            from { opacity: 0; transform: scale(0.95) translateY(20px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .animate-modal { animation: modalEntry 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .animate-pulse-slow { animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite; }

        .row-item:hover { background-color: #fffaf8; }
        .stat-card { transition: all 0.3s ease; }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(249, 115, 22, 0.1), 0 10px 10px -5px rgba(249, 115, 22, 0.04);
        }
        .glass-overlay {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
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
        <div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 z-40 hidden lg:hidden glass-overlay transition-opacity duration-300 opacity-0"></div>

        <!-- === SIDEBAR WRAPPER (Responsive) === -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-2xl lg:shadow-none transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto flex flex-col h-full border-r border-slate-200">
            @include('components.sinarx-sidebar')
        </aside>

        <!-- WRAPPER KONTEN -->
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
                    {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                </div>
            </div>

            <!-- === HEADER DESKTOP === -->
            <div class="hidden lg:block sticky top-0 z-10 bg-white/80 backdrop-blur-md border-b border-slate-200">
                @include('components.sinarx-header', [
                    'title' => 'Data Lembaga',
                    'subtitle' => 'Manajemen Data Unit Layanan Sinar-X'
                ])
            </div>

            <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-8 space-y-6 md:space-y-8 no-scrollbar scroll-smooth">

                <!-- DASHBOARD STATS & SEARCH -->
                <div class="flex flex-col xl:flex-row justify-between items-stretch gap-4 md:gap-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 grow xl:max-w-2xl">
                        <div class="bg-white p-5 md:p-6 rounded-[2rem] md:rounded-[2.5rem] border border-slate-200 shadow-sm flex items-center gap-4 md:gap-5 stat-card">
                            <div class="w-12 h-12 md:w-14 md:h-14 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center shrink-0">
                                <i class="fas fa-hospital text-lg md:text-xl"></i>
                            </div>
                            <div>
                                <p class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Lembaga</p>
                                <h3 class="text-xl md:text-2xl font-black text-slate-800 tracking-tight">{{ $totalLembaga }} <span class="text-[10px] font-bold text-slate-300 ml-1 uppercase">Unit</span></h3>
                            </div>
                        </div>
                        <div class="bg-white p-5 md:p-6 rounded-[2rem] md:rounded-[2.5rem] border border-slate-200 shadow-sm flex items-center gap-4 md:gap-5 stat-card">
                            <div class="w-12 h-12 md:w-14 md:h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center shrink-0">
                                <i class="fas fa-check-double text-lg md:text-xl"></i>
                            </div>
                            <div>
                                <p class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Status Keaktifan</p>
                                <span class="inline-flex items-center gap-1.5 text-[10px] md:text-xs font-bold text-emerald-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Sinkron Aktif
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="relative group grow xl:max-w-md flex items-center">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-6 md:pl-7 pointer-events-none text-slate-300 group-focus-within:text-orange-500 transition-colors">
                            <i class="fas fa-search text-base md:text-lg"></i>
                        </div>
                        <input type="text" id="searchInput" onkeyup="filterTable()"
                            class="block w-full pl-14 md:pl-16 pr-6 md:pr-8 py-4 md:py-5 bg-white border border-slate-200 rounded-2xl md:rounded-[2rem] text-sm font-bold focus:outline-none focus:border-orange-500 focus:ring-4 focus:ring-orange-500/5 transition-all shadow-sm placeholder:text-slate-300"
                            placeholder="Cari nama atau kode instansi...">
                    </div>
                </div>

                <!-- TABLE SECTION -->
                <div class="bg-white border border-slate-200 rounded-[2rem] md:rounded-[3rem] shadow-sm overflow-hidden mb-12">
                    <div class="px-6 md:px-10 py-6 md:py-8 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-3 md:gap-4">
                            <div class="w-1.5 h-6 md:w-2 md:h-8 bg-orange-600 rounded-full"></div>
                            <h3 class="font-black text-base md:text-xl text-slate-800 tracking-tight">Database Unit Terdaftar</h3>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto no-scrollbar">
                        <table class="w-full text-sm text-left text-slate-600 min-w-[900px]" id="lembagaTable">
                            <thead class="text-[9px] md:text-[10px] text-slate-400 uppercase bg-slate-50/50 border-b border-slate-100 font-black tracking-[0.2em]">
                                <tr>
                                    <th class="px-8 md:px-10 py-5 md:py-6 w-16 text-center">ID</th>
                                    <th class="px-6 py-5 md:py-6">Nama Lembaga</th>
                                    <th class="px-6 py-5 md:py-6 text-center">Kode</th>
                                    <th class="px-6 py-5 md:py-6">Email Akses</th>
                                    <th class="px-6 py-5 md:py-6 text-center">Verifikasi</th>
                                    <th class="px-8 md:px-10 py-5 md:py-6 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($users as $index => $u)
                                    <tr class="row-item transition-all duration-200 group cursor-default">
                                        <td class="px-8 md:px-10 py-5 md:py-6 text-center text-slate-300 font-mono text-xs">{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}</td>
                                        <td class="px-6 py-5 md:py-6">
                                            <div class="flex items-center gap-3 md:gap-4">
                                                <div class="w-10 h-10 md:w-11 md:h-11 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-xs md:text-sm font-black border border-orange-100 shrink-0 uppercase">
                                                    {{ substr($u->name, 0, 1) }}
                                                </div>
                                                <span class="font-black text-slate-800 text-xs md:text-sm tracking-tight group-hover:text-orange-600 transition-colors">{{ $u->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 md:py-6 text-center">
                                            <span class="bg-slate-100 text-slate-600 px-2.5 py-1 rounded-lg text-[9px] md:text-[10px] font-black font-mono border border-slate-200 shadow-inner">
                                                {{ $u->kode_instansi ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-5 md:py-6">
                                            <span class="text-[10px] md:text-xs font-bold text-slate-500 italic">{{ $u->email }}</span>
                                        </td>
                                        <td class="px-6 py-5 md:py-6 text-center">
                                            <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-600 text-[8px] md:text-[9px] font-black px-3 py-1.5 rounded-full border border-emerald-100 uppercase tracking-widest">
                                                <i class="fas fa-certificate text-[8px]"></i> Valid
                                            </span>
                                        </td>
                                        <td class="px-8 md:px-10 py-5 md:py-6 text-center">
                                            <button onclick="showDetail('{{ e($u->name) }}', '{{ $u->kode_instansi }}', '{{ $u->email }}', '{{ $u->created_at->format('d M Y') }}')"
                                                class="w-9 h-9 md:w-10 md:h-10 rounded-xl md:rounded-2xl bg-white border border-slate-200 text-slate-400 hover:text-orange-600 hover:border-orange-200 hover:shadow-lg transition-all flex items-center justify-center active:scale-90 mx-auto">
                                                <i class="fas fa-search-plus text-xs"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="px-8 py-32 text-center text-slate-400 italic font-bold">Data tidak ditemukan.</td></tr>
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

    <!-- MODAL DETAIL -->
    <div id="detailModal" class="fixed inset-0 z-[200] hidden flex items-center justify-center p-4 md:p-6">
        <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-md" onclick="closeModal()"></div>
        <div class="relative bg-white rounded-[2rem] md:rounded-[3rem] max-w-2xl w-full shadow-2xl animate-modal overflow-hidden flex flex-col border border-white/20 max-h-[90vh]">

            <!-- Modal Header -->
            <div class="bg-gradient-to-br from-[#7c2d12] to-[#9a3412] p-6 md:p-10 text-white relative flex flex-col sm:flex-row items-center gap-5 md:gap-8 shrink-0">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>
                
                <div class="w-20 h-20 md:w-24 md:h-24 rounded-2xl md:rounded-[2rem] bg-white/10 backdrop-blur-xl border border-white/20 flex items-center justify-center text-orange-400 text-4xl md:text-5xl font-black shadow-2xl shrink-0 uppercase" id="modalAvatar"></div>
                
                <div class="text-center sm:text-left grow relative z-10">
                    <h4 class="text-lg md:text-2xl font-black tracking-tight leading-tight mb-2 md:mb-2 px-2 sm:px-0" id="modalName">-</h4>
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 md:gap-3">
                        <span class="bg-orange-500 text-white text-[9px] md:text-[10px] px-3 md:px-4 py-1.5 rounded-lg md:rounded-xl font-black uppercase tracking-widest shadow-lg shadow-orange-950/20" id="modalCode">-</span>
                        <span class="bg-white/10 text-orange-100 text-[9px] md:text-[10px] px-3 md:px-4 py-1.5 rounded-lg md:rounded-xl font-black uppercase tracking-widest border border-white/10 backdrop-blur-sm">Terverifikasi</span>
                    </div>
                </div>
                
                <button onclick="closeModal()" class="absolute top-4 right-4 md:top-6 md:right-6 w-8 h-8 md:w-10 md:h-10 rounded-full bg-white/10 hover:bg-white/20 transition flex items-center justify-center border border-white/10"><i class="fas fa-times text-xs"></i></button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 md:p-10 bg-white space-y-5 md:space-y-8 overflow-y-auto no-scrollbar">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div class="p-4 md:p-6 bg-slate-50 rounded-[1.5rem] md:rounded-[2rem] border border-slate-100 space-y-1">
                        <span class="text-[8px] md:text-[9px] font-black text-slate-400 uppercase tracking-widest block">Email Akses Utama</span>
                        <div class="flex items-center gap-3 mt-2">
                            <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-orange-500 border border-slate-100 shadow-sm shrink-0"><i class="fas fa-envelope text-[10px]"></i></div>
                            <span class="text-xs md:text-sm font-black text-slate-800 break-all" id="modalEmail">-</span>
                        </div>
                    </div>
                    <div class="p-4 md:p-6 bg-slate-50 rounded-[1.5rem] md:rounded-[2rem] border border-slate-100 space-y-1">
                        <span class="text-[8px] md:text-[9px] font-black text-slate-400 uppercase tracking-widest block">Tanggal Registrasi</span>
                        <div class="flex items-center gap-3 mt-2">
                            <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-orange-500 border border-slate-100 shadow-sm shrink-0"><i class="fas fa-calendar-check text-[10px]"></i></div>
                            <span class="text-xs md:text-sm font-black text-slate-800" id="modalJoin">-</span>
                        </div>
                    </div>
                </div>

                <div class="p-5 md:p-8 bg-emerald-50/50 rounded-[1.5rem] md:rounded-[2rem] border border-emerald-100 flex flex-col sm:flex-row items-center gap-4 md:gap-6">
                    <div class="w-12 h-12 md:w-16 md:h-16 bg-white rounded-xl md:rounded-2xl flex items-center justify-center text-emerald-500 border border-emerald-100 shadow-sm shrink-0">
                        <i class="fas fa-check-circle text-xl md:text-2xl"></i>
                    </div>
                    <div class="text-center sm:text-left">
                        <h5 class="text-[10px] md:text-sm font-black text-emerald-800 uppercase tracking-tight">Status Validasi</h5>
                        <p class="text-[10px] md:text-xs text-emerald-600 font-bold mt-1 leading-relaxed italic">"Identitas lembaga telah diverifikasi dan aktif untuk amandemen sertifikat."</p>
                    </div>
                </div>

                <div class="flex flex-col gap-3 pt-2">
                    <button onclick="closeModal()" class="w-full bg-slate-900 hover:bg-orange-600 text-white font-black py-4 md:py-5 rounded-2xl text-[10px] md:text-[11px] uppercase tracking-[0.2em] transition-all shadow-xl shadow-slate-200 active:scale-95">Tutup Detail</button>
                </div>
            </div>
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

        // === LOGIKA SEARCH TABLE ===
        function filterTable() {
            var input, filter, table, tr, td, i;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("lembagaTable");
            tr = table.getElementsByTagName("tr");
            for (i = 1; i < tr.length; i++) {
                var tdName = tr[i].getElementsByTagName("td")[1];
                var tdCode = tr[i].getElementsByTagName("td")[2];
                if (tdName || tdCode) {
                    var txtName = tdName.textContent || tdName.innerText;
                    var txtCode = tdCode.textContent || tdCode.innerText;
                    if (txtName.toUpperCase().indexOf(filter) > -1 || txtCode.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }

        // === MODAL CONTROL ===
        function showDetail(name, code, email, joinDate) {
            document.getElementById('modalName').innerText = name;
            document.getElementById('modalCode').innerText = code || 'TIDAK TERDAFTAR';
            document.getElementById('modalEmail').innerText = email;
            document.getElementById('modalJoin').innerText = joinDate;
            document.getElementById('modalAvatar').innerText = name.charAt(0);
            
            const modal = document.getElementById('detailModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            const modal = document.getElementById('detailModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
</body>
</html>