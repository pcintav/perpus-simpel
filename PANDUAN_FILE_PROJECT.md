# Panduan File Project - Sistem Perpustakaan

Dokumen ini membantu siswa RPL memahami asal-usul setiap file di project ini: mana yang dibuat manual, mana yang bawaan Laravel tapi diedit, dan mana yang tetap standar.

---

## 1. FILE BUATAN MANUAL (Custom)

File-file ini **dibuat khusus untuk project** ini, tidak ada di instalasi Laravel default.

### Models (`app/Models/`)

| File | Fungsi |
|------|--------|
| `Admin.php` | Model admin perpustakaan. Login, profil, logo. Tabel: `admin` |
| `Anggota.php` | Model anggota/siswa. Pendaftaran, login (email/NIS), pinjam buku. Tabel: `anggota` |
| `Buku.php` | Model koleksi buku. Stok, cover, metadata. Tabel: `buku` |
| `Peminjaman.php` | Model transaksi pinjam. Hubungkan buku-anggota, status Dipinjam/kembali. Tabel: `peminjaman` |

### Controllers (`app/Http/Controllers/`)

| File | Fungsi |
|------|--------|
| `PageController.php` | Halaman publik: home (login), daftar, list_buku, proses login/logout |
| `AdminController.php` | Area admin: dashboard, CRUD buku, manajemen anggota, peminjaman, profil admin |
| `UserController.php` | Area anggota: dashboard, katalog, pinjam buku, edit profil |

### Middleware (`app/Http/Middleware/`)

| File | Fungsi |
|------|--------|
| `AdminMiddleware.php` | Proteksi route admin. Cek `session('admin_id)`. Redirect ke login jika belum masuk |
| `AnggotaMiddleware.php` | Proteksi route anggota. Cek `session('anggota_id)`. Redirect ke home jika belum masuk |

### Console Commands (`app/Console/Commands/`)

| File | Fungsi |
|------|--------|
| `ResetAdminPassword.php` | Command: `php artisan admin:reset-password {email} {password}`. Reset password admin via CLI |

### Views (`resources/views/`)

**Halaman Publik:**
| File | Fungsi |
|------|--------|
| `home.blade.php` | Form login anggota (email/NIS + password + reCAPTCHA) |
| `daftar.blade.php` | Form pendaftaran anggota baru |
| `list_buku.blade.php` | Daftar buku dengan filter (tanpa login) |

**Area Admin:**
| File | Fungsi |
|------|--------|
| `admin/index.blade.php` | Form login admin |
| `admin/dashboard.blade.php` | Dashboard admin (statistik, anggota terbaru, peminjaman terbaru) |
| `admin/stock_buku.blade.php` | Stok buku, filter, tombol tambah/edit/hapus |
| `admin/tambah_buku.blade.php` | Form tambah buku |
| `admin/edit_buku.blade.php` | Form edit buku |
| `admin/list_user.blade.php` | Daftar anggota |
| `admin/edit_user.blade.php` | Form edit data anggota |
| `admin/peminjaman.blade.php` | Daftar transaksi peminjaman, ubah status |
| `admin/edit_admin.blade.php` | Form edit profil admin |

**Area Anggota:**
| File | Fungsi |
|------|--------|
| `user/dashboard.blade.php` | Dashboard anggota (riwayat peminjaman) |
| `user/perpus.blade.php` | Katalog buku dengan tombol Pinjam |
| `user/edit_akun.blade.php` | Form edit profil anggota |

**Partial (Include):**
| File | Fungsi |
|------|--------|
| `inc/header.blade.php` | Navbar untuk halaman publik (home, daftar, list_buku) |
| `inc/header_user.blade.php` | Navbar untuk area anggota |
| `inc/header_adm.blade.php` | Navbar untuk area admin |
| `inc/footer.blade.php` | Footer halaman publik |
| `inc/footer_user.blade.php` | Footer area anggota |
| `inc/footer_adm.blade.php` | Footer area admin |

### Routes (`routes/`)

| File | Perubahan |
|------|-----------|
| `web.php` | **Seluruh isi custom.** Route publik, prefix `/user` (middleware anggota), prefix `/admin` (middleware admin) |

### Public Assets (`public/assets/`)

| Folder/File | Fungsi |
|-------------|--------|
| `assets/css/bootstrap.css` | Bootstrap CSS (bisa dari CDN atau file lokal) |
| `assets/js/bootstrap.js` | Bootstrap JS |
| `assets/img/` | Gambar/icon (logo, placeholder, dll) |

### Database

**Catatan:** Tabel `admin`, `anggota`, `buku`, `peminjaman` kemungkinan dibuat manual (phpMyAdmin/SQL) atau migration terpisah yang tidak ada di folder `database/migrations/`. Project ini hanya punya migration default Laravel (users, password_reset_tokens, failed_jobs, personal_access_tokens).

---

## 2. FILE BAWAAN LARAVEL TAPI DIEDIT

File ini **ada di Laravel default**, tapi **isinya diubah** untuk kebutuhan project.

### `app/Http/Kernel.php`

**Perubahan:**
- Tambah alias middleware `'admin' => AdminMiddleware::class` di `$middlewareAliases`
- Tambah alias middleware `'anggota' => AnggotaMiddleware::class` di `$middlewareAliases`

**Tujuan:** Agar route bisa pakai `->middleware('admin')` dan `->middleware('anggota')`

---

### `app/Providers/AppServiceProvider.php`

**Perubahan:**
- Tambah `View::composer` untuk `inc.header` dan `inc.header_user`: share `namaSekolah` dan `logoUrl` dari Admin
- Tambah `View::composer` untuk `inc.header_adm`: share `namaSekolah` dan `logoUrl` (admin yang login atau admin pertama)

**Tujuan:** Nama sekolah dan logo dinamis dari database, tampil di semua header

---

### `config/services.php`

**Perubahan:**
- Tambah konfigurasi `recaptcha` dengan `secret` dan `site_key` dari `.env`

**Tujuan:** reCAPTCHA di form login dan daftar. Variabel di `.env`: `RECAPTCHA_SECRET_KEY`, `RECAPTCHA_SITE_KEY`

---

### `routes/web.php`

**Perubahan:**
- Hampir seluruh isi diganti. Route default (welcome) di-comment atau dihapus
- Route custom: home, login, daftar, list_buku, logout, prefix user, prefix admin

**Tujuan:** Semua URL aplikasi didefinisikan di sini

---

## 3. FILE BAWAAN LARAVEL (Tidak Diubah / Sedikit Diubah)

File ini **tetap standar Laravel**. Bisa dipelajari sebagai referensi, tapi tidak perlu diedit untuk project ini.

### Models

| File | Keterangan |
|------|------------|
| `app/Models/User.php` | Model User default. **Tidak dipakai** di project (pakai Anggota & Admin). Tetap ada untuk kompatibilitas |

### Controllers

| File | Keterangan |
|------|------------|
| `app/Http/Controllers/Controller.php` | Base controller. Parent dari PageController, AdminController, UserController |

### Middleware (Bawaan)

| File | Fungsi |
|------|--------|
| `Authenticate.php` | Middleware auth default (untuk User) |
| `EncryptCookies.php` | Enkripsi cookie |
| `PreventRequestsDuringMaintenance.php` | Blokir request saat maintenance mode |
| `RedirectIfAuthenticated.php` | Redirect jika sudah login (untuk route guest) |
| `TrimStrings.php` | Hapus spasi di input |
| `TrustHosts.php` | Trust host |
| `TrustProxies.php` | Trust proxy headers |
| `ValidateSignature.php` | Validasi URL signed |
| `VerifyCsrfToken.php` | Cek token CSRF (wajib untuk form POST) |

### Providers

| File | Keterangan |
|------|------------|
| `AuthServiceProvider.php` | Provider auth (default) |
| `BroadcastServiceProvider.php` | Provider broadcasting |
| `EventServiceProvider.php` | Provider event |
| `RouteServiceProvider.php` | Load route dari web.php, api.php |

### Lainnya

| File | Keterangan |
|------|------------|
| `app/Console/Kernel.php` | Kernel console (schedule, command) |
| `app/Exceptions/Handler.php` | Penanganan exception |
| `bootstrap/app.php` | Bootstrap aplikasi |
| `routes/api.php` | Route API (default, jarang dipakai) |
| `routes/channels.php` | Broadcast channels |
| `routes/console.php` | Command console |
| `config/*.php` | Konfigurasi (app, auth, database, dll) - standar |
| `database/migrations/*` | Migration default (users, password_reset_tokens, failed_jobs, personal_access_tokens) |
| `database/seeders/DatabaseSeeder.php` | Seeder (default, factory di-comment) |
| `database/factories/UserFactory.php` | Factory User |
| `public/index.php` | Entry point aplikasi |
| `public/.htaccess` | Konfigurasi Apache |

---

## 4. FILE OTOMATIS LARAVEL (Penting, Tidak Perlu Diedit)

File-file ini **otomatis dibuat/diupdate oleh Laravel** saat aplikasi berjalan atau saat menjalankan perintah tertentu. **Penting** untuk jalannya aplikasi, tapi **jangan diedit manual**.

### `vendor/` (folder)

| Asal | Keterangan |
|------|------------|
| `composer install` / `composer update` | Semua dependency PHP (Laravel framework, package, dll). **Jangan diedit.** Bisa dihapus lalu jalankan `composer install` lagi jika perlu. |

### `composer.lock`

| Asal | Keterangan |
|------|------------|
| `composer install` / `composer update` | Mengunci versi dependency agar sama di semua environment. **Jangan diedit.** Commit ke git. |

### `storage/framework/views/` (file .php dengan nama hash)

| Asal | Keterangan |
|------|------------|
| Laravel saat pertama kali render view | Compile file `.blade.php` jadi PHP murni (cache). Contoh: `8ae4bc8894fc73faa5807cf683ee9c8a.php` = hasil compile salah satu view. **Jangan diedit.** Bisa dihapus, Laravel akan compile ulang. |

### `storage/framework/sessions/`

| Asal | Keterangan |
|------|------------|
| Saat user login / pakai session | File session per user (file driver). Berisi `admin_id`, `anggota_id`, dll. **Jangan diedit.** Bisa dihapus untuk clear session. |

### `storage/framework/cache/`

| Asal | Keterangan |
|------|------------|
| `cache()`, `Cache::put()`, `php artisan config:cache` | Cache aplikasi. **Jangan diedit.** |

### `storage/logs/laravel.log`

| Asal | Keterangan |
|------|------------|
| Saat ada error / `Log::info()`, dll | Log error dan aktivitas. **Jangan diedit** (baca saja untuk debug). Rotasi otomatis per hari. |

### `bootstrap/cache/` (packages.php, services.php, config.php, routes-v7.php)

| Asal | Keterangan |
|------|------------|
| `php artisan config:cache`, `route:cache`, `package:discover` | Cache config, route, package. **Jangan diedit.** Hapus file ini lalu jalankan `php artisan config:clear` jika ada masalah. |

### `node_modules/` (jika pakai npm)

| Asal | Keterangan |
|------|------------|
| `npm install` | Dependency JavaScript (Vite, dll). **Jangan diedit.** Bisa dihapus lalu `npm install` lagi. |

### `public/build/` (jika pakai Vite)

| Asal | Keterangan |
|------|------------|
| `npm run build` | Asset yang di-compile (CSS, JS). **Jangan diedit manual.** |

### `public/hot` (saat development)

| Asal | Keterangan |
|------|------------|
| `npm run dev` | Tanda Vite dev server aktif. **Jangan diedit.** |

### `vendor/composer/` (autoload_*.php)

| Asal | Keterangan |
|------|------------|
| `composer dump-autoload` | Peta class untuk autoload. **Jangan diedit.** |

---

### Ringkasan: File Auto-Generated

| Lokasi | Dibuat oleh | Boleh dihapus? |
|--------|-------------|----------------|
| `vendor/` | composer | Ya, lalu `composer install` |
| `storage/framework/views/*.php` | Laravel (render view) | Ya, akan compile ulang |
| `storage/framework/sessions/*` | Laravel (session) | Ya, session hilang |
| `storage/logs/*.log` | Laravel (logging) | Ya, log hilang |
| `bootstrap/cache/*.php` | artisan config:cache, dll | Ya, lalu `artisan config:clear` |
| `composer.lock` | composer | **Tidak** (commit ke git) |
| `node_modules/` | npm | Ya, lalu `npm install` |

**Penting:** Folder `storage/` dan `bootstrap/cache/` harus **writable** (izin tulis) agar Laravel bisa membuat file di sana. Jika error "Permission denied", cek izin folder.

---

## 5. RINGKASAN UNTUK SISWA RPL

### Yang Perlu Dipelajari (Custom + Edited)

1. **Models:** Admin, Anggota, Buku, Peminjaman
2. **Controllers:** PageController, AdminController, UserController
3. **Middleware:** AdminMiddleware, AnggotaMiddleware
4. **Views:** Semua di `resources/views/` (kecuali yang tidak dipakai)
5. **Routes:** `routes/web.php`
6. **Edit di:** Kernel.php, AppServiceProvider.php, config/services.php

### Yang Bisa Diabaikan (Sementara)

- Model User (tidak dipakai)
- routes/api.php, routes/channels.php, routes/console.php
- Middleware bawaan (kecuali perlu paham CSRF, cookie, session)
- Config yang tidak diubah

### Alur Belajar yang Disarankan

1. **Route** (`web.php`) → pahami URL dan controller mana yang dipanggil
2. **Controller** → pahami method dan logic
3. **Model** → pahami struktur data dan relasi
4. **View** → pahami layout dan data yang ditampilkan
5. **Middleware** → pahami proteksi route (admin, anggota)

---

## 6. STRUKTUR FOLDER SINGKAT

```
app/
├── Http/
│   ├── Controllers/     → PageController, AdminController, UserController (custom)
│   ├── Kernel.php       → diedit (tambah middleware alias)
│   └── Middleware/      → AdminMiddleware, AnggotaMiddleware (custom) + bawaan Laravel
├── Models/              → Admin, Anggota, Buku, Peminjaman (custom) + User (bawaan)
├── Console/Commands/   → ResetAdminPassword (custom)
└── Providers/          → AppServiceProvider (diedit)

resources/views/
├── home, daftar, list_buku     → publik
├── admin/*                     → area admin
├── user/*                      → area anggota
└── inc/*                       → header, footer (partial)

routes/
└── web.php              → seluruh route custom
```

---

*Dokumen ini dibuat untuk memudahkan siswa RPL memahami struktur project Sistem Perpustakaan.*
