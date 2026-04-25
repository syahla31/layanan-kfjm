<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @font-face {
            font-family: 'EuphoriaScript';
            src: url('{{ public_path('fonts/EuphoriaScript-Regular.ttf') }}') format('truetype');
        }

        @page { 
            size: 297mm 210mm landscape; 
            margin: 0; 
        }
        body { margin: 0; padding: 0; }

        .certificate-container {
            width: 297mm;
            height: 210mm;
            background-image: url('{{ public_path('image/sertikat-survailen-pelatihan.png') }}');
            background-size: 100% 100%;
            background-repeat: no-repeat;
            position: relative;
        }

        /* NAMA: Diturunkan sedikit dari 30% ke 37% */
        .nama-user {
            position: absolute;
            top: 34%; 
            width: 100%;
            text-align: center;
            font-family: 'EuphoriaScript', cursive; 
            font-size: 50pt; 
            color: #32a4c2; 
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }

        /* PREDIKAT: Tetap di tengah kotak biru */
        .predikat {
            position: absolute;
            top: 110mm;
            width: 100%;
            text-align: center;
            font-family: 'Arial', sans-serif;
            font-size: 35pt; 
            font-weight: 800;
            color: #f1f1f1;
        }

        /* TANGGAL: Diturunkan dari 80mm ke 43mm agar di atas teks Direktur */
        .tanggal {
            position: absolute;
            bottom: 48mm; 
            width: 100%;
            text-align: center;
            font-family: 'Arial', sans-serif;
            font-size: 14pt;
            font-weight: normal;
            color: #000;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="nama-user">{{ $nama_user }}</div>
        <div class="predikat">Predikat {{ $predikat }}</div>
        <div class="tanggal">Jakarta, {{ $tanggal }}</div>
    </div>
</body>
</html>