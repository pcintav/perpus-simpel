<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Admin - Representasi Tabel admin
 *
 * Menyimpan data admin perpustakaan (pengelola). Digunakan untuk autentikasi
 * login di AdminController::login (cek email + password via Hash::check).
 * Data admin juga dipakai di AppServiceProvider untuk menampilkan nama sekolah
 * dan logo di header (inc.header_adm, inc.header, inc.header_user).
 *
 * Tabel: admin
 * Primary key: id
 * Tidak pakai created_at/updated_at (timestamps = false)
 *
 * Relasi: Tidak ada relasi Eloquent ke model lain. Admin standalone.
 */
class Admin extends Model
{
    /**
     * Trait untuk factory (testing/seeding).
     * Memungkinkan Admin::factory()->create() di database seeders atau tests.
     */
    use HasFactory;

    /**
     * Nama tabel di database.
     * Default Laravel: nama class plural (admins). Override ke 'admin'.
     *
     * @var string
     */
    protected $table = 'admin';

    /**
     * Primary key tabel.
     * Default Laravel: 'id'. Di sini tetap 'id' (eksplisit).
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Nonaktifkan created_at dan updated_at.
     * Tabel admin tidak punya kolom timestamp. Jika true, Laravel otomatis
     * isi created_at/updated_at saat create/update (akan error jika kolom tidak ada).
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Kolom yang boleh diisi mass assignment (create/update).
     * Contoh: Admin::create([...]), $admin->update([...]).
     * Kolom di luar array ini akan diabaikan saat mass assign (keamanan).
     *
     * @var array<string>
     */
    protected $fillable = [
        'nama',
        'email',
        'role',
        'nama_sekolah',
        'alamat',
        'telepon',
        'password',
        'logo_namafile',
        'logo_url',
        'tgl_update',
    ];

    /**
     * Kolom yang disembunyikan saat model di-convert ke JSON/array.
     * Password tidak boleh ikut terkirim ke frontend (keamanan).
     * Contoh: $admin->toArray() atau response JSON tidak akan include 'password'.
     *
     * @var array<string>
     */
    protected $hidden = ['password'];

    protected $casts = [
        'password' => 'hashed',
        'tgl_update' => 'datetime',
    ];
}
