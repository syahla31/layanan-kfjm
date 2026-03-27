<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Disetujui | Admin Pelatihan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .table-row-hover:hover td { background-color: #f8fafc; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none;  scrollbar-width: none; }
        
        /* Custom Scrollbar for Modal */
        .modal-scroll::-webkit-scrollbar { width: 6px; }
        .modal-scroll::-webkit-scrollbar-track { background: #f1f5f9; }
        .modal-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .modal-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <!-- FETCH DATA & STATS -->
    @php
        use App\Models\Submission;
        use Illuminate\Http\Request;
        
        $filterType = request('filter_type', 'Semua');

        $query = Submission::with(['user', 'files'])
                        ->where('category', 'pelatihan')
                        ->where('status', 'approved')
                        ->whereIn('type', ['KAK', 'Laporan Kinerja']);

        if ($filterType && $filterType !== 'Semua') {
            $query->where('type', $filterType);
        }

        $histories = $query->orderBy('updated_at', 'desc')->get();

        // Statistik Widgets
        $totalApproved = Submission::where('category', 'pelatihan')->where('status', 'approved')->whereIn('type', ['KAK', 'Laporan Kinerja'])->count();
        $totalKAK = Submission::where('category', 'pelatihan')->where('status', 'approved')->where('type', 'KAK')->count();
        $totalLapkin = Submission::where('category', 'pelatihan')->where('status', 'approved')->where('type', 'Laporan Kinerja')->count();

        // Label Map untuk Dropdown
        $filterLabel = [
            'Semua' => 'Semua Dokumen',
            'KAK' => 'Kerangka Acuan Kerja',
            'Laporan Kinerja' => 'Laporan Kinerja'
        ];
        $currentLabel = $filterLabel[$filterType] ?? 'Semua Dokumen';
    @endphp

    <div class="flex h-screen overflow-hidden">
        
        <!-- SIDEBAR DESKTOP -->
        <div class="hidden md:flex h-full bg-blue-900">
            @include('components.pelatihan-sidebar')
        </div>

        <!-- MOBILE SIDEBAR OVERLAY -->
        <div id="mobileSidebar" class="fixed inset-0 z-50 hidden">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="toggleSidebar()"></div>
            <div class="absolute left-0 top-0 bottom-0 w-64 bg-blue-900 shadow-xl transform transition-transform duration-300">
                @include('components.pelatihan-sidebar')
            </div>
        </div>

        <div class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50 relative w-full">
            
            <!-- MOBILE HEADER (Identik dengan Survailen) -->
            <div class="lg:hidden bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between sticky top-0 z-30 shadow-sm">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="p-2 text-slate-600 hover:text-teal-600 hover:bg-slate-100 rounded-lg transition-colors">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <span class="font-bold text-slate-800 text-sm tracking-wide">SI-MUTU <span class="text-teal-600">DKKN</span></span>
                </div>
                <div class="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center text-teal-600 text-xs font-bold border border-teal-200">
                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                </div>
            </div>

            <!-- HEADER DESKTOP -->
            <div class="hidden md:block">
                @include('components.pelatihan-header', [
                    'title' => 'Arsip Dokumen Pelatihan',
                    'subtitle' => 'Manajemen dan pemantauan dokumen yang telah disetujui'
                ])
            </div>

            <!-- MAIN CONTENT -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-6 lg:p-8 space-y-6">
                
                <!-- STATISTIK WIDGETS (SURVAILEN STYLE) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
                    <!-- Total Approved -->
                    <div class="relative bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:-translate-y-1 transition-transform duration-300 group overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <i class="fas fa-check-double text-6xl text-blue-500"></i>
                        </div>
                        <div class="relative z-10">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="bg-blue-100 text-blue-600 px-2 py-1 rounded-md text-xs font-bold uppercase tracking-wider">Total Arsip</span>
                            </div>
                            <h2 class="text-4xl font-bold text-slate-800">{{ $totalApproved }}</h2>
                            <p class="text-xs text-slate-400 mt-2">Dokumen disetujui tersimpan</p>
                        </div>
                    </div>

                    <!-- KAK Approved -->
                    <div class="relative bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:-translate-y-1 transition-transform duration-300 group overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <i class="fas fa-project-diagram text-6xl text-indigo-500"></i>
                        </div>
                        <div class="relative z-10">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="bg-indigo-100 text-indigo-600 px-2 py-1 rounded-md text-xs font-bold uppercase tracking-wider">KAK</span>
                            </div>
                            <h2 class="text-4xl font-bold text-slate-800">{{ $totalKAK }}</h2>
                            <p class="text-xs text-slate-400 mt-2">Kerangka Acuan Kerja</p>
                        </div>
                    </div>

                    <!-- Lapkin Approved -->
                    <div class="relative bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:-translate-y-1 transition-transform duration-300 group overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <i class="fas fa-file-invoice text-6xl text-teal-500"></i>
                        </div>
                        <div class="relative z-10">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="bg-teal-100 text-teal-600 px-2 py-1 rounded-md text-xs font-bold uppercase tracking-wider">Lapkin</span>
                            </div>
                            <h2 class="text-4xl font-bold text-slate-800">{{ $totalLapkin }}</h2>
                            <p class="text-xs text-slate-400 mt-2">Laporan Kinerja Bulanan</p>
                        </div>
                    </div>
                </div>

                <!-- TABLE CARD (SURVAILEN STYLE) -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 bg-white flex flex-col md:flex-row justify-between items-center gap-4">
                        <div>
                            <h3 class="font-bold text-slate-800 text-lg">Daftar Dokumen Disetujui</h3>
                            <p class="text-sm text-slate-500 mt-0.5">Riwayat KAK dan Laporan Kinerja yang telah melalui verifikasi</p>
                        </div>
                        
                        <!-- FILTER DROPDOWN -->
                        <form id="filterForm" method="GET" action="{{ url()->current() }}" class="relative min-w-[240px] z-30">
                            <input type="hidden" name="filter_type" id="filterInput" value="{{ $filterType }}">
                            <button type="button" onclick="toggleDropdown()" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 shadow-sm hover:border-blue-400 transition-all flex items-center justify-between group focus:outline-none focus:ring-2 focus:ring-blue-100">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-filter text-sm text-slate-400 group-hover:text-blue-500 transition-colors"></i>
                                    <span class="text-sm font-bold text-slate-700">{{ $currentLabel }}</span>
                                </div>
                                <i class="fas fa-chevron-down text-xs text-slate-400 transition-transform duration-200" id="dropdownArrow"></i>
                            </button>

                            <!-- Dropdown Menu -->
                            <div id="dropdownMenu" class="absolute right-0 top-full mt-2 w-full bg-white rounded-xl shadow-xl border border-slate-100 hidden transform origin-top scale-95 opacity-0 transition-all duration-200 overflow-hidden">
                                <div class="p-1.5 flex flex-col gap-1">
                                    <div onclick="selectFilter('Semua')" class="px-3 py-2.5 rounded-lg cursor-pointer flex items-center gap-3 text-sm font-medium transition-colors {{ $filterType == 'Semua' ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                        <i class="fas fa-layer-group w-4 text-center {{ $filterType == 'Semua' ? 'text-blue-500' : 'text-slate-400' }}"></i>
                                        <span class="flex-1">Semua Dokumen</span>
                                        @if($filterType == 'Semua') <i class="fas fa-check text-blue-500 text-xs"></i> @endif
                                    </div>
                                    <div onclick="selectFilter('KAK')" class="px-3 py-2.5 rounded-lg cursor-pointer flex items-center gap-3 text-sm font-medium transition-colors {{ $filterType == 'KAK' ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                        <i class="fas fa-project-diagram w-4 text-center {{ $filterType == 'KAK' ? 'text-indigo-500' : 'text-slate-400' }}"></i>
                                        <span class="flex-1">Kerangka Acuan Kerja</span>
                                        @if($filterType == 'KAK') <i class="fas fa-check text-indigo-500 text-xs"></i> @endif
                                    </div>
                                    <div onclick="selectFilter('Laporan Kinerja')" class="px-3 py-2.5 rounded-lg cursor-pointer flex items-center gap-3 text-sm font-medium transition-colors {{ $filterType == 'Laporan Kinerja' ? 'bg-teal-50 text-teal-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                        <i class="fas fa-file-invoice w-4 text-center {{ $filterType == 'Laporan Kinerja' ? 'text-teal-500' : 'text-slate-400' }}"></i>
                                        <span class="flex-1">Laporan Kinerja</span>
                                        @if($filterType == 'Laporan Kinerja') <i class="fas fa-check text-teal-500 text-xs"></i> @endif
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="overflow-x-auto no-scrollbar">
                        <table class="w-full text-sm text-left text-slate-600">
                            <thead class="text-[11px] text-slate-400 uppercase bg-slate-50/80 border-b border-slate-100">
                                <tr>
                                    <th class="px-5 py-3 font-semibold tracking-wider">Lembaga</th>
                                    <th class="px-5 py-3 font-semibold tracking-wider">Dokumen</th>
                                    <th class="px-5 py-3 font-semibold tracking-wider text-center">Tgl Disetujui</th>
                                    <th class="px-5 py-3 font-semibold tracking-wider text-center">File</th>
                                    <th class="px-5 py-3 font-semibold tracking-wider text-center">Jejak</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse($histories as $item)
                                <tr class="table-row-hover transition-colors group">

                                    {{-- Lembaga --}}
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 text-xs font-bold shrink-0">
                                                {{ substr($item->user->name ?? '?', 0, 1) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-semibold text-slate-700 text-xs truncate max-w-[140px]">{{ $item->user->name ?? 'Unknown' }}</p>
                                                <p class="text-[10px] text-slate-400 font-mono">{{ $item->user->kode_instansi ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Dokumen --}}
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-2">
                                            @if($item->type == 'KAK')
                                                <span class="bg-indigo-50 text-indigo-600 border border-indigo-100 text-[10px] px-1.5 py-0.5 rounded font-bold shrink-0">KAK</span>
                                            @else
                                                <span class="bg-teal-50 text-teal-600 border border-teal-100 text-[10px] px-1.5 py-0.5 rounded font-bold shrink-0">LAPKIN</span>
                                            @endif
                                            <span class="text-slate-700 font-medium text-xs truncate max-w-[200px]" title="{{ $item->title }}">{{ $item->title }}</span>
                                        </div>
                                    </td>

                                    {{-- Tanggal --}}
                                    <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                        <p class="text-xs font-semibold text-slate-600">{{ $item->updated_at->format('d M Y') }}</p>
                                        <p class="text-[10px] text-slate-400">{{ $item->updated_at->format('H:i') }} WIB</p>
                                    </td>

                                    {{-- File + SK --}}
                                    <td class="px-5 py-3.5 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank"
                                                class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-500 hover:text-indigo-600 bg-slate-50 hover:bg-indigo-50 border border-slate-100 hover:border-indigo-200 px-2.5 py-1.5 rounded-lg transition-all"
                                                title="Dokumen Final">
                                                <i class="far fa-file-pdf text-rose-400"></i> PDF
                                            </a>
                                            @if($item->admin_file)
                                                <a href="{{ asset('storage/' . $item->admin_file) }}" target="_blank"
                                                    class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-600 hover:text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-100 px-2.5 py-1.5 rounded-lg transition-all"
                                                    title="Unduh SK">
                                                    <i class="fas fa-certificate text-xs"></i> SK
                                                </a>
                                            @else
                                                <span class="text-[10px] text-emerald-500 font-semibold bg-emerald-50 px-2 py-1 rounded-lg border border-emerald-100">
                                                    Selesai
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Jejak --}}
                                    <td class="px-5 py-3.5 text-center">
                                        <button onclick='openHistoryModal(@json($item->files ?? []), "{{ $item->status }}", "{{ $item->title }}")'
                                            class="w-7 h-7 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all flex items-center justify-center mx-auto"
                                            title="Lihat Jejak">
                                            <i class="fas fa-history text-sm"></i>
                                        </button>
                                    </td>

                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-3 border border-slate-100">
                                                <i class="fas fa-folder-open text-2xl text-slate-300"></i>
                                            </div>
                                            <p class="text-slate-700 font-semibold text-sm">Tidak ada arsip</p>
                                            <p class="text-slate-400 text-xs mt-1">Belum ada dokumen disetujui untuk kategori ini.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                <div class="mt-8 text-center text-xs text-slate-400">
                    &copy; 2026 Sistem Informasi Jaminan Mutu Ketenaganukliran
                </div>
                
            </main>
        </div>
    </div>

    <!-- MODAL HISTORY (PRO TIMELINE - SURVAILEN STYLE) -->
    <div id="historyModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeHistoryModal()"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-3xl border border-slate-100">
                    
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-slate-800 to-slate-900 px-6 py-5 flex justify-between items-center text-white shadow-md relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-5 rounded-full -mr-10 -mt-10 pointer-events-none"></div>
                        <div>
                            <h3 class="text-xl font-bold flex items-center gap-3">
                                <div class="bg-white/10 p-2 rounded-lg backdrop-blur-sm"><i class="fas fa-history text-lg"></i></div>
                                Jejak Verifikasi
                            </h3>
                            <p class="text-xs text-slate-300 mt-1 opacity-90" id="historyTitle">Detail perjalanan dokumen</p>
                        </div>
                        <button onclick="closeHistoryModal()" class="text-slate-400 hover:text-white bg-white/10 hover:bg-white/20 rounded-xl p-2 transition-all active:scale-95 z-10">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>

                    <!-- Timeline Body -->
                    <div class="max-h-[65vh] overflow-y-auto bg-slate-50 modal-scroll">
                        <div id="timelineContainer" class="px-6 py-8 relative">
                            <!-- JS Injection -->
                        </div>
                    </div>

                    <div class="bg-white px-6 py-4 flex justify-end border-t border-slate-200 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] relative z-20">
                         <button onclick="closeHistoryModal()" class="px-6 py-2.5 bg-white border border-slate-300 text-slate-700 text-sm rounded-xl hover:bg-slate-50 font-bold shadow-sm transition-colors">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // --- DROPDOWN LOGIC ---
        function toggleDropdown() {
            const menu = document.getElementById('dropdownMenu');
            const arrow = document.getElementById('dropdownArrow');
            
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                setTimeout(() => {
                    menu.classList.remove('opacity-0', 'scale-95');
                    menu.classList.add('opacity-100', 'scale-100');
                }, 10);
                arrow.style.transform = 'rotate(180deg)';
            } else {
                closeDropdown();
            }
        }

        function closeDropdown() {
            const menu = document.getElementById('dropdownMenu');
            const arrow = document.getElementById('dropdownArrow');
            menu.classList.remove('opacity-100', 'scale-100');
            menu.classList.add('opacity-0', 'scale-95');
            setTimeout(() => {
                menu.classList.add('hidden');
            }, 200);
            arrow.style.transform = 'rotate(0deg)';
        }

        function selectFilter(type) {
            document.getElementById('filterInput').value = type;
            document.getElementById('filterForm').submit();
        }

        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('filterForm');
            if (dropdown && !dropdown.contains(event.target)) {
                closeDropdown();
            }
        });

        function toggleSidebar() {
            const sidebar = document.getElementById('mobileSidebar');
            if (sidebar.classList.contains('hidden')) {
                sidebar.classList.remove('hidden');
            } else {
                sidebar.classList.add('hidden');
            }
        }

        // --- HISTORY LOGIC ---
        function openHistoryModal(files, currentStatus, docTitle) {
            const container = document.getElementById('timelineContainer');
            document.getElementById('historyTitle').innerText = "Arsip: " + docTitle;
            container.innerHTML = ''; 
            
            if(!files || files.length === 0) {
                container.innerHTML = `
                    <div class="flex flex-col items-center justify-center py-10 text-slate-400">
                        <i class="far fa-folder-open text-4xl mb-3 opacity-50"></i>
                        <p class="text-sm">Riwayat tidak tersedia.</p>
                    </div>`;
            } else {
                files.sort((a, b) => a.version - b.version);
                
                let cleanFiles = [];
                files.forEach((file) => {
                    if (cleanFiles.length > 0) {
                        let lastClean = cleanFiles[cleanFiles.length - 1];
                        if (file.file_path === lastClean.file_path && file.file_name === lastClean.file_name) {
                            if (file.admin_note) lastClean.admin_note = file.admin_note;
                            if (file.admin_file) lastClean.admin_file = file.admin_file;
                            return;
                        }
                    }
                    cleanFiles.push({...file}); 
                });

                cleanFiles.forEach((file, index) => {
                    const d = new Date(file.created_at);
                    const dateStr = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
                    
                    let isLatest = (index === cleanFiles.length - 1);
                    let isStart = (file.version == 0);

                    let versionLabel, colorClass, actionTitle, userFileHTML, adminFeedbackHTML;

                    if (isStart) {
                        versionLabel = '<i class="fas fa-flag"></i>';
                        colorClass = 'bg-amber-100 text-amber-600 ring-4 ring-amber-50';
                        actionTitle = "Inisiasi / Awal Dokumen";
                        userFileHTML = '';
                        if(file.admin_file) {
                            userFileHTML = `
                                <div class="mt-2 bg-amber-50 rounded-lg p-3 border border-amber-100 flex items-start gap-3">
                                    <div class="bg-white p-2 rounded-md shadow-sm text-red-500"><i class="fas fa-file-pdf text-lg"></i></div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-700 mb-0.5">Surat Referensi</p>
                                        <a href="/storage/${file.admin_file}" target="_blank" class="text-[11px] text-blue-600 hover:underline font-medium">Lihat Dokumen</a>
                                    </div>
                                </div>`;
                        }
                    } else {
                        versionLabel = `v${file.version}`;
                        colorClass = isLatest ? 'bg-blue-600 text-white ring-4 ring-blue-100 shadow-md' : 'bg-white border-2 border-slate-200 text-slate-500';
                        actionTitle = "Respon Lembaga";

                        userFileHTML = `
                            <div class="mt-2 flex items-center gap-3 group/file cursor-pointer" onclick="window.open('/storage/${file.file_path}', '_blank')">
                                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 group-hover/file:bg-blue-100 group-hover/file:scale-110 transition-all">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-slate-700 group-hover/file:text-blue-700 transition-colors">${file.file_name || 'File Lembaga'}</p>
                                    <p class="text-[10px] text-slate-400 font-mono">Verifikasi Selesai</p>
                                </div>
                            </div>
                            ${file.user_note ? `<div class="mt-2 ml-1 text-xs text-slate-500 italic pl-3 border-l-2 border-slate-200">"${file.user_note}"</div>` : ''}
                        `;

                        let badgeHTML = '';
                        if (isLatest) {
                            badgeHTML = `<span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border border-emerald-200">DISETUJUI (FINAL)</span>`;
                        } else {
                            badgeHTML = `<span class="bg-slate-200 text-slate-600 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">REVISI</span>`;
                        }

                        if (badgeHTML || file.admin_note || file.admin_file) {
                            adminFeedbackHTML = `
                                <div class="mt-4 pt-3 border-t border-slate-100 relative">
                                    <div class="absolute -top-2 left-4 bg-slate-50 px-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Respon Admin</div>
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-xs font-bold text-slate-700">Status</span>
                                        ${badgeHTML}
                                    </div>
                                    ${file.admin_note ? `<div class="bg-yellow-50/50 border border-yellow-100 rounded-lg p-3 text-xs text-slate-700 mb-2"><i class="fas fa-comment-alt text-yellow-500 mr-1.5"></i> "${file.admin_note}"</div>` : ''}
                                    ${file.admin_file ? `<a href="/storage/${file.admin_file}" target="_blank" class="flex items-center gap-2 text-xs font-bold text-blue-600 bg-blue-50/50 hover:bg-blue-50 p-2 rounded-lg transition-colors border border-blue-100/50"><i class="fas fa-paperclip"></i> Lampiran Final (SK)</a>` : ''}
                                </div>
                            `;
                        }
                    }

                    const itemHTML = `
                        <div class="relative flex gap-6 pb-8 last:pb-0">
                            <div class="absolute top-0 left-4 -bottom-8 w-0.5 bg-slate-200 last:hidden"></div>
                            <div class="relative z-10 flex-shrink-0 w-8 h-8 rounded-full ${colorClass} flex items-center justify-center border-2 border-white shadow-sm">
                                <span class="text-[10px] font-bold">${versionLabel}</span>
                            </div>
                            <div class="flex-1 bg-white rounded-xl p-4 border border-slate-200 shadow-sm relative hover:shadow-md transition-all duration-300">
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-xs font-bold text-slate-700 flex items-center gap-2">${actionTitle}</span> 
                                    <span class="text-[10px] text-slate-400 bg-slate-50 px-2 py-1 rounded-full border border-slate-100 font-mono">${dateStr}</span>
                                </div>
                                ${userFileHTML || ''}
                                ${adminFeedbackHTML || ''}
                            </div>
                        </div>
                    `;
                    container.innerHTML += itemHTML;
                });
            }
            document.getElementById('historyModal').classList.remove('hidden');
        }
        function closeHistoryModal() { document.getElementById('historyModal').classList.add('hidden'); }
    </script>
</body>
</html>