<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Lapkin | SI-MUTU</title>
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
        
        /* Glass overlay for mobile */
        .glass-overlay {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <!-- 1. LOGIKA FETCH DATA KHUSUS LAPKIN -->
    @php
        use App\Models\Submission;
        use Illuminate\Support\Facades\Auth;
        
        $specificType = 'Laporan Kinerja'; 

        if (!isset($submissions)) {
            $query = Submission::where('user_id', Auth::id())
                               ->where('type', $specificType)
                               ->orderBy('created_at', 'desc');
            try {
                $check = new Submission();
                if(method_exists($check, 'files')) {
                    $query->with('files');
                }
            } catch(\Exception $e) {}

            $submissions = $query->get();
        }

        $myPending = $submissions->where('status', 'pending')->count();
        $myApproved = $submissions->where('status', 'approved')->count();
        $myRejected = $submissions->where('status', 'rejected')->count();
    @endphp

    <div class="flex h-screen overflow-hidden bg-slate-50">
        
        <!-- === MOBILE OVERLAY === -->
        <div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 z-40 hidden lg:hidden glass-overlay transition-opacity duration-300"></div>

        <!-- === SIDEBAR WRAPPER (Responsive) === -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-2xl lg:shadow-none transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto flex flex-col h-full border-r border-slate-200">
            @include('components.pelatihan-sidebar')
        </aside>

        <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
            
            <!-- === MOBILE HEADER BAR === -->
            <div class="lg:hidden bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between sticky top-0 z-30 shadow-sm">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="p-2 text-slate-600 hover:text-teal-600 hover:bg-slate-100 rounded-lg transition-colors">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <span class="font-bold text-slate-800 text-sm tracking-wide">SI-MUTU <span class="text-teal-600">DKKN</span></span>
                </div>
                <div class="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center text-teal-600 text-xs font-bold border border-teal-200">
                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                </div>
            </div>

            <!-- Header Desktop -->
            <div class="hidden lg:block">
                @include('components.pelatihan-header', [
                    'title' => 'Dashboard Lapkin',
                    'subtitle' => 'Kelola Laporan Kinerja Tahunan'
                ])
            </div>

            <!-- MAIN CONTENT -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-8 relative">
                
                <!-- SUCCESS POP-UP -->
                @if (session('success'))
                <div id="successModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 backdrop-blur-[3px] transition-all duration-300">
                    <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-sm w-full mx-4 text-center animate-pop-in relative overflow-hidden border border-white/50">
                        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-teal-400 to-emerald-500"></div>
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-teal-50 rounded-full blur-2xl opacity-60 pointer-events-none"></div>
                        <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-emerald-50 rounded-full blur-2xl opacity-60 pointer-events-none"></div>
                        
                        <div class="w-20 h-20 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow-inner relative z-10">
                            <i class="fas fa-check text-4xl text-teal-600 drop-shadow-sm"></i>
                        </div>
                        
                        <h3 class="text-2xl font-bold text-slate-800 mb-2 relative z-10">Berhasil!</h3>
                        <p class="text-slate-600 mb-6 text-sm leading-relaxed relative z-10 font-medium">
                            {{ session('success') }}
                        </p>
                        
                        <div class="relative z-10 w-full bg-slate-100 h-1.5 rounded-full mb-5 overflow-hidden">
                            <div id="progressBar" class="h-full bg-teal-500 rounded-full" style="width: 100%"></div>
                        </div>
                        
                        <button onclick="closeNotification('successModal')" class="relative z-10 w-full bg-white border-2 border-slate-100 hover:border-teal-400 hover:bg-teal-50 text-slate-500 hover:text-teal-700 font-bold py-3 rounded-xl transition-all duration-300 transform active:scale-95 shadow-sm hover:shadow-md group">
                            <span class="flex items-center justify-center gap-2">
                                Tutup Sekarang 
                                <i class="fas fa-times group-hover:rotate-90 transition-transform duration-300 text-xs"></i>
                            </span>
                        </button>
                    </div>
                </div>
                @endif

                <!-- ERROR POP-UP -->
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

                <!-- CANCEL POP-UP -->
                <div id="cancelModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-slate-900/50 backdrop-blur-[2px] transition-all duration-300">
                    <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-sm w-full mx-4 text-center animate-pop-in relative overflow-hidden border border-white/50">
                        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-orange-400 to-red-500"></div>
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-orange-50 rounded-full blur-2xl opacity-60 pointer-events-none"></div>
                        
                        <div class="w-20 h-20 bg-orange-50 border-4 border-orange-100 rounded-full flex items-center justify-center mx-auto mb-5 relative z-10">
                            <i class="fas fa-question text-4xl text-orange-500 drop-shadow-sm"></i>
                        </div>
                        
                        <h3 class="text-xl font-bold text-slate-800 mb-2 relative z-10">Batalkan Laporan?</h3>
                        <p class="text-slate-500 mb-8 text-sm leading-relaxed relative z-10 font-medium">
                            Anda yakin ingin menghapus laporan ini? Tindakan ini tidak dapat dibatalkan.
                        </p>
                        
                        <div class="flex flex-col gap-3 relative z-10">
                            <form id="cancelForm" action="" method="POST" class="w-full">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-bold py-3 rounded-xl transition-all shadow-lg shadow-rose-200 transform active:scale-95 flex items-center justify-center gap-2">
                                    <i class="fas fa-trash-alt"></i> Ya, Hapus Laporan
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
                    <!-- Total -->
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow duration-300">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Total Laporan</p>
                                <h3 class="text-3xl font-bold text-slate-800 mt-2">{{ $submissions->count() }}</h3>
                            </div>
                            <div class="p-3 bg-teal-50 rounded-xl text-teal-600">
                                <i class="fas fa-file-invoice text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-xs text-slate-400">
                            <span class="text-teal-600 font-medium bg-teal-50 px-1.5 py-0.5 rounded mr-2">Semua</span> Periode
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
                                <i class="fas fa-hourglass-half text-xl"></i>
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
                            <span class="text-emerald-600 font-medium bg-emerald-50 px-1.5 py-0.5 rounded mr-2">Sukses</span> Valid
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
                            <span class="text-rose-600 font-medium bg-rose-50 px-1.5 py-0.5 rounded mr-2">Action</span> Cek Catatan
                        </div>
                    </div>
                </div>

                <!-- Call to Action Upload (Gradient Emerald) -->
                <div class="bg-gradient-to-r from-teal-600 to-emerald-600 rounded-2xl p-6 mb-8 text-white shadow-lg shadow-teal-200 flex flex-col md:flex-row justify-between items-center gap-4 relative overflow-hidden group hover:shadow-xl transition-shadow">
                    <div class="absolute top-0 right-0 -mr-10 -mt-10 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl group-hover:scale-110 transition-transform duration-700"></div>
                    <div class="absolute bottom-0 left-0 -ml-10 -mb-10 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl"></div>

                    <div class="relative z-10 text-center md:text-left">
                        <h3 class="font-bold text-xl mb-1">Lapor Kinerja Tahunan</h3>
                        <p class="text-teal-50 text-sm">Upload laporan kinerja untuk periode tahun berjalan.</p>
                    </div>
                    <button onclick="openModal('add')" class="relative z-10 bg-white text-teal-700 hover:bg-teal-50 px-6 py-3 rounded-xl shadow-md transition-all transform hover:scale-105 flex items-center gap-2 font-bold text-sm group-btn">
                        <div class="bg-teal-100 rounded-full p-1 group-hover:bg-teal-200 transition-colors">
                            <i class="fas fa-plus text-xs"></i>
                        </div>
                        Buat Laporan
                    </button>
                </div>

                <!-- Tabel Data -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 bg-white flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h3 class="font-bold text-slate-800 text-lg">Riwayat Laporan Kinerja</h3>
                            <p class="text-slate-500 text-xs">Arsip pelaporan kinerja lembaga</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-slate-600">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-50/50 border-b border-slate-100">
                                <tr>
                                    <th class="px-6 py-4 font-semibold tracking-wider">Tanggal Upload</th>
                                    <th class="px-6 py-4 font-semibold tracking-wider">Judul / Tahun</th>
                                    <th class="px-6 py-4 font-semibold tracking-wider">File Laporan</th>
                                    <th class="px-6 py-4 font-semibold tracking-wider text-center">Status</th>
                                    <th class="px-6 py-4 font-semibold tracking-wider text-center">Feedback Admin</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($submissions as $item)
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
                                            <span class="font-bold text-slate-800 text-sm group-hover:text-teal-600 transition-colors">
                                                {{ $item->title }}
                                            </span>
                                            <span class="text-xs text-slate-400">Laporan Tahunan</span>
                                        </div>
                                    </td>

                                    <!-- File -->
                                    <td class="px-6 py-4">
                                        <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank" class="inline-flex items-center gap-2 bg-white border border-slate-200 text-slate-600 hover:text-teal-600 hover:border-teal-300 px-3 py-2 rounded-lg transition-all shadow-sm group/file">
                                            <div class="bg-rose-50 text-rose-500 p-1 rounded group-hover/file:bg-rose-100 transition-colors">
                                                <i class="far fa-file-pdf"></i>
                                            </div>
                                            <span class="font-medium text-xs">Buka PDF</span>
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

                                    <!-- Tindak Lanjut / Feedback -->
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-2 items-center">
                                            
                                            <!-- JIKA ADA CATATAN -->
                                            @if($item->admin_note)
                                                <div class="text-[10px] bg-slate-50 p-2 rounded border border-slate-200 text-slate-600 mb-1 w-full max-w-[200px]">
                                                    <span class="font-bold text-slate-700 block mb-0.5"><i class="fas fa-comment-alt mr-1 text-teal-500"></i> Admin:</span> 
                                                    "{{ $item->admin_note }}"
                                                </div>
                                            @endif

                                            <!-- JIKA ADA FILE BALASAN -->
                                            @if($item->admin_file)
                                                <a href="{{ asset('storage/' . $item->admin_file) }}" target="_blank" class="w-full bg-blue-50 border border-blue-100 text-blue-700 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-blue-100 transition-all text-center">
                                                    <i class="fas fa-download mr-1"></i> Surat Balasan
                                                </a>
                                            @endif

                                            <!-- JIKA PERLU REVISI -->
                                            @if($item->status == 'rejected')
                                                <button onclick="openModal('edit', {{ $item->id }}, '{{ $item->title }}')" class="w-full bg-rose-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-rose-700 shadow-sm transition-all flex items-center justify-center gap-1.5 active:scale-95">
                                                    <i class="fas fa-upload"></i> Upload Revisi
                                                </button>
                                            @elseif($item->status == 'approved' && !$item->admin_file)
                                                <div class="text-center text-xs text-emerald-600 font-medium">
                                                    <i class="fas fa-check-double"></i> Selesai
                                                </div>
                                            @elseif($item->status == 'pending')
                                                <span class="text-[10px] text-slate-400 italic mb-1">Menunggu respon...</span>
                                                <!-- TOMBOL HAPUS BARU (DENGAN MODAL) -->
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
                                                <i class="fas fa-file-invoice text-4xl text-slate-300"></i>
                                            </div>
                                            <h3 class="text-slate-800 font-bold text-lg">Belum ada Laporan</h3>
                                            <p class="text-slate-500 text-sm mt-1 max-w-xs mx-auto">Mulai ajukan Laporan Kinerja Tahunan Anda.</p>
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
                    
                    <!-- Header Modal Emerald -->
                    <div class="bg-gradient-to-r from-teal-600 to-emerald-600 px-6 py-4 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2" id="modalTitle">
                            Upload Laporan
                        </h3>
                        <button onclick="closeModal()" class="text-teal-100 hover:text-white transition-colors bg-teal-800/20 hover:bg-teal-800/40 rounded-lg p-1">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>

                    <form id="submissionForm" method="POST" enctype="multipart/form-data" action="{{ route('lapkin.store') }}">
                        @csrf
                        <div id="methodField"></div> 

                        <!-- HIDDEN FIELDS -->
                        <input type="hidden" name="periode" value="Tahunan">

                        <div class="px-6 py-5 space-y-4">
                            <!-- Informasi Jenis (Static) -->
                            <div class="bg-teal-50 border border-teal-100 rounded-lg p-3 flex items-start gap-3">
                                <i class="fas fa-info-circle text-teal-500 mt-0.5"></i>
                                <div>
                                    <p class="text-xs font-bold text-teal-800">Jenis Dokumen</p>
                                    <p class="text-xs text-teal-600">Laporan Kinerja Tahunan</p>
                                </div>
                            </div>

                            <!-- Input Tahun -->
                            <div id="newReportFields">
                                <div class="space-y-1">
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Tahun Laporan</label>
                                    <input type="number" name="tahun" id="inputTahun" value="{{ date('Y') }}" class="block w-full rounded-xl border-slate-300 bg-slate-50 p-2.5 text-sm shadow-sm focus:border-teal-500 focus:ring-teal-500 placeholder:text-slate-400" required>
                                    <p class="text-[10px] text-slate-400 mt-1 italic">*Judul otomatis: "Laporan Kinerja Tahunan [Tahun]"</p>
                                </div>
                            </div>

                            <!-- Info Judul (Untuk Edit/Revisi) -->
                            <div id="revisionInfo" class="hidden space-y-1">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Revisi Untuk</label>
                                <input type="text" id="readOnlyTitle" class="block w-full rounded-xl border-slate-200 bg-slate-100 p-2.5 text-sm text-slate-500 font-bold" readonly>
                            </div>

                            <!-- File Upload Area -->
                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide">File PDF (Max 2MB)</label>
                                <div class="mt-1 flex justify-center rounded-xl border-2 border-dashed border-slate-300 px-6 py-8 hover:bg-slate-50 hover:border-teal-400 transition-colors group relative cursor-pointer">
                                    <div class="text-center">
                                        <div class="mx-auto h-12 w-12 text-slate-300 group-hover:text-teal-500 transition-colors">
                                            <i class="fas fa-cloud-upload-alt text-3xl"></i>
                                        </div>
                                        <div class="mt-2 flex text-sm leading-6 text-slate-600 justify-center">
                                            <label for="inputFile" class="relative cursor-pointer rounded-md bg-transparent font-bold text-teal-600 hover:text-teal-500">
                                                <span>Pilih file</span>
                                                <input id="inputFile" name="file_upload" type="file" class="sr-only" accept=".pdf" onchange="showFileName(this)">
                                            </label>
                                            <p class="pl-1">atau drag and drop</p>
                                        </div>
                                        <p class="text-xs leading-5 text-slate-500">PDF hingga 2MB</p>
                                        <p id="fileNameDisplay" class="mt-2 text-sm font-bold text-slate-800 hidden"></p>
                                    </div>
                                </div>
                                <p class="text-[10px] text-amber-600 mt-2 hidden flex items-center gap-1 bg-amber-50 p-2 rounded" id="fileNote">
                                    <i class="fas fa-info-circle"></i> Kosongkan jika tidak ingin mengganti file.
                                </p>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="bg-slate-50 px-6 py-4 flex flex-col gap-2 border-t border-slate-100 sm:flex-row-reverse">
                            <button type="submit" class="inline-flex w-full sm:w-auto justify-center items-center rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white shadow-md hover:bg-emerald-700 active:scale-95 transition-all gap-2">
                                <i class="fas fa-save text-xs"></i> Simpan Laporan                            </button>
                            <button type="button" onclick="closeModal()" class="inline-flex w-full sm:w-auto justify-center items-center rounded-xl bg-white px-5 py-2.5 text-sm font-bold text-slate-500 border border-slate-200 hover:bg-slate-100 transition-all">
                                Batal
                            </button>
                        </div>
                    </form>
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

        // === NOTIFICATION LOGIC ===
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
                setTimeout(() => {
                    progressBar.style.transition = 'width 3s linear';
                    progressBar.style.width = '0%';
                }, 100);

                // Close after 3.1 seconds
                setTimeout(() => {
                    closeNotification('successModal');
                }, 3100);
            }
        });

        // === FORM LOGIC ===
        function showFileName(input) {
            const display = document.getElementById('fileNameDisplay');
            if (input.files && input.files[0]) {
                display.textContent = "File terpilih: " + input.files[0].name;
                display.classList.remove('hidden');
                display.classList.add('text-teal-600');
            } else {
                display.classList.add('hidden');
            }
        }

        // Modal Logic
        function openModal(mode, id = null, title = '') {
            const modal = document.getElementById('submissionModal');
            const form = document.getElementById('submissionForm');
            const modalTitle = document.getElementById('modalTitle');
            const methodField = document.getElementById('methodField');
            
            const newReportFields = document.getElementById('newReportFields');
            const revisionInfo = document.getElementById('revisionInfo');
            const readOnlyTitle = document.getElementById('readOnlyTitle');
            
            const inputFile = document.getElementById('inputFile');
            const fileNote = document.getElementById('fileNote');
            const fileNameDisplay = document.getElementById('fileNameDisplay');

            modal.classList.remove('hidden');
            fileNameDisplay.classList.add('hidden'); 
            inputFile.value = ''; 

            if (mode === 'add') {
                modalTitle.innerHTML = '<i class="fas fa-plus-circle"></i> Buat Laporan Baru';
                form.action = "{{ route('lapkin.store') }}";
                methodField.innerHTML = ''; 
                
                newReportFields.classList.remove('hidden');
                revisionInfo.classList.add('hidden');
                inputFile.required = true; 
                fileNote.classList.add('hidden');
            } else {
                modalTitle.innerHTML = '<i class="fas fa-edit"></i> Revisi Laporan';
                form.action = "{{ url('/submission/update') }}/" + id;
                methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
                
                newReportFields.classList.add('hidden');
                revisionInfo.classList.remove('hidden');
                readOnlyTitle.value = title;
                
                inputFile.required = false; 
                fileNote.classList.remove('hidden');
            }
        }
        
        function closeModal() {
            document.getElementById('submissionModal').classList.add('hidden');
        }
    </script>
</body>
</html>