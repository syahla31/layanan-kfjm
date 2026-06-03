<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Verifikasi | SI-MUTU Admin</title>
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

        /* Timer Progress Bar Animation */
        @keyframes timerProgress {
            from { width: 100%; }
            to { width: 0%; }
        }
        .timer-bar { animation: timerProgress 5s linear forwards; }

        /* Modal Backdrop Blur */
        .modal-backdrop-blur {
            background-color: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(8px);
        }

        @keyframes popIn {
            0% { opacity: 0; transform: scale(0.9) translateY(20px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }
        .animate-pop-in { animation: popIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-purple-100 selection:text-purple-900">

@php
    use App\Models\Submission;
    use Illuminate\Support\Facades\Auth;
    
    // Ambil data verifikasi terbaru
    $verifikasis = Submission::where('user_id', Auth::id())
                        ->where('type', 'Verifikasi')
                        ->orderBy('created_at', 'desc')
                        ->get();

    $needAction = $verifikasis->filter(fn($i) => empty($i->user_note) && $i->status != 'approved')->count();
    $waitingAdmin = $verifikasis->filter(fn($i) => !empty($i->user_note) && $i->status != 'approved')->count();
    $completed = $verifikasis->where('status', 'approved')->count();
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
                'title' => 'Status Verifikasi',
                'subtitle' => 'Penerbitan dokumen hasil verifikasi dan akreditasi'
            ])
        </div>

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

        <!-- MAIN CONTENT -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-8 space-y-6">
            
            <!-- STATS CARDS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4 md:gap-6">
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 group text-left relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-rose-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-widest group-hover:text-rose-600">Perlu Respon</p>
                            <h3 class="text-3xl font-extrabold text-slate-800 mt-2">{{ $needAction }}</h3>
                        </div>
                        <div class="p-3 bg-rose-50 rounded-xl text-rose-500">
                            <i class="fas fa-exclamation-triangle text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-[10px] text-slate-400 font-bold relative z-10">
                        <span class="text-rose-600 font-black bg-rose-50 px-1.5 py-0.5 rounded mr-2 uppercase">Action</span> Instruksi Belum Dikonfirmasi
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 group text-left relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-widest group-hover:text-emerald-600">Selesai</p>
                            <h3 class="text-3xl font-extrabold text-slate-800 mt-2">{{ $completed }}</h3>
                        </div>
                        <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600">
                            <i class="fas fa-check-double text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-[10px] text-slate-400 font-bold relative z-10">
                        <span class="text-emerald-600 font-black bg-emerald-50 px-1.5 py-0.5 rounded mr-2 uppercase">Sukses</span> Dokumen Telah Terbit
                    </div>
                </div>
            </div>

            <!-- BANNER -->
            <div class="bg-white border border-purple-100 rounded-2xl p-6 shadow-sm flex flex-col md:flex-row items-center gap-6 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-32 h-32 bg-purple-50 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-110"></div>
                <div class="flex-shrink-0 relative">
                    <div class="w-14 h-14 bg-purple-600 rounded-2xl flex items-center justify-center shadow-lg shadow-purple-200">
                        <i class="fas fa-info-circle text-2xl text-white"></i>
                    </div>
                </div>
                <div class="flex-1 text-center md:text-left relative">
                    <h4 class="font-bold text-slate-800 text-lg mb-1">Instruksi Hasil Verifikasi</h4>
                    <p class="text-slate-500 text-sm leading-relaxed">
                        Unduh dokumen surat hasil verifikasi pada kolom <span class="text-purple-600 font-semibold italic">File Admin</span>. Jika ada tindak lanjut, selesaikan proses di aplikasi <span class="bg-purple-50 text-purple-700 px-2 py-0.5 rounded font-bold text-xs border border-purple-100">Balis 2.5 Modul Penunjukan</span> lalu lakukan konfirmasi melalui tombol di tabel bawah.
                    </p>
                </div>
            </div>

            <!-- TABLE SECTION -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden text-left">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-white">
                    <h3 class="font-bold text-slate-800 text-lg tracking-tight">Kotak Masuk Verifikasi</h3>
                </div>

                <div class="overflow-x-auto no-scrollbar">
                    <table class="w-full text-sm text-left border-collapse">
                        <thead class="text-[11px] text-slate-400 uppercase bg-slate-50/50 border-b font-black tracking-widest">
                            <tr>
                                <th class="px-6 py-4">Tanggal</th>
                                <th class="px-6 py-4">Judul Dokumen</th>
                                <th class="px-6 py-4">File Admin</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-center">Aksi Anda</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($verifikasis as $item)
                            <tr class="hover:bg-slate-50/50 transition-colors group cursor-pointer">
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-700 text-xs">{{ $item->created_at->format('d M Y') }}</span>
                                        <span class="text-[10px] text-slate-400 font-medium">{{ $item->created_at->format('H:i') }} WIB</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-col text-left">
                                        <span class="font-bold text-slate-800 group-hover:text-purple-600 transition-colors text-sm">{{ $item->title }}</span>
                                        <div class="text-[10px] text-slate-400 font-mono tracking-tighter mt-1 uppercase font-bold">Ref: #{{ substr($item->id, 0, 8) }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    @if($item->admin_file)
                                        <a href="{{ asset('storage/' . $item->admin_file) }}" target="_blank" onclick="event.stopPropagation()" class="inline-flex items-center gap-2 bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded-lg hover:bg-indigo-100 transition-all font-bold text-[10px] uppercase border border-indigo-100 shadow-sm">
                                            <i class="fas fa-file-pdf text-rose-500"></i> Unduh Surat
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-300 italic">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-5 text-center">
                                    @php
                                        $stData = match($item->status) {
                                            'approved' => ['Selesai', 'bg-emerald-100 text-emerald-700 border-emerald-200'],
                                            default => $item->user_note 
                                                ? ['Verif Admin', 'bg-blue-100 text-blue-700 border-blue-200 animate-pulse'] 
                                                : ['Belum Aksi', 'bg-purple-100 text-purple-700 border-purple-200']
                                        };
                                    @endphp
                                    <span class="{{ $stData[1] }} px-3 py-0.5 rounded-full text-[10px] font-black border uppercase tracking-tighter">{{ $stData[0] }}</span>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    @if($item->status == 'approved')
                                        <i class="fas fa-check-circle text-emerald-500 text-lg"></i>
                                    @elseif($item->user_note)
                                        <span class="text-[10px] text-slate-400 font-bold italic">Terkonfirmasi</span>
                                    @else
                                        <button 
                                            onclick="event.stopPropagation(); openConfirmModal(this)"
                                            data-id="{{ $item->id }}"
                                            data-title="{{ $item->title }}"
                                            class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-1.5 rounded-lg text-[10px] font-black shadow-md transition-all active:scale-95 uppercase tracking-wide">
                                            Tindak Lanjut
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-32 text-center opacity-30 uppercase font-black text-xs tracking-widest">Tidak ada data verifikasi masuk</td>
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

<!-- SUCCESS MODAL DENGAN TIMER -->
@if (session('success'))
<div id="successModal" class="fixed inset-0 z-[100] flex items-center justify-center modal-backdrop-blur transition-opacity duration-300">
    <div class="absolute inset-0" onclick="closeSuccessModal()"></div>
    <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-sm w-full mx-4 text-center animate-pop-in relative border border-slate-100 overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-emerald-500 to-teal-600"></div>
        <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-4 shadow-inner">
            <i class="fas fa-check text-4xl text-emerald-600"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-800 mb-2 tracking-tight">Berhasil!</h3>
        <p class="text-slate-500 mb-6 text-sm font-medium leading-relaxed">{{ session('success') }}</p>
        <button onclick="closeSuccessModal()" class="w-full bg-slate-900 text-white font-bold py-3 rounded-xl hover:bg-black transition-all active:scale-95 shadow-lg uppercase text-xs tracking-widest">Tutup</button>
        
        <!-- Timer Progress Bar -->
        <div class="absolute bottom-0 left-0 h-1.5 bg-emerald-500 timer-bar"></div>
    </div>
</div>
@endif

<!-- CONFIRM MODAL -->
<div id="confirmModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="fixed inset-0 modal-backdrop-blur transition-opacity duration-300" onclick="closeModal()"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md border border-slate-100 animate-pop-in overflow-hidden text-left">
        <div class="bg-slate-900 px-6 py-6 flex items-center justify-between text-white relative">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-purple-500 via-indigo-500 to-blue-500"></div>
            <h3 class="text-lg font-black flex items-center gap-3">
                <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center border border-white/20">
                    <i class="fas fa-tasks text-blue-400"></i>
                </div>
                Konfirmasi Tindak Lanjut
            </h3>
            <button onclick="closeModal()" class="w-8 h-8 rounded-full hover:bg-white/10 flex items-center justify-center transition-colors">
                <i class="fas fa-times text-slate-400"></i>
            </button>
        </div>
        <div class="p-8">
            <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200/60 mb-8 shadow-inner">
                <p class="text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest flex items-center gap-2">
                    <i class="fas fa-file-alt text-slate-300"></i> Dokumen Terkait
                </p>
                <p id="docTitleDisplay" class="text-sm font-bold text-slate-700 italic leading-snug">Judul Dokumen</p>
            </div>
            
            <div class="flex items-start gap-4 mb-8 bg-purple-50/50 p-4 rounded-2xl border border-purple-100">
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center text-purple-600 shrink-0">
                    <i class="fas fa-info-circle text-sm"></i>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed font-medium">
                    Pastikan Anda telah menindaklanjuti instruksi Admin di aplikasi <strong class="text-slate-800">Balis 2.5 Modul Penunjukan</strong> sebelum mengirim konfirmasi ini.
                </p>
            </div>

            <form id="responseForm" method="POST" action="">
                @csrf
                @method('PUT')
                
                <input type="hidden" name="title" id="hiddenDocTitle">
                <input type="hidden" name="user_note" value="Lembaga telah menindaklanjuti dokumen via Balis 2.5 Modul Penunjukan.">
                <input type="hidden" name="force_status" value="pending">
                
                <div class="space-y-3">
                    <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-indigo-700 hover:from-purple-700 hover:to-indigo-800 text-white font-black py-4 rounded-2xl shadow-xl shadow-purple-200 transition-all active:scale-[0.98] text-xs uppercase tracking-widest flex items-center justify-center gap-2">
                        Kirim Konfirmasi <i class="fas fa-paper-plane text-[10px] opacity-70"></i>
                    </button>
                    <button type="button" onclick="closeModal()" class="w-full bg-white border-2 border-slate-100 text-slate-400 font-bold py-3.5 rounded-2xl hover:bg-slate-50 hover:text-slate-600 transition-all text-xs uppercase tracking-widest">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }

    function openConfirmModal(btn) {
        const id = btn.getAttribute('data-id');
        const title = btn.getAttribute('data-title');
        document.getElementById('docTitleDisplay').innerText = title;
        document.getElementById('hiddenDocTitle').value = title;
        document.getElementById('responseForm').action = "/submission/update/" + id;
        document.getElementById('confirmModal').classList.remove('hidden');
        document.getElementById('confirmModal').classList.add('flex');
    }

    function closeModal() {
        const modal = document.getElementById('confirmModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function closeSuccessModal() {
        const modal = document.getElementById('successModal');
        if(modal) {
            modal.style.opacity = '0';
            modal.style.transition = 'opacity 0.3s ease';
            setTimeout(() => { 
                modal.classList.add('hidden'); 
                modal.classList.remove('flex');
                modal.remove(); // Hapus dari DOM
            }, 300);
        }
    }

    // Jalankan timer saat halaman dimuat jika ada modal sukses
    window.onload = () => {
        const successModal = document.getElementById('successModal');
        if(successModal) {
            setTimeout(() => {
                closeSuccessModal();
            }, 5000); // 5 detik sesuai durasi animasi bar
        }
    }
</script>

</body>
</html>