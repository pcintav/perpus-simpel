<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::post('/login', [PageController::class, 'login'])->name('login');
Route::get('/daftar', [PageController::class, 'daftar'])->name('daftar');
Route::post('/daftar', [PageController::class, 'store_daftar'])->name('store_daftar');
Route::get('/list_buku', [PageController::class, 'list_buku'])->name('list_buku');
Route::post('/logout', [PageController::class, 'logout'])->name('logout');

Route::prefix('user')->name('user.')->middleware('anggota')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/perpus', [UserController::class, 'perpus'])->name('perpus');
        Route::post('/pinjam', [UserController::class, 'pinjam'])->name('pinjam');
        Route::post('/perpanjang', [UserController::class, 'ajukanPerpanjang'])->name('perpanjang');
        Route::post('/reservasi', [UserController::class, 'reservasi'])->name('reservasi');
        Route::post('/reservasi/batal', [UserController::class, 'batalReservasi'])->name('reservasi.batal');
        Route::get('/kartu', [UserController::class, 'kartu'])->name('kartu');
    Route::get('/edit_akun', [UserController::class, 'edit_akun'])->name('edit_akun');
    Route::post('/update_akun', [UserController::class, 'update_akun'])->name('update_akun');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/index', [AdminController::class, 'index'])->name('index');
    Route::post('/login', [AdminController::class, 'login'])->name('login');
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

    Route::middleware('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/list_user', [AdminController::class, 'list_user'])->name('list_user');
        Route::get('/edit_user/{id}', [AdminController::class, 'edit_user'])->name('edit_user');
        Route::post('/update_user', [AdminController::class, 'update_user'])->name('update_user');
        Route::post('/anggota/status', [AdminController::class, 'update_status_anggota'])->name('anggota.status');
        Route::post('/hapus_user/{id}', [AdminController::class, 'hapus_user'])->name('hapus_user');
        Route::get('/stock_buku', [AdminController::class, 'stock_buku'])->name('stock_buku');
        Route::get('/edit_buku/{id}', [AdminController::class, 'edit_buku'])->name('edit_buku');
        Route::post('/update_buku', [AdminController::class, 'update_buku'])->name('update_buku');
        Route::post('/hapus_buku/{id}', [AdminController::class, 'hapus_buku'])->name('hapus_buku');
        Route::get('/tambah_buku', [AdminController::class, 'tambah_buku'])->name('tambah_buku');
        Route::post('/tambah_buku', [AdminController::class, 'store_buku'])->name('store_buku');
        Route::post('/peminjaman/update_status', [AdminController::class, 'update_status_peminjaman'])->name('peminjaman.update_status');
        Route::get('/peminjaman', [AdminController::class, 'peminjaman'])->name('peminjaman');
        Route::post('/perpanjang', [AdminController::class, 'prosesPerpanjang'])->name('perpanjang');
        Route::post('/denda/lunas', [AdminController::class, 'lunasiDenda'])->name('denda.lunas');
        Route::get('/kwitansi/{id}', [AdminController::class, 'kwitansi'])->name('kwitansi');
        Route::get('/kartu/{id}', [AdminController::class, 'kartu'])->name('kartu');
        Route::get('/rekap-kelas', [AdminController::class, 'rekapKelas'])->name('rekap_kelas');
        Route::get('/laporan', [AdminController::class, 'laporan'])->name('laporan');
        Route::get('/export/peminjaman', [AdminController::class, 'export_peminjaman'])->name('export.peminjaman');
        Route::get('/edit_admin', [AdminController::class, 'edit_admin'])->name('edit_admin');
        Route::post('/edit_admin', [AdminController::class, 'update_admin'])->name('update_admin');
    });
});
