<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Menambah kolom fitur baru tanpa merusak data lama.
 * Semua kolom baru nullable / punya default.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('anggota') && ! Schema::hasColumn('anggota', 'status')) {
            Schema::table('anggota', function (Blueprint $table) {
                $table->string('status', 20)->default('aktif');
            });
        }

        if (Schema::hasTable('buku')) {
            if (! Schema::hasColumn('buku', 'isbn')) {
                Schema::table('buku', function (Blueprint $table) {
                    $table->string('isbn', 30)->nullable();
                });
            }
            if (! Schema::hasColumn('buku', 'kategori')) {
                Schema::table('buku', function (Blueprint $table) {
                    $table->string('kategori', 50)->nullable();
                });
            }
            if (! Schema::hasColumn('buku', 'rak')) {
                Schema::table('buku', function (Blueprint $table) {
                    $table->string('rak', 30)->nullable();
                });
            }
            if (! Schema::hasColumn('buku', 'kondisi')) {
                Schema::table('buku', function (Blueprint $table) {
                    $table->string('kondisi', 20)->default('Baik');
                });
            }
        }

        // Perbaiki ENUM status yang rusak (ada nilai '' duplikat) sebelum ALTER
        if (Schema::hasTable('peminjaman')) {
            try {
                DB::statement("ALTER TABLE `peminjaman` MODIFY `status` ENUM('Dipinjam','kembali') NOT NULL DEFAULT 'Dipinjam'");
            } catch (\Throwable $e) {
                // Abaikan jika sudah valid
            }

            if (! Schema::hasColumn('peminjaman', 'tanggal_dikembalikan')) {
                Schema::table('peminjaman', function (Blueprint $table) {
                    $table->dateTime('tanggal_dikembalikan')->nullable();
                });
            }
            if (! Schema::hasColumn('peminjaman', 'denda')) {
                Schema::table('peminjaman', function (Blueprint $table) {
                    $table->unsignedInteger('denda')->default(0);
                });
            }
            if (! Schema::hasColumn('peminjaman', 'denda_lunas')) {
                Schema::table('peminjaman', function (Blueprint $table) {
                    $table->boolean('denda_lunas')->default(false);
                });
            }
            if (! Schema::hasColumn('peminjaman', 'tgl_bayar_denda')) {
                Schema::table('peminjaman', function (Blueprint $table) {
                    $table->dateTime('tgl_bayar_denda')->nullable();
                });
            }
            if (! Schema::hasColumn('peminjaman', 'perpanjang_status')) {
                Schema::table('peminjaman', function (Blueprint $table) {
                    $table->string('perpanjang_status', 20)->nullable();
                });
            }
            if (! Schema::hasColumn('peminjaman', 'perpanjang_hari')) {
                Schema::table('peminjaman', function (Blueprint $table) {
                    $table->integer('perpanjang_hari')->default(0);
                });
            }
        }

        if (! Schema::hasTable('reservasi')) {
            Schema::create('reservasi', function (Blueprint $table) {
                $table->increments('id_reservasi');
                $table->unsignedInteger('id_buku');
                $table->unsignedInteger('id_anggota');
                $table->dateTime('tanggal');
                $table->string('status', 20)->default('antri');
            });
        }

        if (Schema::hasTable('admin') && ! Schema::hasColumn('admin', 'role')) {
            Schema::table('admin', function (Blueprint $table) {
                $table->string('role', 20)->default('admin');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('anggota') && Schema::hasColumn('anggota', 'status')) {
            Schema::table('anggota', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }

        if (Schema::hasTable('buku')) {
            foreach (['rak', 'kategori', 'isbn'] as $col) {
                if (Schema::hasColumn('buku', $col)) {
                    Schema::table('buku', function (Blueprint $table) use ($col) {
                        $table->dropColumn($col);
                    });
                }
            }
        }

        if (Schema::hasTable('peminjaman')) {
            foreach (['denda', 'tanggal_dikembalikan'] as $col) {
                if (Schema::hasColumn('peminjaman', $col)) {
                    Schema::table('peminjaman', function (Blueprint $table) use ($col) {
                        $table->dropColumn($col);
                    });
                }
            }
        }

        if (Schema::hasTable('admin') && Schema::hasColumn('admin', 'role')) {
            Schema::table('admin', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
    }
};
