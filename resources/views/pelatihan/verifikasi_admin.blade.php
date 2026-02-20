<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Verifikasi | SI-MUTU Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .table-row-hover:hover td { background-color: #f8fafc; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none;  scrollbar-width: none; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    @php
        use App\Models\Submission;
        use App\Models\User;
        
        if (!isset($data)) {
            $data = Submission::with(['user'])
                        ->where('category', 'pelatihan')
                        ->where('type', 'Verifikasi')
                        ->orderBy('created_at', 'desc')
                        ->get();
        }
        $users = User::where('role', 'user')->where('category', 'pelatihan')->get();
    @endphp

    <div class="flex h-screen overflow-hidden">
        
        <!-- SIDEBAR DESKTOP -->
        <div class="hidden md:flex h-full bg-blue-900">
            @include('components.pelatihan-sidebar')
        </div>

        <!-- MOBILE SIDEBAR OVERLAY -->
        <div id="mobileSidebar" class="fixed inset-0 z-50 hidden">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="toggleSidebar()"></div>
            <div class="absolute left-0 top-0 bottom-0 w-64 bg-blue-900 shadow-xl transform transition-transform duration-300">
                @include('components.pelatihan-sidebar')
            </div>
        </div>

        <div class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50 relative w-full">
            
            <!-- MOBILE HEADER -->
            <div class="md:hidden bg-white border-b border-slate-200 px-4 py-3 flex justify-between items-center z-20">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <span class="font-bold text-slate-800">SI-PELATIHAN</span>
                </div>
                <button onclick="toggleSidebar()" class="text-slate-500 hover:text-blue-600 p-2 rounded-lg hover:bg-slate-100">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>

            <!-- HEADER -->
            <div class="hidden md:block">
                @include('components.pelatihan-header', [
                    'title' => 'Manajemen Verifikasi',
                    'subtitle' => 'Penerbitan dokumen verifikasi ke lembaga'
                ])
            </div>

            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-6 lg:p-8 space-y-6">
                
                @if (session('success'))
                    <div id="alert" class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-xl shadow-sm flex items-center justify-between animate-fade-in-down">
                        <div class="flex items-center gap-3">
                            <div class="bg-emerald-100 p-2 rounded-full text-emerald-600">
                                <i class="fas fa-check-circle text-lg"></i>
                            </div>
                            <p class="font-bold text-sm">{{ session('success') }}</p>
                        </div>
                        <button onclick="document.getElementById('alert').remove()" class="text-emerald-400 hover:text-emerald-600 transition-colors"><i class="fas fa-times"></i></button>
                    </div>
                @endif

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 bg-white flex flex-col md:flex-row justify-between items-center gap-4">
                        <div>
                            <h3 class="font-bold text-slate-800 text-lg">Daftar Dokumen Keluar</h3>
                            <p class="text-sm text-slate-500 mt-0.5">Arsip dokumen verifikasi yang diterbitkan</p>
                        </div>
                        <button onclick="openCreateModal()" class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2.5 rounded-xl shadow-lg shadow-purple-200 transition-all flex items-center gap-2 font-bold text-sm transform active:scale-95">
                            <i class="fas fa-plus-circle"></i> Terbitkan Verifikasi Baru
                        </button>
                    </div>

                    <div class="overflow-x-auto no-scrollbar">
                        <table class="w-full text-sm text-left text-slate-600 min-w-[900px] md:min-w-0">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-6 py-4 font-bold tracking-wider w-32">Tgl Terbit</th>
                                    <!-- FIX: Lebar kolom diperbesar dari w-48 menjadi min-w-[220px] agar tidak sempit -->
                                    <th class="px-6 py-4 font-bold tracking-wider min-w-[220px]">Lembaga Tujuan</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Judul Dokumen</th>
                                    <th class="px-6 py-4 font-bold tracking-wider text-center">File Terkirim</th>
                                    <th class="px-6 py-4 font-bold tracking-wider text-center">Status & Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($data as $item)
                                <tr class="table-row-hover transition-colors group">
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-slate-700">{{ $item->created_at->setTimezone('Asia/Jakarta')->format('d M Y') }}</span>
                                            <span class="text-xs text-slate-400 mt-0.5">{{ $item->created_at->setTimezone('Asia/Jakarta')->format('H:i') }} WIB</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-start gap-3">
                                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 text-xs font-bold shrink-0 mt-0.5">
                                                {{ substr($item->user->name ?? '?', 0, 1) }}
                                            </div>
                                            <div>
                                                <!-- FIX: line-clamp-1 dihapus agar teks nama instansi tidak terpotong -->
                                                <div class="font-bold text-slate-700 text-sm">{{ $item->user->name ?? 'Unknown' }}</div>
                                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $item->user->kode_instansi ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="text-sm font-medium text-slate-800">{{ $item->title }}</span>
                                        @if($item->admin_note)
                                            <p class="text-[10px] text-slate-400 italic mt-1 bg-slate-50 w-fit px-1.5 rounded border border-slate-100">Catatan: "{{ $item->admin_note }}"</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <a href="{{ asset('storage/' . $item->admin_file) }}" target="_blank" class="inline-flex items-center gap-1.5 text-purple-600 hover:text-purple-800 bg-purple-50 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors border border-purple-100 hover:border-purple-200">
                                            <i class="fas fa-file-pdf"></i> PDF
                                        </a>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        @if($item->status == 'approved')
                                            <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-bold border border-emerald-200 inline-flex items-center gap-1">
                                                <i class="fas fa-check-circle"></i> Selesai
                                            </span>
                                        @elseif($item->user_note)
                                            <!-- JIKA USER SUDAH KONFIRMASI -->
                                            <div class="flex flex-col items-center gap-2">
                                                <span class="text-[10px] text-blue-600 font-bold bg-blue-50 px-2 py-0.5 rounded-full border border-blue-100 flex items-center gap-1">
                                                    <i class="fas fa-info-circle"></i> User Konfirmasi
                                                </span>
                                                <form action="{{ url('/submission/approve/' . $item->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-blue-700 shadow-sm transition-all active:scale-95 flex items-center gap-1">
                                                        <i class="fas fa-check-double"></i> Tandai Selesai
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <!-- MENUNGGU USER -->
                                            <span class="bg-slate-100 text-slate-500 px-3 py-1 rounded-full text-xs font-bold border border-slate-200 inline-flex items-center gap-1">
                                                <i class="fas fa-clock"></i> Menunggu
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-folder-open text-3xl mb-2 text-slate-300"></i>
                                            <p>Belum ada dokumen verifikasi diterbitkan.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- MODAL CREATE -->
    <div id="createModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeCreateModal()"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100">
                    
                    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4 flex justify-between items-center text-white">
                        <h3 class="text-lg font-bold flex items-center gap-2">
                            <i class="fas fa-plus-circle"></i> Terbitkan Verifikasi
                        </h3>
                        <button onclick="closeCreateModal()" class="text-purple-100 hover:text-white bg-white/10 hover:bg-white/20 p-2 rounded-lg transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form action="{{ route('verifikasi.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="px-6 py-6 space-y-5">
                            
                            <!-- Pilih Lembaga -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 uppercase">Tujuan Lembaga</label>
                                <div class="relative">
                                    <select name="user_id" class="block w-full rounded-xl border-slate-300 bg-slate-50 p-3 text-sm focus:border-purple-500 focus:ring-purple-500 appearance-none" required>
                                        <option value="" disabled selected>-- Pilih Lembaga --</option>
                                        @foreach($users as $u)
                                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-slate-500">
                                        <i class="fas fa-chevron-down text-xs"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Judul -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 uppercase">Judul Dokumen</label>
                                <input type="text" name="title" class="block w-full rounded-xl border-slate-300 bg-white p-3 text-sm focus:border-purple-500 focus:ring-purple-500" placeholder="Contoh: SK Akreditasi 2026" required>
                            </div>

                            <!-- File -->
                            <div class="space-y-2 bg-slate-50 p-4 rounded-xl border border-slate-200">
                                <label class="block text-xs font-bold text-slate-700 uppercase flex justify-between">
                                    <span>Upload File (PDF)</span>
                                    <span class="text-[10px] bg-red-100 text-red-600 px-1.5 py-0.5 rounded">Wajib</span>
                                </label>
                                <input type="file" name="admin_file" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-white file:text-purple-700 hover:file:bg-purple-50 border border-slate-300 rounded-lg cursor-pointer bg-white" accept=".pdf" required>
                            </div>

                            <!-- Catatan -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 uppercase">Catatan</label>
                                <textarea name="admin_note" rows="2" class="block w-full rounded-xl border-slate-300 bg-white p-3 text-sm focus:border-purple-500 focus:ring-purple-500" placeholder="Pesan untuk lembaga..."></textarea>
                            </div>
                        </div>

                        <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-slate-100">
                            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-purple-200 hover:shadow-none transition-all active:scale-95">Terbitkan</button>
                            <button type="button" onclick="closeCreateModal()" class="bg-white border border-slate-300 text-slate-700 px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-50 shadow-sm transition-all">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('mobileSidebar');
            if (sidebar.classList.contains('hidden')) { sidebar.classList.remove('hidden'); } 
            else { sidebar.classList.add('hidden'); }
        }
        function openCreateModal() { document.getElementById('createModal').classList.remove('hidden'); }
        function closeCreateModal() { document.getElementById('createModal').classList.add('hidden'); }
    </script>
</body>
</html>