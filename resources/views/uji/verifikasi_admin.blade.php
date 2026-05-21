<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Verifikasi Uji | SI-LAB ADMIN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .table-row-hover:hover td { background-color: #f0fdfa; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Custom Scrollbar Teal */
        .modal-scroll::-webkit-scrollbar { width: 6px; }
        .modal-scroll::-webkit-scrollbar-track { background: #f1f5f9; }
        .modal-scroll::-webkit-scrollbar-thumb { background: #5eead4; border-radius: 10px; }
        .modal-scroll::-webkit-scrollbar-thumb:hover { background: #2dd4bf; }

        /* =============================================
           CUSTOM DROPDOWN STYLES
        ============================================= */
        .cs-wrap { position: relative; }

        /* Trigger button */
        .cs-trigger {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 10px 14px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            cursor: pointer;
            transition: border-color 0.15s, box-shadow 0.15s;
            user-select: none;
        }
        .cs-trigger:hover { border-color: #0d9488; }
        .cs-trigger.open {
            border-color: #0d9488;
            box-shadow: 0 0 0 3px rgba(13,148,136,0.12);
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }

        .cs-trigger-left { display: flex; align-items: center; gap: 10px; }

        /* Avatar circle inside trigger */
        .cs-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #f1f5f9;
            border: 1.5px solid #e2e8f0;
            color: #94a3b8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            flex-shrink: 0;
            transition: background 0.15s, border-color 0.15s, color 0.15s;
        }
        .cs-avatar.filled {
            background: #ccfbf1;
            border-color: #5eead4;
            color: #0f766e;
        }

        .cs-trigger-text { font-size: 13px; color: #1e293b; line-height: 1.2; }
        .cs-trigger-text.placeholder { color: #94a3b8; }
        .cs-trigger-kode { font-size: 10px; color: #94a3b8; font-family: 'Courier New', monospace; margin-top: 1px; }

        /* Chevron icon */
        .cs-chevron {
            color: #94a3b8;
            transition: transform 0.2s ease;
            flex-shrink: 0;
        }
        .cs-trigger.open .cs-chevron { transform: rotate(180deg); }

        /* Dropdown panel */
        .cs-panel {
            position: absolute;
            left: 0; right: 0; top: 100%;
            background: #fff;
            border: 1.5px solid #0d9488;
            border-top: none;
            border-bottom-left-radius: 12px;
            border-bottom-right-radius: 12px;
            box-shadow: 0 12px 32px -4px rgba(0,0,0,0.12);
            z-index: 200;
            overflow: hidden;
            max-height: 0;
            opacity: 0;
            transition: max-height 0.22s cubic-bezier(0.4,0,0.2,1), opacity 0.18s;
        }
        .cs-panel.open { max-height: 260px; opacity: 1; }

        /* Search inside dropdown */
        .cs-search {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 9px 14px;
            border-bottom: 1px solid #f1f5f9;
            background: #f8fafc;
        }
        .cs-search i { color: #94a3b8; font-size: 11px; }
        .cs-search input {
            border: none;
            background: transparent;
            outline: none;
            font-size: 12px;
            color: #334155;
            width: 100%;
            font-family: 'Inter', sans-serif;
        }
        .cs-search input::placeholder { color: #cbd5e1; }

        /* Option list scroll */
        .cs-options {
            overflow-y: auto;
            max-height: 200px;
        }
        .cs-options::-webkit-scrollbar { width: 4px; }
        .cs-options::-webkit-scrollbar-track { background: #f8fafc; }
        .cs-options::-webkit-scrollbar-thumb { background: #5eead4; border-radius: 10px; }

        /* Single option row */
        .cs-option {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 14px;
            cursor: pointer;
            transition: background 0.1s;
        }
        .cs-option:hover { background: #f0fdfa; }
        .cs-option.selected { background: #f0fdfa; }

        .cs-opt-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #ccfbf1;
            border: 1.5px solid #5eead4;
            color: #0f766e;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 700;
            flex-shrink: 0;
        }
        .cs-opt-name { font-size: 12px; font-weight: 600; color: #1e293b; line-height: 1.2; }
        .cs-opt-kode { font-size: 10px; color: #94a3b8; font-family: 'Courier New', monospace; }
        .cs-opt-check { margin-left: auto; color: #0d9488; font-size: 11px; opacity: 0; transition: opacity 0.1s; }
        .cs-option.selected .cs-opt-check { opacity: 1; }

        .cs-empty {
            padding: 18px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <!-- 1. DATA FETCHING (KHUSUS UJI) -->
    @php
        use App\Models\Submission;
        use App\Models\User;
        
        if (!isset($data)) {
            $data = Submission::with(['user', 'files']) 
                        ->where('category', 'uji')
                        ->where('type', 'Verifikasi')
                        ->orderBy('created_at', 'desc')
                        ->get();
        }

        $users = User::where('role', 'user')->where('category', 'uji')->get();

        $waitingUser = $data->whereNull('user_note')->where('status', 'pending')->count();
        $needReview  = $data->whereNotNull('user_note')->where('status', 'pending')->count();
        $completed   = $data->where('status', 'approved')->count();
    @endphp

    <div class="flex h-screen overflow-hidden">
        
        <!-- SIDEBAR DESKTOP -->
        <div class="hidden md:flex h-full bg-teal-900">
            @include('components.uji-sidebar')
        </div>

        <!-- MOBILE SIDEBAR OVERLAY -->
        <div id="mobileSidebar" class="fixed inset-0 z-50 hidden">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="toggleSidebar()"></div>
            <div class="absolute left-0 top-0 bottom-0 w-64 bg-teal-900 shadow-xl transform transition-transform duration-300">
                @include('components.uji-sidebar')
            </div>
        </div>

        <div class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50 relative w-full">
            
            <!-- MOBILE HEADER -->
            <div class="md:hidden bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between z-20 sticky top-0 shadow-sm">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="p-2 text-slate-500 hover:text-teal-600 hover:bg-slate-100 rounded-lg transition-colors">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-teal-600 flex items-center justify-center text-white shadow-sm">
                            <i class="fas fa-flask text-sm"></i>
                        </div>
                        <span class="font-bold text-slate-800 text-sm tracking-wide">SI-LAB UJI</span>
                    </div>
                </div>
                <div class="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center text-teal-600 text-xs font-bold border border-teal-200">
                    {{ substr(Auth::user()->name ?? 'L', 0, 1) }}
                </div>
            </div>

            <!-- HEADER DESKTOP -->
            <div class="hidden md:block">
                @include('components.uji-header', [
                    'title'    => 'Manajemen Verifikasi Uji',
                    'subtitle' => 'Penerbitan surat hasil verifikasi untuk laboratorium'
                ])
            </div>

            <!-- MAIN CONTENT -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-6 lg:p-8 space-y-6">

                <!-- ── STAT CARDS ── -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    {{-- Card 1: Surat Terkirim --}}
                    <div class="relative rounded-2xl overflow-hidden group cursor-default" style="background:#0f172a;">
                        {{-- decorative ring --}}
                        <div class="absolute -top-6 -right-6 w-28 h-28 rounded-full opacity-10 group-hover:opacity-20 transition-opacity duration-500" style="background:#94a3b8;"></div>
                        <div class="absolute bottom-0 left-0 h-1 w-full" style="background:linear-gradient(90deg,#475569,#1e293b);"></div>
                        <div class="relative z-10 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-[9px] font-bold tracking-[0.15em] uppercase" style="color:#94a3b8;">Surat Terkirim</span>
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:rgba(148,163,184,0.12);">
                                    <i class="fas fa-paper-plane text-xs" style="color:#94a3b8;"></i>
                                </div>
                            </div>
                            <div class="text-5xl font-black tracking-tight" style="color:#f1f5f9; font-family:'Georgia',serif; line-height:1;">{{ $waitingUser }}</div>
                            <p class="mt-3 text-[11px]" style="color:#475569;">Menunggu tindak lanjut lab</p>
                        </div>
                    </div>

                    {{-- Card 2: Konfirmasi Masuk (highlighted) --}}
                    <div class="relative rounded-2xl overflow-hidden group cursor-default" style="background:#134e4a;">
                        <div class="absolute -top-6 -right-6 w-28 h-28 rounded-full opacity-15 group-hover:opacity-25 transition-opacity duration-500" style="background:#5eead4;"></div>
                        {{-- animated top bar --}}
                        <div class="absolute top-0 left-0 h-0.5 w-full overflow-hidden">
                            <div class="h-full animate-pulse" style="background:linear-gradient(90deg,#14b8a6,#34d399,#14b8a6); background-size:200%;"></div>
                        </div>
                        <div class="absolute bottom-0 left-0 h-1 w-full" style="background:linear-gradient(90deg,#0d9488,#134e4a);"></div>
                        <div class="relative z-10 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <span class="flex items-center gap-1.5 text-[9px] font-bold tracking-[0.15em] uppercase" style="color:#5eead4;">
                                    <span class="w-1.5 h-1.5 rounded-full bg-teal-400 animate-pulse inline-block"></span>
                                    Konfirmasi Masuk
                                </span>
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:rgba(94,234,212,0.15);">
                                    <i class="fas fa-bell text-xs" style="color:#5eead4;"></i>
                                </div>
                            </div>
                            <div class="text-5xl font-black tracking-tight" style="color:#ccfbf1; font-family:'Georgia',serif; line-height:1;">{{ $needReview }}</div>
                            <p class="mt-3 text-[11px]" style="color:#0d9488;">Perlu validasi admin segera</p>
                        </div>
                    </div>

                    {{-- Card 3: Selesai --}}
                    <div class="relative rounded-2xl overflow-hidden group cursor-default" style="background:#052e16;">
                        <div class="absolute -top-6 -right-6 w-28 h-28 rounded-full opacity-10 group-hover:opacity-20 transition-opacity duration-500" style="background:#34d399;"></div>
                        <div class="absolute bottom-0 left-0 h-1 w-full" style="background:linear-gradient(90deg,#059669,#052e16);"></div>
                        <div class="relative z-10 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-[9px] font-bold tracking-[0.15em] uppercase" style="color:#6ee7b7;">Verifikasi Selesai</span>
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:rgba(110,231,183,0.12);">
                                    <i class="fas fa-check-double text-xs" style="color:#6ee7b7;"></i>
                                </div>
                            </div>
                            <div class="text-5xl font-black tracking-tight" style="color:#d1fae5; font-family:'Georgia',serif; line-height:1;">{{ $completed }}</div>
                            <p class="mt-3 text-[11px]" style="color:#065f46;">Arsip yang telah disetujui</p>
                        </div>
                    </div>
                </div>

                <!-- ── TABLE CARD ── -->
                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden" style="box-shadow:0 4px 24px -4px rgba(0,0,0,0.07);">

                    {{-- Table header bar --}}
                    <div class="px-6 py-4 border-b border-slate-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-3"
                         style="background:linear-gradient(135deg,#f8fafc 0%,#f0fdfa 100%);">
                        <div>
                            <div class="flex items-center gap-2 mb-0.5">
                                <div class="w-1 h-5 rounded-full" style="background:#0d9488;"></div>
                                <h3 class="font-bold text-slate-800 text-base tracking-tight">Daftar Dokumen Verifikasi</h3>
                            </div>
                            <p class="text-[11px] text-slate-400 ml-3">Riwayat penerbitan & konfirmasi surat verifikasi lembaga uji</p>
                        </div>
                        <button onclick="openCreateModal()"
                            class="flex items-center gap-2 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition-all active:scale-95 shrink-0"
                            style="background:#0d9488; box-shadow:0 4px 14px -2px rgba(13,148,136,0.45);"
                            onmouseover="this.style.background='#0f766e'" onmouseout="this.style.background='#0d9488'">
                            <i class="fas fa-plus-circle"></i>
                            Terbitkan Verifikasi Baru
                        </button>
                    </div>

                    {{-- Table --}}
                    <div class="overflow-x-auto no-scrollbar">
                        <table class="w-full text-sm text-left min-w-[900px]">
                            <thead>
                                <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                                    <th class="px-5 py-3.5 text-[10px] font-bold tracking-[0.1em] uppercase text-slate-400 w-32">Tgl Terbit</th>
                                    <th class="px-5 py-3.5 text-[10px] font-bold tracking-[0.1em] uppercase text-slate-400">Laboratorium</th>
                                    <th class="px-5 py-3.5 text-[10px] font-bold tracking-[0.1em] uppercase text-slate-400">Judul Dokumen</th>
                                    <th class="px-5 py-3.5 text-[10px] font-bold tracking-[0.1em] uppercase text-slate-400 text-center">Status</th>
                                    <th class="px-5 py-3.5 text-[10px] font-bold tracking-[0.1em] uppercase text-slate-400 text-center">File</th>
                                    <th class="px-5 py-3.5 text-[10px] font-bold tracking-[0.1em] uppercase text-slate-400 text-center w-28">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr class="border-b border-slate-50 transition-all duration-150 hover:bg-teal-50/40 group">

                                    {{-- Tanggal --}}
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-bold text-slate-700">{{ $item->created_at->format('d M Y') }}</span>
                                            <span class="text-[10px] text-slate-400 mt-0.5 font-mono">{{ $item->created_at->format('H:i') }} WIB</span>
                                        </div>
                                    </td>

                                    {{-- Laboratorium --}}
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-black shrink-0 border"
                                                 style="background:#ccfbf1; color:#0f766e; border-color:#99f6e4;">
                                                {{ substr($item->user->name ?? '?', 0, 1) }}
                                            </div>
                                            <div class="min-w-0">
                                                <div class="text-xs font-bold text-slate-800 truncate leading-tight">{{ $item->user->name ?? 'Unknown' }}</div>
                                                <div class="text-[10px] font-mono mt-0.5 px-1.5 py-0.5 rounded inline-block" style="color:#0d9488; background:#f0fdfa;">{{ $item->user->kode_instansi ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Judul --}}
                                    <td class="px-5 py-4 max-w-[220px]">
                                        <span class="text-xs font-semibold text-slate-700 line-clamp-1 leading-tight" title="{{ $item->title }}">{{ $item->title }}</span>
                                        @if($item->admin_note)
                                            <p class="text-[9px] text-slate-400 italic mt-1 flex items-center gap-1">
                                                <i class="fas fa-quote-left text-[7px] opacity-50"></i>
                                                {{ Str::limit($item->admin_note, 40) }}
                                            </p>
                                        @endif
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-5 py-4 text-center">
                                        @if($item->status == 'approved')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold" style="background:#d1fae5; color:#065f46;">
                                                <span class="w-1.5 h-1.5 rounded-full inline-block" style="background:#10b981;"></span>
                                                Selesai
                                            </span>
                                        @elseif($item->status == 'rejected')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold" style="background:#ffe4e6; color:#9f1239;">
                                                <span class="w-1.5 h-1.5 rounded-full inline-block" style="background:#f43f5e;"></span>
                                                Revisi
                                            </span>
                                        @elseif($item->user_note)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold animate-pulse" style="background:#ccfbf1; color:#134e4a;">
                                                <span class="w-1.5 h-1.5 rounded-full inline-block" style="background:#14b8a6;"></span>
                                                Konfirmasi
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold" style="background:#f1f5f9; color:#64748b;">
                                                <span class="w-1.5 h-1.5 rounded-full inline-block" style="background:#94a3b8;"></span>
                                                Menunggu
                                            </span>
                                        @endif
                                    </td>

                                    {{-- File --}}
                                    <td class="px-5 py-4 text-center">
                                        <a href="{{ asset('storage/' . $item->admin_file) }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-bold transition-all border"
                                           style="background:#fef2f2; color:#dc2626; border-color:#fecaca;"
                                           onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'">
                                            <i class="fas fa-file-pdf text-[10px]"></i> PDF
                                        </a>
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="px-5 py-4 text-center">
                                        @if($item->user_note && $item->status == 'pending')
                                            <button onclick="openVerifyModal('{{ $item->id }}', '{{ $item->title }}')"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-bold text-white transition-all active:scale-95"
                                                    style="background:#0d9488; box-shadow:0 2px 8px -1px rgba(13,148,136,0.4);"
                                                    onmouseover="this.style.background='#0f766e'" onmouseout="this.style.background='#0d9488'">
                                                <i class="fas fa-shield-check text-[10px]"></i> Verifikasi
                                            </button>
                                        @elseif($item->status == 'approved')
                                            <div class="w-8 h-8 rounded-lg inline-flex items-center justify-center mx-auto" style="background:#d1fae5;">
                                                <i class="fas fa-check-double text-xs" style="color:#059669;"></i>
                                            </div>
                                        @else
                                            <span class="text-slate-200 text-lg font-light">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="py-20 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center" style="background:#f0fdfa;">
                                                <i class="fas fa-inbox text-2xl" style="color:#99f6e4;"></i>
                                            </div>
                                            <p class="text-sm font-semibold text-slate-400">Belum ada dokumen verifikasi</p>
                                            <p class="text-xs text-slate-300">Terbitkan verifikasi pertama untuk Lab Uji</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Table footer --}}
                    <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-between" style="background:#fafafa;">
                        <span class="text-[10px] text-slate-400">
                            Total <span class="font-bold text-slate-600">{{ $data->count() }}</span> dokumen
                        </span>
                    </div>
                </div>
                <div class="mt-8 text-center text-xs text-slate-400">
                    &copy; 2026 Sistem Informasi Jaminan Mutu Ketenaganukliran
                </div>
            </main>
        </div>
    </div>

    <!-- ============================================================
         2. MODAL CREATE VERIFIKASI (dengan Custom Dropdown)
    ============================================================ -->
    {{-- MODIFIKASI: Ditambahkan pengecekan @if($errors->any()) agar modal otomatis terbuka kembali jika validasi gagal --}}
    <div id="createModal" class="fixed inset-0 z-50 @if($errors->any()) block @else hidden @endif" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeCreateModal()"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100">
                    
                    <!-- Modal Header -->
                    <div class="bg-gradient-to-r from-teal-600 to-emerald-600 px-6 py-4 flex justify-between items-center text-white font-bold">
                        <h3 class="flex items-center gap-2"><i class="fas fa-plus-circle"></i> Terbitkan Verifikasi</h3>
                        <button onclick="closeCreateModal()" class="text-teal-100 hover:text-white bg-white/10 p-2 rounded-lg transition-colors"><i class="fas fa-times"></i></button>
                    </div>

                    <form action="{{ route('uji.verifikasi_admin.store') }}" method="POST" enctype="multipart/form-data" id="createVerifikasiForm">
                        @csrf
                        <input type="hidden" name="category" value="uji">

                        {{-- Hidden input yang dikirim ke server --}}
                        <input type="hidden" name="user_id" id="cs_hidden_user_id" value="{{ old('user_id') }}" required>

                        {{-- MODIFIKASI: Banner Alert Error Global Laravel --}}
                        @if ($errors->any())
                            <div class="mx-6 mt-4 p-4 bg-rose-50 border border-rose-200 rounded-xl flex items-start gap-3 text-rose-600 text-xs">
                                <i class="fas fa-exclamation-circle mt-0.5 shrink-0 text-sm"></i>
                                <div>
                                    <span class="font-bold block mb-1">Penerbitan Gagal:</span>
                                    <ul class="list-disc list-inside space-y-0.5 font-medium">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                        <div class="px-6 py-6 space-y-5">

                            <!-- CUSTOM DROPDOWN: Laboratorium / Lembaga Tujuan -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Laboratorium / Lembaga Tujuan
                                </label>

                                <div class="cs-wrap" id="csWrap">
                                    <!-- Trigger -->
                                    <div class="cs-trigger @error('user_id') border-rose-300 bg-rose-50/20 @enderror" id="csTrigger" onclick="csToggle()">
                                        <div class="cs-trigger-left">
                                            <div class="cs-avatar" id="csAvatar">
                                                <i class="fas fa-flask text-xs"></i>
                                            </div>
                                            <div>
                                                <div class="cs-trigger-text placeholder" id="csTriggerText">-- Pilih Laboratorium --</div>
                                                <div class="cs-trigger-kode" id="csTriggerKode"></div>
                                            </div>
                                        </div>
                                        <i class="fas fa-chevron-down cs-chevron text-xs"></i>
                                    </div>

                                    <!-- Dropdown Panel -->
                                    <div class="cs-panel" id="csPanel">
                                        <!-- Search -->
                                        <div class="cs-search">
                                            <i class="fas fa-search"></i>
                                            <input
                                                type="text"
                                                id="csSearchInput"
                                                placeholder="Cari nama / kode laboratorium..."
                                                oninput="csFilter()"
                                                autocomplete="off"
                                            >
                                        </div>
                                        <!-- Options list -->
                                        <div class="cs-options" id="csOptionsList"></div>
                                    </div>
                                </div>
                                @error('user_id')
                                    <p class="text-rose-600 text-[11px] font-semibold mt-1"><i class="fas fa-info-circle"></i> Lembaga tujuan wajib dipilih.</p>
                                @enderror
                            </div>
                            <!-- END CUSTOM DROPDOWN -->

                            <!-- Judul Dokumen -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 uppercase">Judul Dokumen</label>
                                <input
                                    type="text"
                                    name="title"
                                    class="block w-full rounded-xl @error('title') border-rose-300 bg-rose-50/20 @else border-slate-300 @enderror bg-white p-3 text-sm focus:border-teal-500 focus:ring-teal-500"
                                    placeholder="Contoh: Surat Hasil Verifikasi Akreditasi..."
                                    value="{{ old('title') }}"
                                    required
                                >
                                @error('title')
                                    <p class="text-rose-600 text-[11px] font-semibold mt-1"><i class="fas fa-info-circle"></i> {{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Upload File -->
                            {{-- MODIFIKASI: Border akan otomatis berwarna merah jika ada error dari Laravel --}}
                            <div class="space-y-2 bg-slate-50 p-4 rounded-xl border @error('admin_file') border-rose-300 bg-rose-50/30 @else border-slate-200 @enderror">
                                <label class="block text-xs font-bold text-slate-700 uppercase flex justify-between">
                                    <span>Upload Surat (PDF)</span>
                                    <span class="text-[10px] bg-red-100 text-red-600 px-1.5 py-0.5 rounded font-bold">Maks 2MB</span>
                                </label>
                                <input
                                    type="file"
                                    name="admin_file"
                                    id="admin_file_input"
                                    onchange="validateAdminFile(this)"
                                    class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-teal-600 file:text-white hover:file:bg-teal-700 border border-slate-300 rounded-lg cursor-pointer bg-white"
                                    accept=".pdf"
                                    required
                                >
                                
                                {{-- Penanganan Error Backend (Laravel) --}}
                                @error('admin_file')
                                    <p class="text-rose-600 text-[11px] font-semibold mt-1.5 flex items-center gap-1">
                                        <i class="fas fa-info-circle"></i> {{ $message }}
                                    </p>
                                @enderror

                                {{-- Penanganan Error Frontend Instan (JS) --}}
                                <p id="js_file_error" class="hidden text-rose-600 text-[11px] font-semibold mt-1.5 flex items-center gap-1">
                                    <i class="fas fa-info-circle"></i> <span id="js_file_error_text"></span>
                                </p>
                            </div>

                            <!-- Instruksi Tambahan -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 uppercase">Instruksi Tambahan</label>
                                <textarea
                                    name="admin_note"
                                    rows="2"
                                    class="block w-full rounded-xl border-slate-300 bg-white p-3 text-sm focus:border-teal-500 focus:ring-teal-500"
                                    placeholder="Pesan instruksi untuk laboratorium..."
                                >{{ old('admin_note') }}</textarea>
                            </div>
                        </div>

                        <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-slate-100">
                            <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-teal-200 transition-all">Terbitkan</button>
                            <button type="button" onclick="closeCreateModal()" class="bg-white border border-slate-300 text-slate-700 px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-50">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. MODAL VALIDASI KONFIRMASI -->
    <div id="verifyModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeVerifyModal()"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-100">
                    <form id="verifyForm" method="POST" action="">
                        @csrf
                        <div class="bg-white px-6 py-5 border-b border-slate-100 flex items-center gap-3">
                            <div class="bg-teal-100 p-2 rounded-full text-teal-600"><i class="fas fa-check-circle text-lg"></i></div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 text-base">Validasi Konfirmasi Lab</h3>
                                <p class="text-xs text-slate-500 line-clamp-1" id="verifyTitle">Title...</p>
                            </div>
                        </div>
                        <div class="px-6 py-6 space-y-4">
                            <p class="text-sm text-slate-600">Verifikasi apakah laboratorium telah menyelesaikan instruksi pada surat hasil verifikasi ini?</p>
                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-slate-700 uppercase">Catatan Admin</label>
                                <textarea name="admin_note" rows="2" class="block w-full rounded-lg border-slate-300 text-xs p-3 bg-slate-50 focus:border-teal-500 focus:ring-teal-500" placeholder="Tulis catatan penutupan atau alasan revisi..."></textarea>
                            </div>
                            <div class="flex gap-3">
                                <button type="submit" onclick="setVerifyAction('approve')" class="flex-1 bg-emerald-600 text-white py-2.5 rounded-xl font-bold hover:bg-emerald-700 shadow-md transition-all active:scale-95">Setujui & Selesai</button>
                                <button type="submit" onclick="setVerifyAction('reject')" class="flex-1 bg-white border border-rose-200 text-rose-600 py-2.5 rounded-xl font-bold hover:bg-rose-50 shadow-sm transition-all active:scale-95">Minta Revisi</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // =============================================
        // SIDEBAR
        // =============================================
        function toggleSidebar() {
            document.getElementById('mobileSidebar').classList.toggle('hidden');
        }

        // =============================================
        // MODAL CREATE
        // =============================================
        function openCreateModal() {
            document.getElementById('createModal').classList.remove('hidden');
            document.getElementById('createModal').classList.add('block');
            csReset();
        }
        function closeCreateModal() {
            document.getElementById('createModal').classList.add('hidden');
            document.getElementById('createModal').classList.remove('block');
            csClose();
        }

        // =============================================
        // JS VALIDASI UKURAN FILE (PRE-UPLOAD CHECK)
        // =============================================
        function validateAdminFile(input) {
            const file = input.files[0];
            const errorElement = document.getElementById('js_file_error');
            const errorText = document.getElementById('js_file_error_text');
            
            if (file) {
                const limitInBytes = 2 * 1024 * 1024; // 2MB
                if (file.size > limitInBytes) {
                    const sizeInMb = (file.size / (1024 * 1024)).toFixed(2);
                    errorText.innerText = "Ukuran file " + sizeInMb + "MB melebihi batas maksimal 2.00MB.";
                    errorElement.classList.remove('hidden');
                    input.value = ""; // Reset form input file
                } else {
                    errorElement.classList.add('hidden');
                }
            }
        }

        // =============================================
        // CUSTOM DROPDOWN LOGIC
        // =============================================

        // Data laboratorium dari Laravel (di-inject sebagai JS array)
        @php
            $csLabJson = $users->map(function($u) {
                return [
                    'id'   => $u->id,
                    'name' => $u->name,
                    'kode' => $u->kode_instansi ?? '-',
                ];
            })->values()->toJson();
        @endphp
        const csLabData = {!! $csLabJson !!};

        let csSelectedId   = null;
        let csDropdownOpen = false;

        function csGetInitial(name) {
            return name ? name.trim().charAt(0).toUpperCase() : '?';
        }

        function csRenderOptions(filter) {
            const list    = document.getElementById('csOptionsList');
            const keyword = (filter || '').toLowerCase();
            const filtered = csLabData.filter(l =>
                l.name.toLowerCase().includes(keyword) ||
                l.kode.toLowerCase().includes(keyword)
            );

            if (!filtered.length) {
                list.innerHTML = '<div class="cs-empty"><i class="fas fa-search-minus mr-1 opacity-50"></i> Laboratorium tidak ditemukan</div>';
                return;
            }

            list.innerHTML = filtered.map(l => `
                <div class="cs-option ${csSelectedId == l.id ? 'selected' : ''}" onclick="csSelect(${l.id})">
                    <div class="cs-opt-avatar">${csGetInitial(l.name)}</div>
                    <div style="min-width:0; flex:1;">
                        <div class="cs-opt-name">${l.name}</div>
                        <div class="cs-opt-kode">${l.kode}</div>
                    </div>
                    <i class="fas fa-check-circle cs-opt-check"></i>
                </div>
            `).join('');
        }

        function csToggle() {
            csDropdownOpen ? csClose() : csOpen();
        }

        function csOpen() {
            csDropdownOpen = true;
            document.getElementById('csTrigger').classList.add('open');
            document.getElementById('csPanel').classList.add('open');
            csRenderOptions('');
            setTimeout(() => document.getElementById('csSearchInput').focus(), 150);
        }

        function csClose() {
            csDropdownOpen = false;
            document.getElementById('csTrigger').classList.remove('open');
            document.getElementById('csPanel').classList.remove('open');
            document.getElementById('csSearchInput').value = '';
        }

        function csFilter() {
            csRenderOptions(document.getElementById('csSearchInput').value);
        }

        function csSelect(id) {
            const lab = csLabData.find(l => l.id == id);
            if (!lab) return;

            csSelectedId = id;

            // Update hidden input
            document.getElementById('cs_hidden_user_id').value = id;

            // Update trigger display
            const avatar    = document.getElementById('csAvatar');
            const trigText  = document.getElementById('csTriggerText');
            const trigKode  = document.getElementById('csTriggerKode');

            avatar.innerHTML = csGetInitial(lab.name);
            avatar.classList.add('filled');

            trigText.textContent = lab.name;
            trigText.classList.remove('placeholder');

            trigKode.textContent = lab.kode;

            csClose();
        }

        function csReset() {
            csSelectedId = null;
            document.getElementById('cs_hidden_user_id').value = '';

            const avatar   = document.getElementById('csAvatar');
            const trigText = document.getElementById('csTriggerText');
            const trigKode = document.getElementById('csTriggerKode');

            avatar.innerHTML = '<i class="fas fa-flask text-xs"></i>';
            avatar.classList.remove('filled');

            trigText.textContent = '-- Pilih Laboratorium --';
            trigText.classList.add('placeholder');

            trigKode.textContent = '';
            csClose();
        }

        // Auto-select laboratorium jika session old() terisi setelah validasi gagal
        document.addEventListener('DOMContentLoaded', function() {
            const oldUserId = "{{ old('user_id') }}";
            if(oldUserId) {
                csSelect(oldUserId);
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (csDropdownOpen && !e.target.closest('#csWrap')) {
                csClose();
            }
        });

        // =============================================
        // MODAL VERIFY
        // =============================================
        let currentVerifyId = null;
        function openVerifyModal(id, title) {
            currentVerifyId = id;
            document.getElementById('verifyTitle').innerText = title;
            document.getElementById('verifyModal').classList.remove('hidden');
        }
        function closeVerifyModal() {
            document.getElementById('verifyModal').classList.add('hidden');
        }
        function setVerifyAction(action) {
            const form = document.getElementById('verifyForm');
            form.action = (action === 'approve')
                ? "{{ url('/submission/approve') }}/" + currentVerifyId
                : "{{ url('/submission/reject') }}/" + currentVerifyId;
        }

        // =============================================
        // MODAL HISTORY
        // =============================================
        function openHistoryModal(files, currentStatus, docTitle) {
            const container = document.getElementById('timelineContainer');
            container.innerHTML = '';
            
            if (!files || files.length === 0) {
                container.innerHTML = `<div class="py-10 text-center text-slate-400 text-sm">Belum ada riwayat tercatat.</div>`;
            } else {
                files.sort((a, b) => a.version - b.version);
                
                let cleanFiles = [];
                files.forEach((file) => {
                    if (cleanFiles.length > 0) {
                        let lastClean = cleanFiles[cleanFiles.length - 1];
                        if (file.file_path === lastClean.file_path && file.file_name === lastClean.file_name) {
                            if (file.admin_note) lastClean.admin_note = file.admin_note;
                            if (file.admin_file) lastClean.admin_file = file.admin_file;
                            return;
                        }
                    }
                    cleanFiles.push({...file}); 
                });

                cleanFiles.forEach((file, index) => {
                    const d = new Date(file.created_at);
                    const dateStr = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
                    let isLatest = (index === cleanFiles.length - 1);
                    let isStart  = (file.version == 0);
                    let versionLabel, colorClass, actionTitle, userFileHTML, adminFeedbackHTML = '';

                    if (isStart) {
                        versionLabel = '<i class="fas fa-flag"></i>';
                        colorClass   = 'bg-teal-100 text-teal-600 ring-4 ring-teal-50';
                        actionTitle  = "Penerbitan Surat Hasil Verifikasi (Admin)";
                        userFileHTML = file.admin_file ? `
                            <div class="mt-2 bg-teal-50 rounded-lg p-3 border border-teal-100 flex items-start gap-3">
                                <div class="bg-white p-2 rounded-md shadow-sm text-red-500"><i class="fas fa-file-pdf text-lg"></i></div>
                                <div>
                                    <p class="text-xs font-bold text-slate-700 mb-0.5">Dokumen Surat</p>
                                    <a href="/storage/${file.admin_file}" target="_blank" class="text-[11px] text-teal-600 hover:underline font-medium font-mono">Lihat Dokumen</a>
                                </div>
                            </div>
                        ` : '';
                    } else {
                        versionLabel = `v${file.version}`;
                        colorClass   = isLatest ? 'bg-teal-600 text-white ring-4 ring-teal-100 shadow-md' : 'bg-white border-2 border-slate-200 text-slate-500';
                        actionTitle  = "Konfirmasi Tindak Lanjut Lab";
                        userFileHTML = `
                            <div class="mt-2 flex flex-col gap-2">
                                <div class="bg-slate-50 rounded-lg p-3 border border-slate-200">
                                    <p class="text-[11px] font-medium text-slate-600 italic">"${file.user_note || 'Konfirmasi tindak lanjut telah diselesaikan oleh laboratorium.'}"</p>
                                </div>
                            </div>
                        `;
                        
                        let badgeHTML = '';
                        if (isLatest) {
                            if (currentStatus === 'approved') badgeHTML = `<span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded text-[10px] font-bold tracking-wider border border-emerald-200">DISETUJUI (FINAL)</span>`;
                            else if (currentStatus === 'rejected') badgeHTML = `<span class="bg-rose-100 text-rose-700 px-2 py-0.5 rounded text-[10px] font-bold tracking-wider border border-rose-200">REVISI</span>`;
                        }

                        if (badgeHTML || file.admin_note) {
                            adminFeedbackHTML = `
                                <div class="mt-4 pt-3 border-t border-slate-100 relative">
                                    <div class="absolute -top-2 left-4 bg-slate-50 px-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Evaluasi Admin</div>
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-xs font-bold text-slate-700">Status</span>
                                        ${badgeHTML}
                                    </div>
                                    ${file.admin_note ? `<div class="bg-yellow-50 border border-yellow-100 rounded-lg p-3 text-xs text-slate-700"><i class="fas fa-comment-alt text-yellow-500 mr-1.5"></i> "${file.admin_note}"</div>` : ''}
                                </div>
                            `;
                        }
                    }

                    container.innerHTML += `
                        <div class="relative flex gap-6 pb-8 last:pb-0">
                            <div class="absolute top-0 left-4 -bottom-8 w-0.5 bg-slate-200 last:hidden"></div>
                            <div class="relative z-10 flex-shrink-0 w-8 h-8 rounded-full ${colorClass} flex items-center justify-center border-2 border-white shadow-sm">
                                <span class="text-[10px] font-bold">${versionLabel}</span>
                            </div>
                            <div class="flex-1 bg-white rounded-xl p-4 border border-slate-200 shadow-sm relative transition-all">
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-xs font-bold text-slate-700">${actionTitle}</span> 
                                    <span class="text-[10px] text-slate-400 bg-slate-50 px-2 py-1 rounded-full border border-slate-100 font-mono">${dateStr}</span>
                                </div>
                                ${userFileHTML}
                                ${adminFeedbackHTML}
                            </div>
                        </div>
                    `;
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