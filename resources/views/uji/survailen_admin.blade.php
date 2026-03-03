<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Survailen Uji | SI-LAB ADMIN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Choices.js CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    },
                    animation: {
                        'pop-in': 'popIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards',
                        'toast-in': 'toastIn 0.4s ease-out forwards',
                    },
                    keyframes: {
                        popIn: {
                            '0%': {
                                opacity: '0',
                                transform: 'scale(0.8) translateY(20px)'
                            },
                            '100%': {
                                opacity: '1',
                                transform: 'scale(1) translateY(0)'
                            },
                        },
                        toastIn: {
                            '0%': {
                                opacity: '0',
                                transform: 'translateY(-20px)'
                            },
                            '100%': {
                                opacity: '1',
                                transform: 'translateY(0)'
                            },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .table-row-hover:hover td {
            background-color: #f0fdfa;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .modal-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .modal-scroll::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .modal-scroll::-webkit-scrollbar-thumb {
            background: #5eead4;
            border-radius: 10px;
        }

        .modal-scroll::-webkit-scrollbar-thumb:hover {
            background: #2dd4bf;
        }

        /* Custom Choices.js Styling */
        .choices__inner {
            background-color: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.75rem !important;
            min-height: 48px !important;
            display: flex;
            align-items: center;
        }

        .is-focused .choices__inner {
            border-color: #0d9488 !important;
            box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.1) !important;
        }

        .choices__list--dropdown {
            border-radius: 1rem !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1) !important;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 antialiased">

    @php
        use App\Models\Submission;
        use App\Models\User;

        if (!isset($audits)) {
            $audits = Submission::with(['user', 'files'])
                ->where('category', 'uji')
                ->where('type', 'Survailen')
                ->orderBy('created_at', 'desc')
                ->get();
        }
        $users = User::where('role', 'user')->where('category', 'uji')->get();

        $waitingUser = $audits->whereNull('file_path')->count();
        $needReview = $audits->whereNotNull('file_path')->where('status', 'pending')->count();
        $completed = $audits->where('status', 'approved')->count();
    @endphp

    <!-- Container untuk Toast Validasi (Floating) -->
    <div id="toast-container" class="fixed top-5 right-5 z-[110] flex flex-col gap-3 pointer-events-none"></div>

    <!-- === POP-UP NOTIFIKASI MODAL === -->

    <!-- 1. Success Modal -->
    @if (session('success'))
        <div id="successModal"
            class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 backdrop-blur-[3px] transition-all duration-300">
            <div
                class="bg-white rounded-2xl shadow-2xl p-8 max-w-sm w-full mx-4 text-center animate-pop-in relative overflow-hidden border border-white/50">
                <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-teal-400 to-emerald-500"></div>
                <div
                    class="w-20 h-20 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow-inner">
                    <i class="fas fa-check text-4xl text-teal-600 drop-shadow-sm"></i>
                </div>
                <h3 class="text-2xl font-bold text-slate-800 mb-2">Berhasil!</h3>
                <p class="text-slate-600 mb-6 text-sm leading-relaxed font-medium">{{ session('success') }}</p>
                <div class="w-full bg-slate-100 h-1.5 rounded-full mb-5 overflow-hidden">
                    <div id="progressBar" class="h-full bg-teal-500 rounded-full" style="width: 100%"></div>
                </div>
                <button onclick="closeNotification('successModal')"
                    class="w-full bg-white border-2 border-slate-100 hover:border-teal-400 hover:bg-teal-50 text-slate-500 hover:text-teal-700 font-bold py-3 rounded-xl transition-all duration-300 group">
                    Tutup Sekarang <i class="fas fa-times group-hover:rotate-90 transition-transform text-xs ml-1"></i>
                </button>
            </div>
        </div>
    @endif

    <!-- 2. Error Modal -->
    @if ($errors->any() || session('error'))
        <div id="errorModal"
            class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 backdrop-blur-[3px] transition-all duration-300">
            <div
                class="bg-white rounded-2xl shadow-2xl p-8 max-w-sm w-full mx-4 text-center animate-pop-in relative overflow-hidden border border-white/50">
                <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-rose-500 to-red-600"></div>
                <div
                    class="w-20 h-20 bg-rose-100 rounded-full flex items-center justify-center mx-auto mb-5 shadow-inner">
                    <i class="fas fa-exclamation-triangle text-4xl text-rose-600"></i>
                </div>
                <h3 class="text-2xl font-bold text-slate-800 mb-2">Gagal!</h3>
                <div class="text-slate-600 mb-8 text-sm leading-relaxed font-medium">
                    @if (session('error'))
                        <p>{{ session('error') }}</p>
                    @else
                        <ul class="list-none space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <button onclick="closeNotification('errorModal')"
                    class="w-full bg-slate-800 hover:bg-slate-900 text-white font-bold py-3.5 rounded-xl transition-all shadow-lg transform active:scale-95">Coba
                    Lagi</button>
            </div>
        </div>
    @endif

    <div class="flex h-screen overflow-hidden">
        <div class="hidden md:flex h-full bg-teal-900">
            @include('components.uji-sidebar')
        </div>

        <div id="mobileSidebar" class="fixed inset-0 z-50 hidden">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="toggleSidebar()">
            </div>
            <div
                class="absolute left-0 top-0 bottom-0 w-64 bg-teal-900 shadow-xl transform transition-transform duration-300">
                @include('components.uji-sidebar')
            </div>
        </div>

        <div class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50 relative w-full">
            <!-- Header Mobile -->
            <div
                class="md:hidden bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between z-20 sticky top-0 shadow-sm">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()"
                        class="p-2 text-slate-500 hover:text-teal-600 hover:bg-slate-100 rounded-lg transition-colors">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div class="flex items-center gap-2">
                        <div
                            class="w-8 h-8 rounded-lg bg-teal-600 flex items-center justify-center text-white shadow-sm">
                            <i class="fas fa-flask text-sm"></i></div>
                        <span class="font-bold text-slate-800 text-sm tracking-wide">SI-LAB UJI</span>
                    </div>
                </div>
                <div
                    class="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center text-teal-600 text-xs font-bold border border-teal-200">
                    {{ substr(Auth::user()->name ?? 'L', 0, 1) }}
                </div>
            </div>

            <div class="hidden md:block">
                @include('components.uji-header', [
                    'title' => 'Manajemen Audit & Survailen',
                    'subtitle' => 'Kirim dan pantau tindak lanjut audit lembaga',
                ])
            </div>

            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-6 lg:p-8 space-y-6">
                <!-- Statistik Widgets -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
                    <div
                        class="relative bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:-translate-y-1 transition-all group overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20"><i
                                class="fas fa-paper-plane text-6xl text-slate-500"></i></div>
                        <div class="relative z-10">
                            <span
                                class="bg-slate-100 text-slate-600 px-2 py-1 rounded-md text-[10px] font-bold uppercase">Terkirim</span>
                            <h2 class="text-4xl font-bold text-slate-800 mt-2">{{ $waitingUser }}</h2>
                            <p class="text-xs text-slate-400 mt-2">Menunggu respon lembaga</p>
                        </div>
                    </div>
                    <div
                        class="relative bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:-translate-y-1 transition-all group overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20"><i
                                class="fas fa-microscope text-6xl text-teal-500"></i></div>
                        <div class="relative z-10">
                            <span
                                class="bg-teal-100 text-teal-600 px-2 py-1 rounded-md text-[10px] font-bold uppercase animate-pulse">Perlu
                                Review</span>
                            <h2 class="text-4xl font-bold text-slate-800 mt-2">{{ $needReview }}</h2>
                            <p class="text-xs text-slate-400 mt-2">Respon masuk dari Lab</p>
                        </div>
                    </div>
                    <div
                        class="relative bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:-translate-y-1 transition-all group overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20"><i
                                class="fas fa-clipboard-check text-6xl text-emerald-500"></i></div>
                        <div class="relative z-10">
                            <span
                                class="bg-emerald-100 text-emerald-600 px-2 py-1 rounded-md text-[10px] font-bold uppercase">Selesai</span>
                            <h2 class="text-4xl font-bold text-slate-800 mt-2">{{ $completed }}</h2>
                            <p class="text-xs text-slate-400 mt-2">Audit ditutup (Compliant)</p>
                        </div>
                    </div>
                </div>

                <!-- TABLE CARD -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div
                        class="px-6 py-5 border-b border-slate-100 bg-white flex flex-col md:flex-row justify-between items-center gap-4">
                        <div>
                            <h3 class="font-bold text-slate-800 text-lg">Daftar Audit Laboratorium</h3>
                            <p class="text-sm text-slate-500 mt-0.5">Survailen dan inspeksi lembaga uji</p>
                        </div>
                        <button onclick="openCreateModal()"
                            class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-2.5 rounded-xl shadow-lg shadow-teal-200 transition-all flex items-center gap-2 font-bold text-sm transform active:scale-95 border border-teal-700">
                            <i class="fas fa-plus-circle"></i> Buat Surat Audit Baru
                        </button>
                    </div>

                    <div class="overflow-x-auto no-scrollbar">
                        <table class="w-full text-sm text-left text-slate-600 min-w-[800px] md:min-w-0">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-6 py-4 font-bold tracking-wider">Tgl Kirim</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Laboratorium</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Perihal</th>
                                    <th class="px-6 py-4 font-bold tracking-wider text-center">Jejak</th>
                                    <th class="px-6 py-4 font-bold tracking-wider text-center">Status & Tindakan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($audits as $item)
                                    <tr class="table-row-hover transition-colors group">
                                        <td class="px-6 py-5 whitespace-nowrap">
                                            <div class="flex flex-col">
                                                <span
                                                    class="font-bold text-slate-700">{{ $item->created_at->format('d M Y') }}</span>
                                                <span
                                                    class="text-[10px] text-slate-400 mt-0.5">{{ $item->created_at->format('H:i') }}
                                                    WIB</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-8 h-8 rounded-full bg-teal-50 flex items-center justify-center text-teal-600 text-xs font-bold shrink-0 border border-teal-100">
                                                    {{ substr($item->user->name ?? '?', 0, 1) }}
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="font-bold text-slate-800 line-clamp-1 text-sm">
                                                        {{ $item->user->name ?? 'Unknown' }}</div>
                                                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                                        {{ $item->user->kode_instansi ?? 'LAB-???' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <span
                                                class="text-sm font-medium text-slate-800 line-clamp-1">{{ $item->title }}</span>
                                        </td>
                                        <td class="px-6 py-5 text-center">
                                            <button
                                                onclick='openHistoryModal(@json($item->files ?? []), "{{ $item->status }}", "{{ $item->title }}")'
                                                class="text-slate-400 hover:text-teal-600 p-2 rounded-full hover:bg-teal-50 transition-all"><i
                                                    class="fas fa-history text-lg"></i></button>
                                        </td>
                                        <td class="px-6 py-5 text-center whitespace-nowrap">
                                            @if ($item->status == 'approved')
                                                <span
                                                    class="bg-emerald-100 text-emerald-700 px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-wider border border-emerald-200 inline-flex items-center gap-1.5">
                                                    <i class="fas fa-check-circle"></i> Selesai
                                                </span>
                                            @elseif($item->file_path && $item->file_path !== '-' && $item->status == 'pending')
                                                <button
                                                    onclick="openVerifyModal('{{ $item->id }}', '{{ $item->title }}')"
                                                    class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-1.5 rounded-lg text-xs font-bold shadow-md active:scale-95 transition-all inline-flex items-center gap-2">
                                                    <i class="fas fa-tasks"></i> Verifikasi
                                                </button>
                                            @elseif($item->status == 'rejected')
                                                <span
                                                    class="bg-rose-100 text-rose-700 px-3 py-1.5 rounded-full text-[10px] font-bold uppercase border border-rose-200">
                                                    Menunggu Revisi
                                                </span>
                                            @else
                                                <span
                                                    class="bg-slate-100 text-slate-500 px-3 py-1.5 rounded-full text-[10px] font-bold uppercase border border-slate-200">
                                                    Terkirim
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-slate-400">Belum ada
                                            audit laboratorium.</td>
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

    <!-- MODAL CREATE AUDIT -->
    <div id="createModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeCreateModal()">
        </div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg animate-pop-in border border-slate-100">
                    <div
                        class="bg-gradient-to-r from-teal-600 to-emerald-600 px-6 py-4 flex justify-between items-center text-white">
                        <h3 class="text-lg font-bold flex items-center gap-2"><i class="fas fa-paper-plane"></i> Kirim
                            Surat Audit</h3>
                        <button onclick="closeCreateModal()"
                            class="text-teal-100 hover:text-white p-2 rounded-lg bg-white/10"><i
                                class="fas fa-times"></i></button>
                    </div>
                    <form action="{{ route('survailen.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="category" value="uji">
                        <div class="px-6 py-6 space-y-5">
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Tujuan
                                    Lembaga Uji</label>
                                <select name="user_id" id="user-select-choices" required>
                                    <option value="" disabled selected>-- Pilih Lembaga Uji --</option>
                                    @foreach ($users as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }}
                                            ({{ $u->kode_instansi ?? '-' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-1.5">
                                <label
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Perihal</label>
                                <input type="text" name="title"
                                    class="block w-full rounded-xl border-slate-200 bg-slate-50 p-3 text-sm focus:border-teal-500 outline-none transition-all"
                                    placeholder="Judul audit..." required>
                            </div>
                            <div class="space-y-2 bg-slate-50 p-4 rounded-xl border border-slate-200">
                                <label class="block text-xs font-bold text-slate-700 uppercase flex justify-between">
                                    <span>Upload Surat (PDF)</span>
                                    <span
                                        class="text-[10px] bg-red-100 text-red-600 px-1.5 py-0.5 rounded font-bold">10MB
                                        MAX</span>
                                </label>
                                <input type="file" name="admin_file" onchange="validateFileSize(this)"
                                    class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-white file:text-teal-700 border border-slate-200 rounded-lg cursor-pointer bg-white shadow-sm"
                                    accept=".pdf" required>
                            </div>
                        </div>
                        <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-3 border-t">
                            <button type="submit"
                                class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-teal-200 active:scale-95">Kirim</button>
                            <button type="button" onclick="closeCreateModal()"
                                class="bg-white border border-slate-300 text-slate-700 px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-50">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL VERIFIKASI -->
    <div id="verifyModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeVerifyModal()">
        </div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div
                class="bg-white rounded-3xl w-full max-w-md shadow-2xl overflow-hidden animate-pop-in border border-slate-100">
                <div class="bg-teal-600 text-white px-6 py-4 font-bold flex items-center gap-3">
                    <i class="fas fa-microscope text-lg"></i>
                    <h3>Review Dokumen Lab</h3>
                </div>
                <form id="verifyForm" method="POST" action="" enctype="multipart/form-data">
                    @csrf
                    <div class="px-6 py-6 space-y-4">
                        <div class="p-3 bg-teal-50 rounded-xl border border-teal-100">
                            <p class="text-[10px] font-bold text-teal-600 uppercase mb-1 tracking-wider">Perihal Audit:
                            </p>
                            <p class="text-sm text-slate-700 font-bold" id="verifyTitleDisplay"></p>
                        </div>
                        <div class="space-y-3 bg-slate-50 p-4 rounded-xl border">
                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide">Evaluasi
                                    Admin</label>
                                <textarea name="admin_note" rows="2"
                                    class="block w-full rounded-lg border-slate-300 text-xs p-2.5 bg-white shadow-sm focus:border-teal-500 outline-none"
                                    placeholder="Tulis catatan evaluasi..."></textarea>
                            </div>
                            <div class="space-y-1">
                                <label
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wide flex justify-between">
                                    <span>Fail Lampiran (PDF)</span>
                                    <span class="text-[9px] text-slate-400">10MB MAX</span>
                                </label>
                                <input type="file" name="admin_file" onchange="validateFileSize(this)"
                                    class="block w-full text-xs text-slate-500 border border-slate-200 rounded-lg bg-white shadow-sm file:bg-teal-50 file:border-0 file:py-1 file:px-3 file:text-[10px] file:font-bold file:text-teal-700"
                                    accept=".pdf">
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <button type="submit" onclick="setVerifyAction('approve')"
                                class="flex-1 bg-emerald-600 text-white py-2.5 rounded-xl font-bold hover:bg-emerald-700 shadow-md transform active:scale-95 transition-all">Setujui</button>
                            <button type="submit" onclick="setVerifyAction('reject')"
                                class="flex-1 bg-white border border-rose-200 text-rose-600 py-2.5 rounded-xl font-bold hover:bg-rose-50 transform active:scale-95 transition-all">Revisi</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL HISTORY -->
    <div id="historyModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeHistoryModal()">
        </div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="bg-white rounded-3xl w-full max-w-3xl shadow-2xl overflow-hidden border animate-pop-in">
                <div
                    class="bg-slate-900 text-white px-6 py-5 flex justify-between items-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -mr-10 -mt-10"></div>
                    <h3 class="text-xl font-bold flex items-center gap-3 relative z-10">
                        <div class="bg-white/10 p-2 rounded-lg backdrop-blur-sm"><i
                                class="fas fa-history text-lg"></i></div>
                        Jejak Audit
                    </h3>
                    <button onclick="closeHistoryModal()"
                        class="text-slate-400 hover:text-white transition-colors relative z-10"><i
                            class="fas fa-times text-xl"></i></button>
                </div>
                <div class="max-h-[65vh] overflow-y-auto bg-slate-50 modal-scroll">
                    <div id="timelineContainer" class="px-8 py-10 relative"></div>
                </div>
                <div
                    class="bg-white px-6 py-4 flex justify-end border-t border-slate-100 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.02)]">
                    <button onclick="closeHistoryModal()"
                        class="px-6 py-2.5 bg-slate-100 text-slate-700 rounded-xl text-sm font-bold hover:bg-slate-200 transition-all active:scale-95">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
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

        function showErrorToast(title, message) {
            const container = document.getElementById('toast-container');
            const toastId = 'toast-' + Date.now();
            const html = `
                <div id="${toastId}" class="pointer-events-auto bg-white border-l-4 border-rose-500 shadow-xl rounded-xl animate-toast-in w-72">
                    <div class="p-4 flex items-start gap-3">
                        <div class="bg-rose-100 p-2 rounded-full text-rose-600 shrink-0"><i class="fas fa-exclamation-circle"></i></div>
                        <div class="flex-1"><p class="text-xs font-bold text-slate-800">${title}</p><p class="text-[10px] text-slate-500 mt-0.5 leading-tight">${message}</p></div>
                        <button onclick="document.getElementById('${toastId}').remove()" class="text-slate-400"><i class="fas fa-times text-[10px]"></i></button>
                    </div>
                </div>`;
            container.insertAdjacentHTML('beforeend', html);
            setTimeout(() => {
                const t = document.getElementById(toastId);
                if (t) {
                    t.style.opacity = '0';
                    t.style.transform = 'translateX(20px)';
                    t.style.transition = '0.4s';
                    setTimeout(() => t.remove(), 400);
                }
            }, 5000);
        }

        function validateFileSize(input) {
            if (input.files && input.files[0]) {
                if (input.files[0].size / 1024 / 1024 > 10) {
                    showErrorToast('File Terlalu Besar', 'Maksimal ukuran file adalah 10 MB. Silakan kompres file Anda.');
                    input.value = '';
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const successModal = document.getElementById('successModal');
            if (successModal) {
                const pb = document.getElementById('progressBar');
                setTimeout(() => {
                    pb.style.transition = 'width 4s linear';
                    pb.style.width = '0%';
                }, 100);
                setTimeout(() => {
                    closeNotification('successModal');
                }, 4200);
            }
            const el = document.getElementById('user-select-choices');
            if (el) new Choices(el, {
                searchEnabled: true,
                itemSelectText: '',
                shouldSort: false
            });
        });

        // UI Logic
        function toggleSidebar() {
            document.getElementById('mobileSidebar').classList.toggle('hidden');
        }

        function openCreateModal() {
            document.getElementById('createModal').classList.remove('hidden');
        }

        function closeCreateModal() {
            document.getElementById('createModal').classList.add('hidden');
        }

        let curVerifyId = null;

        function openVerifyModal(id, title) {
            curVerifyId = id;
            document.getElementById('verifyTitleDisplay').innerText = title;
            document.getElementById('verifyModal').classList.remove('hidden');
        }

        function closeVerifyModal() {
            document.getElementById('verifyModal').classList.add('hidden');
        }

        function setVerifyAction(a) {
            const f = document.getElementById('verifyForm');
            f.action = `{{ url('/submission') }}/${a}/${curVerifyId}`;
        }

        function openHistoryModal(files, status, title) {
            const container = document.getElementById('timelineContainer');
            container.innerHTML = '';
            if (!files || files.length === 0) {
                container.innerHTML =
                    `<div class="py-10 text-center text-slate-400 text-sm">Belum ada riwayat jejak audit.</div>`;
            } else {
                files.sort((a, b) => a.version - b.version);
                files.forEach((f, i) => {
                    const date = new Date(f.created_at).toLocaleString('id-ID', {
                        day: 'numeric',
                        month: 'short',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    const isLatest = (i === files.length - 1);
                    const isStart = (f.version == 0);
                    let versionLabel = isStart ? '<i class="fas fa-flag"></i>' : `v${f.version}`;
                    let colorClass = isLatest ? 'bg-teal-600 text-white ring-4 ring-teal-100 shadow-md' :
                        'bg-white border-2 border-slate-200 text-slate-500';
                    let actionTitle = isStart ? "Inisiasi Surat Audit" : "Tanggapan Laboratorium";
                    let itemHTML = `
                        <div class="relative flex gap-6 pb-10 last:pb-0">
                            <div class="absolute top-0 left-4 -bottom-10 w-0.5 bg-slate-200 last:hidden"></div>
                            <div class="relative z-10 flex-shrink-0 w-8 h-8 rounded-full ${colorClass} flex items-center justify-center border-2 border-white shadow-sm">
                                <span class="text-[10px] font-bold">${versionLabel}</span>
                            </div>
                            <div class="flex-1 bg-white rounded-2xl p-5 border border-slate-200 shadow-sm relative hover:shadow-md transition-all duration-300">
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-xs font-black text-slate-700 uppercase tracking-widest flex items-center gap-1.5">${actionTitle}</span> 
                                    <span class="text-[10px] text-slate-400 bg-slate-50 px-2 py-1 rounded-full border border-slate-100 font-mono">${date}</span>
                                </div>
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-teal-50 flex items-center justify-center text-teal-600 flex-shrink-0"><i class="fas ${isStart ? 'fa-file-pdf' : 'fa-file-alt'} text-lg"></i></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-slate-800 truncate">${f.file_name || 'Dokumen Audit'}</p>
                                        <a href="/storage/${isStart ? f.admin_file : f.file_path}" target="_blank" class="text-[11px] text-teal-600 hover:text-teal-800 font-bold inline-flex items-center gap-1 mt-1 hover:underline"><i class="fas fa-external-link-alt"></i> Buka Dokumen</a>
                                    </div>
                                </div>
                                ${f.admin_note ? `<div class="mt-4 p-3 bg-teal-50/50 border border-teal-100 rounded-xl"><p class="text-[10px] font-black text-teal-600 uppercase mb-1 flex items-center gap-1.5"><i class="fas fa-comment-dots"></i> Catatan Admin</p><p class="text-[11px] text-slate-600 italic leading-relaxed">"${f.admin_note}"</p></div>` : ''}
                                ${isLatest && status === 'approved' ? `<div class="mt-4 flex items-center gap-2"><span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded text-[10px] font-bold border border-emerald-200 uppercase tracking-wider"><i class="fas fa-check-circle mr-1"></i> Audit Selesai</span></div>` : ''}
                            </div>
                        </div>`;
                    container.innerHTML += itemHTML;
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