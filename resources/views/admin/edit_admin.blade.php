@include('inc.header_adm')

<div class="container-fluid py-5 bg-light min-vh-100">
    <div class="container">
        <div class="col-md-8 mx-auto">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-4 text-center" style="color: rgb(88, 41, 150);">
                        <i class="bi bi-person-gear me-2"></i>Edit Akun Admin
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

                    <form action="{{ route('admin.update_admin') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-medium">Nama Admin <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" value="{{ old('nama', $admin->nama) }}"
                                   placeholder="Nama lengkap" required>
                            <small class="text-muted">Hanya alfabet dan spasi</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $admin->email) }}"
                                   placeholder="email@contoh.com" required>
                            <small class="text-muted">Digunakan untuk login</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Nama Sekolah <span class="text-danger">*</span></label>
                            <input type="text" name="nama_sekolah" class="form-control" value="{{ old('nama_sekolah', $admin->nama_sekolah) }}"
                                   placeholder="Nama sekolah" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Alamat <span class="text-danger">*</span></label>
                            <input type="text" name="alamat" class="form-control" value="{{ old('alamat', $admin->alamat) }}"
                                   placeholder="Alamat lengkap" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Telepon <span class="text-danger">*</span></label>
                            <input type="tel" name="telepon" class="form-control" value="{{ old('telepon', $admin->telepon) }}"
                                   placeholder="08123456789" required>
                            <small class="text-muted">10-15 digit angka</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Password Baru (kosongkan jika tidak ingin mengubah)</label>
                            <input type="password" name="password" class="form-control" placeholder="Min. 8 karakter">
                            <input type="password" name="password_confirmation" class="form-control mt-2" placeholder="Konfirmasi password">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Logo/Foto Profil</label>
                            @if($admin->logo_url)
                                <div class="mb-2">
                                    <img src="{{ asset($admin->logo_url) }}" alt="Logo" class="img-thumbnail" style="max-height: 100px;">
                                    <span class="d-block small text-muted mt-1">Logo saat ini (unggah baru untuk mengubah)</span>
                                </div>
                            @endif
                            <input type="file" name="logo" class="form-control" accept="image/jpeg,image/jpg,image/png">
                            <small class="text-muted">Format: jpg, jpeg, png. Kosongkan jika tidak ingin mengubah.</small>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn text-white rounded-pill fw-medium"
                                    style="background: linear-gradient(135deg, rgb(88, 41, 150) 0%, rgb(125, 125, 219) 100%);">
                                <i class="bi bi-save me-2"></i>Simpan Perubahan
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary rounded-pill fw-medium">
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
