@extends('layouts.app')

@section('title', 'Lembaga Pelatihan')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
    <div class="flex justify-between items-center mb-8 border-b border-slate-100 pb-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-xl">
                <i class="fa-solid fa-chalkboard-user"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Lembaga Pelatihan</h2>
                <p class="text-slate-500 text-sm">Kelola data lembaga pelatihan terdaftar</p>
            </div>
        </div>
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-medium">
            + Tambah Data
        </button>
    </div>

    <!-- Tabel Data (Contoh Statis, nanti diganti Loop Database) -->
    <table class="w-full text-left">
        <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
            <tr>
                <th class="px-4 py-3">Nama Lembaga</th>
                <th class="px-4 py-3">Lokasi</th>
                <th class="px-4 py-3">Status</th>
            </tr>
        </thead>
        <tbody>
            <tr class="border-b">
                <td class="px-4 py-3 font-medium">LPK Medika Utama</td>
                <td class="px-4 py-3">Jakarta Selatan</td>
                <td class="px-4 py-3 text-green-600">Aktif</td>
            </tr>
            <!-- Di Laravel nanti pakai: @foreach($data as $item) ... @endforeach -->
        </tbody>
    </table>
</div>
@endsection