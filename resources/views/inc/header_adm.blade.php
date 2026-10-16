<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Area Admin Perpustakaan" />
        <meta name="keywords" content="admin" />
        <meta name="author" content="Khairul" />
        <title>AREA ADMIN PERPUSTAKAAN</title>
        <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/plain.css') }}">
        <link rel="icon" href="{{ asset('assets/img/icons8-registration-26 (1).png') }}">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <script src="https://cdn.tiny.cloud/1/s30tcw2ifbtad90un1rsoed2lj7jec8m85q4hlmt553re67r/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>

    </head>
    <body>
        
    <!-- Modern Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light navbar-gradient shadow-sm sticky-top">
    <div class="container-fluid px-4">

        <!-- Logo & Brand -->
        <a class="navbar-brand d-flex align-items-center" href="{{ route('admin.index') }}">
            <div class="rounded-circle p-2 me-3">
                <img src="{{ $logoUrl ?? asset('assets/img/icons8-registration-26 (1).png') }}" 
                     alt="Logo" 
                     class="img-fluid" 
                     style="width: 40px; height: 40px; object-fit: contain;">
            </div>
            <div class="d-none d-md-block">
                <h5 class="mb-0 fw-bold" style="color: #0f766e;">PERPUSTAKAAN</h5>
                <small class="text-muted">{{ $namaSekolah ?? 'Admin' }}</small>
            </div>
        </a>

        <!-- Mobile Togele Button -->
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

                <!-- Dashboard -->
                <li class="nav-item mx-2">
                    <a class="nav-link px-3 py-2" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2 me-2"></i>
                        <span>DASHBOARD</span>
                    </a>
                </li>

                <!-- List Buku -->
                <li class="nav-item mx-2">
                    <a class="nav-link px-3 py-2" href="{{ route('admin.stock_buku') }}">
                        <i class="bi bi-clipboard-data-fill me-2"></i>
                        <span>LIST BUKU</span>
                    </a>
                </li>
       
                <!-- List Pengguna -->
                <li class="nav-item mx-2">
                    <a class="nav-link px-3 py-2" href="{{ route('admin.list_user') }}">
                        <i class="bi bi-files me-2"></i>
                        <span>LIST USER</span>
                    </a>
                </li>
                
                <!-- List Kontak -->
                <li class="nav-item mx-2">
                    <a class="nav-link px-3 py-2" href="{{ route('admin.peminjaman') }}">
                        <i class="bi bi-files me-2"></i>
                        <span>PEMINJAMAN</span>
                    </a>
                </li>

                <li class="nav-item mx-2">
                    <a class="nav-link px-3 py-2" href="{{ route('admin.laporan') }}">
                        <i class="bi bi-bar-chart-fill me-2"></i>
                        <span>LAPORAN</span>
                    </a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link px-3 py-2" href="{{ route('admin.rekap_kelas') }}">
                        <i class="bi bi-people-fill me-2"></i>
                        <span>REKAP KELAS</span>
                    </a>
                </li>

                <!-- Edit Akun Admin -->
                <li class="nav-item mx-2">
                    <a class="nav-link px-3 py-2" href="{{ route('admin.edit_admin') }}">
                        <i class="bi bi-person-plus-fill me-2"></i>
                        <span>EDIT AKUN ADMIN</span>
                    </a>
                </li>

                <!-- Logout Button (POST + CSRF) -->
                <li class="nav-item ms-lg-3 mt-3 mt-lg-0">
                    <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm text-white px-4 py-2 rounded-pill shadow-sm border-0"
                           style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            <span>Keluar</span>
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>