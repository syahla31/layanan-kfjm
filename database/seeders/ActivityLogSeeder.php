<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ActivityLog;
use App\Models\User;

class ActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $actions = ['LOGIN', 'LOGOUT', 'UPLOAD', 'UPDATE', 'DELETE', 'VERIFIKASI'];

        // Buat 50 data dummy
        foreach(range(1, 50) as $index) {
            $user = $users->random();
            $action = $actions[array_rand($actions)];
            
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => $action,
                'description' => $this->getDescription($action),
                'ip_address' => '192.168.1.' . rand(1, 255),
                'created_at' => now()->subMinutes(rand(1, 10000)),
            ]);
        }
    }

    private function getDescription($action) {
        return match($action) {
            'LOGIN' => 'Berhasil masuk ke sistem',
            'LOGOUT' => 'Keluar dari sistem',
            'UPLOAD' => 'Mengunggah dokumen baru',
            'UPDATE' => 'Memperbarui data profil',
            'DELETE' => 'Menghapus data pengajuan',
            'VERIFIKASI' => 'Memverifikasi akun pengguna',
            default => 'Melakukan aktivitas sistem',
        };
    }
}