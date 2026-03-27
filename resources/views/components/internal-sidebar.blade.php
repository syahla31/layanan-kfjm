<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 text-white transition-transform duration-300 transform lg:relative lg:translate-x-0 flex flex-col shadow-2xl">

    <div class="h-20 flex items-center gap-4 px-6 border-b border-slate-800/50">
        <div
            class="w-10 h-10 rounded-xl bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white shadow-lg shadow-red-900/20">
            <i class="fas fa-shield-alt text-lg"></i>
        </div>
        <div>
            <h1 class="font-bold text-lg tracking-wide text-slate-100">Panel Admin</h1>
            <p class="text-[10px] uppercase font-bold text-red-500 tracking-wider">Internal DKKN</p>
        </div>
        <button @click="sidebarOpen = false" class="lg:hidden ml-auto text-slate-400">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>

    <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-1">
        <div class="px-3 mb-2 text-xs font-bold text-slate-500 uppercase tracking-widest">Verifikasi User</div>

        <a href="{{ url('/internal/dashboard') }}"
            class="flex items-center gap-3 py-3 px-4 rounded-xl transition-all {{ request()->is('internal/dashboard') ? 'bg-red-600 text-white shadow-lg' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}">
            <i class="fas fa-user-check w-5 text-center"></i>
            <span class="font-medium text-sm">Verifikasi Akun</span>
        </a>

        <a href="{{ url('/internal/users') }}"
            class="flex items-center gap-3 py-3 px-4 rounded-xl transition-all {{ request()->is('internal/users') ? 'bg-red-600 text-white shadow-lg' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}">
            <i class="fas fa-users w-5 text-center"></i>
            <span class="font-medium text-sm">Data Pengguna</span>
        </a>

        {{-- <div class="my-4 border-t border-slate-800/50 mx-3"></div>
        <div class="px-3 mb-2 text-xs font-bold text-slate-500 uppercase tracking-widest">Sistem</div>

        <a href="{{ url('/internal/settings') }}"
            class="flex items-center gap-3 py-3 px-4 rounded-xl transition-all {{ request()->is('internal/settings') ? 'bg-red-600 text-white shadow-lg' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}">
            <i class="fas fa-cog w-5 text-center"></i>
            <span class="font-medium text-sm">Konfigurasi</span>
        </a>

        <a href="{{ url('/internal/logs') }}"
            class="flex items-center gap-3 py-3 px-4 rounded-xl transition-all {{ request()->is('internal/logs') ? 'bg-red-600 text-white shadow-lg' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}">
            <i class="fas fa-database w-5 text-center"></i>
            <span class="font-medium text-sm">Log Aktivitas</span>
        </a> --}}
    </nav>

    <div class="p-4 border-t border-slate-800/50">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button
                class="w-full flex items-center justify-center gap-2 bg-slate-800 hover:bg-red-600 text-white py-2.5 rounded-lg text-xs font-bold transition-all">
                <i class="fas fa-sign-out-alt"></i> KELUAR
            </button>
        </form>
    </div>
</aside>