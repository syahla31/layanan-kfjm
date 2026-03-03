<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Collection;

class UsersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $users;

    /**
     * @param Collection $users
     */
    public function __construct($users)
    {
        // Memastikan data selalu dalam bentuk Collection agar FromCollection tidak error
        $this->users = $users instanceof Collection ? $users : collect($users);
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->users;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID SYSTEM',
            'NAMA INSTANSI',
            'EMAIL KORESPONDENSI',
            'KATEGORI MODUL',
            'STATUS AKUN',
            'TANGGAL REGISTRASI',
        ];
    }

    /**
     * @param mixed $user
     * @return array
     */
    public function map($user): array
    {
        return [
            '#' . str_pad($user->id, 5, '0', STR_PAD_LEFT),
            strtoupper($user->name),
            $user->email,
            strtoupper($user->category),
            $user->status === 'active' ? 'AKTIF' : 'PENDING',
            $user->created_at ? $user->created_at->format('d/m/Y H:i') : '-',
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'F2F2F2'],
                ]
            ],
        ];
    }
}