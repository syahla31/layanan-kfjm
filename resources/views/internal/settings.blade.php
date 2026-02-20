<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfigurasi | SI-MUTU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-100 text-slate-800">

    <div class="flex h-screen overflow-hidden">
        
        <!-- INCLUDE SIDEBAR (Menggantikan kode manual) -->
        @include('components.internal-sidebar')

        <!-- KONTEN UTAMA -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden">
            <header class="bg-white shadow-sm h-20 flex items-center justify-between px-8 z-10">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Konfigurasi Sistem</h2>
                    <p class="text-xs text-slate-500 mt-1">Pengaturan operasional aplikasi SI-MUTU</p>
                </div>
            </header>
            
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50/50 p-8">
                <div class="max-w-4xl mx-auto space-y-6">
                    
                    <!-- 1. PENGUMUMAN -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                        <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2"><i class="fas fa-bullhorn text-blue-500"></i> Pengumuman Dashboard</h3>
                        <form>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-600 mb-1">Isi Pengumuman</label>
                                <textarea rows="3" class="w-full border border-slate-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Contoh: Sistem akan maintenance pada tanggal..."></textarea>
                            </div>
                            <div class="flex items-center gap-4">
                                <label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" class="rounded text-blue-600 focus:ring-blue-500" checked> Tampilkan di Dashboard User</label>
                                <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-blue-700 ml-auto">Simpan</button>
                            </div>
                        </form>
                    </div>

                    <!-- 2. MODE MAINTENANCE -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                        <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2"><i class="fas fa-tools text-orange-500"></i> Mode Pemeliharaan</h3>
                        <div class="flex items-center justify-between p-4 bg-orange-50 rounded-lg border border-orange-100">
                            <div>
                                <p class="text-sm font-bold text-orange-800">Status Sistem: ONLINE</p>
                                <p class="text-xs text-orange-600">Jika dimatikan, hanya Admin yang bisa login.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" value="" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                            </label>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>
</body>
</html>