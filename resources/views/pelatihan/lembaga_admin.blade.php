<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Lembaga | SI-MUTU Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none;  scrollbar-width: none; }
        .table-row-hover:hover td { background-color: #f8fafc; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <!-- DATA FETCHING -->
    @php
        use App\Models\User;
        
        // Ambil semua user dengan role 'user' dan kategori 'pelatihan'
        if (!isset($users)) {
            $users = User::where('role', 'user')
                         ->where('category', 'pelatihan')
                         ->orderBy('name', 'asc')
                         ->get();
        }

        $totalLembaga = $users->count();
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
            <div class="lg:hidden bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between sticky top-0 z-30 shadow-sm">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="p-2 text-slate-600 hover:text-blue-600 hover:bg-slate-100 rounded-lg transition-colors">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <span class="font-bold text-slate-800 text-sm tracking-wide">SI-MUTU <span class="text-blue-600">DKKN</span></span>
                </div>
                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-xs font-bold border border-blue-200">
                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                </div>
            </div>

            <!-- HEADER DESKTOP -->
            <div class="hidden md:block">
                @include('components.pelatihan-header', [
                    'title' => 'Data Lembaga',
                    'subtitle' => 'Manajemen data lembaga pelatihan terdaftar'
                ])
            </div>

            <!-- MAIN CONTENT -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-6 lg:p-8 space-y-6">

                <!-- SEARCH & FILTER BAR -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">Daftar Lembaga</h2>
                        <p class="text-sm text-slate-500 mt-1">Total <span class="font-bold text-slate-700">{{ $totalLembaga }}</span> lembaga aktif</p>
                    </div>
                    
                    <div class="relative group w-full sm:w-64">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400 group-focus-within:text-blue-500 transition-colors">
                            <i class="fas fa-search"></i>
                        </div>
                        <input type="text" id="searchInput" onkeyup="filterTable()" class="block w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all shadow-sm" placeholder="Cari nama atau kode...">
                    </div>
                </div>

                <!-- TABLE CARD -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto no-scrollbar">
                        <table class="w-full text-sm text-left text-slate-600 min-w-[900px] md:min-w-0" id="lembagaTable">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-6 py-4 font-bold tracking-wider w-16 text-center">No</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Nama Lembaga</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Kode Instansi</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Kontak</th>
                                    <th class="px-6 py-4 font-bold tracking-wider text-center">Status</th>
                                    <th class="px-6 py-4 font-bold tracking-wider text-center">Bergabung</th>
                                    <th class="px-6 py-4 font-bold tracking-wider text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($users as $index => $u)
                                <tr class="table-row-hover transition-colors group">
                                    <td class="px-6 py-5 text-center text-slate-400 font-mono text-xs">
                                        {{ $index + 1 }}
                                    </td>
                                    
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-xs font-bold border border-slate-200 shadow-sm shrink-0 uppercase">
                                                {{ substr($u->name, 0, 1) }}
                                            </div>
                                            <span class="font-bold text-slate-800 text-sm line-clamp-1">{{ $u->name }}</span>
                                        </div>
                                    </td>

                                    <td class="px-6 py-5">
                                        <span class="bg-slate-100 text-slate-600 px-2 py-1 rounded-md text-xs font-mono font-bold border border-slate-200">
                                            {{ $u->kode_instansi ?? '-' }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-5">
                                        <div class="flex flex-col">
                                            <span class="text-slate-700 text-xs font-medium">{{ $u->email }}</span>
                                        </div>
                                    </td>

                                    <td class="px-6 py-5 text-center">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide bg-emerald-50 text-emerald-600 border border-emerald-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                        </span>
                                    </td>

                                    <td class="px-6 py-5 text-center whitespace-nowrap">
                                        <div class="flex flex-col items-center">
                                            <span class="text-slate-700 text-xs font-bold">{{ $u->created_at->format('d M Y') }}</span>
                                            <span class="text-[10px] text-slate-400">{{ $u->created_at->diffForHumans() }}</span>
                                        </div>
                                    </td>

                                    <td class="px-6 py-5 text-center">
                                        <button onclick="showDetail('{{ $u->name }}', '{{ $u->kode_instansi }}', '{{ $u->email }}', '{{ $u->created_at->format('d M Y') }}')" class="text-slate-400 hover:text-blue-600 p-2 rounded-lg hover:bg-blue-50 transition-all" title="Lihat Detail">
                                            <i class="fas fa-eye text-lg"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 border border-slate-100">
                                                <i class="fas fa-users text-3xl text-slate-300"></i>
                                            </div>
                                            <h3 class="text-slate-800 font-bold">Belum ada data</h3>
                                            <p class="text-slate-500 text-sm mt-1">Data lembaga belum tersedia.</p>
                                        </div>
                                    </td>
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

    <!-- MODAL DETAIL USER (FIX RESPONSIVE) -->
    <div id="detailModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-sm border border-slate-100">
                    
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-5 flex justify-between items-center text-white">
                        <h3 class="text-lg font-bold flex items-center gap-2">
                            <i class="fas fa-id-card"></i> Detail Lembaga
                        </h3>
                        <button onclick="closeModal()" class="text-blue-100 hover:text-white bg-white/10 hover:bg-white/20 p-2 rounded-lg transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="px-6 py-6 space-y-5">
                        <!-- Profile Info -->
                        <div class="flex flex-col items-center sm:flex-row sm:items-center sm:gap-4 mb-2 text-center sm:text-left">
                            <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-2xl font-bold border-2 border-slate-200 uppercase mb-3 sm:mb-0" id="modalAvatar">
                                <!-- Initials -->
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-lg font-bold text-slate-800 leading-tight break-words" id="modalName">Nama Lembaga</h4>
                                <span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded font-mono font-bold mt-1 inline-block" id="modalCode">KODE</span>
                            </div>
                        </div>

                        <!-- Details List (FIX Layout: Flex Col di HP, Row di Tablet+) -->
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 space-y-3">
                            
                            <!-- Email Item -->
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center border-b border-slate-200 pb-2 gap-1">
                                <span class="text-xs font-bold text-slate-500 uppercase whitespace-nowrap">Email</span>
                                <span class="text-sm font-medium text-slate-800 break-all sm:text-right" id="modalEmail">email@example.com</span>
                            </div>
                            
                            <!-- Status Item -->
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center border-b border-slate-200 pb-2 gap-1">
                                <span class="text-xs font-bold text-slate-500 uppercase whitespace-nowrap">Status Akun</span>
                                <span class="inline-flex items-center gap-1 text-emerald-600 text-xs font-bold bg-emerald-50 px-2 py-0.5 rounded-full w-fit">
                                    <i class="fas fa-check-circle"></i> Aktif
                                </span>
                            </div>
                            
                            <!-- Tanggal Bergabung Item -->
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1">
                                <span class="text-xs font-bold text-slate-500 uppercase whitespace-nowrap">Tanggal Bergabung</span>
                                <span class="text-sm font-medium text-slate-800" id="modalJoin">01 Jan 2024</span>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="bg-slate-50 px-6 py-4 flex justify-end border-t border-slate-100">
                        <button onclick="closeModal()" class="w-full sm:w-auto bg-white border border-slate-300 text-slate-700 px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-50 shadow-sm transition-colors">Tutup</button>
                    </div>
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

        // Script Pencarian Sederhana (Client Side)
        function filterTable() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("lembagaTable");
            tr = table.getElementsByTagName("tr");

            for (i = 1; i < tr.length; i++) { 
                var tdName = tr[i].getElementsByTagName("td")[1];
                var tdCode = tr[i].getElementsByTagName("td")[2];
                
                if (tdName || tdCode) {
                    var txtName = tdName.textContent || tdName.innerText;
                    var txtCode = tdCode.textContent || tdCode.innerText;
                    
                    if (txtName.toUpperCase().indexOf(filter) > -1 || txtCode.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }

        // Modal Logic
        function showDetail(name, code, email, joinDate) {
            document.getElementById('modalName').innerText = name;
            document.getElementById('modalCode').innerText = code || '-';
            document.getElementById('modalEmail').innerText = email;
            document.getElementById('modalJoin').innerText = joinDate;
            document.getElementById('modalAvatar').innerText = name.charAt(0);
            
            document.getElementById('detailModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }
    </script>
</body>
</html>