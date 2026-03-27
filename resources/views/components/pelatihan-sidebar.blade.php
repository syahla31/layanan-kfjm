<aside class="w-64 h-full bg-blue-900 text-white flex-shrink-0 flex flex-col shadow-xl relative z-20">

    <!-- HEADER -->
    <div class="pt-8 pb-5 px-5 flex items-center justify-between border-b border-blue-800/30 mb-1">
        <div class="flex items-center gap-3">

            <div
                class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white shadow-xl">
                <i class="fas fa-chalkboard-teacher text-lg"></i>
            </div>

            <div>
                <h1 class="font-extrabold text-lg leading-none">
                    SI-PELATIHAN
                </h1>

                <p class="text-[10px] uppercase font-bold text-blue-400 mt-1">
                    {{ Auth::user()->role == 'admin' ? 'Admin Unit' : 'Lembaga' }}
                </p>
            </div>

        </div>
    </div>


    <!-- MENU -->
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">


        @if (Auth::user()->role == 'admin')
            <!-- ================= ADMIN ================= -->


            <!-- DASHBOARD -->
            <div class="px-3 mb-2 text-xs font-bold text-blue-400 uppercase tracking-widest">
                Dashboard
            </div>

            <a href="{{ url('/pelatihan/dashboard') }}"
                class="flex items-center gap-3 py-3.5 px-4 rounded-xl transition
{{ request()->is('pelatihan/dashboard*') ? 'bg-blue-800 text-white border-l-4 border-blue-400' : 'text-blue-200 hover:bg-blue-800/50' }}">

                <i class="fas fa-home w-5 text-center"></i>
                <span class="text-sm font-medium">Dashboard</span>

            </a>


            <!-- DOKUMEN MASUK -->
            <div class="px-3 mt-4 mb-2 text-xs font-bold text-blue-400 uppercase tracking-widest">
                Dokumen Masuk
            </div>

            <a href="{{ url('/pelatihan/history') }}"
                class="flex items-center gap-3 py-3.5 px-4 rounded-xl transition
{{ request()->is('pelatihan/history*') ? 'bg-blue-800 text-white border-l-4 border-blue-400' : 'text-blue-200 hover:bg-blue-800/50' }}">

                <i class="fas fa-check-circle w-5 text-center"></i>
                <span class="text-sm font-medium">Riwayat Disetujui</span>

            </a>


            <!-- AUDIT -->
            <div class="px-3 mt-4 mb-2 text-xs font-bold text-blue-400 uppercase tracking-widest">
                Audit & Penetapan
            </div>

            <a href="{{ route('survailen.admin') }}"
                class="flex items-center gap-3 py-3.5 px-4 rounded-xl transition
{{ request()->routeIs('survailen.admin') ? 'bg-blue-800 text-white border-l-4 border-blue-400' : 'text-blue-200 hover:bg-blue-800/50' }}">

                <i class="fas fa-clipboard-check w-5 text-center"></i>
                <span class="text-sm font-medium">Manajemen Survailen</span>

            </a>


            <a href="{{ route('verifikasi.admin') }}"
                class="flex items-center gap-3 py-3.5 px-4 rounded-xl transition
{{ request()->routeIs('verifikasi.admin') ? 'bg-blue-800 text-white border-l-4 border-blue-400' : 'text-blue-200 hover:bg-blue-800/50' }}">

                <i class="fas fa-certificate w-5 text-center"></i>
                <span class="text-sm font-medium">Manajemen Verifikasi</span>

            </a>


            <!-- KTUN -->
            <div class="px-3 mt-4 mb-2 text-xs font-bold text-blue-400 uppercase tracking-widest">
                Dokumen KTUN
            </div>

            <a href="{{ route('pelatihan.ktun_admin') }}"
                class="flex items-center gap-3 py-3.5 px-4 rounded-xl transition
{{ request()->routeIs('pelatihan.ktun_admin') ? 'bg-blue-800 text-white border-l-4 border-blue-400' : 'text-blue-200 hover:bg-blue-800/50' }}">

                <i class="fas fa-paper-plane w-5 text-center"></i>
                <span class="text-sm font-medium">Kirim Dokumen KTUN</span>

            </a>


            <!-- MASTER DATA -->
            <div class="px-3 mt-4 mb-2 text-xs font-bold text-blue-400 uppercase tracking-widest">
                Master Data
            </div>

            <a href="{{ route('lembaga.admin') }}"
                class="flex items-center gap-3 py-3.5 px-4 rounded-xl transition
{{ request()->routeIs('lembaga.admin') ? 'bg-blue-800 text-white border-l-4 border-blue-400' : 'text-blue-200 hover:bg-blue-800/50' }}">

                <i class="fas fa-building w-5 text-center"></i>
                <span class="text-sm font-medium">Data Lembaga</span>

            </a>
        @else
            <!-- ================= USER ================= -->


            <!-- DASHBOARD -->
            <div class="px-3 mb-2 text-xs font-bold text-blue-400 uppercase tracking-widest">
                Dashboard
            </div>

            <a href="{{ url('/pelatihan/dashboard') }}"
                class="flex items-center gap-3 py-3.5 px-4 rounded-xl transition
{{ request()->is('pelatihan/dashboard') ? 'bg-blue-800 text-white border-l-4 border-blue-400' : 'text-blue-200 hover:bg-blue-800/50' }}">

                <i class="fas fa-home w-5 text-center"></i>
                <span class="text-sm font-medium">Dashboard</span>

            </a>


            <!-- PERENCANAAN -->
            <div class="px-3 mt-4 mb-2 text-xs font-bold text-blue-400 uppercase tracking-widest">
                Perencanaan
            </div>

            <a href="{{ url('/pelatihan/kak') }}"
                class="flex items-center gap-3 py-3.5 px-4 rounded-xl transition
{{ request()->is('pelatihan/kak*') ? 'bg-blue-800 text-white border-l-4 border-blue-400' : 'text-blue-200 hover:bg-blue-800/50' }}">

                <i class="fas fa-project-diagram w-5 text-center"></i>
                <span class="text-sm font-medium">KAK</span>

            </a>


            <!-- PELAPORAN -->
            <div class="px-3 mt-4 mb-2 text-xs font-bold text-blue-400 uppercase tracking-widest">
                Pelaporan
            </div>

            <a href="{{ route('lapkin.index') }}"
                class="flex items-center gap-3 py-3.5 px-4 rounded-xl transition
{{ request()->routeIs('lapkin.*') ? 'bg-blue-800 text-white border-l-4 border-blue-400' : 'text-blue-200 hover:bg-blue-800/50' }}">

                <i class="fas fa-file-invoice w-5 text-center"></i>
                <span class="text-sm font-medium">Laporan Kinerja</span>

            </a>


            <!-- STATUS MUTU -->
            <div class="px-3 mt-4 mb-2 text-xs font-bold text-blue-400 uppercase tracking-widest">
                Status Mutu
            </div>

            <a href="{{ route('survailen.index') }}"
                class="flex items-center gap-3 py-3.5 px-4 rounded-xl transition
{{ request()->routeIs('survailen.index') ? 'bg-blue-800 text-white border-l-4 border-blue-400' : 'text-blue-200 hover:bg-blue-800/50' }}">

                <i class="fas fa-clipboard-list w-5 text-center"></i>
                <span class="text-sm font-medium">Survailen</span>

            </a>


            <a href="{{ route('verifikasi.index') }}"
                class="flex items-center gap-3 py-3.5 px-4 rounded-xl transition
{{ request()->routeIs('verifikasi.index') ? 'bg-blue-800 text-white border-l-4 border-blue-400' : 'text-blue-200 hover:bg-blue-800/50' }}">

                <i class="fas fa-certificate w-5 text-center"></i>
                <span class="text-sm font-medium">Verifikasi</span>

            </a>


            <!-- KTUN -->
            <div class="px-3 mt-4 mb-2 text-xs font-bold text-blue-400 uppercase tracking-widest">
                Dokumen KTUN
            </div>

            <a href="{{ route('pelatihan.ktun') }}"
                class="flex items-center gap-3 py-3.5 px-4 rounded-xl transition
{{ request()->routeIs('pelatihan.ktun') ? 'bg-blue-800 text-white border-l-4 border-blue-400' : 'text-blue-200 hover:bg-blue-800/50' }}">

                <i class="fas fa-file-signature w-5 text-center"></i>
                <span class="text-sm font-medium">Dokumen KTUN</span>

            </a>
        @endif


    </nav>


    <!-- LOGOUT -->
    <div class="p-4 border-t border-blue-800 bg-blue-900">

        <form action="{{ route('logout') }}" method="POST">
            @csrf

            <button type="submit"
                class="w-full flex items-center justify-center gap-2 bg-blue-800 hover:bg-rose-600 text-blue-100 hover:text-white py-2.5 rounded-lg text-xs font-bold uppercase tracking-wide transition">

                <i class="fas fa-sign-out-alt"></i>
                Logout

            </button>

        </form>

    </div>

</aside>