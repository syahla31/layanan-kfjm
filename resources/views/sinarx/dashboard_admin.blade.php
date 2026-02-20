<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel Sinar-X | SI-MUTU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans text-slate-800">

    <div class="flex h-screen overflow-hidden">
        
        <!-- SIDEBAR ADMIN (Oranye Gelap) -->
        <aside class="w-64 bg-slate-900 text-white flex-shrink-0 flex flex-col transition-all duration-300">
            <div class="p-6 flex items-center gap-3 border-b border-slate-800">
                <i class="fas fa-radiation-alt text-2xl text-orange-400"></i>
                <div>
                    <h1 class="font-bold text-lg tracking-wide">ADMIN DKKN</h1>
                    <p class="text-xs text-orange-400">Unit Sinar-X</p>
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto py-4">
                <p class="px-6 text-xs font-semibold text-slate-500 uppercase mb-2">Amandemen</p>
                
                <a href="#" class="flex items-center py-3 px-6 bg-orange-900 border-r-4 border-orange-500 text-white">
                    <i class="fas fa-file-contract w-6"></i>
                    <span>Permohonan Baru</span>
                </a>
                <a href="#" class="flex items-center py-3 px-6 text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                    <i class="fas fa-archive w-6"></i>
                    <span>Arsip Sertifikat</span>
                </a>
            </nav>

            <div class="p-4 border-t border-slate-800">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white py-2 rounded-md text-sm transition-colors">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- KONTEN UTAMA -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden">
            
            <header class="bg-white shadow-sm h-16 flex items-center justify-between px-6 z-10 border-b border-orange-100">
                <h2 class="text-xl font-bold text-slate-800">Validasi Amandemen Sertifikat</h2>
                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-slate-700">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-slate-500">Validator Sertifikat</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-orange-600 flex items-center justify-center text-white font-bold">
                        S
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                        <h3 class="font-bold text-gray-700">Daftar Permohonan Amandemen</h3>
                        <div class="flex gap-2">
                            <input type="text" placeholder="Cari No. Sertifikat..." class="text-xs border border-gray-300 rounded px-2 py-1">
                        </div>
                    </div>
                    
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                            <tr>
                                <th class="px-6 py-3">Pemohon</th>
                                <th class="px-6 py-3">No. Sertifikat Lama</th>
                                <th class="px-6 py-3">Alasan Perubahan</th>
                                <th class="px-6 py-3">Bukti</th>
                                <th class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-white border-b hover:bg-orange-50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900">RS Sehat Sentosa</td>
                                <td class="px-6 py-4">UK-2024-999</td>
                                <td class="px-6 py-4 truncate max-w-xs">Kesalahan penulisan nama instansi pada sertifikat asli.</td>
                                <td class="px-6 py-4 text-blue-600 underline cursor-pointer">Bukti_Scan.pdf</td>
                                <td class="px-6 py-4 text-center flex justify-center gap-1">
                                    <button class="bg-green-500 text-white p-2 rounded hover:bg-green-600" title="Setujui"><i class="fas fa-check"></i></button>
                                    <button class="bg-red-500 text-white p-2 rounded hover:bg-red-600" title="Tolak"><i class="fas fa-times"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </main>
        </div>
    </div>
</body>
</html>