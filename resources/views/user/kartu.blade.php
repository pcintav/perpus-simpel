<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Anggota</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <style>
        body { background: #fff; }
        .kartu { width: 420px; border: 2px solid #1f4e79; padding: 16px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body class="p-4">
    <button class="btn btn-primary no-print mb-3" onclick="window.print()">Cetak</button>
    <div class="kartu">
        <div class="d-flex gap-3">
            @if($anggota->photo_url)
                <img src="{{ asset($anggota->photo_url) }}" alt="" width="90" height="110" style="object-fit: cover;">
            @endif
            <div>
                <div class="fw-bold">KARTU ANGGOTA PERPUSTAKAAN</div>
                <div class="small text-muted mb-2">{{ $namaSekolah ?? 'Sekolah' }}</div>
                <div><strong>{{ $anggota->nama }}</strong></div>
                <div>NIS {{ $anggota->nis }} · Kelas {{ $anggota->kelas }}</div>
                <div class="small">{{ $anggota->email }}</div>
                <div class="small">Status: {{ $anggota->status ?? 'aktif' }}</div>
            </div>
        </div>
    </div>
</body>
</html>
