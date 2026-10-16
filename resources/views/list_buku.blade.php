@include('inc.header')

<div class="container-fluid bg-light min-vh-100 py-5">
    <div class="container">

        @if(session('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Header -->
        <div class="mb-4">
            <h4 class="fw-bold mb-1" style="color: #0f766e;">
                <i class="bi bi-book-fill me-2"></i>List Buku Perpustakaan
            </h4>
            <p class="text-muted mb-0">Daftar buku yang tersedia di perpustakaan</p>
        </div>

        <!-- Search & Filter -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('list_buku') }}" class="row g-3">
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
                        <a href="{{ route('list_buku') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i>Reset Filter
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- List Buku - Card Grid -->
        <div class="row" id="bukuContainer">
            @forelse($buku as $item)
            @php $dipinjam = $item->dipinjam_count ?? 0; $tersisa = $item->stok - $dipinjam; @endphp
            <div class="col-md-4 col-lg-3 mb-4 buku-item">
                <div class="card border-0 shadow-sm rounded-4 h-100 buku-card" role="button" style="cursor: pointer;"
                     data-bs-toggle="modal" data-bs-target="#modalBuku"
                     data-judul="{{ $item->judul }}"
                     data-pengarang="{{ $item->pengarang }}"
                     data-penerbit="{{ $item->penerbit }}"
                     data-tahun="{{ $item->tahun }}"
                     data-stok="{{ $item->stok }}"
                     data-dipinjam="{{ $dipinjam }}"
                     data-tersisa="{{ $tersisa }}"
                     data-img="{{ $item->bukuimg_url ? asset($item->bukuimg_url) : '' }}">
                    <div class="card-body p-3">
                        @if($item->bukuimg_url)
                            <img src="{{ asset($item->bukuimg_url) }}" class="card-img-top rounded-3 mb-2" alt="{{ $item->judul }}"
                                 style="height: 180px; object-fit: cover;">
                        @else
                            <div class="bg-light rounded-3 d-flex align-items-center justify-content-center mb-2" style="height: 180px;">
                                <i class="bi bi-book text-muted" style="font-size: 4rem;"></i>
                            </div>
                        @endif
                        <h6 class="fw-bold text-truncate" style="color: #0f766e;" title="{{ $item->judul }}">{{ $item->judul }}</h6>
                        <p class="small text-muted mb-1"><i class="bi bi-person me-1"></i>{{ $item->pengarang }}</p>
                        <p class="small mb-0">
                            <span class="badge bg-primary">{{ $item->tahun }}</span>
                            <span class="badge bg-success">Tersisa: {{ $tersisa }}</span>
                            <span class="badge bg-warning text-dark">Dipinjam: {{ $dipinjam }}</span>
                        </p>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-search" style="font-size: 5rem; color: #0f766e; opacity: 0.2;"></i>
                <h5 class="mt-4 fw-bold text-muted">Tidak Ada Buku Ditemukan</h5>
                <p class="text-muted mb-3">Tidak ada buku yang sesuai dengan filter Anda.</p>
                <a href="{{ route('list_buku') }}" class="btn btn-outline-primary rounded-pill">Reset Filter</a>
            </div>
            @endforelse
        </div>
        <div class="mt-4 d-flex justify-content-center">{{ $buku->links() }}</div>

    </div>
</div>

<!-- Modal Detail Buku -->
<div class="modal fade" id="modalBuku" tabindex="-1" aria-labelledby="modalBukuLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header text-white border-0"
                 style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);">
                <h5 class="modal-title fw-bold" id="modalBukuLabel">
                    <i class="bi bi-journal-bookmark-fill me-2"></i>Detail Buku
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-4 text-center">
                        <div class="position-relative mx-auto" style="width: 120px; height: 180px;">
                            <img id="modalImg" src="" alt="Cover" class="position-absolute top-0 start-0 w-100 h-100 rounded shadow-sm" style="object-fit: cover; z-index: 2; display: none;">
                            <div id="modalImgPlaceholder" class="position-absolute top-0 start-0 w-100 h-100 bg-light rounded d-flex align-items-center justify-content-center" style="z-index: 1; display: flex;">
                                <i class="bi bi-book text-muted" style="font-size: 3rem;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-8">
                        <h5 class="fw-bold mb-2" id="modalJudul"></h5>
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted" style="width: 120px;">Pengarang</td>
                                <td id="modalPengarang"></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Penerbit</td>
                                <td id="modalPenerbit"></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tahun</td>
                                <td id="modalTahun"></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tersisa</td>
                                <td id="modalTersisa"></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Dipinjam</td>
                                <td id="modalDipinjam"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-top">
                    <a href="{{ route('daftar') }}" class="btn w-100 text-white rounded-pill py-2 text-center"
                       style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);">
                        <i class="bi bi-person-plus-fill me-2"></i>Daftar untuk Meminjam Buku
                    </a>
                    <p class="text-muted small text-center mt-2 mb-0">Sudah punya akun? <a href="{{ route('home') }}">Masuk di sini</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('modalBuku').addEventListener('show.bs.modal', function(e) {
    const card = e.relatedTarget ? e.relatedTarget.closest('.buku-card') : null;
    if (!card) return;

    const judul = card.getAttribute('data-judul') || '-';
    const pengarang = card.getAttribute('data-pengarang') || '-';
    const penerbit = card.getAttribute('data-penerbit') || '-';
    const tahun = card.getAttribute('data-tahun') || '-';
    const tersisa = parseInt(card.getAttribute('data-tersisa') || 0);
    const dipinjam = parseInt(card.getAttribute('data-dipinjam') || 0);
    const img = (card.getAttribute('data-img') || '').trim();
    const coverImg = card.querySelector('img[src]');
    const imgUrl = img || (coverImg ? coverImg.getAttribute('src') : '');

    document.getElementById('modalJudul').textContent = judul;
    document.getElementById('modalPengarang').textContent = pengarang;
    document.getElementById('modalPenerbit').textContent = penerbit;
    document.getElementById('modalTahun').textContent = tahun;
    document.getElementById('modalTersisa').innerHTML = '<span class="badge bg-success">' + tersisa + ' eksemplar</span>';
    document.getElementById('modalDipinjam').innerHTML = '<span class="badge bg-warning text-dark">' + dipinjam + ' eksemplar</span>';

    const modalImg = document.getElementById('modalImg');
    const modalPlaceholder = document.getElementById('modalImgPlaceholder');
    if (imgUrl && imgUrl.length > 0) {
        modalImg.src = imgUrl;
        modalImg.style.display = 'block';
        modalPlaceholder.style.display = 'none';
    } else {
        modalImg.removeAttribute('src');
        modalImg.style.display = 'none';
        modalPlaceholder.style.display = 'flex';
    }
});
</script>

@include('inc.footer')
