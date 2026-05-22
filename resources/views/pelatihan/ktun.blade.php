<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokumen KTUN | SI-MUTU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        @keyframes bounceIn {
            0% { opacity: 0; transform: scale(0.9) translateY(20px); }
            50% { opacity: 1; transform: scale(1.02) translateY(-5px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }
        .animate-gemay { animation: bounceIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }

        @keyframes floating {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-12px) rotate(5deg); }
        }
        .animate-float { animation: floating 3s ease-in-out infinite; }

        /* Pattern Dekoratif */
        .bg-pattern {
            background-image: radial-gradient(#cbd5e1 0.5px, transparent 0.5px);
            background-size: 15px 15px;
        }
    </style>
</head>
<body class="bg-[#fcfcfd] text-slate-800 antialiased overflow-hidden">

    @php
        // Simulasi mengambil semua data delivery untuk user ini
        // Di aplikasi nyata, ini dikirim dari KtunDeliveryController
        $deliveries = \App\Models\KtunDelivery::where('user_id', Auth::id())->latest()->get();
        $latestDelivery = $deliveries->first();
        $previousDeliveries = $deliveries->skip(1);
    @endphp

    <div class="flex h-screen overflow-hidden">
        
        <div class="hidden md:block shrink-0">
            @include('components.pelatihan-sidebar')
        </div>

        <div id="mobileSidebar" class="fixed inset-0 z-[60] hidden">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="toggleSidebar()"></div>
            <div class="absolute left-0 top-0 bottom-0 w-64 bg-blue-900 shadow-2xl transform transition-transform duration-300">
                @include('components.pelatihan-sidebar')
            </div>
        </div>

        <div class="flex-1 flex flex-col h-screen overflow-hidden relative w-full">
            
            <div class="md:hidden bg-white border-b border-slate-100 px-5 py-3.5 flex items-center justify-between z-30 sticky top-0 shadow-sm">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="w-10 h-10 flex items-center justify-center text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-2xl transition-all active:scale-90">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white shadow-sm">
                            <i class="fas fa-flask text-sm"></i>
                        </div>
                        <span class="font-bold text-slate-800 text-sm tracking-tight">SI-LAB Pelatihan</span>
                    </div>
                </div>
                <div class="w-9 h-9 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 text-xs font-bold border border-blue-100 uppercase">
                    {{ substr(Auth::user()->name ?? 'L', 0, 1) }}
                </div>
            </div>

            <div class="hidden md:block">
                @include('components.pelatihan-header', [
                    'title' => 'Penetapan Dokumen',
                    'subtitle' => 'Kelola dan unduh paket dokumen penetapan (KTUN)'
                ])
            </div>

            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-10 space-y-8 no-scrollbar bg-pattern bg-opacity-30">
                
                <div class="max-w-5xl mx-auto space-y-10">
                    
                    @if($latestDelivery)
                        <div class="bg-white rounded-[3rem] md:rounded-[4rem] p-8 md:p-14 shadow-[0_40px_100px_-25px_rgba(0,0,0,0.06)] border border-slate-50 relative overflow-hidden animate-gemay">
                            <div class="absolute top-0 right-0 w-80 h-80 bg-blue-50 rounded-full blur-3xl opacity-50 -mr-32 -mt-32"></div>
                            <div class="absolute bottom-0 left-0 w-60 h-60 bg-blue-50 rounded-full blur-2xl opacity-40 -ml-20 -mb-20"></div>
                            
                            <div class="relative z-10 flex flex-col items-center text-center">
                                <div class="mb-8 relative">
                                    <div class="w-20 h-20 md:w-28 md:h-28 bg-gradient-to-br {{ $latestDelivery->is_survey_filled ? 'from-blue-400 to-blue-600' : 'from-blue-400 to-blue-600' }} rounded-[2rem] md:rounded-[2.5rem] flex items-center justify-center shadow-2xl {{ $latestDelivery->is_survey_filled ? 'shadow-blue-100' : 'shadow-blue-100' }} animate-float border-4 border-white">
                                        <i class="fas {{ $latestDelivery->is_survey_filled ? 'fa-envelope-open-text' : 'fa-box-open' }} text-3xl md:text-5xl text-white"></i>
                                    </div>
                                    @if(!$latestDelivery->is_survey_filled)
                                    <div class="absolute -top-2 -right-2 md:-top-3 md:-right-3 w-10 h-10 bg-white border-4 border-slate-50 rounded-full flex items-center justify-center text-blue-500 shadow-xl">
                                        <i class="fas fa-lock text-sm"></i>
                                    </div>
                                    @endif
                                </div>

                                <div class="max-w-xl mx-auto mb-10">
                                    <div class="inline-flex items-center gap-2 bg-slate-100 text-slate-500 text-[10px] font-bold px-3 py-1 rounded-full mb-4 uppercase tracking-widest">Diterima {{ $latestDelivery->created_at->format('d M Y') }}</div>
                                    <h2 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight mb-3 uppercase">
                                        {{ $latestDelivery->is_survey_filled ? 'Dokumen Terbaru Siap!' : 'Paket Baru Terkunci' }}
                                    </h2>
                                    <p class="text-slate-400 text-sm md:text-base font-medium leading-relaxed">
                                        {{ $latestDelivery->is_survey_filled 
                                            ? 'Terima kasih telah berpartisipasi dalam survey kepuasan. Seluruh dokumen penetapan terbaru instansi Anda sudah tersedia di bawah ini.' 
                                            : 'Admin BAPETEN telah mengirimkan paket dokumen penetapan terbaru. Selesaikan survey singkat untuk membuka akses pengunduhan dokumen.' }}
                                    </p>
                                </div>

                                @if(!$latestDelivery->is_survey_filled)
                                    <button onclick="openSurveyModal({{ $latestDelivery->id }})" class="w-full sm:w-auto bg-slate-900 text-white font-extrabold px-10 md:px-12 py-5 rounded-[1.8rem] shadow-2xl hover:bg-blue-600 transition-all transform active:scale-95 text-xs tracking-[0.2em] flex items-center justify-center gap-4">
                                        <i class="fas fa-key text-[10px]"></i>
                                        <span>BUKA PAKET DOKUMEN</span>
                                    </button>
                                @else
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 w-full">
                                        @foreach([
                                            ['path' => $latestDelivery->file_surat_pengantar, 'label' => 'Surat Pengantar', 'icon' => 'fa-file-alt'],
                                            ['path' => $latestDelivery->file_ktun, 'label' => 'Salinan KTUN', 'icon' => 'fa-certificate'],
                                            ['path' => $latestDelivery->file_kwintansi, 'label' => 'Bukti Kwitansi', 'icon' => 'fa-receipt']
                                        ] as $doc)
                                        <a href="{{ asset('storage/'.$doc['path']) }}" target="_blank" class="flex flex-col items-center gap-4 p-7 bg-slate-50 border-2 border-dashed border-slate-200 rounded-[2.5rem] hover:bg-blue-50 hover:border-blue-400 hover:scale-[1.03] transition-all group shadow-sm">
                                            <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-blue-600 shadow-sm border border-slate-100 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                                                <i class="fas {{ $doc['icon'] }} text-xl"></i>
                                            </div>
                                            <div class="space-y-1">
                                                <p class="text-[10px] font-extrabold text-slate-700 uppercase tracking-widest">{{ $doc['label'] }}</p>
                                                <span class="text-[9px] font-bold text-blue-500 uppercase tracking-tighter opacity-0 group-hover:opacity-100 transition-opacity">Klik Unduh PDF</span>
                                            </div>
                                        </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="bg-white rounded-[3rem] p-8 md:p-12 shadow-sm border border-slate-100 relative overflow-hidden animate-gemay">
                            <h3 class="text-xl font-extrabold text-slate-800 mb-8 flex items-center gap-3">
                                <div class="w-10 h-10 bg-slate-50 text-slate-400 rounded-2xl flex items-center justify-center shadow-inner border border-slate-100">
                                    <i class="fas fa-archive text-sm"></i>
                                </div>
                                Arsip Paket KTUN
                            </h3>

                            <div class="overflow-x-auto no-scrollbar">
                                <table class="w-full text-left">
                                    <thead>
                                        <tr class="text-[10px] text-slate-400 uppercase tracking-[0.2em] border-b border-slate-50">
                                            <th class="pb-5 font-extrabold">Tanggal Kirim</th>
                                            <th class="pb-5 font-extrabold text-center">Status</th>
                                            <th class="pb-5 font-extrabold text-right">Akses Dokumen</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-50">
                                        @forelse($deliveries as $item)
                                        <tr class="group hover:bg-slate-50/50 transition-colors">
                                            <td class="py-5">
                                                <p class="text-sm font-extrabold text-slate-700">{{ $item->created_at->format('d M Y') }}</p>
                                                <p class="text-[10px] text-slate-400 font-medium">{{ $item->created_at->format('H:i') }} WIB</p>
                                            </td>
                                            <td class="py-5 text-center">
                                                @if($item->is_survey_filled)
                                                    <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-600 text-[9px] font-extrabold px-3 py-1.5 rounded-full border border-emerald-100 uppercase tracking-widest">
                                                        <i class="fas fa-check-circle"></i> Terbuka
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-600 text-[9px] font-extrabold px-3 py-1.5 rounded-full border border-amber-100 uppercase tracking-widest">
                                                        <i class="fas fa-lock"></i> Terkunci
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-5 text-right">
                                                @if($item->is_survey_filled)
                                                    <div class="flex justify-end gap-2">
                                                        <a href="{{ asset('storage/'.$item->file_surat_pengantar) }}" target="_blank" class="w-9 h-9 flex items-center justify-center bg-white border border-slate-200 text-slate-400 hover:text-blue-600 hover:border-blue-200 rounded-xl transition-all shadow-sm" title="Unduh Surat Pengantar">
                                                            <i class="fas fa-file-alt text-xs"></i>
                                                        </a>
                                                        <a href="{{ asset('storage/'.$item->file_ktun) }}" target="_blank" class="w-9 h-9 flex items-center justify-center bg-white border border-slate-200 text-slate-400 hover:text-blue-600 hover:border-blue-200 rounded-xl transition-all shadow-sm" title="Unduh KTUN">
                                                            <i class="fas fa-certificate text-xs"></i>
                                                        </a>
                                                        <a href="{{ asset('storage/'.$item->file_kwintansi) }}" target="_blank" class="w-9 h-9 flex items-center justify-center bg-white border border-slate-200 text-slate-400 hover:text-blue-600 hover:border-blue-200 rounded-xl transition-all shadow-sm" title="Unduh Kwitansi">
                                                            <i class="fas fa-receipt text-xs"></i>
                                                        </a>
                                                    </div>
                                                @else
                                                    <button onclick="openSurveyModal({{ $item->id }})" class="text-[10px] font-extrabold text-blue-600 hover:underline underline-offset-4 tracking-widest">UNLOCK SEKARANG</button>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="py-10 text-center opacity-30 italic text-sm font-medium">Belum ada riwayat dokumen.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    @else
                        <div class="bg-white rounded-[3.5rem] p-16 md:p-24 text-center shadow-sm border border-slate-100 animate-gemay">
                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-200">
                                <i class="fas fa-ghost text-4xl"></i>
                            </div>
                            <h3 class="text-xl font-extrabold text-slate-400 tracking-tight uppercase">Belum Ada Dokumen</h3>
                            <p class="text-slate-300 text-sm mt-1 font-medium italic">Dokumen KTUN akan muncul di sini setelah diverifikasi.</p>
                        </div>
                    @endif

                    <div class="bg-white p-8 md:p-12 rounded-[3rem] md:rounded-[3.5rem] border border-slate-100 shadow-sm relative overflow-hidden animate-gemay">
                        <div class="absolute -right-10 -bottom-10 opacity-[0.03] text-blue-900 rotate-12"><i class="fas fa-info-circle text-[12rem]"></i></div>
                        
                        <h4 class="font-extrabold text-slate-800 mb-8 flex items-center gap-3 uppercase tracking-widest text-[10px]">
                            <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center shrink-0">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            Panduan Akses Dokumen
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10">
                            <div class="flex gap-4">
                                <div class="w-10 h-10 rounded-2xl bg-slate-50 text-slate-400 border border-slate-100 flex items-center justify-center shrink-0 font-bold text-sm shadow-inner">01</div>
                                <div class="space-y-1">
                                    <p class="text-sm font-extrabold text-slate-700">Multi-Dokumen</p>
                                    <p class="text-xs text-slate-500 leading-relaxed">Jika ada perubahan atau amandemen, dokumen terbaru akan muncul di panel atas secara otomatis.</p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="w-10 h-10 rounded-2xl bg-slate-50 text-slate-400 border border-slate-100 flex items-center justify-center shrink-0 font-bold text-sm shadow-inner">02</div>
                                <div class="space-y-1">
                                    <p class="text-sm font-extrabold text-slate-700">Akses Mandiri</p>
                                    <p class="text-xs text-slate-500 leading-relaxed">Setiap paket dokumen memiliki status survey masing-masing. Pastikan semua paket terkonfirmasi.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center pb-10">
                    <p class="text-[10px] text-slate-300 font-bold uppercase tracking-[0.3em]">SI-MUTU DKKN BAPETEN &copy; 2026</p>
                </div>

            </main>
        </div>
    </div>

    @if (session('success'))
    <div id="successPopup" class="fixed inset-0 z-[300] flex items-center justify-center p-6 bg-slate-900/60 backdrop-blur-xl transition-all duration-300">
        <div class="absolute inset-0" onclick="closeSuccessModal()"></div>
        <div class="relative bg-white rounded-[3.5rem] md:rounded-[4rem] p-12 md:p-16 max-w-sm w-full shadow-[0_50px_100px_-20px_rgba(0,0,0,0.4)] text-center animate-gemay border-4 border-white">
            <div class="w-24 h-24 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-8 border-4 border-emerald-100 relative">
                <div class="absolute inset-0 bg-emerald-400 rounded-full animate-ping opacity-10"></div>
                <i class="fas fa-check-circle text-5xl relative z-10"></i>
            </div>
            <h3 class="text-2xl font-extrabold text-slate-800 mb-4 tracking-tight uppercase">Yuhuu, Terbuka!</h3>
            <p class="text-slate-500 text-sm font-medium leading-relaxed px-2 mb-10">
                Akses paket dokumen KTUN Anda sekarang telah terbuka sepenuhnya. Silakan unduh berkas aslinya.
            </p>
            <button onclick="closeSuccessModal()" class="w-full bg-slate-900 text-white font-extrabold py-5 rounded-[1.8rem] hover:bg-emerald-600 transition-all shadow-xl active:scale-95 text-[10px] tracking-[0.2em] uppercase">
                OKE, MENGERTI
            </button>
        </div>
    </div>
    @endif

    <div id="surveyModal" class="fixed inset-0 z-[250] hidden flex items-center justify-center p-6 bg-slate-900/60 backdrop-blur-xl">
        <div class="absolute inset-0" onclick="closeSurveyModal()"></div>
        <div class="relative bg-white rounded-[3.5rem] md:rounded-[4rem] p-10 md:p-14 max-w-md w-full shadow-[0_50px_100px_-20px_rgba(0,0,0,0.4)] text-center animate-gemay border-4 border-white">
            
            <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-10 border-4 border-blue-100 relative">
                <i class="fab fa-google text-3xl"></i>
                <div class="absolute -bottom-2 -right-2 w-10 h-10 bg-emerald-500 text-white rounded-full flex items-center justify-center border-4 border-white">
                    <i class="fas fa-pen text-[10px]"></i>
                </div>
            </div>
            
            <h3 class="text-2xl font-extrabold text-slate-800 mb-4 tracking-tight uppercase">Survey Layanan</h3>
            
            <p id="surveyInstruction" class="text-slate-500 text-sm mb-12 font-medium leading-relaxed px-2">
                Mohon klik tombol di bawah untuk mengisi survey kepuasan. Dokumen akan terbuka otomatis setelah Anda selesai mengisi.
            </p>
            
            <div id="surveyLoading" class="hidden flex-col items-center gap-3 mb-12">
                <div class="w-10 h-10 border-4 border-slate-200 border-t-blue-600 rounded-full animate-spin"></div>
                <p class="text-xs font-bold text-blue-600 uppercase tracking-widest">Menunggu Anda Kembali...</p>
            </div>
            
            <div class="space-y-4">
                <a id="btnGform" href="https://docs.google.com/forms/d/e/1FAIpQLSeW6FwPyZMMGtdAMBPCJPHNpyfBgj12iI4_V_ZeXddE6G8kkg/viewform" target="_blank" onclick="startSurveyRedirect()" class="w-full inline-flex items-center justify-center gap-3 bg-blue-600 text-white font-extrabold py-5 rounded-[2rem] hover:bg-blue-700 transition-all shadow-xl shadow-blue-200 active:scale-95 text-xs tracking-widest uppercase">
                    <span>BUKA GOOGLE FORM</span>
                    <i class="fas fa-external-link-alt text-[10px]"></i>
                </a>

                <form id="surveyForm" method="POST" action="" class="hidden">
                    @csrf
                </form>
                
                <button onclick="closeSurveyModal()" class="text-[10px] font-extrabold text-slate-300 hover:text-slate-500 uppercase tracking-[0.2em] transition-colors pt-4">NANTI SAJA</button>
            </div>
        </div>
    </div>

    <script>
        let isFormClicked = false;

        function toggleSidebar() {
            const sidebar = document.getElementById('mobileSidebar');
            sidebar.classList.toggle('hidden');
        }

        function openSurveyModal(id) {
            const m = document.getElementById('surveyModal');
            document.getElementById('surveyForm').action = "/ktun/survey/unlock/" + id; 
            
            // Reset state modal kembali ke default
            isFormClicked = false;
            document.getElementById('surveyInstruction').classList.remove('hidden');
            document.getElementById('surveyLoading').classList.add('hidden');
            document.getElementById('btnGform').classList.remove('opacity-50', 'pointer-events-none');
            
            m.classList.remove('hidden');
        }

        function closeSurveyModal() { 
            document.getElementById('surveyModal').classList.add('hidden'); 
            isFormClicked = false;
        }
        
        function closeSuccessModal() {
            const m = document.getElementById('successPopup');
            if (m) m.remove();
        }

        // --- ALUR OTOMATISASI BARU ---
        function startSurveyRedirect() {
            isFormClicked = true;
            
            // Perbarui tampilan interface modal menjadi mode loading
            document.getElementById('surveyInstruction').classList.add('hidden');
            document.getElementById('surveyLoading').classList.remove('hidden');
            document.getElementById('btnGform').classList.add('opacity-50', 'pointer-events-none');
        }

        // Mendeteksi ketika user kembali fokus ke tab aplikasi kita setelah dari Google Form
        window.addEventListener('focus', function() {
            if (isFormClicked) {
                isFormClicked = false; // Reset flag agar tidak terjadi looping submit
                
                // Submit form ke Laravel backend secara otomatis
                document.getElementById('surveyForm').submit();
            }
        });
    </script>
</body>
</html>