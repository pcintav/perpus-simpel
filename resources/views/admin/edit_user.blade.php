@include('inc.header_adm')

<div class="container-fluid py-5 bg-light min-vh-100">
    <div class="container">
        <div class="col-md-8 mx-auto">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-4 text-center" style="color: rgb(88, 41, 150);">
                        <i class="bi bi-pencil-square me-2"></i>Edit Data Anggota
                    </h4>

                    @if(session('message'))
                        <div class="alert alert-success">{{ session('message') }}</div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
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

                    <form action="{{ route('admin.update_user') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id_anggota" value="{{ $anggota->id_anggota }}">

                        <div class="mb-3">
                            <label class="form-label fw-medium">Nama</label>
                            <input type="text" name="nama" class="form-control" value="{{ old('nama', $anggota->nama) }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Kelas</label>
                                <input type="text" name="kelas" class="form-control" value="{{ old('kelas', $anggota->kelas) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">NIS</label>
                                <input type="text" name="nis" class="form-control" value="{{ old('nis', $anggota->nis) }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $anggota->email) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Telepon</label>
                            <input type="tel" name="telp" class="form-control" value="{{ old('telp', $anggota->telp) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="2" required>{{ old('alamat', $anggota->alamat) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Status Akun</label>
                                <select name="status" class="form-select" required>
                                    @foreach(['pending'=>'Pending','aktif'=>'Aktif','nonaktif'=>'Nonaktif'] as $val => $label)
                                        <option value="{{ $val }}" @selected(old('status', $anggota->status ?? 'aktif') === $val)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Password Baru (opsional)</label>
                                <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah" minlength="8">
                                <small class="text-muted">Untuk reset password anggota (lupa password)</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Foto Profil (opsional)</label>
                            @if($anggota->photo_url)
                                <div class="mb-2">
                                    <img src="{{ asset($anggota->photo_url) }}" alt="Foto" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                            @endif
                            <input type="file" name="photo" class="form-control" accept="image/*">
                            <small class="text-muted">Kosongkan jika tidak ingin mengganti foto</small>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn text-white rounded-pill fw-medium"
                                    style="background: linear-gradient(135deg, rgb(88, 41, 150) 0%, rgb(125, 125, 219) 100%);">
                                <i class="bi bi-save me-2"></i>Simpan Perubahan
                            </button>
                            <a href="{{ route('admin.list_user') }}" class="btn btn-secondary rounded-pill fw-medium">
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
