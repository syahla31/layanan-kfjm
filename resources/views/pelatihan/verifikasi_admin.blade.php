<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Verifikasi | SI-MUTU Admin</title>
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
                        'toast-in': 'toastIn 0.4s ease-out forwards',
                    },
                    keyframes: {
                        popIn: {
                            '0%': { opacity: '0', transform: 'scale(0.8) translateY(20px)' },
                            '100%': { opacity: '1', transform: 'scale(1) translateY(0)' },
                        },
                        toastIn: {
                            '0%': { opacity: '0', transform: 'translateY(-20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
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

    <!-- DATA FETCHING -->
    @php
        use App\Models\Submission;
        use App\Models\User;
        
        if (!isset($data)) {
            $data = Submission::with(['user']) 
                        ->where('category', 'pelatihan')
                        ->where('type', 'Verifikasi')
                        ->orderBy('created_at', 'desc')
                        ->get();
        }

        $users = User::where('role', 'user')->where('category', 'pelatihan')->get();

        $waitingUser = $data->whereNull('user_note')->where('status', 'pending')->count();
        $needReview = $data->whereNotNull('user_note')->where('status', 'pending')->count();
        $completed = $data->where('status', 'approved')->count();
    @endphp

    <!-- Container untuk Toast Dinamis (JS) -->
    <div id="toast-container" class="fixed top-5 right-5 z-[110] flex flex-col gap-3 pointer-events-none"></div>

    <!-- === POP-UP NOTIFIKASI MODAL (Server Side) === -->
    
    <!-- 1. Success Modal (Auto Close) -->
    <x-success-popup />

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
                    <button onclick="toggleSidebar()" class="p-2 text-slate-600 hover:text-purple-600 hover:bg-slate-100 rounded-lg transition-colors">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <span class="font-bold text-slate-800 text-sm tracking-wide">SI-MUTU <span class="text-purple-600">DKKN</span></span>
                </div>
                <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 text-xs font-bold border border-purple-200">
                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                </div>
            </div>

            <!-- HEADER DESKTOP -->
            <div class="hidden md:block">
                @include('components.pelatihan-header', [
                    'title' => 'Manajemen Verifikasi',
                    'subtitle' => 'Penerbitan dokumen verifikasi dan pemantauan konfirmasi lembaga'
                ])
            </div>

            <!-- MAIN CONTENT -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-6 lg:p-8 space-y-6">
                
                <!-- STATISTIK WIDGETS -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
                    <div class="relative bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:-translate-y-1 transition-all group overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <i class="fas fa-clock text-6xl text-slate-500"></i>
                        </div>
                        <div class="relative z-10">
                            <span class="bg-slate-100 text-slate-600 px-2 py-1 rounded-md text-[10px] font-bold uppercase">Menunggu</span>
                            <h2 class="text-4xl font-bold text-slate-800 mt-2">{{ $waitingUser }}</h2>
                            <p class="text-xs text-slate-400 mt-2">Menunggu respon lembaga</p>
                        </div>
                    </div>

                    <div class="relative bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:-translate-y-1 transition-all group overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <i class="fas fa-file-signature text-6xl text-purple-500"></i>
                        </div>
                        <div class="relative z-10">
                            <span class="bg-purple-100 text-purple-600 px-2 py-1 rounded-md text-[10px] font-bold uppercase animate-pulse">Konfirmasi Masuk</span>
                            <h2 class="text-4xl font-bold text-slate-800 mt-2">{{ $needReview }}</h2>
                            <p class="text-xs text-slate-400 mt-2">Perlu validasi admin</p>
                        </div>
                    </div>

                    <div class="relative bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:-translate-y-1 transition-all group overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <i class="fas fa-check-double text-6xl text-emerald-500"></i>
                        </div>
                        <div class="relative z-10">
                            <span class="bg-emerald-100 text-emerald-600 px-2 py-1 rounded-md text-[10px] font-bold uppercase">Selesai</span>
                            <h2 class="text-4xl font-bold text-slate-800 mt-2">{{ $completed }}</h2>
                            <p class="text-xs text-slate-400 mt-2">Dokumen terbit (Approved)</p>
                        </div>
                    </div>
                </div>

                <!-- TABEL DATA -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4">
                        <div>
                            <h3 class="font-bold text-slate-800 text-lg">Daftar Dokumen Keluar</h3>
                            <p class="text-sm text-slate-500 mt-0.5">Arsip dokumen verifikasi yang diterbitkan</p>
                        </div>
                        <button onclick="openCreateModal()" class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2.5 rounded-xl shadow-lg shadow-purple-200 transition-all flex items-center gap-2 font-bold text-sm transform active:scale-95">
                            <i class="fas fa-plus-circle"></i> Terbitkan Verifikasi Baru
                        </button>
                    </div>

                    <div class="overflow-x-auto no-scrollbar">
                        <table class="w-full text-sm text-left text-slate-600 min-w-[900px]">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-6 py-4 font-bold tracking-wider">Tgl Terbit</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Lembaga Tujuan</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Judul Dokumen</th>
                                    <th class="px-6 py-4 font-bold tracking-wider text-center">File Terkirim</th>
                                    <th class="px-6 py-4 font-bold tracking-wider text-center">Status</th>
                                    <th class="px-6 py-4 font-bold tracking-wider text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($data as $item)
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
                                        <span class="text-sm font-medium text-slate-800 line-clamp-1">{{ $item->title }}</span>
                                        @if($item->admin_note)
                                            <p class="text-[10px] text-slate-400 italic mt-1 truncate max-w-[200px]">"{{ $item->admin_note }}"</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <a href="{{ asset('storage/' . $item->admin_file) }}" target="_blank" class="text-purple-600 hover:text-purple-800 bg-purple-50 px-3 py-1.5 rounded-lg text-xs font-bold border border-purple-100">
                                            <i class="fas fa-file-pdf"></i> PDF
                                        </a>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        @if($item->status == 'approved')
                                            <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-[10px] font-bold uppercase border border-emerald-200">Selesai</span>
                                        @elseif($item->status == 'rejected')
                                            <span class="bg-rose-100 text-rose-700 px-3 py-1 rounded-full text-[10px] font-bold uppercase border border-rose-200">Ditolak</span>
                                        @elseif($item->user_note)
                                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-[10px] font-bold uppercase border border-blue-200 animate-pulse">Konfirmasi</span>
                                        @else
                                            <span class="bg-slate-100 text-slate-500 px-3 py-1 rounded-full text-[10px] font-bold uppercase border border-slate-200">Menunggu</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        @if($item->user_note && $item->status == 'pending')
                                            <button onclick="openVerifyModal('{{ $item->id }}', '{{ $item->title }}')" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm active:scale-95 transition-all">
                                                Verifikasi
                                            </button>
                                        @elseif($item->status == 'approved')
                                            <i class="fas fa-check-circle text-emerald-500 text-lg"></i>
                                        @else
                                            <span class="text-slate-300">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">Belum ada dokumen verifikasi.</td>
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

    <!-- MODAL CREATE -->
    <div id="createModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeCreateModal()"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100">
                    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4 flex justify-between items-center text-white font-bold">
                        <h3 class="text-lg flex items-center gap-2"><i class="fas fa-plus-circle"></i> Terbitkan Verifikasi</h3>
                        <button onclick="closeCreateModal()" class="text-purple-100 hover:text-white p-2 rounded-lg bg-white/10"><i class="fas fa-times"></i></button>
                    </div>

                    <form action="{{ route('verifikasi.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="category" value="pelatihan">
                        <div class="px-6 py-6 space-y-5">
                            <!-- KUSTOM DROPDOWN TUJUAN LEMBAGA -->
                            <div class="space-y-1.5 relative">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Tujuan Lembaga</label>
                                <input type="hidden" name="user_id" id="selected_user_id" required>
                                
                                <div class="relative" id="customDropdown">
                                    <button type="button" onclick="toggleDropdown()" class="w-full flex justify-between items-center rounded-xl border border-slate-300 bg-slate-50 p-3 text-sm text-left focus:ring-2 focus:ring-purple-500 transition-all outline-none">
                                        <span id="dropdownLabel" class="text-slate-400">-- Pilih Lembaga --</span>
                                        <i class="fas fa-chevron-down text-xs text-slate-400 transition-transform" id="dropdownIcon"></i>
                                    </button>

                                    <div id="dropdownMenu" class="dropdown-hidden dropdown-animate absolute z-50 mt-2 w-full bg-white border border-slate-200 rounded-xl shadow-xl overflow-hidden">
                                        <div class="p-2 border-b border-slate-100 sticky top-0 bg-white">
                                            <div class="relative">
                                                <i class="fas fa-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                                                <input type="text" id="userSearch" onkeyup="filterUsersDropdown(this.value)" placeholder="Cari lembaga..." class="w-full pl-9 pr-4 py-2 text-xs border border-slate-200 rounded-lg bg-slate-50 outline-none">
                                            </div>
                                        </div>
                                        <div class="max-h-48 overflow-y-auto modal-scroll py-1" id="userOptions">
                                            @foreach($users as $u)
                                            <div onclick="selectUserDropdown('{{ $u->id }}', '{{ $u->name }}', '{{ $u->kode_instansi }}')" class="user-option px-4 py-2.5 text-sm hover:bg-purple-50 cursor-pointer flex flex-col transition-colors border-b border-slate-50 last:border-0" data-name="{{ strtolower($u->name) }}">
                                                <span class="font-bold text-slate-700">{{ $u->name }}</span>
                                                <span class="text-[10px] text-slate-400 font-mono">{{ $u->kode_instansi ?? '-' }}</span>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Judul Dokumen</label>
                                <input type="text" name="title" class="block w-full rounded-xl border border-slate-300 p-3 text-sm focus:border-purple-500 outline-none transition-all" placeholder="Contoh: SK Akreditasi 2026" required>
                            </div>

                            <div class="space-y-2 bg-slate-50 p-4 rounded-xl border border-slate-200">
                                <label class="block text-xs font-bold text-slate-700 uppercase flex justify-between">
                                    <span>Upload File (PDF)</span>
                                    <span class="text-[10px] bg-red-100 text-red-600 px-1.5 py-0.5 rounded uppercase">Wajib</span>
                                </label>
                                <input type="file" name="admin_file" onchange="validateFileSize(this)" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-white file:text-purple-700 border border-slate-300 rounded-lg cursor-pointer bg-white" accept=".pdf" required>
                                <p class="text-[10px] text-slate-400 italic mt-1"><i class="fas fa-info-circle mr-1"></i> Ukuran file maksimal: <span class="font-bold">2 MB</span></p>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Catatan</label>
                                <textarea name="admin_note" rows="2" class="block w-full rounded-xl border border-slate-300 p-3 text-sm focus:border-purple-500 outline-none" placeholder="Pesan untuk lembaga..."></textarea>
                            </div>
                        </div>

                        <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-slate-100">
                            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-lg transition-all active:scale-95">Terbitkan</button>
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
                <div class="bg-blue-600 text-white px-6 py-4 font-bold flex items-center gap-3">
                    <i class="fas fa-tasks"></i>
                    <h3>Verifikasi Konfirmasi</h3>
                </div>
                <form id="verifyForm" method="POST" action="">
                    @csrf
                    <div class="px-6 py-6 space-y-4">
                        <div class="p-3 bg-blue-50 border border-blue-100 rounded-xl">
                            <p class="text-[10px] font-bold text-blue-600 uppercase mb-1">Judul Dokumen:</p>
                            <p class="text-sm text-slate-700 font-bold line-clamp-2" id="verifyTitleDisplay"></p>
                        </div>
                        
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-slate-700 uppercase">Catatan Tambahan</label>
                            <textarea name="admin_note" rows="2" class="w-full rounded-xl border border-slate-300 p-3 text-sm outline-none bg-slate-50 focus:border-blue-500" placeholder="Tulis alasan jika menolak..."></textarea>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit" onclick="setVerifyAction('approve')" class="flex-1 bg-emerald-600 text-white py-2.5 rounded-xl font-bold hover:bg-emerald-700 shadow-md transition-all active:scale-95 flex items-center justify-center gap-2">
                                <i class="fas fa-check"></i> Selesai
                            </button>
                            <button type="submit" onclick="setVerifyAction('reject')" class="flex-1 bg-white border border-rose-200 text-rose-600 py-2.5 rounded-xl font-bold hover:bg-rose-50 shadow-sm transition-all active:scale-95 flex items-center justify-center gap-2">
                                <i class="fas fa-times"></i> Tolak
                            </button>
                        </div>
                    </div>
                </form>
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

        // Fungsi untuk menampilkan pesan error melayang (Toast)
        function showErrorToast(title, message) {
            const container = document.getElementById('toast-container');
            const toastId = 'toast-' + Date.now();
            const toastHTML = `
                <div id="${toastId}" class="pointer-events-auto bg-white border-l-4 border-rose-500 shadow-xl rounded-xl overflow-hidden animate-toast-in w-72">
                    <div class="p-4 flex items-start gap-3">
                        <div class="bg-rose-100 p-2 rounded-full text-rose-600 shrink-0">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-bold text-slate-800">${title}</p>
                            <p class="text-[10px] text-slate-500 mt-0.5 leading-tight">${message}</p>
                        </div>
                        <button onclick="document.getElementById('${toastId}').remove()" class="text-slate-400 hover:text-slate-600">
                            <i class="fas fa-times text-[10px]"></i>
                        </button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', toastHTML);
            setTimeout(() => {
                const toast = document.getElementById(toastId);
                if (toast) {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateX(20px)';
                    toast.style.transition = 'all 0.4s ease';
                    setTimeout(() => toast.remove(), 400);
                }
            }, 5000);
        }

        // Fungsi Validasi Ukuran File (JS)
        function validateFileSize(input) {
            if (input.files && input.files[0]) {
                const fileSize = input.files[0].size / 1024 / 1024; // MB
                if (fileSize > 2) {
                    showErrorToast('File Terlalu Besar', 'Maksimal ukuran file adalah 2 MB. Silakan kompres file Anda.');
                    input.value = ''; // Reset input
                }
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

        // Sidebar logic
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

        /* === LOGIKA CUSTOM DROPDOWN === */
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
                const name = opt.getAttribute('data-name');
                opt.style.display = name.includes(query) ? 'flex' : 'none';
            });
        }

        // Menutup dropdown jika klik di luar area
        window.onclick = function(event) {
            if (!document.getElementById('customDropdown').contains(event.target)) {
                closeDropdown();
            }
        }

        let currentVerifyId = null;
        function openVerifyModal(id, title) {
            currentVerifyId = id;
            document.getElementById('verifyTitleDisplay').innerText = title;
            document.getElementById('verifyModal').classList.remove('hidden');
        }
        function closeVerifyModal() { document.getElementById('verifyModal').classList.add('hidden'); }
        
        function setVerifyAction(action) {
            const form = document.getElementById('verifyForm');
            if(action === 'approve') form.action = "{{ url('/submission/approve') }}/" + currentVerifyId;
            else form.action = "{{ url('/submission/reject') }}/" + currentVerifyId;
        }
    </script>
</body>
</html>