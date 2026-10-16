@include('inc.header_user')

<div class="container-fluid bg-light min-vh-100 py-5">
    <div class="container">
        <div class="col-lg-8 mx-auto">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-4" style="color: rgb(88, 41, 150);">
                        <i class="bi bi-person-gear me-2"></i>Edit Akun
                    </h4>
                    <p class="text-muted small mb-4">Perbarui data profil Anda</p>

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

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('user.update_akun') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-medium">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" value="{{ old('nama', $anggota->nama) }}"
                                   placeholder="Masukkan nama lengkap" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Kelas <span class="text-danger">*</span></label>
                                <input type="text" name="kelas" class="form-control" value="{{ old('kelas', $anggota->kelas) }}"
                                       placeholder="Contoh: X RPL 1" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">NIS <span class="text-danger">*</span></label>
                                <input type="text" name="nis" class="form-control" value="{{ old('nis', $anggota->nis) }}"
                                       placeholder="5-7 digit angka" pattern="[0-9]{5,7}" minlength="5" maxlength="7" required>
                                <small class="text-muted">Minimal 5 angka, maksimal 7 angka</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $anggota->email) }}"
                                   placeholder="email@contoh.com" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Nomor Telepon <span class="text-danger">*</span></label>
                            <input type="tel" name="telp" class="form-control" value="{{ old('telp', $anggota->telp) }}"
                                   placeholder="08123456789" required>
                            <small class="text-muted">10-15 digit angka</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Alamat <span class="text-danger">*</span></label>
                            <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat lengkap" required>{{ old('alamat', $anggota->alamat) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Password Baru (kosongkan jika tidak ingin mengubah)</label>
                            <input type="password" name="password" class="form-control" placeholder="Min. 8 karakter">
                            <input type="password" name="password_confirmation" class="form-control mt-2" placeholder="Konfirmasi password">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-medium">Foto Profil</label>
                            @if($anggota->photo_url)
                                <div class="mb-2">
                                    <img src="{{ asset($anggota->photo_url) }}" alt="Foto" class="img-thumbnail rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                                    <span class="d-block small text-muted mt-1">Foto saat ini (unggah baru untuk mengubah)</span>
                                </div>
                            @endif
                            <input type="file" name="photo" class="form-control" accept="image/jpeg,image/jpg,image/png">
                            <small class="text-muted">Format: jpg, jpeg, png. Kosongkan jika tidak ingin mengubah.</small>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn text-white rounded-pill fw-medium py-2"
                                    style="background: linear-gradient(135deg, rgb(88, 41, 150) 0%, rgb(125, 125, 219) 100%);">
                                <i class="bi bi-save me-2"></i>Simpan Perubahan
                            </button>
                            <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary rounded-pill">
                                <i class="bi bi-arrow-left me-2"></i>Kembali ke Dashboard
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('inc.footer_user')
