<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirim KTUN | Admin SI-MUTU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        /* Animasi Pop-up Bouncy Tengah */
        @keyframes modalBounce {
            0% { opacity: 0; transform: scale(0.8) translateY(40px); }
            50% { opacity: 1; transform: scale(1.05) translateY(-10px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }
        .animate-gemay { animation: modalBounce 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }

        /* Custom Transition */
        .transition-all-custom { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }

        /* Dropdown custom height control */
        .dropdown-list { max-height: 0; overflow: hidden; transition: max-height 0.3s ease-out; }
        .dropdown-list.active { max-height: 300px; overflow-y: auto; }

        .file-input-box { transition: all 0.3s ease; }
        .file-input-wrapper:hover .file-input-box { border-color: #0d9488; background-color: #f0fdfa; }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-800 antialiased overflow-hidden">

    <div class="flex h-screen overflow-hidden">
        
        <div class="hidden md:block shrink-0">
            @include('components.uji-sidebar')
        </div>

        <div id="mobileSidebar" class="fixed inset-0 z-[60] hidden">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="toggleSidebar()"></div>
            <div class="absolute left-0 top-0 bottom-0 w-64 bg-teal-900 shadow-2xl transform transition-transform duration-300">
                @include('components.uji-sidebar')
            </div>
        </div>

        <div class="flex-1 flex flex-col h-screen overflow-hidden relative w-full">
            
            <div class="md:hidden bg-white border-b border-slate-100 px-5 py-3.5 flex items-center justify-between z-30 sticky top-0 shadow-sm">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="w-10 h-10 flex items-center justify-center text-slate-500 hover:text-teal-600 hover:bg-teal-50 rounded-2xl transition-all active:scale-90">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-teal-600 flex items-center justify-center text-white shadow-sm">
                            <i class="fas fa-flask text-sm"></i>
                        </div>
                        <span class="font-bold text-slate-800 text-sm tracking-tight">SI-LAB UJI</span>
                    </div>
                </div>
                <div class="w-9 h-9 rounded-full bg-teal-50 flex items-center justify-center text-teal-600 text-xs font-bold border border-teal-100 uppercase">
                    {{ substr(Auth::user()->name ?? 'L', 0, 1) }}
                </div>
            </div>

            <div class="hidden md:block">
                @include('components.uji-header', [
                    'title' => 'Penetapan Dokumen',
                    'subtitle' => 'Kelola dan unduh paket dokumen penetapan (KTUN)'
                ])
            </div>

            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-10 space-y-8 no-scrollbar bg-pattern bg-opacity-30">
                
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start max-w-[1600px] mx-auto">
                    
                    <div class="lg:col-span-5 space-y-6">
                        <div class="bg-white p-6 md:p-10 rounded-[3rem] shadow-[0_20px_60px_-20px_rgba(0,0,0,0.05)] border border-slate-100 relative overflow-hidden">
                            <div class="absolute -right-6 -top-6 w-32 h-32 bg-teal-50 rounded-full blur-3xl opacity-60"></div>
                            
                            <div class="relative z-10">
                                <div class="flex items-center gap-4 mb-8">
                                    <div class="w-14 h-14 bg-gradient-to-br from-teal-500 to-teal-600 text-white rounded-2xl flex items-center justify-center shadow-xl shadow-teal-100">
                                        <i class="fas fa-upload text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight">Kirim Paket</h3>
                                        <p class="text-[10px] text-teal-600 font-bold uppercase tracking-[0.2em] mt-0.5">Penetapan KTUN & Kwitansi</p>
                                    </div>
                                </div>

                                <form action="{{ route('ktun.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="mainForm">
                                    @csrf
                                    
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest ml-3 italic">Pilih Laboratorium</label>
                                        
                                        <div class="relative" id="customDropdown">
                                            <button type="button" onclick="toggleDropdown()" id="dropdownBtn" class="w-full flex items-center justify-between bg-slate-50 border-2 border-dashed border-slate-100 rounded-[1.5rem] p-3 pl-4 text-sm font-bold focus:ring-2 focus:ring-teal-100 transition-all shadow-inner hover:border-teal-300">
                                                <span id="selectedLabText" class="text-slate-400">
                                                -- Pilih Instansi Penerima --
                                                </span>
                                                <i class="fas fa-chevron-down text-lg text-slate-300"></i>
                                            </button>
                                            
                                            <input type="hidden" name="user_id" id="selectedUserId" required>

                                            <div id="dropdownContent" class="dropdown-list absolute z-50 left-0 right-0 mt-2 bg-white border border-slate-100 rounded-3xl shadow-2xl transition-all duration-300 pointer-events-none opacity-0 scale-95 origin-top">
                                                <div class="p-4 border-b border-slate-50">
                                                    <div class="relative">
                                                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-xs"></i>
                                                        <input type="text" id="labSearch" placeholder="Cari laboratorium..." class="w-full pl-10 pr-4 py-2 bg-slate-50 border-none rounded-xl text-xs font-bold outline-none focus:ring-2 focus:ring-teal-100">
                                                    </div>
                                                </div>
                                                <div class="max-h-[220px] overflow-y-auto p-2 no-scrollbar" id="labList">
                                                    @foreach($labs as $lab)
                                                    <div onclick="selectLab('{{ $lab->id }}', '{{ $lab->name }}')" class="lab-item px-4 py-3 rounded-2xl text-sm font-bold text-slate-600 hover:bg-teal-50 hover:text-teal-700 cursor-pointer transition-colors" data-name="{{ strtolower($lab->name) }}">
                                                        {{ $lab->name }}
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-4">
                                        @foreach([
                                            ['name' => 'file_pengantar', 'label' => '1. Surat Pengantar (PDF)'],
                                            ['name' => 'file_ktun', 'label' => '2. Dokumen KTUN (PDF)'],
                                            ['name' => 'file_kwintansi', 'label' => '3. Bukti Kwitansi (PDF)']
                                        ] as $file)
                                        <div class="file-input-wrapper space-y-2">
                                            <label class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest ml-3">{{ $file['label'] }}</label>
                                            <div class="relative group">
                                                <div class="file-input-box w-full bg-slate-50 border-2 border-dashed border-slate-100 rounded-[1.5rem] p-3 flex items-center justify-between group-hover:border-teal-300 transition-all shadow-inner">
                                                    <div class="flex items-center gap-4 overflow-hidden pl-2">
                                                        <div class="w-9 h-9 bg-white rounded-xl flex items-center justify-center text-teal-500 shadow-sm shrink-0 border border-slate-50">
                                                            <i class="far fa-file-pdf text-lg"></i>
                                                        </div>
                                                        <span id="label-{{ $file['name'] }}" class="text-[11px] font-bold text-slate-400 truncate tracking-tight">Klik untuk upload file...</span>
                                                    </div>
                                                    <span class="bg-teal-600 text-white text-[10px] font-extrabold px-5 py-2.5 rounded-[1.2rem] shadow-lg shadow-teal-100 group-hover:bg-slate-900 transition-all shrink-0 uppercase tracking-wider">Browse</span>
                                                </div>
                                                <input type="file" name="{{ $file['name'] }}" class="absolute inset-0 opacity-0 cursor-pointer" accept=".pdf" required onchange="handleFileSelect(this, 'label-{{ $file['name'] }}')">
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>

                                    <button type="submit" class="w-full bg-slate-900 text-white font-extrabold py-5 rounded-[2rem] shadow-2xl shadow-slate-200 hover:bg-teal-600 transition-all transform active:scale-95 text-xs uppercase tracking-[0.25em] mt-2 flex items-center justify-center gap-3">
                                        <span>Kirim Sekarang</span>
                                        <i class="fas fa-paper-plane text-[10px]"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-7">
                        <div class="bg-white rounded-[3rem] shadow-[0_20px_60px_-20px_rgba(0,0,0,0.05)] border border-slate-100 overflow-hidden">
                            <div class="px-8 py-8 border-b border-slate-50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white">
                                <div>
                                    <h3 class="text-xl font-extrabold text-slate-800 tracking-tight">Log Pengiriman</h3>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em] mt-1">Survey G-Form Monitor</p>
                                </div>
                                <div class="flex items-center gap-2 bg-teal-50 px-4 py-2 rounded-full border border-teal-100">
                                    <div class="w-2 h-2 rounded-full bg-teal-500 animate-pulse"></div>
                                    <span class="text-[10px] font-extrabold text-teal-700 uppercase tracking-widest">Live Updates</span>
                                </div>
                            </div>
                            <div class="overflow-x-auto no-scrollbar">
                                <table class="w-full text-sm text-left">
                                    <thead class="text-[10px] text-slate-400 uppercase font-extrabold tracking-[0.2em] bg-slate-50/50 border-b border-slate-50">
                                        <tr>
                                            <th class="px-8 py-5">Laboratorium</th>
                                            <th class="px-8 py-5 text-center">Status Survey</th>
                                            <th class="px-8 py-5 text-center w-24">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-50">
                                        @forelse($deliveries as $item)
                                        <tr class="hover:bg-slate-50/80 transition-all group">
                                            <td class="px-8 py-6">
                                                <p class="font-extrabold text-slate-700 text-sm group-hover:text-teal-700 transition-colors">{{ $item->user->name }}</p>
                                                <div class="flex items-center gap-2 mt-1.5">
                                                    <span class="bg-slate-100 text-slate-500 text-[9px] font-extrabold px-2 py-0.5 rounded-lg border border-slate-200 uppercase tracking-tighter shadow-sm">
                                                        {{ $item->created_at->format('d M Y') }}
                                                    </span>
                                                    <span class="text-[10px] text-slate-300 font-bold uppercase">{{ $item->created_at->format('H:i') }} WIB</span>
                                                </div>
                                            </td>
                                            <td class="px-8 py-6 text-center">
                                                @if($item->is_survey_filled)
                                                    <div class="inline-flex items-center gap-2 bg-emerald-50 text-emerald-600 text-[9px] font-extrabold px-4 py-2 rounded-full border border-emerald-100 uppercase tracking-widest shadow-sm">
                                                        <i class="fas fa-check-circle text-xs"></i> Selesai
                                                    </div>
                                                @else
                                                    <div class="inline-flex items-center gap-2 bg-amber-50 text-amber-600 text-[9px] font-extrabold px-4 py-2 rounded-full border border-amber-100 uppercase tracking-widest shadow-sm">
                                                        <span class="w-1.5 h-1.5 bg-amber-400 rounded-full animate-pulse"></span> Menunggu
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-8 py-6 text-center">
                                                <div class="flex justify-center gap-3">
                                                    <form action="{{ route('ktun.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus riwayat pengiriman ini?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="w-10 h-10 flex items-center justify-center rounded-2xl bg-slate-50 text-slate-300 hover:text-rose-600 hover:bg-rose-50 transition-all shadow-sm active:scale-90">
                                                            <i class="fas fa-trash-alt text-xs"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="py-32 text-center">
                                                <div class="flex flex-col items-center opacity-20">
                                                    <div class="w-24 h-24 bg-slate-100 rounded-[2.5rem] flex items-center justify-center mb-6">
                                                        <i class="fas fa-box-open text-4xl text-slate-400"></i>
                                                    </div>
                                                    <p class="text-slate-500 font-extrabold tracking-[0.3em] uppercase text-xs">Belum ada pengiriman</p>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="mt-8 text-center text-xs text-slate-400">
                    &copy; 2026 Sistem Informasi Jaminan Mutu Ketenaganukliran
                </div>

            </main>
        </div>
    </div>

    @if (session('success'))
    <div id="successPopup" class="fixed inset-0 z-[200] flex items-center justify-center p-6 bg-slate-900/40 backdrop-blur-md">
        <div class="absolute inset-0" onclick="closePopup()"></div>
        <div class="relative bg-white rounded-[4rem] p-10 md:p-14 max-w-sm w-full shadow-[0_30px_100px_-15px_rgba(0,0,0,0.3)] text-center animate-gemay border border-white">
            <div class="w-24 h-24 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-8 relative border-4 border-emerald-100">
                <div class="absolute inset-0 bg-emerald-400 rounded-full animate-ping opacity-10"></div>
                <i class="fas fa-check-circle text-5xl relative z-10"></i>
            </div>
            <h3 class="text-2xl font-extrabold text-slate-800 mb-3 tracking-tight uppercase">Yeay! Terkirim</h3>
            <p class="text-slate-500 text-sm mb-10 leading-relaxed font-medium px-4">{{ session('success') }}</p>
            <button onclick="closePopup()" class="w-full bg-slate-900 text-white font-extrabold py-5 rounded-[2rem] hover:bg-teal-600 transition-all shadow-xl active:scale-95 uppercase text-xs tracking-widest">
                Oke, Mantap!
            </button>
        </div>
    </div>
    @endif

    <div id="errorValidationPopup" class="fixed inset-0 z-[200] hidden flex items-center justify-center p-6 bg-slate-900/40 backdrop-blur-md">
        <div class="absolute inset-0" onclick="closeErrorPopup()"></div>
        <div class="relative bg-white rounded-[4rem] p-10 md:p-14 max-w-sm w-full shadow-[0_30px_100px_-15px_rgba(0,0,0,0.3)] text-center animate-gemay border border-white overflow-hidden">
            <div class="w-24 h-24 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-8 relative border-4 border-rose-100">
                <i class="fas fa-exclamation-circle text-4xl"></i>
            </div>
            <h3 class="text-2xl font-extrabold text-slate-800 mb-3 tracking-tight uppercase">Gagal Mengirim</h3>
            <p id="errorValidationMessage" class="text-slate-500 text-sm mb-10 leading-relaxed font-medium px-2">Keterangan error akan muncul di sini.</p>
            <button onclick="closeErrorPopup()" class="w-full bg-rose-600 text-white font-extrabold py-5 rounded-[2rem] hover:bg-rose-700 transition-all shadow-xl active:scale-95 uppercase text-xs tracking-widest">
                Perbaiki Data
            </button>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('mobileSidebar');
            sidebar.classList.toggle('hidden');
        }

        // LOGIKA DROPDOWN CUSTOM
        function toggleDropdown() {
            const content = document.getElementById('dropdownContent');
            const isActive = content.classList.contains('active');
            
            if (isActive) {
                content.classList.remove('active', 'opacity-100', 'scale-100', 'pointer-events-auto');
                content.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
            } else {
                content.classList.add('active', 'opacity-100', 'scale-100', 'pointer-events-auto');
                content.classList.remove('opacity-0', 'scale-95', 'pointer-events-none');
                document.getElementById('labSearch').focus();
            }
        }

        function selectLab(id, name) {
            document.getElementById('selectedLabText').innerText = name;
            document.getElementById('selectedLabText').classList.remove('text-slate-400');
            document.getElementById('selectedLabText').classList.add('text-slate-800');
            document.getElementById('selectedUserId').value = id;
            toggleDropdown();
        }

        document.getElementById('labSearch').addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase();
            const items = document.querySelectorAll('.lab-item');
            items.forEach(item => {
                const name = item.getAttribute('data-name');
                if (name.includes(term)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        window.addEventListener('click', function(e) {
            if (!document.getElementById('customDropdown').contains(e.target)) {
                const content = document.getElementById('dropdownContent');
                content.classList.remove('active', 'opacity-100', 'scale-100', 'pointer-events-auto');
                content.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
            }
        });

        // POP-UP CONTROL BERSALIN
        function closePopup() {
            const popup = document.getElementById('successPopup');
            if (popup) {
                popup.style.opacity = '0';
                popup.style.transform = 'scale(0.9) translateY(20px)';
                setTimeout(() => popup.remove(), 400);
            }
        }

        function showErrorPopup(message) {
            document.getElementById('errorValidationMessage').innerText = message;
            document.getElementById('errorValidationPopup').classList.remove('hidden');
        }

        function closeErrorPopup() {
            document.getElementById('errorValidationPopup').classList.add('hidden');
        }

        // VALIDASI UKURAN FILE & LOGIKA LIVE PREVIEW
        function handleFileSelect(input, labelId) {
            const label = document.getElementById(labelId);
            if (input.files && input.files.length > 0) {
                const file = input.files[0];
                const fileSizeInMB = file.size / (1024 * 1024);

                // Cek jika ukuran file lebih dari 2 MB
                if (fileSizeInMB > 2) {
                    showErrorPopup(`Berkas "${file.name}" terlalu besar (${fileSizeInMB.toFixed(2)} MB). Batas maksimum ukuran dokumen adalah 2 MB.`);
                    input.value = ''; 
                    label.innerText = "Klik untuk upload file...";
                    label.classList.remove('text-teal-600', 'font-extrabold');
                    label.classList.add('text-slate-400');
                    return;
                }

                label.innerText = file.name;
                label.classList.remove('text-slate-400');
                label.classList.add('text-teal-600', 'font-extrabold');
            } else {
                label.innerText = "Klik untuk upload file...";
                label.classList.remove('text-teal-600', 'font-extrabold');
                label.classList.add('text-slate-400');
            }
        }

        // INTERSEPTOR FORM SUBMIT (CEK DATA BELUM DIISI)
        document.getElementById('mainForm').addEventListener('submit', function(e) {
            const userId = document.getElementById('selectedUserId').value;
            
            // 1. Validasi dropdown institusi
            if (!userId) {
                e.preventDefault(); 
                showErrorPopup('Silakan pilih Laboratorium / Instansi Penerima terlebih dahulu!');
                return;
            }

            // 2. Validasi file kosong
            const fileInputs = this.querySelectorAll('input[type="file"]');
            let fileMissing = false;
            
            fileInputs.forEach(input => {
                if (!input.files || input.files.length === 0) {
                    fileMissing = true;
                }
            });

            if (fileMissing) {
                e.preventDefault();
                showErrorPopup('Seluruh file paket dokumen penetapan (Surat Pengantar, KTUN, & Kwitansi) wajib diunggah!');
                return;
            }
        });
    </script>
</body>
</html>