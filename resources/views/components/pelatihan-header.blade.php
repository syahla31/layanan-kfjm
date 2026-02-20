<header class="bg-white shadow-sm h-20 flex items-center justify-between px-8 z-10 border-b border-blue-100">
    <div>
        <!-- Judul Halaman Dinamis -->
        <h2 class="text-xl font-bold text-slate-800">{{ $title ?? 'Dashboard' }}</h2>
        <p class="text-xs text-slate-500 mt-1">{{ $subtitle ?? 'Selamat datang di panel layanan pelatihan' }}</p>
    </div>
    
    <div class="flex items-center gap-3">
        <div class="text-right hidden sm:block">
            <p class="text-sm font-bold text-slate-700">{{ Auth::user()->name }}</p>
            <p class="text-xs text-slate-500">{{ Auth::user()->email }}</p>
        </div>
        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold border border-blue-200 shadow-sm">
            {{ substr(Auth::user()->name, 0, 1) }}
        </div>
    </div>
</header>