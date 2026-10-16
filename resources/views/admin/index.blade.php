<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Halaman login admin perpustakaan" />
        <meta name="keywords" content="login, admin, perpustakaan" />
        <meta name="author" content="Khairul" />
        <title>AREA ADMIN PERPUSTAKAAN</title>
        <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/plain.css') }}">
        <link rel="icon" href="{{ asset('assets/img/icons8-registration-26 (1).png') }}">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    </head>
<body>   
   <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4"></div>
            <div class="col-lg-4" style="padding: 50px 150px 50px 0px;">
                <div class="col p-4 bg-white" style="border: 1px solid #d0d5dd;">
                    <p class="h5 text-center fw-bold mb-1">Login Admin</p>
                    <p class="mb-4 text-secondary text-center">Masuk ke dashboard admin</p>
                        @if(session('message'))
                            <div class="alert alert-success fw-medium text-success mt-1">{{ session('message') }}</div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger fw-medium mt-1">{{ session('error') }}</div>
                        @endif
                    <form action="{{ route('admin.login') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="{{ old('email') }}"
                                   placeholder="Masukkan email admin" style="border-radius: 50px; border: 1px solid gray;" required>
                        </div>
                        <div class="col mb-4">
                            <label for="input_password" class="form-label">Kata Sandi</label>
                            <div class="input-group">
                                <input type="password" name="password" class="form-control" id="input_password" placeholder="Masukkan Kata Sandi" style="border-radius: 50px 0 0 50px; border: 1px solid gray;" required>
                                <button type="button" class="btn btn-outline-secondary" id="togglePassword" style="border-radius: 0 50px 50px 0; border: 1px solid gray; border-left: none;" title="Tampilkan/Sembunyikan password">
                                    <i class="bi bi-eye-fill" id="togglePasswordIcon"></i>
                                </button>
                            </div>
                        </div>
                        <!-- reCAPTCHA -->
                        <div class="mb-4">
                            @if($recaptchaRequired ?? true)
                            <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                            @endif
                        </div>
                        <div class="mb-4">
                            <input type="submit" class="btn col-12 rounded-pill text-white" value="Masuk" style="background-color: rgb(88, 41, 150);">
                        </div>
                    </form>
                    <div class="text-center pt-2 border-top">
                        <a href="{{ route('home') }}" class="text-decoration-none" style="color: rgb(88, 41, 150);">
                            <i class="bi bi-arrow-left me-1"></i>Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4"></div>
        </div>
    </div>

    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('input_password');
            const toggleIcon = document.getElementById('togglePasswordIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.replace('bi-eye-fill', 'bi-eye-slash-fill');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.replace('bi-eye-slash-fill', 'bi-eye-fill');
            }
        });
    </script>
</body>
</html>    