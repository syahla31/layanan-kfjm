<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Trail | SI-MUTU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_red.css"> 
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <style>
        /* Custom style agar input Flatpickr terlihat seperti input kamu yang lain */
        .flatpickr-input[readonly] {
            background-color: white !important;
        }
        body {
            font-family: 'Inter', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }

        /* Custom scrollbar */
        main::-webkit-scrollbar {
            width: 4px;
        }

        main::-webkit-scrollbar-track {
            background: transparent;
        }

        main::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }

        /* --- PENGATURAN EXPORT PDF / PRINT --- */
        @media print {
            @page {
                size: A4 portrait;
                margin: 30mm 20mm;
            }

            aside,
            header,
            form,
            .pagination-footer,
            button,
            .no-print {
                display: none !important;
            }

            body {
                background-color: white !important;
                color: black !important;
                overflow: visible !important;
                height: auto !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .flex.h-screen {
                display: block !important;
                height: auto !important;
            }

            main {
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                background: white !important;
            }

            .bg-white {
                border: none !important;
                box-shadow: none !important;
                background: white !important;
            }

            .rounded-\[2\.5rem\] {
                border-radius: 0 !important;
                border: none !important;
            }

            .overflow-hidden {
                overflow: visible !important;
            }

            table {
                width: 100% !important;
                border-collapse: collapse !important;
                table-layout: fixed !important;
            }

            thead {
                display: table-header-group !important;
                background-color: #f1f5f9 !important;
            }

            th {
                background-color: #f8fafc !important;
                color: #000 !important;
                border: 1px solid #94a3b8 !important;
                padding: 14px 10px !important;
                font-size: 10px !important;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                font-weight: 800 !important;
            }

            td {
                border: 1px solid #cbd5e1 !important;
                padding: 14px 10px !important;
                font-size: 10px !important;
                vertical-align: middle !important;
                white-space: normal !important;
                word-wrap: break-word !important;
            }

            .print-header {
                display: block !important;
                margin-bottom: 40px;
                text-align: center;
                border-bottom: 3px solid #000;
                padding-bottom: 20px;
            }
        }

        .print-header {
            display: none;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 overflow-hidden" x-data="{ sidebarOpen: false }">

    <!-- LOGIKA FETCH DATA DATABASE -->
    @php
        use App\Models\ActivityLog;
        use Illuminate\Support\Facades\Request;

        $f_action = Request::get('action') ?? 'Semua Aktivitas';
        $f_date = Request::get('date');
        $f_category = Request::get('category') ?? 'Semua Kategori';

        $query = ActivityLog::with('user')->latest();

        if ($f_action && $f_action !== 'Semua Aktivitas') {
            $query->where('action', $f_action);
        }

        if ($f_date) {
            $query->whereDate('created_at', $f_date);
        }

        if ($f_category && $f_category !== 'Semua Kategori') {
            $query->whereHas('user', function ($q) use ($f_category) {
                $q->where('category', $f_category);
            });
        }

        $logs = $query->paginate(15);
    @endphp

    <div class="flex h-screen w-full overflow-hidden">

        <!-- SIDEBAR -->
        @include('components.internal-sidebar')

        <!-- KONTEN UTAMA -->
        <div class="flex-1 flex flex-col min-w-0 bg-slate-50">

            <!-- HEADER -->
            <header
                class="bg-white border-b border-slate-200 h-20 flex items-center justify-between px-6 lg:px-10 shrink-0 relative z-30">
                <div class="flex items-center gap-5">
                    <button @click="sidebarOpen = true"
                        class="lg:hidden w-11 h-11 flex items-center justify-center rounded-2xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-900 tracking-tight leading-tight">Audit Trail</h2>
                        <div class="flex items-center gap-2 mt-1">
                            <i class="fas fa-history text-[10px] text-red-500"></i>
                            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-[0.15em] leading-none">Log
                                Aktivitas Pengguna</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="button" onclick="window.print()"
                        class="bg-white border border-slate-200 text-slate-600 px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all shadow-sm flex items-center gap-2 active:scale-95">
                        <i class="fas fa-file-pdf text-red-500"></i> Export PDF
                    </button>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6 lg:p-10">

                <!-- HEADER KHUSUS PRINT -->
                <div class="print-header">
                    <div
                        style="display: flex; align-items: center; justify-content: center; gap: 15px; margin-bottom: 10px;">
                        <div
                            style="width: 50px; height: 50px; background: #000; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white;">
                            <i class="fas fa-shield-alt text-2xl"></i>
                        </div>
                        <div style="text-align: left;">
                            <h1 style="font-size: 22px; font-weight: 900; margin: 0; line-height: 1.1; color: #000;">
                                LAPORAN AUDIT TRAIL SISTEM</h1>
                            <p
                                style="font-size: 11px; color: #334155; font-weight: 700; text-transform: uppercase; margin-top: 6px; letter-spacing: 1px;">
                                Aplikasi SI-MUTU - Internal DKKN BAPETEN</p>
                        </div>
                    </div>
                    <div style="font-size: 10px; color: #64748b; margin-top: 15px; font-weight: 600;">
                        Dokumen ini dicetak otomatis oleh sistem pada:
                        <strong>{{ now()->format('d F Y, H:i:s') }}</strong>
                    </div>
                </div>

                <!-- Filter Toolbar Modern -->
                <form id="filter-form" action="{{ url('/internal/logs') }}" method="GET"
                    class="mb-8 grid grid-cols-1 sm:grid-cols-4 gap-6 items-end">

                    <!-- Filter Jenis Aktivitas -->
                    <div x-data="{
                        open: false,
                        selected: '{{ $f_action }}',
                        options: ['Semua Aktivitas', 'LOGIN', 'LOGOUT', 'UPLOAD', 'UPDATE', 'DELETE', 'VERIFIKASI']
                    }" class="relative">
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Jenis
                            Aktivitas</label>
                        <input type="hidden" name="action" :value="selected">
                        <button type="button" @click="open = !open" @click.away="open = false"
                            class="w-full h-12 bg-white border border-slate-200 rounded-2xl px-5 flex items-center justify-between text-xs font-bold text-slate-700 hover:border-red-500 transition-all shadow-sm">
                            <span x-text="selected"></span>
                            <i class="fas fa-chevron-down text-[10px] text-slate-400 transition-transform"
                                :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-cloak x-transition
                            class="absolute z-50 w-full mt-2 bg-white border border-slate-100 rounded-2xl shadow-xl overflow-hidden">
                            <template x-for="option in options">
                                <button type="button"
                                    @click="selected = option; open = false; $nextTick(() => $el.closest('form').submit())"
                                    class="w-full px-5 py-3 text-left text-xs font-bold text-slate-600 hover:bg-red-50 hover:text-red-600 transition-colors"
                                    :class="selected === option ? 'bg-red-50 text-red-600' : ''"
                                    x-text="option"></button>
                            </template>
                        </div>
                    </div>

                    <!-- Filter Kategori Pelaku -->
                    <div x-data="{
                        open: false,
                        selected: '{{ $f_category }}',
                        displaySelected: '{{ $f_category === 'sinarx' ? 'Sinar-X' : ($f_category === 'uji' ? 'Lembaga Uji' : ucfirst($f_category)) }}',
                        options: [
                            { val: 'Semua Kategori', label: 'Semua Kategori' },
                            { val: 'pelatihan', label: 'Pelatihan' },
                            { val: 'uji', label: 'Lembaga Uji' },
                            { val: 'sinarx', label: 'Sinar-X' }
                        ]
                    }" class="relative">
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Kategori
                            Pelaku</label>
                        <input type="hidden" name="category" :value="selected">
                        <button type="button" @click="open = !open" @click.away="open = false"
                            class="w-full h-12 bg-white border border-slate-200 rounded-2xl px-5 flex items-center justify-between text-xs font-bold text-slate-700 hover:border-red-500 transition-all shadow-sm">
                            <span x-text="displaySelected"></span>
                            <i class="fas fa-chevron-down text-[10px] text-slate-400 transition-transform"
                                :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-cloak x-transition
                            class="absolute z-50 w-full mt-2 bg-white border border-slate-100 rounded-2xl shadow-xl overflow-hidden">
                            <template x-for="opt in options">
                                <button type="button"
                                    @click="selected = opt.val; displaySelected = opt.label; open = false; $nextTick(() => $el.closest('form').submit())"
                                    class="w-full px-5 py-3 text-left text-xs font-bold text-slate-600 hover:bg-red-50 hover:text-red-600 transition-colors"
                                    :class="selected === opt.val ? 'bg-red-50 text-red-600' : ''"
                                    x-text="opt.label"></button>
                            </template>
                        </div>
                    </div>

                   <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">
                            Pilih Tanggal
                        </label>

                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none z-10">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 text-slate-400 group-focus-within:text-red-500 transition"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>

                            <input type="text" id="date-picker" name="date" value="{{ $f_date }}"
                                placeholder="Pilih Tanggal..."
                                class="w-full h-12 bg-white border border-slate-200 rounded-2xl 
                                    pl-11 pr-4 text-xs font-semibold text-slate-700 
                                    outline-none shadow-sm transition-all duration-200
                                    hover:border-slate-300 focus:border-red-500 cursor-pointer">
                        </div>
                    </div>
                    <!-- Tombol Reset / Submit -->
                    <div class="flex gap-2">
                        <button type="submit"
                            class="flex-[2] h-12 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg active:scale-95 transition-all hover:bg-black">
                            Terapkan Filter
                        </button>
                        @if ($f_action !== 'Semua Aktivitas' || $f_date || $f_category !== 'Semua Kategori')
                            <a href="{{ url('/internal/logs') }}"
                                class="flex-1 h-12 flex items-center justify-center bg-slate-200 hover:bg-slate-300 text-slate-600 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all"
                                title="Reset">
                                <i class="fas fa-undo"></i>
                            </a>
                        @endif
                    </div>
                </form>

                <!-- Table Container -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden mb-8">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead
                                class="text-[10px] text-slate-400 uppercase bg-slate-50/50 font-black tracking-widest border-b border-slate-100">
                                <tr>
                                    <th class="px-8 py-5" style="width: 18%;">Waktu & Tanggal</th>
                                    <th class="px-8 py-5" style="width: 22%;">Identitas Pelaku</th>
                                    <th class="px-8 py-5" style="width: 15%;">Kategori</th>
                                    <th class="px-8 py-5" style="width: 32%;">Deskripsi Aktivitas</th>
                                    <th class="px-8 py-5 text-center" style="width: 13%;">IP Address</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse($logs as $log)
                                    <tr class="hover:bg-slate-50/80 transition-all group">
                                        <td class="px-8 py-6">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-2 h-8 bg-slate-100 rounded-full group-hover:bg-red-500 transition-all duration-300 no-print-bg">
                                                </div>
                                                <div>
                                                    <p class="font-black text-slate-900 text-xs leading-none">
                                                        {{ $log->created_at->format('d M Y') }}</p>
                                                    <p
                                                        class="text-[10px] text-slate-400 font-mono mt-1 leading-none tracking-tight">
                                                        {{ $log->created_at->format('H:i:s') }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6">
                                            <div class="flex items-center gap-4">
                                                <div
                                                    class="w-10 h-10 rounded-xl bg-slate-50 text-slate-500 flex items-center justify-center text-[10px] font-black border border-slate-200 shrink-0">
                                                    {{ substr($log->user->name ?? 'S', 0, 2) }}
                                                </div>
                                                <div class="overflow-hidden">
                                                    <span
                                                        class="font-bold text-slate-900 block leading-tight truncate">{{ $log->user->name ?? 'Sistem / Terhapus' }}</span>
                                                    <span
                                                        class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">{{ $log->user->role ?? '-' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6">
                                            @php
                                                $catName = $log->user->category ?? 'umum';
                                                $catClass = match ($catName) {
                                                    'pelatihan' => 'bg-blue-50 text-blue-600 border-blue-100',
                                                    'uji' => 'bg-purple-50 text-purple-600 border-purple-100',
                                                    'sinarx' => 'bg-orange-50 text-orange-600 border-orange-100',
                                                    default => 'bg-slate-50 text-slate-500 border-slate-200',
                                                };
                                            @endphp
                                            <span
                                                class="{{ $catClass }} border text-[9px] font-black px-3 py-1 rounded-lg uppercase tracking-widest inline-block">
                                                {{ $catName === 'sinarx' ? 'Sinar-X' : ($catName === 'uji' ? 'Lembaga Uji' : ucfirst($catName)) }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-6">
                                            @php
                                                $badgeColor = match ($log->action) {
                                                    'LOGIN' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                                    'LOGOUT' => 'bg-slate-50 text-slate-600 border-slate-200',
                                                    'UPLOAD' => 'bg-blue-50 text-blue-600 border-blue-100',
                                                    'DELETE' => 'bg-red-50 text-red-600 border-red-100',
                                                    'UPDATE' => 'bg-amber-50 text-amber-600 border-amber-100',
                                                    'VERIFIKASI' => 'bg-purple-50 text-purple-600 border-purple-100',
                                                    default => 'bg-slate-50 text-slate-600 border-slate-200',
                                                };
                                            @endphp
                                            <div class="flex items-start gap-3">
                                                <span
                                                    class="{{ $badgeColor }} border text-[9px] font-black px-2.5 py-1 rounded-lg uppercase tracking-widest shrink-0 mt-0.5">
                                                    {{ $log->action }}
                                                </span>
                                                <span
                                                    class="text-slate-600 text-xs font-medium whitespace-normal leading-relaxed">
                                                    {{ $log->description }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 text-center">
                                            <span
                                                class="px-3 py-1 bg-slate-50 rounded-lg text-[10px] font-mono text-slate-400 border border-slate-100">
                                                {{ $log->ip_address }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-8 py-24 text-center">
                                            <div class="flex flex-col items-center opacity-30">
                                                <div
                                                    class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center text-slate-300 mb-6 border-4 border-white shadow-inner">
                                                    <i class="fas fa-history text-3xl"></i>
                                                </div>
                                                <h4
                                                    class="text-sm font-black uppercase tracking-widest text-slate-800">
                                                    Tidak Ada Rekaman</h4>
                                                <p class="text-xs text-slate-400 mt-2 font-medium">Belum ada data
                                                    aktivitas untuk kriteria ini.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination Footer -->
                <div class="pagination-footer flex flex-col sm:flex-row justify-between items-center gap-4 px-2">
                    <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        Menampilkan {{ $logs->firstItem() ?? 0 }} - {{ $logs->lastItem() ?? 0 }} dari
                        {{ $logs->total() }} Data
                    </div>

                    <div class="flex items-center gap-2">
                        @if ($logs->onFirstPage())
                            <span
                                class="px-5 py-2.5 bg-slate-100 text-slate-300 rounded-xl text-[10px] font-black uppercase tracking-widest cursor-not-allowed">
                                <i class="fas fa-chevron-left mr-2"></i> Sebelumnya
                            </span>
                        @else
                            <a href="{{ $logs->previousPageUrl() }}"
                                class="px-5 py-2.5 bg-white border border-slate-200 text-slate-600 hover:text-red-600 hover:border-red-100 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm">
                                <i class="fas fa-chevron-left mr-2"></i> Sebelumnya
                            </a>
                        @endif

                        @if ($logs->hasMorePages())
                            <a href="{{ $logs->nextPageUrl() }}"
                                class="px-5 py-2.5 bg-white border border-slate-200 text-slate-600 hover:text-red-600 hover:border-red-100 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm">
                                Selanjutnya <i class="fas fa-chevron-right ml-2"></i>
                            </a>
                        @else
                            <span
                                class="px-5 py-2.5 bg-slate-100 text-slate-300 rounded-xl text-[10px] font-black uppercase tracking-widest cursor-not-allowed">
                                Selanjutnya <i class="fas fa-chevron-right ml-2"></i>
                            </span>
                        @endif
                    </div>
                </div>

            </main>
        </div>
    </div>
</body>
</html>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("#date-picker", {
            dateFormat: "Y-m-d", // Format database (YYYY-MM-DD)
            altInput: true,      // Menampilkan format yang lebih cantik ke user
            altFormat: "d F Y",  // Contoh: 26 Februari 2026
            disableMobile: "true", // Memaksa UI yang sama di HP
            onChange: function(selectedDates, dateStr, instance) {
                // Otomatis submit form saat tanggal dipilih
                document.getElementById('filter-form').submit();
            }
        });
    });
</script>