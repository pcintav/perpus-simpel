<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservasi extends Model
{
    public const ANTRI = 'antri';
    public const SIAP = 'siap';
    public const SELESAI = 'selesai';
    public const BATAL = 'batal';

    protected $table = 'reservasi';
    protected $primaryKey = 'id_reservasi';
    public $timestamps = false;

    protected $fillable = [
        'id_buku',
        'id_anggota',
        'tanggal',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
    ];

    public function buku()
    {
        return $this->belongsTo(Buku::class, 'id_buku', 'id_buku');
    }

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'id_anggota', 'id_anggota');
    }

    /** Satu buku kembali membuka satu giliran berikutnya. */
    public static function siapkanBerikutnya(int $idBuku): void
    {
        $berikut = static::where('id_buku', $idBuku)
            ->where('status', self::ANTRI)
            ->orderBy('tanggal')
            ->orderBy('id_reservasi')
            ->first();

        if ($berikut) {
            $berikut->status = self::SIAP;
            $berikut->save();
        }
    }

    /** Batalkan antrian anggota (misalnya akun dinonaktifkan) dan geser giliran. */
    public static function lepasAnggota(int $idAnggota): void
    {
        $rows = static::where('id_anggota', $idAnggota)
            ->whereIn('status', [self::ANTRI, self::SIAP])
            ->get();

        foreach ($rows as $row) {
            $wasSiap = $row->status === self::SIAP;
            $idBuku = (int) $row->id_buku;
            $row->status = self::BATAL;
            $row->save();
            if ($wasSiap) {
                static::siapkanBerikutnya($idBuku);
            }
        }
    }
}
