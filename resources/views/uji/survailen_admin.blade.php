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
                    colors: { primary: '#1e3a8a', secondary: '#2563eb' },
                    animation: { 
                        'pop-in': 'popIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards',
                        'slide-up': 'slideUp 0.4s ease-out forwards',
                    },
                    keyframes: {
                        popIn: { '0%': { opacity: '0', transform: 'scale(0.97) translateY(5px)' }, '100%': { opacity: '1', transform: 'scale(1) translateY(0)' } },
                        slideUp: { '0%': { opacity: '0', transform: 'translateY(10px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } }
                    }
                }
            }
        }
    </script>

    <style>
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #f8fafc; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .modal-backdrop { backdrop-filter: blur(10px); background-color: rgba(15, 23, 42, 0.4); }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .score-1:checked + label { background-color: #ef4444; color: white; border-color: #ef4444; }
        .score-2:checked + label { background-color: #f59e0b; color: white; border-color: #f59e0b; }
        .score-3:checked + label { background-color: #3b82f6; color: white; border-color: #3b82f6; }
        .score-4:checked + label { background-color: #10b981; color: white; border-color: #10b981; }
        .score-label { transition: all 0.2s ease; border-width: 2px; }
        .glass-overlay { background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); }
        
        /* Disabled state styling */
        input:disabled + label { cursor: default; opacity: 0.7; }
        input:disabled:not(:checked) + label { border-color: #f1f5f9; background: #f8fafc; color: #cbd5e1; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased overflow-hidden text-left">

    @php
        $needReview = $audits->where('status', 'verification')->count();
        $completed = $audits->where('status', 'completed')->count();
        $total = $audits->count();
    @endphp

    {{-- === MODAL BERHASIL === --}}
    @if(session('success'))
    <div id="successModal" class="fixed inset-0 z-[120] flex items-center justify-center p-4 text-center">
        <div class="absolute inset-0 modal-backdrop" onclick="closeSuccessModal()"></div>
        <div class="relative bg-white rounded-[2.5rem] w-full max-w-sm p-8 shadow-2xl animate-pop-in border border-white">
            <div class="w-16 h-16 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center mx-auto mb-6 text-2xl shadow-inner border border-emerald-100/50">
                <i class="fas fa-circle-check"></i>
            </div>
            <h3 class="text-xl font-black text-slate-900 uppercase tracking-tighter mb-2">Simpan Berhasil</h3>
            <p class="text-slate-500 text-[11px] font-medium leading-relaxed mb-8 px-4 text-center">
                {{ session('success') }}
            </p>
            <button onclick="closeSuccessModal()" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-emerald-600 transition-all shadow-xl active:scale-95 text-center">
                Tutup
            </button>
        </div>
    </div>
    @endif

    {{-- === MODAL GAGAL === --}}
    @if($errors->any())
    <div id="errorModal" class="fixed inset-0 z-[120] flex items-center justify-center p-4 text-center">
        <div class="absolute inset-0 modal-backdrop" onclick="closeErrorModal()"></div>
        <div class="relative bg-white rounded-[2.5rem] w-full max-w-sm p-8 shadow-2xl animate-pop-in border border-white">
            <div class="w-16 h-16 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center mx-auto mb-6 text-2xl shadow-inner border border-rose-100/50">
                <i class="fas fa-circle-exclamation"></i>
            </div>
            <h3 class="text-xl font-black text-slate-900 uppercase tracking-tighter mb-2">Gagal Simpan</h3>
            <div class="text-slate-500 text-[11px] font-bold leading-relaxed mb-8 text-left bg-slate-50 p-4 rounded-2xl border border-slate-100 no-scrollbar overflow-y-auto max-h-32">
                <ul class="list-disc ml-4 space-y-1">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
            <button onclick="closeErrorModal()" class="w-full bg-rose-600 text-white py-4 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-slate-900 transition-all shadow-xl active:scale-95 text-center">
                Perbaiki
            </button>
        </div>
    </div>
    @endif

    <div class="flex h-screen overflow-hidden">
        
        <!-- MOBILE OVERLAY -->
        <div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 z-40 hidden lg:hidden glass-overlay transition-opacity duration-300"></div>

        <!-- SIDEBAR -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-primary transform -translate-x-full transition-transform duration-300 lg:translate-x-0 lg:static flex flex-col h-full border-r border-white/5">
            @include('components.uji-sidebar')
        </aside>

        <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
            
            <!-- === MOBILE HEADER BAR === -->
            <div class="lg:hidden bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between sticky top-0 z-30 shadow-sm">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="p-2 text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <span class="font-bold text-slate-800 text-sm tracking-tight uppercase">SI-MUTU <span class="text-teal-600">DKKN</span></span>
                </div>
                <div class="w-8 h-8 rounded-xl bg-teal-100 flex items-center justify-center text-teal-600 text-xs font-bold border border-teal-200 shadow-sm">
                    {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                </div>
            </div>

            <!-- DESKTOP HEADER -->
            <div class="hidden lg:block">
                @include('components.uji-header', ['title' => 'Evaluasi Surveilan', 'subtitle' => 'Penilaian Akreditasi Lembaga Pelatihan'])
            </div>

            <main class="flex-1 overflow-y-auto p-4 lg:p-10 space-y-8 no-scrollbar text-left">
                
                {{-- STATS CARDS --}}
                <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-4 lg:gap-6 animate-slide-up">
                    <div class="bg-white rounded-[2rem] p-6 border border-slate-100 shadow-sm flex flex-col items-center md:flex-row md:items-center gap-4 lg:gap-5 text-center md:text-left">
                        <div class="w-14 h-14 bg-teal-50 text-teal-600 rounded-2xl flex items-center justify-center text-xl shadow-inner shrink-0"><i class="fas fa-archive"></i></div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Total Arsip</p>
                            <h2 class="text-3xl font-black text-slate-800 leading-tight">{{ $total }}</h2>
                        </div>
                    </div>
                    <div class="bg-white rounded-[2rem] p-6 border border-slate-100 shadow-sm flex flex-col items-center md:flex-row md:items-center gap-4 lg:gap-5 text-center md:text-left">
                        <div class="w-14 h-14 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center text-xl shadow-inner shrink-0"><i class="fas fa-clock"></i></div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Perlu Penilaian</p>
                            <h2 class="text-3xl font-black text-slate-800 leading-tight">{{ $needReview }}</h2>
                        </div>
                    </div>
                    <div class="bg-white rounded-[2rem] p-6 border border-slate-100 shadow-sm flex flex-col items-center md:flex-row md:items-center gap-4 lg:gap-5 text-center md:text-left">
                        <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-xl shadow-inner shrink-0"><i class="fas fa-check-double"></i></div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Selesai</p>
                            <h2 class="text-3xl font-black text-slate-800 leading-tight">{{ $completed }}</h2>
                        </div>
                    </div>
                </div>

                {{-- ANTRIAN --}}
                <div class="max-w-6xl mx-auto space-y-4 animate-slide-up text-left" style="animation-delay: 0.1s">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em] px-2 text-center md:text-left">Antrian Evaluasi</h4>
                    <div class="grid grid-cols-1 gap-4">
                        @forelse($audits as $item)
                        <div class="bg-white rounded-[2rem] border border-slate-100 p-6 lg:p-8 hover:shadow-xl transition-all group relative overflow-hidden text-center md:text-left">
                            <div class="flex flex-col md:flex-row items-center justify-between gap-6 relative z-10 text-center md:text-left">
                                <div class="flex flex-col md:flex-row items-center gap-5 flex-1 text-center md:text-left">
                                    <div class="w-16 h-16 rounded-2xl bg-slate-50 flex items-center justify-center font-black text-primary text-xl uppercase shadow-inner border border-slate-100 shrink-0">
                                        {{ substr($item->user->name, 0, 1) }}
                                    </div>
                                    <div class="flex flex-col items-center md:items-start text-center md:text-left">
                                        <h4 class="font-black text-slate-800 uppercase tracking-tight text-lg lg:text-xl">{{ $item->user->name }}</h4>
                                        <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mt-2">
                                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">ID #{{ substr($item->id, 0, 8) }}</span>
                                            <div class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-[0.1em] border {{ $item->status == 'completed' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-amber-50 text-amber-600 border-amber-100' }}">
                                                {{ $item->status == 'verification' ? 'PENDING EVALUASI' : 'SELESAI / TERBIT' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" data-submission='@json($item)' onclick="openEvaluateModal(this)" class="w-full md:w-auto bg-slate-900 text-white px-10 py-4 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-emerald-900 transition-all shadow-xl active:scale-95">
                                    {{ $item->status == 'completed' ? 'LIHAT DETAIL' : 'MULAI PENILAIAN' }}
                                </button>
                            </div>
                        </div>
                        @empty
                        <div class="py-24 text-center bg-white rounded-[3rem] border border-dashed border-slate-200 text-slate-300">
                            <i class="fas fa-inbox text-5xl mb-4 opacity-30"></i>
                            <p class="font-black uppercase text-[10px] tracking-[0.3em]">Belum Ada Pengajuan</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <div class="mt-8 text-center text-xs text-slate-400">
                    &copy; 2026 Sistem Informasi Jaminan Mutu Ketenaganukliran
                </div>
            </main>
        </div>
    </div>

    {{-- MODAL EVALUASI --}}
    <div id="evaluateModal" class="fixed inset-0 z-[110] hidden items-center justify-center p-3 lg:p-4 text-left">
        <div class="absolute inset-0 modal-backdrop" onclick="closeEvaluateModal()"></div>
        <div class="relative bg-white rounded-[2rem] lg:rounded-[2.5rem] w-full max-w-6xl max-h-[92vh] flex flex-col shadow-2xl overflow-hidden animate-pop-in border border-white text-left">
            
            <div id="modalHeader" class="bg-primary p-6 md:p-8 text-white flex justify-between items-center shrink-0 transition-colors duration-500">
                <div class="text-left">
                    <h3 id="modalHeaderTitle" class="text-lg lg:text-xl font-black uppercase tracking-tight text-left">Instrumen Evaluasi Survailen</h3>
                    <p id="evalModalTitle" class="text-[10px] text-teal-200 font-bold uppercase tracking-widest mt-1.5 flex items-center gap-2 text-left"><i class="fas fa-university"></i> <span></span></p>
                </div>
                <button onclick="closeEvaluateModal()" class="w-11 h-11 bg-white/10 rounded-2xl flex items-center justify-center hover:bg-rose-500 transition-all"><i class="fas fa-times text-lg"></i></button>
            </div>
            
            <form id="evaluateForm" method="POST" action="" enctype="multipart/form-data" class="flex-1 flex flex-col overflow-hidden bg-slate-50/30 text-left">
                @csrf
                <div class="flex-1 overflow-y-auto p-5 lg:p-10 space-y-8 no-scrollbar text-left">
                    
                    <div id="progressContainer" class="bg-white p-5 rounded-2xl border border-slate-100 flex items-center justify-between gap-6 shadow-sm">
                        <div class="flex-1 h-3 bg-slate-100 rounded-full overflow-hidden text-left"><div id="progressBar" class="h-full bg-secondary w-0 transition-all duration-700 ease-out"></div></div>
                        <span id="progressText" class="text-[9px] lg:text-[10px] font-black text-secondary uppercase tracking-[0.2em] whitespace-nowrap">0 / 7 KATEGORI</span>
                    </div>

                    <div class="bg-white rounded-3xl border border-slate-100 overflow-x-auto shadow-sm text-left no-scrollbar">
                        <table class="w-full text-left border-collapse min-w-[700px]">
                            <thead class="bg-slate-50 text-slate-400 font-black uppercase text-[10px] tracking-widest border-b border-slate-100 text-left">
                                <tr>
                                    <th class="px-8 py-5 text-left">Kategori & Standar</th>
                                    <th class="px-6 py-5 text-left">Dokumen User (PDF)</th>
                                    <th class="px-8 py-5 text-center">Beri Skor (1-4)</th>
                                    <th class="px-6 py-5 text-center">Bobot</th>
                                </tr>
                            </thead>
                            <tbody id="evaluationTableBody" class="divide-y divide-slate-50 text-left"></tbody>
                        </table>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 text-left">
                        <div class="bg-white p-10 rounded-[2.5rem] border border-slate-100 flex flex-col justify-center items-center shadow-sm text-center">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-4 text-left">Hasil Akreditasi Akhir</span>
                            <div id="finalPercentageDisplay" class="text-6xl lg:text-7xl font-black text-primary tracking-tighter text-left">0.0%</div>
                            <div id="predikatDisplay" class="text-[10px] lg:text-[11px] font-black text-slate-300 uppercase mt-6 px-8 py-3 rounded-full bg-slate-50 tracking-[0.2em] border border-slate-100 text-center">LENGKAPI SELURUH NILAI</div>
                        </div>

                        <div class="space-y-5 text-left">
                            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-6 text-left">
                                <div id="inputLHS" class="text-left">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-3 italic text-left">1. Laporan Hasil Survailen (LHS) <span class="text-rose-500">*</span></label>
                                    <div id="lhsFileContainer">
                                        <input type="file" name="admin_file" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-primary file:text-white hover:file:bg-secondary cursor-pointer">
                                    </div>
                                </div>
                                <div id="inputCert" class="pt-4 border-t border-slate-50 text-left">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-3 italic text-left">2. Sertifikat Akreditasi <span class="text-rose-500">*</span></label>
                                    <div id="certFileContainer">
                                        <input type="file" name="certificate_file" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-amber-500 file:text-white hover:file:bg-amber-600 cursor-pointer">
                                    </div>
                                </div>
                                <p id="fileNote" class="text-[9px] text-slate-400 font-bold italic mt-2 text-left">* Maksimal 2MB per file.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="modalFooter" class="p-8 bg-white border-t border-slate-100 flex justify-end items-center gap-6 shrink-0">
                    <button type="button" onclick="closeEvaluateModal()" class="text-[11px] font-black uppercase text-slate-400 tracking-[0.2em] hover:text-rose-500 transition-colors">Batal / Tutup</button>
                    <button id="saveBtn" type="submit" class="bg-slate-900 text-white px-12 py-5 rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] hover:bg-secondary transition-all shadow-xl active:scale-95"><i class="fas fa-save mr-2"></i> Simpan Penilaian</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const storageBase = "{{ asset('storage') }}";
        const evaluateRouteBase = "{{ route('survailen.evaluate', ':id') }}";
        
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

        function closeSuccessModal() { document.getElementById('successModal')?.remove(); }
        function closeErrorModal() { document.getElementById('errorModal')?.remove(); }

        const categories = [
            { key: 'legalitas', label: '1. Legalitas Lembaga', bobot: 10, hint: 'OSS, MOU, Izin Pendukung', files: [{n: 'NIB/OSS', f: 'file_oss'}, {n: 'MoU', f: 'file_mou'}, {n: 'Izin Lain', f: 'file_izin_lainnya'}] },
            { key: 'mutu', label: '2. Manajemen Mutu', bobot: 20, hint: 'Manual Mutu & SOP', files: [{n: 'Manual Mutu', f: 'file_manual_mutu'}, {n: 'SOP Pelatihan', f: 'file_prosedur_pelatihan'}] },
            { key: 'rekaman', label: '3. Rekaman & Pantau', bobot: 20, hint: 'Monev & Implementasi', files: [{n: 'Pantau Mutu', f: 'file_pantau_mutu'}, {n: 'Rekaman Lain', f: 'file_rekaman_lainnya'}] },
            { key: 'kinerja', label: '4. Laporan Kinerja', bobot: 5, hint: 'Lapkin & KAK', files: [{n: 'Laporan Kinerja', f: 'file_lapkin'}, {n: 'KAK', f: 'file_kak'}] },
            { key: 'sdm', label: '5. Sumber Daya Manusia', bobot: 10, hint: 'Manajemen & Pengajar', files: [{n: 'Manajemen', f: 'file_daftar_manajemen'}, {n: 'Pengajar', f: 'file_daftar_pengajar'}] },
            { key: 'sarpras', label: '6. Sarana Prasarana', bobot: 15, hint: 'Fasilitas & Alat', files: [{n: 'Peralatan', f: 'file_daftar_sarana'}, {n: 'Gedung', f: 'file_daftar_prasarana'}] },
            { key: 'kurikulum', label: '7. Kurikulum & Modul', bobot: 20, hint: 'Materi & Bahan Ajar', files: [{n: 'Kurikulum', f: 'file_kurikulum'}, {n: 'Modul', f: 'file_modul'}, {n: 'Bahan Ajar', f: 'file_bahan_ajar'}] }
        ];

        function openEvaluateModal(btn) {
            const item = JSON.parse(btn.getAttribute('data-submission'));
            const tableBody = document.getElementById('evaluationTableBody');
            const scores = item.evaluator_scores ? JSON.parse(item.evaluator_scores) : {};
            const isCompleted = item.status === 'completed';
            
            // 1. Reset UI ke mode awal
            document.getElementById('evaluateForm').action = evaluateRouteBase.replace(':id', item.id);
            document.getElementById('evalModalTitle').querySelector('span').innerText = item.user.name;
            
            // 2. Tampilan Header & Footer Berdasarkan Status
            const header = document.getElementById('modalHeader');
            const headerTitle = document.getElementById('modalHeaderTitle');
            const saveBtn = document.getElementById('saveBtn');
            const progressContainer = document.getElementById('progressContainer');

            if (isCompleted) {
                header.classList.replace('bg-primary', 'bg-slate-900');
                headerTitle.innerText = "Detail Penilaian (Read-Only)";
                saveBtn.classList.add('hidden');
                progressContainer.classList.add('hidden');
            } else {
                header.classList.replace('bg-slate-900', 'bg-primary');
                headerTitle.innerText = "Instrumen Evaluasi Survailen";
                saveBtn.classList.remove('hidden');
                progressContainer.classList.remove('hidden');
            }

            // 3. Render Baris Evaluasi
            tableBody.innerHTML = '';
            categories.forEach((cat, idx) => {
                const currentScore = scores[cat.key] || null;
                
                // Dokumen dari User
                let filesHtml = `<div class="grid grid-cols-1 gap-1.5 text-left">`;
                cat.files.forEach(fileObj => {
                    const path = item.details ? item.details[fileObj.f] : null;
                    if (path) {
                        const cleanPath = path.startsWith('/') ? path.substring(1) : path;
                        filesHtml += `<a href="${storageBase}/${cleanPath}" target="_blank" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-teal-50 text-teal-600 hover:bg-teal-600 hover:text-white transition-all text-[10px] font-black"><i class="fas fa-file-pdf mr-2"></i>${fileObj.n}</a>`;
                    } else {
                        filesHtml += `<div class="px-3 py-1.5 rounded-lg bg-slate-50 text-slate-300 text-[9px] font-bold border border-slate-100 italic"><i class="fas fa-times-circle mr-2"></i>${fileObj.n}</div>`;
                    }
                });
                filesHtml += `</div>`;

                tableBody.innerHTML += `
                    <tr class="group hover:bg-slate-50/50 transition-colors text-left">
                        <td class="px-8 py-6 text-left">
                            <span class="block font-black text-slate-800 text-sm uppercase tracking-tight text-left">${cat.label}</span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase mt-1 block text-left tracking-wider opacity-60">${cat.hint}</span>
                        </td>
                        <td class="px-6 py-6 text-left">${filesHtml}</td>
                        <td class="px-8 py-6 text-center">
                            <div class="flex justify-center gap-2">
                                ${[1, 2, 3, 4].map(s => `
                                    <div class="relative text-center">
                                        <input type="radio" name="scores[${cat.key}]" id="s_${idx}_${s}" value="${s}" 
                                            class="hidden score-btn score-${s}" 
                                            onchange="calc()" 
                                            ${currentScore == s ? 'checked' : ''} 
                                            ${isCompleted ? 'disabled' : ''}
                                            required>
                                        <label for="s_${idx}_${s}" class="score-label w-10 h-10 flex items-center justify-center rounded-xl bg-white text-slate-300 text-sm font-black ${isCompleted ? '' : 'cursor-pointer'} border-2 border-slate-100 text-center">${s}</label>
                                    </div>
                                `).join('')}
                            </div>
                        </td>
                        <td class="px-6 py-6 text-[11px] font-black text-slate-400 text-center uppercase tracking-widest text-center">${cat.bobot}%</td>
                    </tr>
                `;
            });

            // 4. Update Area Unggah File (LHS & Sertifikat)
            const lhsContainer = document.getElementById('lhsFileContainer');
            const certContainer = document.getElementById('certFileContainer');
            const fileNote = document.getElementById('fileNote');

            if (isCompleted) {
                fileNote.classList.add('hidden');
                
                // Tampilkan link download saja jika sudah selesai
                lhsContainer.innerHTML = item.admin_file ? 
                    `<a href="${storageBase}/${item.admin_file}" target="_blank" class="flex items-center gap-3 p-4 bg-slate-100 rounded-2xl text-slate-700 font-bold text-[10px] uppercase hover:bg-primary hover:text-white transition-all"><i class="fas fa-file-pdf text-xl"></i> Lihat Laporan Hasil Survailen</a>` : 
                    `<span class="text-rose-500 font-bold text-[10px]">FILE TIDAK TERSEDIA</span>`;
                
                certContainer.innerHTML = item.certificate_file ? 
                    `<a href="${storageBase}/${item.certificate_file}" target="_blank" class="flex items-center gap-3 p-4 bg-slate-100 rounded-2xl text-slate-700 font-bold text-[10px] uppercase hover:bg-amber-500 hover:text-white transition-all"><i class="fas fa-certificate text-xl"></i> Lihat Sertifikat Terbit</a>` : 
                    `<span class="text-rose-500 font-bold text-[10px]">FILE TIDAK TERSEDIA</span>`;
            } else {
                fileNote.classList.remove('hidden');
                lhsContainer.innerHTML = `<input type="file" name="admin_file" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-primary file:text-white hover:file:bg-secondary cursor-pointer" required>`;
                certContainer.innerHTML = `<input type="file" name="certificate_file" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-amber-500 file:text-white hover:file:bg-amber-600 cursor-pointer" required>`;
            }

            document.getElementById('evaluateModal').classList.remove('hidden');
            document.getElementById('evaluateModal').classList.add('flex');
            calc();
        }

        function calc() {
            const selected = document.querySelectorAll('.score-btn:checked');
            let totalWeighted = 0;
            selected.forEach(input => {
                const key = input.name.match(/\[(.*?)\]/)[1];
                const cat = categories.find(c => c.key === key);
                if (cat) totalWeighted += (parseInt(input.value) * cat.bobot);
            });

            const percentage = (totalWeighted / 400) * 100;
            document.getElementById('finalPercentageDisplay').innerText = percentage.toFixed(1) + "%";
            
            const progressBar = document.getElementById('progressBar');
            if(progressBar) {
                const progress = (selected.length / 7) * 100;
                progressBar.style.width = progress + '%';
                document.getElementById('progressText').innerText = `${selected.length} / 7 KATEGORI TERISI`;
            }

            const display = document.getElementById('predikatDisplay');
            if (selected.length === 7) {
                let p = (percentage >= 85) ? 'A' : (percentage >= 70) ? 'B' : (percentage >= 55) ? 'C' : 'D';
                display.innerText = "PREDIKAT HASIL: " + p;
                display.className = "text-[11px] font-black uppercase mt-6 px-8 py-3 rounded-full bg-primary text-white tracking-[0.2em] shadow-lg text-center";
            } else {
                display.innerText = "LENGKAPI SELURUH NILAI";
                display.className = "text-[11px] font-black uppercase mt-6 px-8 py-3 rounded-full bg-slate-50 text-slate-300 tracking-[0.2em] border text-center";
            }
        }

        function closeEvaluateModal() {
            document.getElementById('evaluateModal').classList.add('hidden');
            document.getElementById('evaluateModal').classList.remove('flex');
        }
    </script>
</body>
</html>