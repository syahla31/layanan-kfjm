<aside class="w-64 h-full bg-teal-900 text-white flex-shrink-0 flex flex-col transition-all duration-300 shadow-xl relative z-20">
    
    <!-- Header Sidebar -->
    <div class="h-20 flex items-center justify-between px-6 border-b border-teal-800">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-teal-400 to-teal-600 flex items-center justify-center text-white shadow-lg shadow-teal-900/20">
                <i class="fas fa-flask text-lg"></i>
            </div>
            <div class="text-left">
                <h1 class="font-bold text-lg tracking-wide leading-tight text-left">SI-LAB UJI</h1>
                <p class="text-[10px] uppercase font-bold text-teal-300 tracking-wider text-left">
                    {{ Auth::user()->role == 'admin' ? 'Admin Unit' : 'Lembaga' }}
                </p>
            </div>
        </div>

        <!-- Tombol Close (Hanya Tampil di Mobile) -->
        <button onclick="toggleSidebar()" class="md:hidden text-teal-300 hover:text-white transition-colors p-1">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>

    <!-- Menu Navigasi -->
    <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-1 scrollbar-thin scrollbar-thumb-teal-800 scrollbar-track-transparent text-left">
        
        @if(Auth::user()->role == 'admin')
            <!-- === MENU KHUSUS ADMIN === -->
            
            <div class="px-3 mb-2 text-xs font-bold text-teal-400 uppercase tracking-widest text-left">Dokumen Masuk</div>
            
            <a href="{{ url('/uji/dashboard') }}" class="flex items-center gap-3 py-3 px-4 rounded-xl transition-all duration-200 group {{ request()->is('uji/dashboard*') ? 'bg-teal-800 text-white shadow-md' : 'text-teal-200 hover:text-white hover:bg-teal-800/50' }}">
                <i class="fas fa-inbox w-5 text-center"></i>
                <span class="font-medium text-sm text-left">Verifikasi Laporan</span>
            </a>
            
            <a href="{{ url('/uji/history') }}" class="flex items-center gap-3 py-3 px-4 rounded-xl transition-all duration-200 group {{ request()->is('uji/history*') ? 'bg-teal-800 text-white shadow-md' : 'text-teal-200 hover:text-white hover:bg-teal-800/50' }}">
                <i class="fas fa-check-circle w-5 text-center"></i>
                <span class="font-medium text-sm text-left">Riwayat Disetujui</span>
            </a>

            <!-- MENU PENETAPAN ADMIN -->
            <div class="px-3 mb-2 text-xs font-bold text-teal-400 uppercase tracking-widest mt-4 text-left">Penetapan</div>

            <a href="{{ route('uji.ktun_admin') }}" class="flex items-center gap-3 py-3 px-4 rounded-xl transition-all duration-200 group {{ request()->routeIs('uji.ktun_admin') ? 'bg-teal-800 text-white shadow-md' : 'text-teal-200 hover:text-white hover:bg-teal-800/50' }}">
                <i class="fas fa-paper-plane w-5 text-center {{ request()->routeIs('uji.ktun_admin') ? 'text-teal-300' : 'group-hover:text-teal-300' }}"></i>
                <span class="font-medium text-sm text-left">Kirim KTUN</span>
            </a>

            <div class="px-3 mb-2 text-xs font-bold text-teal-400 uppercase tracking-widest mt-4 text-left">Audit & Status Mutu</div>

            <a href="{{ route('survailen.uji.admin') }}" class="flex items-center gap-3 py-3 px-4 rounded-xl transition-all duration-200 group {{ request()->routeIs('survailen.uji.admin') ? 'bg-teal-800 text-white shadow-md' : 'text-teal-200 hover:text-white hover:bg-teal-800/50' }}">
                <i class="fas fa-search-location w-5 text-center"></i>
                <span class="font-medium text-sm text-left">Manajemen Survailen</span>
            </a>

            <a href="{{ route('uji.verifikasi_admin') }}" class="flex items-center gap-3 py-3 px-4 rounded-xl transition-all duration-200 group {{ request()->routeIs('uji.verifikasi_admin') ? 'bg-teal-800 text-white shadow-md' : 'text-teal-200 hover:text-white hover:bg-teal-800/50' }}">
                <i class="fas fa-certificate w-5 text-center"></i>
                <span class="font-medium text-sm text-left">Manajemen Verifikasi</span>
            </a>

            <div class="my-4 border-t border-teal-800/50 mx-3"></div>
            <div class="px-3 mb-2 text-xs font-bold text-teal-400 uppercase tracking-widest">Master Data</div>

            <a href="{{ route('lembaga.adminUji') }}" class="flex items-center gap-3 py-3 px-4 rounded-xl transition-all duration-200 group {{ request()->routeIs('lembaga.adminUji') ? 'bg-teal-800 text-white shadow-md' : 'text-teal-200 hover:text-white hover:bg-teal-800/50' }}">
                <i class="fas fa-users w-5 text-center"></i>
                <span class="font-medium text-sm">Data Lembaga</span>
            </a>

        @else
            <!-- === MENU KHUSUS USER (LEMBAGA) === -->
            
            <div class="px-3 mb-2 text-xs font-bold text-teal-400 uppercase tracking-widest text-left">Utama</div>
            
            <!-- Dashboard Ringkasan -->
            <a href="{{ url('/uji/dashboard') }}" class="flex items-center gap-3 py-3 px-4 rounded-xl transition-all duration-200 group {{ request()->routeIs('uji.dashboard') ? 'bg-teal-800 text-white shadow-md' : 'text-teal-200 hover:text-white hover:bg-teal-800/50' }}">
                <i class="fas fa-chart-pie w-5 text-center group-hover:text-teal-300 transition-colors"></i>
                <span class="font-medium text-sm text-left">Dashboard Utama</span>
            </a>

            <!-- MENU PENETAPAN USER -->
            <div class="px-3 mb-2 text-xs font-bold text-teal-400 uppercase tracking-widest mt-4 text-left">Penetapan</div>

            <a href="{{ route('uji.ktun') }}" class="flex items-center gap-3 py-3 px-4 rounded-xl transition-all duration-200 group {{ request()->routeIs('uji.ktun') ? 'bg-teal-800 text-white shadow-md' : 'text-teal-200 hover:text-white hover:bg-teal-800/50' }}">
                <i class="fas fa-gift w-5 text-center {{ request()->routeIs('uji.ktun') ? 'text-teal-300' : 'group-hover:text-teal-300' }}"></i>
                <span class="font-medium text-sm text-left">Dokumen KTUN</span>
            </a>

            <div class="px-3 mb-2 text-xs font-bold text-teal-400 uppercase tracking-widest mt-4 text-left">Pelaporan</div>
            
            <!-- Halaman Manajemen Laporan Tahunan -->
            <a href="{{ url('/uji/laporan') }}" class="flex items-center gap-3 py-3 px-4 rounded-xl transition-all duration-200 group {{ request()->routeIs('uji.laporan') ? 'bg-teal-800 text-white shadow-md' : 'text-teal-200 hover:text-white hover:bg-teal-800/50' }}">
                <i class="fas fa-file-invoice w-5 text-center group-hover:text-teal-300 transition-colors text-left"></i>
                <span class="font-medium text-sm text-left">Laporan Tahunan</span>
            </a>

            <div class="px-3 mb-2 text-xs font-bold text-teal-400 uppercase tracking-widest mt-4 text-left">Status Mutu</div>
            
            <!-- Survailen User -->
            <a href="{{ url('/uji/survailen') }}" class="flex items-center gap-3 py-3 px-4 rounded-xl transition-all duration-200 group {{ request()->is('uji/survailen*') ? 'bg-teal-800 text-white shadow-md' : 'text-teal-200 hover:text-white hover:bg-teal-800/50' }}">
                <i class="fas fa-search-location w-5 text-center group-hover:text-teal-300 transition-colors"></i>
                <span class="font-medium text-sm text-left">Survailen</span>
            </a>

            <a href="{{ route('uji.verifikasi') }}" class="flex items-center gap-3 py-3 px-4 rounded-xl transition-all duration-200 group {{ request()->routeIs('uji.verifikasi') ? 'bg-teal-800 text-white shadow-md' : 'text-teal-200 hover:text-white hover:bg-teal-800/50' }}">
                <i class="fas fa-certificate w-5 text-center group-hover:text-teal-300 transition-colors text-left"></i>
                <span class="font-medium text-sm text-left">Verifikasi</span>
            </a>
        @endif

    </nav>

    <!-- Footer Sidebar / Logout -->
    <div class="p-4 border-t border-teal-800 bg-teal-900">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 bg-teal-800 hover:bg-rose-600 text-teal-100 hover:text-white py-2.5 rounded-lg text-xs font-bold uppercase tracking-wide transition-all duration-300 border border-teal-700 hover:border-rose-500 shadow-sm hover:shadow-md active:scale-95">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>
</aside>