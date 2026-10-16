@include('inc.header')

<div class="container-fluid min-vh-100 d-flex align-items-center bg-light">
    <div class="container">
        <div class="row g-0 shadow-lg rounded-4 overflow-hidden bg-white">

            <!-- Left Side - Branding -->
            <div class="col-lg-6 d-none d-lg-flex flex-column justify-content-center align-items-center p-5"
                 style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 50%, #134e4a 100%);">

                <div class="text-white text-center">
                    <div class="mb-4">
                        <i class="bi bi-book-fill" style="font-size: 5rem;"></i>
                    </div>
                    <h1 class="h3 fw-bold mb-2">Sistem Perpustakaan</h1>
                    <p class="mb-0">Login anggota untuk meminjam buku.</p>

                    <div class="mt-5">
                        <img src="{{ asset('assets/img/sistem-sekolah.jpg') }}"
                             class="img-fluid rounded-3 shadow"
                             style="max-width: 80%; height: auto;"
                             alt="Ilustrasi Sistem"
                             onerror="this.style.display='none'">
                    </div>
                </div>
            </div>

            <!-- Right Side - Login Form -->
            <div class="col-lg-6 d-flex align-items-center">
                <div class="w-100 p-5">

                    <!-- Mobile Logo -->
                    <div class="d-lg-none text-center mb-4">
                        <i class="bi bi-book-fill" style="font-size: 3rem; color: #0f766e;"></i>
                        <h2 class="mt-2" style="color: #0f766e;">PERPUSTAKAAN ONLINE</h2>
                    </div>

                    <!-- Login Header -->
                    <div class="mb-5">
                        <h3 class="h5 fw-bold mb-2">Login Anggota</h3>
                        <p class="text-muted mb-0">Masukkan email atau NIS dan kata sandi.</p>
                    </div>

                    @if(session('message'))
                        <div class="alert alert-success fw-medium text-success">
                            {{ session('message') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger fw-medium">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Login Form Anggota -->
                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="exampleInputEmail" class="form-label fw-semibold">
                                <i class="bi bi-envelope-fill me-2"></i>Email / NIS
                            </label>
                            <input type="text" class="form-control form-control-lg" id="exampleInputEmail"
                                   name="email" value="{{ old('email') }}" placeholder="Masukkan email atau NIS" required>
                        </div>

                        <div class="mb-4">
                            <label for="input_password" class="form-label fw-semibold">
                                <i class="bi bi-lock-fill me-2"></i>Kata Sandi
                            </label>
                            <div class="input-group">
                                <input type="password" name="password" class="form-control form-control-lg"
                                       id="input_password" placeholder="Masukkan kata sandi" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                    <i class="bi bi-eye-fill" id="toggleIcon"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-4">
                            @if($recaptchaRequired ?? true)
                            <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                            @endif
                        </div>

                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-lg text-white fw-semibold py-3"
                                    style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%); border: none;">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                            </button>
                        </div>

                        <div class="text-center pt-3 border-top">
                            <p class="text-muted mb-0">
                                Belum memiliki akun?
                                <a href="{{ route('daftar') }}" class="fw-bold text-decoration-none"
                                   style="color: #0f766e;">Daftar Sekarang</a>
                            </p>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('input_password');
    const toggleIcon = document.getElementById('toggleIcon');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.replace('bi-eye-fill', 'bi-eye-slash-fill');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.replace('bi-eye-slash-fill', 'bi-eye-fill');
    }
}
</script>

@include('inc.footer')
