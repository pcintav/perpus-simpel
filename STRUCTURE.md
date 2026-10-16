# Struktur Project Sistem Perpustakaan SMK RPL

Dokumen ini berisi struktur lengkap folder dan file project, dengan keterangan:
- **[EDIT]** = File bawaan Laravel yang dimodifikasi
- **[*]** = File buatan sendiri (custom)
- **(bawaan)** = File bawaan Laravel, tidak/tidak banyak diubah

---

## Struktur Utama

```
perpus-project/
│
├── app/
│   ├── Console/
│   │   ├── Commands/
│   │   │   └── ResetAdminPassword.php          [*] Command buatan
│   │   └── Kernel.php                          (bawaan)
│   │
│   ├── Exceptions/
│   │   └── Handler.php                         (bawaan)
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Controller.php                  (bawaan)
│   │   │   ├── AdminController.php             [*] Controller admin
│   │   │   ├── PageController.php              [*] Controller publik (home, login, daftar, list_buku)
│   │   │   └── UserController.php              [*] Controller anggota/user
│   │   │
│   │   ├── Middleware/
│   │   │   ├── AdminMiddleware.php             [*] Cek login admin
│   │   │   ├── AnggotaMiddleware.php           [*] Cek login anggota
│   │   │   ├── Authenticate.php                (bawaan)
│   │   │   ├── EncryptCookies.php              (bawaan)
│   │   │   ├── PreventRequestsDuringMaintenance.php  (bawaan)
│   │   │   ├── RedirectIfAuthenticated.php     (bawaan)
│   │   │   ├── TrimStrings.php                 (bawaan)
│   │   │   ├── TrustProxies.php                (bawaan)
│   │   │   ├── TrustHosts.php                  (bawaan)
│   │   │   ├── ValidateSignature.php           (bawaan)
│   │   │   └── VerifyCsrfToken.php             (bawaan)
│   │   │
│   │   └── Kernel.php                          [EDIT] Registrasi middleware admin & anggota
│   │
│   ├── Models/
│   │   ├── Admin.php                           [*] Model admin
│   │   ├── Anggota.php                         [*] Model anggota
│   │   ├── Buku.php                            [*] Model buku
│   │   ├── Peminjaman.php                      [*] Model peminjaman
│   │   └── User.php                            (bawaan, tidak dipakai)
│   │
│   └── Providers/
│       ├── AppServiceProvider.php              [EDIT] View Composer (nama sekolah, logo)
│       ├── AuthServiceProvider.php             (bawaan)
│       ├── BroadcastServiceProvider.php        (bawaan)
│       ├── EventServiceProvider.php            (bawaan)
│       └── RouteServiceProvider.php            (bawaan)
│
├── bootstrap/
│   ├── app.php                                 (bawaan)
│   └── cache/
│       ├── packages.php                        (auto-generated)
│       └── services.php                        (auto-generated)
│
├── config/
│   ├── app.php                                 (bawaan)
│   ├── auth.php                                (bawaan)
│   ├── broadcast.php                           (bawaan)
│   ├── cache.php                               (bawaan)
│   ├── cors.php                                (bawaan)
│   ├── database.php                            (bawaan)
│   ├── filesystems.php                         (bawaan)
│   ├── hashing.php                             (bawaan)
│   ├── logging.php                             (bawaan)
│   ├── mail.php                                (bawaan)
│   ├── queue.php                               (bawaan)
│   ├── sanctum.php                             (bawaan)
│   ├── services.php                            [EDIT] Konfigurasi reCAPTCHA
│   ├── session.php                            (bawaan)
│   └── view.php                                (bawaan)
│
├── database/
│   ├── factories/
│   │   └── UserFactory.php                     (bawaan)
│   │
│   ├── migrations/
│   │   ├── 2014_10_12_000000_create_users_table.php
│   │   ├── 2014_10_12_100000_create_password_reset_tokens_table.php
│   │   ├── 2019_08_19_000000_create_failed_jobs_table.php
│   │   └── 2019_12_14_000001_create_personal_access_tokens_table.php
│   │   (*) Tabel admin, anggota, buku, peminjaman dibuat manual via phpMyAdmin
│   │
│   └── seeders/
│       └── DatabaseSeeder.php                  (bawaan)
│
├── public/
│   ├── assets/
│   │   ├── css/
│   │   │   └── bootstrap.css                  [*] Bootstrap CSS
│   │   ├── img/
│   │   │   ├── icons8-registration-26 (1).png  [*] Favicon
│   │   │   ├── sistem-sekolah.jpg              [*] Gambar homepage
│   │   │   └── virtual-assistant.png          [*] Default logo
│   │   └── js/
│   │       ├── bootstrap.js                    [*]
│   │       └── bootstrap.min.js                [*]
│   │
│   ├── uploads/                                [*] Folder upload (dibuat runtime)
│   │   ├── admin/                              Logo admin
│   │   ├── anggota/                            Foto profil anggota
│   │   └── buku/                               Cover buku
│   │
│   ├── .htaccess                               (bawaan)
│   ├── index.php                               (bawaan)
│   └── robots.txt                              (bawaan)
│
├── resources/
│   ├── css/
│   │   └── app.css                             (bawaan, untuk Vite)
│   │
│   ├── js/
│   │   ├── app.js                              (bawaan)
│   │   └── bootstrap.js                        (bawaan)
│   │
│   └── views/
│       │
│       ├── inc/
│       │   ├── header.blade.php                [*] Header publik (home, daftar, list_buku)
│       │   ├── header_user.blade.php           [*] Header area anggota
│       │   ├── header_adm.blade.php            [*] Header area admin
│       │   ├── footer.blade.php                [*] Footer publik
│       │   ├── footer_user.blade.php           [*] Footer area anggota
│       │   └── footer_adm.blade.php            [*] Footer area admin
│       │
│       ├── admin/
│       │   ├── index.blade.php                 [*] Login admin
│       │   ├── dashboard.blade.php             [*] Dashboard admin
│       │   ├── list_user.blade.php             [*] Daftar anggota
│       │   ├── edit_user.blade.php             [*] Edit data anggota
│       │   ├── stock_buku.blade.php            [*] Stok & kelola buku
│       │   ├── tambah_buku.blade.php           [*] Form tambah buku
│       │   ├── edit_buku.blade.php             [*] Form edit buku
│       │   ├── peminjaman.blade.php            [*] Data peminjaman
│       │   └── edit_admin.blade.php            [*] Form edit profil admin
│       │
│       ├── user/
│       │   ├── dashboard.blade.php             [*] Dashboard anggota
│       │   ├── perpus.blade.php                [*] Katalog buku (pinjam)
│       │   └── edit_akun.blade.php             [*] Form edit profil anggota
│       │
│       ├── home.blade.php                      [*] Halaman utama + form login anggota
│       ├── daftar.blade.php                    [*] Form pendaftaran anggota
│       └── list_buku.blade.php                 [*] List buku publik (guest)
│
├── routes/
│   ├── api.php                                 (bawaan)
│   ├── channels.php                            (bawaan)
│   ├── console.php                             (bawaan)
│   └── web.php                                 [EDIT] Semua route aplikasi
│
├── storage/
│   ├── app/                                    (bawaan)
│   ├── framework/
│   │   ├── cache/                              (auto-generated)
│   │   ├── sessions/                           (auto-generated)
│   │   └── views/                              (compiled Blade)
│   └── logs/                                   (bawaan)
│
├── vendor/                                     (bawaan, dari composer install)
│
├── .env                                        [EDIT] Konfigurasi (DB, reCAPTCHA)
├── .env.example                                (bawaan)
├── artisan                                     (bawaan)
├── composer.json                               (bawaan)
├── composer.lock                               (bawaan)
├── package.json                                (bawaan)
├── vite.config.js                              (bawaan)
├── phpunit.xml                                 (bawaan)
└── STRUCTURE.md                                [*] Dokumen ini
```

---

## Ringkasan File Custom vs Bawaan

### File Buatan Sendiri [*]
| Kategori | File |
|----------|------|
| **Controller** | PageController, AdminController, UserController |
| **Middleware** | AdminMiddleware, AnggotaMiddleware |
| **Model** | Admin, Anggota, Buku, Peminjaman |
| **View Admin** | index, dashboard, list_user, edit_user, stock_buku, tambah_buku, edit_buku, peminjaman, edit_admin |
| **View User** | dashboard, perpus, edit_akun |
| **View Publik** | home, daftar, list_buku |
| **Include** | header, header_user, header_adm, footer, footer_user, footer_adm |
| **Assets** | public/assets/* (css, js, img) |
| **Command** | ResetAdminPassword |

### File Bawaan yang Diedit [EDIT]
| File | Perubahan |
|------|-----------|
| `app/Http/Kernel.php` | Registrasi middleware `admin`, `anggota` |
| `config/services.php` | Tambah config `recaptcha` |
| `app/Providers/AppServiceProvider.php` | View Composer untuk nama sekolah & logo |
| `routes/web.php` | Semua route aplikasi |
| `.env` | DB_DATABASE, RECAPTCHA_SECRET_KEY, RECAPTCHA_SITE_KEY |

---

## Struktur Database (Referensi)

```
admin       → id, nama, email, nama_sekolah, alamat, telepon, password, logo_namafile, logo_url, tgl_update
anggota     → id_anggota, nama, kelas, nis, email, telp, alamat, password, photo_name, photo_url, tgl_daftar
buku        → id_buku, judul, pengarang, penerbit, tahun, stok, bukuimg_name, bukuimg_url, tgl_input
peminjaman  → id_pinjam, id_buku, id_anggota, tanggal_pinjam, tanggal_kembali, status (Dipinjam/kembali)
```

---

*Dokumen terakhir diperbarui: sesuai state project saat ini.*
