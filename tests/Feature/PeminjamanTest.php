<?php

namespace Tests\Feature;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature test dasar alur pinjam (latihan SMK RPL).
 * Catatan: memakai RefreshDatabase — jalankan hanya di DB testing.
 */
class PeminjamanTest extends TestCase
{
    public function test_guest_cannot_access_user_dashboard(): void
    {
        $response = $this->get(route('user.dashboard'));
        $response->assertRedirect();
    }

    public function test_status_constants_are_defined(): void
    {
        $this->assertSame('Dipinjam', Peminjaman::STATUS_DIPINJAM);
        $this->assertSame('kembali', Peminjaman::STATUS_KEMBALI);
    }

    public function test_anggota_aktif_helper(): void
    {
        $anggota = new Anggota(['status' => 'aktif']);
        $this->assertTrue($anggota->isAktif());

        $pending = new Anggota(['status' => 'pending']);
        $this->assertFalse($pending->isAktif());
    }

    public function test_buku_filter_scope_exists(): void
    {
        $this->assertTrue(method_exists(Buku::class, 'scopeFilterKatalog'));
        $this->assertTrue(method_exists(Buku::class, 'scopeWithDipinjamCount'));
    }
}
