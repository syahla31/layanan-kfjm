<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluasi Surveilan | SI-MUTU Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Outfit', 'sans-serif'] },
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    },
                    animation: { 
                        'pop-in': 'popIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards',
                        'shake': 'shake 0.4s ease-in-out',
                    },
                    keyframes: {
                        popIn: { '0%': { opacity: '0', transform: 'scale(0.98) translateY(8px)' }, '100%': { opacity: '1', transform: 'scale(1) translateY(0)' } },
                        shake: { '0%, 100%': { transform: 'translateX(0)' }, '25%': { transform: 'translateX(-5px)' }, '75%': { transform: 'translateX(5px)' } }
                    }
                }
            }
        }
    </script>

    <style>
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .modal-backdrop { backdrop-filter: blur(12px); background-color: rgba(15, 23, 42, 0.5); }
        
        .score-input:checked + label {
            background-color: #2563eb; color: white; border-color: #2563eb;
            transform: translateY(-2px); box-shadow: 0 8px 15px -3px rgba(37, 99, 235, 0.4);
        }
        .score-label { transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
        textarea { white-space: pre-wrap; line-height: 1.5; resize: none; }
        
        .row-item { transition: all 0.3s ease; border-bottom: 1px solid #f1f5f9; }
        .row-item:hover { background-color: #fbfcfe; }
        .row-item:last-child { border-bottom: none; }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-800 antialiased overflow-hidden text-left">

    @php
        $pendingAudits = $audits->where('is_published', 0);
        $completedAudits = $audits->where('is_published', 1);
        
        $total = $audits->count();
        $needReview = $pendingAudits->count();
        $completedCount = $completedAudits->count();

        $categoryConfig = [
            'file_legalitas' => ['label' => 'Aspek Legalitas & Perizinan', 'bobot' => 10, 'icon' => 'fa-building'],
            'file_mutu'      => ['label' => 'Sistem Manajemen Mutu (SMM)', 'bobot' => 20, 'icon' => 'fa-check-double'],
            'file_rekaman'   => ['label' => 'Rekaman & Laporan Implementasi', 'bobot' => 20, 'icon' => 'fa-history'],
            'file_kinerja'   => ['label' => 'Laporan Kinerja & KAK', 'bobot' => 5, 'icon' => 'fa-chart-line'],
            'file_sdm'       => ['label' => 'Sumber Daya Manusia (SDM)', 'bobot' => 10, 'icon' => 'fa-users'],
            'file_sarpras'   => ['label' => 'Sarana & Prasarana Penunjang', 'bobot' => 15, 'icon' => 'fa-tools'],
            'file_kurikulum' => ['label' => 'Kurikulum, Modul & Bahan Ajar', 'bobot' => 20, 'icon' => 'fa-book-open']
        ];
    @endphp

    <div class="flex h-screen overflow-hidden w-full text-left">
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-primary-900 transform -translate-x-full transition-transform duration-300 lg:translate-x-0 lg:static flex flex-col h-full border-r border-blue-900/20 text-left">
            @include('components.pelatihan-sidebar')
        </aside>

        <div class="flex-1 flex flex-col h-screen overflow-hidden relative w-full text-left">
            @include('components.pelatihan-header', ['title' => 'Evaluasi Surveilan', 'subtitle' => 'Panel Verifikasi & Penilaian'])

            <main class="flex-1 overflow-y-auto p-4 md:p-8 space-y-12 no-scrollbar text-left">
                <!-- Stats Section -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-left">
                    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex items-center justify-between group hover:border-blue-400 transition-all text-left">
                        <div class="text-left">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest text-left">Total Pengajuan</p>
                            <h3 class="text-3xl font-black text-slate-900 mt-1 text-left">{{ $total }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center shrink-0"><i class="fas fa-inbox text-left"></i></div>
                    </div>
                    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex items-center justify-between group hover:border-amber-400 transition-all text-left">
                        <div class="text-left">
                            <p class="text-[10px] font-black text-amber-500 uppercase tracking-widest text-left">Antrean & Draf</p>
                            <h3 class="text-3xl font-black text-slate-900 mt-1 text-left">{{ $needReview }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center shrink-0"><i class="fas fa-clock text-left"></i></div>
                    </div>
                    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex items-center justify-between group hover:border-emerald-400 transition-all text-left">
                        <div class="text-left">
                            <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest text-left">Telah Terkirim</p>
                            <h3 class="text-3xl font-black text-slate-900 mt-1 text-left">{{ $completedCount }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center shrink-0"><i class="fas fa-check-double text-left"></i></div>
                    </div>
                </div>

                <!-- Antrean -->
                <div class="space-y-6 text-left">
                    <div class="flex items-center gap-4 px-2 text-left">
                        <h4 class="text-[10px] font-black text-amber-500 uppercase tracking-[0.4em] text-left">Antrean & Draf Evaluasi</h4>
                        <div class="flex-1 h-px bg-amber-100"></div>
                    </div>
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5 text-left">
                        @forelse($pendingAudits as $item)
                        <div class="bg-white rounded-[2.5rem] border border-slate-100 p-8 flex flex-col md:flex-row items-center gap-6 group hover:border-amber-400 transition-all shadow-sm text-left">
                            <div class="flex-1 w-full text-left">
                                <div class="flex items-center gap-5 mb-4 text-left">
                                    <div class="w-14 h-14 rounded-2xl bg-amber-500 text-white flex items-center justify-center font-black text-xl shadow-lg shrink-0">
                                        {{ substr($item->user->name, 0, 1) }}
                                    </div>
                                    <div class="text-left">
                                        <h4 class="font-black text-slate-900 uppercase tracking-tight text-base text-left">{{ $item->user->name }}</h4>
                                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-1 text-left">ID: #{{ $item->id }}</p>
                                    </div>
                                </div>
                                <span class="px-4 py-1.5 rounded-full text-[8px] font-black uppercase tracking-widest border {{ $item->evaluator_scores ? 'bg-amber-50 text-amber-600 border-amber-100' : 'bg-slate-50 text-slate-400 border-slate-100' }}">
                                    {{ $item->evaluator_scores ? 'DRAF TERSEDIA' : 'BELUM DIREVIEW' }}
                                </span>
                            </div>
                            <div class="flex flex-col gap-3 w-full md:w-auto shrink-0 text-left">
                                <button onclick='openEvaluateModal(@json($item))' class="bg-primary-900 text-white px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-primary-600 transition-all shadow-lg flex items-center gap-3 justify-center text-left">
                                    <span>{{ $item->evaluator_scores ? 'TINJAU DRAF' : 'EVALUASI' }}</span>
                                    <i class="fas fa-arrow-right text-[9px] opacity-40 text-left"></i>
                                </button>
                                @if($item->evaluator_scores)
                                <button type="button" onclick="confirmPublish({{ $item->id }}, '{{ $item->user->name }}')" class="w-full bg-emerald-600 text-white px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-lg flex items-center gap-3 justify-center text-left">
                                    <span>KIRIM KE USER</span>
                                    <i class="fas fa-paper-plane text-[9px] opacity-40 text-left"></i>
                                </button>
                                <form id="publish-form-{{ $item->id }}" action="{{ route('survailen.publish', $item->id) }}" method="POST" class="hidden">@csrf</form>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="col-span-full py-12 text-center opacity-30 italic font-black uppercase text-[10px] tracking-[0.5em] text-center">Antrian Kosong</div>
                        @endforelse
                    </div>
                </div>

                <!-- Riwayat Terkirim -->
                <div class="space-y-6 text-left">
                    <div class="flex items-center gap-4 px-2 text-left">
                        <h4 class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.4em] text-left">Laporan Terkirim (Final)</h4>
                        <div class="flex-1 h-px bg-emerald-100"></div>
                    </div>
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5 pb-10 text-left">
                        @forelse($completedAudits as $item)
                        <div class="bg-white rounded-[2.5rem] border border-slate-100 p-8 flex flex-col md:flex-row items-center gap-6 group hover:border-emerald-400 transition-all shadow-sm text-left">
                            <div class="flex-1 w-full text-left">
                                <div class="flex items-center gap-5 mb-4 text-left">
                                    <div class="w-14 h-14 rounded-2xl bg-slate-900 text-white flex items-center justify-center font-black text-xl shadow-lg shrink-0">
                                        {{ substr($item->user->name, 0, 1) }}
                                    </div>
                                    <div class="text-left">
                                        <h4 class="font-black text-slate-900 uppercase tracking-tight text-base text-left">{{ $item->user->name }}</h4>
                                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-1 text-left">Terkirim: {{ $item->updated_at->format('d M Y') }}</p>
                                    </div>
                                </div>
                                <span class="px-4 py-1.5 rounded-full text-[8px] font-black uppercase tracking-widest border bg-emerald-50 text-emerald-600 border-emerald-100">TERPUBLIKASI</span>
                            </div>
                            <div class="flex items-center gap-6 w-full md:w-auto shrink-0 justify-between md:justify-end text-left">
                                <div class="text-right pr-6 border-r border-slate-100 text-right">
                                    <span class="text-3xl font-black text-primary-600 leading-none text-right">{{ $item->predikat }}</span>
                                    <p class="text-[9px] font-bold text-slate-400 mt-1 tracking-widest text-right">{{ number_format($item->final_score, 0) }}%</p>
                                </div>
                                <button onclick='openEvaluateModal(@json($item))' class="bg-slate-50 text-slate-400 px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-primary-900 hover:text-white transition-all shadow-sm flex items-center gap-3 text-left">
                                    <span>RINCIAN</span>
                                    <i class="fas fa-eye text-[9px] opacity-40 text-left"></i>
                                </button>
                            </div>
                        </div>
                        @empty
                        <div class="col-span-full py-12 text-center opacity-20 italic font-black uppercase text-[10px] tracking-[0.5em] text-center">Belum ada data terkirim</div>
                        @endforelse
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- MAIN EVALUATE MODAL -->
    <div id="evaluateModal" class="fixed inset-0 z-[110] hidden items-center justify-center p-4 md:p-6 text-left">
        <div class="absolute inset-0 modal-backdrop" onclick="closeEvaluateModal()"></div>
        <div class="relative bg-white rounded-[3rem] md:rounded-[4rem] w-full max-h-[96vh] flex flex-col shadow-[0_40px_120px_-20px_rgba(0,0,0,0.3)] overflow-hidden animate-pop-in border-[10px] border-white max-w-[95vw] text-left">
            
            <div class="p-8 md:p-10 flex justify-between items-center shrink-0 border-b border-slate-100 relative bg-white text-left">
                <div class="text-left">
                    <div class="flex items-center gap-3 mb-2 text-left">
                        <span id="statusLabel" class="px-3 py-1 bg-primary-600 text-white text-[8px] font-black uppercase tracking-widest rounded-lg text-left shadow-lg shadow-primary-50">Status</span>
                        <p id="evalModalId" class="text-[10px] font-bold text-slate-300 uppercase tracking-widest text-left"></p>
                    </div>
                    <h3 class="text-2xl md:text-3xl font-black text-slate-900 uppercase tracking-tighter leading-none text-left mb-1.5">Penilaian Surveilan</h3>
                    <div class="flex items-center gap-2 text-slate-400 text-left">
                        <i class="fas fa-landmark text-xs text-left"></i>
                        <span id="evalModalTitleText" class="text-[10px] md:text-sm font-bold uppercase tracking-wide truncate max-w-xl text-left"></span>
                    </div>
                </div>
                <div class="flex items-center gap-3 text-left">
                    <button id="toggleEditBtn" type="button" onclick="enableEditMode()" class="hidden items-center gap-2 px-6 py-4 rounded-2xl bg-amber-500 text-white hover:bg-amber-600 transition-all shrink-0 shadow-lg shadow-amber-50 text-left">
                        <i class="fas fa-edit text-[10px] text-left"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest text-left">Edit Data</span>
                    </button>
                    <button onclick="closeEvaluateModal()" class="w-12 h-12 md:w-14 md:h-14 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 hover:bg-rose-500 hover:text-white transition-all active:scale-90 shrink-0 border border-slate-100 text-left">
                        <i class="fas fa-times text-lg text-left"></i>
                    </button>
                </div>
            </div>
            
            <form id="evaluateForm" onsubmit="handleFormSubmit(event)" method="POST" action="" enctype="multipart/form-data" class="flex-1 flex flex-col overflow-hidden text-left bg-white">
                @csrf
                <input type="hidden" name="final_score" id="inputFinalScore">
                <input type="hidden" name="predikat" id="inputPredikat">

                <!-- Header Legend -->
                <div class="hidden lg:grid grid-cols-12 gap-0 px-12 py-6 bg-slate-50/50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-widest text-left shrink-0">
                    <div class="col-span-4 pl-4 text-left">Komponen & Bobot</div>
                    <div class="col-span-3 pl-4 text-left">Dokumen Lampiran</div>
                    <div class="col-span-1 text-center">Self</div>
                    <div class="col-span-2 text-center">Skor Asesor</div>
                    <div class="col-span-2 pl-6 text-left">Analisis Verifikasi</div>
                </div>

                <div class="flex-1 overflow-y-auto p-4 md:p-8 space-y-px no-scrollbar modal-content-area text-left bg-white">
                    <div id="evaluationBody" class="divide-y divide-slate-100 border border-slate-100 rounded-[2.5rem] overflow-hidden bg-white shadow-sm text-left">
                        <!-- JS Content -->
                    </div>

                    <!-- Footer Summary -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-12 mb-8 text-left">
                        <div class="bg-white p-10 rounded-[3.5rem] text-slate-900 flex flex-col justify-center items-center relative shadow-xl border border-slate-100 text-center">
                            <div class="absolute top-0 right-0 p-12 opacity-[0.02] pointer-events-none text-primary-900 text-center"><i class="fas fa-award text-[16rem] text-center"></i></div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.5em] mb-4 text-center">Skor Kumulatif Akhir</p>
                            <h4 id="finalPercentageDisplay" class="text-8xl md:text-9xl font-black leading-none tracking-tighter text-primary-600 text-center">0%</h4>
                            <div id="predikatDisplay" class="mt-10 text-xs md:text-sm font-black text-white uppercase tracking-[0.3em] bg-primary-600 px-14 py-5 rounded-full border-4 border-primary-50 shadow-2xl shadow-primary-200 text-center">
                                Menunggu Skor
                            </div>
                        </div>

                        <div class="space-y-6 text-left">
                            <!-- New: Signing Officer Fields -->
                            <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-xl space-y-4 text-left">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block px-4 text-left mb-2">Pejabat Penandatangan Laporan</label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left">
                                    <div class="space-y-1">
                                        <span class="text-[8px] font-bold text-slate-400 uppercase ml-4">Nama Lengkap & Gelar</span>
                                        <input type="text" name="chairman_name" id="chairmanName" 
                                               class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-4 text-xs font-bold outline-none focus:bg-white focus:border-primary-500 transition-all text-left" 
                                               placeholder="Contoh: Budi Santoso, S.T., M.Eng.">
                                    </div>
                                    <div class="space-y-1">
                                        <span class="text-[8px] font-bold text-slate-400 uppercase ml-4">Nomor Induk Pegawai (NIP)</span>
                                        <input type="text" name="chairman_nip" id="chairmanNip" 
                                               class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-4 text-xs font-bold outline-none focus:bg-white focus:border-primary-500 transition-all text-left" 
                                               placeholder="Contoh: 198506122010121001">
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-xl space-y-4 text-left">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block px-4 text-left">Penerbitan Dokumen LHS</label>
                                <div id="finalFilesArea" class="grid grid-cols-1 gap-4 text-left"></div>
                            </div>
                            <div class="space-y-3 px-2 text-left">
                                <div class="flex justify-between items-center mb-2 text-left">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block px-2 text-left">Kesimpulan & Rekomendasi Umum</label>
                                    <button type="button" onclick="openCommentModal('admin_note', 'REKOMENDASI UMUM')" class="text-[9px] font-black text-primary-600 uppercase tracking-widest hover:text-primary-800 transition-all flex items-center gap-1.5 text-left">
                                        Perbesar <i class="fas fa-expand-alt text-[8px] text-left"></i>
                                    </button>
                                </div>
                                <textarea id="adminNote" name="admin_note" rows="4" onkeydown="handleNumbering(event)" class="w-full rounded-[3rem] border border-slate-200 p-8 text-sm font-bold outline-none shadow-inner bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-50 leading-relaxed text-left transition-all" placeholder="Ketik kesimpulan verifikasi di sini..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-8 md:p-10 bg-white border-t border-slate-100 flex flex-col md:flex-row justify-end items-center gap-6 shrink-0 text-left">
                    <button type="button" onclick="closeEvaluateModal()" class="text-[11px] font-black uppercase tracking-widest text-slate-300 hover:text-rose-500 transition-all text-left">Batalkan</button>
                    <button id="submitBtn" type="submit" class="bg-primary-600 text-white w-full md:w-auto px-16 py-5 rounded-[2rem] text-[11px] font-black uppercase tracking-[0.4em] shadow-2xl shadow-primary-200 hover:bg-primary-900 transition-all flex items-center justify-center gap-5 active:scale-95 text-left text-center">
                        <span>SIMPAN LAPORAN</span>
                        <i class="fas fa-check-double text-xs opacity-50 text-left"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- NOTIFICATION MODAL -->
    <div id="notifyModal" class="fixed inset-0 z-[300] hidden items-center justify-center p-4 text-left">
        <div class="absolute inset-0 modal-backdrop text-left"></div>
        <div class="relative bg-white rounded-[3rem] p-10 shadow-2xl animate-pop-in max-w-md w-full text-center border-[8px] border-white text-left">
            <div id="notifyIcon" class="w-20 h-20 rounded-3xl mx-auto mb-6 flex items-center justify-center text-3xl shadow-lg text-center"></div>
            <h4 id="notifyTitle" class="text-2xl font-black text-slate-900 uppercase tracking-tighter mb-2 text-center">Peringatan</h4>
            <p id="notifyText" class="text-sm font-medium text-slate-500 leading-relaxed mb-10 px-4 text-center"></p>
            <div id="notifyActions" class="flex gap-4 text-left"></div>
        </div>
    </div>

    <!-- MODAL EXPAND CATATAN -->
    <div id="commentExpandModal" class="fixed inset-0 z-[200] hidden items-center justify-center p-4 text-left">
        <div class="absolute inset-0 modal-backdrop" onclick="saveAndCloseCommentModal()"></div>
        <div class="relative bg-white rounded-[4rem] w-full max-w-5xl shadow-2xl overflow-hidden animate-pop-in flex flex-col max-h-[90vh] border-[12px] border-white text-left text-slate-900">
            <div class="p-12 border-b border-slate-50 flex justify-between items-center bg-slate-50/50 text-left">
                <div class="text-left">
                    <span class="text-[9px] font-black text-primary-600 uppercase tracking-widest text-left">Detail Analisis Verifikasi</span>
                    <h4 id="expandedCommentLabel" class="text-3xl font-black text-slate-900 uppercase leading-tight text-left">KOMPONEN</h4>
                </div>
                <button type="button" onclick="saveAndCloseCommentModal()" class="w-16 h-16 bg-white shadow-xl border border-slate-100 rounded-3xl flex items-center justify-center text-slate-400 hover:bg-rose-500 hover:text-white transition-all shrink-0 text-left text-center"><i class="fas fa-times text-xl text-left"></i></button>
            </div>
            <div class="p-12 flex-1 flex flex-col text-left">
                <textarea id="expandedTextarea" onkeydown="handleNumbering(event)" class="w-full flex-1 rounded-[3rem] border-2 border-primary-50 p-12 text-lg md:text-xl font-medium outline-none focus:border-primary-400 shadow-inner leading-relaxed min-h-[400px] bg-slate-50/20 focus:bg-white transition-all text-left" placeholder="Ketik catatan detail di sini..."></textarea>
                <div class="mt-10 flex justify-end text-left text-center">
                    <button type="button" onclick="saveAndCloseCommentModal()" class="bg-primary-600 text-white px-16 py-6 rounded-[2rem] font-black text-xs uppercase tracking-[0.3em] shadow-2xl shadow-primary-200 hover:bg-primary-900 transition-all flex items-center gap-5 text-left text-center">
                        <span>Simpan & Terapkan</span>
                        <i class="fas fa-check-circle text-left"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const categoryConfig = @json($categoryConfig);
        const storageBase = "{{ asset('storage') }}";
        let currentItem = null;
        let currentEditingKey = null;

        function showNotify({title, text, type = 'warning', onConfirm = null}) {
            const modal = document.getElementById('notifyModal');
            const icon = document.getElementById('notifyIcon');
            const titleEl = document.getElementById('notifyTitle');
            const textEl = document.getElementById('notifyText');
            const actions = document.getElementById('notifyActions');
            titleEl.innerText = title;
            textEl.innerText = text;
            if(type === 'warning') {
                icon.className = "w-20 h-20 rounded-3xl mx-auto mb-6 flex items-center justify-center text-3xl shadow-lg bg-amber-50 text-amber-500";
                icon.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
                actions.innerHTML = `<button onclick="closeNotify()" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl">Saya Mengerti</button>`;
            } else if(type === 'confirm') {
                icon.className = "w-20 h-20 rounded-3xl mx-auto mb-6 flex items-center justify-center text-3xl shadow-lg bg-primary-50 text-primary-600";
                icon.innerHTML = '<i class="fas fa-paper-plane"></i>';
                actions.innerHTML = `<button onclick="closeNotify()" class="flex-1 bg-slate-100 text-slate-500 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest">Batal</button><button id="finalConfirmBtn" class="flex-1 bg-primary-600 text-white py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl">Kirim</button>`;
                document.getElementById('finalConfirmBtn').onclick = () => { closeNotify(); if(onConfirm) onConfirm(); };
            }
            modal.classList.replace('hidden', 'flex');
        }

        function closeNotify() { document.getElementById('notifyModal').classList.replace('flex', 'hidden'); }

        function handleFormSubmit(e) {
            e.preventDefault();
            const selected = document.querySelectorAll('.score-input:checked');
            const note = document.getElementById('adminNote').value.trim();
            const chName = document.getElementById('chairmanName').value.trim();
            const chNip = document.getElementById('chairmanNip').value.trim();
            const totalCats = Object.keys(categoryConfig).length;

            if (selected.length < totalCats || !note || !chName || !chNip) {
                showNotify({
                    title: "Data Belum Lengkap",
                    text: "Mohon pastikan seluruh 7 komponen sudah dinilai, serta kolom pejabat penandatangan (Nama & NIP) dan kesimpulan sudah diisi.",
                    type: "warning"
                });
                return;
            }
            e.target.submit();
        }

        function confirmPublish(id, name) {
            showNotify({
                title: "Konfirmasi Kirim",
                text: `Kirim laporan untuk ${name} ke user? Data tidak bisa diubah lagi setelah dikirim.`,
                type: "confirm",
                onConfirm: () => { document.getElementById(`publish-form-${id}`).submit(); }
            });
        }

        function handleNumbering(e) {
            const textarea = e.target;
            const start = textarea.selectionStart;
            const textBefore = textarea.value.substring(0, start);
            const lines = textBefore.split('\n');
            const lastLine = lines[lines.length - 1];
            const patternNum = /^(\s*)(\d+)\.\s/;
            const patternAlpha = /^(\s*)([a-z])\.\s/;
            const patternBullet = /^(\s*)([-*•])\s/;
            if (e.key === 'Enter') {
                let match = lastLine.match(patternNum) || lastLine.match(patternAlpha) || lastLine.match(patternBullet);
                if (match) {
                    const indent = match[1]; 
                    const markerTotal = match[0]; 
                    if (lastLine.trim() === markerTotal.trim()) {
                        e.preventDefault();
                        const beforeLine = textBefore.substring(0, textBefore.length - lastLine.length);
                        textarea.value = beforeLine + textarea.value.substring(textarea.selectionEnd);
                        textarea.selectionStart = textarea.selectionEnd = beforeLine.length;
                        return;
                    }
                    e.preventDefault();
                    let nextMarker = "";
                    if (lastLine.match(patternNum)) nextMarker = indent + (parseInt(match[2]) + 1) + ". ";
                    else if (lastLine.match(patternAlpha)) nextMarker = indent + String.fromCharCode(match[2].charCodeAt(0) + 1) + ". ";
                    else if (lastLine.match(patternBullet)) nextMarker = indent + match[2] + " ";
                    const val = textarea.value;
                    textarea.value = val.substring(0, start) + "\n" + nextMarker + val.substring(textarea.selectionEnd);
                    textarea.selectionStart = textarea.selectionEnd = start + nextMarker.length + 1;
                }
            }
            if (e.key === 'Tab') {
                e.preventDefault();
                const isShift = e.shiftKey;
                if (!isShift) {
                    if (lastLine.match(patternNum)) replacePrefix(textarea, lastLine, patternNum, "    a. ", start);
                    else if (lastLine.match(patternAlpha)) replacePrefix(textarea, lastLine, patternAlpha, "        - ", start);
                } else {
                    if (lastLine.match(patternBullet)) replacePrefix(textarea, lastLine, patternBullet, "    a. ", start);
                    else if (lastLine.match(patternAlpha)) replacePrefix(textarea, lastLine, patternAlpha, "1. ", start);
                }
            }
        }

        function replacePrefix(textarea, lastLine, pattern, newPrefix, currentPos) {
            const text = textarea.value;
            const textBeforePos = text.substring(0, currentPos);
            const lines = textBeforePos.split('\n');
            lines[lines.length - 1] = lastLine.replace(pattern, newPrefix);
            const newTextBefore = lines.join('\n');
            textarea.value = newTextBefore + text.substring(currentPos);
            textarea.selectionStart = textarea.selectionEnd = newTextBefore.length;
        }

        function openCommentModal(key, label) {
            currentEditingKey = key;
            const sourceEl = key === 'admin_note' ? document.getElementById('adminNote') : document.querySelector(`textarea[name="comments[${key}]"]`);
            const targetEl = document.getElementById('expandedTextarea');
            document.getElementById('expandedCommentLabel').innerText = label;
            targetEl.value = sourceEl ? sourceEl.value : '';
            targetEl.readOnly = sourceEl ? sourceEl.readOnly : false;
            document.getElementById('commentExpandModal').classList.replace('hidden', 'flex');
            setTimeout(() => targetEl.focus(), 100);
        }

        function saveAndCloseCommentModal() {
            const val = document.getElementById('expandedTextarea').value;
            if (currentEditingKey === 'admin_note') { document.getElementById('adminNote').value = val; }
            else { document.querySelectorAll(`textarea[name="comments[${currentEditingKey}]"]`).forEach(t => t.value = val); }
            document.getElementById('commentExpandModal').classList.replace('flex', 'hidden');
        }

        function openEvaluateModal(item) {
            currentItem = item;
            const isFinal = (item.is_published == 1);
            const hasDraft = !!item.evaluator_scores;
            renderModalContent(item, isFinal || hasDraft);
            const toggleEditBtn = document.getElementById('toggleEditBtn');
            if (!isFinal && hasDraft) { toggleEditBtn.classList.replace('hidden', 'flex'); } else { toggleEditBtn.classList.add('hidden'); }
            document.getElementById('evaluateModal').classList.replace('hidden', 'flex');
            document.body.style.overflow = 'hidden';
        }

        function enableEditMode() { renderModalContent(currentItem, false); document.getElementById('toggleEditBtn').classList.add('hidden'); }

        function renderModalContent(item, isReadOnly) {
            const container = document.getElementById('evaluationBody');
            const adminNote = document.getElementById('adminNote');
            const chName = document.getElementById('chairmanName');
            const chNip = document.getElementById('chairmanNip');
            
            document.getElementById('evaluateForm').action = "/survailen/evaluate-process/" + item.id;
            document.getElementById('evalModalId').innerText = "ID: #" + item.id;
            document.getElementById('evalModalTitleText').innerText = (item.user.name || "").toUpperCase();
            
            adminNote.value = item.admin_note || '';
            adminNote.readOnly = isReadOnly;
            adminNote.classList.toggle('bg-slate-50', isReadOnly);

            // Populate Chairperson Info
            chName.value = item.chairman_name || '';
            chName.readOnly = isReadOnly;
            chName.classList.toggle('bg-slate-100', isReadOnly);
            chNip.value = item.chairman_nip || '';
            chNip.readOnly = isReadOnly;
            chNip.classList.toggle('bg-slate-100', isReadOnly);

            document.getElementById('submitBtn').style.display = isReadOnly ? 'none' : 'flex';
            
            const statusLabel = document.getElementById('statusLabel');
            if(item.is_published == 1) {
                statusLabel.innerText = 'FINAL (TERKIRIM)';
                statusLabel.className = 'px-3 py-1 bg-emerald-500 text-white text-[8px] font-black uppercase rounded-lg shadow-lg shadow-emerald-50 text-left';
            } else {
                statusLabel.innerText = isReadOnly ? 'TINJAU DRAF' : 'MODE EDIT';
                statusLabel.className = isReadOnly ? 'px-3 py-1 bg-amber-500 text-white text-[8px] font-black uppercase rounded-lg shadow-lg shadow-amber-50 text-left' : 'px-3 py-1 bg-primary-600 text-white text-[8px] font-black uppercase rounded-lg shadow-lg shadow-primary-50 text-left';
            }

            let filesHtml = '';
            if(item.admin_file) filesHtml += `<a href="${storageBase}/${item.admin_file}" target="_blank" class="p-5 bg-slate-900 text-white rounded-[2rem] flex items-center justify-between text-[10px] font-black uppercase shadow-xl hover:bg-black text-left text-center"><span>LAPORAN HASIL (LHS)</span><i class="fas fa-file-pdf text-amber-400 text-left"></i></a>`;
            if(item.certificate_file) filesHtml += `<a href="${storageBase}/${item.certificate_file}" target="_blank" class="p-5 bg-primary-600 text-white rounded-[2rem] flex items-center justify-between text-[10px] font-black uppercase shadow-xl hover:bg-primary-800 text-left text-center"><span>SERTIFIKAT AKREDITASI</span><i class="fas fa-award text-left"></i></a>`;
            document.getElementById('finalFilesArea').innerHTML = filesHtml || '<p class="text-[10px] font-bold text-slate-300 italic text-center py-6 bg-slate-50 rounded-[2rem] border-2 border-dashed border-slate-100 text-center">Berkas belum tersedia</p>';

            const evalScores = (typeof item.evaluator_scores === 'string') ? JSON.parse(item.evaluator_scores) : (item.evaluator_scores || {});
            const evalComments = (typeof item.evaluator_comments === 'string') ? JSON.parse(item.evaluator_comments) : (item.evaluator_comments || {});
            const selfScores = (typeof item.self_assessment_scores === 'string') ? JSON.parse(item.self_assessment_scores) : (item.self_assessment_scores || {});
            container.innerHTML = '';

            Object.keys(categoryConfig).forEach((key, idx) => {
                const cat = categoryConfig[key];
                const eScore = evalScores[key] || '';
                const eComment = evalComments[key] || '';
                const uScore = selfScores[key] || '-';
                const files = item.files ? item.files.filter(f => f.category_key === key) : [];
                let fileUI = files.map(f => `<a href="${storageBase}/${f.file_path}" target="_blank" class="flex items-center gap-2 bg-white border border-slate-100 px-4 py-2.5 rounded-[1rem] text-[9px] font-black uppercase hover:bg-primary-50 transition-all text-slate-500 hover:text-primary-600 shadow-sm text-left"><i class="fas fa-file-pdf text-rose-500 text-left"></i><span class="truncate max-w-[120px] text-left">${f.file_name}</span></a>`).join('') || '<div class="px-4 py-3 bg-slate-50 rounded-[1rem] border-2 border-dashed border-slate-100 text-[9px] font-bold text-slate-300 uppercase text-center w-full">Kosong</div>';
                const renderScores = (p) => {
                    if(isReadOnly) return `<div class="w-12 h-12 flex items-center justify-center rounded-[1.2rem] bg-primary-900 text-white font-black text-lg text-center shadow-lg text-center">${eScore || '-'}</div>`;
                    return `<div class="flex gap-2 justify-center">${[1, 2, 3, 4].map(s => `<input type="radio" name="scores[${key}]" id="${p}_${idx}_${s}" value="${s}" class="hidden score-input" onchange="syncScores('${key}', ${s})" ${eScore == s ? 'checked' : ''}><label for="${p}_${idx}_${s}" class="score-label w-11 h-11 flex items-center justify-center rounded-[1.1rem] border-2 border-slate-100 bg-white text-[13px] font-black text-slate-300 cursor-pointer hover:border-primary-300 hover:text-primary-500 shadow-sm text-center">${s}</label>`).join('')}</div>`;
                };
                container.innerHTML += `<div class="row-item bg-white grid grid-cols-1 lg:grid-cols-12 gap-0 items-stretch text-left px-12"><div class="lg:col-span-4 flex items-center gap-5 py-6 pr-6 border-r border-slate-100 text-left"><div class="w-12 h-12 bg-primary-50 text-primary-600 rounded-[1.2rem] flex items-center justify-center shrink-0 border border-primary-100 text-base text-center"><i class="fas ${cat.icon} text-center"></i></div><div class="overflow-hidden text-left"><p class="font-black text-slate-900 uppercase text-[10px] leading-tight mb-1.5 text-left">${cat.label}</p><span class="text-[8px] font-black text-primary-600 uppercase tracking-widest bg-primary-50 px-2 py-0.5 rounded text-left">Bobot: ${cat.bobot}%</span></div></div><div class="lg:col-span-3 flex flex-wrap gap-2 items-center p-6 border-r border-slate-100 bg-slate-50/10 text-left">${fileUI}</div><div class="lg:col-span-1 flex items-center justify-center p-6 border-r border-slate-100 text-center text-center"><div class="w-11 h-11 flex items-center justify-center rounded-[1rem] bg-slate-100 text-slate-400 font-black text-sm border border-slate-200 text-center text-center">${uScore}</div></div><div class="lg:col-span-2 flex items-center justify-center p-6 border-r border-slate-100 bg-slate-50/5 text-center text-center">${renderScores('dt')}</div><div class="lg:col-span-2 relative group p-6 text-left h-full text-left"><button type="button" onclick="openCommentModal('${key}', '${cat.label}')" class="absolute top-4 right-4 text-primary-600 opacity-0 group-hover:opacity-100 transition-all text-left text-center"><i class="fas fa-expand-alt text-[10px] text-left"></i></button><textarea name="comments[${key}]" rows="2" class="w-full text-[10px] font-bold p-3 rounded-[1rem] border border-slate-100 bg-slate-50/50 focus:bg-white focus:border-primary-200 outline-none leading-relaxed transition-all min-h-[60px] text-left" placeholder="Ketik catatan..." ${isReadOnly ? 'readonly' : ''}>${eComment}</textarea></div></div>`;
            });

            if (isReadOnly) { updateFinalDisplay(parseFloat(item.final_score) || 0, 7, true); } else { calc(); }
        }

        function syncScores(k, v) { document.querySelectorAll(`input[name="scores[${k}]"][value="${v}"]`).forEach(r => r.checked = true); calc(); }

        function updateFinalDisplay(pct, counted, isFull) {
            const displayPct = document.getElementById('finalPercentageDisplay');
            const displayPred = document.getElementById('predikatDisplay');
            if (displayPct) displayPct.innerText = Math.round(pct) + "%";
            document.getElementById('inputFinalScore').value = Math.round(pct);
            let p = pct >= 85 ? "A (UNGGUL)" : (pct >= 70 ? "B (BAIK SEKALI)" : (pct >= 55 ? "C (BAIK)" : "D (CUKUP)"));
            document.getElementById('inputPredikat').value = p;
            if (displayPred) {
                if (isFull || counted === 7) {
                    displayPred.innerText = p;
                    displayPred.className = "mt-10 text-sm font-black text-white uppercase tracking-[0.3em] bg-emerald-500 px-14 py-5 rounded-full border-4 border-emerald-50 text-center shadow-2xl shadow-emerald-100 animate-pop-in text-center";
                } else {
                    displayPred.innerText = `${counted}/7 Komponen Terisi`;
                    displayPred.className = "mt-10 text-sm font-black text-white uppercase tracking-[0.3em] bg-primary-600 px-14 py-5 rounded-full border-4 border-primary-50 text-center shadow-2xl shadow-primary-200 text-center";
                }
            }
        }

        function calc() {
            const selected = document.querySelectorAll('.score-input:checked');
            let total = 0, counted = new Set();
            selected.forEach(input => {
                const k = input.getAttribute('name').match(/\[(.*?)\]/)[1];
                if(!counted.has(k)) { total += (parseInt(input.value) * (categoryConfig[k]?.bobot || 0)); counted.add(k); }
            });
            updateFinalDisplay((total / 400) * 100, counted.size, false);
        }

        function closeEvaluateModal() { document.getElementById('evaluateModal').classList.add('hidden'); document.body.style.overflow = ''; }
    </script>
</body>
</html>