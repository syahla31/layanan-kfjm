<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Pengguna | SI-MUTU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-100 text-slate-800">

    @php
        use App\Models\User;
        use Illuminate\Support\Facades\Request;
        $search = Request::get('search'); 
        if(!isset($users)) {
            $query = User::orderBy('created_at', 'desc');
            if ($search) { $query->where('name', 'like', '%' . $search . '%'); }
            $users = $query->get();
        }
    @endphp

    <div class="flex h-screen overflow-hidden">
        
        <!-- INCLUDE SIDEBAR -->
        @include('components.internal-sidebar')

        <div class="flex-1 flex flex-col h-screen overflow-hidden">
            <header class="bg-white shadow-sm h-20 flex items-center justify-between px-8 z-10">
                <div><h2 class="text-xl font-bold text-slate-800">Data Master Pengguna</h2></div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50/50 p-8">
                <!-- Toolbar Search -->
                <form action="{{ url('/internal/users') }}" method="GET" class="mb-6 flex gap-4">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari user..." class="w-full md:w-96 py-2.5 pl-4 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500">
                    <button class="bg-slate-800 text-white px-4 py-2 rounded-lg text-xs font-bold">Cari</button>
                </form>

                <!-- Tabel User -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-slate-200">
                    <table class="w-full text-sm text-left text-slate-600">
                        <thead class="text-xs text-slate-500 uppercase bg-slate-50/50 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-4">Nama</th>
                                <th class="px-6 py-4">Email</th>
                                <th class="px-6 py-4">Kategori</th>
                                <th class="px-6 py-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr class="bg-white hover:bg-slate-50 border-b">
                                <td class="px-6 py-4 font-bold">{{ $user->name }}</td>
                                <td class="px-6 py-4">{{ $user->email }}</td>
                                <td class="px-6 py-4">{{ ucfirst($user->category) }}</td>
                                <td class="px-6 py-4">{{ ucfirst($user->status) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>
</body>
</html>