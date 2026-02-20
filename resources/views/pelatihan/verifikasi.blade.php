<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Verifikasi | SI-MUTU</title>
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

    @php
        use App\Models\Submission;
        use Illuminate\Support\Facades\Auth;
        
        if (!isset($verifikasis)) {
            $verifikasis = Submission::where('user_id', Auth::id())
                               ->where('type', 'Verifikasi')
                               ->orderBy('created_at', 'desc')
                               ->get();
        }

        // Logic Statistik
        $needAction = $verifikasis->filter(function($item) {
            return empty($item->user_note) && $item->status != 'approved';
        })->count();

        $waitingVerification = $verifikasis->filter(function($item) {
            return !empty($item->user_note) && $item->status != 'approved';
        })->count();

        $completed = $verifikasis->where('status', 'approved')->count();
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
                    <button onclick="toggleSidebar()" class="p-2 text-slate-600 hover:text-purple-600 hover:bg-slate-100 rounded-lg transition-colors">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <span class="font-bold text-slate-800 text-sm tracking-wide">SI-MUTU <span class="text-purple-600">DKKN</span></span>
                </div>
                <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 text-xs font-bold border border-purple-200">
                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                </div>
            </div>

            <!-- Header Desktop -->
            <div class="hidden lg:block">
                @include('components.pelatihan-header', [
                    'title' => 'Status Verifikasi',
                    'subtitle' => 'Penerbitan dokumen hasil verifikasi dan akreditasi'
                ])
            </div>
            
            <!-- Header Mobile Title -->
            <div class="lg:hidden px-4 pt-4 pb-2">
                <h1 class="text-xl font-bold text-slate-800">Status Verifikasi</h1>
                <p class="text-xs text-slate-500">Penerbitan dokumen hasil verifikasi</p>
            </div>

            <!-- MAIN CONTENT -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-8 relative space-y-6">
                
                <!-- SUCCESS POP-UP (SESUAI GAMBAR REFERENSI 1) -->
                @if (session('success'))
                <div id="successModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-[2px] transition-all duration-300">
                    <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-sm w-full mx-4 text-center animate-pop-in relative overflow-hidden">
                        
                        <!-- Icon Check Hijau -->
                        <div class="w-20 h-20 bg-emerald-100/50 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm relative z-10 animate-[bounce_1s_infinite]">
                            <i class="fas fa-check text-4xl text-emerald-500"></i>
                        </div>
                        
                        <!-- Judul & Pesan -->
                        <h3 class="text-2xl font-bold text-slate-800 mb-2 relative z-10 tracking-tight">Berhasil!</h3>
                        <p class="text-slate-500 mb-8 text-sm font-medium relative z-10">
                            {{ session('success') }}
                        </p>
                        
                        <!-- Progress Bar Tipis -->
                        <div class="absolute bottom-0 left-0 h-1 bg-emerald-500 transition-all duration-[3000ms] ease-linear w-full" id="progressBar"></div>
                        
                        <!-- Tombol Hijau Full -->
                        <button onclick="closeNotification('successModal')" class="relative z-10 w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 rounded-xl transition-all shadow-lg hover:shadow-emerald-200 transform active:scale-95 text-sm">
                            OK, Lanjutkan
                        </button>
                    </div>
                </div>
                @endif

                <!-- ERROR POP-UP -->
                @if ($errors->any() || session('error'))
                <div id="errorModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-[2px] transition-all duration-300">
                    <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-sm w-full mx-4 text-center animate-pop-in relative overflow-hidden">
                        <div class="w-20 h-20 bg-rose-100/50 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm relative z-10">
                            <i class="fas fa-times text-4xl text-rose-500"></i>
                        </div>
                        
                        <h3 class="text-2xl font-bold text-slate-800 mb-2 relative z-10 tracking-tight">Gagal!</h3>
                        <div class="text-slate-500 mb-8 text-sm font-medium relative z-10">
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
                        
                        <button onclick="closeNotification('errorModal')" class="relative z-10 w-full bg-slate-800 hover:bg-slate-900 text-white font-bold py-3.5 rounded-xl transition-all shadow-lg transform active:scale-95 text-sm">
                            Coba Lagi
                        </button>
                    </div>
                </div>
                @endif

                <!-- STATISTIK SECTION (Warna Purple) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                    <!-- Perlu Respon -->
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow duration-300 group">
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
                            <span class="text-rose-600 font-medium bg-rose-50 px-1.5 py-0.5 rounded mr-2">Action</span> Segera Konfirmasi
                        </div>
                    </div>

                    <!-- Menunggu -->
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow duration-300 group">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider group-hover:text-blue-600 transition-colors">Menunggu Admin</p>
                                <h3 class="text-3xl font-bold text-slate-800 mt-2">{{ $waitingVerification }}</h3>
                            </div>
                            <div class="p-3 bg-blue-50 rounded-xl text-blue-500 group-hover:scale-110 transition-transform">
                                <i class="fas fa-clock text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-xs text-slate-400">
                            <span class="text-blue-600 font-medium bg-blue-50 px-1.5 py-0.5 rounded mr-2">Proses</span> Verifikasi Lanjut
                        </div>
                    </div>

                    <!-- Selesai -->
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow duration-300 group">
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
                            <span class="text-emerald-600 font-medium bg-emerald-50 px-1.5 py-0.5 rounded mr-2">Sukses</span> Dokumen Valid
                        </div>
                    </div>
                </div>

                <!-- Call to Action Info (SESUAI GAMBAR REFERENSI 2) -->
                <div class="bg-gradient-to-br from-purple-600 to-blue-600 rounded-3xl p-8 mb-8 text-white shadow-xl shadow-purple-200/50 flex flex-col items-center justify-center relative overflow-hidden text-center group">
                    <!-- Background Decoration -->
                    <div class="absolute top-[-50%] left-[-10%] w-[300px] h-[300px] bg-white opacity-10 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute bottom-[-50%] right-[-10%] w-[300px] h-[300px] bg-indigo-400 opacity-20 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="relative z-10 max-w-2xl mx-auto">
                        <h3 class="font-extrabold text-2xl md:text-3xl mb-3 tracking-tight">
                            Dokumen Hasil Verifikasi
                        </h3>
                        <p class="text-purple-100 text-sm md:text-base leading-relaxed mb-6 font-medium">
                            Unduh dokumen SK/Sertifikat dari admin.<br>
                            Jika ada tindak lanjut, proses di <strong class="text-white font-bold underline decoration-indigo-400 underline-offset-4">BALISPEKERJA</strong> lalu konfirmasi di sini.
                        </p>
                        
                        <!-- Icon Box -->
                        <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center mx-auto shadow-inner border border-white/30 group-hover:scale-110 transition-transform duration-500">
                            <i class="fas fa-certificate text-3xl text-white drop-shadow-md"></i>
                        </div>
                    </div>
                </div>

                <!-- Tabel Data -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 bg-white flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h3 class="font-bold text-slate-800 text-lg">Kotak Masuk Verifikasi</h3>
                            <p class="text-slate-500 text-xs">Daftar dokumen verifikasi yang diterima</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-slate-600 min-w-[900px] md:min-w-0">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-50/50 border-b border-slate-100">
                                <tr>
                                    <th class="px-6 py-4 font-semibold tracking-wider">Tanggal</th>
                                    <th class="px-6 py-4 font-semibold tracking-wider">Judul Dokumen</th>
                                    <th class="px-6 py-4 font-semibold tracking-wider">File Admin</th>
                                    <th class="px-6 py-4 font-semibold tracking-wider text-center">Status</th>
                                    <th class="px-6 py-4 font-semibold tracking-wider text-center">Aksi Anda</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($verifikasis as $item)
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
                                            <span class="font-bold text-slate-800 text-sm group-hover:text-purple-600 transition-colors">
                                                {{ $item->title }}
                                            </span>
                                            <span class="text-[10px] bg-purple-50 text-purple-700 px-2 py-0.5 rounded border border-purple-100 w-fit font-bold">Verifikasi</span>
                                        </div>
                                    </td>

                                    <!-- File Admin -->
                                    <td class="px-6 py-4">
                                        @if($item->admin_file)
                                            <a href="{{ asset('storage/' . $item->admin_file) }}" target="_blank" class="inline-flex items-center gap-2 bg-white border border-slate-200 text-slate-600 hover:text-purple-600 hover:border-purple-300 px-3 py-2 rounded-lg transition-all shadow-sm group/file">
                                                <div class="bg-purple-50 text-purple-500 p-1 rounded group-hover/file:bg-purple-100 transition-colors">
                                                    <i class="fas fa-file-download"></i>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="font-medium text-xs">Unduh File</span>
                                                </div>
                                            </a>
                                            @if($item->admin_note)
                                                <div class="mt-2 text-[10px] bg-slate-50 p-2 rounded text-slate-500 italic border border-slate-100 w-fit">
                                                    "{{ $item->admin_note }}"
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-xs text-slate-400 italic">Menunggu upload...</span>
                                        @endif
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4 text-center">
                                        @if($item->status == 'approved')
                                            <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 text-xs font-bold px-3 py-1.5 rounded-full border border-emerald-100">
                                                <i class="fas fa-check-circle text-emerald-500"></i> Selesai
                                            </span>
                                        @elseif($item->status == 'rejected')
                                            <span class="inline-flex items-center gap-1.5 bg-rose-50 text-rose-700 text-xs font-bold px-3 py-1.5 rounded-full border border-rose-100">
                                                <i class="fas fa-exclamation-circle text-rose-500"></i> Revisi
                                            </span>
                                        @elseif($item->user_note)
                                            <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 text-xs font-bold px-3 py-1.5 rounded-full border border-blue-100">
                                                <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span> Menunggu Verif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 bg-purple-50 text-purple-700 text-xs font-bold px-3 py-1.5 rounded-full border border-purple-100">
                                                Perlu Aksi
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Aksi -->
                                    <td class="px-6 py-4 text-center">
                                        @if($item->status == 'approved')
                                            <span class="text-emerald-500 text-lg drop-shadow-sm"><i class="fas fa-check-double"></i></span>
                                        @elseif($item->user_note)
                                            <span class="text-xs text-slate-400 italic">Menunggu Admin</span>
                                        @else
                                            <button 
                                                type="button"
                                                class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm hover:shadow active:scale-95 transition-all flex items-center gap-1.5 justify-center mx-auto whitespace-nowrap"
                                                data-id="{{ $item->id }}"
                                                data-title="{{ $item->title ?? 'Dokumen Verifikasi' }}"
                                                onclick="openConfirmModal(this)">
                                                <i class="fas fa-check-double"></i> Tindak Lanjut
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                                <i class="fas fa-certificate text-4xl text-slate-300"></i>
                                            </div>
                                            <h3 class="text-slate-800 font-bold text-lg">Belum ada dokumen</h3>
                                            <p class="text-slate-500 text-sm mt-1 max-w-xs mx-auto">Belum ada dokumen verifikasi yang masuk dari Admin.</p>
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

    <!-- MODAL KONFIRMASI -->
    <div id="confirmModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100 animate-pop-in">
                    
                    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <i class="fas fa-check-circle"></i> Konfirmasi Tindak Lanjut
                        </h3>
                        <button type="button" onclick="closeModal()" class="text-purple-100 hover:text-white bg-white/10 hover:bg-white/20 p-1.5 rounded-lg transition-colors"><i class="fas fa-times text-lg"></i></button>
                    </div>

                    <form id="responseForm" method="POST" action="" onsubmit="return showLoading()">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="force_status" value="pending">
                        <input type="hidden" name="user_note" value="Konfirmasi: Sudah ditindaklanjuti via Balispekerja">
                        <input type="hidden" name="title" id="inputTitleHidden" value="Dokumen Verifikasi">

                        <div class="px-6 py-6 space-y-4">
                            <div class="bg-purple-50 p-4 rounded-xl border border-purple-100">
                                <p class="text-xs text-purple-900 font-bold mb-1 uppercase tracking-wide">Konfirmasi Dokumen:</p>
                                <p class="text-sm text-purple-700 font-medium" id="docTitleDisplay">Dokumen Verifikasi</p>
                            </div>
                            
                            <div class="text-center py-2">
                                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-400">
                                    <i class="fas fa-external-link-alt text-2xl"></i>
                                </div>
                                <p class="text-sm text-slate-600 font-medium">
                                    Pastikan Anda telah menyelesaikan proses di <strong>BALISPEKERJA</strong>.
                                </p>
                                <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                                    Klik tombol di bawah untuk memberitahu Admin bahwa Anda sudah menindaklanjuti dokumen ini.
                                </p>
                            </div>
                        </div>

                        <div class="bg-slate-50 px-6 py-4 flex flex-col gap-3 border-t border-slate-100">
                            <button type="submit" id="btnSubmit" class="w-full justify-center rounded-xl bg-purple-600 px-5 py-3 text-sm font-bold text-white shadow-md hover:bg-purple-700 transition-colors flex items-center gap-2 active:scale-95">
                                <i class="fas fa-check"></i> Ya, Saya Sudah Tindak Lanjut
                            </button>
                            <button type="button" onclick="closeModal()" class="w-full justify-center rounded-xl bg-white px-5 py-3 text-sm font-bold text-slate-600 border border-slate-200 hover:bg-slate-50 transition-colors">
                                Batal
                            </button>
                        </div>
                    </form>
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
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        }

        // === NOTIFICATIONS ===
        function closeNotification(modalId) {
            const modal = document.getElementById(modalId);
            if(modal) {
                modal.classList.add('opacity-0');
                modal.querySelector('div').classList.add('scale-95');
                setTimeout(() => { modal.style.display = 'none'; }, 300);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const successModal = document.getElementById('successModal');
            if(successModal) {
                setTimeout(() => { 
                    const bar = document.getElementById('progressBar');
                    if(bar) bar.style.width = '0%'; 
                }, 100);
                setTimeout(() => { closeNotification('successModal'); }, 3100);
            }
        });

        // === CONFIRM MODAL LOGIC ===
        function openConfirmModal(button) {
            var id = button.getAttribute('data-id');
            var title = button.getAttribute('data-title');
            var safeTitle = (title && title.trim() !== "") ? title : "Dokumen Verifikasi";

            var modal = document.getElementById('confirmModal');
            var form = document.getElementById('responseForm');
            var titleDisplay = document.getElementById('docTitleDisplay');
            var titleInput = document.getElementById('inputTitleHidden');
            
            if(titleDisplay) titleDisplay.innerText = safeTitle;
            if(titleInput) titleInput.value = safeTitle;
            if(form) form.action = "{{ url('/submission/update') }}/" + id;
            
            if(modal) modal.classList.remove('hidden');
        }
        
        function closeModal() { 
            var modal = document.getElementById('confirmModal');
            if(modal) modal.classList.add('hidden'); 
        }

        function showLoading() {
            var btn = document.getElementById('btnSubmit');
            if(btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
            }
            return true; 
        }
    </script>
</body>
</html>