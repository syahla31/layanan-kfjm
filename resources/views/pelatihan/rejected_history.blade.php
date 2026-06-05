<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pengembalian | Admin Pelatihan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .table-row-hover:hover td { background-color: #fff1f2; /* rose-50 */ }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none;  scrollbar-width: none; }
        
        .modal-scroll::-webkit-scrollbar { width: 6px; }
        .modal-scroll::-webkit-scrollbar-track { background: #f1f5f9; }
        .modal-scroll::-webkit-scrollbar-thumb { background: #fecaca; border-radius: 10px; }
        .modal-scroll::-webkit-scrollbar-thumb:hover { background: #fda4af; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    @php
        use App\Models\Submission;
        use Illuminate\Http\Request;
        
        $filterType = request('filter_type', 'Semua');

        $query = Submission::with(['user', 'files'])
                        ->where('category', 'pelatihan')
                        ->where('status', 'rejected')
                        ->whereIn('type', ['KAK', 'Laporan Kinerja']);

        if ($filterType && $filterType !== 'Semua') {
            $query->where('type', $filterType);
        }

        $histories = $query->orderBy('updated_at', 'desc')->get();

        $filterLabel = [
            'Semua' => 'Semua Dokumen',
            'KAK' => 'Kerangka Acuan Kerja',
            'Laporan Kinerja' => 'Laporan Kinerja'
        ];
        $currentLabel = $filterLabel[$filterType] ?? 'Semua Dokumen';
    @endphp

    <div class="flex h-screen overflow-hidden">
        
        <div class="hidden md:flex h-full bg-blue-900">
            @include('components.pelatihan-sidebar')
        </div>

        <div id="mobileSidebar" class="fixed inset-0 z-50 hidden">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="toggleSidebar()"></div>
            <div class="absolute left-0 top-0 bottom-0 w-64 bg-blue-900 shadow-xl transform transition-transform duration-300">
                @include('components.pelatihan-sidebar')
            </div>
        </div>

        <div class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50 relative w-full">
            
            <div class="lg:hidden bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between sticky top-0 z-30 shadow-sm">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="p-2 text-slate-600 hover:text-blue-600 hover:bg-slate-100 rounded-lg transition-colors">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <span class="font-bold text-slate-800 text-sm tracking-wide">SI-PELATIHAN</span>
                </div>
                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-xs font-bold border border-blue-200">
                    {{ substr(Auth::user()->name ?? 'L', 0, 1) }}
                </div>
            </div>

            <div class="hidden md:block">
                @include('components.pelatihan-header', [
                    'title' => 'Riwayat Pengembalian',
                    'subtitle' => 'Daftar dokumen KAK & Lapkin yang sedang direvisi'
                ])
            </div>

            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-6 lg:p-8 space-y-6">
                
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">Dokumen Perlu Revisi</h2>
                        <p class="text-sm text-slate-500 mt-1">Total <span class="font-bold text-rose-600">{{ $histories->count() }}</span> dokumen sedang direvisi</p>
                    </div>
                    
                    <form id="filterForm" method="GET" action="{{ url()->current() }}" class="relative min-w-[260px] z-30">
                        <input type="hidden" name="filter_type" id="filterInput" value="{{ $filterType }}">
                        <button type="button" onclick="toggleDropdown()" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 shadow-sm hover:border-rose-400 hover:shadow-md transition-all flex items-center justify-between group focus:outline-none focus:ring-2 focus:ring-rose-100">
                            <div class="flex items-center gap-3">
                                <div class="text-slate-400 group-hover:text-rose-500 transition-colors">
                                    <i class="fas fa-filter text-sm"></i>
                                </div>
                                <span class="text-sm font-bold text-slate-700">{{ $currentLabel }}</span>
                            </div>
                            <i class="fas fa-chevron-down text-xs text-slate-400 transition-transform duration-200" id="dropdownArrow"></i>
                        </button>

                        <div id="dropdownMenu" class="absolute right-0 top-full mt-2 w-full bg-white rounded-xl shadow-xl border border-slate-100 hidden transform origin-top scale-95 opacity-0 transition-all duration-200 overflow-hidden">
                            <div class="p-1.5 flex flex-col gap-1">
                                @foreach($filterLabel as $val => $label)
                                <div onclick="selectFilter('{{ $val }}')" class="px-3 py-2.5 rounded-lg cursor-pointer flex items-center gap-3 text-sm font-medium transition-colors {{ $filterType == $val ? 'bg-rose-50 text-rose-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <i class="fas fa-layer-group w-4 text-center {{ $filterType == $val ? 'text-rose-500' : 'text-slate-400' }}"></i>
                                    <span class="flex-1">{{ $label }}</span>
                                    @if($filterType == $val) <i class="fas fa-check text-rose-500 text-xs"></i> @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </form>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden relative z-10">
                    <div class="overflow-x-auto no-scrollbar">
                        <table class="w-full text-sm text-left text-slate-600 min-w-[900px] md:min-w-0">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-6 py-4 font-bold tracking-wider w-40">Tgl Dikembalikan</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Lembaga Pengirim</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Jenis & Judul</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Catatan Admin</th>
                                    <th class="px-6 py-4 font-bold tracking-wider text-center w-20">Jejak</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($histories as $item)
                                <tr class="table-row-hover transition-colors group">
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-slate-700">{{ $item->updated_at->format('d M Y') }}</span>
                                            <span class="text-xs text-slate-400 mt-0.5">{{ $item->updated_at->format('H:i') }} WIB</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-start gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-600 text-xs font-bold shrink-0 uppercase">
                                                {{ substr($item->user->name ?? '?', 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-800 line-clamp-1 text-sm">{{ $item->user->name ?? 'User Hapus' }}</div>
                                                <div class="text-[10px] text-slate-400 font-mono mt-0.5 tracking-wider">{{ $item->user->kode_instansi ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col gap-1.5">
                                            @if($item->type == 'KAK')
                                                <span class="bg-indigo-50 text-indigo-700 border border-indigo-200 text-[9px] px-2 py-0.5 rounded-full font-bold w-fit uppercase tracking-tighter">KAK</span>
                                            @else
                                                <span class="bg-teal-50 text-teal-700 border border-teal-200 text-[9px] px-2 py-0.5 rounded-full font-bold w-fit uppercase tracking-tighter">LAPKIN</span>
                                            @endif
                                            <span class="text-slate-800 font-bold text-sm line-clamp-1" title="{{ $item->title }}">{{ $item->title }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="bg-rose-50/50 border border-rose-100 rounded-lg p-3">
                                            <p class="text-xs text-slate-600 italic">"{{ $item->admin_note ?? 'Tidak ada catatan khusus.' }}"</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <button onclick='openHistoryModal(@json($item->files ?? []), "{{ $item->status }}", "{{ $item->title }}")' class="text-slate-400 hover:text-rose-600 p-2 rounded-full hover:bg-rose-50 transition-all" title="Lihat Perjalanan Dokumen">
                                            <i class="fas fa-history text-lg"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4 border border-slate-100">
                                                <i class="fas fa-undo text-3xl text-slate-200"></i>
                                            </div>
                                            <h3 class="text-slate-800 font-bold">Tidak ada data</h3>
                                            <p class="text-slate-500 text-sm mt-1">Belum ada dokumen yang dikembalikan saat ini.</p>
                                        </div>
                                    </td>
                                </tr>
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

    <!-- MODAL HISTORY -->
    <div id="historyModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeHistoryModal()"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-3xl border border-slate-100">
                    <div class="bg-gradient-to-r from-rose-800 to-rose-950 px-6 py-5 flex justify-between items-center text-white shadow-md relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-5 rounded-full -mr-10 -mt-10 pointer-events-none"></div>
                        <div>
                            <h3 class="text-xl font-bold flex items-center gap-3">
                                <div class="bg-white/10 p-2 rounded-lg backdrop-blur-sm"><i class="fas fa-history text-lg"></i></div>
                                Jejak Pengembalian
                            </h3>
                            <p class="text-xs text-rose-200 mt-1" id="historyTitle">Detail perjalanan dokumen</p>
                        </div>
                        <button onclick="closeHistoryModal()" class="text-rose-200 hover:text-white bg-white/10 hover:bg-white/20 rounded-xl p-2 transition-all active:scale-95 z-10">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                    <div class="max-h-[65vh] overflow-y-auto bg-slate-50 modal-scroll">
                        <div id="timelineContainer" class="px-6 py-8 relative"></div>
                    </div>
                    <div class="bg-white px-6 py-4 flex justify-end border-t border-slate-200 shadow-sm relative z-20">
                         <button onclick="closeHistoryModal()" class="px-6 py-2.5 bg-slate-100 text-slate-700 text-sm rounded-xl hover:bg-slate-200 font-bold transition-colors">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleDropdown() {
            const menu = document.getElementById('dropdownMenu');
            const arrow = document.getElementById('dropdownArrow');
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                setTimeout(() => { menu.classList.remove('opacity-0', 'scale-95'); menu.classList.add('opacity-100', 'scale-100'); }, 10);
                arrow.style.transform = 'rotate(180deg)';
            } else { closeDropdown(); }
        }
        function closeDropdown() {
            const menu = document.getElementById('dropdownMenu');
            const arrow = document.getElementById('dropdownArrow');
            menu.classList.remove('opacity-100', 'scale-100');
            menu.classList.add('opacity-0', 'scale-95');
            setTimeout(() => { menu.classList.add('hidden'); }, 200);
            arrow.style.transform = 'rotate(0deg)';
        }
        function selectFilter(type) {
            document.getElementById('filterInput').value = type;
            document.getElementById('filterForm').submit();
        }
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('filterForm');
            if (dropdown && !dropdown.contains(event.target)) { closeDropdown(); }
        });
        function toggleSidebar() {
            const sidebar = document.getElementById('mobileSidebar');
            sidebar.classList.toggle('hidden');
        }
        function openHistoryModal(files, currentStatus, docTitle) {
            const container = document.getElementById('timelineContainer');
            document.getElementById('historyTitle').innerText = "Dokumen: " + docTitle;
            container.innerHTML = ''; 
            if(!files || files.length === 0) {
                container.innerHTML = `<div class="py-10 text-center text-slate-400 text-sm">Riwayat tidak tersedia.</div>`;
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
                    let versionLabel = isStart ? '<i class="fas fa-flag"></i>' : `v${file.version}`;
                    let colorClass = isLatest ? 'bg-rose-600 text-white ring-4 ring-rose-100' : 'bg-white border-2 border-slate-200 text-slate-500';
                    let actionTitle = isStart ? "Inisiasi / Awal" : "Unggahan Lembaga";
                    let userFileHTML = isStart ? '' : `
                        <div class="mt-2 flex items-center gap-3 bg-rose-50/50 p-2 rounded-lg border border-rose-100 cursor-pointer hover:bg-rose-50 transition-all" onclick="window.open('/storage/${file.file_path}', '_blank')">
                            <div class="w-8 h-8 rounded bg-rose-100 flex items-center justify-center text-rose-700"><i class="fas fa-file-pdf"></i></div>
                            <div class="flex-1">
                                <p class="text-[11px] font-bold text-slate-700 truncate">${file.file_name || 'Dokumen Lembaga'}</p>
                                <p class="text-[9px] text-rose-600">Klik untuk melihat</p>
                            </div>
                        </div>`;
                    let adminFeedbackHTML = '';
                    if (file.admin_note || file.admin_file) {
                        let badgeLabel = isLatest ? 'REVISI (STATUS SAAT INI)' : 'REVISI';
                        let badgeClass = isLatest ? 'bg-rose-600 text-white shadow-sm' : 'bg-amber-100 text-amber-700 border-amber-200';
                        adminFeedbackHTML = `
                            <div class="mt-3 pt-3 border-t border-slate-100">
                                <span class="${badgeClass} px-2 py-0.5 rounded text-[10px] font-bold border">${badgeLabel}</span>
                                ${file.admin_note ? `<p class="mt-2 text-xs text-slate-600 italic bg-white p-2 rounded border border-slate-200">"${file.admin_note}"</p>` : ''}
                                ${file.admin_file ? `<a href="/storage/${file.admin_file}" target="_blank" class="mt-2 inline-flex items-center gap-2 text-[10px] font-bold text-rose-700"><i class="fas fa-paperclip"></i> Lihat Lampiran Admin</a>` : ''}
                            </div>`;
                    }
                    container.innerHTML += `
                        <div class="relative flex gap-6 pb-8 last:pb-0">
                            <div class="absolute top-0 left-4 -bottom-8 w-0.5 bg-slate-200 last:hidden"></div>
                            <div class="relative z-10 flex-shrink-0 w-8 h-8 rounded-full ${colorClass} flex items-center justify-center border-2 border-white text-[10px] font-bold">${versionLabel}</div>
                            <div class="flex-1 bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs font-bold text-slate-700">${actionTitle}</span> 
                                    <span class="text-[10px] text-slate-400 font-mono">${dateStr}</span>
                                </div>
                                ${userFileHTML}
                                ${adminFeedbackHTML}
                            </div>
                        </div>`;
                });
            }
            document.getElementById('historyModal').classList.remove('hidden');
        }
        function closeHistoryModal() { document.getElementById('historyModal').classList.add('hidden'); }
    </script>
</body>
</html>