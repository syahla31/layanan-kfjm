<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pengguna | SI-MUTU</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        
        .row-interactive { 
            transition: all 0.2s ease; 
            cursor: pointer; 
        }
        
        .row-interactive:hover { background-color: #f8fafc; }
        
        /* Efek Glow Admin yang Baru: Menggunakan background animation agar tidak merusak layout */
        .admin-row:hover td {
            background-image: linear-gradient(90deg, transparent, rgba(245, 158, 11, 0.04), transparent);
            background-size: 200% 100%;
            animation: admin-shine 1.5s linear infinite;
        }

        @keyframes admin-shine {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        
        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    </style>
</head>

@php
    // LOGIKA DATABASE
    $usersFromDb = App\Models\User::all()
        ->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => strtolower($user->role), 
                'category' => strtolower($user->category ?? 'umum'),
                'status' => strtolower($user->status),
                'date' => $user->created_at->format('d M Y'),
                'surat_kuasa' => $user->surat_kuasa_path,
            ];
        });
@endphp

<body class="bg-slate-50 text-slate-800 h-full overflow-hidden" x-data="{
    sidebarOpen: false,
    filterOpen: false,
    exportMenuOpen: false,
    selectedUser: null,
    editingUser: null,
    exporting: false,
    exportType: '',
    showExportToast: false,
    searchQuery: '',
    filterCategory: 'Semua',
    filterStatus: 'Semua',
    
    catDropdownOpen: false,
    statDropdownOpen: false,

    users: {{ $usersFromDb->toJson() }},

    getCategoryLabel(user) {
        const labels = {
            'sinarx': 'Sinar-X',
            'uji': 'Lembaga Uji',
            'pelatihan': 'Pelatihan',
            'umum': 'Umum'
        };
        const cat = user.category.toLowerCase();
        return labels[cat] || user.category.toUpperCase();
    },

    runExport(type) {
        this.exportType = type;
        this.exporting = true;
        this.exportMenuOpen = false;
        
        // Trigger actual Laravel export
        if (type === 'PDF') {
            window.location.href = '{{ route('internal.users.export') }}?format=pdf' +
                (this.searchQuery ? '&search=' + this.searchQuery : '') +
                (this.filterCategory !== 'Semua' ? '&category=' + this.filterCategory : '') +
                (this.filterStatus !== 'Semua' ? '&status=' + this.filterStatus : '');
        }

        setTimeout(() => {
            this.exporting = false;
            this.showExportToast = true;
            setTimeout(() => this.showExportToast = false, 4000);
        }, 1500);
    }
}">

    <div class="flex h-screen overflow-hidden w-full">

        <!-- SIDEBAR -->
        @include('components.internal-sidebar')

        <div class="flex-1 flex flex-col min-w-0 h-full bg-slate-50/50 relative overflow-hidden">

            <!-- HEADER -->
            <header class="bg-white shadow-sm h-20 flex items-center justify-between px-4 lg:px-8 shrink-0 z-30">
                <div class="flex items-center gap-4 min-w-0">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-slate-500 text-xl p-2 hover:bg-slate-100 rounded-lg">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="truncate">
                        <h2 class="text-lg md:text-xl font-bold text-slate-800 tracking-tight truncate">Data Master Pengguna</h2>
                        <p class="text-[10px] text-slate-400 font-medium uppercase tracking-widest hidden sm:block truncate">Manajemen Akun Terdaftar & Administrator</p>
                    </div>
                </div>
                
                <div class="hidden md:flex items-center gap-3 shrink-0">
                    <div class="text-right">
                        <p class="text-xs font-bold text-slate-700">Admin Central</p>
                        <p class="text-[9px] text-slate-400">Super Administrator</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center">
                        <i class="fas fa-user-shield text-slate-400 text-sm"></i>
                    </div>
                </div>
            </header>

            <!-- KONTEN UTAMA -->
            <main class="flex-1 overflow-y-auto p-4 lg:p-8 custom-scrollbar">

                <!-- TOOLBAR -->
                <div class="mb-6 flex flex-col md:flex-row gap-4 items-stretch">
                    <div class="flex-1 relative group min-w-0">
                        <input type="text" x-model="searchQuery" placeholder="Cari nama, email, atau ID..."
                            class="w-full py-3.5 pl-12 pr-4 bg-white border border-slate-200 rounded-2xl text-sm focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all shadow-sm">
                        <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    </div>
                    <div class="flex gap-2 shrink-0">
                        <button @click="filterOpen = !filterOpen" :class="filterOpen ? 'bg-slate-900 text-white' : 'bg-white text-slate-600'"
                            class="px-6 py-3.5 rounded-2xl text-sm font-bold border border-slate-200 transition-all flex items-center justify-center gap-2 shadow-sm active:scale-95">
                            <i class="fas fa-sliders-h text-xs"></i> <span>Filter</span>
                        </button>

                        <div class="relative" @click.away="exportMenuOpen = false">
                            <button @click="exportMenuOpen = !exportMenuOpen" :disabled="exporting"
                                class="bg-white border border-slate-200 text-slate-600 px-6 py-3.5 rounded-2xl text-sm font-bold hover:bg-slate-50 transition-all flex items-center justify-center gap-2 shadow-sm active:scale-95 disabled:opacity-50">
                                <template x-if="!exporting"><i class="fas fa-file-export text-xs text-emerald-500"></i></template>
                                <template x-if="exporting"><i class="fas fa-circle-notch fa-spin text-xs"></i></template>
                                <span x-text="exporting ? 'Proses...' : 'Export'"></span>
                                <i class="fas fa-chevron-down text-[10px] ml-1 opacity-50"></i>
                            </button>
                            <div x-show="exportMenuOpen" x-cloak x-transition class="absolute right-0 mt-2 w-48 bg-white border border-slate-200 rounded-2xl shadow-xl z-50 overflow-hidden">
                                <button @click="runExport('PDF')" class="w-full px-5 py-3 text-left text-xs font-bold text-slate-600 hover:bg-red-50 hover:text-red-700 flex items-center gap-3 transition-colors">
                                    <i class="fas fa-file-pdf text-red-500"></i> Export PDF (.pdf)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PANEL FILTER -->
                <div x-show="filterOpen" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                    class="mb-6 p-6 md:p-8 bg-white border border-slate-200 rounded-[2rem] shadow-xl shadow-slate-200/50 grid grid-cols-1 md:grid-cols-3 gap-6 relative z-20">
                    
                    <div class="relative" @click.away="catDropdownOpen = false">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Modul / Role</label>
                        <button @click="catDropdownOpen = !catDropdownOpen" 
                            class="w-full bg-slate-50 border border-slate-100 hover:border-slate-300 rounded-2xl px-5 py-3.5 text-xs font-bold flex items-center justify-between transition-all active:scale-[0.98]">
                            <span class="text-slate-700 truncate" x-text="filterCategory === 'Semua' ? 'Semua Kategori' : (filterCategory === 'admin' ? 'Semua Admin' : filterCategory.charAt(0).toUpperCase() + filterCategory.slice(1))"></span>
                            <i class="fas fa-chevron-down text-[10px] transition-transform duration-300 shrink-0" :class="catDropdownOpen ? 'rotate-180' : ''"></i>
                        </button>
                        
                        <div x-show="catDropdownOpen" x-cloak x-transition 
                            class="absolute left-0 right-0 mt-2 bg-white border border-slate-100 rounded-2xl shadow-2xl z-30 overflow-hidden custom-scrollbar max-h-60 overflow-y-auto">
                            <div class="p-2 space-y-1">
                                <template x-for="opt in ['Semua', 'admin', 'pelatihan', 'uji', 'sinarx']">
                                    <button @click="filterCategory = opt; catDropdownOpen = false"
                                        :class="filterCategory === opt ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-50'"
                                        class="w-full px-4 py-3 text-left text-[11px] font-bold rounded-xl transition-all flex items-center justify-between">
                                        <span x-text="opt === 'Semua' ? 'Semua Kategori' : (opt === 'admin' ? 'Semua Admin' : (opt === 'uji' ? 'Lembaga Uji' : opt.charAt(0).toUpperCase() + opt.slice(1)))"></span>
                                        <i x-show="filterCategory === opt" class="fas fa-check text-[10px]"></i>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="relative" @click.away="statDropdownOpen = false">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Status Akun</label>
                        <button @click="statDropdownOpen = !statDropdownOpen" 
                            class="w-full bg-slate-50 border border-slate-100 hover:border-slate-300 rounded-2xl px-5 py-3.5 text-xs font-bold flex items-center justify-between transition-all active:scale-[0.98]">
                            <span class="text-slate-700" x-text="filterStatus === 'Semua' ? 'Semua Status' : filterStatus.toUpperCase()"></span>
                            <i class="fas fa-chevron-down text-[10px] transition-transform duration-300" :class="statDropdownOpen ? 'rotate-180' : ''"></i>
                        </button>
                        
                        <div x-show="statDropdownOpen" x-cloak x-transition 
                            class="absolute left-0 right-0 mt-2 bg-white border border-slate-100 rounded-2xl shadow-2xl z-30 overflow-hidden">
                            <div class="p-2 space-y-1">
                                <template x-for="opt in ['Semua', 'active', 'pending']">
                                    <button @click="filterStatus = opt; statDropdownOpen = false"
                                        :class="filterStatus === opt ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-50'"
                                        class="w-full px-4 py-3 text-left text-[11px] font-bold rounded-xl transition-all flex items-center justify-between">
                                        <span x-text="opt === 'Semua' ? 'Semua Status' : opt.toUpperCase()"></span>
                                        <i x-show="filterStatus === opt" class="fas fa-check text-[10px]"></i>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-end">
                        <button @click="filterOpen = false" 
                            class="w-full bg-slate-900 text-white py-4 rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-slate-800 transition-all active:scale-95 shadow-lg shadow-slate-900/20">
                            Terapkan Filter
                        </button>
                    </div>
                </div>

                <!-- TABEL -->
                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 relative z-10 w-full overflow-hidden">
                    <div class="overflow-x-auto custom-scrollbar w-full">
                        <table class="w-full text-sm text-left text-slate-600 table-auto border-collapse">
                            <thead class="text-[10px] text-slate-400 uppercase bg-slate-50/80 border-b border-slate-100">
                                <tr>
                                    <th class="px-8 py-5 font-bold min-w-[250px]">Identitas Pengguna</th>
                                    <th class="px-6 py-5 font-bold">Kontak & Registrasi</th>
                                    <th class="px-6 py-5 font-bold">Kategori / Role</th>
                                    <th class="px-6 py-5 font-bold text-center">Status</th>
                                    <th class="px-8 py-5 font-bold text-center w-32">Opsi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <template x-for="user in users" :key="user.id">
                                    <tr class="row-interactive group" 
                                        @click="selectedUser = user"
                                        :class="user.role === 'admin' ? 'admin-row bg-amber-50/30' : ''"
                                        x-show="(
                                            filterCategory === 'Semua' || 
                                            user.category.toLowerCase() === filterCategory.toLowerCase() || 
                                            (filterCategory === 'admin' && user.role === 'admin')
                                        ) && (
                                            filterStatus === 'Semua' || 
                                            user.status.toLowerCase() === filterStatus.toLowerCase()
                                        ) && (
                                            user.name.toLowerCase().includes(searchQuery.toLowerCase()) || 
                                            user.email.toLowerCase().includes(searchQuery.toLowerCase())
                                        )">
                                        
                                        <td class="px-8 py-5 relative z-10">
                                            <div class="flex items-center gap-4">
                                                <div :class="user.role === 'admin' ? 'bg-amber-100 text-amber-600 border-amber-200' : 'bg-slate-100 text-slate-600 border-slate-200'"
                                                    class="w-12 h-12 rounded-2xl flex items-center justify-center text-xs font-bold border shrink-0 relative">
                                                    <span x-show="user.role !== 'admin'" x-text="user.name.substring(0,2).toUpperCase()"></span>
                                                    <i x-show="user.role === 'admin'" class="fas fa-crown text-lg"></i>
                                                    <div x-show="user.role === 'admin'" class="absolute -top-1 -right-1 w-4 h-4 bg-amber-500 rounded-full border-2 border-white flex items-center justify-center">
                                                        <i class="fas fa-check text-[6px] text-white"></i>
                                                    </div>
                                                </div>
                                                <div class="min-w-0">
                                                    <span class="font-bold block group-hover:text-red-600 transition-colors truncate" 
                                                        :class="user.role === 'admin' ? 'text-red-600' : 'text-slate-800'"
                                                        x-text="user.name"></span>
                                                    <span class="text-[10px] text-slate-400 font-mono" x-text="'ID: #000' + user.id"></span>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-5 whitespace-nowrap relative z-10">
                                            <div class="flex flex-col">
                                                <div class="text-xs text-slate-600 font-medium" x-text="user.email"></div>
                                                <div class="text-[10px] text-slate-400 flex items-center gap-1 mt-0.5">
                                                    <i class="far fa-calendar-alt"></i> <span x-text="user.date"></span>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-5 relative z-10">
                                            <span :class="{
                                                    'bg-blue-50 text-blue-600 border-blue-100': user.category === 'pelatihan',
                                                    'bg-purple-50 text-purple-600 border-purple-100': user.category === 'uji',
                                                    'bg-orange-50 text-orange-600 border-orange-100': user.category === 'sinarx',
                                                    'bg-slate-50 text-slate-500 border-slate-100': user.category === 'umum'
                                                }"
                                                class="px-3 py-1.5 rounded-xl border text-[10px] font-bold uppercase inline-block shadow-sm"
                                                x-text="getCategoryLabel(user)"></span>
                                        </td>

                                        <td class="px-6 py-5 text-center relative z-10">
                                            <template x-if="user.status === 'active'">
                                                <span class="inline-flex items-center gap-1.5 text-emerald-600 text-[10px] font-bold px-3 py-1 bg-emerald-50 rounded-full border border-emerald-100">
                                                    <i class="fas fa-circle text-[6px]"></i> AKTIF
                                                </span>
                                            </template>
                                            <template x-if="user.status === 'pending'">
                                                <span class="inline-flex items-center gap-1.5 text-amber-500 text-[10px] font-bold px-3 py-1 bg-amber-50 rounded-full border border-amber-100">
                                                    <i class="fas fa-circle text-[6px]"></i> PENDING
                                                </span>
                                            </template>
                                        </td>

                                        <td class="px-8 py-5 relative z-10" @click.stop>
                                            <div class="flex justify-center gap-2">
                                                <button @click="selectedUser = user" class="w-8 h-8 rounded-lg bg-slate-50 text-slate-400 hover:bg-slate-900 hover:text-white flex items-center justify-center transition-all active:scale-90" title="Detail">
                                                    <i class="fas fa-info-circle text-xs"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- MODAL DETAIL -->
    <template x-if="selectedUser">
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-transition>
            <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden border border-white/20 relative" @click.away="selectedUser = null">
                <div :class="selectedUser.role === 'admin' ? 'bg-amber-500' : 'bg-slate-900'" class="p-8 text-white relative overflow-hidden transition-colors">
                    <div class="absolute top-0 right-0 p-10 opacity-10 -mr-10 -mt-5">
                        <i :class="selectedUser.role === 'admin' ? 'fa-crown' : 'fa-users'" class="fas text-[10rem]"></i>
                    </div>
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <h3 class="font-bold text-2xl tracking-tight" x-text="selectedUser.role === 'admin' ? 'Profil Administrator' : 'Detail Pengguna'"></h3>
                            <p class="text-[10px] text-white/70 uppercase tracking-[0.2em] mt-1 font-bold">Informasi Entitas & Verifikasi Sistem</p>
                        </div>
                        <button @click="selectedUser = null" class="w-10 h-10 rounded-2xl bg-white/20 flex items-center justify-center hover:bg-red-500 transition-all hover:rotate-90">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <div class="p-8 space-y-6 relative z-10">
                    <div class="flex items-center gap-6 pb-6 border-b border-slate-100">
                        <div :class="selectedUser.role === 'admin' ? 'from-amber-400 to-amber-600' : 'from-red-500 to-red-700'"
                            class="w-20 h-20 rounded-3xl bg-gradient-to-br text-white flex items-center justify-center text-2xl font-black shadow-xl shrink-0">
                            <template x-if="selectedUser.role === 'admin'"><i class="fas fa-crown text-3xl"></i></template>
                            <template x-if="selectedUser.role !== 'admin'"><span x-text="selectedUser.name.substring(0,2).toUpperCase()"></span></template>
                        </div>
                        <div class="min-w-0">
                            <h4 class="font-black text-slate-800 text-xl md:text-2xl truncate" x-text="selectedUser.name"></h4>
                            <div class="flex items-center gap-2 text-sm text-slate-500 mt-1 truncate">
                                <i class="fas fa-envelope text-red-500 text-xs shrink-0"></i>
                                <span class="truncate" x-text="selectedUser.email"></span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-5 bg-slate-50 rounded-[2rem] border border-slate-100">
                            <p class="text-[10px] uppercase font-black text-slate-400 mb-2 tracking-widest">Kategori Layanan</p>
                            <span class="text-xs font-bold text-slate-700 uppercase" x-text="getCategoryLabel(selectedUser)"></span>
                        </div>
                        <div class="p-5 bg-slate-50 rounded-[2rem] border border-slate-100">
                            <p class="text-[10px] uppercase font-black text-slate-400 mb-2 tracking-widest">Status Verifikasi</p>
                            <span class="text-xs font-bold flex items-center gap-2" :class="selectedUser.status === 'active' ? 'text-emerald-600' : 'text-amber-500'">
                                <i class="fas fa-circle text-[8px]"></i>
                                <span x-text="selectedUser.status === 'active' ? 'AKTIF' : 'PENDING'"></span>
                            </span>
                        </div>
                        <div class="p-5 bg-slate-50 rounded-[2rem] border border-slate-100 col-span-2">
                            <template x-if="!selectedUser.surat_kuasa">
                                <span class="text-xs font-bold text-slate-400 italic">Dokumen belum diunggah</span>
                            </template>
                        </div>
                    </div>

                    <div class="pt-6 flex gap-3">
                        <button @click="selectedUser = null" class="flex-1 px-6 py-4 bg-slate-100 hover:bg-slate-200 rounded-2xl text-xs font-bold text-slate-600 transition-all active:scale-95">Tutup</button>
                        
                        <template x-if="selectedUser.surat_kuasa">
                            <a :href="'/internal/users/' + selectedUser.id + '/surat-kuasa'"
                                class="flex-[2] px-6 py-4 bg-red-600 hover:bg-red-700 text-white rounded-2xl text-xs font-bold shadow-lg shadow-red-900/20 transition-all flex items-center justify-center gap-2 active:scale-95">
                                <i class="fas fa-file-pdf text-xs"></i> Review Surat Kuasa
                            </a>
                        </template>
                        <template x-if="!selectedUser.surat_kuasa">
                            <button disabled
                                class="flex-[2] px-6 py-4 bg-slate-300 text-white rounded-2xl text-xs font-bold transition-all flex items-center justify-center gap-2 cursor-not-allowed opacity-50">
                                <i class="fas fa-exclamation-circle text-xs"></i> Kelola Data
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- TOAST NOTIFIKASI -->
    <div x-show="showExportToast" x-cloak x-transition
        class="fixed bottom-4 right-4 md:bottom-8 md:right-8 z-[100] p-6 bg-slate-900 text-white rounded-[2rem] shadow-2xl border border-white/10 flex items-center gap-5 min-w-[280px] max-w-[90vw]">
        <div class="w-12 h-12 rounded-2xl bg-emerald-500 flex items-center justify-center text-white shadow-lg shadow-emerald-500/20 shrink-0">
            <i class="fas fa-check text-xl"></i>
        </div>
        <div class="min-w-0">
            <p class="font-bold text-sm tracking-tight truncate" x-text="exportType ? 'Export Selesai!' : 'Update Berhasil!'"></p>
            <p class="text-[10px] opacity-60 uppercase font-bold tracking-widest mt-0.5 truncate"
                x-text="exportType ? `File ${exportType} Berhasil Diunduh` : 'Data Instansi Telah Disimpan'">
            </p>
        </div>
        <button @click="showExportToast = false" class="ml-auto opacity-30 hover:opacity-100 transition-opacity">
            <i class="fas fa-times"></i>
        </button>
    </div>

</body>
</html>
