<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Area Anggota - Perpustakaan</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plain.css') }}">
    <link rel="icon" href="{{ asset('assets/img/icons8-registration-26 (1).png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light navbar-gradient shadow-sm sticky-top">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('user.dashboard') }}">
            <img src="{{ $logoUrl ?? asset('assets/img/virtual-assistant.png') }}" alt="Logo" class="img-fluid me-2" style="width: 40px; height: 40px; object-fit: contain;">
            <div class="d-none d-md-block">
                <h5 class="mb-0 fw-bold" style="color: #0f766e;">PERPUSTAKAAN</h5>
                <small class="text-muted">{{ $namaSekolah ?? 'Area Anggota' }}</small>
            </div>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item mx-2">
                    <a class="nav-link px-3 py-2" href="{{ route('user.dashboard') }}">
                        <i class="bi bi-house-door-fill me-2"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link px-3 py-2" href="{{ route('user.perpus') }}">
                        <i class="bi bi-book me-2"></i>Katalog Buku
                    </a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link px-3 py-2" href="{{ route('user.kartu') }}" target="_blank">
                        <i class="bi bi-person-badge me-2"></i>Kartu
                    </a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link px-3 py-2" href="{{ route('user.edit_akun') }}">
                        <i class="bi bi-person-gear me-2"></i>Edit Akun
                    </a>
                </li>
                <li class="nav-item ms-lg-3 mt-3 mt-lg-0">
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm text-white px-4 py-2 rounded-pill border-0"
                           style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);">
                            <i class="bi bi-box-arrow-right me-2"></i>Keluar
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
