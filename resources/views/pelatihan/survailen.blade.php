<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survailen & Audit | SI-MUTU</title>
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
                        'fade-in': 'fadeIn 0.3s ease-out forwards'
                    },
                    keyframes: {
                        popIn: {
                            '0%': { opacity: '0', transform: 'scale(0.95) translateY(10px)' },
                            '100%': { opacity: '1', transform: 'scale(1) translateY(0)' },
                        },
                        fadeOut: {
                            '0%': { opacity: '1' },
                            '100%': { opacity: '0' },
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
        .table-row-hover:hover td { background-color: #f0fdfa; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none;  scrollbar-width: none; }
        
        .modal-scroll::-webkit-scrollbar { width: 4px; }
        .modal-scroll::-webkit-scrollbar-track { background: transparent; }
        .modal-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        
        .modal-backdrop-blur { backdrop-filter: blur(4px); background-color: rgba(15, 23, 42, 0.6); }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-teal-100 selection:text-teal-900">

    <!-- 1. DATA PREPARATION -->
    @php
        use App\Models\Submission;
        use Illuminate\Support\Facades\Auth;
        
        if (!isset($survailens)) {
            $survailens = Submission::where('user_id', Auth::id())
                               ->where('type', 'Survailen')
                               ->orderBy('created_at', 'desc')
                               ->with('files')
                               ->get();
        }

        // Statistik Logic - Sesuai dengan gaya Survailen
        $needAction = $survailens->filter(fn($item) => empty($item->file_path) || $item->file_path === '-' || $item->status == 'rejected')->count();
        $waitingReview = $survailens->where('status', 'pending')->where('file_path', '!=', '-')->whereNotNull('file_path')->count();
        $completed = $survailens->where('status', 'approved')->count();
    @endphp

    <div class="flex h-screen overflow-hidden bg-slate-50">
        
        <!-- SIDEBAR -->
        <div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 z-40 hidden lg:hidden modal-backdrop-blur transition-opacity duration-300"></div>
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto flex flex-col h-full border-r border-slate-200">
            @include('components.uji-sidebar')
        </aside>

        <div class="flex-1 flex flex-col h-screen overflow-hidden relative w-full text-left">
            
            <!-- HEADER -->
            <div class="hidden lg:block text-left">
                @include('components.uji-header', [
                    'title' => 'Status Mutu & Survailen',
                    'subtitle' => 'Tindak lanjut hasil audit dan pengawasan teknis'
                ])
            </div>

            <!-- MOBILE HEADER -->
            <div class="lg:hidden bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between sticky top-0 z-30 shadow-sm text-left">
                <button onclick="toggleSidebar()" class="p-2 text-slate-600 rounded-lg transition-colors"><i class="fas fa-bars text-xl"></i></button>
                <span class="font-bold text-slate-800 text-sm tracking-tight uppercase">SI-MUTU UJI</span>
                <div class="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center text-teal-600 text-xs font-bold border border-teal-200 uppercase">
                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                </div>
            </div>

            <!-- MAIN CONTENT -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-8 space-y-6">
                
                <!-- SUCCESS POP-UP -->
                @if (session('success'))
                <div id="successModal" class="fixed inset-0 z-[100] flex items-center justify-center modal-backdrop-blur transition-opacity duration-300 text-center">
                    <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-sm w-full mx-4 text-center animate-pop-in relative border border-slate-100 overflow-hidden">
                        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-teal-500 to-indigo-600"></div>
                        <div class="w-20 h-20 bg-teal-50 rounded-full flex items-center justify-center mx-auto mb-4 shadow-inner">
                            <i class="fas fa-check text-4xl text-teal-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 mb-2 tracking-tight">Berhasil Terkirim!</h3>
                        <p class="text-slate-500 mb-6 text-sm font-medium leading-relaxed">{{ session('success') }}</p>
                        <div class="relative w-full bg-slate-100 h-1 rounded-full mb-6 overflow-hidden">
                            <div id="progressBar" class="h-full bg-teal-500 rounded-full transition-all ease-linear" style="width: 100%"></div>
                        </div>
                        <button onclick="closeNotification('successModal')" class="w-full bg-slate-900 text-white font-bold py-3 rounded-xl hover:bg-black transition-all active:scale-95 shadow-lg uppercase text-xs tracking-widest">Tutup Sekarang</button>
                    </div>
                </div>
                @endif

                <!-- STATISTIK SECTION (SINKRON DENGAN GAYA VERIFIKASI) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                    <!-- Perlu Respon -->
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 group text-left relative overflow-hidden">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-rose-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
                        <div class="flex justify-between items-start relative z-10 text-left">
                            <div>
                                <p class="text-slate-500 text-[10px] font-bold uppercase tracking-widest group-hover:text-rose-600">Perlu Respon</p>
                                <h3 class="text-3xl font-extrabold text-slate-800 mt-2">{{ $needAction }}</h3>
                            </div>
                            <div class="p-3 bg-rose-50 rounded-xl text-rose-500">
                                <i class="fas fa-exclamation-circle text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-[10px] text-slate-400 font-bold relative z-10">
                            <span class="text-rose-600 font-black bg-rose-50 px-1.5 py-0.5 rounded mr-2 uppercase">Action</span> Audit Belum Ditanggapi
                        </div>
                    </div>

                    <!-- Menunggu Review -->
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 group text-left relative overflow-hidden">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
                        <div class="flex justify-between items-start relative z-10 text-left">
                            <div>
                                <p class="text-slate-500 text-[10px] font-bold uppercase tracking-widest group-hover:text-amber-600">Menunggu Admin</p>
                                <h3 class="text-3xl font-extrabold text-slate-800 mt-2">{{ $waitingReview }}</h3>
                            </div>
                            <div class="p-3 bg-amber-50 rounded-xl text-amber-500">
                                <i class="fas fa-history text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-[10px] text-slate-400 font-bold relative z-10">
                            <span class="text-amber-600 font-black bg-amber-50 px-1.5 py-0.5 rounded mr-2 uppercase">Proses</span> Verifikasi Tindak Lanjut
                        </div>
                    </div>

                    <!-- Selesai -->
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 group text-left relative overflow-hidden">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
                        <div class="flex justify-between items-start relative z-10 text-left">
                            <div>
                                <p class="text-slate-500 text-[10px] font-bold uppercase tracking-widest group-hover:text-emerald-600">Audit Selesai</p>
                                <h3 class="text-3xl font-extrabold text-slate-800 mt-2">{{ $completed }}</h3>
                            </div>
                            <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600">
                                <i class="fas fa-check-double text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-[10px] text-slate-400 font-bold relative z-10">
                            <span class="text-emerald-600 font-black bg-emerald-50 px-1.5 py-0.5 rounded mr-2 uppercase">Sukses</span> Dokumen Mutu Patuh
                        </div>
                    </div>
                </div>

                <!-- TABLE DATA -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden text-left">
                    <div class="px-6 py-5 border-b border-slate-100 bg-white flex justify-between items-center text-left">
                        <h3 class="font-bold text-slate-800 text-lg tracking-tight">Antrian Audit Survailen</h3>
                    </div>

                    <div class="overflow-x-auto no-scrollbar">
                        <table class="w-full text-sm text-left">
                            <thead class="text-[11px] text-slate-400 uppercase bg-slate-50/50 border-b font-black tracking-widest text-left">
                                <tr>
                                    <th class="px-6 py-4">Tgl Masuk</th>
                                    <th class="px-6 py-4">Perihal / Topik</th>
                                    <th class="px-6 py-4 text-center">Instruksi</th>
                                    <th class="px-6 py-4 text-center">Laporan Anda</th>
                                    <th class="px-6 py-4 text-center w-20">Jejak</th>
                                    <th class="px-6 py-4 text-center">Status</th>
                                    <th class="px-6 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-left">
                                @forelse($survailens as $item)
                                <tr class="table-row-hover transition-colors group cursor-pointer text-left" onclick='openHistoryModal(@json($item->files ?? []), "{{ $item->status }}", "{{ $item->title }}")'>
                                    <td class="px-6 py-5 whitespace-nowrap text-slate-600 font-bold text-left">
                                        {{ $item->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-5 text-left">
                                        <div class="font-bold text-slate-800 group-hover:text-teal-600 transition-colors line-clamp-1 text-sm">{{ $item->title }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono tracking-tighter mt-1 uppercase font-bold">Ref: #{{ substr($item->id, 0, 8) }}</div>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        @php $adminFile = $item->admin_file ?: ($item->files->where('version', 0)->first()->admin_file ?? null); @endphp
                                        @if($adminFile)
                                            <a href="{{ asset('storage/' . $adminFile) }}" target="_blank" onclick="event.stopPropagation()" class="inline-flex items-center gap-2 bg-indigo-50 text-indigo-700 px-3 py-1 rounded-lg hover:bg-indigo-100 transition-all font-bold text-[10px] uppercase border border-indigo-100">
                                                Surat
                                            </a>
                                        @else
                                            <span class="text-xs text-slate-300 italic">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        @if($item->file_path && $item->file_path !== '-')
                                            <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank" onclick="event.stopPropagation()" class="text-teal-600 font-bold text-[10px] uppercase bg-teal-50 px-3 py-1 rounded-lg border border-teal-100">
                                                Bukti
                                            </a>
                                        @else
                                            <span class="text-[10px] text-rose-500 font-black uppercase tracking-tighter">BELUM</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center mx-auto text-slate-300 group-hover:bg-teal-50 group-hover:text-teal-600 transition-all">
                                            <i class="fas fa-history"></i>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        @php
                                            $stData = match($item->status) {
                                                'approved' => ['Selesai', 'bg-emerald-100 text-emerald-700 border-emerald-200'],
                                                'rejected' => ['Revisi', 'bg-rose-100 text-rose-700 border-rose-200'],
                                                default => ($item->file_path && $item->file_path !== '-') 
                                                    ? ['Review', 'bg-amber-100 text-amber-700 border-amber-200 animate-pulse'] 
                                                    : ['Menunggu', 'bg-slate-100 text-slate-500 border-slate-200']
                                            };
                                        @endphp
                                        <span class="{{ $stData[1] }} px-3 py-0.5 rounded-full text-[10px] font-black border uppercase tracking-tighter">{{ $stData[0] }}</span>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        @if(!$item->file_path || $item->file_path == '-' || $item->status == 'rejected')
                                            <button onclick="event.stopPropagation(); openUploadModal({{ $item->id }}, '{{ $item->title }}')" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-1.5 rounded-lg text-[10px] font-black shadow-md transition-all active:scale-95 uppercase tracking-wide">
                                                Respon
                                            </button>
                                        @else
                                            <i class="fas fa-check-circle text-emerald-500 text-lg"></i>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="py-32 text-center opacity-30 uppercase font-black text-xs tracking-widest text-left">Tidak ada data audit masuk</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="text-center py-6 opacity-30 text-xs font-bold uppercase tracking-[0.2em]">SI-MUTU DKKN | BAPETEN</div>
            </main>
        </div>
    </div>

    <!-- MODAL 1: UPLOAD RESPON -->
    <div id="uploadModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div class="fixed inset-0 modal-backdrop-blur transition-opacity duration-300" onclick="closeUploadModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md border border-slate-100 animate-pop-in overflow-hidden text-left">
            <div class="bg-gradient-to-r from-teal-600 to-indigo-700 px-6 py-5 flex justify-between items-center text-white">
                <div>
                    <h3 class="text-lg font-bold">Kirim Bukti Survailen</h3>
                    <p class="text-[10px] text-teal-100 font-bold uppercase tracking-widest mt-0.5">Unggah Laporan Tindak Lanjut</p>
                </div>
                <button onclick="closeUploadModal()" class="w-8 h-8 rounded-full hover:bg-white/10 flex items-center justify-center transition-colors"><i class="fas fa-times text-lg"></i></button>
            </div>
            <form id="responseForm" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                @csrf 
                <input type="hidden" name="_method" value="PUT">
                <div class="space-y-1.5 text-left">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Topik Audit</label>
                    <input type="text" id="docTitle" class="w-full bg-slate-50 border-slate-200 rounded-xl p-3.5 text-sm font-bold text-slate-700 shadow-inner outline-none" readonly>
                </div>
                <div class="bg-teal-50/50 p-4 rounded-xl border border-teal-100 relative overflow-hidden text-left">
                    <label class="block text-[10px] font-black text-teal-600 uppercase flex justify-between relative z-10">
                        <span>Upload Laporan (PDF)</span>
                        <span class="bg-teal-600 text-white px-1.5 py-0.5 rounded text-[8px]">Wajib</span>
                    </label>
                    <input name="file_upload" type="file" class="w-full text-sm mt-3 file:mr-4 file:py-1.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-teal-600 file:text-white cursor-pointer relative z-10" accept=".pdf" required>
                </div>
                <div class="space-y-1.5 text-left">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Catatan Tambahan</label>
                    <textarea name="user_note" rows="3" class="w-full border-slate-200 rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-teal-500/20 outline-none transition-all" placeholder="Jelaskan secara singkat tindak lanjut Anda..."></textarea>
                </div>
                <div class="flex gap-4 pt-3">
                    <button type="button" onclick="closeUploadModal()" class="flex-1 py-3 rounded-xl border-2 border-slate-100 font-bold text-xs uppercase text-slate-500 hover:bg-slate-50 transition-all">Batal</button>
                    <button type="submit" class="flex-1 py-3 rounded-xl bg-teal-600 text-white font-black text-xs uppercase shadow-lg shadow-teal-200 hover:bg-teal-700 transition-all active:scale-95">Kirim Laporan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: TIMELINE HISTORY (SINKRON DENGAN VERIFIKASI - DARK HEADER) -->
    <div id="historyModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div class="fixed inset-0 modal-backdrop-blur transition-opacity duration-300" onclick="closeHistoryModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg border border-slate-100 animate-pop-in overflow-hidden text-left flex flex-col">
            <!-- Header Gelap Sesuai Screenshot -->
            <div class="bg-slate-900 px-6 py-6 flex justify-between items-center text-white relative">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-teal-500 to-indigo-500"></div>
                <div class="text-left flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-500/20 rounded-xl flex items-center justify-center text-indigo-400 border border-indigo-500/30">
                        <i class="fas fa-history text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black tracking-tight leading-none uppercase">Jejak Audit</h3>
                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-2" id="historyTitle">Memuat riwayat...</p>
                    </div>
                </div>
                <button onclick="closeHistoryModal()" class="w-10 h-10 rounded-full hover:bg-white/10 flex items-center justify-center transition-colors text-slate-400 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="max-h-[65vh] overflow-y-auto bg-slate-50 no-scrollbar p-6">
                <div id="timelineContainer" class="relative"></div>
            </div>
            
            <div class="bg-white px-6 py-4 flex justify-end border-t border-slate-100 shadow-inner">
                <button onclick="closeHistoryModal()" class="px-10 py-3 bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-md hover:bg-black transition-all active:scale-95">Tutup Detail</button>
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

        // SIDEBAR TOGGLE
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        function openUploadModal(id, title) { 
            const form = document.getElementById('responseForm');
            form.action = "{{ url('/submission/update') }}/" + id;
            document.getElementById('docTitle').value = title;
            toggleModal('uploadModal', true); 
        }
        
        function closeUploadModal() { toggleModal('uploadModal', false); }
        function closeHistoryModal() { toggleModal('historyModal', false); }

        function closeNotification(modalId) {
            const modal = document.getElementById(modalId);
            if(modal) {
                modal.style.opacity = '0';
                setTimeout(() => { toggleModal(modalId, false); }, 300);
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

        // TIMELINE LOGIC (Alur: Admin -> User -> Evaluasi)
        function openHistoryModal(files, currentStatus, docTitle) {
            const container = document.getElementById('timelineContainer');
            document.getElementById('historyTitle').innerText = docTitle;
            container.innerHTML = ''; 
            
            if(!files || files.length === 0) {
                container.innerHTML = `<div class="py-16 text-center text-slate-300 font-bold uppercase tracking-widest opacity-40 text-xs text-left">Belum ada riwayat tercatat</div>`;
            } else {
                files.sort((a, b) => a.version - b.version);
                
                files.forEach((file, index) => {
                    const d = new Date(file.created_at);
                    const dateStr = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
                    let isLatest = (index === files.length - 1), isStart = (file.version == 0);
                    
                    if (isStart) {
                        // Card 1: Inisiasi Admin
                        container.innerHTML += `
                            <div class="relative flex gap-5 pb-10 animate-slide-in text-left">
                                <div class="absolute top-0 left-4 -bottom-10 w-0.5 bg-slate-200 text-left"></div>
                                <div class="relative z-10 flex-shrink-0 w-8 h-8 rounded-full bg-amber-500 text-white flex items-center justify-center text-[10px] font-black ring-4 ring-amber-50 shadow-sm"><i class="fas fa-flag text-[10px] text-left"></i></div>
                                <div class="flex-1 bg-white rounded-2xl p-5 border border-slate-200 shadow-sm text-left">
                                    <div class="flex justify-between items-start mb-2 text-left">
                                        <span class="text-[11px] font-black text-slate-800 uppercase tracking-tight text-left">Admin: Inisiasi Audit</span>
                                        <span class="text-[9px] text-slate-400 font-mono bg-slate-50 px-2 py-0.5 rounded-full border text-left">${dateStr}</span>
                                    </div>
                                    <div class="mt-3 bg-teal-50 rounded-xl p-3.5 border border-teal-100 flex items-center gap-4 transition-colors hover:bg-teal-100/50 text-left">
                                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-red-500 shadow-sm shrink-0 text-left"><i class="fas fa-file-pdf text-xl text-left"></i></div>
                                        <div class="flex-1 min-w-0 text-left">
                                            <p class="text-[9px] font-black text-teal-600 uppercase tracking-wide leading-none mb-1 text-left">Surat Instruksi</p>
                                            <a href="/storage/${file.admin_file}" target="_blank" class="text-xs font-bold text-slate-800 hover:text-teal-600 underline truncate block text-left">Lihat Dokumen Terlampir</a>
                                        </div>
                                    </div>
                                    ${file.admin_note ? `<div class="mt-4 p-4 bg-slate-50 border border-slate-100 rounded-xl text-[11px] text-slate-600 italic leading-relaxed text-left">"${file.admin_note}"</div>` : ''}
                                </div>
                            </div>`;
                    } else {
                        // Card 2: Tanggapan Anda
                        if (file.file_path && file.file_path !== '-') {
                            container.innerHTML += `
                                <div class="relative flex gap-5 pb-10 animate-slide-in text-left">
                                    <div class="absolute top-0 left-4 -bottom-10 w-0.5 bg-slate-200 text-left"></div>
                                    <div class="relative z-10 flex-shrink-0 w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-[10px] font-black ring-4 ring-blue-50 shadow-sm text-left">v${file.version}</div>
                                    <div class="flex-1 bg-white rounded-2xl p-5 border border-slate-200 shadow-sm text-left hover:border-blue-300 transition-colors text-left">
                                        <div class="flex justify-between items-start mb-2 text-left">
                                            <span class="text-[11px] font-black text-blue-700 uppercase tracking-tight text-left">Anda: Laporan Bukti</span>
                                            <span class="text-[8px] text-slate-400 font-mono bg-slate-50 px-2 py-0.5 rounded-full border border-slate-100 text-left text-left">${dateStr}</span>
                                        </div>
                                        <div class="mt-3 flex items-center gap-4 bg-slate-50 p-3 rounded-xl border border-slate-200 cursor-pointer group/file text-left text-left" onclick="window.open('/storage/${file.file_path}', '_blank')">
                                            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 shrink-0 group-hover/file:bg-blue-100 transition-colors text-left"><i class="fas fa-file-alt text-xl text-left"></i></div>
                                            <div class="flex-1 min-w-0 text-left text-left">
                                                <p class="text-[9px] font-black text-blue-600 uppercase tracking-wide leading-none mb-1 text-left">Dokumen Terkirim</p>
                                                <p class="text-[11px] font-bold text-slate-800 truncate text-left text-left">Lihat Lampiran Laporan</p>
                                            </div>
                                        </div>
                                        ${file.user_note ? `<div class="mt-3 p-4 bg-white border border-slate-100 rounded-xl text-[11px] text-slate-500 italic border-l-4 border-l-blue-500 leading-relaxed text-left">"${file.user_note}"</div>` : ''}
                                    </div>
                                </div>`;
                        }

                        // Card 3: Evaluasi Admin
                        if (file.admin_note || file.admin_file) {
                            let badgeHTML = '';
                            if (isLatest) {
                                if (currentStatus === 'approved') badgeHTML = `<span class="bg-emerald-500 text-white px-3 py-1 rounded-full text-[8px] font-black shadow-sm uppercase tracking-wider text-left">Disetujui</span>`;
                                else if (currentStatus === 'rejected') badgeHTML = `<span class="bg-rose-500 text-white px-3 py-1 rounded-full text-[8px] font-black shadow-sm uppercase tracking-wider text-left text-left">Minta Revisi</span>`;
                            } else {
                                badgeHTML = `<span class="bg-slate-100 text-slate-500 px-3 py-1 rounded-full text-[8px] font-black uppercase border border-slate-200 text-left">Arsip</span>`;
                            }

                            container.innerHTML += `
                                <div class="relative flex gap-5 pb-8 last:pb-0 animate-slide-in text-left">
                                    <div class="absolute top-0 left-4 -bottom-8 w-0.5 bg-slate-200 last:hidden text-left"></div>
                                    <div class="relative z-10 flex-shrink-0 w-8 h-8 rounded-full bg-slate-800 text-white flex items-center justify-center text-[10px] font-black ring-4 ring-slate-100 shadow-sm text-left"><i class="fas fa-check-circle text-[10px] text-left"></i></div>
                                    <div class="flex-1 bg-slate-50 rounded-2xl p-5 border border-slate-200 shadow-sm text-left transition-all hover:shadow-md text-left">
                                        <div class="flex justify-between items-center mb-4 text-left">
                                            <span class="text-[11px] font-black text-slate-700 uppercase tracking-tight text-left text-left">Admin: Hasil Evaluasi</span>
                                            ${badgeHTML}
                                        </div>
                                        <div class="bg-white border border-slate-200 rounded-xl p-4 text-[11px] text-slate-700 leading-relaxed italic border-l-4 border-l-teal-500 shadow-sm text-left text-left text-left">
                                            "${file.admin_note || 'Pemeriksaan laporan verifikasi selesai.'}"
                                        </div>
                                        ${file.admin_file ? `<a href="/storage/${file.admin_file}" target="_blank" class="mt-4 flex items-center gap-3 text-[11px] font-extrabold text-teal-600 bg-white p-3 rounded-xl border border-teal-100 hover:bg-teal-50 transition-all shadow-sm text-left text-left"><i class="fas fa-paperclip text-left text-left"></i> Lihat Dokumen Balasan</a>` : ''}
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