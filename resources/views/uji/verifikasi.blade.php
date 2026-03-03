<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Verifikasi Uji | SI-LAB ADMIN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .table-row-hover:hover td { background-color: #f0fdfa; } /* Teal hover */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none;  scrollbar-width: none; }
        
        /* Custom Scrollbar Teal */
        .modal-scroll::-webkit-scrollbar { width: 6px; }
        .modal-scroll::-webkit-scrollbar-track { background: #f1f5f9; }
        .modal-scroll::-webkit-scrollbar-thumb { background: #5eead4; border-radius: 10px; }
        .modal-scroll::-webkit-scrollbar-thumb:hover { background: #2dd4bf; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <!-- 1. DATA FETCHING (KHUSUS UJI) -->
    @php
        use App\Models\Submission;
        use App\Models\User;
        
        if (!isset($data)) {
            $data = Submission::with(['user', 'files']) 
                        ->where('category', 'uji') // Filter khusus kategori Uji
                        ->where('type', 'Verifikasi')
                        ->orderBy('created_at', 'desc')
                        ->get();
        }

        // Ambil user kategori Uji untuk dropdown
        $users = User::where('role', 'user')->where('category', 'uji')->get();

        // Statistik
        $waitingUser = $data->whereNull('user_note')->where('status', 'pending')->count();
        $needReview = $data->whereNotNull('user_note')->where('status', 'pending')->count();
        $completed = $data->where('status', 'approved')->count();
    @endphp

    <div class="flex h-screen overflow-hidden">
        
        <!-- SIDEBAR DESKTOP -->
        <div class="hidden md:flex h-full bg-teal-900">
            @include('components.uji-sidebar')
        </div>

        <!-- MOBILE SIDEBAR OVERLAY -->
        <div id="mobileSidebar" class="fixed inset-0 z-50 hidden">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="toggleSidebar()"></div>
            <div class="absolute left-0 top-0 bottom-0 w-64 bg-teal-900 shadow-xl transform transition-transform duration-300">
                @include('components.uji-sidebar')
            </div>
        </div>

        <div class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50 relative w-full">
            
            <div class="md:hidden bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between z-20 sticky top-0 shadow-sm">
                <div class="flex items-center gap-3">
                    <!-- Tombol Hamburger di Kiri -->
                    <button onclick="toggleSidebar()" class="p-2 text-slate-500 hover:text-teal-600 hover:bg-slate-100 rounded-lg transition-colors">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    
                    <!-- Logo/Brand -->
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-teal-600 flex items-center justify-center text-white shadow-sm">
                            <i class="fas fa-flask text-sm"></i>
                        </div>
                        <span class="font-bold text-slate-800 text-sm tracking-wide">SI-LAB UJI</span>
                    </div>
                </div>

                <!-- Profile Icon Kanan -->
                <div class="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center text-teal-600 text-xs font-bold border border-teal-200">
                    {{ substr(Auth::user()->name ?? 'L', 0, 1) }}
                </div>
            </div>

            <!-- HEADER DESKTOP -->
            <div class="hidden md:block">
                @include('components.uji-header', [
                    'title' => 'Manajemen Verifikasi Uji',
                    'subtitle' => 'Penerbitan surat hasil verifikasi untuk laboratorium'
                ])
            </div>

            <!-- MAIN CONTENT -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-6 lg:p-8 space-y-6">
                
                <!-- STATISTIK WIDGETS -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
                    <!-- Menunggu Konfirmasi -->
                    <div class="relative bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:-translate-y-1 transition-transform duration-300 group overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <i class="fas fa-paper-plane text-6xl text-slate-400"></i>
                        </div>
                        <div class="relative z-10">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="bg-slate-100 text-slate-600 px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider">Surat Terkirim</span>
                            </div>
                            <h2 class="text-4xl font-bold text-slate-800">{{ $waitingUser }}</h2>
                            <p class="text-[11px] text-slate-400 mt-2">Menunggu tindak lanjut laboratorium</p>
                        </div>
                    </div>

                    <!-- Perlu Review -->
                    <div class="relative bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:-translate-y-1 transition-transform duration-300 group overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <i class="fas fa-clipboard-list text-6xl text-teal-500"></i>
                        </div>
                        <div class="relative z-10">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="bg-teal-100 text-teal-600 px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider animate-pulse">Konfirmasi Masuk</span>
                            </div>
                            <h2 class="text-4xl font-bold text-slate-800">{{ $needReview }}</h2>
                            <p class="text-[11px] text-slate-400 mt-2">Perlu validasi admin Lab</p>
                        </div>
                    </div>

                    <!-- Selesai -->
                    <div class="relative bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:-translate-y-1 transition-transform duration-300 group overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <i class="fas fa-check-double text-6xl text-emerald-500"></i>
                        </div>
                        <div class="relative z-10">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="bg-emerald-100 text-emerald-600 px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider">Verifikasi Selesai</span>
                            </div>
                            <h2 class="text-4xl font-bold text-slate-800">{{ $completed }}</h2>
                            <p class="text-[11px] text-slate-400 mt-2">Arsip verifikasi yang telah disetujui</p>
                        </div>
                    </div>
                </div>

                <!-- TABLE CARD -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 bg-white flex flex-col md:flex-row justify-between items-center gap-4">
                        <div>
                            <h3 class="font-bold text-slate-800 text-lg">Daftar Dokumen Verifikasi</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Riwayat penerbitan surat hasil verifikasi laboratorium</p>
                        </div>
                        <button onclick="openCreateModal()" class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-2.5 rounded-xl shadow-lg shadow-teal-200 transition-all flex items-center gap-2 font-bold text-sm transform active:scale-95 border border-teal-700">
                            <i class="fas fa-plus-circle"></i> Terbitkan Verifikasi Baru
                        </button>
                    </div>

                    <div class="overflow-x-auto no-scrollbar">
                        <table class="w-full text-sm text-left text-slate-600 min-w-[1000px] md:min-w-0">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-6 py-4 font-bold tracking-wider">Tgl Terbit</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Laboratorium Tujuan</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Judul Dokumen</th>
                                    <th class="px-6 py-4 font-bold tracking-wider text-center">File Surat</th>
                                    <th class="px-6 py-4 font-bold tracking-wider text-center w-20">Jejak</th>
                                    <th class="px-6 py-4 font-bold tracking-wider text-center">Status</th>
                                    <th class="px-6 py-4 font-bold tracking-wider text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($data as $item)
                                <tr class="table-row-hover transition-colors group">
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-slate-700 text-xs">{{ $item->created_at->format('d M Y') }}</span>
                                            <span class="text-[10px] text-slate-400 mt-0.5">{{ $item->created_at->format('H:i') }} WIB</span>
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-teal-50 flex items-center justify-center text-teal-600 text-xs font-bold shrink-0 border border-teal-100">
                                                {{ substr($item->user->name ?? '?', 0, 1) }}
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-bold text-slate-800 text-xs truncate">{{ $item->user->name ?? 'Unknown' }}</div>
                                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $item->user->kode_instansi ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-5">
                                        <span class="text-xs font-medium text-slate-800 line-clamp-1" title="{{ $item->title }}">{{ $item->title }}</span>
                                        @if($item->admin_note)
                                            <p class="text-[9px] text-slate-400 italic mt-1 bg-slate-50 w-fit px-1.5 rounded border border-slate-100">Catatan: "{{ $item->admin_note }}"</p>
                                        @endif
                                    </td>

                                    <td class="px-6 py-5 text-center">
                                        <a href="{{ asset('storage/' . $item->admin_file) }}" target="_blank" class="inline-flex items-center gap-1.5 bg-teal-50 border border-teal-100 text-teal-700 px-3 py-1.5 rounded-lg text-[11px] font-bold hover:bg-teal-100 transition-all">
                                            <i class="fas fa-file-pdf"></i> Lihat File
                                        </a>
                                    </td>

                                    <td class="px-6 py-5 text-center">
                                        <button onclick='openHistoryModal(@json($item->files ?? []), "{{ $item->status }}", "{{ $item->title }}")' class="text-slate-400 hover:text-teal-600 p-2 rounded-full hover:bg-teal-50 transition-all">
                                            <i class="fas fa-history text-lg"></i>
                                        </button>
                                    </td>

                                    <td class="px-6 py-5 text-center">
                                        @if($item->status == 'approved')
                                            <span class="bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider border border-emerald-200">Selesai</span>
                                        @elseif($item->status == 'rejected')
                                            <span class="bg-rose-100 text-rose-700 px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider border border-rose-200">Revisi</span>
                                        @elseif($item->user_note)
                                            <span class="bg-teal-100 text-teal-700 px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider border border-teal-200 animate-pulse">Konfirmasi Masuk</span>
                                        @else
                                            <span class="bg-slate-100 text-slate-500 px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider border border-slate-200">Menunggu</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-5 text-center">
                                        @if($item->user_note && $item->status == 'pending')
                                            <button onclick="openVerifyModal('{{ $item->id }}', '{{ $item->title }}')" class="bg-teal-600 hover:bg-teal-700 text-white px-3 py-1.5 rounded-lg text-[11px] font-bold shadow-sm active:scale-95 transition-all">
                                                Verifikasi
                                            </button>
                                        @elseif($item->status == 'approved')
                                            <i class="fas fa-check-double text-emerald-500 text-lg"></i>
                                        @else
                                            <span class="text-slate-300">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-inbox text-4xl mb-3 text-slate-200"></i>
                                            <p class="text-sm font-medium text-slate-400">Belum ada data verifikasi untuk Lab Uji.</p>
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

    <!-- 2. MODAL CREATE VERIFIKASI -->
    <div id="createModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeCreateModal()"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100">
                    <div class="bg-gradient-to-r from-teal-600 to-emerald-600 px-6 py-4 flex justify-between items-center text-white font-bold">
                        <h3 class="flex items-center gap-2"><i class="fas fa-plus-circle"></i> Terbitkan Verifikasi</h3>
                        <button onclick="closeCreateModal()" class="text-teal-100 hover:text-white bg-white/10 p-2 rounded-lg transition-colors"><i class="fas fa-times"></i></button>
                    </div>
                    <form action="{{ route('verifikasi.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="category" value="uji">
                        <div class="px-6 py-6 space-y-5">
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 uppercase">Laboratorium / Lembaga Tujuan</label>
                                <select name="user_id" class="block w-full rounded-xl border-slate-300 bg-slate-50 p-3 text-sm focus:border-teal-500 focus:ring-teal-500" required>
                                    <option value="" disabled selected>-- Pilih Laboratorium --</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->kode_instansi ?? '-' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 uppercase">Judul Dokumen</label>
                                <input type="text" name="title" class="block w-full rounded-xl border-slate-300 bg-white p-3 text-sm focus:border-teal-500 focus:ring-teal-500" placeholder="Contoh: Surat Hasil Verifikasi Akreditasi..." required>
                            </div>
                            <div class="space-y-2 bg-slate-50 p-4 rounded-xl border border-slate-200">
                                <label class="block text-xs font-bold text-slate-700 uppercase flex justify-between">
                                    <span>Upload Surat (PDF)</span>
                                    <span class="text-[10px] bg-red-100 text-red-600 px-1.5 py-0.5 rounded">Wajib</span>
                                </label>
                                <input type="file" name="admin_file" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-teal-600 file:text-white hover:file:bg-teal-700 border border-slate-300 rounded-lg cursor-pointer bg-white" accept=".pdf" required>
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 uppercase">Instruksi Tambahan</label>
                                <textarea name="admin_note" rows="2" class="block w-full rounded-xl border-slate-300 bg-white p-3 text-sm focus:border-teal-500 focus:ring-teal-500" placeholder="Pesan instruksi untuk laboratorium..."></textarea>
                            </div>
                        </div>
                        <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-slate-100">
                            <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-teal-200 transition-all">Terbitkan</button>
                            <button type="button" onclick="closeCreateModal()" class="bg-white border border-slate-300 text-slate-700 px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-50">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. MODAL VALIDASI KONFIRMASI -->
    <div id="verifyModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeVerifyModal()"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-100">
                    <form id="verifyForm" method="POST" action="">
                        @csrf
                        <div class="bg-white px-6 py-5 border-b border-slate-100 flex items-center gap-3">
                            <div class="bg-teal-100 p-2 rounded-full text-teal-600"><i class="fas fa-check-circle text-lg"></i></div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 text-base">Validasi Konfirmasi Lab</h3>
                                <p class="text-xs text-slate-500 line-clamp-1" id="verifyTitle">Title...</p>
                            </div>
                        </div>
                        <div class="px-6 py-6 space-y-4">
                            <p class="text-sm text-slate-600">Verifikasi apakah laboratorium telah menyelesaikan instruksi pada surat hasil verifikasi ini?</p>
                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-slate-700 uppercase">Catatan Admin</label>
                                <textarea name="admin_note" rows="2" class="block w-full rounded-lg border-slate-300 text-xs p-3 bg-slate-50 focus:border-teal-500 focus:ring-teal-500" placeholder="Tulis catatan penutupan atau alasan revisi..."></textarea>
                            </div>
                            <div class="flex gap-3">
                                <button type="submit" onclick="setVerifyAction('approve')" class="flex-1 bg-emerald-600 text-white py-2.5 rounded-xl font-bold hover:bg-emerald-700 shadow-md transition-all active:scale-95">Setujui & Selesai</button>
                                <button type="submit" onclick="setVerifyAction('reject')" class="flex-1 bg-white border border-rose-200 text-rose-600 py-2.5 rounded-xl font-bold hover:bg-rose-50 shadow-sm transition-all active:scale-95">Minta Revisi</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. MODAL HISTORY (JEJAK VERIFIKASI) -->
    <div id="historyModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeHistoryModal()"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-3xl border border-slate-100">
                    <div class="bg-gradient-to-r from-slate-800 to-slate-900 px-6 py-5 flex justify-between items-center text-white shadow-md">
                        <h3 class="text-xl font-bold flex items-center gap-3">
                            <div class="bg-white/10 p-2 rounded-lg backdrop-blur-sm"><i class="fas fa-history text-lg"></i></div>
                            Jejak Verifikasi
                        </h3>
                        <button onclick="closeHistoryModal()" class="text-slate-400 hover:text-white bg-white/10 hover:bg-white/20 rounded-xl p-2 transition-all active:scale-95"><i class="fas fa-times text-lg"></i></button>
                    </div>
                    <div class="max-h-[65vh] overflow-y-auto bg-slate-50 modal-scroll">
                        <div id="timelineContainer" class="px-6 py-8 relative"></div>
                    </div>
                    <div class="bg-white px-6 py-4 flex justify-end border-t border-slate-200">
                         <button onclick="closeHistoryModal()" class="px-6 py-2.5 bg-white border border-slate-300 text-slate-700 text-sm rounded-xl hover:bg-slate-50 font-bold shadow-sm transition-colors">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('mobileSidebar');
            sidebar.classList.toggle('hidden');
        }

        function openCreateModal() { document.getElementById('createModal').classList.remove('hidden'); }
        function closeCreateModal() { document.getElementById('createModal').classList.add('hidden'); }

        let currentVerifyId = null;
        function openVerifyModal(id, title) {
            currentVerifyId = id;
            document.getElementById('verifyTitle').innerText = title;
            document.getElementById('verifyModal').classList.remove('hidden');
        }
        function closeVerifyModal() { document.getElementById('verifyModal').classList.add('hidden'); }
        function setVerifyAction(action) {
            const form = document.getElementById('verifyForm');
            if(action === 'approve') form.action = "{{ url('/submission/approve') }}/" + currentVerifyId;
            else form.action = "{{ url('/submission/reject') }}/" + currentVerifyId;
        }

        function openHistoryModal(files, currentStatus, docTitle) {
            const container = document.getElementById('timelineContainer');
            container.innerHTML = ''; 
            
            if(!files || files.length === 0) {
                container.innerHTML = `<div class="py-10 text-center text-slate-400 text-sm">Belum ada riwayat tercatat.</div>`;
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
                        colorClass = 'bg-teal-100 text-teal-600 ring-4 ring-teal-50';
                        actionTitle = "Penerbitan Surat Hasil Verifikasi (Admin)";
                        userFileHTML = file.admin_file ? `
                            <div class="mt-2 bg-teal-50 rounded-lg p-3 border border-teal-100 flex items-start gap-3">
                                <div class="bg-white p-2 rounded-md shadow-sm text-red-500"><i class="fas fa-file-pdf text-lg"></i></div>
                                <div>
                                    <p class="text-xs font-bold text-slate-700 mb-0.5">Dokumen Surat</p>
                                    <a href="/storage/${file.admin_file}" target="_blank" class="text-[11px] text-teal-600 hover:underline font-medium font-mono">Lihat Dokumen</a>
                                </div>
                            </div>
                        ` : '';
                    } else {
                        versionLabel = `v${file.version}`;
                        colorClass = isLatest ? 'bg-teal-600 text-white ring-4 ring-teal-100 shadow-md' : 'bg-white border-2 border-slate-200 text-slate-500';
                        actionTitle = "Konfirmasi Tindak Lanjut Lab";
                        userFileHTML = `
                            <div class="mt-2 flex flex-col gap-2">
                                <div class="bg-slate-50 rounded-lg p-3 border border-slate-200">
                                    <p class="text-[11px] font-medium text-slate-600 italic">"${file.user_note || 'Konfirmasi tindak lanjut telah diselesaikan oleh laboratorium.'}"</p>
                                </div>
                            </div>
                        `;
                        
                        let badgeHTML = '';
                        if (isLatest) {
                            if (currentStatus === 'approved') badgeHTML = `<span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded text-[10px] font-bold tracking-wider border border-emerald-200">DISETUJUI (FINAL)</span>`;
                            else if (currentStatus === 'rejected') badgeHTML = `<span class="bg-rose-100 text-rose-700 px-2 py-0.5 rounded text-[10px] font-bold tracking-wider border border-rose-200">REVISI</span>`;
                        }

                        if (badgeHTML || file.admin_note) {
                            adminFeedbackHTML = `
                                <div class="mt-4 pt-3 border-t border-slate-100 relative">
                                    <div class="absolute -top-2 left-4 bg-slate-50 px-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Evaluasi Admin</div>
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-xs font-bold text-slate-700">Status</span>
                                        ${badgeHTML}
                                    </div>
                                    ${file.admin_note ? `<div class="bg-yellow-50 border border-yellow-100 rounded-lg p-3 text-xs text-slate-700"><i class="fas fa-comment-alt text-yellow-500 mr-1.5"></i> "${file.admin_note}"</div>` : ''}
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
                            <div class="flex-1 bg-white rounded-xl p-4 border border-slate-200 shadow-sm relative transition-all">
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-xs font-bold text-slate-700">${actionTitle}</span> 
                                    <span class="text-[10px] text-slate-400 bg-slate-50 px-2 py-1 rounded-full border border-slate-100 font-mono">${dateStr}</span>
                                </div>
                                ${userFileHTML}
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