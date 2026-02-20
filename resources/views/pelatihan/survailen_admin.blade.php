<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Survailen | SI-MUTU Admin</title>
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

    <!-- DATA FETCHING -->
    @php
        use App\Models\Submission;
        use App\Models\User;
        
        if (!isset($audits)) {
            $audits = Submission::with(['user', 'files']) // Load Files History
                        ->where('category', 'pelatihan')
                        ->where('type', 'Survailen')
                        ->orderBy('created_at', 'desc')
                        ->get();
        }

        // Ambil user lembaga untuk dropdown
        $users = User::where('role', 'user')->where('category', 'pelatihan')->get();

        $waitingUser = $audits->whereNull('file_path')->count();
        $needReview = $audits->whereNotNull('file_path')->where('status', 'pending')->count();
        $completed = $audits->where('status', 'approved')->count();
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
                    'title' => 'Manajemen Audit & Survailen',
                    'subtitle' => 'Kirim dan pantau tindak lanjut audit lembaga'
                ])
            </div>

            <!-- MAIN CONTENT -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-6 lg:p-8 space-y-6">
                
                <!-- ALERT SUCCESS -->
                @if (session('success'))
                    <div id="alert" class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl shadow-sm flex items-center justify-between animate-fade-in-down">
                        <div class="flex items-center gap-3">
                            <div class="bg-emerald-100 p-2 rounded-full text-emerald-600">
                                <i class="fas fa-check-circle text-lg"></i>
                            </div>
                            <p class="font-bold text-sm">{{ session('success') }}</p>
                        </div>
                        <button onclick="document.getElementById('alert').remove()" class="text-emerald-400 hover:text-emerald-600 p-1"><i class="fas fa-times"></i></button>
                    </div>
                @endif

                <!-- ERROR VALIDATION -->
                @if ($errors->any())
                    <div class="p-4 bg-rose-50 border border-rose-100 text-rose-800 rounded-xl shadow-sm">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-exclamation-triangle text-lg mt-0.5"></i>
                            <div>
                                <p class="font-bold text-sm">Gagal Menyimpan</p>
                                <ul class="list-disc list-inside text-xs mt-1 opacity-80">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- STATISTIK WIDGETS -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
                    <!-- Menunggu Respon -->
                    <div class="relative bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:-translate-y-1 transition-transform duration-300 group overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <i class="fas fa-user-clock text-6xl text-slate-500"></i>
                        </div>
                        <div class="relative z-10">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="bg-slate-100 text-slate-600 px-2 py-1 rounded-md text-xs font-bold uppercase">Terkirim</span>
                            </div>
                            <h2 class="text-4xl font-bold text-slate-800">{{ $waitingUser }}</h2>
                            <p class="text-xs text-slate-400 mt-2">Menunggu respon lembaga</p>
                        </div>
                    </div>

                    <!-- Perlu Verifikasi -->
                    <div class="relative bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:-translate-y-1 transition-transform duration-300 group overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <i class="fas fa-inbox text-6xl text-amber-500"></i>
                        </div>
                        <div class="relative z-10">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="bg-amber-100 text-amber-600 px-2 py-1 rounded-md text-xs font-bold uppercase animate-pulse">Perlu Review</span>
                            </div>
                            <h2 class="text-4xl font-bold text-slate-800">{{ $needReview }}</h2>
                            <p class="text-xs text-slate-400 mt-2">Respon masuk dari lembaga</p>
                        </div>
                    </div>

                    <!-- Selesai -->
                    <div class="relative bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:-translate-y-1 transition-transform duration-300 group overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <i class="fas fa-check-double text-6xl text-emerald-500"></i>
                        </div>
                        <div class="relative z-10">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="bg-emerald-100 text-emerald-600 px-2 py-1 rounded-md text-xs font-bold uppercase">Selesai</span>
                            </div>
                            <h2 class="text-4xl font-bold text-slate-800">{{ $completed }}</h2>
                            <p class="text-xs text-slate-400 mt-2">Audit ditutup (Approved)</p>
                        </div>
                    </div>
                </div>

                <!-- TABLE CARD -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 bg-white flex flex-col md:flex-row justify-between items-center gap-4">
                        <div>
                            <h3 class="font-bold text-slate-800 text-lg">Daftar Audit</h3>
                            <p class="text-sm text-slate-500 mt-0.5">Riwayat pengiriman surat dan status respon</p>
                        </div>
                        <button onclick="openCreateModal()" class="bg-amber-600 hover:bg-amber-700 text-white px-5 py-2.5 rounded-xl shadow-lg shadow-amber-200 transition-all flex items-center gap-2 font-bold text-sm transform active:scale-95">
                            <i class="fas fa-paper-plane"></i> Kirim Surat Audit Baru
                        </button>
                    </div>

                    <div class="overflow-x-auto no-scrollbar">
                        <table class="w-full text-sm text-left text-slate-600 min-w-[900px] md:min-w-0">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-6 py-4 font-bold tracking-wider">Tgl Kirim</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Lembaga Tujuan</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Perihal</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Respon Lembaga</th>
                                    <th class="px-6 py-4 font-bold tracking-wider text-center w-20">Riwayat</th>
                                    <th class="px-6 py-4 font-bold tracking-wider text-center">Status</th>
                                    <th class="px-6 py-4 font-bold tracking-wider text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($audits as $item)
                                <tr class="table-row-hover transition-colors group">
                                    
                                    <!-- Tanggal -->
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-slate-700">{{ $item->created_at->format('d M Y') }}</span>
                                            <span class="text-xs text-slate-400 mt-0.5">{{ $item->created_at->format('H:i') }} WIB</span>
                                        </div>
                                    </td>
                                    
                                    <!-- Lembaga -->
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 text-xs font-bold shrink-0">
                                                {{ substr($item->user->name ?? '?', 0, 1) }}
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-bold text-slate-800 line-clamp-1">{{ $item->user->name ?? 'Unknown' }}</div>
                                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $item->user->kode_instansi ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Perihal & File Admin -->
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col gap-1">
                                            <span class="font-medium text-slate-800 line-clamp-1" title="{{ $item->title }}">{{ $item->title }}</span>
                                            @if($item->admin_file)
                                                <a href="{{ asset('storage/' . $item->admin_file) }}" target="_blank" class="text-xs text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-1 w-fit">
                                                    <i class="fas fa-file-pdf"></i> Lihat Surat
                                                </a>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Respon User -->
                                    <td class="px-6 py-5">
                                        @if($item->file_path && $item->file_path !== '-')
                                            <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank" class="inline-flex items-center gap-1.5 bg-emerald-50 border border-emerald-100 text-emerald-700 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-emerald-100 transition-colors">
                                                <i class="fas fa-download"></i> Bukti Upload
                                            </a>
                                        @else
                                            <span class="text-xs text-slate-400 italic bg-slate-50 px-2 py-1 rounded">Belum ada respon</span>
                                        @endif
                                    </td>

                                    <!-- Riwayat -->
                                    <td class="px-6 py-5 text-center">
                                        <button onclick='openHistoryModal(@json($item->files ?? []), "{{ $item->status }}", "{{ $item->title }}")' class="text-slate-400 hover:text-amber-600 p-2 rounded-full hover:bg-amber-50 transition-all" title="Lihat Jejak Audit">
                                            <i class="fas fa-history text-lg"></i>
                                        </button>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-5 text-center">
                                        @if($item->status == 'approved')
                                            <span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider border border-emerald-200">Selesai</span>
                                        @elseif($item->status == 'rejected')
                                            <span class="bg-rose-100 text-rose-700 px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider border border-rose-200">Ditolak</span>
                                        @elseif($item->file_path && $item->file_path !== '-')
                                            <span class="bg-amber-100 text-amber-700 px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider border border-amber-200 animate-pulse">Perlu Cek</span>
                                        @else
                                            <span class="bg-slate-100 text-slate-500 px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider border border-slate-200">Terkirim</span>
                                        @endif
                                    </td>

                                    <!-- Aksi -->
                                    <td class="px-6 py-5 text-center">
                                        @if($item->file_path && $item->file_path !== '-' && $item->status == 'pending')
                                            <button 
                                                onclick="openVerifyModal('{{ $item->id }}', this.getAttribute('data-title'))" 
                                                data-title="{{ $item->title }}"
                                                class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm active:scale-95 transition-all">
                                                Verifikasi
                                            </button>
                                        @elseif($item->status == 'approved')
                                            <i class="fas fa-check text-slate-300"></i>
                                        @else
                                            <span class="text-slate-300">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-inbox text-4xl mb-2 text-slate-200"></i>
                                            <p>Belum ada data audit yang dikirim.</p>
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

    <!-- MODAL CREATE AUDIT -->
    <div id="createModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeCreateModal()"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100">
                    
                    <div class="bg-gradient-to-r from-amber-600 to-orange-600 px-6 py-4 flex justify-between items-center text-white">
                        <h3 class="text-lg font-bold flex items-center gap-2">
                            <i class="fas fa-paper-plane"></i> Kirim Surat Audit
                        </h3>
                        <button onclick="closeCreateModal()" class="text-amber-100 hover:text-white transition-colors bg-white/10 hover:bg-white/20 p-2 rounded-lg">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form action="{{ route('survailen.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="px-6 py-6 space-y-5">
                            
                            <!-- Pilih Lembaga -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 uppercase">Tujuan Lembaga</label>
                                <div class="relative">
                                    <select name="user_id" class="block w-full rounded-xl border-slate-300 bg-slate-50 p-3 text-sm focus:border-amber-500 focus:ring-amber-500 appearance-none" required>
                                        <option value="" disabled selected>-- Pilih Lembaga --</option>
                                        @foreach($users as $u)
                                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->kode_instansi ?? '-' }})</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-slate-500">
                                        <i class="fas fa-chevron-down text-xs"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Judul -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 uppercase">Perihal / Judul</label>
                                <input type="text" name="title" class="block w-full rounded-xl border-slate-300 bg-white p-3 text-sm focus:border-amber-500 focus:ring-amber-500" placeholder="Contoh: Audit Lapangan Semester 1" required>
                            </div>

                            <!-- File Admin -->
                            <div class="space-y-2 bg-slate-50 p-4 rounded-xl border border-slate-200">
                                <label class="block text-xs font-bold text-slate-700 uppercase flex justify-between">
                                    <span>Upload Surat Audit (PDF)</span>
                                    <span class="text-[10px] bg-red-100 text-red-600 px-1.5 py-0.5 rounded">Wajib</span>
                                </label>
                                <input type="file" name="admin_file" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-white file:text-amber-700 hover:file:bg-amber-50 border border-slate-300 rounded-lg cursor-pointer bg-white" accept=".pdf" required>
                            </div>

                            <!-- Catatan -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 uppercase">Catatan / Instruksi</label>
                                <textarea name="admin_note" rows="2" class="block w-full rounded-xl border-slate-300 bg-white p-3 text-sm focus:border-amber-500 focus:ring-amber-500" placeholder="Tambahkan instruksi khusus..."></textarea>
                            </div>
                        </div>

                        <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-slate-100">
                            <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-amber-200 hover:shadow-none transition-all active:scale-95">Kirim</button>
                            <button type="button" onclick="closeCreateModal()" class="bg-white border border-slate-300 text-slate-700 px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-50 shadow-sm transition-all">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL VERIFIKASI (Modern) -->
    <div id="verifyModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeVerifyModal()"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-100">
                    
                    <form id="verifyForm" method="POST" action="" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="bg-white px-6 py-5 border-b border-slate-100">
                            <div class="flex items-center gap-3">
                                <div class="bg-amber-100 p-2 rounded-full text-amber-600">
                                    <i class="fas fa-tasks text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-slate-900">Verifikasi Respon</h3>
                                    <p class="text-xs text-slate-500 font-medium line-clamp-1" id="verifyTitle">Title...</p>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-6 space-y-5">
                            <p class="text-sm text-slate-600">
                                Apakah bukti tindak lanjut dari lembaga sudah sesuai?
                            </p>
                            
                            <!-- Input Tambahan -->
                            <div class="space-y-3 bg-slate-50 p-4 rounded-xl border border-slate-200">
                                <div class="space-y-1">
                                    <label class="block text-xs font-bold text-slate-700 uppercase">Catatan Balasan</label>
                                    <textarea name="admin_note" rows="2" class="block w-full rounded-lg border-slate-300 text-xs p-2.5 bg-white" placeholder="Tulis catatan jika revisi..."></textarea>
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-xs font-bold text-slate-700 uppercase flex justify-between">
                                        <span>File Balasan</span>
                                        <span class="text-[10px] bg-slate-200 px-1.5 py-0.5 rounded text-slate-500">Opsional</span>
                                    </label>
                                    <input type="file" name="admin_file" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:bg-white file:text-slate-700 hover:file:bg-slate-100 border border-slate-300 rounded-lg bg-white">
                                </div>
                            </div>

                            <div class="flex gap-3">
                                <button type="submit" onclick="setVerifyAction('approve')" class="flex-1 bg-emerald-600 text-white py-2.5 rounded-xl font-bold hover:bg-emerald-700 shadow-md transition-all active:scale-95 flex items-center justify-center gap-2">
                                    <i class="fas fa-check"></i> Selesai
                                </button>
                                <button type="submit" onclick="setVerifyAction('reject')" class="flex-1 bg-white border border-rose-200 text-rose-600 py-2.5 rounded-xl font-bold hover:bg-rose-50 shadow-sm transition-all active:scale-95 flex items-center justify-center gap-2">
                                    <i class="fas fa-undo"></i> Minta Revisi
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL HISTORY (Pro Timeline) -->
    <div id="historyModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeHistoryModal()"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-3xl border border-slate-100">
                    
                    <div class="bg-gradient-to-r from-slate-800 to-slate-900 px-6 py-5 flex justify-between items-center text-white shadow-md">
                        <div>
                            <h3 class="text-xl font-bold flex items-center gap-3">
                                <div class="bg-white/10 p-2 rounded-lg backdrop-blur-sm"><i class="fas fa-history text-lg"></i></div>
                                Jejak Audit
                            </h3>
                            <p class="text-xs text-slate-300 mt-1 opacity-90" id="historyTitle">Detail riwayat komunikasi</p>
                        </div>
                        <button onclick="closeHistoryModal()" class="text-slate-400 hover:text-white bg-white/10 hover:bg-white/20 rounded-xl p-2 transition-all active:scale-95">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>

                    <div class="max-h-[65vh] overflow-y-auto bg-slate-50 modal-scroll">
                        <div id="timelineContainer" class="px-6 py-8 relative">
                            <!-- Timeline Injected Here -->
                        </div>
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
            if (sidebar.classList.contains('hidden')) { sidebar.classList.remove('hidden'); } 
            else { sidebar.classList.add('hidden'); }
        }

        // Cek Error Validasi
        @if ($errors->any())
            document.addEventListener("DOMContentLoaded", function() {
                document.getElementById('createModal').classList.remove('hidden');
            });
        @endif

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

        // HISTORY LOGIC (SAMA DENGAN VERSI USER)
        function openHistoryModal(files, currentStatus, docTitle) {
            const container = document.getElementById('timelineContainer');
            document.getElementById('historyTitle').innerText = "Riwayat: " + docTitle;
            container.innerHTML = ''; 
            
            if(!files || files.length === 0) {
                container.innerHTML = `<div class="py-10 text-center text-slate-400 text-sm">Belum ada riwayat.</div>`;
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
                        actionTitle = "Inisiasi Audit (Anda)";
                        userFileHTML = '';
                        if(file.admin_file) {
                            userFileHTML = `
                                <div class="mt-2 bg-amber-50 rounded-lg p-3 border border-amber-100 flex items-start gap-3">
                                    <div class="bg-white p-2 rounded-md shadow-sm text-red-500"><i class="fas fa-file-pdf text-lg"></i></div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-700 mb-0.5">Surat Audit Awal</p>
                                        <a href="/storage/${file.admin_file}" target="_blank" class="text-[11px] text-blue-600 hover:underline font-medium">Lihat Dokumen</a>
                                    </div>
                                </div>
                                ${file.admin_note ? `<div class="mt-2 text-xs text-slate-600 italic bg-white p-2 rounded border border-slate-100">"${file.admin_note}"</div>` : ''}
                            `;
                        }
                        adminFeedbackHTML = ''; 
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
                                    <p class="text-[10px] text-slate-400">Klik untuk melihat file</p>
                                </div>
                            </div>
                            ${file.user_note ? `<div class="mt-2 ml-1 text-xs text-slate-500 italic pl-3 border-l-2 border-slate-200">"${file.user_note}"</div>` : ''}
                        `;
                        
                        let badgeHTML = '';
                        if (isLatest) {
                            if (currentStatus === 'approved') badgeHTML = `<span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border border-emerald-200">DISETUJUI (FINAL)</span>`;
                            else if (currentStatus === 'rejected') badgeHTML = `<span class="bg-rose-100 text-rose-700 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border border-rose-200">MINTA REVISI</span>`;
                        } else if (file.admin_note || file.admin_file) {
                            badgeHTML = `<span class="bg-slate-200 text-slate-600 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">REVISI SEBELUMNYA</span>`;
                        }

                        if (badgeHTML || file.admin_note || file.admin_file) {
                            adminFeedbackHTML = `
                                <div class="mt-4 pt-3 border-t border-slate-100 relative">
                                    <div class="absolute -top-2 left-4 bg-slate-50 px-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tanggapan Anda</div>
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-xs font-bold text-slate-700">Status</span>
                                        ${badgeHTML}
                                    </div>
                                    ${file.admin_note ? `<div class="bg-yellow-50/50 border border-yellow-100 rounded-lg p-3 text-xs text-slate-700 mb-2"><i class="fas fa-comment-alt text-yellow-500 mr-1.5"></i> "${file.admin_note}"</div>` : ''}
                                    ${file.admin_file ? `<a href="/storage/${file.admin_file}" target="_blank" class="flex items-center gap-2 text-xs font-bold text-blue-600 bg-blue-50/50 hover:bg-blue-50 p-2 rounded-lg transition-colors border border-blue-100/50"><i class="fas fa-paperclip"></i> Lampiran Balasan Anda</a>` : ''}
                                </div>
                            `;
                        } else {
                            adminFeedbackHTML = `<div class="mt-4 pt-2 border-t border-slate-100 text-center"><span class="text-[10px] text-slate-400 italic">Menunggu evaluasi Anda...</span></div>`;
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