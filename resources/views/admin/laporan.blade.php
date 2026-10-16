@include('inc.header_adm')

<div class="container-fluid bg-light min-vh-100 py-5">
    <div class="container">
        @if(session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h4 class="fw-bold mb-1" style="color: #0f766e;">
                    <i class="bi bi-bar-chart-fill me-2"></i>Laporan Perpustakaan
                </h4>
                <p class="text-muted mb-0">Ringkasan statistik untuk evaluasi</p>
            </div>
            <a href="{{ route('admin.export.peminjaman') }}" class="btn btn-outline-success rounded-pill">
                <i class="bi bi-filetype-csv me-1"></i>Export CSV Peminjaman
            </a>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <div class="text-muted small">Pinjam bulan ini</div>
                        <div class="fs-3 fw-bold" style="color: #0f766e;">{{ $pinjamBulanIni }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <div class="text-muted small">Sedang terlambat</div>
                        <div class="fs-3 fw-bold text-danger">{{ $terlambat }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <div class="text-muted small">Total denda terkumpul</div>
                        <div class="fs-3 fw-bold">Rp {{ number_format($totalDenda, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <div class="text-muted small">Denda / hari</div>
                        <div class="fs-3 fw-bold">Rp {{ number_format(config('perpus.denda_per_hari'), 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Buku Terpopuler</h5>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle">
                                <thead><tr><th>Judul</th><th>Total Pinjam</th></tr></thead>
                                <tbody>
                                    @forelse($bukuPopuler as $b)
                                    <tr>
                                        <td>{{ $b->judul }}</td>
                                        <td><span class="badge bg-primary">{{ $b->total_pinjam }}</span></td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="2" class="text-muted">Belum ada data</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Anggota Paling Aktif</h5>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle">
                                <thead><tr><th>Nama</th><th>Kelas</th><th>Total Pinjam</th></tr></thead>
                                <tbody>
                                    @forelse($anggotaAktif as $a)
                                    <tr>
                                        <td>{{ $a->nama }}</td>
                                        <td>{{ $a->kelas }}</td>
                                        <td><span class="badge bg-success">{{ $a->total_pinjam }}</span></td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="3" class="text-muted">Belum ada data</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Peminjaman per Bulan (6 bulan terakhir)</h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead><tr><th>Bulan</th><th>Jumlah</th></tr></thead>
                                <tbody>
                                    @forelse($perBulan as $row)
                                    <tr>
                                        <td>{{ $row->bulan }}</td>
                                        <td>{{ $row->total }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="2" class="text-muted">Belum ada data</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('inc.footer_adm')
