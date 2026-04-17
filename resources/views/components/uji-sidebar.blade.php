<aside class="w-64 h-full bg-teal-900 text-white flex-shrink-0 flex flex-col shadow-xl relative z-20">

    <!-- HEADER -->
    <div class="pt-8 pb-5 px-5 flex items-center justify-between border-b border-teal-800/30 mb-1">
        <div class="flex items-center gap-3">
            <!-- Icon Box -->
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-teal-400 to-teal-600 flex items-center justify-center text-white shadow-xl">
                <i class="fas fa-flask text-lg"></i>
            </div>

            <div>
                <h1 class="font-extrabold text-lg leading-none tracking-tight">
                    SI-LAB UJI
                </h1>
                <p class="text-[10px] uppercase font-bold text-teal-400 mt-1">
                    {{ Auth::user()->role == 'admin' ? 'Admin Unit' : 'Lembaga' }}
                </p>
            </div>
        </div>
        
        <!-- Tombol Close (Mobile) -->
        <button onclick="toggleSidebar()" class="md:hidden text-teal-300 hover:text-white transition-colors">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>

    <!-- MENU NAVIGASI -->
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1 scrollbar-thin scrollbar-thumb-teal-800 scrollbar-track-transparent">

        @if(Auth::user()->role == 'admin')
            <!-- ================= ADMIN ================= -->

            <!-- DASHBOARD -->
            <div class="px-3 mb-2 text-xs font-bold text-teal-400 uppercase tracking-widest">
                Dashboard
            </div>
            <a href="{{ url('/uji/dashboard') }}" 
               class="flex items-center gap-3 py-3.5 px-4 rounded-xl transition-all duration-200 group {{ request()->is('uji/dashboard*') ? 'bg-teal-800 text-white border-l-4 border-teal-400 shadow-md' : 'text-teal-200 hover:bg-teal-800/50 hover:text-white' }}">
                <i class="fas fa-home w-5 text-center"></i>
                <span class="text-sm font-medium">Dashboard</span>
            </a>

            <!-- DOKUMEN MASUK -->
            <div class="px-3 mt-4 mb-2 text-xs font-bold text-teal-400 uppercase tracking-widest">
                Dokumen Masuk
            </div>
            <a href="{{ url('/uji/history') }}" 
               class="flex items-center gap-3 py-3.5 px-4 rounded-xl transition-all duration-200 group {{ request()->is('uji/history*') ? 'bg-teal-800 text-white border-l-4 border-teal-400' : 'text-teal-200 hover:bg-teal-800/50 hover:text-white' }}">
                <i class="fas fa-check-circle w-5 text-center"></i>
                <span class="text-sm font-medium">Riwayat Disetujui</span>
            </a>

            <!-- PENETAPAN -->
            <div class="px-3 mt-4 mb-2 text-xs font-bold text-teal-400 uppercase tracking-widest">
                Penetapan
            </div>
            <a href="{{ route('uji.ktun_admin') }}" 
               class="flex items-center gap-3 py-3.5 px-4 rounded-xl transition-all duration-200 group {{ request()->routeIs('uji.ktun_admin') ? 'bg-teal-800 text-white border-l-4 border-teal-400' : 'text-teal-200 hover:bg-teal-800/50 hover:text-white' }}">
                <i class="fas fa-paper-plane w-5 text-center"></i>
                <span class="text-sm font-medium">Kirim KTUN</span>
            </a>

            <!-- AUDIT & MUTU -->
            <div class="px-3 mt-4 mb-2 text-xs font-bold text-teal-400 uppercase tracking-widest">
                Audit & Status Mutu
            </div>
            <a href="{{ route('survailen.uji.admin') }}" 
               class="flex items-center gap-3 py-3.5 px-4 rounded-xl transition-all duration-200 group {{ request()->routeIs('survailen.uji.admin') ? 'bg-teal-800 text-white border-l-4 border-teal-400' : 'text-teal-200 hover:bg-teal-800/50 hover:text-white' }}">
                <i class="fas fa-search-location w-5 text-center"></i>
                <span class="text-sm font-medium">Manajemen Survailen</span>
            </a>
            <a href="{{ route('uji.verifikasi_admin') }}" 
               class="flex items-center gap-3 py-3.5 px-4 rounded-xl transition-all duration-200 group {{ request()->routeIs('uji.verifikasi_admin') ? 'bg-teal-800 text-white border-l-4 border-teal-400' : 'text-teal-200 hover:bg-teal-800/50 hover:text-white' }}">
                <i class="fas fa-certificate w-5 text-center"></i>
                <span class="text-sm font-medium">Manajemen Verifikasi</span>
            </a>

            <!-- MASTER DATA -->
            <div class="px-3 mt-4 mb-2 text-xs font-bold text-teal-400 uppercase tracking-widest">
                Master Data
            </div>
            <a href="{{ route('lembaga.adminUji') }}" 
               class="flex items-center gap-3 py-3.5 px-4 rounded-xl transition-all duration-200 group {{ request()->routeIs('lembaga.adminUji') ? 'bg-teal-800 text-white border-l-4 border-teal-400' : 'text-teal-200 hover:bg-teal-800/50 hover:text-white' }}">
                <i class="fas fa-users w-5 text-center"></i>
                <span class="text-sm font-medium">Data Lembaga</span>
            </a>

        @else
            <!-- ================= USER (LEMBAGA) ================= -->

            <!-- UTAMA -->
            <div class="px-3 mb-2 text-xs font-bold text-teal-400 uppercase tracking-widest">
                Dashboard
            </div>
            <a href="{{ url('/uji/dashboard') }}" 
               class="flex items-center gap-3 py-3.5 px-4 rounded-xl transition-all duration-200 group {{ request()->is('uji/dashboard') ? 'bg-teal-800 text-white border-l-4 border-teal-400 shadow-md' : 'text-teal-200 hover:bg-teal-800/50 hover:text-white' }}">
                <i class="fas fa-home w-5 text-center"></i>
                <span class="text-sm font-medium">Dashboard Utama</span>
            </a>

            <!-- PENETAPAN -->
            <div class="px-3 mt-4 mb-2 text-xs font-bold text-teal-400 uppercase tracking-widest">
                Penetapan
            </div>
            <a href="{{ route('uji.ktun') }}" 
               class="flex items-center gap-3 py-3.5 px-4 rounded-xl transition-all duration-200 group {{ request()->routeIs('uji.ktun') ? 'bg-teal-800 text-white border-l-4 border-teal-400' : 'text-teal-200 hover:bg-teal-800/50 hover:text-white' }}">
                <i class="fas fa-file-signature w-5 text-center"></i>
                <span class="text-sm font-medium">Dokumen KTUN</span>
            </a>

            <!-- PELAPORAN -->
            <div class="px-3 mt-4 mb-2 text-xs font-bold text-teal-400 uppercase tracking-widest">
                Pelaporan
            </div>
            <a href="{{ url('/uji/laporan') }}" 
               class="flex items-center gap-3 py-3.5 px-4 rounded-xl transition-all duration-200 group {{ request()->is('uji/laporan*') ? 'bg-teal-800 text-white border-l-4 border-teal-400' : 'text-teal-200 hover:bg-teal-800/50 hover:text-white' }}">
                <i class="fas fa-file-invoice w-5 text-center"></i>
                <span class="text-sm font-medium">Laporan Tahunan</span>
            </a>

            <!-- STATUS MUTU -->
            <div class="px-3 mt-4 mb-2 text-xs font-bold text-teal-400 uppercase tracking-widest">
                Status Mutu
            </div>
            <a href="{{ url('/uji/survailen') }}" 
               class="flex items-center gap-3 py-3.5 px-4 rounded-xl transition-all duration-200 group {{ request()->is('uji/survailen*') ? 'bg-teal-800 text-white border-l-4 border-teal-400' : 'text-teal-200 hover:bg-teal-800/50 hover:text-white' }}">
                <i class="fas fa-search-location w-5 text-center"></i>
                <span class="text-sm font-medium">Survailen</span>
            </a>
            <a href="{{ route('uji.verifikasi') }}" 
               class="flex items-center gap-3 py-3.5 px-4 rounded-xl transition-all duration-200 group {{ request()->routeIs('uji.verifikasi') ? 'bg-teal-800 text-white border-l-4 border-teal-400' : 'text-teal-200 hover:bg-teal-800/50 hover:text-white' }}">
                <i class="fas fa-certificate w-5 text-center"></i>
                <span class="text-sm font-medium">Verifikasi</span>
            </a>
        @endif

    </nav>

    <!-- FOOTER / LOGOUT -->
    <div class="p-4 border-t border-teal-800 bg-teal-900">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" 
                    class="w-full flex items-center justify-center gap-2 bg-teal-800 hover:bg-rose-600 text-teal-100 hover:text-white py-2.5 rounded-lg text-xs font-bold uppercase tracking-wide transition-all duration-300 border border-teal-700 hover:border-rose-500 shadow-sm active:scale-95">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>
</aside>