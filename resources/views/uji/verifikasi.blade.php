<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Lembaga Uji | SI-MUTU</title>
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
                            '0%': { transform: 'translateX(-20px)', opacity: '0' },
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
        
        .modal-scroll::-webkit-scrollbar { width: 4px; }
        .modal-scroll::-webkit-scrollbar-track { background: transparent; }
        .modal-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        
        .modal-backdrop-blur { backdrop-filter: blur(4px); background-color: rgba(15, 23, 42, 0.6); }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-indigo-100 selection:text-indigo-700">

    <!-- 1. DATA PREPARATION (USER SIDE) -->
    @php
        use App\Models\Submission;
        use Illuminate\Support\Facades\Auth;
        
        if (!isset($verifikasis)) {
            $verifikasis = Submission::where('user_id', Auth::id())
                               ->where('category', 'uji')
                               ->where('type', 'like', '%Verifikasi%')
                               ->orderBy('created_at', 'desc')
                               ->with('files')
                               ->get();
        }

        // Logic Statistik
        $needAction = $verifikasis->filter(fn($item) => empty($item->file_path) || $item->file_path === '-' || $item->status == 'rejected')->count();
        $waitingReview = $verifikasis->where('status', 'pending')->where('file_path', '!=', '-')->count();
        $completed = $verifikasis->where('status', 'approved')->count();
    @endphp

    <div class="flex h-screen overflow-hidden bg-slate-50">
        
        <!-- SIDEBAR -->
        <div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 z-40 hidden lg:hidden modal-backdrop-blur transition-opacity duration-300"></div>
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto flex flex-col h-full border-r border-slate-200 text-left">
            @include('components.uji-sidebar')
        </aside>

        <div class="flex-1 flex flex-col h-screen overflow-hidden relative w-full text-left">
            
            <!-- HEADER -->
            <div class="hidden lg:block text-left">
                @include('components.uji-header', [
                    'title' => 'Verifikasi Penunjukan',
                    'subtitle' => 'Kelola dokumen persyaratan dan verifikasi teknis'
                ])
            </div>

            <!-- MOBILE HEADER -->
            <div class="lg:hidden bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between sticky top-0 z-30 shadow-sm text-left">
                <button onclick="toggleSidebar()" class="p-2 text-slate-600 rounded-lg transition-colors"><i class="fas fa-bars text-xl"></i></button>
                <span class="font-bold text-slate-800 text-sm tracking-tight uppercase">SI-MUTU UJI</span>
                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 text-xs font-bold border border-indigo-200 uppercase">
                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                </div>
            </div>

            <!-- MAIN CONTENT -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-8 space-y-6">
                
                <!-- SUCCESS POP-UP -->
                @if (session('success'))
                <div id="successModal" class="fixed inset-0 z-[100] flex items-center justify-center modal-backdrop-blur transition-opacity duration-300 text-center">
                    <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-sm w-full mx-4 text-center animate-pop-in relative border border-slate-100 overflow-hidden">
                        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-indigo-500 to-blue-600"></div>
                        <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow-inner">
                            <i class="fas fa-check text-3xl text-indigo-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 mb-2">Berhasil!</h3>
                        <p class="text-slate-500 mb-6 text-sm font-medium leading-relaxed">{{ session('success') }}</p>
                        <div class="relative w-full bg-slate-100 h-1 rounded-full mb-6 overflow-hidden text-left">
                            <div id="progressBar" class="h-full bg-indigo-500 rounded-full transition-all ease-linear" style="width: 100%"></div>
                        </div>
                        <button onclick="closeNotification('successModal')" class="w-full bg-slate-900 text-white font-bold py-3 rounded-xl hover:bg-black transition-all active:scale-95 shadow-lg uppercase text-xs tracking-widest">Tutup Sekarang</button>
                    </div>
                </div>
                @endif

                <!-- STATISTIK SECTION (GAYA SURVAILEN: PROFESIONAL & CLEAN) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                    <!-- Perlu Respon -->
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 group text-left">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider group-hover:text-rose-600 transition-colors">Perlu Respon</p>
                                <h3 class="text-3xl font-extrabold text-slate-800 mt-2">{{ $needAction }}</h3>
                            </div>
                            <div class="p-3 bg-rose-50 rounded-xl text-rose-500 group-hover:scale-110 transition-transform">
                                <i class="fas fa-exclamation-circle text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-[10px] text-slate-400 font-bold">
                            <span class="text-rose-600 font-black bg-rose-50 px-1.5 py-0.5 rounded mr-2 uppercase">Action</span> Instruksi atau Revisi Baru
                        </div>
                    </div>

                    <!-- Menunggu -->
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 group text-left">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider group-hover:text-amber-600 transition-colors">Menunggu Admin</p>
                                <h3 class="text-3xl font-extrabold text-slate-800 mt-2">{{ $waitingReview }}</h3>
                            </div>
                            <div class="p-3 bg-amber-50 rounded-xl text-amber-500 group-hover:scale-110 transition-transform">
                                <i class="fas fa-clock text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-[10px] text-slate-400 font-bold">
                            <span class="text-amber-600 font-black bg-amber-50 px-1.5 py-0.5 rounded mr-2 uppercase">Proses</span> Sedang Diverifikasi
                        </div>
                    </div>

                    <!-- Selesai -->
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 group text-left">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider group-hover:text-emerald-600 transition-colors">Selesai</p>
                                <h3 class="text-3xl font-extrabold text-slate-800 mt-2">{{ $completed }}</h3>
                            </div>
                            <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600 group-hover:scale-110 transition-transform">
                                <i class="fas fa-check-double text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-[10px] text-slate-400 font-bold">
                            <span class="text-emerald-600 font-black bg-emerald-50 px-1.5 py-0.5 rounded mr-2 uppercase">Sukses</span> Penunjukan Valid
                        </div>
                    </div>
                </div>

                <!-- TABLE SECTION -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden text-left">
                    <div class="px-6 py-5 border-b border-slate-100 bg-white flex justify-between items-center text-left">
                        <h3 class="font-bold text-slate-800 text-lg tracking-tight">Daftar Verifikasi Aktif</h3>
                    </div>

                    <div class="overflow-x-auto no-scrollbar">
                        <table class="w-full text-sm text-left text-slate-600 min-w-[900px]">
                            <thead class="text-[11px] text-slate-400 uppercase bg-slate-50/50 border-b font-black tracking-widest">
                                <tr>
                                    <th class="px-6 py-4 text-left">Tgl Masuk</th>
                                    <th class="px-6 py-4 text-left">Perihal / Judul</th>
                                    <th class="px-6 py-4 text-left">Instruksi Admin</th>
                                    <th class="px-6 py-4 text-left">Respon Anda</th>
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
                                        <div class="font-bold text-slate-800 group-hover:text-indigo-600 transition-colors line-clamp-1 text-sm text-left">{{ $item->title }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono tracking-tighter mt-1 text-left">ID: #{{ substr($item->id, 0, 8) }}</div>
                                    </td>
                                    <td class="px-6 py-5 text-left">
                                        @php $adminFile = $item->admin_file ?: ($item->files->where('version', 0)->first()->admin_file ?? null); @endphp
                                        @if($adminFile)
                                            <a href="{{ asset('storage/' . $adminFile) }}" target="_blank" onclick="event.stopPropagation()" class="inline-flex items-center gap-2 bg-white hover:bg-indigo-50 text-slate-700 border border-slate-200 hover:border-indigo-300 px-3 py-1.5 rounded-xl transition-all shadow-sm text-left">
                                                <i class="fas fa-file-pdf text-red-500"></i> <span class="text-[11px] font-bold tracking-tight">Unduh Surat</span>
                                            </a>
                                        @else
                                            <span class="text-xs text-slate-300 italic font-medium text-left">Tanpa Lampiran</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5 text-left">
                                        @if($item->file_path && $item->file_path !== '-')
                                            <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank" onclick="event.stopPropagation()" class="text-indigo-600 font-bold text-[11px] hover:underline flex items-center gap-1 text-left">
                                                <i class="fas fa-paperclip"></i> Lihat Bukti
                                            </a>
                                        @else
                                            <span class="text-[10px] text-rose-500 font-black uppercase tracking-tighter bg-rose-50 px-2 py-0.5 rounded border border-rose-100 text-left">Belum Respon</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center mx-auto text-slate-300 group-hover:bg-indigo-50 group-hover:text-indigo-500 transition-all text-center">
                                            <i class="fas fa-history text-center"></i>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        @php
                                            // FIX: Variabel $statusData digunakan secara konsisten
                                            $statusData = match($item->status) {
                                                'approved' => ['Selesai', 'bg-emerald-100 text-emerald-700 border-emerald-200'],
                                                'rejected' => ['Revisi', 'bg-rose-100 text-rose-700 border-rose-200'],
                                                default => ($item->file_path && $item->file_path !== '-') 
                                                    ? ['Review', 'bg-indigo-100 text-indigo-700 border-indigo-200 animate-pulse'] 
                                                    : ['Menunggu', 'bg-slate-100 text-slate-500 border-slate-200']
                                            };
                                        @endphp
                                        <span class="{{ $statusData[1] }} px-3 py-1 rounded-full text-[10px] font-black border uppercase tracking-tighter text-center">{{ $statusData[0] }}</span>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        @if(!$item->file_path || $item->file_path == '-' || $item->status == 'rejected')
                                            <button onclick="event.stopPropagation(); openUploadModal('{{ $item->id }}', '{{ $item->title }}')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-[10px] font-black shadow-md transition-all active:scale-95 uppercase tracking-wide text-center">
                                                {{ $item->status == 'rejected' ? 'Kirim Revisi' : 'Tindak Lanjut' }}
                                            </button>
                                        @else
                                            <i class="fas fa-check-circle text-emerald-500 text-xl text-center"></i>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="py-32 text-center opacity-30 uppercase font-black text-xs tracking-widest text-left">Tidak ada verifikasi aktif</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- MODAL 1: UPLOAD RESPON -->
    <div id="uploadModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 text-left">
        <div class="fixed inset-0 modal-backdrop-blur transition-opacity duration-300" onclick="closeUploadModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md border border-slate-100 animate-pop-in overflow-hidden text-left">
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 px-6 py-5 flex justify-between items-center text-white text-left">
                <div>
                    <h3 class="text-lg font-bold text-left">Kirim Bukti Verifikasi</h3>
                    <p class="text-[10px] text-indigo-200 font-bold uppercase tracking-widest mt-0.5 text-left text-left">Respon Tindak Lanjut</p>
                </div>
                <button onclick="closeUploadModal()" class="w-8 h-8 rounded-full hover:bg-white/10 flex items-center justify-center transition-colors text-left"><i class="fas fa-times text-lg"></i></button>
            </div>
            <form id="responseForm" method="POST" enctype="multipart/form-data" class="p-6 space-y-5 text-left">
                @csrf 
                <input type="hidden" name="_method" value="PUT">
                <div class="space-y-1.5 text-left text-left">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest text-left">Topik Dokumen</label>
                    <input type="text" id="docTitle" class="w-full bg-slate-50 border-slate-200 rounded-xl p-3.5 text-sm font-bold text-slate-700 shadow-inner outline-none text-left" readonly>
                </div>
                <div class="bg-indigo-50/50 p-4 rounded-xl border border-indigo-100 relative overflow-hidden text-left">
                    <div class="absolute -right-6 -bottom-6 w-20 h-20 bg-indigo-100 rounded-full opacity-30 text-left"></div>
                    <label class="block text-[10px] font-black text-indigo-600 uppercase flex justify-between relative z-10 text-left">
                        <span>Upload Laporan (PDF)</span>
                        <span class="bg-indigo-600 text-white px-1.5 py-0.5 rounded text-[8px] text-left">Wajib</span>
                    </label>
                    <input name="file_upload" type="file" class="w-full text-sm mt-3 file:mr-4 file:py-1.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white cursor-pointer relative z-10 text-left" accept=".pdf" required>
                </div>
                <div class="space-y-1.5 text-left text-left">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest text-left text-left text-left">Catatan Tambahan</label>
                    <textarea name="user_note" rows="3" class="w-full border-slate-200 rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all text-left" placeholder="Jelaskan secara singkat tindak lanjut Anda..."></textarea>
                </div>
                <div class="flex gap-4 pt-3 text-left">
                    <button type="button" onclick="closeUploadModal()" class="flex-1 py-3 rounded-xl border-2 border-slate-100 font-bold text-xs uppercase text-slate-500 hover:bg-slate-50 transition-all text-left">Batal</button>
                    <button type="submit" class="flex-1 py-3 rounded-xl bg-indigo-600 text-white font-black text-xs uppercase shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-all active:scale-95 text-left">Kirim Laporan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: TIMELINE HISTORY (RAMPING & LOGIS) -->
    <div id="historyModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 text-left">
        <div class="fixed inset-0 modal-backdrop-blur transition-opacity duration-300" onclick="closeHistoryModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg border border-slate-100 animate-pop-in overflow-hidden text-left flex flex-col text-left">
            <div class="bg-gradient-to-br from-slate-800 to-slate-900 px-6 py-5 flex justify-between items-center text-white text-left text-left text-left">
                <div class="text-left text-left">
                    <h3 class="text-xl font-black flex items-center gap-3 text-left">
                        <div class="bg-indigo-500 p-2 rounded-xl shadow-lg text-left"><i class="fas fa-history text-left"></i></div>
                        Jejak Verifikasi
                    </h3>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-1.5 text-left" id="historyTitle">Memuat riwayat...</p>
                </div>
                <button onclick="closeHistoryModal()" class="w-10 h-10 rounded-full hover:bg-white/10 flex items-center justify-center transition-colors text-left"><i class="fas fa-times text-lg text-left"></i></button>
            </div>
            <div class="max-h-[65vh] overflow-y-auto bg-slate-50 no-scrollbar p-6 text-left">
                <div id="timelineContainer" class="relative text-left"></div>
            </div>
            <div class="bg-white px-6 py-4 flex justify-end border-t border-slate-100 shadow-inner text-left">
                <button onclick="closeHistoryModal()" class="px-8 py-3 bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-md hover:bg-black transition-all active:scale-95 text-left">Tutup Detail</button>
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

        // NOTIFICATION LOGIC
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

        // TIMELINE LOGIC (Alur: Admin -> User -> Evaluasi)
        function openHistoryModal(files, currentStatus, docTitle) {
            const container = document.getElementById('timelineContainer');
            document.getElementById('historyTitle').innerText = docTitle;
            container.innerHTML = ''; 
            
            if(!files || files.length === 0) {
                container.innerHTML = `<div class="py-16 text-center text-slate-300 font-bold uppercase tracking-widest opacity-40 text-xs text-left text-left">Belum ada riwayat tercatat</div>`;
            } else {
                files.sort((a, b) => a.version - b.version);
                
                files.forEach((file, index) => {
                    const d = new Date(file.created_at);
                    const dateStr = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
                    let isLatest = (index === files.length - 1), isStart = (file.version == 0);
                    
                    if (isStart) {
                        // Card 1: Inisiasi Admin
                        container.innerHTML += `
                            <div class="relative flex gap-5 pb-8 animate-slide-in text-left text-left text-left text-left text-left">
                                <div class="absolute top-0 left-4 -bottom-8 w-0.5 bg-slate-200 text-left"></div>
                                <div class="relative z-10 flex-shrink-0 w-8 h-8 rounded-full bg-amber-500 text-white flex items-center justify-center text-[10px] font-black ring-4 ring-amber-50 shadow-sm text-left"><i class="fas fa-flag text-[10px] text-left"></i></div>
                                <div class="flex-1 bg-white rounded-2xl p-4 border border-slate-200 shadow-sm text-left">
                                    <div class="flex justify-between items-start mb-2 text-left">
                                        <span class="text-[11px] font-black text-slate-800 uppercase tracking-tight text-left">Admin: Inisiasi Verifikasi</span>
                                        <span class="text-[9px] text-slate-400 font-mono bg-slate-50 px-2 py-0.5 rounded-full border text-left">${dateStr}</span>
                                    </div>
                                    <div class="mt-3 bg-indigo-50 rounded-xl p-3 border border-indigo-100 flex items-center gap-4 transition-colors hover:bg-indigo-100/50 text-left text-left">
                                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-red-500 shadow-sm shrink-0 text-left"><i class="fas fa-file-pdf text-xl text-left"></i></div>
                                        <div class="flex-1 min-w-0 text-left text-left text-left">
                                            <p class="text-[9px] font-black text-indigo-600 uppercase tracking-wide leading-none mb-1 text-left">Surat Instruksi</p>
                                            <a href="/storage/${file.admin_file}" target="_blank" class="text-xs font-bold text-slate-800 hover:text-indigo-600 underline truncate block text-left">Lihat Dokumen Terlampir</a>
                                        </div>
                                    </div>
                                    ${file.admin_note ? `<div class="mt-3 p-3 bg-slate-50/50 border border-slate-100 rounded-xl text-xs text-slate-500 italic leading-relaxed text-left text-left">"${file.admin_note}"</div>` : ''}
                                </div>
                            </div>`;
                    } else {
                        // Card 2: Tanggapan Anda
                        if (file.file_path && file.file_path !== '-') {
                            container.innerHTML += `
                                <div class="relative flex gap-5 pb-8 animate-slide-in text-left text-left text-left">
                                    <div class="absolute top-0 left-4 -bottom-8 w-0.5 bg-slate-200 text-left"></div>
                                    <div class="relative z-10 flex-shrink-0 w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-[10px] font-black ring-4 ring-blue-50 shadow-sm text-left">v${file.version}</div>
                                    <div class="flex-1 bg-white rounded-2xl p-4 border border-slate-200 shadow-sm text-left hover:border-blue-300 transition-colors text-left">
                                        <div class="flex justify-between items-start mb-2 text-left">
                                            <span class="text-[11px] font-black text-blue-700 uppercase tracking-tight text-left">Anda: Laporan Bukti</span>
                                            <span class="text-[8px] text-slate-400 font-mono bg-slate-50 px-2 py-0.5 rounded-full border border-slate-100 text-left">${dateStr}</span>
                                        </div>
                                        <div class="mt-3 flex items-center gap-4 bg-slate-50 p-3 rounded-xl border border-slate-200 cursor-pointer group/file text-left text-left text-left" onclick="window.open('/storage/${file.file_path}', '_blank')">
                                            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 shrink-0 group-hover/file:bg-blue-100 transition-colors text-left text-left"><i class="fas fa-file-alt text-xl text-left"></i></div>
                                            <div class="flex-1 min-w-0 text-left text-left text-left text-left text-left">
                                                <p class="text-[9px] font-black text-blue-600 uppercase tracking-wide leading-none mb-1 text-left">Dokumen Terkirim</p>
                                                <p class="text-xs font-bold text-slate-800 truncate text-left text-left text-left text-left">Lihat Lampiran Laporan</p>
                                            </div>
                                        </div>
                                        ${file.user_note ? `<div class="mt-3 p-3 bg-white border border-slate-100 rounded-xl text-xs text-slate-500 italic border-l-4 border-l-blue-500 leading-relaxed text-left text-left">"${file.user_note}"</div>` : ''}
                                    </div>
                                </div>`;
                        }

                        // Card 3: Evaluasi Admin
                        if (file.admin_note || file.admin_file) {
                            let badgeHTML = '';
                            if (isLatest) {
                                if (currentStatus === 'approved') badgeHTML = `<span class="bg-emerald-500 text-white px-3 py-1 rounded-full text-[8px] font-black shadow-sm uppercase tracking-wider text-left">Disetujui</span>`;
                                else if (currentStatus === 'rejected') badgeHTML = `<span class="bg-rose-500 text-white px-3 py-1 rounded-full text-[8px] font-black shadow-sm uppercase tracking-wider text-left">Minta Revisi</span>`;
                            } else {
                                badgeHTML = `<span class="bg-slate-100 text-slate-500 px-3 py-1 rounded-full text-[8px] font-black uppercase border border-slate-200 text-left">Arsip</span>`;
                            }

                            container.innerHTML += `
                                <div class="relative flex gap-5 pb-8 last:pb-0 animate-slide-in text-left text-left text-left text-left text-left">
                                    <div class="absolute top-0 left-4 -bottom-8 w-0.5 bg-slate-200 last:hidden text-left"></div>
                                    <div class="relative z-10 flex-shrink-0 w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center text-[10px] font-black ring-4 ring-slate-100 shadow-sm text-left"><i class="fas fa-check-circle text-[10px] text-left"></i></div>
                                    <div class="flex-1 bg-slate-50 rounded-2xl p-4 border border-slate-200 shadow-sm text-left transition-all hover:shadow-md text-left text-left">
                                        <div class="flex justify-between items-center mb-3 text-left">
                                            <span class="text-[11px] font-black text-slate-700 uppercase tracking-tight text-left text-left">Admin: Hasil Evaluasi</span>
                                            ${badgeHTML}
                                        </div>
                                        <div class="bg-white border border-slate-200 rounded-xl p-4 text-[11px] text-slate-700 leading-relaxed italic border-l-4 border-l-indigo-500 shadow-sm text-left text-left text-left text-left">
                                            "${file.admin_note || 'Pemeriksaan laporan verifikasi selesai.'}"
                                        </div>
                                        ${file.admin_file ? `<a href="/storage/${file.admin_file}" target="_blank" class="mt-3.5 flex items-center gap-3 text-[11px] font-extrabold text-blue-600 bg-white p-2.5 rounded-xl border border-blue-100 hover:bg-blue-50 transition-all shadow-sm text-left text-left text-left text-left text-left"><i class="fas fa-paperclip text-left text-left text-left"></i> Lihat Dokumen Balasan</a>` : ''}
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