<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Verifikasi | SI-MUTU Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        primary: '#0d9488', // Teal-600
                        primaryDark: '#0f766e', // Teal-700
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .table-row-hover:hover td { background-color: #f8fafc; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <!-- DATA FETCHING -->
    @php
        // Filter hanya untuk Lembaga Uji
        // Menggunakan FQCN untuk menghindari error
        $query = \App\Models\Submission::with('user')
            ->whereHas('user', function($q) {
                $q->where('category', 'uji '); 
            });

        // Ambil Antrian (Pending) - Prioritas FIFO (First In First Out)
        $submissions = (clone $query)->where('status', 'pending')
                                     ->orderBy('created_at', 'asc')
                                     ->paginate(10);

        // Statistik
        $countPending = (clone $query)->where('status', 'pending')->count();
        
        $countApprovedMonth = (clone $query)->where('status', 'approved')
                                            ->whereMonth('updated_at', date('m'))
                                            ->count();

        $countRejectedMonth = (clone $query)->where('status', 'rejected')
                                            ->whereMonth('updated_at', date('m'))
                                            ->count();
    @endphp

    <div class="flex h-screen overflow-hidden">
        
        <!-- SIDEBAR DESKTOP -->
        <div class="hidden md:flex flex-shrink-0 w-64 h-full bg-slate-900 z-20">
            @include('components.uji-sidebar')
        </div>

        <!-- MOBILE SIDEBAR OVERLAY -->
        <div id="mobileSidebar" class="fixed inset-0 z-50 hidden">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="toggleSidebar()"></div>
            <div class="absolute left-0 top-0 bottom-0 w-64 bg-slate-900 shadow-xl transform transition-transform duration-300">
                @include('components.uji-sidebar')
            </div>
        </div>

        <div class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50 relative w-full">
            
            <!-- MOBILE HEADER -->
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

            <!-- HEADER DESKTOP (Simple) -->
            <div class="hidden md:flex h-16 bg-white border-b border-slate-200 px-8 items-center justify-between">
                <div>
                    <h1 class="text-lg font-bold text-slate-800">Panel Verifikasi</h1>
                    <p class="text-xs text-slate-500">Dashboard Verifikasi Dokumen Lembaga Uji</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm font-medium text-slate-600">Administrator</span>
                    <div class="w-8 h-8 rounded-full bg-slate-800 text-white flex items-center justify-center font-bold text-xs">A</div>
                </div>
            </div>

            <!-- MAIN CONTENT -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-6 lg:p-8 space-y-6 md:space-y-8">
                
                <!-- ALERT SUKSES -->
                @if (session('success'))
                    <div id="alert" class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl shadow-sm flex items-center justify-between animate-fade-in-up">
                        <div class="flex items-center gap-3">
                            <div class="bg-emerald-100 p-2 rounded-full text-emerald-600 flex-shrink-0">
                                <i class="fas fa-check-circle text-lg"></i>
                            </div>
                            <div>
                                <p class="font-bold text-sm">Aksi Berhasil</p>
                                <p class="text-xs opacity-80">{{ session('success') }}</p>
                            </div>
                        </div>
                        <button onclick="document.getElementById('alert').remove()" class="text-emerald-400 hover:text-emerald-600 transition-colors p-1">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                <!-- SECTION 1: STATISTIK WIDGETS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 md:gap-6">
                    
                    <!-- Card 1: Antrian Pending (Highlight) -->
                    <div class="relative bg-white rounded-2xl p-5 md:p-6 border border-slate-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:-translate-y-1 transition-transform duration-300 group overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <i class="fas fa-hourglass-half text-5xl md:text-6xl text-amber-500"></i>
                        </div>
                        <div class="relative z-10">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="bg-amber-100 text-amber-600 p-2 rounded-lg text-xs font-bold whitespace-nowrap">
                                    <i class="fas fa-inbox"></i> Inbox
                                </span>
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wide truncate">Perlu Tindakan</span>
                            </div>
                            <div class="flex items-end gap-2">
                                <h2 class="text-3xl md:text-4xl font-bold text-slate-800">{{ $countPending }}</h2>
                                <span class="text-sm text-slate-500 mb-1.5">dokumen</span>
                            </div>
                            <p class="text-xs text-slate-400 mt-2 truncate">Menunggu verifikasi Anda saat ini</p>
                            
                            <div class="w-full bg-slate-100 h-1.5 rounded-full mt-4 overflow-hidden">
                                <div class="bg-amber-500 h-full rounded-full" style="width: {{ $countPending > 0 ? '100%' : '5%' }}"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Disetujui Bulan Ini -->
                    <div class="relative bg-white rounded-2xl p-5 md:p-6 border border-slate-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:-translate-y-1 transition-transform duration-300 group overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <i class="fas fa-check-double text-5xl md:text-6xl text-emerald-500"></i>
                        </div>
                        <div class="relative z-10">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="bg-emerald-100 text-emerald-600 p-2 rounded-lg text-xs font-bold whitespace-nowrap">
                                    <i class="fas fa-calendar-check"></i> Bulan Ini
                                </span>
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wide truncate">Disetujui</span>
                            </div>
                            <div class="flex items-end gap-2">
                                <h2 class="text-3xl md:text-4xl font-bold text-slate-800">{{ $countApprovedMonth }}</h2>
                                <span class="text-sm text-slate-500 mb-1.5">dokumen</span>
                            </div>
                            <p class="text-xs text-slate-400 mt-2 truncate">Dokumen valid & lengkap</p>
                        </div>
                    </div>

                    <!-- Card 3: Revisi Bulan Ini -->
                    <div class="relative bg-white rounded-2xl p-5 md:p-6 border border-slate-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:-translate-y-1 transition-transform duration-300 group overflow-hidden sm:col-span-2 xl:col-span-1">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <i class="fas fa-undo-alt text-5xl md:text-6xl text-rose-500"></i>
                        </div>
                        <div class="relative z-10">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="bg-rose-100 text-rose-600 p-2 rounded-lg text-xs font-bold whitespace-nowrap">
                                    <i class="fas fa-exclamation-circle"></i> Bulan Ini
                                </span>
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wide truncate">Dikembalikan</span>
                            </div>
                            <div class="flex items-end gap-2">
                                <h2 class="text-3xl md:text-4xl font-bold text-slate-800">{{ $countRejectedMonth }}</h2>
                                <span class="text-sm text-slate-500 mb-1.5">dokumen</span>
                            </div>
                            <p class="text-xs text-slate-400 mt-2 truncate">Memerlukan revisi lembaga</p>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: TABEL ANTRIAN -->
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
                        <div>
                            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                                <i class="fas fa-list-ul text-teal-600"></i> Antrian Dokumen Masuk
                            </h3>
                            <p class="text-sm text-slate-500">Daftar dokumen yang belum diproses</p>
                        </div>
                        
                        @if($submissions->count() > 0)
                            <div class="bg-teal-600 text-white px-4 py-1.5 rounded-full text-xs font-bold shadow-md flex items-center gap-2 animate-pulse w-fit">
                                <span class="w-2 h-2 bg-white rounded-full"></span>
                                {{ $submissions->count() }} Dokumen Baru
                            </div>
                        @else
                            <div class="bg-slate-100 text-slate-500 px-4 py-1.5 rounded-full text-xs font-bold flex items-center gap-2 w-fit">
                                <i class="fas fa-check"></i> Semua Bersih
                            </div>
                        @endif
                    </div>

                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                        <div class="overflow-x-auto no-scrollbar">
                            <table class="w-full text-sm text-left text-slate-600 min-w-[800px] md:min-w-0">
                                <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                                    <tr>
                                        <th class="px-4 py-3 md:px-6 md:py-4 font-bold tracking-wider w-16 text-center">No</th>
                                        <th class="px-4 py-3 md:px-6 md:py-4 font-bold tracking-wider w-40">Waktu Masuk</th>
                                        <th class="px-4 py-3 md:px-6 md:py-4 font-bold tracking-wider">Instansi Pengirim</th>
                                        <th class="px-4 py-3 md:px-6 md:py-4 font-bold tracking-wider">Detail Dokumen</th>
                                        <th class="px-4 py-3 md:px-6 md:py-4 font-bold tracking-wider text-center w-32">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($submissions as $index => $item)
                                    <tr class="table-row-hover transition-colors group">
                                        <td class="px-4 py-4 md:px-6 md:py-5 text-center text-slate-400 font-mono text-xs">
                                            {{ $submissions->firstItem() + $index }}
                                        </td>
                                        
                                        <td class="px-4 py-4 md:px-6 md:py-5 whitespace-nowrap">
                                            <div class="flex flex-col">
                                                <span class="font-bold text-slate-700">{{ $item->created_at->format('d M Y') }}</span>
                                                <div class="flex items-center gap-1.5 mt-1">
                                                    <i class="far fa-clock text-slate-400 text-xs"></i>
                                                    <span class="text-xs text-slate-500">{{ $item->created_at->format('H:i') }} WIB</span>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-4 py-4 md:px-6 md:py-5">
                                            <div class="flex items-start gap-3">
                                                <div class="hidden sm:flex w-9 h-9 rounded-full bg-gradient-to-br from-teal-100 to-emerald-100 text-teal-600 items-center justify-center text-xs font-bold border border-teal-200 flex-shrink-0">
                                                    {{ substr($item->user->name ?? '?', 0, 1) }}
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="font-bold text-slate-800 text-sm line-clamp-1 break-all">{{ $item->user->name ?? 'Unknown' }}</p>
                                                    <div class="flex items-center gap-2 mt-1">
                                                        <span class="bg-slate-100 text-slate-500 text-[10px] px-2 py-0.5 rounded border border-slate-200 font-mono truncate">
                                                            {{ $item->user->kode_instansi ?? 'ID: ' . $item->user_id }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-4 py-4 md:px-6 md:py-5">
                                            <div class="flex flex-col gap-2">
                                                <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2">
                                                    @php 
                                                        $parts = explode(' - ', $item->type);
                                                        $scope = $parts[0] ?? $item->type;
                                                    @endphp
                                                    <span class="bg-indigo-50 text-indigo-700 border border-indigo-100 text-[10px] font-bold px-2 py-0.5 rounded w-fit uppercase">
                                                        {{ $scope }}
                                                    </span>
                                                    <p class="font-medium text-slate-800 text-sm line-clamp-1 break-all" title="{{ $item->title }}">{{ $item->title }}</p>
                                                </div>
                                                
                                                <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank" class="flex items-center gap-2 text-xs font-bold text-teal-600 hover:text-teal-800 w-fit group/link transition-colors bg-teal-50 px-3 py-1.5 rounded-lg border border-teal-100 hover:border-teal-300">
                                                    <i class="far fa-file-pdf text-red-500 text-sm"></i>
                                                    <span class="group-hover/link:underline">Buka File</span>
                                                </a>
                                            </div>
                                        </td>

                                        <td class="px-4 py-4 md:px-6 md:py-5">
                                            <div class="flex flex-row sm:flex-row items-center justify-center gap-2">
                                                <!-- Tombol Approve -->
                                                <button 
                                                    onclick="openModal('approve', '{{ $item->id }}', this.getAttribute('data-title'), '{{ $item->type }}')" 
                                                    data-title="{{ $item->title }}"
                                                    class="bg-emerald-600 text-white p-2 md:p-2.5 rounded-lg hover:bg-emerald-700 shadow-sm hover:shadow-md transition-all active:scale-95 group/btn" 
                                                    title="Setujui">
                                                    <i class="fas fa-check text-sm w-4 h-4 md:w-5 md:h-5 flex items-center justify-center"></i>
                                                </button>
                                                
                                                <!-- Tombol Reject -->
                                                <button 
                                                    onclick="openModal('reject', '{{ $item->id }}', this.getAttribute('data-title'), '{{ $item->type }}')" 
                                                    data-title="{{ $item->title }}"
                                                    class="bg-white border border-rose-200 text-rose-600 p-2 md:p-2.5 rounded-lg hover:bg-rose-50 hover:border-rose-300 shadow-sm transition-all active:scale-95" 
                                                    title="Tolak / Revisi">
                                                    <i class="fas fa-times text-sm w-4 h-4 md:w-5 md:h-5 flex items-center justify-center"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-20 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="w-20 h-20 md:w-24 md:h-24 bg-slate-50 rounded-full flex items-center justify-center mb-4 border border-slate-100">
                                                    <i class="fas fa-clipboard-check text-3xl md:text-4xl text-slate-300"></i>
                                                </div>
                                                <h3 class="text-slate-800 font-bold text-lg">Semua Selesai!</h3>
                                                <p class="text-slate-500 text-sm mt-1">Tidak ada antrian dokumen baru yang perlu diverifikasi.</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Pagination -->
                    @if($submissions->hasPages())
                        <div class="px-4">
                            {{ $submissions->links() }}
                        </div>
                    @endif
                </div>

                <div class="mt-8 text-center text-xs text-slate-400">
                    &copy; 2026 Sistem Informasi Jaminan Mutu Ketenaganukliran
                </div>

            </main>
        </div>
    </div>

    <!-- MODAL POPUP VERIFIKASI -->
    <div id="verifyModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity opacity-0" id="modalBackdrop" onclick="closeModal()"></div>

        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100 scale-95 opacity-0" id="modalPanel">
                    
                    <form id="verifyForm" method="POST" enctype="multipart/form-data" action="">
                        @csrf
                        
                        <div class="bg-gradient-to-r from-slate-50 to-white px-6 py-5 border-b border-slate-100 flex items-start gap-4">
                            <div id="modalIconBg" class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-slate-100 shadow-sm border border-white">
                                <i id="modalIcon" class="fas fa-check text-slate-600 text-lg"></i>
                            </div>
                            <div class="flex-1 pt-1">
                                <h3 class="text-lg font-bold text-slate-900 leading-tight" id="modalTitle">Konfirmasi</h3>
                                <p class="text-xs text-slate-500 mt-1 line-clamp-1 font-medium" id="modalDesc">Keterangan tindakan...</p>
                            </div>
                            <button type="button" onclick="closeModal()" class="text-slate-400 hover:text-slate-600 transition-colors bg-white hover:bg-slate-100 p-2 rounded-lg">
                                <i class="fas fa-times text-lg"></i>
                            </button>
                        </div>
                        
                        <div class="px-6 py-6 space-y-5">
                            
                            <div class="space-y-2 bg-slate-50 p-4 rounded-xl border border-slate-200" id="fileInputContainer">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide flex justify-between" id="fileInputLabel">
                                    <span>Upload Surat Balasan (PDF)</span>
                                    <span class="text-[10px] bg-slate-200 px-1.5 py-0.5 rounded text-slate-500">Opsional</span>
                                </label>
                                <input type="file" name="admin_file" class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-white file:text-teal-600 hover:file:bg-teal-50 border border-slate-300 rounded-lg cursor-pointer bg-white" accept=".pdf">
                                <p class="text-[10px] text-slate-400 mt-1 flex items-center gap-1" id="fileInputHelp">
                                    <i class="fas fa-info-circle"></i> <span>Dokumen SK atau surat balasan resmi.</span>
                                </p>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide">
                                    Catatan Tambahan
                                </label>
                                <textarea name="admin_note" rows="3" class="block w-full rounded-xl border-slate-300 bg-white p-3 text-sm focus:border-teal-500 focus:ring-teal-500 shadow-sm resize-none" placeholder="Tuliskan catatan atau alasan revisi di sini..."></textarea>
                            </div>
                        </div>

                        <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-slate-100">
                            <button type="submit" id="modalBtn" class="inline-flex w-full justify-center rounded-xl bg-slate-800 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-slate-200 hover:shadow-none hover:bg-slate-700 sm:w-auto transition-all active:scale-95">
                                Konfirmasi
                            </button>
                            <button type="button" onclick="closeModal()" class="inline-flex w-full justify-center rounded-xl bg-white border border-slate-300 px-5 py-2.5 text-sm font-bold text-slate-700 shadow-sm hover:bg-slate-50 sm:w-auto transition-all">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('mobileSidebar');
            if (sidebar.classList.contains('hidden')) {
                sidebar.classList.remove('hidden');
            } else {
                sidebar.classList.add('hidden');
            }
        }

        function openModal(action, id, title, type) {
            const modal = document.getElementById('verifyModal');
            const backdrop = document.getElementById('modalBackdrop');
            const panel = document.getElementById('modalPanel');
            
            const form = document.getElementById('verifyForm');
            const titleEl = document.getElementById('modalTitle');
            const descEl = document.getElementById('modalDesc');
            const btn = document.getElementById('modalBtn');
            const iconBg = document.getElementById('modalIconBg');
            const icon = document.getElementById('modalIcon');

            const fileLabel = document.getElementById('fileInputLabel');
            const fileHelp = document.getElementById('fileInputHelp');

            if(action === 'approve') {
                // Route harus sesuai: Route::post('/submission/approve/{id}')
                form.action = "{{ url('/submission/approve') }}/" + id; 
                titleEl.innerText = "Setujui Dokumen";
                descEl.innerText = "Menyetujui: " + title;
                
                iconBg.className = "flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-emerald-100 text-emerald-600 border border-emerald-200";
                icon.className = "fas fa-check text-xl";
                btn.innerText = "Setujui & Kirim";
                btn.className = "inline-flex w-full justify-center rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-emerald-200 hover:shadow-none hover:bg-emerald-700 sm:w-auto transition-all active:scale-95";
                
                // Set wording upload file
                fileLabel.innerHTML = `<span>Upload Surat Balasan (PDF)</span> <span class="text-[10px] bg-slate-200 px-1.5 py-0.5 rounded text-slate-500">Opsional</span>`;
                fileHelp.innerHTML = '<i class="fas fa-info-circle"></i> <span>Dokumen SK atau surat balasan resmi.</span>';

            } else {
                // Route harus sesuai: Route::post('/submission/reject/{id}')
                form.action = "{{ url('/submission/reject') }}/" + id;
                titleEl.innerText = "Kembalikan Revisi";
                descEl.innerText = "Meminta revisi untuk: " + title;
                
                iconBg.className = "flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-rose-100 text-rose-600 border border-rose-200";
                icon.className = "fas fa-undo text-xl";
                btn.innerText = "Kirim Revisi";
                btn.className = "inline-flex w-full justify-center rounded-xl bg-rose-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-rose-200 hover:shadow-none hover:bg-rose-700 sm:w-auto transition-all active:scale-95";

                fileLabel.innerHTML = `<span>Upload File Penjelas</span> <span class="text-[10px] bg-slate-200 px-1.5 py-0.5 rounded text-slate-500">Opsional</span>`;
                fileHelp.innerText = "Upload dokumen coretan revisi jika ada.";
            }

            modal.classList.remove('hidden');
            setTimeout(() => {
                backdrop.classList.remove('opacity-0');
                panel.classList.remove('scale-95', 'opacity-0');
                panel.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeModal() {
            const modal = document.getElementById('verifyModal');
            const backdrop = document.getElementById('modalBackdrop');
            const panel = document.getElementById('modalPanel');

            backdrop.classList.add('opacity-0');
            panel.classList.remove('scale-100', 'opacity-100');
            panel.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300); 
        }
    </script>
</body>
</html>