<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Anggota;
use App\Models\Buku;
use Illuminate\Database\Seeder;
/**
 * Seeder data awal untuk latihan SMK RPL.
 * Aman dijalankan berulang: hanya insert jika email/NIS/judul belum ada.
 */
class PerpustakaanSeeder extends Seeder
{
    public function run(): void
    {
        if (! Admin::where('email', 'admin@perpus.local')->exists()) {
            Admin::create([
                'nama' => 'Admin Perpustakaan',
                'email' => 'admin@perpus.local',
                'nama_sekolah' => 'SMK TI Ponorogo',
                'alamat' => 'Ponorogo',
                'telepon' => 6281234567890,
                'password' => 'admin12345', // cast hashed di model
                'logo_namafile' => 'default.png',
                'logo_url' => 'assets/img/icons8-registration-26 (1).png',
                'role' => 'superadmin',
            ]);
        }

        if (! Anggota::where('nis', '12345')->exists()) {
            Anggota::create([
                'nama' => 'Siswa Demo',
                'kelas' => 'XII RPL 1',
                'nis' => '12345',
                'email' => 'siswa@perpus.local',
                'telp' => 6281234567891,
                'alamat' => 'Ponorogo',
                'password' => 'siswa12345',
                'photo_name' => 'default.png',
                'photo_url' => 'assets/img/virtual-assistant.png',
                'tgl_daftar' => now(),
                'status' => 'aktif',
            ]);
        }

        if (! Buku::where('judul', 'Pemrograman Web Dasar')->exists()) {
            Buku::create([
                'judul' => 'Pemrograman Web Dasar',
                'pengarang' => 'Tim RPL',
                'penerbit' => 'Erlangga',
                'tahun' => 2024,
                'isbn' => '978-602-000000-1',
                'kategori' => 'Teknologi',
                'rak' => 'A1',
                'kondisi' => 'Baik',
                'stok' => 5,
                'bukuimg_name' => 'default.png',
                'bukuimg_url' => 'assets/img/virtual-assistant.png',
                'tgl_input' => now(),
            ]);
        }
    }
}
