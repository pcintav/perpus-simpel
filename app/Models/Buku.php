<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Buku extends Model
{
    use HasFactory;

    protected $table = 'buku';
    protected $primaryKey = 'id_buku';
    public $timestamps = false;

    protected $fillable = [
        'judul',
        'pengarang',
        'penerbit',
        'tahun',
        'isbn',
        'kategori',
        'rak',
        'kondisi',
        'stok',
        'bukuimg_name',
        'bukuimg_url',
        'tgl_input',
    ];

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'id_buku', 'id_buku');
    }

    /** Scope: hitung jumlah eksemplar sedang dipinjam */
    public function scopeWithDipinjamCount(Builder $query): Builder
    {
        return $query->withCount(['peminjaman as dipinjam_count' => function ($q) {
            $q->where('status', Peminjaman::STATUS_DIPINJAM);
        }]);
    }

    /** Scope: filter katalog dari request (q, tahun, penerbit, pengarang, kategori) */
    public function scopeFilterKatalog(Builder $query, Request $request): Builder
    {
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qry) use ($q) {
                $qry->where('judul', 'like', "%{$q}%")
                    ->orWhere('pengarang', 'like', "%{$q}%")
                    ->orWhere('penerbit', 'like', "%{$q}%")
                    ->orWhere('isbn', 'like', "%{$q}%");
            });
        }
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }
        if ($request->filled('penerbit')) {
            $query->where('penerbit', $request->penerbit);
        }
        if ($request->filled('pengarang')) {
            $query->where('pengarang', $request->pengarang);
        }
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        return $query;
    }

    /** List unik untuk dropdown filter */
    public static function filterOptions(): array
    {
        return [
            'listTahun' => static::select('tahun')->distinct()->whereNotNull('tahun')->where('tahun', '!=', '')->orderBy('tahun', 'desc')->pluck('tahun'),
            'listPenerbit' => static::select('penerbit')->distinct()->whereNotNull('penerbit')->where('penerbit', '!=', '')->orderBy('penerbit')->pluck('penerbit'),
            'listPengarang' => static::select('pengarang')->distinct()->whereNotNull('pengarang')->where('pengarang', '!=', '')->orderBy('pengarang')->pluck('pengarang'),
            'listKategori' => static::select('kategori')->distinct()->whereNotNull('kategori')->where('kategori', '!=', '')->orderBy('kategori')->pluck('kategori'),
        ];
    }
}
