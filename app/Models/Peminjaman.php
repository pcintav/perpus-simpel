<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    /** Status: masih dipinjam (nilai enum DB) */
    public const STATUS_DIPINJAM = 'Dipinjam';

    /** Status: sudah dikembalikan (nilai enum DB — jangan diubah ke 'Kembali') */
    public const STATUS_KEMBALI = 'kembali';

    protected $table = 'peminjaman';
    protected $primaryKey = 'id_pinjam';
    public $timestamps = false;

    protected $fillable = [
        'id_buku',
        'id_anggota',
        'tanggal_pinjam',
        'tanggal_kembali',
        'tanggal_dikembalikan',
        'denda',
        'denda_lunas',
        'tgl_bayar_denda',
        'perpanjang_status',
        'perpanjang_hari',
        'status',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'datetime',
        'tanggal_kembali' => 'datetime',
        'tanggal_dikembalikan' => 'datetime',
        'denda' => 'integer',
        'denda_lunas' => 'boolean',
        'tgl_bayar_denda' => 'datetime',
    ];

    public function buku()
    {
        return $this->belongsTo(Buku::class, 'id_buku', 'id_buku');
    }

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'id_anggota', 'id_anggota');
    }

    /** Label tampilan status */
    public function getStatusLabelAttribute(): string
    {
        return $this->status === self::STATUS_DIPINJAM ? 'Dipinjam' : 'Kembali';
    }

    /** Apakah terlambat (masih dipinjam dan lewat jatuh tempo) */
    public function isTerlambat(): bool
    {
        return $this->status === self::STATUS_DIPINJAM
            && $this->tanggal_kembali
            && Carbon::now()->greaterThan($this->tanggal_kembali);
    }

    /** Jumlah hari terlambat (0 jika tidak) */
    public function hariTerlambat(): int
    {
        if (! $this->isTerlambat()) {
            return 0;
        }

        return (int) Carbon::now()->startOfDay()->diffInDays($this->tanggal_kembali->copy()->startOfDay());
    }

    /** Hitung denda berdasarkan config */
    public function hitungDenda(): int
    {
        $hari = $this->hariTerlambat();
        if ($hari <= 0) {
            return 0;
        }

        return $hari * (int) config('perpus.denda_per_hari', 1000);
    }

    /** Proses pengembalian resmi */
    public function tandaiKembali(): void
    {
        $denda = $this->hitungDenda();
        $this->status = self::STATUS_KEMBALI;
        $this->tanggal_dikembalikan = Carbon::now();
        $this->denda = $denda;
        $this->denda_lunas = $denda <= 0;
        $this->save();

        Reservasi::siapkanBerikutnya((int) $this->id_buku);
    }
}
