<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 text-white transition-transform duration-300 transform lg:relative lg:translate-x-0 flex flex-col shadow-2xl">

    <!-- Header Sidebar -->
    <div class="h-20 flex items-center gap-4 px-6 border-b border-slate-800/50">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white shadow-lg shadow-red-900/20 shrink-0">
            <i class="fas fa-shield-alt text-lg"></i>
        </div>
        <div>
            <h1 class="font-bold text-lg tracking-wide text-slate-100 whitespace-nowrap">Panel Admin</h1>
            <p class="text-[10px] uppercase font-bold text-red-500 tracking-wider">Internal DKKN</p>
        </div>
        <!-- Tombol Close (Mobile) -->
        <button @click="sidebarOpen = false" class="lg:hidden ml-auto text-slate-400 hover:text-white transition-colors">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>

    <!-- Menu Navigasi -->
    <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-1 scrollbar-thin scrollbar-thumb-slate-800 scrollbar-track-transparent">
        
        <div class="px-3 mb-2 text-xs font-bold text-slate-500 uppercase tracking-widest">Verifikasi User</div>

        <!-- Menu Verifikasi Akun -->
        <a href="{{ url('/internal/dashboard') }}"
            class="flex items-center gap-3 py-3 px-4 rounded-xl transition-all duration-200 group {{ request()->is('internal/dashboard') ? 'bg-red-600 text-white shadow-lg' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}">
            <i class="fas fa-user-check w-5 text-center group-hover:scale-110 transition-transform"></i>
            <span class="font-medium text-sm">Verifikasi Akun</span>
        </a>

        <!-- Menu Data Pengguna -->
        <a href="{{ url('/internal/users') }}"
            class="flex items-center gap-3 py-3 px-4 rounded-xl transition-all duration-200 group {{ request()->is('internal/users') ? 'bg-red-600 text-white shadow-lg' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}">
            <i class="fas fa-users w-5 text-center group-hover:scale-110 transition-transform"></i>
            <span class="font-medium text-sm">Data Pengguna</span>
        </a>

    </nav>

    <!-- Footer Sidebar / Logout -->
    <div class="p-4 border-t border-slate-800/50 bg-slate-900/50">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                class="w-full flex items-center justify-center gap-2 bg-slate-800 hover:bg-red-600 text-white py-2.5 rounded-lg text-xs font-bold uppercase tracking-widest transition-all duration-300 shadow-sm active:scale-95">
                <i class="fas fa-sign-out-alt"></i> KELUAR
            </button>
        </form>
    </div>
</aside>