<?php

/**
 * ResetAdminPassword - Artisan Command untuk Reset Password Admin
 *
 * File ini merupakan Custom Artisan Command Laravel. Artisan adalah CLI (Command Line Interface)
 * bawaan Laravel untuk menjalankan tugas-tugas otomatis via terminal (php artisan ...).
 *
 * Fungsi utama: Mereset password admin yang lupa password tanpa perlu akses ke database
 * atau form web. Berguna saat admin lupa password dan tidak ada fitur "Lupa Password".
 *
 * Cara pakai di terminal:
 *   php artisan admin:reset-password email@contoh.com passwordbaru123
 *
 * Asal: Dibuat manual untuk project perpustakaan. Bukan bawaan Laravel.
 */

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;

/**
 * Class ResetAdminPassword
 *
 * Extends Illuminate\Console\Command = class bawaan Laravel untuk membuat Artisan Command.
 * Semua command custom harus extend Command agar terdaftar di php artisan.
 */
class ResetAdminPassword extends Command
{
    /**
     * $signature - Mendefinisikan nama command dan argument yang diterima
     *
     * Format: 'nama:command {arg1} {arg2}'
     * - admin:reset-password = nama command (dipanggil: php artisan admin:reset-password)
     * - {email} = argument wajib pertama (email admin yang akan direset)
     * - {password} = argument wajib kedua (password baru dalam bentuk plain text)
     *
     * Asal: Sintaks standar Laravel untuk Artisan Command signature.
     */
    protected $signature = 'admin:reset-password {email} {password}';

    /**
     * $description - Deskripsi command yang muncul di php artisan list
     *
     * Teks ini ditampilkan ketika user menjalankan 'php artisan list' untuk
     * melihat daftar command yang tersedia. Membantu user memahami fungsi command.
     */
    protected $description = 'Reset password admin berdasarkan email (password akan di-hash bcrypt)';

    /**
     * handle() - Method utama yang dijalankan saat command dieksekusi
     *
     * Method ini wajib ada di setiap Artisan Command. Laravel memanggil method ini
     * otomatis ketika user menjalankan: php artisan admin:reset-password email pass
     *
     * Alur:
     * 1. Ambil argument email dan password dari input terminal
     * 2. Cari admin di database berdasarkan email
     * 3. Jika tidak ditemukan -> tampilkan error, return FAILURE
     * 4. Jika ditemukan -> hash password, simpan ke database, tampilkan sukses
     *
     * @return int Command::SUCCESS (0) jika berhasil, Command::FAILURE (1) jika gagal
     */
    public function handle()
    {
        /** Ambil nilai argument dari terminal. $this->argument() = method bawaan Command untuk mengakses argument yang didefinisikan di $signature */
        $email = $this->argument('email');
        $password = $this->argument('password');

        /** Cari record admin di tabel 'admin' yang kolom email-nya sesuai. first() = ambil satu record atau null jika tidak ada. Asal: Eloquent ORM Laravel */
        $admin = Admin::where('email', $email)->first();

        /** Validasi: jika admin tidak ditemukan, tampilkan pesan error dan hentikan command dengan status gagal */
        if (!$admin) {
            $this->error("Admin dengan email '{$email}' tidak ditemukan.");
            return Command::FAILURE;
        }

        /** Assign password plain text; cast 'hashed' di model Admin otomatis bcrypt sebelum disimpan. */
        $admin->password = $password;
        /** Simpan perubahan ke database. save() = method Eloquent untuk update record yang sudah di-load */
        $admin->save();

        /** Tampilkan pesan sukses ke terminal. $this->info() = method Command untuk output teks hijau (sukses), $this->error() = teks merah (error) */
        $this->info("Password admin '{$admin->nama}' ({$email}) berhasil diperbarui!");
        $this->info('Silakan login dengan password baru: ' . $password);

        /** Return status sukses agar Laravel tahu command berhasil dijalankan */
        return Command::SUCCESS;
    }
}
