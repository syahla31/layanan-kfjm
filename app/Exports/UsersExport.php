<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $users;

    /**
     * Menerima data users dari Controller
     */
    public function __construct($users)
    {
        $this->users = $users;
    }

    /**
     * Mengembalikan koleksi data
     */
    public function collection()
    {
        return $this->users;
    }

    /**
     * Menentukan Header/Judul Kolom di Excel
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nama Instansi',
            'Email',
            'Kategori',
            'Status',
            'Surat Kuasa',
            'Tanggal Registrasi',
        ];
    }

    /**
     * Memetakan data dari model ke kolom Excel
     */
    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            ucfirst($user->category),
            strtoupper($user->status),
            $user->surat_kuasa_path ? 'Ada' : 'Tidak Ada',
            $user->created_at->format('d/m/Y'),
        ];
    }
}
