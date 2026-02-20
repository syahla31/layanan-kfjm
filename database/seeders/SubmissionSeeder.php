<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Submission;
use App\Models\User;

class SubmissionSeeder extends Seeder
{
    public function run(): void
    {
        // Cari User kategori Pelatihan (Balai Diklat)
        $userPelatihan = User::where('category', 'pelatihan')->where('role', 'user')->first();

        if ($userPelatihan) {
            // Data 1: Laporan Tahunan (Pending)
            Submission::create([
                'user_id' => $userPelatihan->id,
                'category' => 'pelatihan',
                'type' => 'Laporan Tahunan',
                'title' => 'Laporan Kegiatan Tahunan 2025',
                'status' => 'pending',
                'created_at' => now()->subHours(2)
            ]);

            // Data 2: Survailen (Approved)
            Submission::create([
                'user_id' => $userPelatihan->id,
                'category' => 'pelatihan',
                'type' => 'Survailen',
                'title' => 'Laporan Survailen Semester 1',
                'status' => 'approved',
                'created_at' => now()->subDays(2)
            ]);

            // Data 3: KAK (Rejected)
            Submission::create([
                'user_id' => $userPelatihan->id,
                'category' => 'pelatihan',
                'type' => 'KAK',
                'title' => 'Kerangka Acuan Kerja 2026',
                'status' => 'rejected',
                'created_at' => now()->subDays(5)
            ]);
        }
    }
}