@include('inc.header_user')

<div class="container-fluid bg-light min-vh-100 py-5">
    <div class="container">

        @if(session('message'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Card Kata Sambutan / Profil -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4" style="background: linear-gradient(135deg, rgb(88, 41, 150) 0%, rgb(125, 125, 219) 100%);">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                @if($anggota->photo_url)
                                    <img src="{{ asset($anggota->photo_url) }}" alt="{{ $anggota->nama }}"
                                         class="rounded-circle shadow" style="width: 90px; height: 90px; object-fit: cover;">
                                @else
                                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 90px; height: 90px;">
                                        <i class="bi bi-person-fill text-white" style="font-size: 3rem;"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="col">
                                <h4 class="text-white fw-bold mb-2">
                                    <i class="bi bi-hand-wave me-2"></i>Hai, {{ $anggota->nama }}!
                                </h4>
                                <p class="text-white mb-1 opacity-90">
                                    <i class="bi bi-geo-alt-fill me-2"></i>{{ $anggota->alamat }}
                                </p>
                                <p class="text-white mb-1 opacity-90">
                                    <i class="bi bi-mortarboard-fill me-2"></i>Kelas {{ $anggota->kelas }} • NIS {{ $anggota->nis }}
                                </p>
                                <p class="text-white mb-0 opacity-90">
                                    <i class="bi bi-envelope-fill me-2"></i>{{ $anggota->email }}
                                    <span class="ms-3"><i class="bi bi-telephone-fill me-2"></i>{{ $anggota->telp }}</span>
                                </p>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('user.edit_akun') }}" class="btn btn-light btn-sm rounded-pill">
                                    <i class="bi bi-pencil-square me-1"></i>Edit Profil
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- List Buku yang Dipinjam -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="fw-bold mb-1" style="color: rgb(88, 41, 150);">
                                    <i class="bi bi-journal-bookmark-fill me-2"></i>Buku yang Saya Pinjam
                                </h5>
                                <p class="text-muted mb-0 small">
                                    Pinjam aktif: {{ $aktifCount ?? 0 }} / {{ $maxPinjam ?? 3 }} buku
                                </p>
                            </div>
                            <a href="{{ route('user.perpus') }}" class="btn btn-sm text-white rounded-pill"
                               style="background: linear-gradient(135deg, rgb(88, 41, 150) 0%, rgb(125, 125, 219) 100%);">
                                <i class="bi bi-plus-circle me-1"></i>Pinjam Buku
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Cover</th>
                                        <th>Judul Buku</th>
                                        <th>Pengarang</th>
                                        <th>Tanggal Pinjam</th>
                                        <th>Wajib Kembali</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($peminjaman as $no => $item)
                                    <tr>
                                        <td>{{ $no + 1 }}</td>
                                        <td>
                                            @if($item->buku && $item->buku->bukuimg_url)
                                                <img src="{{ asset($item->buku->bukuimg_url) }}" alt="" class="rounded" style="height: 45px; width: 35px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 45px; width: 35px;">
                                                    <i class="bi bi-book text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="fw-semibold">{{ $item->buku ? $item->buku->judul : '-' }}</td>
                                        <td>{{ $item->buku ? $item->buku->pengarang : '-' }}</td>
                                        <td>{{ $item->tanggal_pinjam ? $item->tanggal_pinjam->format('d M Y') : '-' }}</td>
                                        <td>{{ $item->tanggal_kembali ? $item->tanggal_kembali->format('d M Y') : '-' }}</td>
                                        <td>
                                            @if($item->status == 'Dipinjam')
                                                <span class="badge bg-warning text-dark"><i class="bi bi-clock-history me-1"></i>Dipinjam</span>
                                                @if($item->isTerlambat())
                                                    <br><span class="badge bg-danger mt-1">Terlambat {{ $item->hariTerlambat() }} hari</span>
                                                @endif
                                            @else
                                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Kembali</span>
                                                @if(($item->denda ?? 0) > 0)
                                                    <br><small class="text-danger">Denda Rp {{ number_format($item->denda, 0, ',', '.') }}</small>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->status == 'Dipinjam' && $item->perpanjang_status !== 'disetujui' && $item->perpanjang_status !== 'diajukan')
                                                <form method="POST" action="{{ route('user.perpanjang') }}">
                                                    @csrf
                                                    <input type="hidden" name="id_pinjam" value="{{ $item->id_pinjam }}">
                                                    <button class="btn btn-sm btn-outline-primary">Perpanjang</button>
                                                </form>
                                            @elseif($item->perpanjang_status === 'diajukan')
                                                <span class="small">Menunggu admin</span>
                                            @elseif($item->perpanjang_status === 'disetujui')
                                                <span class="small text-success">Diperpanjang</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                                            <h6 class="mt-3 fw-semibold text-muted">Belum ada peminjaman</h6>
                                            <p class="text-muted small mb-3">Anda belum meminjam buku apapun.</p>
                                            <a href="{{ route('user.perpus') }}" class="btn btn-primary rounded-pill">
                                                <i class="bi bi-book me-1"></i>Lihat Katalog Buku
                                            </a>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Reservasi saya</h5>
                        <table class="table table-sm">
                            <thead><tr><th>Buku</th><th>Status</th><th></th></tr></thead>
                            <tbody>
                                @forelse($reservasi ?? [] as $r)
                                <tr>
                                    <td>{{ $r->buku->judul ?? '-' }}</td>
                                    <td>{{ $r->status === 'siap' ? 'Siap diambil' : 'Masih antri' }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('user.reservasi.batal') }}">
                                            @csrf
                                            <input type="hidden" name="id_reservasi" value="{{ $r->id_reservasi }}">
                                            <button class="btn btn-sm btn-outline-danger">Batal</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-muted">Tidak ada antrian.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                        <a href="{{ route('user.kartu') }}" class="btn btn-outline-primary btn-sm" target="_blank">Cetak kartu anggota</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@include('inc.footer_user')
