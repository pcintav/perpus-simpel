@include('inc.header_adm')

<div class="container-fluid bg-light min-vh-100 py-5">
    <div class="container">

        @if(session('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="fw-bold mb-1" style="color: rgb(88, 41, 150);">
                    <i class="bi bi-journal-check me-2"></i>Data Peminjaman
                </h4>
                <p class="text-muted mb-0">Daftar peminjaman buku perpustakaan</p>
            </div>
            <a href="{{ route('admin.export.peminjaman') }}" class="btn btn-sm btn-outline-success rounded-pill">
                <i class="bi bi-download me-1"></i>Export CSV
            </a>
        </div>

        <form method="GET" class="row mb-4 g-2">
            <div class="col-md-5">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                       placeholder="Cari nama anggota atau judul buku...">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="status">
                    <option value="">Semua Status</option>
                    <option value="Dipinjam" @selected(request('status')==='Dipinjam')>Dipinjam</option>
                    <option value="kembali" @selected(request('status')==='kembali')>Kembali</option>
                </select>
            </div>
            <div class="col-md-2">
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" name="terlambat" value="1" id="terlambat"
                           @checked(request()->boolean('terlambat'))>
                    <label class="form-check-label" for="terlambat">Terlambat</label>
                </div>
            </div>
            <div class="col-md-2">
                <button class="btn text-white w-100" style="background:#0f766e;">Filter</button>
            </div>
        </form>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Judul Buku</th>
                                <th>Anggota</th>
                                <th>Tanggal Pinjam</th>
                                <th>Wajib Kembali</th>
                                <th>Dikembalikan</th>
                                <th>Status</th>
                                <th>Denda</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($peminjaman as $item)
                            <tr>
                                <td>{{ $peminjaman->firstItem() + $loop->index }}</td>
                                <td>{{ $item->buku ? $item->buku->judul : '-' }}</td>
                                <td>{{ $item->anggota ? $item->anggota->nama : '-' }}</td>
                                <td>{{ $item->tanggal_pinjam ? $item->tanggal_pinjam->format('d M Y') : '-' }}</td>
                                <td>
                                    {{ $item->tanggal_kembali ? $item->tanggal_kembali->format('d M Y') : '-' }}
                                    @if($item->isTerlambat())
                                        <br><span class="badge bg-danger">Terlambat {{ $item->hariTerlambat() }} hari</span>
                                    @endif
                                </td>
                                <td>{{ $item->tanggal_dikembalikan ? $item->tanggal_dikembalikan->format('d M Y') : '-' }}</td>
                                <td>
                                    @if($item->status == 'Dipinjam')
                                        <span class="badge bg-warning text-dark">Dipinjam</span>
                                    @else
                                        <span class="badge bg-success">Kembali</span>
                                    @endif
                                </td>
                                <td>
                                    @if(($item->denda ?? 0) > 0)
                                        Rp {{ number_format($item->denda, 0, ',', '.') }}
                                    @elseif($item->isTerlambat())
                                        <span class="text-danger">Est. Rp {{ number_format($item->hitungDenda(), 0, ',', '.') }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('admin.peminjaman.update_status') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="id_pinjam" value="{{ $item->id_pinjam }}">
                                        @if($item->status == 'Dipinjam')
                                            <input type="hidden" name="status" value="kembali">
                                            <button type="submit" class="btn btn-sm btn-success rounded-pill">
                                                <i class="bi bi-check-circle me-1"></i>Kembalikan
                                            </button>
                                        @else
                                            <input type="hidden" name="status" value="Dipinjam">
                                            <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill"
                                                    onclick="return confirm('Koreksi status menjadi Dipinjam?');">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i>Koreksi
                                            </button>
                                        @endif
                                    </form>
                                    @if($item->status == 'Dipinjam' && $item->perpanjang_status === 'diajukan')
                                        <form action="{{ route('admin.perpanjang') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="id_pinjam" value="{{ $item->id_pinjam }}">
                                            <input type="hidden" name="keputusan" value="disetujui">
                                            <button class="btn btn-sm btn-primary">Setujui +{{ $item->perpanjang_hari ?: config('perpus.lama_pinjam_hari', 7) }} hari</button>
                                        </form>
                                        <form action="{{ route('admin.perpanjang') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="id_pinjam" value="{{ $item->id_pinjam }}">
                                            <input type="hidden" name="keputusan" value="ditolak">
                                            <button class="btn btn-sm btn-outline-secondary">Tolak</button>
                                        </form>
                                    @endif
                                    @if((int) $item->denda > 0 && ! $item->denda_lunas)
                                        <form action="{{ route('admin.denda.lunas') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="id_pinjam" value="{{ $item->id_pinjam }}">
                                            <button class="btn btn-sm btn-outline-primary">Lunas + kwitansi</button>
                                        </form>
                                    @elseif((int) $item->denda > 0)
                                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.kwitansi', $item->id_pinjam) }}" target="_blank">Kwitansi</a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                                    <p class="mt-2 mb-0">Belum ada data peminjaman</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $peminjaman->links() }}</div>
            </div>
        </div>

    </div>
</div>

@include('inc.footer_adm')
