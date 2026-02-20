<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Log Aktivitas | SI-MUTU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-100 text-slate-800">

    <!-- LOGIKA FETCH DATA DATABASE -->
    @php
        use App\Models\ActivityLog;
        use Illuminate\Support\Facades\Request;

        // Ambil Filter dari URL
        $f_action = Request::get('action');
        $f_date = Request::get('date');

        // Query Dasar
        $query = ActivityLog::with('user')->latest();

        // Terapkan Filter
        if ($f_action && $f_action !== 'Semua Aktivitas') {
            $query->where('action', $f_action);
        }

        if ($f_date) {
            $query->whereDate('created_at', $f_date);
        }

        // Ambil Data (Pagination 15 per halaman)
        $logs = $query->paginate(15); 
    @endphp

    <div class="flex h-screen overflow-hidden">
        
        <!-- INCLUDE SIDEBAR -->
        @include('components.internal-sidebar')

        <!-- KONTEN UTAMA -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden">
            <header class="bg-white shadow-sm h-20 flex items-center justify-between px-8 z-10">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Audit Trail</h2>
                    <p class="text-xs text-slate-500 mt-1">Rekaman aktivitas pengguna dalam sistem</p>
                </div>
            </header>
            
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50/50 p-8">
                
                <!-- Filter Log Form -->
                <form action="{{ url('/internal/logs') }}" method="GET" class="flex justify-between items-center mb-6">
                    <div class="flex gap-2">
                        <select name="action" onchange="this.form.submit()" class="text-sm border border-slate-300 rounded-lg p-2 focus:ring-red-500 outline-none bg-white">
                            <option {{ $f_action == 'Semua Aktivitas' ? 'selected' : '' }}>Semua Aktivitas</option>
                            <option value="LOGIN" {{ $f_action == 'LOGIN' ? 'selected' : '' }}>Login</option>
                            <option value="UPLOAD" {{ $f_action == 'UPLOAD' ? 'selected' : '' }}>Upload</option>
                            <option value="UPDATE" {{ $f_action == 'UPDATE' ? 'selected' : '' }}>Update</option>
                            <option value="DELETE" {{ $f_action == 'DELETE' ? 'selected' : '' }}>Hapus Data</option>
                            <option value="VERIFIKASI" {{ $f_action == 'VERIFIKASI' ? 'selected' : '' }}>Verifikasi</option>
                        </select>
                        <input type="date" name="date" value="{{ $f_date }}" onchange="this.form.submit()" class="text-sm border border-slate-300 rounded-lg p-2 focus:ring-red-500 outline-none">
                        
                        @if($f_action || $f_date)
                            <a href="{{ url('/internal/logs') }}" class="px-3 py-2 bg-slate-200 hover:bg-slate-300 rounded-lg text-xs font-bold text-slate-600 flex items-center">Reset</a>
                        @endif
                    </div>
                    <button type="button" onclick="window.print()" class="text-xs font-bold text-red-600 hover:underline flex items-center gap-1">
                        <i class="fas fa-file-export"></i> Export PDF
                    </button>
                </form>

                <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-slate-200">
                    <table class="w-full text-sm text-left text-slate-600">
                        <thead class="text-xs text-slate-500 uppercase bg-slate-50/50 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-4 font-semibold">Waktu</th>
                                <th class="px-6 py-4 font-semibold">User</th>
                                <th class="px-6 py-4 font-semibold">Aktivitas</th>
                                <th class="px-6 py-4 font-semibold">IP Address</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($logs as $log)
                            <tr class="bg-white hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-mono text-xs text-slate-500">
                                    {{ $log->created_at->format('d M Y') }}
                                    <span class="block text-[10px]">{{ $log->created_at->format('H:i:s') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800">{{ $log->user->name ?? 'User Terhapus' }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $log->user->email ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $badgeColor = match($log->action) {
                                            'LOGIN' => 'bg-green-100 text-green-700',
                                            'LOGOUT' => 'bg-gray-100 text-gray-700',
                                            'UPLOAD' => 'bg-blue-100 text-blue-700',
                                            'DELETE' => 'bg-red-100 text-red-700',
                                            'VERIFIKASI' => 'bg-purple-100 text-purple-700',
                                            default => 'bg-orange-100 text-orange-700'
                                        };
                                    @endphp
                                    <span class="{{ $badgeColor }} text-[10px] font-bold px-2 py-0.5 rounded mr-2">{{ $log->action }}</span> 
                                    <span class="text-slate-600">{{ $log->description }}</span>
                                </td>
                                <td class="px-6 py-4 text-xs font-mono text-slate-500">{{ $log->ip_address }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                    <i class="fas fa-history text-3xl mb-2 opacity-50"></i>
                                    <p>Belum ada data aktivitas.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination Sederhana -->
                <div class="mt-4 flex justify-between items-center text-xs text-slate-500">
                    <div>
                        Menampilkan {{ $logs->firstItem() ?? 0 }} - {{ $logs->lastItem() ?? 0 }} dari {{ $logs->total() }} data
                    </div>
                    <div class="flex gap-1">
                        @if($logs->onFirstPage())
                            <span class="px-3 py-1 border rounded bg-slate-50 text-slate-300 cursor-not-allowed">Sebelumnya</span>
                        @else
                            <a href="{{ $logs->previousPageUrl() }}" class="px-3 py-1 border rounded hover:bg-white">Sebelumnya</a>
                        @endif

                        @if($logs->hasMorePages())
                            <a href="{{ $logs->nextPageUrl() }}" class="px-3 py-1 border rounded hover:bg-white">Selanjutnya</a>
                        @else
                            <span class="px-3 py-1 border rounded bg-slate-50 text-slate-300 cursor-not-allowed">Selanjutnya</span>
                        @endif
                    </div>
                </div>

            </main>
        </div>
    </div>
</body>
</html>