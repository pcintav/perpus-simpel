# Alur Coding Project - Sistem Perpustakaan

Panduan sistematis untuk membangun project ini dari awal. Urutan file yang dibuat/diedit dan alasan logisnya, agar siswa RPL mudah mengikuti.

---

## Prinsip Utama

**Backend dulu, Frontend menyusul.**  
Backend menyediakan data dan logic. Frontend menampilkan dan mengirim data. Keduanya terhubung via Route → Controller → View.

---

## FASE 0: Persiapan (Sebelum Coding)

| No | Langkah | File/Tindakan | Keterangan |
|----|---------|---------------|------------|
| 1 | Install Laravel | `composer create-project laravel/laravel perpus-project` | Project baru |
| 2 | Konfigurasi env | `.env` | Set DB_DATABASE, DB_USERNAME, DB_PASSWORD |
| 3 | Buat database | phpMyAdmin / MySQL | Database kosong untuk project |
| 4 | Test koneksi | `php artisan migrate` | Pastikan koneksi DB berhasil |

---

## FASE 1: Database & Model (Backend - Fondasi)

**Tujuan:** Struktur data siap. Model = jembatan antara kode dan tabel.

### 1.1 Buat Tabel Database

Bisa via **Migration** atau **phpMyAdmin/SQL manual**. Urutan tabel (sesuai dependency):

| No | Tabel | Kolom Penting | Keterangan |
|----|-------|---------------|------------|
| 1 | `admin` | id, nama, email, password, nama_sekolah, alamat, telepon, logo_namafile, logo_url | Data pengelola |
| 2 | `anggota` | id_anggota, nama, kelas, nis, email, telp, alamat, password, photo_name, photo_url | Data siswa |
| 3 | `buku` | id_buku, judul, pengarang, penerbit, tahun, stok, bukuimg_name, bukuimg_url | Koleksi buku |
| 4 | `peminjaman` | id_pinjam, id_buku, id_anggota, tanggal_pinjam, tanggal_kembali, status | Transaksi pinjam |

### 1.2 Buat Model (Urutan Coding)

| No | File | Dibuat dari | Keterangan |
|----|------|-------------|------------|
| 1 | `app/Models/Admin.php` | `php artisan make:model Admin` | Model pertama, tidak ada relasi |
| 2 | `app/Models/Anggota.php` | `php artisan make:model Anggota` | Punya relasi peminjaman |
| 3 | `app/Models/Buku.php` | `php artisan make:model Buku` | Punya relasi peminjaman |
| 4 | `app/Models/Peminjaman.php` | `php artisan make:model Peminjaman` | Relasi ke Buku & Anggota |

**Edit tiap model:** `$table`, `$primaryKey`, `$fillable`, `$hidden`, `$casts`, relasi (`hasMany`, `belongsTo`).

---

## FASE 2: Route & Controller (Backend - Logic)

**Tujuan:** URL terdefinisi, controller siap terima request.

### 2.1 Buat Controller (Urutan Coding)

| No | File | Perintah | Keterangan |
|----|------|----------|------------|
| 1 | `PageController.php` | `php artisan make:controller PageController` | Halaman publik |
| 2 | `AdminController.php` | `php artisan make:controller AdminController` | Area admin |
| 3 | `UserController.php` | `php artisan make:controller UserController` | Area anggota |

### 2.2 Definisikan Route

| No | File | Urutan isi | Keterangan |
|----|------|------------|------------|
| 1 | `routes/web.php` | 1) Route publik (home, login, daftar, list_buku, logout) | Tanpa middleware |
| | | 2) Route user (dashboard, perpus, pinjam, edit_akun, update_akun) | Middleware `anggota` |
| | | 3) Route admin (index, login, logout + semua fitur admin) | Sebagian pakai middleware `admin` |

### 2.3 Buat Middleware (Sebelum route admin/user jalan)

| No | File | Perintah | Keterangan |
|----|------|----------|------------|
| 1 | `AdminMiddleware.php` | `php artisan make:middleware AdminMiddleware` | Cek session admin_id |
| 2 | `AnggotaMiddleware.php` | `php artisan make:middleware AnggotaMiddleware` | Cek session anggota_id |

**Edit `app/Http/Kernel.php`:** Tambah alias `'admin'` dan `'anggota'` di `$middlewareAliases`.

### 2.4 Isi Method Controller (Urutan Logic)

**PageController** (publik):
1. `home()` → return view
2. `login()` → validasi, reCAPTCHA, cek kredensial, session, redirect
3. `daftar()` → return view
4. `store_daftar()` → validasi, reCAPTCHA, upload foto, Anggota::create
5. `list_buku()` → query + filter, return view
6. `logout()` → session forget, redirect

**AdminController** (admin):
1. `index()` → cek session, return view login
2. `login()` → validasi, reCAPTCHA, cek Admin, session, redirect
3. `logout()` → session forget, redirect
4. `dashboard()` → query statistik, return view
5. `list_user()`, `edit_user()`, `update_user()`
6. `stock_buku()`, `tambah_buku()`, `store_buku()`, `edit_buku()`, `update_buku()`, `hapus_buku()`
7. `peminjaman()`, `update_status_peminjaman()`
8. `edit_admin()`, `update_admin()`

**UserController** (anggota):
1. `dashboard()` → query peminjaman anggota, return view
2. `perpus()` → query buku + filter, return view
3. `pinjam()` → validasi, cek stok, cek duplikat, Peminjaman::create
4. `edit_akun()`, `update_akun()`

---

## FASE 3: Frontend - Layout (Dasar Tampilan)

**Tujuan:** Header, footer, CSS/JS siap. Dipakai ulang di semua halaman.

### 3.1 Assets

| No | File/Folder | Sumber | Keterangan |
|----|-------------|--------|------------|
| 1 | `public/assets/css/bootstrap.css` | Download Bootstrap / CDN | CSS framework |
| 2 | `public/assets/js/bootstrap.js` | Download Bootstrap | JS Bootstrap |
| 3 | `public/assets/img/` | Logo, icon, placeholder | Gambar |

### 3.2 Partial Layout (Urutan Coding)

| No | File | Dipakai di | Keterangan |
|----|------|------------|------------|
| 1 | `inc/header.blade.php` | home, daftar, list_buku | Navbar publik: Beranda, List Buku, Daftar |
| 2 | `inc/footer.blade.php` | home, daftar, list_buku | Footer sederhana |
| 3 | `inc/header_user.blade.php` | user/* | Navbar anggota: Dashboard, Katalog, Edit Akun, Keluar |
| 4 | `inc/footer_user.blade.php` | user/* | Footer anggota |
| 5 | `inc/header_adm.blade.php` | admin/* | Navbar admin: Dashboard, List Buku, List User, Peminjaman, Edit Admin, Keluar |
| 6 | `inc/footer_adm.blade.php` | admin/* | Footer admin |

**Struktur header:** Logo, menu link (route), tombol aksi. Pakai `@include('inc.header')` di tiap view.

### 3.3 View Composer (Data Global ke Header)

**Edit `app/Providers/AppServiceProvider.php`:**  
Tambah `View::composer` untuk share `namaSekolah` dan `logoUrl` dari tabel admin ke header.

---

## FASE 4: Frontend - Halaman Publik

**Tujuan:** Tamu bisa lihat dan daftar tanpa login.

### 4.1 Urutan View

| No | File | Controller Method | Keterangan |
|----|------|-------------------|------------|
| 1 | `home.blade.php` | PageController::home | Form login (email/NIS, password, reCAPTCHA). Action: route('login') |
| 2 | `daftar.blade.php` | PageController::daftar | Form pendaftaran. Action: route('store_daftar') |
| 3 | `list_buku.blade.php` | PageController::list_buku | Daftar buku + filter. Tanpa tombol Pinjam (belum login) |

**Penting:** Setiap form pakai `@csrf`. Form POST ke route yang benar.

---

## FASE 5: Frontend - Area Admin

**Tujuan:** Admin bisa login, kelola buku, anggota, peminjaman.

### 5.1 Urutan View

| No | File | Controller Method | Keterangan |
|----|------|-------------------|------------|
| 1 | `admin/index.blade.php` | AdminController::index | Form login admin. Action: route('admin.login') |
| 2 | `admin/dashboard.blade.php` | AdminController::dashboard | Statistik, 5 anggota terbaru, 5 peminjaman terbaru |
| 3 | `admin/stock_buku.blade.php` | AdminController::stock_buku | Tabel buku, filter, tombol Tambah/Edit/Hapus |
| 4 | `admin/tambah_buku.blade.php` | AdminController::tambah_buku | Form tambah buku. Action: route('admin.store_buku') |
| 5 | `admin/edit_buku.blade.php` | AdminController::edit_buku | Form edit buku. Action: route('admin.update_buku') |
| 6 | `admin/list_user.blade.php` | AdminController::list_user | Tabel anggota, tombol Edit |
| 7 | `admin/edit_user.blade.php` | AdminController::edit_user | Form edit anggota. Action: route('admin.update_user') |
| 8 | `admin/peminjaman.blade.php` | AdminController::peminjaman | Tabel peminjaman, form ubah status |
| 9 | `admin/edit_admin.blade.php` | AdminController::edit_admin | Form edit profil admin. Action: route('admin.update_admin') |

**Include:** `@include('inc.header_adm')` dan `@include('inc.footer_adm')` di semua view admin.

---

## FASE 6: Frontend - Area Anggota

**Tujuan:** Anggota login, lihat katalog, pinjam buku, edit profil.

### 6.1 Urutan View

| No | File | Controller Method | Keterangan |
|----|------|-------------------|------------|
| 1 | `user/dashboard.blade.php` | UserController::dashboard | Riwayat peminjaman anggota |
| 2 | `user/perpus.blade.php` | UserController::perpus | Katalog buku + tombol Pinjam (form POST ke route('user.pinjam')) |
| 3 | `user/edit_akun.blade.php` | UserController::edit_akun | Form edit profil. Action: route('user.update_akun') |

**Include:** `@include('inc.header_user')` dan `@include('inc.footer_user')`.

---

## FASE 7: Konfigurasi & Integrasi

| No | File | Perubahan | Keterangan |
|----|------|-----------|------------|
| 1 | `config/services.php` | Tambah `recaptcha` (secret, site_key) | Untuk reCAPTCHA di login & daftar |
| 2 | `.env` | RECAPTCHA_SECRET_KEY, RECAPTCHA_SITE_KEY | Dapat dari Google reCAPTCHA |
| 3 | Folder `public/uploads/` | Buat: admin, anggota, buku | Untuk simpan file upload |

---

## FASE 8: Fitur Tambahan (Opsional)

| No | File | Fungsi |
|----|------|--------|
| 1 | `ResetAdminPassword.php` | Command reset password admin: `php artisan admin:reset-password email password` |

---

## DIAGRAM ALUR SINGKAT

```
[FASE 0] Persiapan
    ↓
[FASE 1] Database → Model (Admin, Anggota, Buku, Peminjaman)
    ↓
[FASE 2] Middleware → Route → Controller (logic)
    ↓
[FASE 3] Layout (header, footer, assets)
    ↓
[FASE 4] View Publik (home, daftar, list_buku)
    ↓
[FASE 5] View Admin (login, dashboard, CRUD buku, anggota, peminjaman)
    ↓
[FASE 6] View Anggota (dashboard, perpus, edit_akun)
    ↓
[FASE 7] Config (reCAPTCHA, upload folder)
    ↓
[FASE 8] Opsional (command, dll)
```

---

## CHECKLIST URUTAN FILE (Quick Reference)

**Backend (buat/diedit dalam urutan ini):**

1. Migration / SQL tabel
2. `app/Models/Admin.php`
3. `app/Models/Anggota.php`
4. `app/Models/Buku.php`
5. `app/Models/Peminjaman.php`
6. `app/Http/Middleware/AdminMiddleware.php`
7. `app/Http/Middleware/AnggotaMiddleware.php`
8. `app/Http/Kernel.php` (tambah alias)
9. `routes/web.php` (definisi route)
10. `app/Http/Controllers/PageController.php`
11. `app/Http/Controllers/AdminController.php`
12. `app/Http/Controllers/UserController.php`
13. `app/Providers/AppServiceProvider.php` (View composer)
14. `config/services.php` (reCAPTCHA)

**Frontend (buat dalam urutan ini):**

1. `public/assets/` (CSS, JS, img)
2. `inc/header.blade.php`, `inc/footer.blade.php`
3. `inc/header_user.blade.php`, `inc/footer_user.blade.php`
4. `inc/header_adm.blade.php`, `inc/footer_adm.blade.php`
5. `home.blade.php`, `daftar.blade.php`, `list_buku.blade.php`
6. `admin/index.blade.php`, `admin/dashboard.blade.php`
7. `admin/stock_buku.blade.php`, `admin/tambah_buku.blade.php`, `admin/edit_buku.blade.php`
8. `admin/list_user.blade.php`, `admin/edit_user.blade.php`
9. `admin/peminjaman.blade.php`, `admin/edit_admin.blade.php`
10. `user/dashboard.blade.php`, `user/perpus.blade.php`, `user/edit_akun.blade.php`

---

## Tips untuk Siswa RPL

1. **Jangan loncat fase.** Model harus ada sebelum Controller pakai. Route harus ada sebelum View pakai `route()`.
2. **Test tiap fase.** Setelah Model: coba `Model::all()`. Setelah Controller: coba akses URL. Setelah View: cek tampilan.
3. **Satu fitur dulu.** Misal: selesaikan login admin dulu (route, controller, view) sebelum lanjut ke fitur lain.
4. **Copy-paste hati-hati.** Sesuaikan nama route, nama variabel, nama field form dengan controller.

---

*Dokumen ini melengkapi PANDUAN_FILE_PROJECT.md untuk alur coding sistematis.*
