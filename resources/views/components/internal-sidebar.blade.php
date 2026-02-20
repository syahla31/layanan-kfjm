<aside class="w-72 bg-slate-900 text-white flex-shrink-0 flex flex-col shadow-2xl relative z-20">
    
    <!-- Header Logo -->
    <div class="h-20 flex items-center gap-4 px-6 border-b border-slate-800/50 bg-slate-900">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white shadow-lg shadow-red-900/20">
            <i class="fas fa-shield-alt text-lg"></i>
        </div>
        <div>
            <h1 class="font-bold text-lg tracking-wide text-slate-100">Panel Admin</h1>
            <p class="text-[10px] uppercase font-bold text-red-500 tracking-wider">Internal DKKN</p>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-1">
        
        <!-- Section Label -->
        <div class="px-3 mb-2 mt-2 text-xs font-bold text-slate-500 uppercase tracking-widest">
            Verifikasi User
        </div>

        <!-- 1. DASHBOARD / VERIFIKASI -->
        <a href="{{ url('/internal/dashboard') }}" 
           class="flex items-center gap-3 py-3 px-4 rounded-xl transition-all duration-200 group {{ request()->is('internal/dashboard') ? 'bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg shadow-red-900/20' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50' }}">
            <i class="fas fa-user-check w-5 text-center {{ request()->is('internal/dashboard') ? '' : 'group-hover:text-red-400' }}"></i>
            <span class="font-medium text-sm">Verifikasi Akun</span>
        </a>

        <!-- 2. DATA PENGGUNA -->
        <a href="{{ url('/internal/users') }}" 
           class="flex items-center gap-3 py-3 px-4 rounded-xl transition-all duration-200 group {{ request()->is('internal/users') ? 'bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg shadow-red-900/20' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50' }}">
            <i class="fas fa-users w-5 text-center {{ request()->is('internal/users') ? '' : 'group-hover:text-red-400' }}"></i>
            <span class="font-medium text-sm">Data Pengguna</span>
        </a>

        <div class="my-4 border-t border-slate-800/50 mx-3"></div>

        <!-- Section Label -->
        <div class="px-3 mb-2 text-xs font-bold text-slate-500 uppercase tracking-widest">
            Sistem
        </div>

        <!-- 3. KONFIGURASI -->
        <a href="{{ url('/internal/settings') }}" 
           class="flex items-center gap-3 py-3 px-4 rounded-xl transition-all duration-200 group {{ request()->is('internal/settings') ? 'bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg shadow-red-900/20' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50' }}">
            <i class="fas fa-cog w-5 text-center {{ request()->is('internal/settings') ? '' : 'group-hover:text-red-400' }}"></i>
            <span class="font-medium text-sm">Konfigurasi</span>
        </a>
        
        <!-- 4. LOG AKTIVITAS -->
        <a href="{{ url('/internal/logs') }}" 
           class="flex items-center gap-3 py-3 px-4 rounded-xl transition-all duration-200 group {{ request()->is('internal/logs') ? 'bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg shadow-red-900/20' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/50' }}">
            <i class="fas fa-database w-5 text-center {{ request()->is('internal/logs') ? '' : 'group-hover:text-red-400' }}"></i>
            <span class="font-medium text-sm">Log Aktivitas</span>
        </a>
    </nav>

    <!-- User Profile & Logout Footer -->
    <div class="p-4 border-t border-slate-800/50 bg-slate-900/50">
        <div class="flex items-center gap-3 mb-4 px-2">
            <div class="w-9 h-9 rounded-full bg-slate-700 flex items-center justify-center text-xs font-bold text-slate-300 border border-slate-600 shadow-sm">
                {{ substr(Auth::user()->name ?? 'U', 0, 2) }}
            </div>
            <div class="overflow-hidden">
                <p class="text-xs font-bold text-slate-200 truncate">{{ Auth::user()->name ?? 'Admin' }}</p>
                <p class="text-[10px] text-slate-500 truncate">{{ Auth::user()->email ?? '' }}</p>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 bg-slate-800 hover:bg-red-600 text-slate-300 hover:text-white py-2.5 rounded-lg text-xs font-bold uppercase tracking-wide transition-all duration-300 border border-slate-700 hover:border-red-500 group">
                <i class="fas fa-sign-out-alt group-hover:-translate-x-1 transition-transform"></i> Keluar
            </button>
        </form>
    </div>
</aside>