<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Lembaga Uji | SI-MUTU Pro</title>

    <!-- Scripts & Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <!-- Chart JS -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom Config Tailwind -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif']
                    },
                    colors: {
                        primary: '#0f766e', // Teal-700
                        primaryLight: '#14b8a6', // Teal-500
                        surface: '#ffffff',
                        background: '#f8fafc', // Slate-50
                    },
                    boxShadow: {
                        'soft': '0 4px 20px -2px rgba(0, 0, 0, 0.05)',
                        'card': '0 0 0 1px rgba(0,0,0,0.03), 0 2px 8px rgba(0,0,0,0.04)',
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1)',
                        'pop-in': 'popIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards',
                    },
                    keyframes: {
                        fadeInUp: {
                            '0%': {
                                opacity: '0',
                                transform: 'translateY(10px)'
                            },
                            '100%': {
                                opacity: '1',
                                transform: 'translateY(0)'
                            },
                        },
                        popIn: {
                            '0%': {
                                opacity: '0',
                                transform: 'scale(0.8) translateY(20px)'
                            },
                            '100%': {
                                opacity: '1',
                                transform: 'scale(1) translateY(0)'
                            },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Modal Transitions */
        .modal-enter {
            opacity: 0;
            transform: scale(0.95);
        }

        .modal-enter-active {
            opacity: 1;
            transform: scale(1);
            transition: all 0.2s ease-out;
        }

        .modal-exit {
            opacity: 1;
            transform: scale(1);
        }

        .modal-exit-active {
            opacity: 0;
            transform: scale(0.95);
            transition: all 0.2s ease-in;
        }

        /* Custom Scrollbar for Timeline */
        .modal-scroll::-webkit-scrollbar {
            width: 5px;
        }

        .modal-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .modal-scroll::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }

        /* Overlay Responsif */
        .glass-overlay {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
        }
    </style>
</head>

<body class="bg-background text-slate-800 font-sans antialiased selection:bg-primary/20 selection:text-primary">

    <!-- BACKEND LOGIC -->
    @php
        use App\Models\Submission;
        use Illuminate\Support\Facades\Auth;
        use Illuminate\Support\Facades\Request;

        $scopeFilter = Request::get('scope');

        if (!isset($mySubmissions)) {
            // FILTER UTAMA: Hanya mengambil tipe yang mengandung 'Laporan Tahunan'
            $query = Submission::where('user_id', Auth::id())
                ->where('type', 'like', '%Laporan Tahunan%')
                ->orderBy('created_at', 'desc');

            if ($scopeFilter) {
                $query->where('type', 'like', $scopeFilter . '%');
            }
            try {
                $check = new Submission();
                if (method_exists($check, 'files')) {
                    $query->with('files');
                }
            } catch (\Exception $e) {
            }
            $mySubmissions = $query->get();
        }

        $myPending = $mySubmissions->where('status', 'pending')->count();
        $myApproved = $mySubmissions->where('status', 'approved')->count();
        $myRejected = $mySubmissions->where('status', 'rejected')->count();
        $total = $mySubmissions->count();
    @endphp

    <!-- === POP-UP NOTIFIKASI MODAL (Berhasil) === -->
    @if (session('success'))
        <div id="successModal"
            class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 backdrop-blur-[3px] transition-all duration-300">
            <div
                class="bg-white rounded-2xl shadow-2xl p-8 max-w-sm w-full mx-4 text-center animate-pop-in relative overflow-hidden border border-white/50">
                <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-teal-400 to-emerald-500"></div>

                <div
                    class="w-20 h-20 bg-teal-50 rounded-full flex items-center justify-center mx-auto mb-4 shadow-inner">
                    <i class="fas fa-check text-4xl text-teal-600"></i>
                </div>

                <h3 class="text-2xl font-bold text-slate-800 mb-2">Berhasil!</h3>
                <p class="text-slate-600 mb-6 text-sm leading-relaxed font-medium">
                    {{ session('success') }}
                </p>

                <!-- Progress Bar -->
                <div class="w-full bg-slate-100 h-1.5 rounded-full mb-5 overflow-hidden">
                    <div id="progressBar" class="h-full bg-teal-500 rounded-full" style="width: 100%"></div>
                </div>

                <button onclick="closeNotification('successModal')"
                    class="w-full bg-slate-50 border border-slate-200 hover:bg-teal-50 hover:text-teal-700 text-slate-500 font-bold py-3 rounded-xl transition-all duration-300 shadow-sm group">
                    Tutup Sekarang
                </button>
            </div>
        </div>
    @endif

    <div class="flex h-screen overflow-hidden bg-slate-50">

        <!-- === MOBILE OVERLAY === -->
        <div id="sidebarOverlay" onclick="toggleSidebar()"
            class="fixed inset-0 z-40 hidden lg:hidden glass-overlay transition-opacity duration-300"></div>

        <!-- === SIDEBAR WRAPPER (Responsive) === -->
        <aside id="sidebar"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-2xl lg:shadow-none transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto flex flex-col h-full border-r border-slate-200">
            @include('components.uji-sidebar')
        </aside>

        <!-- MAIN CONTENT -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden relative bg-[#f8fafc]">

            <!-- === HEADER MOBILE === -->
            <div
                class="lg:hidden bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between z-20 sticky top-0 shadow-sm">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()"
                        class="p-2 -ml-2 text-slate-500 hover:text-primary hover:bg-slate-100 rounded-lg transition-colors focus:outline-none">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div class="flex items-center gap-2">
                        <div
                            class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center text-white shadow-sm">
                            <i class="fas fa-flask text-sm"></i>
                        </div>
                        <span class="font-bold text-slate-800 text-sm tracking-wide">SI-LAB UJI</span>
                    </div>
                </div>
                <div
                    class="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center text-primary text-xs font-bold border border-teal-200">
                    {{ substr(Auth::user()->name ?? 'L', 0, 1) }}
                </div>
            </div>

            <!-- === HEADER DESKTOP === -->
            <div class="hidden lg:block sticky top-0 z-10 bg-white/80 backdrop-blur-md border-b border-slate-200">
                @include('components.uji-header', [
                    'title' => 'Dashboard Laboratorium',
                    'subtitle' => 'Pantau status pengajuan dan kualitas mutu',
                ])
            </div>

            <!-- SCROLLABLE CONTENT -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-8 scroll-smooth">
                <div class="max-w-7xl mx-auto space-y-8">

                    <!-- SECTION 1: OVERVIEW & CHARTS -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in-up">
                        <div class="lg:col-span-2 space-y-6">
                            <div
                                class="bg-gradient-to-br from-[#0f766e] to-[#115e59] rounded-2xl p-8 text-white shadow-lg relative overflow-hidden">
                                <div class="absolute -right-10 -bottom-10 opacity-10">
                                    <i class="fas fa-microscope text-9xl transform -rotate-12"></i>
                                </div>
                                <div class="relative z-10">
                                    <h2 class="text-2xl font-bold mb-2">Selamat Datang, Laboratorium!</h2>
                                    <p class="text-teal-100/90 text-sm leading-relaxed mb-6 max-w-lg">Kelola dokumen
                                        pengajuan uji mutu Anda dengan mudah. Pantau revisi dan persetujuan secara
                                        real-time.</p>
                                    <button onclick="openModal('add')"
                                        class="bg-white text-teal-700 px-5 py-2.5 rounded-lg font-bold text-sm shadow hover:bg-teal-50 transition-all flex items-center gap-2">
                                        <i class="fas fa-plus-circle"></i> Buat Pengajuan Baru
                                    </button>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-4">
                                <div class="bg-white p-4 rounded-xl shadow-card border border-slate-100">
                                    <div class="flex items-center gap-3 mb-1">
                                        <div
                                            class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-sm border border-amber-100">
                                            <i class="fas fa-hourglass-half"></i>
                                        </div>
                                        <span
                                            class="text-xs font-bold text-slate-400 uppercase tracking-wide">Menunggu</span>
                                    </div>
                                    <div class="text-2xl font-bold text-slate-800 ml-1">{{ $myPending }}</div>
                                </div>
                                <div class="bg-white p-4 rounded-xl shadow-card border border-slate-100">
                                    <div class="flex items-center gap-3 mb-1">
                                        <div
                                            class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm border border-emerald-100">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                        <span
                                            class="text-xs font-bold text-slate-400 uppercase tracking-wide">Disetujui</span>
                                    </div>
                                    <div class="text-2xl font-bold text-slate-800 ml-1">{{ $myApproved }}</div>
                                </div>
                                <div class="bg-white p-4 rounded-xl shadow-card border border-slate-100">
                                    <div class="flex items-center gap-3 mb-1">
                                        <div
                                            class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-sm border border-rose-100">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </div>
                                        <span
                                            class="text-xs font-bold text-slate-400 uppercase tracking-wide">Revisi</span>
                                    </div>
                                    <div class="text-2xl font-bold text-slate-800 ml-1">{{ $myRejected }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-2xl shadow-card border border-slate-100 flex flex-col">
                            <h3 class="font-bold text-slate-700 mb-4 flex items-center gap-2 text-sm">
                                <i class="fas fa-chart-donut text-primary"></i> Ringkasan Status
                            </h3>
                            <div class="flex-1 flex items-center justify-center relative">
                                <div id="statusChart" class="w-full flex justify-center"></div>
                                @if ($total == 0)
                                    <div class="absolute inset-0 flex flex-col items-center justify-center bg-white/90">
                                        <i class="fas fa-chart-pie text-slate-200 text-4xl mb-2"></i>
                                        <span class="text-xs text-slate-400">Belum ada data</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: DATA TABLE -->
                    <div class="bg-white rounded-2xl shadow-card border border-slate-200 overflow-hidden animate-fade-in-up"
                        style="animation-delay: 200ms;">
                        <div
                            class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div
                                class="bg-slate-100 p-1 rounded-xl inline-flex gap-1 overflow-x-auto hide-scrollbar max-w-full">
                                <a href="?scope="
                                    class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ !$scopeFilter ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:bg-slate-200/50' }}">Semua</a>
                                <a href="?scope=LUK"
                                    class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $scopeFilter == 'LUK' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:bg-slate-200/50' }}">Uji
                                    Kesesuaian</a>
                                <a href="?scope=Eksterna"
                                    class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $scopeFilter == 'Eksterna' ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-500 hover:bg-slate-200/50' }}">Dosis
                                    Eksterna</a>
                                <a href="?scope=Nuklida"
                                    class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $scopeFilter == 'Nuklida' ? 'bg-white text-rose-600 shadow-sm' : 'text-slate-500 hover:bg-slate-200/50' }}">Uji
                                    Nuklida</a>
                                <a href="?scope=Radioterapi"
                                    class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $scopeFilter == 'Radioterapi' ? 'bg-white text-purple-600 shadow-sm' : 'text-slate-500 hover:bg-slate-200/50' }}">Radioterapi</a>
                                <a href="?scope=Radioaktivitas Lingkungan"
                                    class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $scopeFilter == 'Radioaktivitas Lingkungan' ? 'bg-white text-teal-600 shadow-sm' : 'text-slate-500 hover:bg-slate-200/50' }}">Radioaktivitas</a>
                            </div>
                            <div class="relative w-full sm:w-64">
                                <input type="text" id="tableSearch" placeholder="Cari dokumen..."
                                    class="w-full pl-9 pr-4 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all">
                                <i class="fas fa-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left" id="mainTable">
                                <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                                    <tr>
                                        <th class="px-6 py-4 font-bold text-slate-600">Dokumen</th>
                                        <th class="px-6 py-4 font-bold text-slate-600">Kategori</th>
                                        <th class="px-6 py-4 font-bold text-center text-slate-600">Status</th>
                                        <th class="px-6 py-4 font-bold text-center text-slate-600">Versi</th>
                                        <th class="px-6 py-4 font-bold text-center text-slate-600">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    @forelse($mySubmissions as $item)
                                        <tr class="group hover:bg-slate-50/50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-4">
                                                    <div
                                                        class="w-10 h-10 rounded-lg bg-teal-50 flex items-center justify-center text-teal-600 border border-teal-100">
                                                        <i class="far fa-file-pdf text-lg"></i>
                                                    </div>
                                                    <div>
                                                        <div class="font-bold text-slate-800 text-sm">
                                                            {{ $item->title }}</div>
                                                        <div class="text-xs text-slate-400 mt-0.5">
                                                            {{ $item->created_at->format('d M Y') }} •
                                                            {{ $item->created_at->format('H:i') }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                @php
                                                    $parts = explode(' - ', $item->type);
                                                    $scope = $parts[0] ?? $item->type;
                                                    $type = $parts[1] ?? '';

                                                    // Logika pemetaan warna berdasarkan scope
                                                    $badgeColors = [
                                                        'LUK'                       => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                                                        'Eksterna'                  => 'bg-orange-50 text-orange-600 border-orange-100',
                                                        'Nuklida'                   => 'bg-rose-50 text-rose-600 border-rose-100',
                                                        'Radioterapi'               => 'bg-purple-50 text-purple-600 border-purple-100',
                                                        'Radioaktivitas Lingkungan' => 'bg-teal-50 text-teal-600 border-teal-100',
                                                    ];

                                                    // Ambil warna, kalau tidak ketemu default ke slate abu-abu
                                                    $currentColor = $badgeColors[$scope] ?? 'bg-slate-50 text-slate-600 border-slate-200';
                                                @endphp

                                                <div class="flex flex-col items-start gap-1">
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide border {{ $currentColor }}">
                                                        {{ $scope }}
                                                    </span>
                                                    
                                                    @if ($type)
                                                        <span class="text-xs text-slate-500">{{ $type }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                @if ($item->status == 'pending')
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-1 rounded-full bg-amber-50 border border-amber-200 text-xs font-bold text-amber-700 gap-1.5">
                                                        <span
                                                            class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                        Menunggu
                                                    </span>
                                                @elseif($item->status == 'approved')
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-xs font-bold text-emerald-700 gap-1.5">
                                                        <i class="fas fa-check text-[10px]"></i> Disetujui
                                                    </span>
                                                @elseif($item->status == 'rejected')
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-1 rounded-full bg-rose-50 border border-rose-200 text-xs font-bold text-rose-700 gap-1.5">
                                                        <i class="fas fa-exclamation text-[10px]"></i> Revisi
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <button
                                                    onclick='openHistoryModal(@json($item->files), "{{ $item->status }}", "{{ $item->updated_at }}")'
                                                    class="px-2.5 py-1 rounded-lg bg-slate-50 border border-slate-200 text-slate-600 text-xs font-bold hover:border-primary transition-all">
                                                    v{{ $item->files ? $item->files->count() : 0 }}
                                                </button>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <div class="flex items-center justify-center gap-2">
                                                    <a href="{{ asset('storage/' . $item->file_path) }}"
                                                        target="_blank"
                                                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-primary transition-all"
                                                        title="Lihat File"><i class="far fa-eye"></i></a>
                                                    @if ($item->status == 'rejected')
                                                        <button
                                                            onclick="openModal('edit', {{ $item->id }}, '{{ $item->type }}', '{{ $item->title }}')"
                                                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-amber-50 border border-amber-200 text-amber-600 transition-all"
                                                            title="Upload Revisi"><i
                                                                class="fas fa-upload"></i></button>
                                                    @endif
                                                    @if ($item->status == 'pending')
                                                        <button onclick="deleteSubmission({{ $item->id }})"
                                                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-rose-500 transition-all"
                                                            title="Batalkan"><i class="fas fa-trash-alt"></i></button>
                                                        <form id="delete-form-{{ $item->id }}"
                                                            action="{{ route('submission.destroy', $item->id) }}"
                                                            method="POST" style="display: none;">@csrf
                                                            @method('DELETE')</form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-24 text-center text-slate-400">Belum ada
                                                dokumen.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-8 text-center text-xs text-slate-400">
                    &copy; 2026 Sistem Informasi Jaminan Mutu Ketenaganukliran
                </div>
            </main>
        </div>
    </div>

    <!-- MODAL FORM -->
    <div id="submissionModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/50 transition-opacity opacity-0" id="modalBackdrop"
            onclick="closeModal()"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all w-full max-w-lg opacity-0 scale-95"
                    id="modalPanel">
                    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-white">
                        <h3 class="text-lg font-bold text-slate-800" id="modalTitle"><span>Upload Laporan</span></h3>
                        <button onclick="closeModal()"
                            class="text-slate-400 hover:text-slate-600 transition-colors"><i
                                class="fas fa-times text-lg"></i></button>
                    </div>
                    <form id="submissionForm" method="POST" enctype="multipart/form-data"
                        action="{{ route('submission.store') }}" class="px-6 pb-6 pt-2 space-y-4 text-left">
                        @csrf
                        <div id="methodField"></div>
                        <input type="hidden" name="type" id="finalType">
                        <input type="hidden" id="scopeValue" value="LUK">

                        <div class="space-y-1.5 !mt-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Lingkup
                                Layanan</label>
                            <div class="relative" id="customScopeDropdown">
                                <button type="button" onclick="toggleDropdown('dropdownMenuScope', 'arrowScope')"
                                    class="w-full text-left bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-lg py-2.5 px-4 flex justify-between items-center transition-all">
                                    <span id="selectedScopeText" class="text-sm">Lembaga Uji Kesesuaian</span>
                                    <i class="fas fa-chevron-down text-slate-400 text-xs transition-transform"
                                        id="arrowScope"></i>
                                </button>
                                <div id="dropdownMenuScope"
                                    class="hidden absolute z-50 mt-1 w-full bg-white border border-slate-100 rounded-lg shadow-xl max-h-60 overflow-y-auto p-1">
                                    <button type="button" onclick="selectScope('LUK', 'Lembaga Uji Kesesuaian')"
                                        class="w-full text-left px-4 py-2 hover:bg-slate-50 hover:text-primary rounded-md text-sm font-medium transition-all">Lembaga
                                        Uji Kesesuaian</button>
                                    <button type="button"
                                        onclick="selectScope('Eksterna', 'Evaluasi Dosis Eksterna')"
                                        class="w-full text-left px-4 py-2 hover:bg-slate-50 hover:text-primary rounded-md text-sm font-medium transition-all">Evaluasi
                                        Dosis Eksterna</button>
                                    <button type="button"
                                        onclick="selectScope('Nuklida', 'Standardisasi Radionuklida')"
                                        class="w-full text-left px-4 py-2 hover:bg-slate-50 hover:text-primary rounded-md text-sm font-medium transition-all">Standardisasi
                                        Radionuklida</button>
                                    <button type="button"
                                        onclick="selectScope('Radioterapi', 'Kalibrasi Radioterapi')"
                                        class="w-full text-left px-4 py-2 hover:bg-slate-50 hover:text-primary rounded-md text-sm font-medium transition-all">Kalibrasi
                                        Radioterapi</button>
                                    <button type="button"
                                        onclick="selectScope('Radioaktivitas Lingkungan', 'Lab Radioaktivitas')"
                                        class="w-full text-left px-4 py-2 hover:bg-slate-50 hover:text-primary rounded-md text-sm font-medium transition-all">Lab
                                        Radioaktivitas</button>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Judul
                                Dokumen</label>
                            <input type="text" name="title" id="inputTitle"
                                class="w-full bg-white border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all"
                                placeholder="Contoh: Laporan Januari 2026" required>
                            <input type="hidden" id="reportSelect" value="Laporan Tahunan">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">File
                                Dokumen</label>
                            <div
                                class="relative border-2 border-dashed border-slate-300 rounded-xl p-6 bg-slate-50 hover:bg-white hover:border-primary transition-all text-center">
                                <input type="file" name="file_upload" id="inputFile"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20"
                                    accept=".pdf" onchange="updateFileName(this)">
                                <div id="dropzoneContent">
                                    <i class="fas fa-cloud-upload-alt text-2xl text-slate-400 mb-2"></i>
                                    <p class="text-sm font-medium text-slate-600">Klik untuk upload file PDF</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Maksimal 10MB</p>
                                </div>
                                <div id="fileNameDisplay" class="hidden relative z-10">
                                    <div class="flex items-center justify-center gap-2 text-primary font-bold text-sm">
                                        <i class="fas fa-file-pdf"></i> <span
                                            id="selectedFileName">filename.pdf</span></div>
                                    <span class="text-[10px] text-slate-400 block mt-1">Klik untuk ganti file</span>
                                </div>
                            </div>
                            <p class="text-[10px] text-amber-600 flex items-center gap-1 hidden px-1" id="fileNote">
                                <i class="fas fa-info-circle"></i> Biarkan kosong jika tidak ingin mengganti file.</p>
                        </div>

                        <div id="radiotherapyNote" class="hidden animate-fade-in-up">
                            <div class="bg-purple-50 border border-purple-200 rounded-lg p-3 flex gap-3">
                                <i class="fas fa-info-circle text-purple-600 mt-0.5"></i>
                                <div class="text-xs text-purple-800 leading-relaxed">
                                    <strong>Catatan Radioterapi:</strong><br>
                                    Harap pastikan Anda telah menyertakan <b>Hasil Uji LHU</b> dan <b>Sertifikat</b> terkait dalam satu file PDF yang sama sebelum mengunggah.
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <button type="button" onclick="closeModal()"
                                class="px-5 py-2.5 rounded-lg border border-slate-200 text-slate-600 font-bold text-sm hover:bg-slate-50 transition-colors">Batal</button>
                            <button type="button" onclick="submitForm()"
                                class="flex-1 py-2.5 rounded-lg bg-primary text-white font-bold text-sm shadow-md shadow-primary/20 hover:bg-teal-800 transition-all">Simpan
                                Laporan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- HISTORY MODAL -->
    <div id="historyModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/50 transition-opacity opacity-0" id="historyBackdrop"
            onclick="closeHistoryModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-xl bg-white shadow-xl transition-all w-full max-w-4xl opacity-0 scale-95 flex flex-col max-h-[85vh]"
                id="historyPanel">
                <div
                    class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-white flex-shrink-0">
                    <h3 class="font-bold text-lg text-slate-800 flex items-center gap-2"><i
                            class="fas fa-history text-primary"></i> Riwayat Revisi</h3>
                    <button onclick="closeHistoryModal()"
                        class="text-slate-400 hover:text-slate-600 transition-all"><i
                            class="fas fa-times text-lg"></i></button>
                </div>
                <div class="overflow-y-auto p-0 bg-white flex-1 modal-scroll">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 sticky top-0 z-10 border-b border-slate-100">
                            <tr>
                                <th
                                    class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest w-20 text-center">
                                    Ver</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest w-1/3">
                                    User Upload</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Respon
                                    Admin</th>
                            </tr>
                        </thead>
                        <tbody id="historyTableBody" class="divide-y divide-slate-100 bg-white"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <script>
        // === POP-UP LOGIC ===
        function closeNotification(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('opacity-0');
                modal.querySelector('div').classList.add('scale-95');
                setTimeout(() => {
                    modal.style.display = 'none';
                }, 300);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Auto close success modal & Progress bar
            const successModal = document.getElementById('successModal');
            if (successModal) {
                const pb = document.getElementById('progressBar');
                setTimeout(() => {
                    pb.style.transition = 'width 3.5s linear';
                    pb.style.width = '0%';
                }, 100);
                setTimeout(() => {
                    closeNotification('successModal');
                }, 4000);
            }

            // Stats Logic
            const dataPending = parseInt('{{ $myPending }}') || 0;
            const dataApproved = parseInt('{{ $myApproved }}') || 0;
            const dataRejected = parseInt('{{ $myRejected }}') || 0;
            const totalData = dataPending + dataApproved + dataRejected;

            if (totalData > 0) {
                const options = {
                    series: [dataPending, dataApproved, dataRejected],
                    chart: {
                        type: 'donut',
                        height: 180,
                        fontFamily: 'inherit'
                    },
                    labels: ['Menunggu', 'Disetujui', 'Revisi'],
                    colors: ['#f59e0b', '#10b981', '#f43f5e'],
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%'
                            }
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    legend: {
                        show: false
                    }
                };
                new ApexCharts(document.querySelector("#statusChart"), options).render();
            }
            const searchInput = document.getElementById('tableSearch');
            const tableRows = document.querySelectorAll('#mainTable tbody tr');

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();

                tableRows.forEach(row => {
                    // Kita ambil teks dari kolom pertama (Dokumen) dan kedua (Kategori)
                    const docTitle = row.querySelector('td:first-child')?.innerText.toLowerCase() || "";
                    
                    if (docTitle.includes(searchTerm)) {
                        row.style.display = ""; // Tampilkan jika cocok
                    } else {
                        row.style.display = "none"; // Sembunyikan jika tidak cocok
                    }
                });
                
                // Opsional: Tampilkan pesan "Data tidak ditemukan" jika semua baris tersembunyi
                checkEmptyTable();
            });
        });

        function checkEmptyTable() {
            const tbody = document.querySelector('#mainTable tbody');
            const visibleRows = Array.from(tbody.querySelectorAll('tr')).filter(row => row.style.display !== 'none');
            
            // Hapus pesan "tidak ditemukan" yang lama jika ada
            const oldMsg = document.getElementById('no-results-msg');
            if (oldMsg) oldMsg.remove();

            if (visibleRows.length === 0) {
                const noResults = `<tr id="no-results-msg"><td colspan="5" class="px-6 py-24 text-center text-slate-400">Dokumen tidak ditemukan.</td></tr>`;
                tbody.insertAdjacentHTML('beforeend', noResults);
            }
        }

        // === FILE SIZE VALIDATION ===
        function updateFileName(input) {
            const display = document.getElementById('fileNameDisplay');
            const content = document.getElementById('dropzoneContent');
            const nameSpan = document.getElementById('selectedFileName');

            if (input.files && input.files[0]) {
                const fileSize = input.files[0].size / 1024 / 1024; // MB
                if (fileSize > 10) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Terlalu Besar',
                        text: 'Maksimal ukuran file laporan adalah 2 MB.',
                        confirmButtonColor: '#0f766e',
                        customClass: {
                            popup: 'rounded-2xl'
                        }
                    });
                    resetFileInput();
                    return;
                }
                nameSpan.innerText = input.files[0].name;
                content.classList.add('hidden');
                display.classList.remove('hidden');
            } else {
                resetFileInput();
            }
        }

        // Sidebar & UI Helpers
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        function toggleModalAnimation(modalId, show) {
            const modal = document.getElementById(modalId);
            const backdrop = modal.querySelector('div[id$="Backdrop"]');
            const panel = modal.querySelector('div[id$="Panel"]');
            if (show) {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    backdrop.classList.remove('opacity-0');
                    panel.classList.remove('opacity-0', 'scale-95');
                    panel.classList.add('opacity-100', 'scale-100');
                }, 10);
            } else {
                backdrop.classList.add('opacity-0');
                panel.classList.add('opacity-0', 'scale-95');
                panel.classList.remove('opacity-100', 'scale-100');
                setTimeout(() => modal.classList.add('hidden'), 300);
            }
        }

        function toggleDropdown(menuId, arrowId) {
            const menu = document.getElementById(menuId);
            const arrow = document.getElementById(arrowId);
            menu.classList.toggle('hidden');
            if (arrow) arrow.classList.toggle('rotate-180');
        }

        function selectScope(value, text, shouldToggle = true) {
            document.getElementById('scopeValue').value = value;
            document.getElementById('selectedScopeText').innerText = text;
            
            // Logika untuk menampilkan note Radioterapi
            const radioNote = document.getElementById('radiotherapyNote');
            if (value === 'Radioterapi') {
                radioNote.classList.remove('hidden');
            } else {
                radioNote.classList.add('hidden');
            }

            if (shouldToggle) toggleDropdown('dropdownMenuScope', 'arrowScope');
        }

        function openModal(mode, id = null, type = '', title = '') {
            const form = document.getElementById('submissionForm');
            const modalTitleSpan = document.getElementById('modalTitle').querySelector('span');
            const methodField = document.getElementById('methodField');
            const inputTitle = document.getElementById('inputTitle');
            const fileNote = document.getElementById('fileNote');

            if (mode === 'add') {
                modalTitleSpan.innerText = 'Upload Laporan Baru';
                form.action = "{{ route('submission.store') }}";
                methodField.innerHTML = '';
                inputTitle.value = '';
                fileNote.classList.add('hidden');
                document.getElementById('radiotherapyNote').classList.add('hidden'); // Reset note
                selectScope('LUK', 'Lembaga Uji Kesesuaian', false); // Reset scope     
            } else {
                modalTitleSpan.innerText = 'Revisi Laporan';
                form.action = "{{ url('/submission/update') }}/" + id;
                methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
                inputTitle.value = title;
                fileNote.classList.remove('hidden');
            }
            resetFileInput();
            toggleModalAnimation('submissionModal', true);
        }

        function closeModal() {
            toggleModalAnimation('submissionModal', false);
        }

        function closeHistoryModal() {
            toggleModalAnimation('historyModal', false);
        }

        function submitForm() {
            const scope = document.getElementById('scopeValue').value;
            const report = document.getElementById('reportSelect').value;
            const fileInput = document.getElementById('inputFile');

            // Validasi file wajib untuk upload baru
            if (!document.getElementById('methodField').innerHTML && !fileInput.files[0]) {
                Swal.fire({
                    icon: 'warning',
                    title: 'File Kosong',
                    text: 'Silakan pilih file laporan PDF terlebih dahulu.',
                    confirmButtonColor: '#0f766e'
                });
                return;
            }

            document.getElementById('finalType').value = scope + ' - ' + report;
            document.getElementById('submissionForm').submit();
        }

        function resetFileInput() {
            document.getElementById('inputFile').value = '';
            document.getElementById('dropzoneContent').classList.remove('hidden');
            document.getElementById('fileNameDisplay').classList.add('hidden');
        }

        function openHistoryModal(files, currentStatus, statusDate) {
            const tbody = document.getElementById('historyTableBody');
            tbody.innerHTML = '';
            if (!files || files.length === 0) {
                tbody.innerHTML =
                    '<tr><td colspan="3" class="px-6 py-12 text-center text-slate-300">Belum ada riwayat revisi.</td></tr>';
            } else {
                files.sort((a, b) => b.version - a.version).forEach((file, index) => {
                    const createdDate = new Date(file.created_at).toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'short',
                        year: 'numeric'
                    });
                    let adminResponse = `<span class="text-slate-300 italic text-xs">Menunggu respon...</span>`;
                    if (file.admin_note || file.admin_file) {
                        adminResponse = `
                            <div class="flex flex-col gap-2">
                                ${file.admin_note ? `<div class="bg-blue-50 p-3 rounded-lg text-xs text-slate-600 border border-blue-100">${file.admin_note}</div>` : ''}
                                ${file.admin_file ? `<a href="/storage/${file.admin_file}" target="_blank" class="text-emerald-600 font-bold text-[10px] hover:underline flex items-center gap-1"><i class="fas fa-file-contract"></i> Unduh SK/Balasan</a>` : ''}
                            </div>`;
                    }
                    tbody.innerHTML += `
                        <tr class="border-b border-slate-50 last:border-none">
                            <td class="px-6 py-4 text-center"><span class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center border text-xs font-bold">v${file.version}</span></td>
                            <td class="px-6 py-4"><div class="text-xs font-bold text-slate-700">${createdDate}</div><a href="/storage/${file.file_path}" target="_blank" class="text-primary text-[10px] truncate max-w-[150px] block hover:underline">${file.file_name}</a></td>
                            <td class="px-6 py-4">${adminResponse}</td>
                        </tr>`;
                });
            }
            toggleModalAnimation('historyModal', true);
        }

        function deleteSubmission(id) {
            Swal.fire({
                title: 'Batalkan Pengajuan?',
                text: "Data akan dihapus permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f43f5e',
                cancelButtonColor: '#e2e8f0',
                confirmButtonText: 'Ya, Hapus',
                customClass: {
                    popup: 'rounded-2xl'
                }
            }).then((result) => {
                if (result.isConfirmed) document.getElementById(`delete-form-${id}`).submit();
            });
        }
    </script>
</body>

</html>