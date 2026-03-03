<aside id="mainSidebar" class="w-64 h-full bg-orange-900 text-white flex-shrink-0 flex flex-col transition-all duration-300 shadow-xl relative z-20">
    
    <!-- Header Sidebar -->
    <div class="h-20 flex items-center justify-between px-6 border-b border-orange-800">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white shadow-lg shadow-orange-900/20 transform transition-transform hover:scale-105 shrink-0">
                <i class="fas fa-radiation text-lg animate-pulse-slow"></i>
            </div>
            <div class="text-left space-y-0">
                <h1 class="font-extrabold text-lg tracking-tight leading-none text-white whitespace-nowrap">
                    SI-SINAR X
                </h1>
                <p class="text-[9px] uppercase font-black text-orange-400 tracking-[0.1em] opacity-80 mt-1">
                    {{ Auth::user()->role == 'admin' ? 'Pusat Kendali' : 'Unit Layanan' }}
                </p>
            </div>
        </div>

        <!-- Tombol Close (Mobile) -->
        <button onclick="toggleSidebar()" class="md:hidden text-orange-300 hover:text-white transition-colors p-1 shrink-0">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>

    <!-- Menu Navigasi -->
    <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-1 scrollbar-thin scrollbar-thumb-orange-800 scrollbar-track-transparent text-left">
        
        @if (Auth::user()->role == 'admin')
            <!-- === MENU KHUSUS ADMIN === -->
            
            <div class="px-3 mb-2 text-xs font-bold text-orange-400 uppercase tracking-widest text-left">Validasi Data</div>
            
            <a href="{{ url('/sinarx/dashboard') }}" class="flex items-center gap-3 py-3 px-4 rounded-xl transition-all duration-200 group {{ request()->is('sinarx/dashboard*') && !request()->routeIs('lembaga*') ? 'bg-orange-800 text-white shadow-md' : 'text-orange-200 hover:text-white hover:bg-orange-800/50' }}">
                <i class="fas fa-file-signature w-5 text-center group-hover:text-orange-300 transition-colors"></i>
                <span class="font-medium text-sm text-left">Permohonan Masuk</span>
                @if(isset($pendingCount) && $pendingCount > 0)
                    <span class="ml-auto bg-orange-500 text-white text-[10px] px-2 py-0.5 rounded-full font-black shadow-sm">{{ $pendingCount }}</span>
                @endif
            </a>

            <div class="my-4 border-t border-orange-800/50 mx-3"></div>
            <div class="px-3 mb-2 text-xs font-bold text-orange-400 uppercase tracking-widest text-left mt-4">Manajemen</div>

            <a href="{{ route('lembaga.adminSinarx') }}" class="flex items-center gap-3 py-3 px-4 rounded-xl transition-all duration-200 group {{ request()->routeIs('lembaga.adminSinarx') ? 'bg-orange-800 text-white shadow-md' : 'text-orange-200 hover:text-white hover:bg-orange-800/50' }}">
                <i class="fas fa-hospital-user w-5 text-center group-hover:text-orange-300 transition-colors"></i>
                <span class="font-medium text-sm text-left">Data Lembaga</span>
            </a>

        @else
            <!-- === MENU KHUSUS USER === -->
            
            <div class="px-3 mb-2 text-xs font-bold text-orange-400 uppercase tracking-widest text-left">Navigasi Utama</div>
            
            <a href="{{ url('/sinarx/dashboard') }}" class="flex items-center gap-3 py-3 px-4 rounded-xl transition-all duration-200 group {{ request()->is('sinarx/dashboard') ? 'bg-orange-800 text-white shadow-md' : 'text-orange-200 hover:text-white hover:bg-orange-800/50' }}">
                <i class="fas fa-home w-5 text-center group-hover:text-orange-300 transition-colors"></i>
                <span class="font-medium text-sm text-left">Dashboard Unit</span>
            </a>
        @endif

    </nav>

    <!-- Footer Sidebar / Logout -->
    <div class="p-4 border-t border-orange-800 bg-orange-900">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 bg-orange-800 hover:bg-rose-600 text-orange-100 hover:text-white py-2.5 rounded-lg text-xs font-bold uppercase tracking-wide transition-all duration-300 border border-orange-700 hover:border-rose-500 shadow-sm hover:shadow-md active:scale-95">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>
</aside>

<!-- Styling Khusus untuk ikon radiasi -->
<style>
    .animate-pulse-slow { animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
    @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: .6; } }
</style>