<!DOCTYPE html>

<html>

<head>
    <title>Data Pengguna SI-MUTU</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .status-active {
            color: green;
            font-weight: bold;
        }

        .status-pending {
            color: orange;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>LAPORAN DATA PENGGUNA SI-MUTU PRO</h2>
        <p>Dicetak pada: {{ now()->format('d M Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Instansi</th>
                <th>Email</th>
                <th>Kategori</th>
                <th>Status</th>
                <th>Surat Kuasa</th>
                <th>Tgl Daftar</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ ucfirst($user->category) }}</td>
                    <td>
                        <span class="{{ $user->status == 'active' ? 'status-active' : 'status-pending' }}">
                            {{ strtoupper($user->status) }}
                        </span>
                    </td>
                    <td>{{ $user->surat_kuasa_path ? 'Ada' : 'Tidak Ada' }}</td>
                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>


</body>

</html>
