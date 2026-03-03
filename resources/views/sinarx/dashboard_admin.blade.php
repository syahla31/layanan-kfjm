<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | SI-MUTU Sinar-X</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Menggunakan font Plus Jakarta Sans agar seragam -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: {
                        primary: '#ea580c', /* orange-600 */
                        orangeMain: '#c2410c', /* orange-700 */
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        @keyframes modalBounce {
            0% { opacity: 0; transform: scale(0.95) translateY(20px); }
            70% { opacity: 1; transform: scale(1.01) translateY(0); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }
        .animate-modal-bounce { animation: modalBounce 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
        .animate-pulse-slow { animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
        
        .glass-overlay {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
        }

        #revisionInput {
            transition: all 0.3s ease-in-out;
            max-height: 0;
            overflow: hidden;
            opacity: 0;
        }
        #revisionInput.show {
            max-height: 500px;
            opacity: 1;
            margin-top: 1.5rem;
        }

        .word-table th, .word-table td {
            border: 1pt solid black;
            padding: 8px;
        }

        @media print {
            @page { size: A4; margin: 0; }
            body > *:not(#notaDinasModal) { display: none !important; }
            html, body { height: auto !important; overflow: visible !important; background: white !important; margin: 0 !important; padding: 0 !important; }
            #notaDinasModal { position: static !important; display: block !important; visibility: visible !important; overflow: visible !important; background: white !important; width: 100% !important; }
            #notaDinasModal .relative { position: static !important; box-shadow: none !important; border: none !important; border-radius: 0 !important; width: 100% !important; height: auto !important; max-height: none !important; }
            #notaDinasModal .no-print, #notaDinasModal .bg-blue-600, #notaDinasModal .absolute.inset-0 { display: none !important; }
            #notaDinasModal .flex-1, #notaDinasModal .bg-slate-100 { background: white !important; padding: 0 !important; margin: 0 !important; overflow: visible !important; height: auto !important; display: block !important; }
            #printableNota { visibility: visible !important; display: block !important; width: 21cm !important; margin: 0 auto !important; padding: 1.5cm 2cm !important; box-shadow: none !important; border: none !important; color: black !important; }
            #printableNota * { color: black !important; border-color: black !important; -webkit-print-color-adjust: exact !important; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased overflow-hidden selection:bg-orange-100 selection:text-orange-900">

    @php
        use App\Models\SinarxSubmission;
        use Illuminate\Support\Facades\Auth;
        
        if (!isset($allSubmissions)) {
            try {
                $allSubmissions = SinarxSubmission::with('user')->orderBy('created_at', 'desc')->get();
            } catch (\Exception $e) { 
                $allSubmissions = collect([]); 
            }
        }

        $stats = (object)[
            'total'    => $allSubmissions->count(),
            'pending'  => $allSubmissions->where('status', 'pending')->count(),
            'approved' => $allSubmissions->where('status', 'approved')->count(),
            'rejected' => $allSubmissions->where('status', 'rejected')->count()
        ];
    @endphp

    <div class="flex h-screen overflow-hidden bg-slate-50 relative no-print">
        
        <!-- === MOBILE OVERLAY === -->
        <div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 z-40 hidden lg:hidden glass-overlay transition-opacity duration-300 opacity-0 no-print"></div>

        <!-- === SIDEBAR WRAPPER (Responsive) === -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-2xl lg:shadow-none transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto flex flex-col h-full border-r border-slate-200 no-print">
            @include('components.sinarx-sidebar')
        </aside>

        <div class="flex-1 flex flex-col h-screen overflow-hidden relative w-full">
            
            <!-- === HEADER MOBILE === -->
            <div class="lg:hidden bg-white/90 backdrop-blur-md border-b border-slate-200 px-4 py-3 flex items-center justify-between z-20 sticky top-0 shadow-sm no-print">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="p-2 -ml-2 text-slate-500 hover:text-orange-600 hover:bg-slate-100 rounded-lg transition-colors focus:outline-none">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-orange-600 flex items-center justify-center text-white shadow-sm">
                            <i class="fas fa-radiation text-sm animate-pulse-slow"></i>
                        </div>
                        <span class="font-bold text-slate-800 text-sm tracking-wide">SI-SINAR X</span>
                    </div>
                </div>
                <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 text-xs font-bold border border-orange-200">
                    {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                </div>
            </div>

            <!-- === HEADER DESKTOP === -->
            <div class="hidden lg:block sticky top-0 z-10 bg-white/80 backdrop-blur-md border-b border-slate-200 no-print">
                @include('components.sinarx-header', [
                    'title' => 'Verifikasi Amandemen',
                    'subtitle' => 'Panel Administrator SI-SINAR X'
                ])
            </div>

            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-6 lg:p-8 space-y-6 no-scrollbar scroll-smooth">
                <!-- STATISTIK -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 text-center lg:text-left">
                    <div class="bg-white p-5 md:p-6 rounded-[2rem] md:rounded-[2.5rem] border border-slate-200 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex items-center gap-4 relative overflow-hidden">
                        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center shrink-0 group-hover:bg-blue-600 group-hover:text-white transition-all"><i class="fas fa-folder-open text-xl"></i></div>
                        <div class="min-w-0 text-left">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Total Masuk</p>
                            <h3 class="text-2xl font-black text-slate-800 tracking-tight">{{ $stats->total }}</h3>
                        </div>
                    </div>
                    <div class="group bg-white p-5 md:p-6 rounded-[2rem] md:rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex items-center gap-4 relative overflow-hidden">
                        <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center shrink-0 group-hover:bg-amber-500 group-hover:text-white transition-all"><i class="fas fa-clock text-xl group-hover:rotate-180 transition-transform duration-500"></i></div>
                        <div class="min-w-0 text-left">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Menunggu</p>
                            <h3 class="text-2xl font-black text-slate-800 tracking-tight">{{ $stats->pending }}</h3>
                        </div>
                    </div>
                    <div class="group bg-white p-5 md:p-6 rounded-[2rem] md:rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex items-center gap-4 relative overflow-hidden">
                        <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center shrink-0 group-hover:bg-emerald-600 group-hover:text-white transition-all"><i class="fas fa-check-double text-xl"></i></div>
                        <div class="min-w-0 text-left">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Selesai</p>
                            <h3 class="text-2xl font-black text-slate-800 tracking-tight">{{ $stats->approved }}</h3>
                        </div>
                    </div>
                    <div class="group bg-white p-5 md:p-6 rounded-[2rem] md:rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex items-center gap-4 relative overflow-hidden">
                        <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center shrink-0 group-hover:bg-rose-600 group-hover:text-white transition-all"><i class="fas fa-exclamation-circle text-xl group-hover:animate-bounce"></i></div>
                        <div class="min-w-0 text-left">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Revisi</p>
                            <h3 class="text-2xl font-black text-slate-800 tracking-tight">{{ $stats->rejected }}</h3>
                        </div>
                    </div>
                </div>

                <!-- TABEL ANTREAN -->
                <div class="bg-white border border-slate-200 rounded-[2rem] md:rounded-[3rem] shadow-sm overflow-hidden mb-10">
                    <div class="px-8 py-7 border-b border-slate-100 bg-white/50 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <span class="w-1.5 h-6 bg-orange-600 rounded-full"></span>
                            <h3 class="font-black text-base md:text-lg text-slate-800 tracking-tight">Antrean Verifikasi Permohonan</h3>
                        </div>
                    </div>
                    <div class="overflow-x-auto no-scrollbar">
                        <table class="w-full text-sm text-left text-slate-600 min-w-[950px]">
                            <thead class="text-[10px] text-slate-400 uppercase bg-slate-50/50 border-b border-slate-100 font-black tracking-[0.15em]">
                                <tr>
                                    <th class="px-8 py-5">Lembaga</th>
                                    <th class="px-6 py-5">Identitas Sertifikat</th>
                                    <th class="px-6 py-5 w-[35%]">Alasan / Perihal</th>
                                    <th class="px-6 py-5 text-center">Tgl Masuk</th>
                                    <th class="px-8 py-5 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($allSubmissions as $item)
                                <tr class="hover:bg-slate-50/80 transition-colors group">
                                    <td class="px-8 py-6">
                                        <span class="font-bold text-slate-800 text-sm block mb-0.5">{{ $item->user->name ?? 'Unit Tidak Terdaftar' }}</span>
                                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tight">ID: UNIT-{{ 1000 + $item->user_id }}</span>
                                    </td>
                                    <td class="px-6 py-6 whitespace-nowrap">
                                        <div class="flex flex-col gap-1.5">
                                            <span class="bg-orange-50 text-orange-600 text-[10px] font-bold px-2 py-0.5 rounded-lg border border-orange-100 w-fit">Sertif: {{ $item->no_sertifikat }}</span>
                                            <span class="bg-slate-50 text-slate-500 text-[10px] font-bold px-2 py-0.5 rounded-lg border border-slate-200 w-fit">No. Surat: {{ $item->nomor_surat ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-6">
                                        <div class="relative">
                                            <p class="text-xs text-slate-500 line-clamp-2 italic leading-relaxed" id="reason-{{ $item->id }}">"{{ $item->alasan_amandemen }}"</p>
                                            @if(strlen($item->alasan_amandemen) > 80)
                                            <button type="button" onclick="toggleReason({{ $item->id }})" class="text-[10px] font-bold text-orange-600 mt-2 hover:underline">Lihat Selengkapnya</button>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-6 text-center">
                                        <span class="font-bold text-slate-700 text-xs block">{{ $item->created_at->format('d/m/Y') }}</span>
                                    </td>
                                    <td class="px-8 py-6 text-center whitespace-nowrap">
                                        @if($item->status == 'pending')
                                            <button type="button" 
                                                data-id="{{ $item->id }}"
                                                data-unit="{{ $item->user->name ?? 'Unknown' }}"
                                                data-sertif="{{ $item->no_sertifikat }}"
                                                data-regis="{{ $item->no_registrasi }}"
                                                data-reason="{{ e($item->alasan_amandemen) }}"
                                                data-file="{{ $item->file_path ? asset('storage/' . $item->file_path) : '#' }}"
                                                data-nomorsurat="{{ $item->nomor_surat ?? '-' }}"
                                                data-bagian="{{ $item->bagian_diperbaiki ?? '-' }}"
                                                data-salah="{{ e($item->ketidaksesuaian ?? '-') }}"
                                                data-benar="{{ e($item->data_sesuai ?? '-') }}"
                                                data-date="{{ $item->created_at->translatedFormat('d F Y') }}"
                                                onclick="event.preventDefault(); window.openReviewModal(this); return false;"
                                                class="bg-slate-900 text-white px-8 py-2.5 rounded-2xl text-xs font-bold shadow-lg hover:bg-orange-600 transition-all active:scale-95 group-hover:scale-105">
                                                <i class="fas fa-search-plus mr-2"></i> Review
                                            </button>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-600 text-[10px] font-black px-4 py-2 rounded-full border border-emerald-100 tracking-widest"><i class="fas fa-check"></i> SELESAI</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="px-8 py-16 text-center text-slate-400 italic font-medium">Belum ada antrean permohonan masuk.</td></tr>
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

    <!-- MODAL REVIEW -->
    <div id="reviewModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 no-print">
        <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-md transition-opacity" onclick="closeReviewModal()"></div>
        <div class="relative bg-white rounded-[2.5rem] max-w-5xl w-full shadow-2xl animate-modal-bounce overflow-hidden flex flex-col max-h-[94vh] border border-white/20">

            <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 px-10 py-7 flex justify-between items-center text-white shrink-0">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-600 to-orange-400 rounded-2xl flex items-center justify-center shadow-xl">
                        <i class="fas fa-clipboard-check text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-xl tracking-tight">Peninjauan Dokumen</h3>
                        <p class="text-[11px] text-slate-400 font-bold uppercase tracking-[0.1em]" id="modalSubTitle">-</p>
                    </div>
                </div>
                <button type="button" onclick="closeReviewModal()" class="w-10 h-10 rounded-full hover:bg-white/10 transition-all flex items-center justify-center bg-white/5 border border-white/10">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-0 no-scrollbar bg-slate-50/50 text-left">
                <div class="px-10 py-8 space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">No. Sertifikat</span>
                            <p class="font-bold text-slate-800 text-sm truncate" id="reviewSertif">-</p>
                        </div>
                        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">No. Surat Unit</span>
                            <p class="font-bold text-slate-800 text-sm truncate" id="reviewNomorSurat">-</p>
                        </div>
                        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Aksi Cepat</span>
                            <button type="button" onclick="generateNotaDinas()" class="font-black text-blue-600 text-[10px] flex items-center gap-2 hover:bg-blue-50 px-3 py-2 rounded-xl transition-all border border-blue-100 shadow-sm w-fit mt-1">
                                <i class="fas fa-file-word text-sm"></i> GENERATE NOTA DINAS
                            </button>
                        </div>
                    </div>

                    <div class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm space-y-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-1.5 h-4 bg-orange-500 rounded-full"></span>
                            <h4 class="text-[10px] font-black text-orange-600 uppercase tracking-widest">Detail Tabel Amandemen</h4>
                        </div>
                        <div class="overflow-hidden border border-slate-200 rounded-2xl">
                            <table class="w-full text-xs text-left">
                                <thead class="bg-slate-50 border-b border-slate-200 text-slate-400 uppercase font-bold text-[9px]">
                                    <tr>
                                        <th class="px-4 py-3 text-center">Bagian Perbaikan</th>
                                        <th class="px-4 py-3 text-center">Ketidaksesuaian</th>
                                        <th class="px-4 py-3 text-center">Data Sesuai</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700">
                                    <tr>
                                        <td class="px-4 py-4 font-bold text-center" id="detailBagian">-</td>
                                        <td class="px-4 py-4 italic whitespace-pre-wrap text-left" id="detailSalah">-</td>
                                        <td class="px-4 py-4 font-bold text-emerald-600 whitespace-pre-wrap text-left" id="detailBenar">-</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="w-full h-[450px] bg-slate-900 rounded-[2.5rem] border-8 border-white shadow-2xl overflow-hidden relative">
                        <iframe id="pdfPreviewFrame" src="" class="w-full h-full border-none block" onload="handleIframeLoad()"></iframe>
                        <div id="pdfFallback" class="absolute inset-0 flex flex-col items-center justify-center text-slate-500 bg-slate-50 transition-all duration-500">
                            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mb-4"><i class="fas fa-file-pdf text-3xl text-orange-400 animate-bounce"></i></div>
                            <p id="pdfStatusLabel" class="text-sm font-bold text-slate-400">Memproses berkas digital...</p>
                        </div>
                    </div>

                    <div id="revisionInput" class="space-y-3">
                        <label class="text-[11px] font-extrabold text-rose-500 uppercase tracking-widest flex items-center gap-2"><i class="fas fa-exclamation-triangle"></i> Catatan Revisi</label>
                        <form id="rejectForm" method="POST">@csrf
                            <textarea name="admin_note" id="adminNote" placeholder="Tuliskan alasan permohonan perlu diperbaiki..." class="w-full bg-white border-2 border-rose-50 rounded-2xl p-5 text-sm focus:ring-4 focus:ring-rose-500/10 focus:border-rose-300 transition-all outline-none shadow-inner resize-none min-h-[140px]" required></textarea>
                        </form>
                    </div>
                </div>
            </div>

            <div class="px-10 py-8 border-t border-slate-100 bg-white/95 backdrop-blur-xl shrink-0">
                <div id="defaultActions" class="flex flex-col sm:flex-row gap-4">
                    <button type="button" onclick="openConfirmApprove()" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 rounded-2xl shadow-xl transition-all active:scale-95 flex items-center justify-center gap-3">
                        <i class="fas fa-check-circle text-lg"></i> <span>Verifikasi & Setujui</span>
                    </button>
                    <button type="button" onclick="showRevisionField()" class="flex-1 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-500 font-bold py-4 rounded-2xl transition-all active:scale-95 flex items-center justify-center gap-3 border border-transparent">
                        <i class="fas fa-edit text-lg"></i> <span>Minta Revisi</span>
                    </button>
                </div>
                <div id="revisionActions" class="hidden flex flex-col sm:flex-row gap-4">
                    <button type="button" onclick="document.getElementById('rejectForm').submit()" class="flex-[2] bg-rose-600 hover:bg-rose-700 text-white font-bold py-4 rounded-2xl shadow-lg transition-all active:scale-95 flex items-center justify-center gap-3">
                        <i class="fas fa-paper-plane"></i> <span>Kirim Catatan Revisi</span>
                    </button>
                    <button type="button" onclick="cancelRevision()" class="flex-1 bg-white text-slate-400 font-bold py-4 rounded-2xl transition-all hover:bg-slate-50 border border-slate-200">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL NOTA DINAS (CETAK) -->
    <div id="notaDinasModal" class="fixed inset-0 z-[150] hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm no-print" onclick="closeNotaDinas()"></div>
        <div class="relative bg-white rounded-[2.5rem] max-w-5xl w-full shadow-2xl animate-modal-bounce flex flex-col max-h-[95vh] border border-white/20 overflow-hidden">
            
            <div class="bg-blue-600 px-10 py-6 flex justify-between items-center text-white shrink-0 no-print">
                <div class="flex items-center gap-3"><i class="fas fa-file-alt text-xl"></i><h3 class="font-extrabold text-lg uppercase tracking-wider">Preview Nota Dinas</h3></div>
                <div class="flex gap-3">
                    <button onclick="window.print()" class="bg-white text-blue-600 px-5 py-2 rounded-xl font-bold text-xs hover:bg-blue-50 transition shadow-lg active:scale-95"><i class="fas fa-print mr-2"></i> CETAK / PDF</button>
                    <button onclick="closeNotaDinas()" class="w-10 h-10 rounded-xl hover:bg-white/20 transition flex items-center justify-center border border-white/20"><i class="fas fa-times"></i></button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-12 bg-slate-100 no-scrollbar">
                <div id="printableNota" class="bg-white shadow-2xl mx-auto p-[1.5cm] min-h-[29.7cm] w-[21cm] text-black text-justify leading-relaxed" style="font-family: 'Times New Roman', serif; color: black !important;">
                    <div class="w-full mb-4 text-black">
                        <div class="flex flex-col items-center w-fit">
                            <div class="mb-1 flex items-center justify-center" style="width: 2.83cm; height: 2.69cm;">
                                <img src="{{ asset('image/logo.svg') }}" alt="Logo BAPETEN" class="w-full h-full object-contain" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMDAgMTAwIj48Y2lyY2xlIGN4PSI1MCIgY3k9IjUwIiByPSI0MCIgZmlsbD0iI2VlZSIgc3Ryb2tlPSIjY2NjIiAvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBkbz0iLjM1ZW0iIHRleHQtYW5jaG9yPSJtaWRkbGUiIGZpbGw9IiM5OTkiIGZvbnQtc2l6ZT0iMTAiPkxPR088L3RleHQ+PC9zdmc+'">
                            </div>
                            <div class="text-center">
                                <div style="font-family: 'Arial Black', sans-serif; color: black;" class="text-[11pt] font-black uppercase tracking-tight leading-none">BADAN PENGAWAS TENAGA NUKLIR</div>
                                <div style="font-family: 'Times New Roman', serif; color: black;" class="text-[12pt] font-medium italic leading-none">Nuclear Energy Regulatory Agency</div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4 mb-1" style="font-family: Arial, sans-serif; font-size: 14pt; font-weight: bold; text-decoration: underline;">NOTA DINAS</div>
                    <div class="text-center mb-4 text-[11pt]">Nomor: <span contenteditable="true" class="italic">0406/PI 05 08/II/2026</span></div>

                    <div class="grid grid-cols-[130px_10px_1fr] gap-x-2 gap-y-0.5 mb-4 text-[11pt]">
                        <div>Kepada Yth.</div><div>:</div><div contenteditable="true">Plt. Kepala Biro Perencanaan, Informasi dan Keuangan</div>
                        <div>Dari</div><div>:</div><div>Direktorat Keteknikan dan Kesiapsiagaan Nuklir</div>
                        <div>Lampiran</div><div>:</div><div>1 (satu) berkas</div>
                        <div>Perihal</div><div>:</div><div>Permohonan Amandemen Sertifikat</div>
                        <div>Tanggal</div><div>:</div><div id="notaDateDisplay">-</div>
                    </div>

                    <p class="mb-3 text-[11pt]">
                        Sehubungan dengan adanya surat permohonan dari <span id="notaUnitName">-</span> No. <span class="font-bold" id="notaNomorSurat">-</span> perihal <span id="notaPerihal">-</span> pada Sertifikat UKES Pesawat Sinar X. Untuk itu kami mohon bantuan Saudara untuk melakukan amandemen sebagai berikut:
                    </p>

                    <table class="w-full border-collapse border border-black mb-4 text-[11pt] leading-normal word-table">
                        <thead>
                            <tr class="bg-slate-50">
                                <th class="text-center w-[20%]">Nomor Sertifikat</th>
                                <th class="text-center w-[25%]">Bagian yang diperbaiki</th>
                                <th class="text-center w-[25%]">Ketidaksesuaian</th>
                                <th class="text-center w-[30%]">Data yang sesuai</th>
                            </tr>
                        </thead>
                        <tbody contenteditable="true">
                            <tr>
                                <td class="text-center align-top">
                                    <span id="notaSertifDisplay" class="font-bold">-</span><br>
                                    <span class="text-[10pt]">Reg: <span id="notaRegisDisplay">-</span></span>
                                </td>
                                <td class="align-top text-center" id="notaBagianDisplay">-</td>
                                <td class="align-top whitespace-pre-wrap text-left" id="notaSalahDisplay">-</td>
                                <td class="align-top whitespace-pre-wrap font-bold text-left" id="notaBenarDisplay">-</td>
                            </tr>
                        </tbody>
                    </table>

                    <p class="mb-3 text-[11pt]">Terlampir kami sampaikan surat permohonan dari <span id="notaUnitNameFooter">-</span> dan dokumen pendukung lainnya.</p>
                    <p class="mb-4 text-[11pt]">Demikian, atas perhatian dan kerjasama Saudara kami ucapkan terima kasih.</p> 

                    <div class="flex justify-end mt-10 mb-16">
                        <div class="text-center w-[10cm] text-[11pt] flex flex-col items-center">
                            <div class="w-[150px] h-[40px] flex items-center justify-center">
                                <span class="text-[11pt] font-bold select-none">#</span>
                            </div>
                        </div>
                    </div>

                    <div class="text-[11pt]">
                        <p>Tembusan Yth:</p>
                        <p contenteditable="true" class="outline-none cursor-text">Pengelola Kegiatan Fungsi Data dan Informasi</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL KONFIRMASI -->
    <div id="confirmApproveModal" class="fixed inset-0 z-[120] hidden flex items-center justify-center p-4 no-print">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeConfirmApprove()"></div>
        <div class="relative bg-white rounded-[2rem] p-8 max-w-sm w-full shadow-2xl text-center animate-modal-bounce border border-slate-100">
            <div class="w-16 h-16 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-emerald-100 relative"><div class="absolute inset-0 bg-emerald-200 rounded-full animate-ping opacity-20"></div><i class="fas fa-check-circle text-2xl relative"></i></div>
            <h3 class="text-xl font-extrabold text-slate-800 mb-2">Setujui Permohonan?</h3>
            <p class="text-slate-500 text-sm mb-8 leading-relaxed px-2">Dokumen dinyatakan valid dan akan diproses untuk amandemen sertifikat.</p>
            <div class="space-y-3">
                <form id="approveFormReal" method="POST">@csrf
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 rounded-xl shadow-lg active:scale-95">Ya, Verifikasi Sekarang</button>
                </form>
                <button type="button" onclick="closeConfirmApprove()" class="w-full bg-transparent text-slate-400 font-bold py-2 text-xs uppercase tracking-widest block text-center">Batal</button>
            </div>
        </div>
    </div>

    <script>
        // === LOGIKA SIDEBAR RESPONSIVE ===
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                setTimeout(() => {
                    overlay.classList.remove('opacity-0');
                    overlay.classList.add('opacity-100');
                }, 10);
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.remove('opacity-100');
                overlay.classList.add('opacity-0');
                setTimeout(() => { overlay.classList.add('hidden'); }, 300);
            }
        }

        window.toggleReason = function(id) {
            const p = document.getElementById('reason-' + id);
            p.classList.toggle('line-clamp-2');
        };

        window.handleIframeLoad = function() {
            const fallback = document.getElementById('pdfFallback');
            if (document.getElementById('pdfPreviewFrame').src !== 'about:blank') {
                fallback.classList.add('opacity-0');
                setTimeout(() => fallback.classList.add('hidden'), 500);
            }
        };

        let currentActiveData = {};

        window.openReviewModal = function(btn) {
            currentActiveData = {
                id: btn.getAttribute('data-id'),
                unit: btn.getAttribute('data-unit'),
                sertif: btn.getAttribute('data-sertif'),
                regis: btn.getAttribute('data-regis'),
                reason: btn.getAttribute('data-reason'),
                fileUrl: btn.getAttribute('data-file'),
                date: btn.getAttribute('data-date'),
                nomorsurat: btn.getAttribute('data-nomorsurat'),
                bagian: btn.getAttribute('data-bagian'),
                salah: btn.getAttribute('data-salah'),
                benar: btn.getAttribute('data-benar')
            };

            document.getElementById('modalSubTitle').innerText = currentActiveData.unit;
            document.getElementById('reviewSertif').innerText = currentActiveData.sertif;
            document.getElementById('reviewNomorSurat').innerText = currentActiveData.nomorsurat;
            
            document.getElementById('detailBagian').innerText = currentActiveData.bagian;
            document.getElementById('detailSalah').innerText = currentActiveData.salah;
            document.getElementById('detailBenar').innerText = currentActiveData.benar;
            
            const iframe = document.getElementById('pdfPreviewFrame');
            const fallback = document.getElementById('pdfFallback');

            fallback.classList.remove('hidden', 'opacity-0');
            iframe.src = 'about:blank';
            setTimeout(() => {
                if (currentActiveData.fileUrl && currentActiveData.fileUrl !== '#' && !currentActiveData.fileUrl.endsWith('/storage/')) {
                    iframe.src = currentActiveData.fileUrl;
                } else {
                    document.getElementById('pdfStatusLabel').innerText = "Berkas tidak ditemukan";
                }
            }, 100);
            
            const baseUrl = "{{ url('/sinarx/submission') }}";
            document.getElementById('approveFormReal').action = baseUrl + "/approve/" + currentActiveData.id;
            document.getElementById('rejectForm').action = baseUrl + "/reject/" + currentActiveData.id;
            
            document.getElementById('reviewModal').classList.remove('hidden');
            cancelRevision();
        };

        window.generateNotaDinas = function() {
            document.getElementById('notaUnitName').innerText = currentActiveData.unit;
            document.getElementById('notaUnitNameFooter').innerText = currentActiveData.unit;
            document.getElementById('notaNomorSurat').innerText = currentActiveData.nomorsurat;
            document.getElementById('notaPerihal').innerText = currentActiveData.reason;
            document.getElementById('notaSertifDisplay').innerText = currentActiveData.sertif;
            document.getElementById('notaRegisDisplay').innerText = currentActiveData.regis;
            document.getElementById('notaDateDisplay').innerText = currentActiveData.date;
            document.getElementById('notaBagianDisplay').innerText = currentActiveData.bagian;
            document.getElementById('notaSalahDisplay').innerText = currentActiveData.salah;
            document.getElementById('notaBenarDisplay').innerText = currentActiveData.benar;

            document.getElementById('notaDinasModal').classList.remove('hidden');
            document.getElementById('notaDinasModal').classList.add('flex');
        };

        function closeNotaDinas() { document.getElementById('notaDinasModal').classList.add('hidden'); document.getElementById('notaDinasModal').classList.remove('flex'); }
        function closeReviewModal() { document.getElementById('reviewModal').classList.add('hidden'); document.getElementById('pdfPreviewFrame').src = 'about:blank'; }
        function openConfirmApprove() { document.getElementById('confirmApproveModal').classList.remove('hidden'); document.getElementById('confirmApproveModal').classList.add('flex'); }
        function closeConfirmApprove() { document.getElementById('confirmApproveModal').classList.add('hidden'); document.getElementById('confirmApproveModal').classList.remove('flex'); }

        function showRevisionField() {
            document.getElementById('revisionInput').classList.add('show');
            document.getElementById('defaultActions').classList.add('hidden');
            document.getElementById('revisionActions').classList.replace('hidden', 'flex');
        }

        function cancelRevision() {
            document.getElementById('revisionInput').classList.remove('show');
            document.getElementById('defaultActions').classList.remove('hidden');
            document.getElementById('revisionActions').classList.replace('flex', 'hidden');
        }
    </script>
</body>
</html>