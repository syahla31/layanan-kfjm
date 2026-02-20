<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin | SI-MUTU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-100 text-slate-800">

    @php
        use App\Models\User;
        
        if(!isset($pendingUsers)) {
            $pendingUsers = User::where('status', 'pending')->orderBy('created_at', 'desc')->get();
        }

        if(!isset($historyUsers)) {
            $historyUsers = User::where('status', 'active')->where('role', '!=', 'admin')->orderBy('updated_at', 'desc')->take(5)->get();
        }
    @endphp

    <div class="flex h-screen overflow-hidden">
        
        <!-- INCLUDE SIDEBAR -->
        @include('components.internal-sidebar')

        <!-- KONTEN UTAMA -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden">
            
            <header class="bg-white shadow-sm h-20 flex items-center justify-between px-8 z-10">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Verifikasi Pendaftaran Baru</h2>
                    <p class="text-xs text-slate-500 mt-1">Kelola persetujuan akun pengguna eksternal</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 border border-slate-200">
                        <i class="far fa-bell"></i>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50/50 p-8">
                <!-- Konten tabel tetap sama seperti sebelumnya, tidak saya ubah -->
                @if (session('success'))
                    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r shadow-sm">
                        <p class="font-bold text-sm">Sukses!</p><p class="text-sm">{{ session('success') }}</p>
                    </div>
                @endif

                <div class="bg-blue-50 border border-blue-100 p-5 mb-8 rounded-xl flex items-start gap-4 shadow-sm">
                    <div class="bg-blue-100 text-blue-600 p-2 rounded-lg"><i class="fas fa-info-circle text-xl"></i></div>
                    <div><h3 class="font-bold text-blue-900">Petunjuk Verifikasi</h3><p class="text-sm text-blue-700 mt-1">Pastikan data instansi valid.</p></div>
                </div>

                <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-slate-200 mb-8">
                    <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-white">
                        <h3 class="font-bold text-slate-800">Antrian Registrasi (Pending)</h3>
                        <span class="bg-yellow-50 text-yellow-700 border border-yellow-200 text-xs font-bold px-3 py-1 rounded-full">{{ $pendingUsers->count() }} Menunggu</span>
                    </div>
                    @if($pendingUsers->count() > 0)
                        <table class="w-full text-sm text-left text-slate-600">
                            <!-- Tabel Header dan Body Sama -->
                            <thead class="text-xs text-slate-500 uppercase bg-slate-50/50 border-b border-slate-100">
                                <tr>
                                    <th class="px-6 py-4 font-semibold">Instansi</th>
                                    <th class="px-6 py-4 font-semibold">Kategori</th>
                                    <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingUsers as $user)
                                <tr class="bg-white hover:bg-slate-50">
                                    <td class="px-6 py-4 font-bold">{{ $user->name }}</td>
                                    <td class="px-6 py-4">{{ ucfirst($user->category) }}</td>
                                    <td class="px-6 py-4 text-center flex justify-center gap-2">
                                        <form action="{{ url('/internal/approve/' . $user->id) }}" method="POST">@csrf <button class="bg-emerald-600 text-white px-3 py-1.5 rounded text-xs font-bold">Aktifkan</button></form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="py-16 text-center"><p class="text-slate-500 text-sm">Tidak ada antrian.</p></div>
                    @endif
                </div>
            </main>
        </div>
    </div>
</body>
</html>