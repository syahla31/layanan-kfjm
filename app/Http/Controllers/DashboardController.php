<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;        // Class export untuk Excel
use Illuminate\Http\Request;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel; // Pastikan sudah instal maatwebsite/excel
use Barryvdh\DomPDF\Facade\Pdf;     // Pastikan sudah instal barryvdh/laravel-dompdf

class DashboardController extends Controller
{
    /**
     * Menampilkan Dashboard Utama Admin (Hanya User Pending)
     */
    public function internal()
    {
        $pendingUsers = User::where('status', 'pending')
                            ->orderBy('created_at', 'desc')
                            ->get();
                            
        return view('internal.dashboard', [
            'pendingUsers' => $pendingUsers
        ]);
    }

    /**
     * Menampilkan Data Master Pengguna (Dengan Fitur Search & Filter)
     */
    public function indexUsers(Request $request)
    {
        $query = User::query();

        // Filter berdasarkan Pencarian (Nama/Email)
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter berdasarkan Kategori Modul
        if ($request->filled('category') && $request->category !== 'Semua') {
            $query->where('category', $request->category);
        }

        // Filter berdasarkan Status
        if ($request->filled('status') && $request->status !== 'Semua') {
            $statusValue = ($request->status === 'Aktif') ? 'active' : 'pending';
            $query->where('status', $statusValue);
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        return view('internal.users', compact('users'));
    }

    /**
     * Memperbarui Data Pengguna (Aksi Edit)
     */
    public function updateUser(Request $request, $id)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $id,
            'category' => 'required',
            'status'   => 'required'
        ]);

        $user = User::findOrFail($id);
        $user->update($request->only(['name', 'email', 'category', 'status']));

        return back()->with('success', 'Data instansi ' . $user->name . ' berhasil diperbarui.');
    }

    /**
     * Mengunduh Data (Excel atau PDF)
     */
    public function exportUsers(Request $request)
    {
        $format = $request->get('format');
        $query = User::query();

        // Terapkan filter yang sama agar data yang didownload sesuai dengan yang tampil di layar
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('category') && $request->category !== 'Semua') {
            $query->where('category', $request->category);
        }
        if ($request->filled('status') && $request->status !== 'Semua') {
            $statusValue = ($request->status === 'Aktif') ? 'active' : 'pending';
            $query->where('status', $statusValue);
        }

        $users = $query->get();

        if ($format === 'excel') {
            // Memerlukan class UsersExport (php artisan make:export UsersExport)
            return Excel::download(new UsersExport($users), 'data_pengguna_simutu.xlsx');
        } 
        
        if ($format === 'pdf') {
            $pdf = Pdf::loadView('exports.users_pdf', compact('users'));
            return $pdf->download('data_pengguna_simutu.pdf');
        }

        return back()->with('error', 'Format export tidak valid.');
    }

    /**
     * Aksi Menyetujui Akun (Approve)
     */
    public function approveUser($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'active';
        $user->save();

        return back()->with('success', 'Akun ' . $user->name . ' telah diaktifkan.');
    }

    /**
     * Aksi Menolak Akun (Reject)
     */
    public function rejectUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return back()->with('success', 'Pendaftaran ' . $user->name . ' telah dihapus.');
    }
}