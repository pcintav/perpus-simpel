<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem perpustakaan sederhana">
    <meta name="keywords" content="perpustakaan, siswa, sekolah">
    <meta name="author" content="">

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plain.css') }}">
    <link rel="icon" href="{{ asset('assets/img/icons8-registration-26 (1).png') }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="https://cdn.tiny.cloud/1/s30tcw2ifbtad90un1rsoed21j7jec8m85q4h1mt553re67r/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>

    <title>Sistem Perpustakaan</title>

</head>
<body>

<!-- Modern Navbar -->
<nav class="navbar navbar-expand-lg navbar-light navbar-gradient shadow-sm sticky-top">
    <div class="container-fluid px-4">
        
        <!-- Logo & Brand -->
        <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
            <div class="rounded-circle p-2 me-3">
                <img src="{{ $logoUrl ?? asset('assets/img/virtual-assistant.png') }}"
                     alt="Logo" 
                     class="img-fluid"
                     style="width: 40px; height: 40px; object-fit: contain;">
            </div>
            <div class="d-none d-md-block">
                <h5 class="mb-0 fw-bold" style="color: #0f766e;">PERPUSTAKAAN</h5>
                <small class="text-muted">{{ $namaSekolah ?? 'Online' }}</small>
            </div>
        </a>

        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler border-0 shadow-none" 
                type="button" 
                data-bs-toggle="collapse" 
                data-bs-target="#navbarNav" 
                aria-controls="navbarNav" 
                aria-expanded="false" 
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Menu -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                
                <!-- Beranda -->
                <li class="nav-item mx-2">
                    <a class="nav-link px-3 py-2" href="{{ route('home') }}">
                        <i class="bi bi-house-door-fill me-2"></i>
                        <span>Beranda</span>
                    </a>
                </li>

                <!-- List Buku -->
                <li class="nav-item mx-2">
                    <a class="nav-link px-3 py-2" href="{{ route('list_buku') }}">
                        <i class="bi bi-book-fill me-2"></i>
                        <span>List Buku Perpustakaan</span>
                    </a>
                </li>

                <!-- Daftar Button -->
                <li class="nav-item ms-lg-3 mt-3 mt-lg-0">
                    <a href="{{ route('daftar') }}" 
                       class="btn btn-sm text-white px-4 py-2 rounded-pill shadow-sm"
                       style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);">
                        <i class="bi bi-box-arrow-in-right me-2"></i>
                        <span>DAFTAR</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
