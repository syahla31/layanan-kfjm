<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Menampilkan Dashboard Super Admin (Internal)
     * Mengambil data user yang statusnya 'pending'
     */
    public function internal()
    {
        // Ambil semua user yang statusnya masih 'pending'
        $pendingUsers = User::where('status', 'pending')
                            ->orderBy('created_at', 'desc')
                            ->get();
                            
        return view('internal.dashboard', [
            'pendingUsers' => $pendingUsers
        ]);
    }

    /**
     * Aksi Menyetujui (Aktifkan) Akun User
     */
    public function approveUser($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'active'; // Ubah status jadi active
        $user->save();

        return back()->with('success', 'Akun ' . $user->name . ' berhasil diaktifkan.');
    }

    /**
     * Aksi Menolak (Hapus) Akun User
     */
    public function rejectUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete(); // Hapus user dari database

        return back()->with('success', 'Akun ' . $user->name . ' telah ditolak dan dihapus.');
    }
}