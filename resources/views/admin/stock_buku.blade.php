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

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h4 class="fw-bold mb-1" style="color: #0f766e;">
                    <i class="bi bi-book-fill me-2"></i>Stok Buku
                </h4>
                <p class="text-muted mb-0">Kelola data buku perpustakaan</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.peminjaman') }}" class="btn btn-outline-primary rounded-pill">
                    <i class="bi bi-journal-check me-2"></i>Data Peminjaman
                </a>
                <a href="{{ route('admin.tambah_buku') }}" class="btn text-white rounded-pill"
                   style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Buku
                </a>
            </div>
        </div>

        <!-- Search & Filter -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('admin.stock_buku') }}" class="row g-3">
                    <div class="col-12 col-md-5">
                        <label class="form-label small text-muted"><i class="bi bi-search me-1"></i>Cari</label>
                        <input type="text" name="q" class="form-control" placeholder="Judul, pengarang, atau penerbit..."
                               value="{{ request('q') }}">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small text-muted"><i class="bi bi-calendar3 me-1"></i>Tahun</label>
                        <select name="tahun" class="form-select">
                            <option value="">Semua Tahun</option>
                            @foreach($listTahun as $t)
                                <option value="{{ $t }}" {{ request('tahun') == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small text-muted"><i class="bi bi-building me-1"></i>Penerbit</label>
                        <select name="penerbit" class="form-select">
                            <option value="">Semua Penerbit</option>
                            @foreach($listPenerbit as $p)
                                <option value="{{ $p }}" {{ request('penerbit') == $p ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small text-muted"><i class="bi bi-person me-1"></i>Pengarang</label>
                        <select name="pengarang" class="form-select">
                            <option value="">Semua Pengarang</option>
                            @foreach($listPengarang as $p)
                                <option value="{{ $p }}" {{ request('pengarang') == $p ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn text-white w-100 rounded-pill"
                                style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);">
                            <i class="bi bi-funnel-fill"></i> Filter
                        </button>
                    </div>
                </form>
                @if(request()->hasAny(['q','tahun','penerbit','pengarang']))
                    <div class="mt-3">
                        <a href="{{ route('admin.stock_buku') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i>Reset Filter
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Table -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Kode</th>
                                <th>Cover</th>
                                <th>Judul</th>
                                <th>Pengarang</th>
                                <th>Penerbit</th>
                                <th>Tahun</th>
                                <th>Stok</th>
                                <th>Dipinjam</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($buku as $no => $item)
                            <tr class="buku-row">
                                <td>{{ $buku->firstItem() + $loop->index }}</td>
                                <td>
                                    @if($item->bukuimg_url)
                                        <img src="{{ asset($item->bukuimg_url) }}" alt="{{ $item->judul }}"
                                             class="rounded btn-cover-buku" style="height: 50px; width: 40px; object-fit: cover; cursor: pointer;"
                                             data-bs-toggle="modal" data-bs-target="#modalFotoBuku"
                                             data-img-url="{{ asset($item->bukuimg_url) }}"
                                             data-judul="{{ $item->judul }}">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 50px; width: 40px;">
                                            <i class="bi bi-book text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="fw-semibold">{{ $item->judul }}</td>
                                <td>{{ $item->pengarang }}</td>
                                <td>{{ $item->penerbit }}</td>
                                <td>{{ $item->tahun }}</td>
                                <td><span class="badge bg-primary">{{ $item->stok }}</span></td>
                                <td><span class="badge bg-warning text-dark">{{ $item->dipinjam_count ?? 0 }}</span></td>
                                <td>
                                    <div class="d-flex gap-1 flex-wrap">
                                        <a href="{{ route('admin.edit_buku', $item->id_buku) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                            <i class="bi bi-pencil-square me-1"></i>Edit
                                        </a>
                                        <form action="{{ route('admin.hapus_buku', $item->id_buku) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus buku ini beserta seluruh catatan peminjamannya? Tindakan tidak dapat dibatalkan.');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">
                                                <i class="bi bi-trash me-1"></i>Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
                                    <p class="mt-2 mb-0">Tidak ada data buku yang sesuai dengan filter</p>
                                    <a href="{{ route('admin.stock_buku') }}" class="btn btn-sm btn-outline-primary mt-2 me-1">Reset Filter</a>
                                    <a href="{{ route('admin.tambah_buku') }}" class="btn btn-sm btn-primary mt-2">Tambah Buku</a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $buku->links() }}</div>
            </div>
        </div>

    </div>
</div>

<!-- Modal Foto Buku -->
<div class="modal fade" id="modalFotoBuku" tabindex="-1" aria-labelledby="modalFotoBukuLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);">
                <h5 class="modal-title text-white fw-bold" id="modalFotoBukuLabel">
                    <i class="bi bi-image-fill me-2"></i>Cover Buku
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <p class="fw-semibold mb-3 text-secondary" id="modalJudulBuku"></p>
                <img id="modalImgBuku" src="" alt="Cover buku" class="img-fluid rounded-3 shadow-sm" style="max-height: 70vh;">
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.btn-cover-buku').forEach(img => {
        img.addEventListener('click', function() {
            document.getElementById('modalImgBuku').src = this.getAttribute('data-img-url');
            document.getElementById('modalJudulBuku').textContent = this.getAttribute('data-judul');
        });
    });
</script>

@include('inc.footer_adm')
