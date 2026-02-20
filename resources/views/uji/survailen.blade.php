<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survailen & Audit | SI-MUTU</title>
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
        
        .glass-overlay {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <!-- 1. LOGIKA FETCH DATA KHUSUS SURVAILEN -->
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

        // Logic Statistik
        $needAction = $survailens->filter(function($item) {
            return empty($item->file_path) || $item->status == 'rejected';
        })->count();

        $waitingVerification = $survailens->where('status', 'pending')->whereNotNull('file_path')->count();
        $completed = $survailens->where('status', 'approved')->count();
    @endphp
    <div class="flex h-screen overflow-hidden bg-slate-50">
        
        <!-- === MOBILE OVERLAY === -->
        <div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 z-40 hidden lg:hidden glass-overlay transition-opacity duration-300"></div>

        <!-- === SIDEBAR WRAPPER (Responsive) === -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-2xl lg:shadow-none transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto flex flex-col h-full border-r border-slate-200">
            @include('components.uji-sidebar')
        </aside>

        <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
            
            <!-- === MOBILE HEADER BAR === -->
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

            <!-- Header Desktop -->
            <div class="hidden lg:block">
                @include('components.uji-header', [
                    'title' => 'Status Mutu & Survailen',
                    'subtitle' => 'Tindak lanjut hasil audit dan pengawasan'
                ])
            </div>
            
            <!-- Header Mobile Title -->
            <div class="lg:hidden px-4 pt-4 pb-2">
                <h1 class="text-xl font-bold text-slate-800">Status Mutu & Survailen</h1>
                <p class="text-xs text-slate-500">Tindak lanjut hasil audit</p>
            </div>

            <!-- MAIN CONTENT -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-8 relative space-y-6">

                <!-- SUCCESS POP-UP -->
                @if (session('success'))
                <div id="successModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 backdrop-blur-[3px] transition-all duration-300">
                    <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-sm w-full mx-4 text-center animate-pop-in relative overflow-hidden border border-white/50">
                        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-amber-400 to-orange-500"></div>
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-amber-50 rounded-full blur-2xl opacity-60 pointer-events-none"></div>
                        <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-orange-50 rounded-full blur-2xl opacity-60 pointer-events-none"></div>
                        
                        <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow-inner relative z-10">
                            <i class="fas fa-check text-4xl text-amber-600 drop-shadow-sm"></i>
                        </div>
                        
                        <h3 class="text-2xl font-bold text-slate-800 mb-2 relative z-10">Respon Terkirim!</h3>
                        <p class="text-slate-600 mb-6 text-sm leading-relaxed relative z-10 font-medium">
                            {{ session('success') }}
                        </p>
                        
                        <div class="relative z-10 w-full bg-slate-100 h-1.5 rounded-full mb-5 overflow-hidden">
                            <div id="progressBar" class="h-full bg-amber-500 rounded-full" style="width: 100%"></div>
                        </div>
                        
                        <button onclick="closeNotification('successModal')" class="relative z-10 w-full bg-white border-2 border-slate-100 hover:border-amber-400 hover:bg-amber-50 text-slate-500 hover:text-amber-700 font-bold py-3 rounded-xl transition-all duration-300 transform active:scale-95 shadow-sm hover:shadow-md group">
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

                <!-- CANCEL POP-UP (Untuk Tombol Batal) -->
                <div id="cancelModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-slate-900/50 backdrop-blur-[2px] transition-all duration-300">
                    <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-sm w-full mx-4 text-center animate-pop-in relative overflow-hidden border border-white/50">
                        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-orange-400 to-red-500"></div>
                        <div class="w-20 h-20 bg-orange-50 border-4 border-orange-100 rounded-full flex items-center justify-center mx-auto mb-5 relative z-10">
                            <i class="fas fa-question text-4xl text-orange-500 drop-shadow-sm"></i>
                        </div>
                        
                        <h3 class="text-xl font-bold text-slate-800 mb-2 relative z-10">Batalkan Tindak Lanjut?</h3>
                        <p class="text-slate-500 mb-8 text-sm leading-relaxed relative z-10 font-medium">
                            Anda yakin ingin membatalkan atau menghapus data ini? Tindakan ini tidak dapat dibatalkan.
                        </p>
                        
                        <div class="flex flex-col gap-3 relative z-10">
                            <form id="cancelForm" action="" method="POST" class="w-full">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-bold py-3 rounded-xl transition-all shadow-lg shadow-rose-200 transform active:scale-95 flex items-center justify-center gap-2">
                                    <i class="fas fa-trash-alt"></i> Ya, Hapus
                                </button>
                            </form>
                            <button onclick="closeCancelModal()" class="w-full bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 font-bold py-3 rounded-xl transition-all">
                                Tidak, Kembali
                            </button>
                        </div>
                    </div>
                </div>

                <!-- STATISTIK SECTION -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                    <!-- Perlu Respon -->
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 group">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider group-hover:text-rose-600 transition-colors">Perlu Respon</p>
                                <h3 class="text-3xl font-bold text-slate-800 mt-2">{{ $needAction }}</h3>
                            </div>
                            <div class="p-3 bg-rose-50 rounded-xl text-rose-500 group-hover:scale-110 transition-transform">
                                <i class="fas fa-exclamation-circle text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-xs text-slate-400">
                            <span class="text-rose-600 font-medium bg-rose-50 px-1.5 py-0.5 rounded mr-2">Action</span> Dokumen Audit Baru/Revisi
                        </div>
                    </div>

                    <!-- Menunggu -->
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 group">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider group-hover:text-amber-600 transition-colors">Menunggu Admin</p>
                                <h3 class="text-3xl font-bold text-slate-800 mt-2">{{ $waitingVerification }}</h3>
                            </div>
                            <div class="p-3 bg-amber-50 rounded-xl text-amber-500 group-hover:scale-110 transition-transform">
                                <i class="fas fa-clock text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-xs text-slate-400">
                            <span class="text-amber-600 font-medium bg-amber-50 px-1.5 py-0.5 rounded mr-2">Proses</span> Verifikasi Tindak Lanjut
                        </div>
                    </div>

                    <!-- Selesai -->
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 group">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider group-hover:text-emerald-600 transition-colors">Selesai</p>
                                <h3 class="text-3xl font-bold text-slate-800 mt-2">{{ $completed }}</h3>
                            </div>
                            <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600 group-hover:scale-110 transition-transform">
                                <i class="fas fa-check-double text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-xs text-slate-400">
                            <span class="text-emerald-600 font-medium bg-emerald-50 px-1.5 py-0.5 rounded mr-2">Sukses</span> Kasus Ditutup
                        </div>
                    </div>
                </div>

                <!-- TABEL DATA -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 bg-white">
                        <h3 class="font-bold text-slate-800 text-lg">Daftar Dokumen Masuk</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-slate-600 min-w-[900px] md:min-w-0">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-50/50 border-b border-slate-100">
                                <tr>
                                    <th class="px-6 py-4 font-semibold tracking-wider w-40">Tanggal Masuk</th>
                                    <th class="px-6 py-4 font-semibold tracking-wider">Perihal / Judul</th>
                                    <th class="px-6 py-4 font-semibold tracking-wider">Dokumen Admin</th>
                                    <th class="px-6 py-4 font-semibold tracking-wider">Respon Anda</th>
                                    <th class="px-6 py-4 font-semibold tracking-wider text-center">Status</th>
                                    <th class="px-6 py-4 font-semibold tracking-wider text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($survailens as $item)
                                <tr class="bg-white hover:bg-slate-50 transition-colors cursor-pointer group" 
                                    onclick='openHistoryModal(@json($item->files ?? []), "{{ $item->status }}", "{{ $item->title }}")'>
                                    
                                    <!-- Tanggal Admin Kirim -->
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-slate-700">{{ $item->created_at->format('d M Y') }}</span>
                                            <span class="text-[11px] text-slate-400">{{ $item->created_at->format('H:i') }} WIB</span>
                                        </div>
                                    </td>

                                    <!-- Judul -->
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-slate-800 group-hover:text-amber-600 transition-colors text-sm line-clamp-1">{{ $item->title }}</span>
                                            <span class="text-[10px] text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded w-fit mt-1 border border-amber-100 font-bold">
                                                SURVAILEN
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Dokumen DARI Admin -->
                                    <td class="px-6 py-5 text-left">
                                        @php
                                            $adminFile = $item->admin_file ?: ($item->files->where('version', 0)->first()->admin_file ?? null);
                                        @endphp
                                        @if($adminFile)
                                            <a href="{{ asset('storage/' . $adminFile) }}" target="_blank" onclick="event.stopPropagation()" class="inline-flex items-center gap-2 bg-white hover:bg-red-50 text-slate-700 border border-slate-200 hover:border-red-200 px-3 py-2 rounded-xl transition-all group/file shadow-sm">
                                                <i class="fas fa-file-pdf text-red-500 group-hover/file:scale-110 transition-transform"></i>
                                                <div class="flex flex-col text-left">
                                                    <span class="text-[9px] uppercase font-bold text-slate-400 leading-none">Download</span>
                                                    <span class="text-xs font-bold">Surat Admin</span>
                                                </div>
                                            </a>
                                        @else
                                            <span class="text-xs text-slate-400 italic bg-slate-100 px-2 py-1 rounded font-medium">Tanpa Lampiran</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-5 text-left">
                                        @if($item->file_path)
                                            <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank" onclick="event.stopPropagation()" class="text-blue-600 hover:text-blue-800 hover:underline text-xs flex items-center gap-2 font-bold w-fit bg-blue-50/50 p-2 rounded-lg border border-blue-100/50">
                                                <i class="fas fa-paperclip"></i> Bukti Upload
                                            </a>
                                        @else
                                            <div class="flex items-center gap-2 text-rose-500 font-bold bg-rose-50 px-3 py-1.5 rounded-lg border border-rose-100 w-fit">
                                                <span class="animate-pulse flex h-2 w-2 rounded-full bg-rose-500"></span>
                                                <span class="text-[10px]">BELUM RESPON</span>
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-5 text-center">
                                        @if(!$item->file_path)
                                            <span class="inline-block px-2.5 py-1 bg-slate-100 text-slate-500 text-[10px] font-bold rounded-full uppercase tracking-wider border border-slate-200">Menunggu Anda</span>
                                        @elseif($item->status == 'pending')
                                            <span class="inline-block px-2.5 py-1 bg-amber-100 text-amber-600 text-[10px] font-bold rounded-full uppercase tracking-wider border border-amber-200">Verifikasi</span>
                                        @elseif($item->status == 'approved')
                                            <span class="inline-block px-2.5 py-1 bg-emerald-100 text-emerald-600 text-[10px] font-bold rounded-full uppercase tracking-wider border border-emerald-200">Selesai</span>
                                        @elseif($item->status == 'rejected')
                                            <span class="inline-block px-2.5 py-1 bg-rose-100 text-rose-600 text-[10px] font-bold rounded-full uppercase tracking-wider border border-rose-200">Ditolak</span>
                                        @endif
                                    </td>

                                    <!-- Aksi User -->
                                    <td class="px-6 py-5 text-center">
                                        @if(!$item->file_path || $item->status == 'rejected')
                                            <button onclick="event.stopPropagation(); openUploadModal({{ $item->id }}, '{{ $item->title }}')" class="w-full bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-md shadow-amber-200 hover:shadow-lg transition-all active:scale-95 flex items-center justify-center gap-1.5 whitespace-nowrap">
                                                <i class="fas fa-upload"></i> {{ $item->status == 'rejected' ? 'Revisi' : 'Tindak Lanjut' }}
                                            </button>
                                        @elseif($item->status == 'approved')
                                            <span class="text-emerald-500 text-lg drop-shadow-sm"><i class="fas fa-check-circle"></i></span>
                                        @else
                                            <button onclick="event.stopPropagation(); openUploadModal({{ $item->id }}, '{{ $item->title }}')" class="text-slate-400 hover:text-amber-600 text-xs font-medium flex items-center justify-center gap-1 mx-auto hover:bg-slate-100 px-2 py-1 rounded transition-colors">
                                                <i class="fas fa-edit mr-1"></i> Edit
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-3 text-slate-300">
                                                <i class="fas fa-clipboard-list text-3xl"></i>
                                            </div>
                                            <p class="font-medium text-slate-500">Tidak ada dokumen survailen masuk.</p>
                                            <p class="text-xs text-slate-400">Jika ada audit, Admin akan mengirimkannya ke sini.</p>
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

    <!-- MODAL UPLOAD RESPON -->
    <div id="uploadModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100 animate-pop-in">
                    
                    <div class="bg-gradient-to-r from-amber-500 to-orange-600 px-6 py-4 flex justify-between items-center text-white">
                        <h3 class="text-lg font-bold flex items-center gap-2">
                            <i class="fas fa-reply"></i> Respon Tindak Lanjut
                        </h3>
                        <button onclick="closeModal()" class="text-amber-100 hover:text-white bg-white/10 hover:bg-white/20 p-2 rounded-lg transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form id="responseForm" method="POST" enctype="multipart/form-data" action="">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="force_status" value="pending">

                        <div class="px-6 py-6 space-y-5">
                            
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Menanggapi Dokumen</label>
                                <input type="text" id="docTitle" class="block w-full rounded-xl border-slate-200 bg-slate-50 p-3 text-sm font-bold text-slate-600" readonly>
                            </div>

                            <div class="space-y-2 bg-slate-50 p-4 rounded-xl border border-slate-200">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide flex justify-between">
                                    <span>Upload Bukti Tindak Lanjut (PDF)</span>
                                    <span class="text-[10px] bg-amber-100 text-amber-700 px-1.5 py-0.5 rounded">Wajib</span>
                                </label>
                                <input id="inputFile" name="file_upload" type="file" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-amber-100 file:text-amber-700 hover:file:bg-amber-200 border border-slate-300 rounded-lg cursor-pointer bg-white" accept=".pdf" onchange="showFileName(this)" required>
                                <p id="fileNameDisplay" class="mt-2 text-sm font-bold text-amber-600 hidden"></p>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Catatan Tambahan (Opsional)</label>
                                <textarea name="user_note" rows="2" class="block w-full rounded-xl border-slate-300 bg-white p-3 text-sm focus:border-amber-500 focus:ring-amber-500" placeholder="Keterangan singkat mengenai tindak lanjut..."></textarea>
                            </div>
                        </div>

                        <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-slate-100">
                            <button type="submit" class="inline-flex w-full justify-center rounded-xl bg-amber-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-amber-200 hover:shadow-none hover:bg-amber-700 transition-all active:scale-95">
                                Kirim Respon
                            </button>
                            <button type="button" onclick="closeModal()" class="inline-flex w-full justify-center rounded-xl bg-white px-5 py-2.5 text-sm font-bold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 transition-all">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL HISTORY (USER SIDE - PRO TIMELINE) -->
    <div id="historyModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeHistoryModal()"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-3xl border border-slate-100 animate-pop-in">
                    
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-slate-800 to-slate-900 px-6 py-5 flex justify-between items-center text-white shadow-md relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-5 rounded-full -mr-10 -mt-10 pointer-events-none"></div>
                        <div>
                            <h3 class="text-xl font-bold flex items-center gap-3">
                                <div class="bg-white/10 p-2 rounded-lg backdrop-blur-sm"><i class="fas fa-history text-lg"></i></div>
                                Perjalanan Audit
                            </h3>
                            <p class="text-xs text-slate-300 mt-1 opacity-90" id="historyTitle">Detail riwayat dan tanggapan admin</p>
                        </div>
                        <button onclick="closeHistoryModal()" class="text-slate-400 hover:text-white bg-white/10 hover:bg-white/20 rounded-xl p-2 transition-all active:scale-95 z-10">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>

                    <!-- Timeline Body -->
                    <div class="max-h-[65vh] overflow-y-auto bg-slate-50">
                        <div id="timelineContainer" class="px-6 py-8 relative">
                            <!-- Timeline items will be injected here -->
                        </div>
                    </div>

                    <div class="bg-white px-6 py-4 flex justify-end border-t border-slate-200 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] relative z-20">
                         <button onclick="closeHistoryModal()" class="px-6 py-2.5 bg-white border border-slate-300 text-slate-700 text-sm rounded-xl hover:bg-slate-50 font-bold shadow-sm transition-colors">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // === SIDEBAR TOGGLE ===
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

        // === NOTIFICATIONS & MODALS ===
        function closeNotification(modalId) {
            const modal = document.getElementById(modalId);
            if(modal) {
                modal.classList.add('opacity-0');
                modal.querySelector('div').classList.add('scale-95');
                setTimeout(() => {
                    modal.style.display = 'none';
                }, 300);
            }
        }

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

        // === UPLOAD FORM LOGIC ===
        function showFileName(input) {
            const display = document.getElementById('fileNameDisplay');
            if (input.files && input.files[0]) {
                display.textContent = "File: " + input.files[0].name;
                display.classList.remove('hidden');
            } else {
                display.classList.add('hidden');
            }
        }

        function openUploadModal(id, title) {
            const modal = document.getElementById('uploadModal');
            const form = document.getElementById('responseForm');
            const titleField = document.getElementById('docTitle');
            
            form.action = "{{ url('/submission/update') }}/" + id;
            titleField.value = title;
            
            document.getElementById('inputFile').value = '';
            document.getElementById('fileNameDisplay').classList.add('hidden');

            modal.classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('uploadModal').classList.add('hidden');
        }

        // === HISTORY TIMELINE LOGIC ===
        function openHistoryModal(files, currentStatus, docTitle) {
            const container = document.getElementById('timelineContainer');
            document.getElementById('historyTitle').innerText = "Riwayat: " + docTitle;
            container.innerHTML = ''; 
            
            if(!files || files.length === 0) {
                container.innerHTML = `
                    <div class="flex flex-col items-center justify-center py-10 text-slate-400">
                        <i class="far fa-folder-open text-4xl mb-3 opacity-50"></i>
                        <p class="text-sm">Belum ada riwayat tercatat.</p>
                    </div>`;
            } else {
                // 1. Sort ascending (Oldest to Newest)
                files.sort((a, b) => a.version - b.version);
                
                // 2. FILTER & MERGE Logic (To show only relevant milestones)
                let cleanFiles = [];
                files.forEach((file) => {
                    if (cleanFiles.length > 0) {
                        let lastClean = cleanFiles[cleanFiles.length - 1];
                        // If same file, just update admin notes/files (merge effect)
                        if (file.file_path === lastClean.file_path && file.file_name === lastClean.file_name) {
                            if (file.admin_note) lastClean.admin_note = file.admin_note;
                            if (file.admin_file) lastClean.admin_file = file.admin_file;
                            return;
                        }
                    }
                    cleanFiles.push({...file}); 
                });

                // 3. Render Timeline Items
                cleanFiles.forEach((file, index) => {
                    const d = new Date(file.created_at);
                    const dateStr = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
                    
                    let isLatest = (index === cleanFiles.length - 1);
                    let isStart = (file.version == 0);

                    // --- CONTENT BUILDER ---
                    let versionLabel, colorClass, actionTitle, userFileHTML, adminFeedbackHTML;

                    // 1. Setup Versi & Icon
                    if (isStart) {
                        versionLabel = '<i class="fas fa-flag"></i>';
                        colorClass = 'bg-amber-100 text-amber-600 ring-4 ring-amber-50';
                        actionTitle = "Inisiasi Audit (Admin)";
                        
                        // File Admin di Versi Awal
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
                        adminFeedbackHTML = ''; // Tidak ada feedback karena ini start

                    } else {
                        versionLabel = `v${file.version}`;
                        colorClass = isLatest ? 'bg-blue-600 text-white ring-4 ring-blue-100 shadow-md' : 'bg-white border-2 border-slate-200 text-slate-500';
                        actionTitle = "Tindak Lanjut Lembaga";

                        // File User
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

                        // Admin Feedback Logic
                        let badgeHTML = '';
                        if (isLatest) {
                            if (currentStatus === 'approved') badgeHTML = `<span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border border-emerald-200">SELESAI</span>`;
                            else if (currentStatus === 'rejected') badgeHTML = `<span class="bg-rose-100 text-rose-700 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border border-rose-200">MINTA REVISI</span>`;
                        } else if (file.admin_note || file.admin_file) {
                            badgeHTML = `<span class="bg-slate-200 text-slate-600 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">Revisi Sebelumnya</span>`;
                        }

                        if (badgeHTML || file.admin_note || file.admin_file) {
                            adminFeedbackHTML = `
                                <div class="mt-4 pt-3 border-t border-slate-100 relative">
                                    <div class="absolute -top-2 left-4 bg-slate-50 px-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Evaluasi Admin</div>
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-xs font-bold text-slate-700">Tanggapan</span>
                                        ${badgeHTML}
                                    </div>
                                    ${file.admin_note ? `<div class="bg-yellow-50/50 border border-yellow-100 rounded-lg p-3 text-xs text-slate-700 mb-2"><i class="fas fa-comment-alt text-yellow-500 mr-1.5"></i> "${file.admin_note}"</div>` : ''}
                                    ${file.admin_file ? `<a href="/storage/${file.admin_file}" target="_blank" class="flex items-center gap-2 text-xs font-bold text-blue-600 bg-blue-50/50 hover:bg-blue-50 p-2 rounded-lg transition-colors border border-blue-100/50"><i class="fas fa-paperclip"></i> Lampiran Balasan Admin</a>` : ''}
                                </div>
                            `;
                        } else {
                            adminFeedbackHTML = `<div class="mt-4 pt-2 border-t border-slate-100 text-center"><span class="text-[10px] text-slate-400 italic">Menunggu evaluasi admin...</span></div>`;
                        }
                    }

                    // --- RENDER HTML ITEM ---
                    const itemHTML = `
                        <div class="relative flex gap-6 pb-8 last:pb-0">
                            <!-- Vertical Line -->
                            <div class="absolute top-0 left-4 -bottom-8 w-0.5 bg-slate-200 last:hidden"></div>
                            
                            <!-- Icon/Badge -->
                            <div class="relative z-10 flex-shrink-0 w-8 h-8 rounded-full ${colorClass} flex items-center justify-center border-2 border-white shadow-sm">
                                <span class="text-[10px] font-bold">${versionLabel}</span>
                            </div>

                            <!-- Content Card -->
                            <div class="flex-1 bg-white rounded-xl p-4 border border-slate-200 shadow-sm relative hover:shadow-md transition-all duration-300 group/card">
                                <!-- Date Header -->
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-xs font-bold text-slate-700 flex items-center gap-2">
                                        ${actionTitle}
                                        ${isLatest ? '<span class="flex h-2 w-2 relative"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span></span>' : ''}
                                    </span> 
                                    <span class="text-[10px] text-slate-400 bg-slate-50 px-2 py-1 rounded-full border border-slate-100 font-mono">${dateStr}</span>
                                </div>

                                <!-- User File Section -->
                                ${userFileHTML}

                                <!-- Admin Feedback Section -->
                                ${adminFeedbackHTML || ''}
                            </div>
                        </div>
                    `;
                    
                    container.innerHTML += itemHTML;
                });
            }
            document.getElementById('historyModal').classList.remove('hidden');
        }

        function closeHistoryModal() {
            document.getElementById('historyModal').classList.add('hidden');
        }
    </script>
</body>
</html>