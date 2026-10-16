<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi Denda</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <style>@media print { .no-print { display: none; } }</style>
</head>
<body class="p-4">
    <button class="btn btn-primary no-print mb-3" onclick="window.print()">Cetak</button>
    <a class="btn btn-outline-secondary no-print mb-3" href="{{ route('admin.peminjaman') }}">Kembali</a>
    <div style="max-width: 520px; border: 1px solid #333; padding: 16px;">
        <h5 class="mb-1">Kwitansi Denda</h5>
        <p class="small text-muted">No. pinjam {{ $pinjam->id_pinjam }}</p>
        <table class="table table-sm">
            <tr><td>Anggota</td><td>{{ $pinjam->anggota->nama ?? '-' }} ({{ $pinjam->anggota->kelas ?? '-' }})</td></tr>
            <tr><td>Buku</td><td>{{ $pinjam->buku->judul ?? '-' }}</td></tr>
            <tr><td>Denda</td><td>Rp {{ number_format($pinjam->denda ?? 0, 0, ',', '.') }}</td></tr>
            <tr><td>Status bayar</td><td>{{ $pinjam->denda_lunas ? 'Lunas' : 'Belum lunas' }}</td></tr>
            <tr><td>Tanggal bayar</td><td>{{ $pinjam->tgl_bayar_denda ? $pinjam->tgl_bayar_denda->format('d M Y H:i') : '-' }}</td></tr>
        </table>
    </div>
</body>
</html>
