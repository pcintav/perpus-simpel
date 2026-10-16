@include('inc.header_adm')

<div class="container-fluid py-5 bg-light min-vh-100">
    <div class="container">
        <div class="col-md-8 mx-auto">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-4 text-center" style="color: rgb(88, 41, 150);">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Buku Baru
                    </h4>

                    @if(session('message'))
                        <div class="alert alert-success">{{ session('message') }}</div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.store_buku') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-medium">Judul Buku <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control" value="{{ old('judul') }}"
                                   placeholder="Masukkan judul buku" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Pengarang <span class="text-danger">*</span></label>
                                <input type="text" name="pengarang" class="form-control" value="{{ old('pengarang') }}"
                                       placeholder="Nama pengarang" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Penerbit <span class="text-danger">*</span></label>
                                <input type="text" name="penerbit" class="form-control" value="{{ old('penerbit') }}"
                                       placeholder="Nama penerbit" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Tahun Terbit <span class="text-danger">*</span></label>
                                <input type="number" name="tahun" class="form-control" value="{{ old('tahun') }}"
                                       placeholder="Contoh: 2024" min="1900" max="2099" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Stok <span class="text-danger">*</span></label>
                                <input type="number" name="stok" class="form-control" value="{{ old('stok', 1) }}"
                                       placeholder="Jumlah stok" min="0" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-medium">ISBN</label>
                                <input type="text" name="isbn" class="form-control" value="{{ old('isbn') }}" placeholder="Opsional">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-medium">Kategori</label>
                                <input type="text" name="kategori" class="form-control" value="{{ old('kategori') }}" placeholder="Contoh: Novel">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-medium">Rak</label>
                                <input type="text" name="rak" class="form-control" value="{{ old('rak') }}" placeholder="Contoh: A1">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-medium">Kondisi</label>
                                <select name="kondisi" class="form-select">
                                    @foreach(['Baik', 'Rusak', 'Hilang'] as $k)
                                        <option value="{{ $k }}" @selected(old('kondisi', 'Baik') === $k)>{{ $k }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Cover Buku <span class="text-danger">*</span></label>
                            <input type="file" name="bukuimg" class="form-control" accept="image/jpeg,image/jpg,image/png" required>
                            <small class="text-muted">Format: jpg, jpeg, png (wajib diisi)</small>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn text-white rounded-pill fw-medium"
                                    style="background: linear-gradient(135deg, rgb(88, 41, 150) 0%, rgb(125, 125, 219) 100%);">
                                <i class="bi bi-save me-2"></i>Simpan Buku
                            </button>
                            <a href="{{ route('admin.stock_buku') }}" class="btn btn-secondary rounded-pill fw-medium">
                                <i class="bi bi-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

@include('inc.footer_adm')
