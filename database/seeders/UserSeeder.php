<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Password default untuk semua akun: 'password'
        $password = Hash::make('password'); // Password: 'password'

        // ====================================================
        // LINGKUP 1: PELATIHAN
        // Login di: /pelatihan/login
        // ====================================================
        
        // Pihak Luar (Pengaju)
        User::create([
            'name' => 'Balai Diklat Jogja',
            'email' => 'diklat@jogja.com',
            'password' => $password,
            'category' => 'pelatihan',
            'role' => 'user', 
            'kode_instansi' => 'LP-001',
            'status' => 'active'
        ]);

        // Pihak Dalam (Admin Verifikator Pelatihan)
        User::create([
            'name' => 'Admin Verifikator Pelatihan',
            'email' => 'admin.pelatihan@bapeten.go.id',
            'password' => $password,
            'category' => 'pelatihan', // Sama-sama category pelatihan
            'role' => 'admin',         // Tapi role-nya admin
            'kode_instansi' => null,
            'status' => 'active'
        ]);

        // ====================================================
        // LINGKUP 2: LEMBAGA UJI
        // Login di: /uji/login
        // ====================================================

        // Pihak Luar (Pengaju)
        User::create([
            'name' => 'PT Lab Uji Prima',
            'email' => 'lab@prima.com',
            'password' => $password,
            'category' => 'uji',
            'role' => 'user',
            'kode_instansi' => 'LU-024',
            'status' => 'active'
        ]);

        // Pihak Dalam (Admin Evaluator Uji)
        User::create([
            'name' => 'Admin Evaluator Uji',
            'email' => 'admin.uji@bapeten.go.id',
            'password' => $password,
            'category' => 'uji',
            'role' => 'admin',
            'kode_instansi' => null,
            'status' => 'active'
        ]);

        // ====================================================
        // LINGKUP 3: SINAR-X
        // Login di: /sinarx/login
        // ====================================================

        // Pihak Luar (Pengaju)
        User::create([
            'name' => 'RS Sehat Sentosa',
            'email' => 'rs@sentosa.com',
            'password' => $password,
            'category' => 'sinarx',
            'role' => 'user',
            'kode_instansi' => 'RS-999',
            'status' => 'active'
        ]);

        // Pihak Dalam (Admin Sinar-X)
        User::create([
            'name' => 'Admin Sinar-X',
            'email' => 'admin.sinarx@bapeten.go.id',
            'password' => $password,
            'category' => 'sinarx',
            'role' => 'admin',
            'kode_instansi' => null,
            'status' => 'active'
        ]);

        User::create([
            'name' => 'Super Admin DKKN',
            'email' => 'admin@bapeten.go.id',
            'password' => $password,
            'category' => 'internal',   
            'role' => 'admin',          // Full Akses User Management
            'kode_instansi' => null,
            'status' => 'active'
        ]);
    }
}
