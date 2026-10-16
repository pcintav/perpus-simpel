<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_AKTIF = 'aktif';
    public const STATUS_NONAKTIF = 'nonaktif';

    protected $table = 'anggota';
    protected $primaryKey = 'id_anggota';
    public $timestamps = false;

    protected $fillable = [
        'nama',
        'kelas',
        'nis',
        'email',
        'telp',
        'alamat',
        'password',
        'photo_name',
        'photo_url',
        'tgl_daftar',
        'status',
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'password' => 'hashed',
        'tgl_daftar' => 'datetime',
    ];

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'id_anggota', 'id_anggota');
    }

    public function isAktif(): bool
    {
        return ($this->status ?? self::STATUS_AKTIF) === self::STATUS_AKTIF;
    }

    public function pinjamanAktifCount(): int
    {
        return $this->peminjaman()->where('status', Peminjaman::STATUS_DIPINJAM)->count();
    }
}
