<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use App\Models\Admin;

/**
 * AppServiceProvider - Service Provider Utama Aplikasi
 *
 * Provider ini di-load saat aplikasi bootstrap (bootstrap/providers.php).
 * Dua method utama: register() untuk binding ke container, boot() untuk
 * inisialisasi setelah semua service ter-register.
 *
 * Di project ini: boot() dipakai untuk View::composer (share data ke view
 * otomatis setiap kali view tertentu di-render). Tanpa View::composer,
 * tiap controller harus pass namaSekolah dan logoUrl ke view - redundant.
 *
 * Asal: extends Illuminate\Support\ServiceProvider (bawaan Laravel).
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * Dipanggil saat bootstrap. Dipakai untuk binding class/interface ke
     * container (contoh: $this->app->singleton(...)). Di project ini kosong
     * karena tidak ada custom binding yang dibutuhkan.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * Dipanggil setelah semua service ter-register. Dipakai untuk
     * View::composer: setiap kali view inc.header, inc.header_user, atau
     * inc.header_adm di-render, variabel namaSekolah dan logoUrl otomatis
     * tersedia (tanpa perlu pass dari controller).
     *
     * View::composer(view, callback): callback dijalankan SEBELUM view
     * di-render. $view->with() menambah variabel ke view.
     *
     * @return void
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        /**
         * Composer untuk header publik (home, daftar, list_buku) dan header user (anggota).
         * Ambil admin PERTAMA dari database (nama sekolah & logo umum).
         * Jika belum ada admin atau tabel kosong: fallback 'Sekolah' dan default image.
         */
        View::composer(['inc.header', 'inc.header_user', 'user.kartu'], function ($view) {
            $admin = Admin::first();
            $view->with('namaSekolah', $admin ? $admin->nama_sekolah : 'Sekolah');
            $view->with('logoUrl', ($admin && $admin->logo_url) ? asset($admin->logo_url) : asset('assets/img/virtual-assistant.png'));
        });

        /**
         * Composer untuk header admin (inc.header_adm).
         * Beda dengan di atas: pakai admin yang SEDANG LOGIN (session admin_id).
         * Jika admin belum login: fallback ke Admin::first() seperti header publik.
         * Fallback image: icons8-registration (bawaan area admin).
         */
        View::composer('inc.header_adm', function ($view) {
            $admin = session('admin_id') ? Admin::find(session('admin_id')) : Admin::first();
            $view->with('namaSekolah', $admin ? $admin->nama_sekolah : 'Sekolah');
            $view->with('logoUrl', ($admin && $admin->logo_url) ? asset($admin->logo_url) : asset('assets/img/icons8-registration-26 (1).png'));
        });
    }
}
