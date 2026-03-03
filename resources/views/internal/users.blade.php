<!DOCTYPE html>

<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pengguna | SI-MUTU</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Link asli yang sudah diperbaiki -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }

        .row-interactive {
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .row-interactive:hover {
            background-color: #f8fafc;
        }
    </style>


</head>

@php
    // LOGIKA DATABASE: Mengambil data user dari database
    // Kita format ke array agar bisa dibaca oleh Alpine.js untuk pencarian instan
    $usersFromDb = App\Models\User::where('role', '!=', 'admin')
        ->get()
        ->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'category' => $user->category ?? 'umum',
                'status' => $user->status,
                'date' => $user->created_at->format('d M Y')
            ];
        });
@endphp

<body class="bg-slate-50 text-slate-800 overflow-hidden" x-data="{
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
    
    // DATA DARI DATABASE
    users: {{ $usersFromDb->toJson() }},

    runExport(type) {
        this.exportType = type;
        this.exporting = true;
        this.exportMenuOpen = false;
        setTimeout(() => {
            this.exporting = false;
            this.showExportToast = true;
            setTimeout(() => this.showExportToast = false, 4000);
        }, 2500);
    }
}">

    <div class="flex flex-col lg:flex-row h-screen overflow-hidden">

        <!-- SIDEBAR -->
        @include('components.internal-sidebar')

        <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden bg-slate-50/50">

            <!-- HEADER -->
            <header class="bg-white shadow-sm h-20 flex items-center justify-between px-4 lg:px-8 shrink-0 z-10">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="lg:hidden text-slate-500 text-xl p-2 hover:bg-slate-100 rounded-lg">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div>
                        <h2 class="text-xl font-bold text-slate-800 tracking-tight">Data Master Pengguna</h2>
                        <p class="text-[10px] text-slate-400 font-medium uppercase tracking-widest hidden sm:block">
                            Manajemen Akun Terdaftar</p>
                    </div>
                </div>
            </header>

            <!-- KONTEN UTAMA -->
            <main class="flex-1 overflow-y-auto p-4 lg:p-8">

                <!-- TOOLBAR -->
                <div class="mb-6 flex flex-col md:flex-row gap-4 items-stretch">
                    <div class="flex-1 relative group">
                        <input type="text" x-model="searchQuery" placeholder="Cari nama instansi, email, atau ID..."
                            class="w-full py-3.5 pl-12 pr-4 bg-white border border-slate-200 rounded-2xl text-sm focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all shadow-sm">
                        <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    </div>
                    <div class="flex gap-2">
                        <button @click="filterOpen = !filterOpen"
                            :class="filterOpen ? 'bg-slate-900 text-white' : 'bg-white text-slate-600'"
                            class="flex-1 md:flex-none border border-slate-200 px-6 py-3.5 rounded-2xl text-sm font-bold transition-all flex items-center justify-center gap-2 shadow-sm active:scale-95">
                            <i class="fas fa-sliders-h text-xs"></i> <span>Filter</span>
                        </button>

                        <!-- TOMBOL EXPORT DENGAN DROPDOWN -->
                        <div class="relative flex-1 md:flex-none" @click.away="exportMenuOpen = false">
                            <button @click="exportMenuOpen = !exportMenuOpen" :disabled="exporting"
                                class="w-full bg-white border border-slate-200 text-slate-600 px-6 py-3.5 rounded-2xl text-sm font-bold hover:bg-slate-50 transition-all flex items-center justify-center gap-2 shadow-sm active:scale-95 disabled:opacity-50">
                                <template x-if="!exporting">
                                    <i class="fas fa-file-export text-xs text-emerald-500"></i>
                                </template>
                                <template x-if="exporting">
                                    <i class="fas fa-circle-notch fa-spin text-xs"></i>
                                </template>
                                <span x-text="exporting ? 'Proses...' : 'Export'"></span>
                                <i class="fas fa-chevron-down text-[10px] ml-1 opacity-50"></i>
                            </button>

                            <!-- Menu Dropdown Export -->
                            <div x-show="exportMenuOpen" x-cloak x-transition
                                class="absolute right-0 mt-2 w-48 bg-white border border-slate-200 rounded-2xl shadow-xl z-50 overflow-hidden">
                                <button @click="runExport('Excel')"
                                    class="w-full px-5 py-3 text-left text-xs font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 flex items-center gap-3 transition-colors">
                                    <i class="fas fa-file-excel text-emerald-500"></i> Export Excel (.xlsx)
                                </button>
                                <button @click="runExport('PDF')"
                                    class="w-full px-5 py-3 text-left text-xs font-bold text-slate-600 hover:bg-red-50 hover:text-red-700 flex items-center gap-3 transition-colors">
                                    <i class="fas fa-file-pdf text-red-500"></i> Export PDF (.pdf)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PANEL FILTER -->
                <div x-show="filterOpen" x-cloak x-transition
                    class="mb-6 p-6 bg-white border border-slate-200 rounded-3xl shadow-xl grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label
                            class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Modul</label>
                        <select x-model="filterCategory"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs outline-none">
                            <option value="Semua">Semua Kategori</option>
                            <option value="pelatihan">Pelatihan</option>
                            <option value="uji">Lembaga Uji</option>
                            <option value="sinarx">Sinar-X</option>
                        </select>
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Status</label>
                        <select x-model="filterStatus"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs outline-none">
                            <option value="Semua">Semua Status</option>
                            <option value="active">Aktif</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button @click="filterOpen = false"
                            class="w-full bg-slate-900 text-white py-2.5 rounded-xl text-xs font-bold">Terapkan
                            Filter</button>
                    </div>
                </div>

                <!-- TABEL -->
                <div class="bg-white rounded-3xl shadow-sm overflow-hidden border border-slate-200">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                            <thead
                                class="text-[10px] text-slate-400 uppercase bg-slate-50/80 border-b border-slate-100">
                                <tr>
                                    <th class="px-8 py-5 font-bold">Instansi</th>
                                    <th class="px-6 py-5 font-bold">Kontak & Registrasi</th>
                                    <th class="px-6 py-5 font-bold">Kategori</th>
                                    <th class="px-6 py-5 font-bold text-center">Status</th>
                                    <th class="px-8 py-5 font-bold text-center">Opsi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <template x-for="user in users" :key="user.id">
                                    <tr class="row-interactive group" @click="selectedUser = user"
                                        x-show="(filterCategory === 'Semua' || user.category === filterCategory) && (filterStatus === 'Semua' || user.status === filterStatus) && (user.name.toLowerCase().includes(searchQuery.toLowerCase()) || user.email.toLowerCase().includes(searchQuery.toLowerCase()))">
                                        <td class="px-8 py-5">
                                            <div class="flex items-center gap-4">
                                                <div
                                                    class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-xs font-bold border border-slate-200">
                                                    <span x-text="user.name.substring(0,2)"></span>
                                                </div>
                                                <div>
                                                    <span
                                                        class="font-bold text-slate-800 block group-hover:text-red-600"
                                                        x-text="user.name"></span>
                                                    <span class="text-[10px] text-slate-400 font-mono"
                                                        x-text="'ID: #000' + user.id"></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="text-xs text-slate-600" x-text="user.email"></div>
                                            <div class="text-[10px] text-slate-400" x-text="user.date"></div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <span
                                                :class="{
                                                    'bg-blue-50 text-blue-600 border-blue-100': user
                                                        .category === 'pelatihan',
                                                    'bg-purple-50 text-purple-600 border-purple-100': user
                                                        .category === 'uji',
                                                    'bg-orange-50 text-orange-600 border-orange-100': user
                                                        .category === 'sinarx'
                                                }"
                                                class="px-3 py-1.5 rounded-xl border text-[10px] font-bold uppercase"
                                                x-text="user.category === 'sinarx' ? 'Sinar-X' : (user.category === 'uji' ? 'Lembaga Uji' : 'Pelatihan')"></span>
                                        </td>
                                        <td class="px-6 py-5 text-center">
                                            <template x-if="user.status === 'active'">
                                                <span
                                                    class="inline-flex items-center gap-1.5 text-emerald-600 text-[10px] font-bold px-3 py-1 bg-emerald-50 rounded-full border border-emerald-100">
                                                    <i class="fas fa-circle text-[6px]"></i> AKTIF
                                                </span>
                                            </template>
                                            <template x-if="user.status === 'pending'">
                                                <span
                                                    class="inline-flex items-center gap-1.5 text-amber-500 text-[10px] font-bold px-3 py-1 bg-amber-50 rounded-full border border-amber-100">
                                                    <i class="fas fa-circle text-[6px]"></i> PENDING
                                                </span>
                                            </template>
                                        </td>
                                        <td class="px-8 py-5 text-center" @click.stop>
                                            <div class="flex justify-center gap-2">
                                                <button @click="selectedUser = user"
                                                    class="w-9 h-9 rounded-xl bg-slate-50 text-slate-400 hover:bg-slate-900 hover:text-white flex items-center justify-center transition-all active:scale-90"
                                                    title="Detail">
                                                    <i class="fas fa-info-circle text-xs"></i>
                                                </button>
                                                <button @click="editingUser = JSON.parse(JSON.stringify(user))"
                                                    class="w-9 h-9 rounded-xl bg-slate-50 text-slate-400 hover:bg-red-600 hover:text-white flex items-center justify-center transition-all active:scale-90"
                                                    title="Edit">
                                                    <i class="fas fa-pen text-xs"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- MODAL DETAIL -->
                <template x-if="selectedUser">
                    <div
                        class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
                        <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden border border-white/20 relative"
                            @click.away="selectedUser = null">
                            <div class="bg-slate-900 p-8 text-white relative overflow-hidden">
                                <div class="absolute top-0 right-0 p-10 opacity-5 -mr-10 -mt-5">
                                    <i class="fas fa-users text-[10rem]"></i>
                                </div>
                                <div class="flex justify-between items-start relative z-10">
                                    <div>
                                        <h3 class="font-bold text-2xl tracking-tight">Detail Pengguna</h3>
                                        <p
                                            class="text-[10px] text-slate-400 uppercase tracking-[0.2em] mt-1 font-bold">
                                            Informasi Entitas & Verifikasi</p>
                                    </div>
                                    <button @click="selectedUser = null"
                                        class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center hover:bg-red-500 transition-all hover:rotate-90">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="p-8 space-y-6 relative z-10">
                                <div class="flex items-center gap-6 pb-6 border-b border-slate-100">
                                    <div
                                        class="w-20 h-20 rounded-3xl bg-gradient-to-br from-red-500 to-red-700 text-white flex items-center justify-center text-2xl font-black shadow-xl shadow-red-500/20">
                                        <span x-text="selectedUser.name.substring(0,2).toUpperCase()"></span>
                                    </div>
                                    <div>
                                        <h4 class="font-black text-slate-800 text-2xl" x-text="selectedUser.name">
                                        </h4>
                                        <div class="flex items-center gap-2 text-sm text-slate-500 mt-1">
                                            <i class="fas fa-envelope text-red-500 text-xs"></i>
                                            <span x-text="selectedUser.email"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="p-5 bg-slate-50 rounded-[2rem] border border-slate-100">
                                        <p
                                            class="text-[10px] uppercase font-black text-slate-400 mb-2 tracking-widest">
                                            Modul Layanan</p>
                                        <span class="text-xs font-bold text-slate-700 uppercase"
                                            x-text="selectedUser.category === 'sinarx' ? 'Sinar-X' : (selectedUser.category === 'uji' ? 'Lembaga Uji' : 'Pelatihan')"></span>
                                    </div>
                                    <div class="p-5 bg-slate-50 rounded-[2rem] border border-slate-100">
                                        <p
                                            class="text-[10px] uppercase font-black text-slate-400 mb-2 tracking-widest">
                                            Status Verifikasi</p>
                                        <span class="text-xs font-bold flex items-center gap-2"
                                            :class="selectedUser.status === 'active' ? 'text-emerald-600' : 'text-amber-500'">
                                            <i class="fas fa-circle text-[8px]"></i>
                                            <span
                                                x-text="selectedUser.status === 'active' ? 'AKTIF' : 'PENDING'"></span>
                                        </span>
                                    </div>
                                </div>

                                <div
                                    class="p-5 bg-slate-50 rounded-[2rem] border border-slate-100 flex items-center justify-between">
                                    <div>
                                        <p
                                            class="text-[10px] uppercase font-black text-slate-400 mb-1 tracking-widest">
                                            Waktu Pendaftaran</p>
                                        <span class="text-xs font-bold text-slate-700"
                                            x-text="selectedUser.date"></span>
                                    </div>
                                    <i class="fas fa-history text-slate-200 text-xl"></i>
                                </div>

                                <div class="pt-6 flex gap-3">
                                    <button @click="selectedUser = null"
                                        class="flex-1 px-6 py-4 bg-slate-100 hover:bg-slate-200 rounded-2xl text-xs font-bold text-slate-600 transition-all active:scale-95">Tutup</button>
                                    <button
                                        @click="selectedUser = null; editingUser = JSON.parse(JSON.stringify(selectedUser))"
                                        class="flex-[2] px-6 py-4 bg-red-600 hover:bg-red-700 text-white rounded-2xl text-xs font-bold shadow-lg shadow-red-900/20 transition-all flex items-center justify-center gap-2 active:scale-95">
                                        <i class="fas fa-pen text-xs"></i> Kelola Data
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- MODAL EDIT -->
                <template x-if="editingUser">
                    <div
                        class="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
                        <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden"
                            @click.away="editingUser = null">
                            <div class="bg-slate-900 p-8 text-white flex justify-between items-center">
                                <h3 class="font-bold text-xl">Edit Data Instansi</h3>
                                <button @click="editingUser = null" class="text-slate-400 hover:text-white"><i
                                        class="fas fa-times"></i></button>
                            </div>
                            <div class="p-8 space-y-4">
                                <div>
                                    <label
                                        class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Nama
                                        Instansi</label>
                                    <input type="text" x-model="editingUser.name"
                                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3 text-sm outline-none focus:ring-2 focus:ring-red-500/20">
                                </div>
                                <div>
                                    <label
                                        class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Email</label>
                                    <input type="email" x-model="editingUser.email"
                                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3 text-sm outline-none focus:ring-2 focus:ring-red-500/20">
                                </div>
                                <div class="pt-6 flex gap-3">
                                    <button @click="editingUser = null"
                                        class="flex-1 py-4 bg-slate-100 rounded-2xl text-xs font-bold text-slate-500">Batal</button>
                                    <button @click="editingUser = null; showExportToast = true"
                                        class="flex-[2] py-4 bg-red-600 text-white rounded-2xl text-xs font-bold shadow-lg shadow-red-900/20">Simpan
                                        Perubahan</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- TOAST NOTIFIKASI EXPORT / UPDATE -->
                <div x-show="showExportToast" x-cloak x-transition
                    class="fixed bottom-8 right-8 z-[100] p-6 bg-slate-900 text-white rounded-[2rem] shadow-2xl border border-white/10 flex items-center gap-5 min-w-[300px]">
                    <div
                        class="w-12 h-12 rounded-2xl bg-emerald-500 flex items-center justify-center text-white shadow-lg shadow-emerald-500/20">
                        <i class="fas fa-check text-xl"></i>
                    </div>
                    <div>
                        <p class="font-bold text-sm tracking-tight"
                            x-text="exportType ? 'Export Selesai!' : 'Update Berhasil!'"></p>
                        <p class="text-[10px] opacity-60 uppercase font-bold tracking-widest mt-0.5"
                            x-text="exportType ? `File ${exportType} Berhasil Diunduh` : 'Data Instansi Telah Disimpan'">
                        </p>
                    </div>
                    <button @click="showExportToast = false"
                        class="ml-auto opacity-30 hover:opacity-100 transition-opacity"><i
                            class="fas fa-times"></i></button>
                </div>

            </main>
        </div>
    </div>


</body>

</html>