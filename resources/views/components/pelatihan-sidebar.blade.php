<aside class="w-64 h-full bg-blue-900 text-white flex-shrink-0 flex flex-col transition-all duration-300 shadow-xl relative z-20">
    
    <!-- Header Sidebar -->
    <div class="pt-8 pb-5 px-5 flex items-center justify-between border-b border-blue-800/30 mb-1">
        <div class="flex items-center gap-3">
            <!-- Ukuran ikon tetap ringkas -->
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white shadow-xl shadow-blue-950/40 transform transition-transform hover:scale-105 shrink-0">
                <i class="fas fa-chalkboard-teacher text-lg"></i>
            </div>
            
            <div class="space-y-0">
                <h1 class="font-extrabold text-lg tracking-tight leading-none text-white whitespace-nowrap">
                    SI-PELATIHAN
                </h1>
                <p class="text-[9px] uppercase font-black text-blue-400 tracking-[0.1em] opacity-80 mt-1">
                    {{ Auth::user()->role == 'admin' ? 'Admin Unit' : 'Lembaga' }}
                </p>
            </div>
        </div>

        <!-- Tombol Close (Mobile) -->
        <button onclick="toggleSidebar()" class="lg:hidden w-8 h-8 rounded-lg bg-blue-800/40 text-blue-200 hover:text-white hover:bg-blue-700/50 transition-all flex items-center justify-center shrink-0 ml-2">
            <i class="fas fa-times text-sm"></i>
        </button>
    </div>

    <!-- Menu Navigasi -->
    <nav class="flex-1 overflow-y-auto py-3 px-3 space-y-1 scrollbar-thin scrollbar-thumb-blue-800 scrollbar-track-transparent">
        
        @if(Auth::user()->role == 'admin')
            <!-- === MENU KHUSUS ADMIN === -->
            
            <!-- 1. INBOX -->
            <div class="px-3 mb-2 text-xs font-bold text-blue-400 uppercase tracking-widest">Dokumen Masuk</div>
            
            <a href="{{ url('/pelatihan/dashboard') }}" class="flex items-center gap-3 py-3 px-4 rounded-xl transition-all duration-200 group {{ request()->is('pelatihan/dashboard*') ? 'bg-blue-800 text-white shadow-md' : 'text-blue-200 hover:text-white hover:bg-blue-800/50' }}">
                <i class="fas fa-inbox w-5 text-center"></i>
                <span class="font-medium text-sm">Verifikasi Dokumen</span>
            </a>
            
            <a href="{{ url('/pelatihan/history') }}" class="flex items-center gap-3 py-3 px-4 rounded-xl transition-all duration-200 group {{ request()->is('pelatihan/history*') ? 'bg-blue-800 text-white shadow-md' : 'text-blue-200 hover:text-white hover:bg-blue-800/50' }}">
                <i class="fas fa-check-circle w-5 text-center"></i>
                <span class="font-medium text-sm">Riwayat Disetujui</span>
            </a>

            <!-- 2. OUTBOX -->
            <div class="px-3 mb-2 text-xs font-bold text-blue-400 uppercase tracking-widest mt-4">Audit & Status Mutu</div>

            <a href="{{ route('survailen.admin') }}" class="flex items-center gap-3 py-3 px-4 rounded-xl transition-all duration-200 group {{ request()->routeIs('survailen.admin') ? 'bg-blue-800 text-white shadow-md' : 'text-blue-200 hover:text-white hover:bg-blue-800/50' }}">
                <i class="fas fa-search-location w-5 text-center"></i>
                <span class="font-medium text-sm">Manajemen Survailen</span>
            </a>

            <a href="{{ route('verifikasi.admin') }}" class="flex items-center gap-3 py-3 px-4 rounded-xl transition-all duration-200 group {{ request()->routeIs('verifikasi.admin') ? 'bg-blue-800 text-white shadow-md' : 'text-blue-200 hover:text-white hover:bg-blue-800/50' }}">
                <i class="fas fa-certificate w-5 text-center"></i>
                <span class="font-medium text-sm">Manajemen Verifikasi</span>
            </a>

            <!-- 3. MASTER DATA -->
            <div class="my-4 border-t border-blue-800/50 mx-3"></div>
            <div class="px-3 mb-2 text-xs font-bold text-blue-400 uppercase tracking-widest">Master Data</div>

            <a href="{{ route('lembaga.admin') }}" class="flex items-center gap-3 py-3 px-4 rounded-xl transition-all duration-200 group {{ request()->routeIs('lembaga.admin') ? 'bg-blue-800 text-white shadow-md' : 'text-blue-200 hover:text-white hover:bg-blue-800/50' }}">
                <i class="fas fa-users w-5 text-center"></i>
                <span class="font-medium text-sm">Data Lembaga</span>
            </a>

        @else
            <!-- === MENU KHUSUS USER (LEMBAGA) === -->
            
            <!-- 0. DASHBOARD -->
            <div class="px-3 mb-2 text-xs font-bold text-blue-400 uppercase tracking-widest">Utama</div>
            <a href="{{ url('/pelatihan/dashboard') }}" class="flex items-center gap-3 py-3 px-4 rounded-xl transition-all duration-200 group {{ request()->is('pelatihan/dashboard') ? 'bg-blue-800 text-white shadow-md' : 'text-blue-200 hover:text-white hover:bg-blue-800/50' }}">
                <i class="fas fa-home w-5 text-center group-hover:text-blue-300 transition-colors"></i>
                <span class="font-medium text-sm">Dashboard</span>
            </a>

            <!-- 1. KAK -->
            <div class="px-3 mb-2 text-xs font-bold text-blue-400 uppercase tracking-widest mt-4">Perencanaan</div>
            <a href="{{ url('/pelatihan/kak') }}" class="flex items-center gap-3 py-3 px-4 rounded-xl transition-all duration-200 group {{ request()->is('pelatihan/kak*') ? 'bg-blue-800 text-white shadow-md' : 'text-blue-200 hover:text-white hover:bg-blue-800/50' }}">
                <i class="fas fa-project-diagram w-5 text-center group-hover:text-blue-300 transition-colors"></i>
                <span class="font-medium text-sm">KAK</span>
            </a>

            <!-- 2. LAPORAN KINERJA -->
            <div class="px-3 mb-2 text-xs font-bold text-blue-400 uppercase tracking-widest mt-4">Pelaporan</div>
            <a href="{{ route('lapkin.index') }}" class="flex items-center gap-3 py-3 px-4 rounded-xl transition-all duration-200 group {{ request()->routeIs('lapkin.*') ? 'bg-blue-800 text-white shadow-md' : 'text-blue-200 hover:text-white hover:bg-blue-800/50' }}">
                <i class="fas fa-file-invoice w-5 text-center group-hover:text-blue-300 transition-colors"></i>
                <span class="font-medium text-sm">Laporan Kinerja</span>
            </a>

            <!-- 3. STATUS MUTU -->
            <div class="px-3 mb-2 text-xs font-bold text-blue-400 uppercase tracking-widest mt-4">Status Mutu</div>
            
            <a href="{{ route('survailen.index') }}" class="flex items-center gap-3 py-3 px-4 rounded-xl transition-all duration-200 group {{ request()->routeIs('survailen.index') ? 'bg-blue-800 text-white shadow-md' : 'text-blue-200 hover:text-white hover:bg-blue-800/50' }}">
                <i class="fas fa-clipboard-list w-5 text-center group-hover:text-blue-300 transition-colors"></i>
                <span class="font-medium text-sm">Survailen</span>
            </a>

            <a href="{{ route('verifikasi.index') }}" class="flex items-center gap-3 py-3 px-4 rounded-xl transition-all duration-200 group {{ request()->routeIs('verifikasi.index') ? 'bg-blue-800 text-white shadow-md' : 'text-blue-200 hover:text-white hover:bg-blue-800/50' }}">
                <i class="fas fa-certificate w-5 text-center group-hover:text-blue-300 transition-colors"></i>
                <span class="font-medium text-sm">Verifikasi</span>
            </a>
        @endif

    </nav>

    <!-- Footer Sidebar / Logout -->
    <div class="p-4 border-t border-blue-800 bg-blue-900">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 bg-blue-800 hover:bg-rose-600 text-blue-100 hover:text-white py-2.5 rounded-lg text-xs font-bold uppercase tracking-wide transition-all duration-300 border border-blue-700 hover:border-rose-500 shadow-sm hover:shadow-md">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>
</aside>