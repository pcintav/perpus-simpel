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

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1" style="color: rgb(88, 41, 150);">
                    <i class="bi bi-journal-bookmark-fill me-2"></i>Katalog Buku
                </h4>
                <p class="text-muted mb-0">Temukan buku yang ingin Anda pinjam</p>
            </div>
        </div>

        <!-- Search & Filter -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('user.perpus') }}" class="row g-3">
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
                                style="background: linear-gradient(135deg, rgb(88, 41, 150) 0%, rgb(125, 125, 219) 100%);">
                            <i class="bi bi-funnel-fill"></i> Filter
                        </button>
                    </div>
                </form>
                @if(request()->hasAny(['q','tahun','penerbit','pengarang']))
                    <div class="mt-3">
                        <a href="{{ route('user.perpus') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i>Reset Filter
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tabel List Katalog Buku -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Cover</th>
                                <th>Judul</th>
                                <th>Pengarang</th>
                                <th>Penerbit</th>
                                <th>Tahun</th>
                                <th>Stok</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($buku as $no => $item)
                            @php
                                $stokTersedia = $item->stok - ($item->dipinjam_count ?? 0);
                            @endphp
                            <tr class="buku-row" role="button" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#modalBuku"
                                data-id="{{ $item->id_buku }}"
                                data-judul="{{ $item->judul }}"
                                data-pengarang="{{ $item->pengarang }}"
                                data-penerbit="{{ $item->penerbit }}"
                                data-tahun="{{ $item->tahun }}"
                                data-stok="{{ $item->stok }}"
                                data-dipinjam="{{ $item->dipinjam_count ?? 0 }}"
                                data-tersedia="{{ ($stokTersedia > 0 && ($item->kondisi ?? 'Baik') === 'Baik') ? '1' : '0' }}"
                                data-kondisi="{{ $item->kondisi ?? 'Baik' }}"
                                data-img="{{ $item->bukuimg_url ? asset($item->bukuimg_url) : '' }}">
                                <td>{{ $buku->firstItem() + $loop->index }}</td>
                                <td>
                                    @if($item->bukuimg_url)
                                        <img src="{{ asset($item->bukuimg_url) }}" alt="" class="rounded" style="height: 45px; width: 35px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 45px; width: 35px;">
                                            <i class="bi bi-book text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="fw-semibold">{{ $item->judul }}</td>
                                <td>{{ $item->pengarang }}</td>
                                <td>{{ $item->penerbit }}</td>
                                <td>{{ $item->tahun }}</td>
                                <td>
                                    @if(($item->kondisi ?? 'Baik') !== 'Baik')
                                        <span class="badge bg-secondary">{{ $item->kondisi }}</span>
                                    @elseif($stokTersedia > 0)
                                        <span class="badge bg-success">{{ $stokTersedia }} tersedia</span>
                                    @else
                                        <span class="badge bg-secondary">Habis</span>
                                    @endif
                                </td>
                                <td><i class="bi bi-chevron-right text-muted"></i></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
                                    <h6 class="mt-3 fw-semibold text-muted">Tidak ditemukan</h6>
                                    <p class="text-muted mb-0">Tidak ada buku yang sesuai dengan filter Anda.</p>
                                    <a href="{{ route('user.perpus') }}" class="btn btn-sm btn-outline-primary mt-3">Reset Filter</a>
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

<!-- Modal Keterangan Buku -->
<div class="modal fade" id="modalBuku" tabindex="-1" aria-labelledby="modalBukuLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header text-white border-0"
                 style="background: linear-gradient(135deg, rgb(88, 41, 150) 0%, rgb(125, 125, 219) 100%);">
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
                                <td class="text-muted" style="width: 100px;">Pengarang</td>
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
                                <td class="text-muted">Stok</td>
                                <td id="modalStok"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-top" id="modalFormWrap">
                    <form id="formPinjam" method="POST" action="{{ route('user.pinjam') }}">
                        @csrf
                        <input type="hidden" name="id_buku" id="modalIdBuku">
                        <button type="submit" class="btn btn-primary w-100">Pinjam Buku</button>
                    </form>
                    <form id="formReservasi" method="POST" action="{{ route('user.reservasi') }}" style="display:none;">
                        @csrf
                        <input type="hidden" name="id_buku" id="modalIdReservasi">
                        <button type="submit" class="btn btn-outline-primary w-100">Reservasi (antrian)</button>
                    </form>
                    <p id="modalStokHabis" class="text-danger small mb-0 text-center mt-2" style="display: none;">
                        Stok habis. Ajukan reservasi untuk mendapat giliran.
                    </p>
                    <p id="modalKondisi" class="text-danger small mb-0 text-center mt-2" style="display: none;">
                        Buku ini tidak bisa dipinjam karena kondisinya rusak atau hilang.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('modalBuku').addEventListener('show.bs.modal', function(e) {
    const row = e.relatedTarget ? e.relatedTarget.closest('tr.buku-row') : null;
    if (!row) return;

    const id = row.getAttribute('data-id');
    const judul = row.getAttribute('data-judul');
    const pengarang = row.getAttribute('data-pengarang');
    const penerbit = row.getAttribute('data-penerbit');
    const tahun = row.getAttribute('data-tahun');
    const stok = parseInt(row.getAttribute('data-stok') || 0);
    const dipinjam = parseInt(row.getAttribute('data-dipinjam') || 0);
    let img = (row.getAttribute('data-img') || '').trim();
    if (!img) {
        const coverImg = row.querySelector('td img[src]');
        if (coverImg) img = coverImg.getAttribute('src') || '';
    }
    const tersedia = row.getAttribute('data-tersedia') === '1';
    const kondisi = row.getAttribute('data-kondisi') || 'Baik';

    document.getElementById('modalIdBuku').value = id;
    document.getElementById('modalIdReservasi').value = id;
    document.getElementById('modalJudul').textContent = judul || '-';
    document.getElementById('modalPengarang').textContent = pengarang || '-';
    document.getElementById('modalPenerbit').textContent = penerbit || '-';
    document.getElementById('modalTahun').textContent = tahun || '-';
    document.getElementById('modalStok').innerHTML = (stok - dipinjam) + ' tersedia';

    const modalImg = document.getElementById('modalImg');
    const modalPlaceholder = document.getElementById('modalImgPlaceholder');
    if (img && img.length > 0) {
        modalImg.src = img;
        modalImg.style.display = 'block';
        modalPlaceholder.style.display = 'none';
    } else {
        modalImg.removeAttribute('src');
        modalImg.style.display = 'none';
        modalPlaceholder.style.display = 'flex';
    }

    const formPinjam = document.getElementById('formPinjam');
    const formReservasi = document.getElementById('formReservasi');
    const stokHabis = document.getElementById('modalStokHabis');
    const modalKondisi = document.getElementById('modalKondisi');
    formPinjam.style.display = 'none';
    formReservasi.style.display = 'none';
    stokHabis.style.display = 'none';
    modalKondisi.style.display = 'none';
    if (kondisi !== 'Baik') {
        modalKondisi.style.display = 'block';
    } else if (tersedia) {
        formPinjam.style.display = 'block';
    } else {
        formReservasi.style.display = 'block';
        stokHabis.style.display = 'block';
    }
});
</script>

@include('inc.footer_user')
