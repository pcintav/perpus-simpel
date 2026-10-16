@include('inc.header')

<div class="container-fluid min-vh-100 d-flex align-items-center bg-light py-5">
    <div class="container">
        <div class="row g-0 shadow-lg rounded-4 overflow-hidden bg-white">

            <!-- Left Side -->
            <div class="col-lg-4 d-none d-lg-flex flex-column justify-content-center align-items-center p-5 text-white text-center"
                 style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 50%, #134e4a 100%);">

                <div class="mb-4">
                    <div class="bg-white bg-opacity-25 rounded-circle p-4 d-inline-block mb-3">
                        <i class="bi bi-person-plus-fill" style="font-size: 4rem;"></i>
                    </div>
                </div>
                <h2 class="h4 fw-bold mb-3">Daftar Anggota</h2>
                <p class="mb-0 px-3">
                    Isi formulir untuk menjadi anggota perpustakaan.
                </p>
            </div>

            <!-- Right Side - Form -->
            <div class="col-lg-8">
                <div class="p-5">

                    <div class="d-lg-none text-center mb-4">
                        <i class="bi bi-person-plus-fill" style="font-size: 3rem; color: #0f766e;"></i>
                        <h2 class="mt-2" style="color: #0f766e;">DAFTAR ANGGOTA</h2>
                    </div>

                    <div class="mb-4">
                        <h3 class="fw-bold" style="color: #0f766e;">
                            <i class="bi bi-pencil-square me-2"></i>Formulir Pendaftaran
                        </h3>
                        <p class="text-muted">Lengkapi data diri untuk menjadi anggota perpustakaan</p>
                    </div>

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

                    <form action="{{ route('store_daftar') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" value="{{ old('nama') }}"
                                   placeholder="Masukkan nama lengkap" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Kelas <span class="text-danger">*</span></label>
                                <input type="text" name="kelas" class="form-control" value="{{ old('kelas') }}"
                                       placeholder="Contoh: X RPL 1" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">NIS <span class="text-danger">*</span></label>
                                <input type="text" name="nis" class="form-control" value="{{ old('nis') }}"
                                       placeholder="5-7 digit angka" pattern="[0-9]{5,7}" minlength="5" maxlength="7" required>
                                <small class="text-muted">Minimal 5 angka, maksimal 7 angka</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                                   placeholder="email@contoh.com" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nomor Telepon <span class="text-danger">*</span></label>
                            <input type="tel" name="telp" class="form-control" value="{{ old('telp') }}"
                                   placeholder="08123456789" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Alamat <span class="text-danger">*</span></label>
                            <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat lengkap" required>{{ old('alamat') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Min. 8 karakter"
                                   minlength="8" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Konfirmasi Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password"
                                   minlength="8" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Foto Profil <span class="text-danger">*</span></label>
                            <input type="file" name="photo" class="form-control" accept="image/jpeg,image/jpg,image/png" required>
                            <small class="text-muted">Format: jpg, jpeg, png (wajib diunggah)</small>
                        </div>

                        <div class="mb-4">
                            @if($recaptchaRequired ?? true)
                            <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                            @endif
                            @error('g-recaptcha-response')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 mb-4">
                            <button type="submit" name="submit" class="btn btn-lg text-white fw-semibold py-3"
                                    style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);">
                                <i class="bi bi-check-circle me-2"></i>Daftar Sekarang
                            </button>
                        </div>

                        <div class="text-center pt-3 border-top">
                            <p class="text-muted mb-0">
                                Sudah memiliki akun?
                                <a href="{{ route('home') }}" class="fw-bold text-decoration-none"
                                   style="color: #0f766e;">Masuk di sini</a>
                            </p>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

@include('inc.footer')
