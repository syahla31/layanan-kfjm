<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard KAK | SI-MUTU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Konfigurasi Animasi Tailwind -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    animation: { 
                        'pop-in': 'popIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards',
                        'fade-out': 'fadeOut 0.3s ease-in forwards'
                    },
                    keyframes: {
                        popIn: {
                            '0%': { opacity: '0', transform: 'scale(0.8) translateY(20px)' },
                            '100%': { opacity: '1', transform: 'scale(1) translateY(0)' },
                        },
                        fadeOut: {
                            '0%': { opacity: '1' },
                            '100%': { opacity: '0' },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        /* Glass effect untuk overlay mobile */
        .glass-overlay {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <!-- 1. LOGIKA FETCH DATA KHUSUS KAK -->
    @php
        use App\Models\Submission;
        use Illuminate\Support\Facades\Auth;
        
        $specificType = 'KAK'; 

        if (!isset($mySubmissions)) {
            $query = Submission::where('user_id', Auth::id())
                               ->where('type', $specificType)
                               ->orderBy('created_at', 'desc');
            
            try {
                $check = new Submission();
                if(method_exists($check, 'files')) {
                    $query->with('files');
                }
            } catch(\Exception $e) {}

            $mySubmissions = $query->get();
        }

        $myPending = $mySubmissions->where('status', 'pending')->count();
        $myApproved = $mySubmissions->where('status', 'approved')->count();
        $myRejected = $mySubmissions->where('status', 'rejected')->count();
    @endphp

    <div class="flex h-screen overflow-hidden bg-slate-50">
        
        <!-- === MOBILE OVERLAY (Black Background) === -->
        <div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 z-40 hidden lg:hidden glass-overlay transition-opacity duration-300"></div>

        <!-- === SIDEBAR WRAPPER (Responsive) === -->
        <!-- Hidden on mobile (-translate-x-full), visible on Desktop (lg:translate-x-0) -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-2xl lg:shadow-none transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto flex flex-col h-full border-r border-slate-200">
            <!-- INCLUDE SIDEBAR ASLI -->
            @include('components.pelatihan-sidebar')
        </aside>

        <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
            
            <!-- === MOBILE HEADER BAR (Hanya Muncul di HP) === -->
            <div class="lg:hidden bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between sticky top-0 z-30 shadow-sm">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="p-2 text-slate-600 hover:text-indigo-600 hover:bg-slate-100 rounded-lg transition-colors">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <span class="font-bold text-slate-800 text-sm tracking-wide">SI-MUTU <span class="text-indigo-600">DKKN</span></span>
                </div>
                <!-- Profile Icon Kecil -->
                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 text-xs font-bold border border-indigo-200">
                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                </div>
            </div>

            <!-- INCLUDE HEADER UTAMA (Akan sembunyi di bawah mobile header saat scroll atau menyatu) -->
            <div class="hidden lg:block">
                @include('components.pelatihan-header', [
                    'title' => 'Dashboard KAK',
                    'subtitle' => 'Kelola pengajuan Kerangka Acuan Kerja (KAK)'
                ])
            </div>
            
            <!-- MAIN CONTENT -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-8 relative">

                <!-- POP-UP MODAL NOTIFIKASI (Global) -->
                <!-- 1. Success Modal (Auto Close) -->
                @if (session('success'))
                <div id="successModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 backdrop-blur-[3px] transition-all duration-300">
                    <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-sm w-full mx-4 text-center animate-pop-in relative overflow-hidden border border-white/50">
                        <!-- Efek Dekorasi Background -->
                        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-emerald-400 to-teal-500"></div>
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-emerald-50 rounded-full blur-2xl opacity-60 pointer-events-none"></div>
                        <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-teal-50 rounded-full blur-2xl opacity-60 pointer-events-none"></div>

                        <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow-inner relative z-10">
                            <i class="fas fa-check text-4xl text-emerald-600 drop-shadow-sm"></i>
                        </div>
                        
                        <h3 class="text-2xl font-bold text-slate-800 mb-2 relative z-10">Berhasil!</h3>
                        <p class="text-slate-600 mb-6 text-sm leading-relaxed relative z-10 font-medium">
                            {{ session('success') }}
                        </p>
                        
                        <!-- Progress Bar Container -->
                        <div class="relative z-10 w-full bg-slate-100 h-1.5 rounded-full mb-5 overflow-hidden">
                            <div id="progressBar" class="h-full bg-emerald-500 rounded-full" style="width: 100%"></div>
                        </div>

                        <!-- Tombol Manual -->
                        <button onclick="closeNotification('successModal')" class="relative z-10 w-full bg-white border-2 border-slate-100 hover:border-emerald-400 hover:bg-emerald-50 text-slate-500 hover:text-emerald-700 font-bold py-3 rounded-xl transition-all duration-300 transform active:scale-95 shadow-sm hover:shadow-md group">
                            <span class="flex items-center justify-center gap-2">
                                Tutup Sekarang 
                                <i class="fas fa-times group-hover:rotate-90 transition-transform duration-300 text-xs"></i>
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
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-rose-50 rounded-full blur-2xl opacity-60 pointer-events-none"></div>
                        <div class="w-20 h-20 bg-rose-100 rounded-full flex items-center justify-center mx-auto mb-5 shadow-inner relative z-10">
                            <i class="fas fa-times text-4xl text-rose-600 drop-shadow-sm"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-800 mb-2 relative z-10">Gagal!</h3>
                        <div class="text-slate-600 mb-8 text-sm leading-relaxed relative z-10 font-medium">
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
                        <button onclick="closeNotification('errorModal')" class="relative z-10 w-full bg-slate-800 hover:bg-slate-900 text-white font-bold py-3.5 rounded-xl transition-all shadow-lg transform active:scale-95">
                            Coba Lagi
                        </button>
                    </div>
                </div>
                @endif

                <!-- 3. KONFIRMASI BATAL MODAL -->
                <div id="cancelModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-slate-900/50 backdrop-blur-[2px] transition-all duration-300">
                    <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-sm w-full mx-4 text-center animate-pop-in relative overflow-hidden border border-white/50">
                        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-orange-400 to-red-500"></div>
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-orange-50 rounded-full blur-2xl opacity-60 pointer-events-none"></div>
                        <div class="w-20 h-20 bg-orange-50 border-4 border-orange-100 rounded-full flex items-center justify-center mx-auto mb-5 relative z-10">
                            <i class="fas fa-question text-4xl text-orange-500 drop-shadow-sm"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 mb-2 relative z-10">Batalkan Pengajuan?</h3>
                        <p class="text-slate-500 mb-8 text-sm leading-relaxed relative z-10 font-medium">
                            Anda yakin ingin menghapus pengajuan ini? Tindakan ini tidak dapat dibatalkan.
                        </p>
                        <div class="flex flex-col gap-3 relative z-10">
                            <form id="cancelForm" action="" method="POST" class="w-full">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-bold py-3 rounded-xl transition-all shadow-lg shadow-rose-200 transform active:scale-95 flex items-center justify-center gap-2">
                                    <i class="fas fa-trash-alt"></i> Ya, Hapus Pengajuan
                                </button>
                            </form>
                            <button onclick="closeCancelModal()" class="w-full bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 font-bold py-3 rounded-xl transition-all">
                                Tidak, Kembali
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Statistik Section -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
                    <!-- Total KAK -->
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow duration-300">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Total KAK</p>
                                <h3 class="text-3xl font-bold text-slate-800 mt-2">{{ $mySubmissions->count() }}</h3>
                            </div>
                            <div class="p-3 bg-indigo-50 rounded-xl text-indigo-600">
                                <i class="fas fa-project-diagram text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-xs text-slate-400">
                            <span class="text-indigo-600 font-medium bg-indigo-50 px-1.5 py-0.5 rounded mr-2">Semua</span> Dokumen
                        </div>
                    </div>

                    <!-- Sedang Diproses -->
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow duration-300">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Sedang Diproses</p>
                                <h3 class="text-3xl font-bold text-slate-800 mt-2">{{ $myPending }}</h3>
                            </div>
                            <div class="p-3 bg-amber-50 rounded-xl text-amber-500">
                                <i class="fas fa-clock text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-xs text-slate-400">
                            <span class="text-amber-600 font-medium bg-amber-50 px-1.5 py-0.5 rounded mr-2">Menunggu</span> Verifikasi
                        </div>
                    </div>

                    <!-- Disetujui -->
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow duration-300">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Disetujui</p>
                                <h3 class="text-3xl font-bold text-slate-800 mt-2">{{ $myApproved }}</h3>
                            </div>
                            <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600">
                                <i class="fas fa-check-circle text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-xs text-slate-400">
                            <span class="text-emerald-600 font-medium bg-emerald-50 px-1.5 py-0.5 rounded mr-2">Sukses</span> Terverifikasi
                        </div>
                    </div>

                    <!-- Perlu Revisi -->
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow duration-300">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Perlu Revisi</p>
                                <h3 class="text-3xl font-bold text-slate-800 mt-2">{{ $myRejected }}</h3>
                            </div>
                            <div class="p-3 bg-rose-50 rounded-xl text-rose-500">
                                <i class="fas fa-exclamation-triangle text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-xs text-slate-400">
                            <span class="text-rose-600 font-medium bg-rose-50 px-1.5 py-0.5 rounded mr-2">Action</span> Segera Perbaiki
                        </div>
                    </div>
                </div>

                <!-- Call to Action Upload -->
                <div class="bg-gradient-to-r from-indigo-600 to-purple-700 rounded-2xl p-6 mb-8 text-white shadow-lg shadow-indigo-200 flex flex-col md:flex-row justify-between items-center gap-4 relative overflow-hidden group hover:shadow-xl transition-shadow">
                    <div class="absolute top-0 right-0 -mr-10 -mt-10 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl group-hover:scale-110 transition-transform duration-700"></div>
                    <div class="absolute bottom-0 left-0 -ml-10 -mb-10 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl"></div>

                    <div class="relative z-10 text-center md:text-left">
                        <h3 class="font-bold text-xl mb-1">Ajukan KAK Baru</h3>
                        <p class="text-indigo-100 text-sm">Upload Kerangka Acuan Kerja untuk kegiatan mendatang.</p>
                    </div>
                    <button onclick="openModal('add')" class="relative z-10 bg-white text-indigo-700 hover:bg-indigo-50 px-6 py-3 rounded-xl shadow-md transition-all transform hover:scale-105 flex items-center gap-2 font-bold text-sm group-btn">
                        <div class="bg-indigo-100 rounded-full p-1 group-hover:bg-indigo-200 transition-colors">
                            <i class="fas fa-plus text-xs"></i>
                        </div>
                        Upload KAK
                    </button>
                </div>

                <!-- Tabel Data -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 bg-white flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h3 class="font-bold text-slate-800 text-lg">Daftar KAK Anda</h3>
                            <p class="text-slate-500 text-xs">Riwayat pengajuan Kerangka Acuan Kerja</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-slate-600">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-50/50 border-b border-slate-100">
                                <tr>
                                    <th class="px-6 py-4 font-semibold tracking-wider">Tanggal</th>
                                    <th class="px-6 py-4 font-semibold tracking-wider">Judul Kegiatan</th>
                                    <th class="px-6 py-4 font-semibold tracking-wider">File Dokumen</th>
                                    <th class="px-6 py-4 font-semibold tracking-wider text-center">Status</th>
                                    <th class="px-6 py-4 font-semibold tracking-wider text-center">Riwayat</th>
                                    <th class="px-6 py-4 font-semibold tracking-wider text-center">Tindak Lanjut</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($mySubmissions as $item)
                                <tr class="bg-white hover:bg-slate-50 transition-colors group">
                                    <!-- Tanggal -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-slate-700">{{ $item->created_at->format('d M Y') }}</span>
                                            <span class="text-[11px] text-slate-400">{{ $item->created_at->format('H:i') }} WIB</span>
                                        </div>
                                    </td>

                                    <!-- Judul -->
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-1">
                                            <span class="font-bold text-slate-800 text-sm group-hover:text-indigo-600 transition-colors">
                                                {{ $item->title }}
                                            </span>
                                            <span class="text-xs text-slate-400">Kerangka Acuan Kerja</span>
                                        </div>
                                    </td>

                                    <!-- File User -->
                                    <td class="px-6 py-4">
                                        <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank" class="inline-flex items-center gap-2 bg-white border border-slate-200 text-slate-600 hover:text-indigo-600 hover:border-indigo-300 px-3 py-2 rounded-lg transition-all shadow-sm group/file">
                                            <div class="bg-rose-50 text-rose-500 p-1 rounded group-hover/file:bg-rose-100 transition-colors">
                                                <i class="far fa-file-pdf"></i>
                                            </div>
                                            <span class="font-medium text-xs">Lihat PDF</span>
                                        </a>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4 text-center">
                                        @if($item->status == 'pending')
                                            <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 text-xs font-bold px-3 py-1.5 rounded-full border border-amber-100">
                                                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span> Menunggu
                                            </span>
                                        @elseif($item->status == 'approved')
                                            <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 text-xs font-bold px-3 py-1.5 rounded-full border border-emerald-100">
                                                <i class="fas fa-check-circle text-emerald-500"></i> Disetujui
                                            </span>
                                        @elseif($item->status == 'rejected')
                                            <span class="inline-flex items-center gap-1.5 bg-rose-50 text-rose-700 text-xs font-bold px-3 py-1.5 rounded-full border border-rose-100">
                                                <i class="fas fa-exclamation-circle text-rose-500"></i> Revisi
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Riwayat -->
                                    <td class="px-6 py-4 text-center">
                                        @if($item->files && $item->files->count() > 0)
                                            <button onclick='openHistoryModal(@json($item->files))' class="text-slate-500 hover:text-indigo-600 bg-slate-50 hover:bg-white border border-transparent hover:border-slate-200 px-3 py-1.5 rounded-lg transition-all text-xs font-medium flex items-center justify-center gap-1 mx-auto">
                                                <i class="fas fa-history"></i> {{ $item->files->count() }} Versi
                                            </button>
                                        @else
                                            <span class="text-slate-300 text-xs">-</span>
                                        @endif
                                    </td>

                                    <!-- Tindak Lanjut -->
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-2 items-center">
                                            
                                            <!-- REVISI MODE -->
                                            @if($item->status == 'rejected')
                                                @if($item->admin_note)
                                                    <div class="group/note relative w-full">
                                                        <div class="text-[10px] bg-rose-50 border border-rose-100 text-rose-700 p-2 rounded cursor-help truncate text-center transition-colors hover:bg-rose-100">
                                                            <i class="fas fa-comment-alt mr-1"></i> Lihat Catatan
                                                        </div>
                                                        <div class="absolute bottom-full right-0 mb-2 w-56 p-3 bg-slate-800 text-white text-xs rounded-xl shadow-xl opacity-0 group-hover/note:opacity-100 transition-all pointer-events-none z-50 text-center transform translate-y-2 group-hover/note:translate-y-0">
                                                            "{{ $item->admin_note }}"
                                                            <div class="absolute top-full right-4 -mt-1 border-4 border-transparent border-t-slate-800"></div>
                                                        </div>
                                                    </div>
                                                @endif
                                                
                                                <button onclick="openModal('edit', {{ $item->id }}, '{{ $item->type }}', '{{ $item->title }}')" class="w-full bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-indigo-700 shadow-sm hover:shadow active:scale-95 transition-all flex items-center justify-center gap-1.5">
                                                    <i class="fas fa-upload"></i> Upload Revisi
                                                </button>
                                            @endif

                                            <!-- DOWNLOAD HASIL -->
                                            @if($item->admin_file)
                                                <a href="{{ asset('storage/' . $item->admin_file) }}" target="_blank" class="w-full bg-white border border-emerald-200 text-emerald-600 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-emerald-50 hover:text-emerald-700 transition-all shadow-sm flex items-center justify-center gap-1.5">
                                                    <i class="fas fa-file-signature"></i> Unduh SK
                                                </a>
                                            @elseif($item->status == 'approved')
                                                <span class="text-xs text-emerald-600 font-medium bg-emerald-50 px-2 py-1 rounded">Selesai</span>
                                            @elseif($item->status == 'pending')
                                                <!-- TOMBOL HAPUS DENGAN MODAL -->
                                                <button onclick="openCancelModal('{{ route('submission.destroy', $item->id) }}')" class="text-xs text-slate-400 hover:text-rose-500 hover:underline transition-colors flex items-center gap-1">
                                                    <i class="far fa-trash-alt"></i> Batalkan
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                                <i class="fas fa-project-diagram text-4xl text-slate-300"></i>
                                            </div>
                                            <h3 class="text-slate-800 font-bold text-lg">Belum ada KAK</h3>
                                            <p class="text-slate-500 text-sm mt-1 max-w-xs mx-auto">Mulai ajukan Kerangka Acuan Kerja Anda dengan tombol di atas.</p>
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

    <!-- === MODAL FORMULIR === -->
    <div id="submissionModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full sm:max-w-md border border-slate-100 animate-pop-in">
                    
                    <!-- Modal Header -->
                    <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4 flex justify-between items-center">
                        <h3 class="text-base font-bold text-white flex items-center gap-2" id="modalTitle">
                            <i class="fas fa-file-upload text-sm"></i> Upload KAK
                        </h3>
                        <button onclick="closeModal()" class="text-indigo-200 hover:text-white bg-white/10 hover:bg-white/20 rounded-lg p-1.5 transition-colors">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>

                    <form id="submissionForm" method="POST" enctype="multipart/form-data" action="{{ route('submission.store') }}">
                        @csrf
                        <div id="methodField"></div>
                        <input type="hidden" name="type" value="KAK">
                        
                        <div class="px-6 py-5 space-y-4">
                            <!-- Info Box -->
                            <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-3 flex items-center gap-3">
                                <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600 flex-shrink-0">
                                    <i class="fas fa-clipboard-check text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest">Jenis Dokumen</p>
                                    <p class="text-sm font-bold text-indigo-900">Kerangka Acuan Kerja (KAK)</p>
                                </div>
                            </div>

                            <!-- Input Judul -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="fas fa-heading text-[10px]"></i> Judul Kegiatan
                                </label>
                                <input type="text" name="title" id="inputTitle"
                                    class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-medium shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 placeholder:text-slate-400 outline-none transition-all"
                                    placeholder="Masukkan judul kegiatan..." required>
                            </div>

                            <!-- Upload File -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="fas fa-file-pdf text-[10px]"></i> Dokumen PDF <span class="text-slate-400 font-normal normal-case tracking-normal">(maks. 2MB)</span>
                                </label>

                                <input id="inputFile" name="file_upload" type="file" class="hidden" accept=".pdf" onchange="showFileName(this)">
                                
                                <!-- State: Empty -->
                                <label for="inputFile" id="uploadArea" class="flex items-center gap-3 w-full px-4 py-3 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 cursor-pointer hover:bg-indigo-50/40 hover:border-indigo-300 transition-all">
                                    <div class="w-9 h-9 bg-white rounded-lg shadow-sm flex items-center justify-center text-slate-300 flex-shrink-0">
                                        <i class="fas fa-cloud-upload-alt text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-600">Klik untuk pilih file</p>
                                        <p class="text-[11px] text-slate-400">Format PDF saja, maksimal 2MB</p>
                                    </div>
                                </label>

                                <!-- State: File terpilih -->
                                <div id="fileSelectedState" class="hidden items-center gap-3 w-full px-4 py-3 rounded-xl border border-indigo-200 bg-indigo-50">
                                    <div class="w-9 h-9 bg-rose-100 rounded-lg flex items-center justify-center text-rose-500 flex-shrink-0">
                                        <i class="far fa-file-pdf text-lg"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p id="fileNameText" class="text-xs font-bold text-slate-800 truncate"></p>
                                        <p class="text-[10px] text-indigo-500 font-medium">File terpilih</p>
                                    </div>
                                    <label for="inputFile" class="text-[10px] text-indigo-600 hover:text-indigo-800 font-bold cursor-pointer flex-shrink-0">Ganti</label>
                                </div>

                                <!-- Note Revisi -->
                                <div id="fileNote" class="hidden items-start gap-2 bg-amber-50 border border-amber-100 p-2.5 rounded-xl">
                                    <i class="fas fa-info-circle text-amber-500 text-xs mt-0.5 flex-shrink-0"></i>
                                    <p class="text-[11px] text-amber-700 leading-relaxed">Biarkan kosong jika tidak ingin mengganti file.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="bg-slate-50 px-6 py-4 flex flex-col gap-2 border-t border-slate-100 sm:flex-row-reverse">
                            <button type="submit" class="inline-flex w-full sm:w-auto justify-center items-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white shadow-md hover:bg-indigo-700 active:scale-95 transition-all gap-2">
                                <i class="fas fa-save text-xs"></i> Simpan Dokumen
                            </button>
                            <button type="button" onclick="closeModal()" class="inline-flex w-full sm:w-auto justify-center items-center rounded-xl bg-white px-5 py-2.5 text-sm font-bold text-slate-500 border border-slate-200 hover:bg-slate-100 transition-all">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL RIWAYAT -->
    <div id="historyModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeHistoryModal()"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-4xl border border-slate-100 animate-pop-in">
                    <div class="bg-slate-800 px-6 py-4 flex justify-between items-center text-white">
                        <h3 class="text-lg font-bold flex items-center gap-3">
                            <div class="bg-slate-700 p-1.5 rounded-lg"><i class="fas fa-history text-sm"></i></div>
                            Riwayat Versi & Evaluasi
                        </h3>
                        <button onclick="closeHistoryModal()" class="text-slate-400 hover:text-white bg-slate-700/50 hover:bg-slate-700 rounded-lg p-1.5 transition-colors"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="max-h-[60vh] overflow-y-auto">
                        <table class="min-w-full text-sm text-left text-slate-600">
                            <thead class="bg-slate-50 font-bold text-slate-700 sticky top-0 z-10 text-xs uppercase shadow-sm">
                                <tr>
                                    <th class="px-6 py-4 border-b border-slate-200 w-20 text-center">Versi</th>
                                    <th class="px-6 py-4 border-b border-slate-200 w-48">Waktu Kirim (Anda)</th>
                                    <th class="px-6 py-4 border-b border-slate-200">File Upload (Anda)</th>
                                    <th class="px-6 py-4 border-b border-slate-200 w-48 bg-slate-100/50">Waktu Respon</th>
                                    <th class="px-6 py-4 border-b border-slate-200 bg-slate-100/50">Evaluasi Admin</th>
                                </tr>
                            </thead>
                            <tbody id="historyTableBody" class="divide-y divide-slate-100 text-xs"></tbody>
                        </table>
                    </div>
                    <div class="bg-slate-50 px-6 py-4 flex justify-end border-t border-slate-100">
                         <button onclick="closeHistoryModal()" class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 text-sm rounded-xl hover:bg-slate-50 font-bold shadow-sm transition-colors">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // === SIDEBAR TOGGLE LOGIC ===
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            if (sidebar.classList.contains('-translate-x-full')) {
                // Open Sidebar
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            } else {
                // Close Sidebar
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        }

        // === MODAL NOTIFICATION LOGIC ===
        function closeNotification(modalId) {
            const modal = document.getElementById(modalId);
            if(modal) {
                // Tambahkan animasi fade-out manual
                modal.classList.add('opacity-0');
                modal.querySelector('div').classList.add('scale-95');
                setTimeout(() => {
                    modal.style.display = 'none';
                }, 300);
            }
        }

        // === CANCEL MODAL LOGIC ===
        function openCancelModal(url) {
            const modal = document.getElementById('cancelModal');
            const form = document.getElementById('cancelForm');
            
            // Set action URL form hapus
            form.action = url;
            
            modal.classList.remove('hidden');
        }

        function closeCancelModal() {
            const modal = document.getElementById('cancelModal');
            if(modal) {
                modal.classList.add('hidden');
            }
        }

        // Auto Close Success Modal Logic
        document.addEventListener('DOMContentLoaded', () => {
            const successModal = document.getElementById('successModal');
            if(successModal) {
                const progressBar = document.getElementById('progressBar');
                
                // Animate Progress Bar (3 seconds)
                // Small delay to ensure CSS transition triggers properly
                setTimeout(() => {
                    progressBar.style.transition = 'width 3s linear';
                    progressBar.style.width = '0%';
                }, 100);

                // Close after 3.1 seconds (slightly after animation ends)
                setTimeout(() => {
                    closeNotification('successModal');
                }, 3100);
            }
        });

        // === FORM & HISTORY LOGIC ===
        function showFileName(input) {
            const uploadArea = document.getElementById('uploadArea');
            const fileSelectedState = document.getElementById('fileSelectedState');
            const fileNameText = document.getElementById('fileNameText');

            if (input.files && input.files[0]) {
                fileNameText.textContent = input.files[0].name;
                uploadArea.classList.add('hidden');
                fileSelectedState.classList.remove('hidden');
                fileSelectedState.classList.add('flex');
            } else {
                uploadArea.classList.remove('hidden');
                fileSelectedState.classList.add('hidden');
                fileSelectedState.classList.remove('flex');
            }
        }

        function openModal(mode, id = null, type = '', title = '') {
            const modal = document.getElementById('submissionModal');
            const form = document.getElementById('submissionForm');
            const modalTitle = document.getElementById('modalTitle');
            const methodField = document.getElementById('methodField');
            const inputTitle = document.getElementById('inputTitle');
            const inputFile = document.getElementById('inputFile');
            const fileNote = document.getElementById('fileNote');
            const uploadArea = document.getElementById('uploadArea');
            const fileSelectedState = document.getElementById('fileSelectedState');

            // Reset tampilan file ke state awal
            inputFile.value = '';
            uploadArea.classList.remove('hidden');
            fileSelectedState.classList.add('hidden');
            fileSelectedState.classList.remove('flex');

            modal.classList.remove('hidden');

            if (mode === 'add') {
                modalTitle.innerHTML = '<i class="fas fa-plus-circle text-sm"></i> Upload KAK Baru';
                form.action = "{{ route('submission.store') }}";
                methodField.innerHTML = '';
                inputTitle.value = '';
                inputFile.required = true;
                fileNote.classList.add('hidden');
                fileNote.classList.remove('flex');
            } else {
                modalTitle.innerHTML = '<i class="fas fa-edit text-sm"></i> Revisi KAK';
                form.action = "{{ url('/submission/update') }}/" + id;
                methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
                inputTitle.value = title;
                inputFile.required = false;
                fileNote.classList.remove('hidden');
                fileNote.classList.add('flex');
            }
        }

        function openHistoryModal(files) {
            const modal = document.getElementById('historyModal');
            const tbody = document.getElementById('historyTableBody');
            tbody.innerHTML = ''; 

            if(!files || files.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="p-12 text-center text-slate-400 italic">Tidak ada riwayat versi.</td></tr>';
            } else {
                files.sort((a, b) => b.version - a.version);
                files.forEach(file => {
                    const userDate = new Date(file.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
                    let adminDate = '-';
                    let adminContent = '<span class="text-slate-300 italic text-[11px]">- Belum ada -</span>';

                    if (file.admin_file || file.admin_note) {
                        const d = new Date(file.updated_at); 
                        adminDate = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
                        adminContent = `
                            <div class="flex flex-col items-start gap-2">
                                ${file.admin_file ? `<a href="/storage/${file.admin_file}" target="_blank" class="inline-flex items-center gap-1.5 bg-white border border-emerald-200 text-emerald-700 px-3 py-1.5 rounded-lg hover:bg-emerald-50 hover:border-emerald-300 transition-colors font-bold shadow-sm text-[11px]"><i class="fas fa-file-signature text-emerald-500"></i> Unduh Evaluasi</a>` : ''}
                                ${file.admin_note ? `<div class="bg-amber-50 border border-amber-100 text-amber-800 px-2 py-1.5 rounded-md text-[10px] w-full"><i class="fas fa-comment-alt mr-1 text-amber-500"></i> "${file.admin_note}"</div>` : ''}
                            </div>`;
                    }

                    const row = `
                        <tr class="hover:bg-slate-50 transition-colors border-b last:border-0">
                            <td class="px-6 py-4"><div class="bg-indigo-50 text-indigo-700 font-bold w-8 h-8 rounded-full flex items-center justify-center mx-auto text-xs border border-indigo-100">v${file.version}</div></td>
                            <td class="px-6 py-4 text-slate-600 font-mono text-[11px]">${userDate}</td>
                            <td class="px-6 py-4"><a href="/storage/${file.file_path}" target="_blank" class="group flex items-center gap-3 text-slate-700 hover:text-indigo-700 transition-colors bg-white border border-slate-200 hover:border-indigo-300 p-2.5 rounded-xl shadow-sm"><div class="bg-rose-50 text-rose-500 p-1.5 rounded-lg group-hover:bg-rose-100 transition-colors"><i class="far fa-file-pdf text-lg"></i></div><span class="font-medium text-[11px] truncate max-w-[150px]">${file.file_name}</span></a>${file.user_note ? `<div class="text-[10px] text-slate-400 mt-1.5 ml-1 pl-2 border-l-2 border-slate-200 italic">"${file.user_note}"</div>` : ''}</td>
                            <td class="px-6 py-4 text-slate-600 font-mono text-[11px] bg-slate-50/50">${adminDate}</td>
                            <td class="px-6 py-4 bg-slate-50/50">${adminContent}</td>
                        </tr>`;
                    tbody.innerHTML += row;
                });
            }
            document.getElementById('historyModal').classList.remove('hidden');
        }

        function closeHistoryModal() { document.getElementById('historyModal').classList.add('hidden'); }
        function closeModal() { document.getElementById('submissionModal').classList.add('hidden'); }
    </script>
</body>
</html>