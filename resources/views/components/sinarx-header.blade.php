<header class="bg-white border-b border-slate-100 h-20 md:h-24 flex items-center justify-between px-6 md:px-10 shrink-0 z-40 sticky top-0 shadow-sm">
    <div class="flex items-center gap-4 md:gap-6">
        <!-- TOMBOL BURGER MOBILE -->
        <button onclick="toggleSidebar()" class="md:hidden w-11 h-11 flex items-center justify-center rounded-2xl bg-slate-50 text-slate-500 hover:bg-orange-50 hover:text-orange-600 border border-slate-200 transition-all active:scale-90">
            <i class="fas fa-bars text-lg"></i>
        </button>
        
        <div class="flex flex-col">
            <h2 class="text-base md:text-xl font-black text-slate-800 tracking-tight leading-none">{{ $title ?? 'Dashboard' }}</h2>
            <p class="text-[10px] md:text-xs text-slate-400 font-bold uppercase tracking-widest mt-1.5 hidden sm:block">
                {{ $subtitle ?? 'Sistem Informasi Mutu Sinar-X' }}
            </p>
        </div>
    </div>
    
    <div class="flex items-center gap-3 md:gap-4">
        <!-- USER INFO -->
        <div class="text-right hidden sm:block">
            <p class="text-xs md:text-sm font-black text-slate-800 leading-none">{{ Auth::user()->name }}</p>
            <p class="text-[9px] md:text-[10px] text-orange-600 mt-1 uppercase font-black tracking-widest">
                {{ Auth::user()->role == 'admin' ? 'Admin Pusat' : 'Layanan Unit' }}
            </p>
        </div>
        <!-- AVATAR -->
        <div class="w-10 h-10 md:w-12 md:h-12 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center text-white font-black text-sm md:text-lg border-2 border-white shadow-xl shadow-orange-500/20 uppercase shrink-0 transform hover:rotate-3 transition-transform cursor-default">
            {{ substr(Auth::user()->name, 0, 1) }}
        </div>
    </div>
</header>