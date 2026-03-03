<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Survailen | SI-MUTU Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    animation: { 
                        'pop-in': 'popIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards',
                    },
                    keyframes: {
                        popIn: {
                            '0%': { opacity: '0', transform: 'scale(0.8) translateY(20px)' },
                            '100%': { opacity: '1', transform: 'scale(1) translateY(0)' },
                        }
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
        
        /* Skrol kustom untuk Modal */
        .modal-scroll::-webkit-scrollbar { width: 6px; }
        .modal-scroll::-webkit-scrollbar-track { background: #f1f5f9; }
        .modal-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .modal-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Animasi Dropdown */
        .dropdown-animate {
            transform-origin: top;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .dropdown-hidden {
            opacity: 0;
            transform: scaleY(0);
            pointer-events: none;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    @php
        // Statistik Ringkasan
        $waitingUser = $audits->whereNull('file_path')->count();
        $needReview = $audits->whereNotNull('file_path')->where('status', 'pending')->count();
        $completed = $audits->where('status', 'approved')->count();
    @endphp

    <!-- === POP-UP NOTIFIKASI MODAL === -->
    
    <!-- 1. Success Modal (Auto Close) -->
    @if (session('success'))
    <div id="successModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 backdrop-blur-[3px] transition-all duration-300">
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-sm w-full mx-4 text-center animate-pop-in relative overflow-hidden border border-white/50">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-emerald-400 to-teal-500"></div>
            
            <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow-inner">
                <i class="fas fa-check text-4xl text-emerald-600 drop-shadow-sm"></i>
            </div>
            
            <h3 class="text-2xl font-bold text-slate-800 mb-2">Berhasil!</h3>
            <p class="text-slate-600 mb-6 text-sm leading-relaxed font-medium">
                {{ session('success') }}
            </p>
            
            <!-- Progress Bar -->
            <div class="w-full bg-slate-100 h-1.5 rounded-full mb-5 overflow-hidden">
                <div id="progressBar" class="h-full bg-emerald-500 rounded-full" style="width: 100%"></div>
            </div>

            <button onclick="closeNotification('successModal')" class="w-full bg-white border-2 border-slate-100 hover:border-emerald-400 hover:bg-emerald-50 text-slate-500 hover:text-emerald-700 font-bold py-3 rounded-xl transition-all duration-300 shadow-sm group">
                <span class="flex items-center justify-center gap-2">
                    Tutup Sekarang <i class="fas fa-times group-hover:rotate-90 transition-transform text-xs"></i>
                </span>
            </button>
        </div>
    </div>
    @endif

    <!-- 2. Error Modal -->
    @if ($errors->any() || session('error'))
    <div id="errorModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 backdrop-blur-[3px] transition-all duration-300">
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-sm w-full mx-4 text-center animate-pop-in relative overflow-hidden border border-white/50">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-rose-500 to-red-600"></div>
            
            <div class="w-20 h-20 bg-rose-100 rounded-full flex items-center justify-center mx-auto mb-5 shadow-inner">
                <i class="fas fa-exclamation-triangle text-4xl text-rose-600"></i>
            </div>
            
            <h3 class="text-2xl font-bold text-slate-800 mb-2">Gagal!</h3>
            <div class="text-slate-600 mb-8 text-sm leading-relaxed font-medium">
                @if(session('error'))
                    <p>{{ session('error') }}</p>
                @else
                    <ul class="list-none space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <button onclick="closeNotification('errorModal')" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-bold py-3.5 rounded-xl transition-all shadow-lg transform active:scale-95">
                Coba Lagi
            </button>
        </div>
    </div>
    @endif

    <div class="flex h-screen overflow-hidden">
        <!-- SIDEBAR DESKTOP -->
        <div class="hidden md:flex h-full bg-blue-900">
            @include('components.pelatihan-sidebar')
        </div>

        <!-- MOBILE SIDEBAR OVERLAY -->
        <div id="mobileSidebar" class="fixed inset-0 z-40 hidden lg:hidden bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="toggleSidebar()"></div>
        <div id="sidebarMobileContent" class="fixed left-0 top-0 bottom-0 z-50 w-64 bg-blue-900 shadow-xl transform -translate-x-full transition-transform duration-300 lg:hidden">
             @include('components.pelatihan-sidebar')
        </div>

        <div class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50 relative w-full">
            <!-- HEADER MOBILE -->
            <div class="lg:hidden bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between sticky top-0 z-30 shadow-sm">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="p-2 text-slate-600 hover:text-amber-600 hover:bg-slate-100 rounded-lg transition-colors">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <span class="font-bold text-slate-800 text-sm tracking-wide">SI-MUTU <span class="text-amber-600">DKKN</span></span>
                </div>
                <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 text-xs font-bold border border-amber-200">
                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                </div>
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
                <!-- STATISTIK WIDGETS -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
                    <div class="relative bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:-translate-y-1 transition-all group overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <i class="fas fa-user-clock text-6xl text-slate-500"></i>
                        </div>
                        <div class="relative z-10">
                            <span class="bg-slate-100 text-slate-600 px-2 py-1 rounded-md text-[10px] font-bold uppercase">Terkirim</span>
                            <h2 class="text-4xl font-bold text-slate-800 mt-2">{{ $waitingUser }}</h2>
                            <p class="text-xs text-slate-400 mt-2">Menunggu respon lembaga</p>
                        </div>
                    </div>

                    <div class="relative bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:-translate-y-1 transition-all group overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <i class="fas fa-inbox text-6xl text-amber-500"></i>
                        </div>
                        <div class="relative z-10">
                            <span class="bg-amber-100 text-amber-600 px-2 py-1 rounded-md text-[10px] font-bold uppercase animate-pulse">Perlu Review</span>
                            <h2 class="text-4xl font-bold text-slate-800 mt-2">{{ $needReview }}</h2>
                            <p class="text-xs text-slate-400 mt-2">Respon masuk dari lembaga</p>
                        </div>
                    </div>

                    <div class="relative bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:-translate-y-1 transition-all group overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <i class="fas fa-check-double text-6xl text-emerald-500"></i>
                        </div>
                        <div class="relative z-10">
                            <span class="bg-emerald-100 text-emerald-600 px-2 py-1 rounded-md text-[10px] font-bold uppercase">Selesai</span>
                            <h2 class="text-4xl font-bold text-slate-800 mt-2">{{ $completed }}</h2>
                            <p class="text-xs text-slate-400 mt-2">Audit ditutup (Approved)</p>
                        </div>
                    </div>
                </div>

                <!-- TABEL DATA -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4">
                        <div>
                            <h3 class="font-bold text-slate-800 text-lg">Daftar Audit</h3>
                            <p class="text-sm text-slate-500 mt-0.5">Riwayat pengiriman surat dan status respon</p>
                        </div>
                        <button onclick="openCreateModal()" class="bg-amber-600 hover:bg-amber-700 text-white px-5 py-2.5 rounded-xl shadow-lg shadow-amber-200 transition-all flex items-center gap-2 font-bold text-sm transform active:scale-95">
                            <i class="fas fa-paper-plane"></i> Kirim Surat Audit Baru
                        </button>
                    </div>

                    <div class="overflow-x-auto no-scrollbar">
                        <table class="w-full text-sm text-left text-slate-600 min-w-[900px]">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-6 py-4 font-bold tracking-wider">Tgl Kirim</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Lembaga Tujuan</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Perihal</th>
                                    <th class="px-6 py-4 font-bold tracking-wider text-center">Riwayat</th>
                                    <th class="px-6 py-4 font-bold tracking-wider text-center">Status</th>
                                    <th class="px-6 py-4 font-bold tracking-wider text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($audits as $item)
                                <tr class="table-row-hover transition-colors">
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-slate-700">{{ $item->created_at->format('d M Y') }}</span>
                                            <span class="text-[10px] text-slate-400 mt-0.5">{{ $item->created_at->format('H:i') }} WIB</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 text-xs font-bold">
                                                {{ substr($item->user->name ?? '?', 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-800">{{ $item->user->name ?? 'Unknown' }}</div>
                                                <div class="text-[10px] text-slate-400 font-mono">{{ $item->user->kode_instansi ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col gap-1">
                                            <span class="font-medium text-slate-800 line-clamp-1">{{ $item->title }}</span>
                                            @if($item->admin_file)
                                                <a href="{{ asset('storage/' . $item->admin_file) }}" target="_blank" class="text-[10px] text-blue-600 hover:underline flex items-center gap-1">
                                                    <i class="fas fa-file-pdf"></i> Lihat Surat Terkirim
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <button onclick='openHistoryModal(@json($item->files ?? []), "{{ $item->status }}", "{{ $item->title }}")' class="text-slate-400 hover:text-amber-600 p-2 rounded-full hover:bg-amber-50 transition-all">
                                            <i class="fas fa-history text-lg"></i>
                                        </button>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        @if($item->status == 'approved')
                                            <span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider border border-emerald-200">Selesai</span>
                                        @elseif($item->file_path && $item->file_path !== '-')
                                            <span class="bg-amber-100 text-amber-700 px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider border border-amber-200 animate-pulse">Perlu Cek</span>
                                        @else
                                            <span class="bg-slate-100 text-slate-500 px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider border border-slate-200">Terkirim</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        @if($item->file_path && $item->file_path !== '-' && $item->status == 'pending')
                                            <button onclick="openVerifyModal('{{ $item->id }}', '{{ $item->title }}')" class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm transform active:scale-95 transition-all">
                                                Verifikasi
                                            </button>
                                        @else
                                            <span class="text-slate-300">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">Belum ada data audit.</td>
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

    <!-- MODAL TAMBAH AUDIT -->
    <div id="createModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeCreateModal()"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100">
                    <div class="bg-gradient-to-r from-amber-600 to-orange-600 px-6 py-4 flex justify-between items-center text-white font-bold">
                        <h3 class="text-lg flex items-center gap-2"><i class="fas fa-paper-plane"></i> Kirim Surat Audit</h3>
                        <button onclick="closeCreateModal()" class="text-amber-100 hover:text-white p-2 rounded-lg bg-white/10"><i class="fas fa-times"></i></button>
                    </div>

                    <form action="{{ route('survailen.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="category" value="pelatihan">
                        
                        <div class="px-6 py-6 space-y-5">
                            <div class="space-y-1.5 relative">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Tujuan Lembaga</label>
                                <input type="hidden" name="user_id" id="selected_user_id" required>
                                
                                <div class="relative" id="customDropdown">
                                    <button type="button" onclick="toggleDropdown()" class="w-full flex justify-between items-center rounded-xl border border-slate-300 bg-slate-50 p-3 text-sm text-left focus:ring-2 focus:ring-amber-500 transition-all outline-none">
                                        <span id="dropdownLabel" class="text-slate-400">-- Pilih Lembaga --</span>
                                        <i class="fas fa-chevron-down text-xs text-slate-400 transition-transform" id="dropdownIcon"></i>
                                    </button>

                                    <div id="dropdownMenu" class="dropdown-hidden dropdown-animate absolute z-50 mt-2 w-full bg-white border border-slate-200 rounded-xl shadow-xl overflow-hidden">
                                        <div class="p-2 border-b border-slate-100 sticky top-0 bg-white">
                                            <div class="relative">
                                                <i class="fas fa-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                                                <input type="text" id="userSearch" onkeyup="filterUsersDropdown(this.value)" placeholder="Cari..." class="w-full pl-9 pr-4 py-2 text-xs border border-slate-200 rounded-lg bg-slate-50 outline-none">
                                            </div>
                                        </div>
                                        <div class="max-h-48 overflow-y-auto modal-scroll py-1" id="userOptions">
                                            @foreach($users as $u)
                                            <div onclick="selectUserDropdown('{{ $u->id }}', '{{ $u->name }}', '{{ $u->kode_instansi }}')" class="user-option px-4 py-2.5 text-sm hover:bg-amber-50 cursor-pointer flex flex-col transition-colors border-b border-slate-50 last:border-0" data-name="{{ strtolower($u->name) }}">
                                                <span class="font-bold text-slate-700">{{ $u->name }}</span>
                                                <span class="text-[10px] text-slate-400 font-mono">{{ $u->kode_instansi ?? '-' }}</span>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Perihal / Judul</label>
                                <input type="text" name="title" class="block w-full rounded-xl border border-slate-300 p-3 text-sm focus:border-amber-500 outline-none transition-all" placeholder="Judul Audit..." required>
                            </div>

                            <div class="space-y-2 bg-slate-50 p-4 rounded-xl border border-slate-200">
                                <label class="block text-xs font-bold text-slate-700 uppercase flex justify-between">
                                    <span>Hasil Survailen (PDF)</span>
                                    <span class="text-[10px] bg-red-100 text-red-600 px-1.5 py-0.5 rounded uppercase">Wajib</span>
                                </label>
                                <input type="file" name="admin_file" onchange="validateFileSize(this)" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-white file:text-amber-700 border border-slate-300 rounded-lg cursor-pointer bg-white" accept=".pdf" required>
                                <p class="text-[10px] text-slate-400 italic mt-1"><i class="fas fa-info-circle mr-1"></i> Saiz fail maksimum: <span class="font-bold">10 MB</span></p>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nota Tambahan</label>
                                <textarea name="admin_note" rows="2" class="block w-full rounded-xl border border-slate-300 p-3 text-sm focus:border-amber-500 outline-none" placeholder="Instruksi khusus..."></textarea>
                            </div>
                        </div>

                        <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-slate-100">
                            <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-lg transition-all active:scale-95">Kirim</button>
                            <button type="button" onclick="closeCreateModal()" class="bg-white border border-slate-300 text-slate-700 px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-50 transition-all">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL VERIFIKASI -->
    <div id="verifyModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeVerifyModal()"></div>
        <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden animate-pop-in">
                <div class="bg-amber-500 text-white px-6 py-4 font-bold flex items-center gap-3">
                    <i class="fas fa-tasks"></i>
                    <h3>Verifikasi Respon Lembaga</h3>
                </div>
                <form id="verifyForm" method="POST" action="" enctype="multipart/form-data">
                    @csrf
                    <div class="px-6 py-6 space-y-4">
                        <p class="text-sm text-slate-600 italic" id="verifyLabelTitle"></p>
                        
                        <div class="space-y-3 bg-slate-50 p-4 rounded-xl border border-slate-200">
                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-slate-700 uppercase">Nota Balasan</label>
                                <textarea name="admin_note" rows="2" class="w-full rounded-lg border border-slate-300 p-2 text-xs outline-none" placeholder="Tulis catatan..."></textarea>
                            </div>
                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-slate-700 uppercase flex justify-between">
                                    <span>Fail Lampiran</span>
                                    <span class="text-[10px] bg-slate-200 px-1.5 py-0.5 rounded text-slate-500">OPSIONAL</span>
                                </label>
                                <input type="file" name="admin_file" onchange="validateFileSize(this)" class="w-full text-xs text-slate-500 file:px-3 file:py-1 file:rounded-md border border-slate-200 rounded-md bg-white">
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit" onclick="setVerifyAction('approve')" class="flex-1 bg-emerald-600 text-white py-2.5 rounded-xl font-bold hover:bg-emerald-700 shadow-md transition-all active:scale-95 flex items-center justify-center gap-2">
                                <i class="fas fa-check"></i> Selesai
                            </button>
                            <button type="submit" onclick="setVerifyAction('reject')" class="flex-1 bg-white border border-rose-200 text-rose-600 py-2.5 rounded-xl font-bold hover:bg-rose-50 shadow-sm transition-all active:scale-95 flex items-center justify-center gap-2">
                                <i class="fas fa-undo"></i> Revisi
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL SEJARAH AUDIT -->
    <div id="historyModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeHistoryModal()"></div>
        <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl w-full max-w-3xl shadow-2xl overflow-hidden border border-slate-100 animate-pop-in">
                <div class="bg-slate-900 text-white px-6 py-5 flex justify-between items-center">
                    <h3 class="font-bold flex items-center gap-3"><i class="fas fa-history"></i> Jejak Audit</h3>
                    <button onclick="closeHistoryModal()" class="text-slate-400 hover:text-white"><i class="fas fa-times"></i></button>
                </div>
                <div class="max-h-[60vh] overflow-y-auto bg-slate-50 modal-scroll" id="timelineContainer"></div>
                <div class="p-4 bg-white border-t border-slate-200 text-right">
                    <button onclick="closeHistoryModal()" class="px-6 py-2 bg-slate-200 rounded-xl text-sm font-bold text-slate-700">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // === LOGIKA NOTIFIKASI MODAL ===
        function closeNotification(modalId) {
            const modal = document.getElementById(modalId);
            if(modal) {
                modal.classList.add('opacity-0');
                modal.querySelector('div').classList.add('scale-95');
                setTimeout(() => { modal.style.display = 'none'; }, 300);
            }
        }

        // Auto Close Logic & Progress Bar
        document.addEventListener('DOMContentLoaded', () => {
            const successModal = document.getElementById('successModal');
            if(successModal) {
                const progressBar = document.getElementById('progressBar');
                setTimeout(() => {
                    progressBar.style.transition = 'width 4s linear';
                    progressBar.style.width = '0%';
                }, 100);

                setTimeout(() => { closeNotification('successModal'); }, 4200);
            }
        });

        // Validasi Fail
        function validateFileSize(input) {
            if (input.files && input.files[0]) {
                const fileSize = input.files[0].size / 1024 / 1024;
                if (fileSize > 10) {
                    // Jika file terlalu besar, kita bisa memicu modal error secara JS atau alert
                    alert('Saiz fail maksimum ialah 10 MB.');
                    input.value = '';
                }
            }
        }

        // Sidebar & UI Logic
        function toggleSidebar() { 
            const sidebar = document.getElementById('sidebarMobileContent');
            const overlay = document.getElementById('mobileSidebar');
            if(sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        }

        function openCreateModal() { document.getElementById('createModal').classList.remove('hidden'); }
        function closeCreateModal() { document.getElementById('createModal').classList.add('hidden'); closeDropdown(); }

        /* DROP DOWN LOGIC */
        function toggleDropdown() {
            document.getElementById('dropdownMenu').classList.toggle('dropdown-hidden');
            document.getElementById('dropdownIcon').classList.toggle('rotate-180');
        }
        function closeDropdown() {
            document.getElementById('dropdownMenu').classList.add('dropdown-hidden');
            document.getElementById('dropdownIcon').classList.remove('rotate-180');
        }
        function selectUserDropdown(id, name, kode) {
            document.getElementById('selected_user_id').value = id;
            const label = document.getElementById('dropdownLabel');
            label.innerText = `${name} (${kode || '-'})`;
            label.classList.remove('text-slate-400');
            label.classList.add('text-slate-800', 'font-bold');
            closeDropdown();
        }
        function filterUsersDropdown(val) {
            const query = val.toLowerCase();
            document.querySelectorAll('.user-option').forEach(opt => {
                opt.style.display = opt.getAttribute('data-name').includes(query) ? 'flex' : 'none';
            });
        }

        /* VERIFY LOGIC */
        let currentVerifyId = null;
        function openVerifyModal(id, title) {
            currentVerifyId = id;
            document.getElementById('verifyLabelTitle').innerText = "Perihal: " + title;
            document.getElementById('verifyModal').classList.remove('hidden');
        }
        function closeVerifyModal() { document.getElementById('verifyModal').classList.add('hidden'); }
        function setVerifyAction(action) {
            const form = document.getElementById('verifyForm');
            form.action = `{{ url('/submission') }}/${action}/${currentVerifyId}`;
        }

        /* HISTORY LOGIC */
        function openHistoryModal(files, status, title) {
            const container = document.getElementById('timelineContainer');
            container.innerHTML = '';
            if(!files || files.length === 0) {
                container.innerHTML = `<div class="py-12 text-center text-slate-400">Belum ada jejak sejarah.</div>`;
            } else {
                let timelineHTML = `<div class="p-8 space-y-6">`;
                files.sort((a, b) => b.version - a.version).forEach((file, index) => {
                    const date = new Date(file.created_at).toLocaleString('id-ID', {day:'numeric', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit'});
                    const isLatest = index === 0;
                    timelineHTML += `
                        <div class="relative pl-8 border-l-2 border-slate-200 last:border-0 pb-2">
                            <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full ${isLatest ? 'bg-amber-500 ring-4 ring-amber-100' : 'bg-slate-300'} border-2 border-white"></div>
                            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-[10px] font-bold uppercase text-slate-400">Versi ${file.version}</span>
                                    <span class="text-[10px] font-mono text-slate-400">${date}</span>
                                </div>
                                <div class="text-sm font-bold text-slate-800">${file.file_name || 'Inisiasi Surat Audit'}</div>
                                ${file.admin_note ? `<div class="mt-2 p-2 bg-amber-50 rounded text-[11px] text-amber-700 italic border border-amber-100">" ${file.admin_note} "</div>` : ''}
                                ${file.admin_file ? `<a href="/storage/${file.admin_file}" target="_blank" class="mt-3 inline-flex items-center gap-2 text-xs font-bold text-blue-600 hover:underline"><i class="fas fa-paperclip"></i> Lampiran Admin</a>` : ''}
                                ${file.file_path && file.file_path !== '-' ? `<a href="/storage/${file.file_path}" target="_blank" class="mt-2 inline-flex items-center gap-2 text-xs font-bold text-emerald-600 hover:underline"><i class="fas fa-file-alt"></i> Respon Lembaga</a>` : ''}
                            </div>
                        </div>`;
                });
                timelineHTML += `</div>`;
                container.innerHTML = timelineHTML;
            }
            document.getElementById('historyModal').classList.remove('hidden');
        }
        function closeHistoryModal() { document.getElementById('historyModal').classList.add('hidden'); }
        window.onclick = function(e) { if (!document.getElementById('customDropdown').contains(e.target)) closeDropdown(); }
    </script>
</body>
</html>