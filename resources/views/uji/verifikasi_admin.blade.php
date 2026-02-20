<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Verifikasi Uji | SI-LAB ADMIN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Konfigurasi Animasi Tailwind -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    animation: { 
                        'pop-in': 'popIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                        'slide-in': 'slideIn 0.4s ease-out forwards',
                        'fade-in': 'fadeIn 0.3s ease-out forwards',
                    },
                    keyframes: {
                        popIn: {
                            '0%': { opacity: '0', transform: 'scale(0.95) translateY(10px)' },
                            '100%': { opacity: '1', transform: 'scale(1) translateY(0)' },
                        },
                        slideIn: {
                            '0%': { transform: 'translateX(-10px)', opacity: '0' },
                            '100%': { transform: 'translateX(0)', opacity: '1' },
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        .table-row-hover:hover td { background-color: #f5f3ff; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none;  scrollbar-width: none; }
        
        /* Custom Scrollbar Ramping */
        .modal-scroll::-webkit-scrollbar { width: 4px; }
        .modal-scroll::-webkit-scrollbar-track { background: transparent; }
        .modal-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        
        .modal-backdrop-blur { backdrop-filter: blur(4px); background-color: rgba(15, 23, 42, 0.6); }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-indigo-100 selection:text-indigo-700">

    <!-- 1. DATA PREPARATION -->
    @php
        use App\Models\Submission;
        use App\Models\User;
        
        if (!isset($verifikasis)) {
            $verifikasis = Submission::with(['user', 'files']) 
                        ->where('category', 'uji')
                        ->where('type', 'like', '%Verifikasi%')
                        ->orderBy('created_at', 'desc')
                        ->get();
        }

        $users = User::where('role', 'user')->where('category', 'uji')->get();

        // Statistik
        $waitingUser = $verifikasis->where('file_path', '-')->count();
        $needReview = $verifikasis->where('file_path', '!=', '-')->where('status', 'pending')->count();
        $completed = $verifikasis->where('status', 'approved')->count();
    @endphp

    <div class="flex h-screen overflow-hidden bg-slate-50">
        
        <!-- SIDEBAR -->
        @include('components.uji-sidebar')

        <div class="flex-1 flex flex-col h-screen overflow-hidden relative w-full text-left">
            
            <!-- HEADER -->
            @include('components.uji-header', [
                'title' => 'Manajemen Verifikasi',
                'subtitle' => 'Validasi teknis dan penunjukan laboratorium uji'
            ])

            <!-- MAIN CONTENT -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-8 space-y-6">
                
                <!-- SUCCESS POP-UP (Otomatis Tertutup) -->
                @if (session('success'))
                <div id="successModal" class="fixed inset-0 z-[100] flex items-center justify-center modal-backdrop-blur transition-opacity duration-300">
                    <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-sm w-full mx-4 text-center animate-pop-in relative border border-slate-100">
                        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-indigo-500 to-blue-600"></div>
                        <div class="w-16 h-16 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-4 shadow-inner">
                            <i class="fas fa-check text-3xl text-indigo-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 mb-2">Berhasil!</h3>
                        <p class="text-slate-500 mb-6 text-sm font-medium leading-relaxed">{{ session('success') }}</p>
                        <div class="relative w-full bg-slate-100 h-1 rounded-full mb-6 overflow-hidden">
                            <div id="progressBar" class="h-full bg-indigo-500 rounded-full transition-all ease-linear" style="width: 100%"></div>
                        </div>
                        <button onclick="closeNotification('successModal')" class="w-full bg-slate-900 text-white font-bold py-3 rounded-xl hover:bg-black transition-all active:scale-95 shadow-lg">Tutup</button>
                    </div>
                </div>
                @endif

                <!-- STATISTIK WIDGETS -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-5 transition-all hover:shadow-md">
                        <div class="w-14 h-14 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 border border-slate-100 shrink-0">
                            <i class="fas fa-paper-plane text-xl"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">Menunggu Lab</p>
                            <h3 class="text-3xl font-black text-slate-800 mt-2">{{ $waitingUser }}</h3>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-indigo-100 shadow-sm flex items-center gap-5 relative overflow-hidden group transition-all hover:shadow-md">
                        <div class="absolute top-0 right-0 w-1 bg-indigo-500 h-full"></div>
                        <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 border border-indigo-100 shrink-0">
                            <i class="fas fa-clock text-xl"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest leading-none">Perlu Review</p>
                                <span class="flex h-1.5 w-1.5 rounded-full bg-indigo-500 animate-ping"></span>
                            </div>
                            <h3 class="text-3xl font-black text-slate-800 mt-2">{{ $needReview }}</h3>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-5 transition-all hover:shadow-md">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 border border-emerald-100 shrink-0">
                            <i class="fas fa-check-double text-xl"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest leading-none">Verifikasi Selesai</p>
                            <h3 class="text-3xl font-black text-slate-800 mt-2">{{ $completed }}</h3>
                        </div>
                    </div>
                </div>

                <!-- TABLE SECTION -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b flex flex-col md:flex-row justify-between items-center gap-4">
                        <div>
                            <h3 class="font-bold text-slate-800 text-lg tracking-tight">Antrian Verifikasi</h3>
                            <p class="text-xs text-slate-400 mt-0.5 font-bold uppercase tracking-wider">Unit Kerja: Lembaga Uji</p>
                        </div>
                        <button onclick="openCreateModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl shadow-lg shadow-indigo-100 font-bold text-sm transform active:scale-95 transition-all flex items-center gap-2">
                            <i class="fas fa-plus text-xs"></i> Mulai Verifikasi
                        </button>
                    </div>

                    <div class="overflow-x-auto no-scrollbar">
                        <table class="w-full text-sm text-left">
                            <thead class="text-[11px] text-slate-400 uppercase bg-slate-50/50 border-b font-black tracking-widest">
                                <tr>
                                    <th class="px-6 py-4">Tgl Inisiasi</th>
                                    <th class="px-6 py-4">Laboratorium</th>
                                    <th class="px-6 py-4">Topik Verifikasi</th>
                                    <th class="px-6 py-4 text-center">Laporan Lab</th>
                                    <th class="px-6 py-4 text-center w-20">Jejak</th>
                                    <th class="px-6 py-4 text-center">Status</th>
                                    <th class="px-6 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($verifikasis as $item)
                                <tr class="table-row-hover transition-colors group cursor-pointer" onclick='openHistoryModal(@json($item->files ?? []), "{{ $item->status }}", "{{ $item->title }}")'>
                                    <td class="px-6 py-5 whitespace-nowrap text-left text-slate-600 font-bold">
                                        {{ $item->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-5 text-left">
                                        <div class="font-bold text-slate-800 group-hover:text-indigo-600 transition-colors">{{ $item->user->name ?? 'Unknown' }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono tracking-tighter">{{ $item->user->kode_instansi ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-5 text-left">
                                        <span class="font-medium text-slate-700 text-sm line-clamp-1">{{ $item->title }}</span>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        @if($item->file_path && $item->file_path !== '-')
                                            <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank" onclick="event.stopPropagation()" class="text-indigo-600 font-bold text-[10px] uppercase bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-100 hover:bg-indigo-100 transition-colors">Unduh</a>
                                        @else
                                            <span class="text-[10px] text-slate-300 font-bold uppercase italic">Menunggu</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center mx-auto text-slate-300 group-hover:bg-indigo-50 group-hover:text-indigo-500 transition-all">
                                            <i class="fas fa-history"></i>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        @php
                                            $st = match($item->status) {
                                                'approved' => ['Selesai', 'bg-emerald-100 text-emerald-700 border-emerald-200'],
                                                'rejected' => ['Revisi', 'bg-rose-100 text-rose-700 border-rose-200'],
                                                default => ($item->file_path !== '-' ? ['Review', 'bg-indigo-100 text-indigo-700 border-indigo-200 animate-pulse'] : ['Terkirim', 'bg-slate-100 text-slate-500 border-slate-200'])
                                            };
                                        @endphp
                                        <span class="{{ $st[1] }} px-3 py-1 rounded-full text-[10px] font-black border uppercase tracking-tighter">{{ $st[0] }}</span>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        @if($item->file_path !== '-' && $item->status == 'pending')
                                            <button onclick="event.stopPropagation(); openVerifyModal('{{ $item->id }}', '{{ $item->title }}')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-[10px] font-black uppercase shadow-sm active:scale-95 transition-all">Verifikasi</button>
                                        @elseif($item->status == 'approved')
                                            <i class="fas fa-check-circle text-emerald-500 text-xl"></i>
                                        @else
                                            <span class="text-slate-300 font-bold">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="py-32 text-center opacity-30">
                                        <i class="fas fa-inbox text-5xl mb-4"></i>
                                        <p class="font-black uppercase tracking-widest text-xs">Belum ada verifikasi aktif</p>
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

    <!-- MODAL 1: INISIASI BARU -->
    <div id="createModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div class="fixed inset-0 modal-backdrop-blur transition-opacity duration-300" onclick="closeCreateModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md border border-slate-100 animate-pop-in overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 px-6 py-4 flex justify-between items-center text-white">
                <div>
                    <h3 class="text-lg font-bold">Kirim Surat Verifikasi</h3>
                    <p class="text-[10px] text-indigo-200 font-bold uppercase tracking-widest">Inisiasi Penunjukan</p>
                </div>
                <button onclick="closeCreateModal()" class="w-8 h-8 rounded-full hover:bg-white/10 flex items-center justify-center transition-colors"><i class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('uji.verifikasi_admin.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5 text-left">
                @csrf <input type="hidden" name="category" value="uji">
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Laboratorium Penerima</label>
                    <select name="user_id" class="w-full bg-slate-50 border-slate-200 rounded-xl p-3 text-sm font-medium focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none" required>
                        <option value="" disabled selected>-- Pilih Lembaga Uji --</option>
                        @foreach($users as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Judul Verifikasi</label>
                    <input type="text" name="title" class="w-full border-slate-200 rounded-xl p-3 text-sm font-bold focus:ring-2 focus:ring-indigo-500/20 outline-none" placeholder="Misal: Verifikasi Lokasi Cilegon 2026" required>
                </div>
                <div class="bg-indigo-50/50 p-4 rounded-xl border border-indigo-100">
                    <label class="text-[10px] font-black text-indigo-600 uppercase flex justify-between"><span>Surat Penugasan (PDF)</span> <span class="bg-indigo-600 text-white px-1 rounded text-[8px]">WAJIB</span></label>
                    <input type="file" name="admin_file" class="w-full text-sm mt-3 file:mr-4 file:py-1.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-white file:text-indigo-600 file:shadow-sm" accept=".pdf" required>
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Instruksi Admin (Opsional)</label>
                    <textarea name="admin_note" rows="2" class="w-full border-slate-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500/20 outline-none" placeholder="Tambahkan instruksi khusus di sini..."></textarea>
                </div>
                <div class="flex gap-4 pt-3">
                    <button type="button" onclick="closeCreateModal()" class="flex-1 py-3 rounded-xl border-2 border-slate-100 font-bold text-xs uppercase text-slate-500 hover:bg-slate-50 transition-all">Batal</button>
                    <button type="submit" class="flex-1 py-3 rounded-xl bg-indigo-600 text-white font-black text-xs uppercase shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-all active:scale-95">Kirim Surat</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: VERIFIKASI / REVIEW -->
    <div id="verifyModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div class="fixed inset-0 modal-backdrop-blur" onclick="closeVerifyModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md border border-slate-100 animate-pop-in overflow-hidden">
            <form id="verifyForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="bg-white px-6 py-5 border-b flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 leading-tight">Review Laporan Lab</h3>
                        <p class="text-[10px] text-slate-400 font-bold uppercase truncate mt-0.5" id="verifyTitle"></p>
                    </div>
                    <button type="button" onclick="closeVerifyModal()" class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-400 transition-colors"><i class="fas fa-times"></i></button>
                </div>
                <div class="p-6 space-y-4">
                    <div class="bg-slate-50 p-5 rounded-xl border border-slate-200 space-y-5 text-left">
                        <div>
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-2">Evaluasi Admin (Keputusan)</label>
                            <textarea name="admin_note" rows="3" class="w-full border-slate-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500/20 outline-none" required placeholder="Jelaskan alasan persetujuan atau poin-poin revisi..."></textarea>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-2">Lampiran File Evaluasi</label>
                            <input type="file" name="admin_file" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-4 file:rounded-xl file:border-0 file:bg-white file:text-indigo-600 file:font-bold file:shadow-sm">
                        </div>
                    </div>
                    <div class="flex gap-4 pt-2">
                        <button type="submit" onclick="setVerifyAction('approve')" class="flex-1 bg-emerald-600 text-white py-3.5 rounded-xl font-black text-xs uppercase tracking-widest active:scale-95 transition-all shadow-lg shadow-emerald-100">Setujui</button>
                        <button type="submit" onclick="setVerifyAction('reject')" class="flex-1 bg-white border-2 border-rose-100 text-rose-600 py-3.5 rounded-xl font-black text-xs uppercase tracking-widest active:scale-95 transition-all">Minta Revisi</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: TIMELINE HISTORY -->
    <div id="historyModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div class="fixed inset-0 modal-backdrop-blur" onclick="closeHistoryModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg border border-slate-100 animate-pop-in overflow-hidden text-left flex flex-col">
            <div class="bg-gradient-to-br from-slate-800 to-slate-900 px-6 py-5 flex justify-between items-center text-white">
                <div>
                    <h3 class="text-xl font-black flex items-center gap-3"><div class="bg-indigo-500 p-2 rounded-xl shadow-lg"><i class="fas fa-history"></i></div> Jejak Verifikasi</h3>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-1.5" id="historyTitle"></p>
                </div>
                <button onclick="closeHistoryModal()" class="w-10 h-10 rounded-full hover:bg-white/10 flex items-center justify-center transition-colors"><i class="fas fa-times"></i></button>
            </div>
            <div class="max-h-[65vh] overflow-y-auto bg-slate-50 no-scrollbar p-6">
                <div id="timelineContainer" class="relative"></div>
            </div>
            <div class="bg-white px-6 py-4 flex justify-end border-t border-slate-100 shadow-inner">
                <button onclick="closeHistoryModal()" class="px-8 py-3 bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-md hover:bg-black transition-all active:scale-95">Tutup Detail</button>
            </div>
        </div>
    </div>

    <script>
        // MODAL TOGGLE UTILS
        function toggleModal(id, show) {
            const modal = document.getElementById(id);
            if (show) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            } else {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        function openCreateModal() { toggleModal('createModal', true); }
        function closeCreateModal() { toggleModal('createModal', false); }
        
        let currentVerifyId = null;
        function openVerifyModal(id, title) { 
            currentVerifyId = id; 
            document.getElementById('verifyTitle').innerText = title; 
            toggleModal('verifyModal', true); 
        }
        function closeVerifyModal() { toggleModal('verifyModal', false); }
        
        function setVerifyAction(action) { 
            const form = document.getElementById('verifyForm'); 
            if(action === 'approve') form.action = "{{ url('/submission/approve') }}/" + currentVerifyId; 
            else form.action = "{{ url('/submission/reject') }}/" + currentVerifyId; 
        }
        
        function closeHistoryModal() { toggleModal('historyModal', false); }

        // SUCCESS NOTIF LOGIC
        function closeNotification(modalId) {
            const modal = document.getElementById(modalId);
            if(modal) {
                modal.style.opacity = '0';
                setTimeout(() => { modal.classList.add('hidden'); modal.classList.remove('flex'); }, 300);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const successModal = document.getElementById('successModal');
            if(successModal) {
                successModal.classList.add('flex');
                const bar = document.getElementById('progressBar');
                setTimeout(() => {
                    bar.style.transition = 'width 3s linear';
                    bar.style.width = '0%';
                }, 100);
                setTimeout(() => { closeNotification('successModal'); }, 3100);
            }
        });

        // TIMELINE LOGIC
        function openHistoryModal(files, currentStatus, docTitle) {
            const container = document.getElementById('timelineContainer');
            document.getElementById('historyTitle').innerText = docTitle;
            container.innerHTML = ''; 
            
            if(!files || files.length === 0) {
                container.innerHTML = `<div class="py-16 text-center text-slate-300 font-bold uppercase tracking-widest opacity-40 text-xs">Belum ada riwayat tercatat</div>`;
            } else {
                files.sort((a, b) => a.version - b.version);
                
                files.forEach((file, index) => {
                    const d = new Date(file.created_at);
                    const dateStr = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
                    let isLatest = (index === files.length - 1);
                    let isStart = (file.version == 0);
                    
                    if (isStart) {
                        // Card 1: Inisiasi
                        container.innerHTML += `
                            <div class="relative flex gap-5 pb-8">
                                <div class="absolute top-0 left-4 -bottom-8 w-0.5 bg-slate-200"></div>
                                <div class="relative z-10 flex-shrink-0 w-8 h-8 rounded-full bg-amber-500 text-white flex items-center justify-center text-[10px] font-black ring-4 ring-amber-50 shadow-sm"><i class="fas fa-flag"></i></div>
                                <div class="flex-1 bg-white rounded-2xl p-4 border border-slate-200 shadow-sm text-left">
                                    <div class="flex justify-between items-start mb-2">
                                        <span class="text-[11px] font-black text-slate-800 uppercase tracking-tight">Admin: Inisiasi Awal</span>
                                        <span class="text-[9px] text-slate-400 font-mono bg-slate-50 px-2 py-0.5 rounded-full border">${dateStr}</span>
                                    </div>
                                    <div class="mt-3 bg-indigo-50 rounded-xl p-3 border border-indigo-100 flex items-center gap-4 transition-colors hover:bg-indigo-100/50">
                                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-red-500 shadow-sm shrink-0"><i class="fas fa-file-pdf text-xl"></i></div>
                                        <div class="flex-1 min-w-0 text-left">
                                            <p class="text-[9px] font-black text-indigo-600 uppercase tracking-wide leading-none mb-1">Surat Penugasan</p>
                                            <a href="/storage/${file.admin_file}" target="_blank" class="text-xs font-bold text-slate-800 hover:text-indigo-600 underline truncate block">Unduh Penugasan Admin</a>
                                        </div>
                                    </div>
                                    ${file.admin_note ? `<div class="mt-3 p-3 bg-slate-50/50 border border-slate-100 rounded-xl text-xs text-slate-500 italic leading-relaxed">"${file.admin_note}"</div>` : ''}
                                </div>
                            </div>`;
                    } else {
                        // Card 2: User Laporan
                        if (file.file_path && file.file_path !== '-') {
                            container.innerHTML += `
                                <div class="relative flex gap-5 pb-8">
                                    <div class="absolute top-0 left-4 -bottom-8 w-0.5 bg-slate-200"></div>
                                    <div class="relative z-10 flex-shrink-0 w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-[10px] font-black ring-4 ring-blue-50 shadow-sm">v${file.version}</div>
                                    <div class="flex-1 bg-white rounded-2xl p-4 border border-slate-200 shadow-sm text-left hover:border-blue-300 transition-colors">
                                        <div class="flex justify-between items-start mb-2">
                                            <span class="text-[11px] font-black text-blue-700 uppercase tracking-tight">User: Laporan Lab</span>
                                            <span class="text-[9px] text-slate-400 font-mono bg-slate-50 px-2 py-0.5 rounded-full border border-slate-100">${dateStr}</span>
                                        </div>
                                        <div class="mt-3 flex items-center gap-4 bg-slate-50 p-3 rounded-xl border border-slate-200 cursor-pointer group/file" onclick="window.open('/storage/${file.file_path}', '_blank')">
                                            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 shrink-0 group-hover/file:bg-blue-100 transition-colors"><i class="fas fa-file-alt text-xl"></i></div>
                                            <div class="flex-1 min-w-0 text-left">
                                                <p class="text-[9px] font-black text-blue-600 uppercase tracking-wide leading-none mb-1">Bukti Tindak Lanjut</p>
                                                <p class="text-xs font-bold text-slate-800 truncate">${file.file_name || 'Berkas Laporan'}</p>
                                            </div>
                                        </div>
                                        ${file.user_note ? `<div class="mt-3 p-3 bg-white border border-slate-100 rounded-xl text-xs text-slate-500 italic border-l-4 border-l-blue-500 leading-relaxed">"${file.user_note}"</div>` : ''}
                                    </div>
                                </div>`;
                        }

                        // Card 3: Admin Review
                        if (file.admin_note || file.admin_file) {
                            let badgeHTML = '';
                            if (isLatest) {
                                if (currentStatus === 'approved') badgeHTML = `<span class="bg-emerald-500 text-white px-3 py-1 rounded-full text-[8px] font-black shadow-sm uppercase tracking-wider">Disetujui</span>`;
                                else if (currentStatus === 'rejected') badgeHTML = `<span class="bg-rose-500 text-white px-3 py-1 rounded-full text-[8px] font-black shadow-sm uppercase tracking-wider">Minta Revisi</span>`;
                            } else {
                                badgeHTML = `<span class="bg-slate-100 text-slate-500 px-3 py-1 rounded-full text-[8px] font-black uppercase border border-slate-200">Arsip</span>`;
                            }

                            container.innerHTML += `
                                <div class="relative flex gap-5 pb-8 last:pb-0">
                                    <div class="absolute top-0 left-4 -bottom-8 w-0.5 bg-slate-200 last:hidden"></div>
                                    <div class="relative z-10 flex-shrink-0 w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center text-[10px] font-black ring-4 ring-slate-100 shadow-sm"><i class="fas fa-check-circle text-xs"></i></div>
                                    <div class="flex-1 bg-slate-50 rounded-2xl p-4 border border-slate-200 shadow-sm text-left transition-all hover:shadow-md">
                                        <div class="flex justify-between items-center mb-3">
                                            <span class="text-[11px] font-black text-slate-700 uppercase tracking-tight">Admin: Evaluasi</span>
                                            ${badgeHTML}
                                        </div>
                                        <div class="bg-white border border-slate-200 rounded-xl p-4 text-[11px] text-slate-700 leading-relaxed italic border-l-4 border-l-indigo-500 shadow-sm text-left">
                                            "${file.admin_note || 'Pemeriksaan laporan verifikasi selesai.'}"
                                        </div>
                                        ${file.admin_file ? `<a href="/storage/${file.admin_file}" target="_blank" class="mt-3.5 flex items-center gap-3 text-[11px] font-extrabold text-blue-600 bg-white p-2.5 rounded-xl border border-blue-100 hover:bg-blue-50 transition-all shadow-sm"><i class="fas fa-paperclip"></i> Dokumen Evaluasi Admin</a>` : ''}
                                    </div>
                                </div>`;
                        }
                    }
                });
            }
            toggleModal('historyModal', true);
        }
    </script>
</body>
</html>