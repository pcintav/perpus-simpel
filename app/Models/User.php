<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Model User - Default Laravel Authenticatable User
 *
 * Model bawaan Laravel untuk autentikasi standar (tabel users). Di project ini,
 * autentikasi UTAMA memakai Anggota (siswa) dan Admin (pengelola), BUKAN User.
 * User tetap ada karena: config/auth.php mereferensi model ini, dan bisa dipakai
 * untuk fitur API (Sanctum) atau fitur Laravel Breeze/Jetstream jika ditambah nanti.
 *
 * Tabel: users (default Laravel)
 * Extends: Authenticatable (bukan Model biasa - punya method auth seperti getAuthIdentifier)
 *
 * Catatan: Jika project hanya pakai Anggota & Admin, model User bisa tidak dipakai.
 * Tetap dipertahankan untuk kompatibilitas Laravel dan kemungkinan perluasan.
 */
class User extends Authenticatable
{
    /**
     * HasApiTokens: Untuk Laravel Sanctum - generate token API (personal access token).
     * HasFactory: Untuk User::factory()->create() di seeder/test.
     * Notifiable: Untuk kirim notifikasi (email, database, dll) via $user->notify().
     */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Kolom yang boleh diisi mass assignment.
     * Struktur default Laravel: name, email, password. Sesuaikan jika tabel users punya kolom lain.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Kolom yang disembunyikan saat model di-convert ke JSON/array.
     * remember_token: Untuk fitur "ingat saya" (Laravel auth).
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Cast atribut ke tipe tertentu.
     *
     * - email_verified_at: Cast ke Carbon (untuk fitur verifikasi email)
     * - password: Otomatis di-hash saat assign (sama seperti Anggota)
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
