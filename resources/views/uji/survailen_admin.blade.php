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
                            50: '#f0fdf4',   // emerald-50
                            100: '#dcfce7',  // emerald-100
                            200: '#bbf7d0',  // emerald-200
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',  // emerald-600 (Main)
                            700: '#15803d',  // emerald-700
                            800: '#166534',  // emerald-800
                            900: '#14532d',  // emerald-900
                        }
                    },
                    animation: { 
                        'pop-in': 'popIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards',
                        'fade-up': 'fadeUp 0.4s ease-out forwards',
                    },
                    keyframes: {
                        popIn: { '0%': { opacity: '0', transform: 'scale(0.98) translateY(8px)' }, '100%': { opacity: '1', transform: 'scale(1) translateY(0)' } },
                        fadeUp: { '0%': { opacity: '0', transform: 'translateY(15px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } }
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
            background-color: #16a34a;
            color: white;
            border-color: #16a34a;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px -3px rgba(22, 163, 74, 0.3);
        }
        .score-label { transition: all 0.2s ease; }
        
        @media (max-width: 768px) {
            .modal-content-area { padding-bottom: 100px; }
        }
        
        .glass-card { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="bg-[#f4f7ff] text-slate-800 antialiased overflow-hidden text-left">

    @php
        $needReview = $audits->where('status', 'verification')->count();
        $completed = $audits->where('status', 'completed')->count();
        $total = $audits->count();

        $categoryConfig = [
            'file_legalitas' => ['label' => 'Aspek Legalitas & Perizinan', 'bobot' => 10, 'icon' => 'fa-building', 'color' => 'emerald'],
            'file_mutu'      => ['label' => 'Sistem Manajemen Mutu (SMM)', 'bobot' => 20, 'icon' => 'fa-check-double', 'color' => 'indigo'],
            'file_rekaman'   => ['label' => 'Rekaman & Laporan Implementasi', 'bobot' => 20, 'icon' => 'fa-history', 'color' => 'purple'],
            'file_kinerja'   => ['label' => 'Laporan Kinerja & KAK', 'bobot' => 5, 'icon' => 'fa-chart-line', 'color' => 'cyan'],
            'file_sdm'       => ['label' => 'Sumber Daya Manusia (SDM)', 'bobot' => 10, 'icon' => 'fa-users', 'color' => 'teal'],
            'file_sarpras'   => ['label' => 'Sarana & Prasarana Penunjang', 'bobot' => 15, 'icon' => 'fa-tools', 'color' => 'amber'],
            'file_kurikulum' => ['label' => 'Bahan Uji', 'bobot' => 20, 'icon' => 'fa-book-open', 'color' => 'rose']
        ];
    @endphp

    <div class="flex h-screen overflow-hidden w-full">
        <!-- Sidebar -->
        <div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 z-40 hidden lg:hidden bg-slate-900/40 backdrop-blur-sm transition-opacity duration-300"></div>
        
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-primary-900 transform -translate-x-full transition-transform duration-300 lg:translate-x-0 lg:static flex flex-col h-full border-r border-emerald-900/20">
            @include('components.uji-sidebar')
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden relative w-full">
            
            <!-- Mobile Header Bar -->
            <div class="lg:hidden bg-white border-b border-slate-200 px-5 py-4 flex items-center justify-between sticky top-0 z-30 shadow-sm shrink-0">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="p-2 text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                        <i class="fas fa-bars-staggered text-xl"></i>
                    </button>
                    <span class="font-black text-primary-900 text-sm tracking-tighter uppercase">SI-MUTU <span class="text-primary-600">ADMIN</span></span>
                </div>
            </div>

            <!-- Header Desktop -->
            <div class="hidden lg:block shrink-0">
                @include('components.uji-header', ['title' => 'Evaluasi Surveilan', 'subtitle' => 'Panel Verifikasi & Penilaian Instrumen'])
            </div>

            <main class="flex-1 overflow-y-auto p-4 md:p-8 space-y-8 no-scrollbar">
                
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 animate-fade-up">
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex items-center justify-between group hover:shadow-xl transition-all">
                        <div class="text-left">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Pengajuan</p>
                            <h3 class="text-3xl font-black text-slate-900 mt-1 leading-none">{{ $total }}</h3>
                        </div>
                        <div class="w-14 h-14 bg-primary-50 text-primary-600 rounded-2xl flex items-center justify-center text-xl shadow-inner group-hover:scale-110 transition-transform"><i class="fas fa-inbox"></i></div>
                    </div>
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex items-center justify-between group hover:shadow-xl transition-all">
                        <div class="text-left">
                            <p class="text-[10px] font-black text-amber-500 uppercase tracking-widest">Menunggu Verifikasi</p>
                            <h3 class="text-3xl font-black text-slate-900 mt-1 leading-none">{{ $needReview }}</h3>
                        </div>
                        <div class="w-14 h-14 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center text-xl shadow-inner group-hover:scale-110 transition-transform"><i class="fas fa-hourglass-half"></i></div>
                    </div>
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex items-center justify-between group hover:shadow-xl transition-all sm:col-span-2 lg:col-span-1">
                        <div class="text-left">
                            <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">Penilaian Selesai</p>
                            <h3 class="text-3xl font-black text-slate-900 mt-1 leading-none">{{ $completed }}</h3>
                        </div>
                        <div class="w-14 h-14 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center text-xl shadow-inner group-hover:scale-110 transition-transform"><i class="fas fa-check-double"></i></div>
                    </div>
                </div>

                <!-- Antrian List -->
                <div class="space-y-6 animate-fade-up" style="animation-delay: 0.1s">
                    <div class="flex items-center gap-4 px-2">
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em] whitespace-nowrap">Antrian Verifikasi</h4>
                        <div class="flex-1 h-px bg-slate-200"></div>
                    </div>

                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 md:gap-5">
                        @forelse($audits as $item)
                        <div class="bg-white rounded-[2.5rem] border border-slate-100 p-6 md:p-8 flex flex-col md:flex-row items-center gap-6 group hover:border-primary-400 transition-all shadow-sm">
                            <div class="flex-1 w-full text-left">
                                <div class="flex items-center gap-5 mb-4">
                                    <div class="w-12 h-12 md:w-14 md:h-14 rounded-2xl bg-primary-900 text-white flex items-center justify-center font-black text-xl shadow-lg shrink-0 group-hover:bg-primary-600 transition-colors">
                                        {{ substr($item->user->name, 0, 1) }}
                                    </div>
                                    <div class="overflow-hidden">
                                        <h4 class="font-black text-slate-900 uppercase tracking-tight text-sm md:text-base leading-tight truncate">{{ $item->user->name }}</h4>
                                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-1">ID: #{{ $item->id }} • {{ $item->updated_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <span class="px-4 py-1.5 rounded-full text-[8px] font-black uppercase tracking-widest border {{ $item->status == 'completed' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-amber-50 text-amber-600 border-amber-100' }}">
                                    {{ $item->status == 'verification' ? 'MENUNGGU REVIEW' : 'PENILAIAN SELESAI' }}
                                </span>
                            </div>
                            
                            <div class="flex items-center gap-6 w-full md:w-auto shrink-0 justify-between md:justify-end border-t md:border-t-0 pt-6 md:pt-0 border-slate-100">
                                @if($item->status == 'completed')
                                <div class="text-right pr-6 border-r border-slate-100">
                                    <span class="text-3xl font-black text-primary-600 leading-none">{{ $item->predikat }}</span>
                                    <p class="text-[9px] font-bold text-slate-400 mt-1 tracking-widest">{{ number_format($item->final_score, 1) }}%</p>
                                </div>
                                @endif
                                <button onclick='openEvaluateModal(@json($item))' class="bg-primary-900 text-white px-8 py-4 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-primary-600 transition-all shadow-lg flex items-center gap-3 active:scale-95 w-full md:w-auto justify-center">
                                    <span>{{ $item->status == 'completed' ? 'RINCIAN' : 'EVALUASI' }}</span>
                                    <i class="fas fa-arrow-right text-[9px] opacity-40"></i>
                                </button>
                            </div>
                        </div>
                        @empty
                        <div class="col-span-full py-20 text-center opacity-30 italic font-black uppercase text-[10px] tracking-[0.5em]">Belum Ada Pengajuan Masuk</div>
                        @endforelse
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal Evaluasi -->
    <div id="evaluateModal" class="fixed inset-0 z-[110] hidden items-center justify-center p-3 md:p-6">
        <div class="absolute inset-0 modal-backdrop" onclick="closeEvaluateModal()"></div>
        <div class="relative bg-white rounded-[2.5rem] md:rounded-[3.5rem] w-full max-w-7xl max-h-[96vh] flex flex-col shadow-2xl overflow-hidden animate-pop-in border-[10px] border-white">
            
            <div class="p-6 md:p-10 flex justify-between items-start shrink-0 border-b border-slate-50 relative">
                <div class="overflow-hidden pr-12 text-left">
                    <div class="flex items-center gap-2 mb-2">
                        <span id="statusLabel" class="px-2 py-0.5 bg-primary-600 text-white text-[7px] font-black uppercase tracking-widest rounded">Status</span>
                        <p id="evalModalId" class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"></p>
                    </div>
                    <h3 class="text-xl md:text-3xl font-black text-slate-900 uppercase tracking-tighter leading-tight">Penilaian Desk Evaluation</h3>
                    <p id="evalModalTitle" class="text-[10px] md:text-[11px] text-slate-400 font-bold uppercase tracking-[0.1em] mt-1.5 truncate max-w-lg"></p>
                </div>
                <button onclick="closeEvaluateModal()" class="w-10 h-10 md:w-12 md:h-12 bg-slate-100 rounded-2xl flex items-center justify-center text-slate-400 hover:bg-primary-900 hover:text-white transition-all active:scale-90 shrink-0"><i class="fas fa-times"></i></button>
            </div>
            
            <form id="evaluateForm" method="POST" action="" enctype="multipart/form-data" class="flex-1 flex flex-col overflow-hidden bg-white">
                @csrf
                <div class="flex-1 overflow-y-auto p-5 md:p-10 space-y-10 no-scrollbar modal-content-area">
                    
                    <!-- Table Desktop -->
                    <div class="hidden lg:block bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs">
                                <thead class="bg-slate-50 text-slate-400 font-black uppercase text-[9px] tracking-[0.2em] border-b">
                                    <tr>
                                        <th class="px-8 py-5 text-left">Komponen Instrumen</th>
                                        <th class="px-6 py-5 text-center">Berkas Lampiran</th>
                                        <th class="px-4 py-5 text-center">Self</th>
                                        <th class="px-8 py-5 text-center">Skor Asesor</th>
                                        <th class="px-8 py-5 text-left">Catatan Penilaian</th>
                                    </tr>
                                </thead>
                                <tbody id="evaluationTableBody" class="divide-y divide-slate-50"></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Card Mobile List -->
                    <div id="evaluationCardList" class="lg:hidden space-y-4"></div>

                    <!-- Footer Info Panel -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-10 pt-4">
                        <div class="bg-primary-600 p-6 md:p-8 rounded-3xl text-white flex flex-col justify-center items-center relative overflow-hidden shadow-xl shadow-primary-100">
                            <div class="absolute top-0 right-0 p-4 opacity-10 pointer-events-none"><i class="fas fa-check-double text-[6rem]"></i></div>
                            <p class="text-[9px] font-bold uppercase tracking-[0.2em] opacity-70 mb-1">Persentase Akhir Akumulasi</p>
                            <h4 id="finalPercentageDisplay" class="text-3xl md:text-4xl font-black leading-none tracking-tighter">0%</h4>
                            <p id="predikatDisplay" class="text-sm md:text-base font-black text-white uppercase mt-4 tracking-[0.3em] bg-white/10 px-8 py-2.5 rounded-full border border-white/20">Menunggu Skor</p>
                        </div>

                        <div class="space-y-6 text-left">
                            <div class="bg-white p-6 md:p-8 rounded-[2rem] border border-slate-100 shadow-sm space-y-4">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block ml-1">Unggah Laporan Hasil (LHS)</label>
                                <div id="finalFilesArea" class="grid grid-cols-1 gap-3"></div>
                            </div>
                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 block">Rekomendasi & Kesimpulan Umum</label>
                                <textarea id="adminNote" name="admin_note" rows="4" class="w-full rounded-[2rem] border border-slate-200 p-6 text-xs md:text-sm font-bold outline-none focus:border-primary-500 shadow-inner bg-slate-50/50 focus:bg-white transition-all text-left" placeholder="Tuliskan catatan evaluasi menyeluruh..." required></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="modalActionFooter" class="p-6 md:p-10 bg-white border-t border-slate-50 flex flex-col sm:flex-row justify-end items-center gap-4 shrink-0">
                    <button type="button" onclick="closeEvaluateModal()" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 transition-colors order-2 sm:order-1 py-3 px-6">Batalkan Penilaian</button>
                    <button id="submitBtn" type="submit" class="w-full sm:w-auto bg-primary-600 text-white px-12 py-5 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] shadow-xl hover:bg-primary-900 transition-all flex items-center justify-center gap-4 active:scale-95 order-1 sm:order-2">
                        <span>Publikasikan Hasil</span>
                        <i class="fas fa-paper-plane text-[9px] opacity-40"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const categoryConfig = @json($categoryConfig);
        const storageBase = "{{ asset('storage') }}";

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if(sidebar && overlay) {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            }
        }

        function openEvaluateModal(item) {
            const isReadOnly = (item.status === 'completed');
            const tableBody = document.getElementById('evaluationTableBody');
            const cardList = document.getElementById('evaluationCardList');
            const form = document.getElementById('evaluateForm');
            const adminNote = document.getElementById('adminNote');
            const submitBtn = document.getElementById('submitBtn');
            const finalFilesArea = document.getElementById('finalFilesArea');
            const statusLabel = document.getElementById('statusLabel');

            form.action = "/survailen/evaluate-process/" + item.id;
            document.getElementById('evalModalId').innerText = "ID: #" + item.id;
            document.getElementById('evalModalTitle').innerText = (item.user.name || "").toUpperCase() + " • " + (item.title || "").toUpperCase();
            
            if(adminNote) {
                adminNote.value = item.admin_note || '';
                adminNote.readOnly = isReadOnly;
                adminNote.className = isReadOnly 
                    ? "w-full rounded-[2rem] border border-slate-200 p-6 text-xs md:text-sm font-bold outline-none bg-slate-100 text-slate-500 shadow-inner text-left" 
                    : "w-full rounded-[2rem] border border-slate-200 p-6 text-xs md:text-sm font-bold outline-none focus:border-primary-500 shadow-inner bg-slate-50/50 focus:bg-white transition-all text-left";
            }

            if(submitBtn) submitBtn.style.display = isReadOnly ? 'none' : 'flex';
            if(statusLabel) {
                statusLabel.innerText = isReadOnly ? 'TERPUBLIKASI' : 'REVIEW ASESOR';
                statusLabel.className = isReadOnly 
                    ? 'px-2 py-0.5 bg-emerald-500 text-white text-[7px] font-black uppercase tracking-widest rounded' 
                    : 'px-2 py-0.5 bg-primary-600 text-white text-[7px] font-black uppercase tracking-widest rounded';
            }

            if(finalFilesArea) {
                if(isReadOnly) {
                    finalFilesArea.innerHTML = `
                        <div class="flex flex-col gap-2">
                            <a href="${storageBase}/${item.admin_file}" target="_blank" class="p-4 bg-slate-900 text-white rounded-xl flex items-center justify-between text-[9px] font-black uppercase tracking-widest group shadow-md">
                                <span class="flex items-center gap-3"><i class="fas fa-file-medical"></i> Buka Laporan LHS</span> 
                                <i class="fas fa-external-link-alt opacity-40"></i>
                            </a>
                            ${item.certificate_file ? `
                            <a href="${storageBase}/${item.certificate_file}" target="_blank" class="p-4 bg-amber-500 text-white rounded-xl flex items-center justify-between text-[9px] font-black uppercase tracking-widest group shadow-md">
                                <span class="flex items-center gap-3"><i class="fas fa-award"></i> Buka Sertifikat</span> 
                                <i class="fas fa-external-link-alt opacity-40"></i>
                            </a>
                            ` : ''}
                        </div>
                    `;
                } else {
                    finalFilesArea.innerHTML = `
                        <div class="relative group w-full">
                            <div class="p-4 bg-white rounded-xl border-2 border-dashed border-slate-200 text-center group-hover:border-primary-400 transition-all">
                                <span id="lbl_adm" class="text-[9px] font-black text-slate-400 uppercase truncate block leading-tight">Unggah Laporan Hasil Survailen (PDF)</span>
                            </div>
                            <input type="file" name="admin_file" onchange="updateLabel(this, 'lbl_adm')" class="absolute inset-0 opacity-0 cursor-pointer" required>
                        </div>
                    `;
                }
            }

            const selfScores = (typeof item.self_assessment_scores === 'string') ? JSON.parse(item.self_assessment_scores) : (item.self_assessment_scores || {});
            const evalScores = (typeof item.evaluator_scores === 'string') ? JSON.parse(item.evaluator_scores) : (item.evaluator_scores || {});
            const evalComments = (typeof item.evaluator_comments === 'string') ? JSON.parse(item.evaluator_comments) : (item.evaluator_comments || {});

            tableBody.innerHTML = '';
            cardList.innerHTML = '';

            Object.keys(categoryConfig).forEach((key, idx) => {
                const cat = categoryConfig[key];
                const uScore = selfScores[key] || '-';
                const eScore = evalScores[key] || '-';
                const eComment = evalComments[key] || '';
                
                const relatedFiles = item.files ? item.files.filter(f => f.category_key === key) : [];
                let fileUI = '';
                if(relatedFiles.length > 0) {
                    relatedFiles.forEach(file => {
                        fileUI += `
                            <a href="${storageBase}/${file.file_path}" target="_blank" class="flex items-center gap-3 bg-white px-3 py-2 rounded-lg mb-1 shadow-sm border border-slate-100 hover:border-primary-300 transition-all">
                                <i class="fas fa-file-pdf text-rose-500 text-[10px]"></i>
                                <span class="text-[9px] font-black text-slate-600 truncate max-w-[100px] uppercase tracking-tighter">${file.file_name}</span>
                            </a>`;
                    });
                } else {
                    fileUI = '<span class="text-slate-300 italic text-[9px] font-bold uppercase py-2">TIDAK ADA BERKAS</span>';
                }

                let asesorScoreUI = '';
                if (isReadOnly) {
                    asesorScoreUI = `<div class="w-10 h-10 inline-flex items-center justify-center rounded-xl bg-primary-900 text-white font-black text-[11px] shadow-lg shadow-primary-100">${eScore}</div>`;
                } else {
                    asesorScoreUI = `
                        <div class="flex justify-center gap-1.5">
                            ${[1, 2, 3, 4].map(s => `
                                <div class="relative">
                                    <input type="radio" name="scores[${key}]" id="s_${idx}_${s}" value="${s}" class="hidden score-input" 
                                           onchange="calc()" required ${eScore == s ? 'checked' : ''}>
                                    <label for="s_${idx}_${s}" class="score-label w-9 h-9 md:w-10 md:h-10 flex items-center justify-center rounded-lg md:rounded-xl border border-slate-100 bg-slate-50 text-[10px] font-black text-slate-300 cursor-pointer hover:bg-slate-100">${s}</label>
                                </div>
                            `).join('')}
                        </div>`;
                }

                // Desktop Row
                tableBody.innerHTML += `
                    <tr class="hover:bg-slate-50/50 transition-colors text-left">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4 text-left">
                                <div class="w-10 h-10 bg-primary-50 text-primary-600 rounded-xl flex items-center justify-center shrink-0 border border-primary-100"><i class="fas ${cat.icon} text-sm"></i></div>
                                <div>
                                    <p class="font-black text-slate-800 uppercase text-[10px] tracking-tight leading-tight">${cat.label}</p>
                                    <span class="text-[8px] font-bold text-primary-500 uppercase tracking-widest mt-1 inline-block">Bobot: ${cat.bobot}%</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5"><div class="flex flex-col items-center">${fileUI}</div></td>
                        <td class="px-4 py-5 text-center"><div class="w-10 h-10 inline-flex items-center justify-center rounded-xl bg-slate-100 text-slate-400 font-black text-[11px]">${uScore}</div></td>
                        <td class="px-8 py-5 text-center">${asesorScoreUI}</td>
                        <td class="px-8 py-5">
                            <textarea id="comment_${key}" name="comments[${key}]" rows="2" class="w-full text-[10px] font-bold p-4 rounded-xl border border-slate-100 outline-none bg-slate-50/50 focus:bg-white transition-all text-left" 
                                      placeholder="Tambahkan catatan..." ${isReadOnly ? 'readonly' : ''}>${eComment}</textarea>
                        </td>
                    </tr>
                `;

                // Mobile Card
                cardList.innerHTML += `
                    <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100 space-y-5 shadow-sm text-left">
                        <div class="flex items-center gap-4 text-left">
                             <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-primary-600 shadow-sm shrink-0 border border-slate-100"><i class="fas ${cat.icon}"></i></div>
                             <div class="overflow-hidden">
                                <h5 class="text-[10px] font-black text-slate-900 uppercase tracking-tight leading-tight truncate">${cat.label}</h5>
                                <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mt-1">Bobot: ${cat.bobot}% • Self: ${uScore}</p>
                             </div>
                        </div>
                        <div class="flex flex-wrap gap-2 py-2">${fileUI}</div>
                        <div class="space-y-3">
                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Penilaian Asesor:</p>
                            <div class="flex gap-2">${asesorScoreUI}</div>
                        </div>
                        <textarea rows="2" class="w-full text-[10px] font-bold p-4 rounded-xl shadow-inner bg-white border border-transparent outline-none text-left" 
                                  placeholder="Catatan..." ${isReadOnly ? 'readonly' : ''} 
                                  oninput="document.getElementById('comment_${key}').value = this.value">${eComment}</textarea>
                    </div>
                `;
            });

            document.getElementById('evaluateModal').classList.replace('hidden', 'flex');
            document.body.style.overflow = 'hidden';
            
            if (isReadOnly) {
                updateFinalDisplay(parseFloat(item.final_score || 0), Object.keys(categoryConfig).length, true);
            } else {
                calc();
            }
        }

        function updateFinalDisplay(percentage, filledCount, isFull) {
            const display = document.getElementById('finalPercentageDisplay');
            const predikatDisplay = document.getElementById('predikatDisplay');
            const totalCats = Object.keys(categoryConfig).length;

            if(!display || !predikatDisplay) return;

            display.innerText = Math.round(percentage) + "%";
            
            if (isFull || filledCount === totalCats) {
                let predikat = "";
                if (percentage >= 85) predikat = "A (UNGGUL)";
                else if (percentage >= 70) predikat = "B (BAIK SEKALI)";
                else if (percentage >= 55) predikat = "C (BAIK)";
                else predikat = "D (CUKUP)";
                
                predikatDisplay.innerText = predikat;
                // Diperbesar agar terbaca (sm/base)
                predikatDisplay.className = "text-sm md:text-base font-black text-white uppercase mt-4 tracking-[0.3em] bg-white/20 px-8 py-2.5 rounded-full border border-white/30";
            } else {
                predikatDisplay.innerText = filledCount + " / " + totalCats + " KOMPONEN TERISI";
                predikatDisplay.className = "text-[8px] font-black text-white/50 uppercase mt-4 tracking-[0.2em]";
            }
        }

        function calc() {
            const selected = document.querySelectorAll('.score-input:checked');
            let totalWeighted = 0;
            selected.forEach(input => {
                const nameAttr = input.getAttribute('name');
                const keyMatch = nameAttr.match(/\[(.*?)\]/);
                if(keyMatch && keyMatch[1]) {
                    const key = keyMatch[1];
                    const cat = categoryConfig[key];
                    if(cat) totalWeighted += (parseInt(input.value) * cat.bobot);
                }
            });

            const percentage = (totalWeighted / 400) * 100;
            updateFinalDisplay(percentage, selected.length, false);
        }

        function updateLabel(input, id) {
            if(input.files && input.files[0]) {
                const label = document.getElementById(id);
                if(label) {
                    label.innerText = input.files[0].name;
                    label.classList.replace('text-slate-400', 'text-primary-600');
                    label.classList.add('font-black');
                    input.parentElement.classList.replace('border-slate-200', 'border-primary-400');
                }
            }
        }

        function closeEvaluateModal() {
            document.getElementById('evaluateModal').classList.replace('flex', 'hidden');
            document.body.style.overflow = '';
        }
    </script>
</body>
</html>