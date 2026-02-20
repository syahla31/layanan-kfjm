<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Sinar-X | SI-MUTU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 font-sans text-slate-800">

    <div class="flex h-screen overflow-hidden">
        
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-blue-900 shadow-2xl md:shadow-none transform -translate-x-full transition-transform duration-300 ease-in-out md:translate-x-0 md:static md:inset-auto flex flex-col h-full border-r border-blue-800/50">
            @include('components.uji-sidebar')
        </aside>


        <!-- KONTEN UTAMA -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden">
            
            <header class="bg-white shadow-sm h-16 flex items-center justify-between px-6 z-10">
                <h2 class="text-xl font-bold text-slate-800">Dashboard Rumah Sakit</h2>
                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-slate-700">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-slate-500">Pemohon</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-bold border border-orange-200">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 p-6">
                
                <!-- Alert Info -->
                <div class="bg-orange-50 border-l-4 border-orange-500 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-orange-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-orange-700">
                                Pengajuan amandemen sertifikat hanya dapat dilakukan jika sertifikat asli masih berlaku dan belum kadaluarsa.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form Cari Sertifikat -->
                <div class="bg-white p-8 rounded-lg shadow-sm border border-slate-200 max-w-2xl mx-auto mt-10">
                    <h3 class="font-bold text-xl text-center text-slate-800 mb-6">Cari Data Sertifikat</h3>
                    
                    <div class="flex gap-0 shadow-sm rounded-lg overflow-hidden border border-slate-300">
                        <div class="bg-gray-100 px-4 py-3 border-r border-slate-300 text-slate-500">
                            <i class="fas fa-search"></i>
                        </div>
                        <input type="text" placeholder="Masukkan Nomor Sertifikat (Contoh: UK-2024-001)" class="flex-1 px-4 py-3 focus:outline-none focus:bg-blue-50 transition-colors">
                        <button class="bg-orange-600 text-white px-6 py-3 font-medium hover:bg-orange-700 transition-colors">
                            Cari Data
                        </button>
                    </div>

                    <p class="text-center text-xs text-slate-400 mt-4">
                        Sistem akan otomatis memvalidasi data sertifikat dari database BAPETEN.
                    </p>
                </div>

            </main>
        </div>
    </div>
</body>
</html>