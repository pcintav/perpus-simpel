<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Membuat tabel domain jika belum ada (aman untuk DB existing).
 * Tidak mengubah tabel yang sudah ada.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('admin')) {
            Schema::create('admin', function (Blueprint $table) {
                $table->increments('id');
                $table->string('nama', 100);
                $table->string('email', 50);
                $table->string('nama_sekolah', 100);
                $table->string('alamat', 50);
                $table->bigInteger('telepon');
                $table->string('password', 256);
                $table->string('logo_namafile', 256);
                $table->string('logo_url', 256);
                $table->dateTime('tgl_update')->nullable();
            });
        }

        if (! Schema::hasTable('anggota')) {
            Schema::create('anggota', function (Blueprint $table) {
                $table->increments('id_anggota');
                $table->string('nama', 100);
                $table->string('kelas', 20);
                $table->string('nis', 20);
                $table->string('email', 30);
                $table->bigInteger('telp');
                $table->text('alamat');
                $table->string('password', 256);
                $table->string('photo_name', 256);
                $table->string('photo_url', 256);
                $table->dateTime('tgl_daftar');
            });
        }

        if (! Schema::hasTable('buku')) {
            Schema::create('buku', function (Blueprint $table) {
                $table->increments('id_buku');
                $table->string('judul', 150);
                $table->string('pengarang', 100);
                $table->string('penerbit', 100);
                $table->integer('tahun');
                $table->integer('stok');
                $table->string('bukuimg_name', 256);
                $table->string('bukuimg_url', 256);
                $table->dateTime('tgl_input');
            });
        }

        if (! Schema::hasTable('peminjaman')) {
            Schema::create('peminjaman', function (Blueprint $table) {
                $table->increments('id_pinjam');
                $table->unsignedInteger('id_buku');
                $table->unsignedInteger('id_anggota');
                $table->dateTime('tanggal_pinjam');
                $table->dateTime('tanggal_kembali');
                $table->string('status', 20)->default('Dipinjam');
            });
        }
    }

    public function down(): void
    {
        // Tidak drop tabel agar data existing aman.
    }
};
