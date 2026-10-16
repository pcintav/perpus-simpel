# Catatan Pengembangan Sistem Perpustakaan (SMK RPL)

Dokumen ini merangkum fitur yang ditambahkan tanpa merusak alur existing.

## Yang sudah ditambahkan

### Fondasi
- Migration aman: buat tabel domain jika belum ada + kolom baru (status anggota, isbn/kategori/rak, tanggal_dikembalikan, denda, role admin)
- Seeder demo: `php artisan db:seed` (admin@perpus.local / admin12345, siswa@perpus.local / siswa12345)
- Config `config/perpus.php` + env: `PERPUS_MAX_PINJAM`, `PERPUS_LAMA_PINJAM`, `PERPUS_DENDA_PER_HARI`, `PERPUS_SKIP_RECAPTCHA_LOCAL`

### Bisnis
- Approval anggota (`pending` → `aktif` / `nonaktif`)
- Batas max pinjam per anggota
- Pengembalian resmi + hitung denda otomatis
- Badge keterlambatan di dashboard anggota & admin
- Laporan + export CSV peminjaman
- ISBN, kategori, rak pada buku
- Pagination list buku/anggota/peminjaman
- Filter server-side (bukan hanya JS)
- Logout via POST + CSRF
- reCAPTCHA di-skip otomatis di `local` (lab sekolah)
- Hapus buku dicegah jika masih dipinjam
- Hapus anggota: nonaktif jika punya riwayat; hapus permanen jika belum pernah pinjam
- Scope filter buku (refactor triple-copy)
- Reset password anggota dari form edit admin

### Sengaja TIDAK diubah (agar tidak merusak)
- Auth tetap session custom (`admin_id` / `anggota_id`) — bukan multi-guard rewrite
- Nilai status DB tetap `Dipinjam` / `kembali` (sesuai ENUM existing)
- Upload tetap ke `public/uploads/...` (kompatibel path lama)

## Perintah penting

```bash
php artisan migrate
php artisan db:seed
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan test --filter=PeminjamanTest
```

## Fitur lanjutan (opsional tugas berikutnya)
- Multi-guard Laravel Auth
- API Sanctum
- Reservasi / perpanjangan / notifikasi email
- Barcode QR
- Role admin bertingkat (Policy)
- SoftDeletes Eloquent
