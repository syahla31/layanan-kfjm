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

    <!-- FETCH DATA REAL -->
    @php
        use App\Models\Submission;
        use Illuminate\Http\Request;
        
        $filterType = request('filter_type', 'Semua'); // Default 'Semua'

        $query = Submission::with(['user', 'files'])
                        ->where('category', 'pelatihan')
                        ->where('status', 'approved')
                        ->whereIn('type', ['KAK', 'Laporan Kinerja']);

        if ($filterType && $filterType !== 'Semua') {
            $query->where('type', $filterType);
        }

        $histories = $query->orderBy('updated_at', 'desc')->get();

        // Label Map untuk Dropdown
        $filterLabel = [
            'Semua' => 'Semua Dokumen',
            'KAK' => 'Kerangka Acuan Kerja',
            'Laporan Kinerja' => 'Laporan Kinerja'
        ];
        $currentLabel = $filterLabel[$filterType] ?? 'Semua Dokumen';
    @endphp

    <div class="flex h-screen overflow-hidden">
        
        <!-- SIDEBAR DESKTOP (FIXED WRAPPER) -->
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
            
            <!-- MOBILE HEADER -->
            <div class="md:hidden bg-white border-b border-slate-200 px-4 py-3 flex justify-between items-center z-20">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <span class="font-bold text-slate-800">SI-PELATIHAN</span>
                </div>
                <button onclick="toggleSidebar()" class="text-slate-500 hover:text-blue-600 p-2 rounded-lg hover:bg-slate-100">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>

            <!-- HEADER DESKTOP -->
            <div class="hidden md:block">
                @include('components.pelatihan-header', [
                    'title' => 'Arsip Dokumen',
                    'subtitle' => 'Daftar dokumen KAK & Lapkin yang telah disetujui'
                ])
            </div>

            <!-- MAIN CONTENT -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-6 lg:p-8 space-y-6">
                
                <!-- Filter & Title Section -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">Riwayat Disetujui</h2>
                        <p class="text-sm text-slate-500 mt-1">Total <span class="font-bold text-slate-700">{{ $histories->count() }}</span> dokumen tersimpan</p>
                    </div>
                    
                    <!-- CUSTOM DROPDOWN FILTER (TANPA SELECT BIASA) -->
                    <form id="filterForm" method="GET" action="{{ url()->current() }}" class="relative min-w-[240px] z-30">
                        <input type="hidden" name="filter_type" id="filterInput" value="{{ $filterType }}">
                        
                        <!-- Trigger Button -->
                        <button type="button" onclick="toggleDropdown()" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 shadow-sm hover:border-blue-400 hover:shadow-md transition-all flex items-center justify-between group focus:outline-none focus:ring-2 focus:ring-blue-100">
                            <div class="flex items-center gap-3">
                                <div class="text-slate-400 group-hover:text-blue-500 transition-colors">
                                    <i class="fas fa-filter text-sm"></i>
                                </div>
                                <span class="text-sm font-bold text-slate-700">{{ $currentLabel }}</span>
                            </div>
                            <i class="fas fa-chevron-down text-xs text-slate-400 transition-transform duration-200" id="dropdownArrow"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="dropdownMenu" class="absolute right-0 top-full mt-2 w-full bg-white rounded-xl shadow-xl border border-slate-100 hidden transform origin-top scale-95 opacity-0 transition-all duration-200 overflow-hidden">
                            <div class="p-1.5 flex flex-col gap-1">
                                <!-- Option: Semua -->
                                <div onclick="selectFilter('Semua')" class="px-3 py-2.5 rounded-lg cursor-pointer flex items-center gap-3 text-sm font-medium transition-colors {{ $filterType == 'Semua' ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <i class="fas fa-layer-group w-4 text-center {{ $filterType == 'Semua' ? 'text-blue-500' : 'text-slate-400' }}"></i>
                                    <span class="flex-1">Semua Dokumen</span>
                                    @if($filterType == 'Semua') <i class="fas fa-check text-blue-500 text-xs"></i> @endif
                                </div>

                                <!-- Option: KAK -->
                                <div onclick="selectFilter('KAK')" class="px-3 py-2.5 rounded-lg cursor-pointer flex items-center gap-3 text-sm font-medium transition-colors {{ $filterType == 'KAK' ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <i class="fas fa-project-diagram w-4 text-center {{ $filterType == 'KAK' ? 'text-indigo-500' : 'text-slate-400' }}"></i>
                                    <span class="flex-1">Kerangka Acuan Kerja</span>
                                    @if($filterType == 'KAK') <i class="fas fa-check text-indigo-500 text-xs"></i> @endif
                                </div>

                                <!-- Option: Lapkin -->
                                <div onclick="selectFilter('Laporan Kinerja')" class="px-3 py-2.5 rounded-lg cursor-pointer flex items-center gap-3 text-sm font-medium transition-colors {{ $filterType == 'Laporan Kinerja' ? 'bg-teal-50 text-teal-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <i class="fas fa-file-invoice w-4 text-center {{ $filterType == 'Laporan Kinerja' ? 'text-teal-500' : 'text-slate-400' }}"></i>
                                    <span class="flex-1">Laporan Kinerja</span>
                                    @if($filterType == 'Laporan Kinerja') <i class="fas fa-check text-teal-500 text-xs"></i> @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- TABLE CARD -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden relative z-10">
                    <div class="overflow-x-auto no-scrollbar">
                        <table class="w-full text-sm text-left text-slate-600 min-w-[900px] md:min-w-0">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-6 py-4 font-bold tracking-wider w-40">Tgl Disetujui</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Instansi Pengirim</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Jenis & Judul</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Dokumen Final</th>
                                    <th class="px-6 py-4 font-bold tracking-wider text-center">Surat Balasan</th>
                                    <th class="px-6 py-4 font-bold tracking-wider text-center w-20">Jejak</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($histories as $item)
                                <tr class="table-row-hover transition-colors group">
                                    
                                    <!-- Tanggal -->
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-slate-700">{{ $item->updated_at->format('d M Y') }}</span>
                                            <span class="text-xs text-slate-400 mt-0.5">{{ $item->updated_at->format('H:i') }} WIB</span>
                                        </div>
                                    </td>

                                    <!-- Instansi -->
                                    <td class="px-6 py-5">
                                        <div class="flex items-start gap-3">
                                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 text-xs font-bold shrink-0">
                                                {{ substr($item->user->name ?? '?', 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-800 line-clamp-1">{{ $item->user->name ?? 'User Hapus' }}</div>
                                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $item->user->kode_instansi ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Jenis & Judul -->
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col gap-1.5">
                                            @php
                                                $badgeColor = $item->type == 'KAK' ? 'bg-indigo-100 text-indigo-700 border-indigo-200' : 'bg-teal-100 text-teal-700 border-teal-200';
                                            @endphp
                                            <span class="{{ $badgeColor }} border text-[10px] px-2 py-0.5 rounded font-bold w-fit">{{ $item->type }}</span>
                                            <span class="text-slate-800 font-medium text-sm line-clamp-1" title="{{ $item->title }}">{{ $item->title }}</span>
                                        </div>
                                    </td>

                                    <!-- File Dokumen -->
                                    <td class="px-6 py-5">
                                        <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank" class="flex items-center gap-2 text-xs font-bold text-blue-600 hover:text-blue-800 w-fit group/link transition-colors bg-blue-50 px-3 py-1.5 rounded-lg border border-blue-100 hover:border-blue-300">
                                            <i class="far fa-file-pdf text-red-500"></i>
                                            <span class="group-hover/link:underline">Lihat File</span>
                                        </a>
                                    </td>

                                    <!-- Surat Balasan (SK) -->
                                    <td class="px-6 py-5 text-center">
                                        @if($item->admin_file)
                                            <a href="{{ asset('storage/' . $item->admin_file) }}" target="_blank" class="inline-flex items-center gap-1.5 text-emerald-600 hover:text-emerald-800 font-bold text-xs bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-100 transition-colors">
                                                <i class="fas fa-certificate"></i> Unduh SK
                                            </a>
                                        @else
                                            <span class="text-slate-300 italic text-xs">-</span>
                                        @endif
                                    </td>

                                    <!-- Jejak -->
                                    <td class="px-6 py-5 text-center">
                                        <button onclick='openHistoryModal(@json($item->files ?? []), "{{ $item->status }}", "{{ $item->title }}")' class="text-slate-400 hover:text-blue-600 p-2 rounded-full hover:bg-blue-50 transition-all" title="Lihat Perjalanan Dokumen">
                                            <i class="fas fa-history text-lg"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4 border border-slate-100">
                                                <i class="fas fa-folder-open text-3xl text-slate-300"></i>
                                            </div>
                                            <h3 class="text-slate-800 font-bold">Tidak ada arsip</h3>
                                            <p class="text-slate-500 text-sm mt-1">Belum ada dokumen yang disetujui sesuai filter ini.</p>
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

    <!-- MODAL HISTORY (PRO TIMELINE) -->
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
        // --- CUSTOM DROPDOWN LOGIC ---
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

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('filterForm');
            if (!dropdown.contains(event.target)) {
                closeDropdown();
            }
        });

        // --- SIDEBAR & MODAL LOGIC ---
        function toggleSidebar() {
            const sidebar = document.getElementById('mobileSidebar');
            if (sidebar.classList.contains('hidden')) {
                sidebar.classList.remove('hidden');
            } else {
                sidebar.classList.add('hidden');
            }
        }

        function openHistoryModal(files, currentStatus, docTitle) {
            const container = document.getElementById('timelineContainer');
            document.getElementById('historyTitle').innerText = "Dokumen: " + docTitle;
            container.innerHTML = ''; 
            
            if(!files || files.length === 0) {
                container.innerHTML = `
                    <div class="flex flex-col items-center justify-center py-10 text-slate-400">
                        <i class="far fa-folder-open text-4xl mb-3 opacity-50"></i>
                        <p class="text-sm">Riwayat tidak tersedia (Versi tunggal).</p>
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
                        actionTitle = "Inisiasi / Awal";
                        userFileHTML = '';
                        if(file.admin_file) {
                            userFileHTML = `
                                <div class="mt-2 bg-amber-50 rounded-lg p-3 border border-amber-100 flex items-start gap-3">
                                    <div class="bg-white p-2 rounded-md shadow-sm text-red-500"><i class="fas fa-file-pdf text-lg"></i></div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-700 mb-0.5">Surat Admin</p>
                                        <a href="/storage/${file.admin_file}" target="_blank" class="text-[11px] text-blue-600 hover:underline font-medium">Lihat Dokumen</a>
                                    </div>
                                </div>`;
                        }
                    } else {
                        versionLabel = `v${file.version}`;
                        colorClass = isLatest ? 'bg-green-600 text-white ring-4 ring-green-100 shadow-md' : 'bg-white border-2 border-slate-200 text-slate-500';
                        actionTitle = "Unggahan Lembaga";

                        userFileHTML = `
                            <div class="mt-2 flex items-center gap-3 group/file cursor-pointer" onclick="window.open('/storage/${file.file_path}', '_blank')">
                                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 group-hover/file:bg-blue-100 group-hover/file:scale-110 transition-all">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-slate-700 group-hover/file:text-blue-700 transition-colors">${file.file_name || 'File Lembaga'}</p>
                                    <p class="text-[10px] text-slate-400">Klik untuk melihat file</p>
                                </div>
                            </div>
                            ${file.user_note ? `<div class="mt-2 ml-1 text-xs text-slate-500 italic pl-3 border-l-2 border-slate-200">"${file.user_note}"</div>` : ''}
                        `;

                        let badgeHTML = '';
                        if (isLatest) {
                            badgeHTML = `<span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border border-emerald-200">DISETUJUI (FINAL)</span>`;
                        } else if (file.admin_note || file.admin_file) {
                            badgeHTML = `<span class="bg-slate-200 text-slate-600 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">REVISI</span>`;
                        }

                        if (badgeHTML || file.admin_note || file.admin_file) {
                            adminFeedbackHTML = `
                                <div class="mt-4 pt-3 border-t border-slate-100 relative">
                                    <div class="absolute -top-2 left-4 bg-slate-50 px-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Respon Verifikator</div>
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-xs font-bold text-slate-700">Status</span>
                                        ${badgeHTML}
                                    </div>
                                    ${file.admin_note ? `<div class="bg-yellow-50/50 border border-yellow-100 rounded-lg p-3 text-xs text-slate-700 mb-2"><i class="fas fa-comment-alt text-yellow-500 mr-1.5"></i> "${file.admin_note}"</div>` : ''}
                                    ${file.admin_file ? `<a href="/storage/${file.admin_file}" target="_blank" class="flex items-center gap-2 text-xs font-bold text-blue-600 bg-blue-50/50 hover:bg-blue-50 p-2 rounded-lg transition-colors border border-blue-100/50"><i class="fas fa-paperclip"></i> Lampiran SK / Balasan</a>` : ''}
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
                                    <span class="text-xs font-bold text-slate-700 flex items-center gap-2">
                                        ${actionTitle}
                                    </span> 
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