@include('inc.header_adm')

<div class="container-fluid bg-light min-vh-100 py-5">
    <div class="container">

        <!-- Welcome Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4" style="background: linear-gradient(135deg, rgb(88, 41, 150) 0%, rgb(125, 125, 219) 100%);">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="text-white fw-bold mb-2">
                                    <i class="bi bi-person-badge-fill me-2"></i>Hai Admin, {{ session('admin_nama', '...') }}!
                                </h4>
                                <p class="text-white opacity-75 mb-0">
                                    <i class="bi bi-building me-2"></i>Selamat datang di Dashboard Perpustakaan
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(session('message'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle p-3 bg-primary bg-opacity-10">
                                <i class="bi bi-book-fill text-primary" style="font-size: 2rem;"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Total Buku</h6>
                                <h3 class="fw-bold mb-0" style="color: rgb(88, 41, 150);">{{ $totalBuku }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle p-3 bg-success bg-opacity-10">
                                <i class="bi bi-people-fill text-success" style="font-size: 2rem;"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Total Anggota</h6>
                                <h3 class="fw-bold mb-0 text-success">{{ $totalAnggota }}</h3>
                                @if(($pendingAnggota ?? 0) > 0)
                                    <small class="text-warning">{{ $pendingAnggota }} menunggu approval</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle p-3 bg-warning bg-opacity-10">
                                <i class="bi bi-journal-check text-warning" style="font-size: 2rem;"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Sedang Dipinjam</h6>
                                <h3 class="fw-bold mb-0 text-warning">{{ $peminjamanAktif }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle p-3 bg-danger bg-opacity-10">
                                <i class="bi bi-exclamation-triangle text-danger" style="font-size: 2rem;"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Terlambat</h6>
                                <h3 class="fw-bold mb-0 text-danger">{{ $terlambat ?? 0 }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- List Terbaru -->
        <div class="row">
            <!-- 5 Pendaftar Terakhir -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 pt-4 pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0" style="color: rgb(88, 41, 150);">
                                <i class="bi bi-person-plus-fill me-2"></i>5 Pendaftar Terakhir
                            </h5>
                            <a href="{{ route('admin.list_user') }}" class="btn btn-sm btn-outline-primary rounded-pill">Lihat Semua</a>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        @forelse($anggotaTerbaru as $item)
                        <div class="d-flex align-items-center py-2 border-bottom">
                            <div class="flex-shrink-0 me-3">
                                @if($item->photo_url)
                                    <img src="{{ asset($item->photo_url) }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;" alt="">
                                @else
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="bi bi-person text-muted"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $item->nama }}</div>
                                <small class="text-muted">{{ $item->kelas }} • {{ $item->tgl_daftar ? \Carbon\Carbon::parse($item->tgl_daftar)->format('d M Y') : '-' }}</small>
                            </div>
                            <a href="{{ route('admin.edit_user', $item->id_anggota) }}" class="btn btn-sm btn-outline-secondary" title="Detail">Detail</a>
                        </div>
                        @empty
                        <p class="text-muted text-center py-4 mb-0">
                            <i class="bi bi-inbox fs-1"></i><br>
                            Belum ada anggota terdaftar
                        </p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- 5 Peminjaman Terakhir -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 pt-4 pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0" style="color: rgb(88, 41, 150);">
                                <i class="bi bi-journal-check me-2"></i>5 Peminjaman Terakhir
                            </h5>
                            <a href="{{ route('admin.peminjaman') }}" class="btn btn-sm btn-outline-primary rounded-pill">Lihat Semua</a>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        @forelse($peminjamanTerbaru as $item)
                        <div class="d-flex align-items-center py-2 border-bottom">
                            <div class="flex-shrink-0 me-3">
                                <div class="bg-primary bg-opacity-10 rounded p-2">
                                    <i class="bi bi-book text-primary"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $item->buku ? $item->buku->judul : '-' }}</div>
                                <small class="text-muted">
                                    {{ $item->anggota ? $item->anggota->nama : '-' }} •
                                    {{ $item->tanggal_pinjam ? $item->tanggal_pinjam->format('d M Y') : '-' }}
                                </small>
                            </div>
                            @if($item->status == 'Dipinjam')
                                <span class="badge bg-warning text-dark">Dipinjam</span>
                            @else
                                <span class="badge bg-success">Kembali</span>
                            @endif
                        </div>
                        @empty
                        <p class="text-muted text-center py-4 mb-0">
                            <i class="bi bi-inbox fs-1"></i><br>
                            Belum ada data peminjaman
                        </p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@include('inc.footer_adm')
