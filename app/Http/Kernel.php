<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

/**
 * HTTP Kernel - Pengaturan Middleware Aplikasi Laravel
 *
 * Class ini mengatur seluruh middleware yang berjalan pada aplikasi. Middleware
 * adalah "penyaring" request yang dijalankan SEBELUM request sampai ke controller.
 * Asalnya: extends Illuminate\Foundation\Http\Kernel (inti Laravel).
 *
 * Terdapat 3 properti utama:
 * - $middleware       : Jalan di SETIAP request (global)
 * - $middlewareGroups : Grup middleware (web/api) yang dipakai di routes
 * - $middlewareAliases: Nama singkat agar bisa dipanggil di Route (contoh: 'admin')
 */
class Kernel extends HttpKernel
{
    /**
     * Global Middleware - Berjalan di SETIAP Request
     *
     * Middleware ini dijalankan untuk SEMUA request (baik web maupun API) dan
     * tidak perlu dideklarasikan di route. Urutan eksekusi: dari atas ke bawah.
     *
     * Daftar dan fungsi masing-masing:
     * - TrustProxies        : Percaya header dari proxy (nginx, load balancer)
     * - HandleCors          : Tangani Cross-Origin Resource Sharing (untuk API)
     * - PreventRequestsDuringMaintenance : Blokir request saat php artisan down
     * - ValidatePostSize   : Tolak request jika body POST terlalu besar
     * - TrimStrings        : Hapus spasi di awal/akhir string input
     * - ConvertEmptyStringsToNull : Ubah string kosong "" menjadi null
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * Middleware Groups - Kumpulan Middleware per Jenis Route
     *
     * Di routes/web.php dan routes/api.php, route otomatis mendapat grup ini.
     * Route web mendapat grup 'web', route api mendapat grup 'api'.
     *
     * Grup 'web' (untuk halaman HTML, form, session):
     * - EncryptCookies        : Enkripsi cookie untuk keamanan
     * - AddQueuedCookiesToResponse : Tambah cookie yang di-queue ke response
     * - StartSession          : Jalankan session (agar session() berfungsi)
     * - ShareErrorsFromSession : Kirim $errors ke view (untuk validasi)
     * - VerifyCsrfToken       : Cek token CSRF pada POST/PUT/DELETE (anti CSRF)
     * - SubstituteBindings    : Resolve route model binding (contoh: Route::get('/user/{user}'))
     *
     * Grup 'api' (untuk API JSON):
     * - ThrottleRequests:api  : Batasi rate request per menit (konfigurasi di RouteServiceProvider)
     * - SubstituteBindings    : Resolve route model binding
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * Middleware Aliases - Nama Singkat untuk Dipakai di Route
     *
     * Alih-alih menulis class lengkap, kita bisa pakai nama alias di route:
     * Route::get('/admin/dashboard')->middleware('admin');
     *
     * Daftar alias dan fungsinya:
     *
     * auth            : Wajib login (Laravel default, pakai User model)
     * auth.basic      : HTTP Basic Authentication
     * auth.session    : Validasi session untuk SPA
     * cache.headers   : Set header Cache-Control pada response
     * can              : Cek authorization (Policy/Gate), contoh: ->middleware('can:edit,post')
     * guest            : Hanya untuk tamu (redirect jika sudah login)
     * password.confirm: Wajib konfirmasi password sebelum aksi sensitif
     * precognitive     : Untuk form precognition (Laravel precognition)
     * signed           : Validasi URL bertanda (untuk link aman via email)
     * throttle         : Rate limiting, contoh: ->middleware('throttle:60,1')
     * verified         : Email harus sudah diverifikasi
     *
     * Kustom project ini:
     * admin    : Cek session admin_id, redirect ke admin/index jika belum login (AdminMiddleware)
     * anggota  : Cek session anggota_id, redirect ke home jika belum login (AnggotaMiddleware)
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
        'anggota' => \App\Http\Middleware\AnggotaMiddleware::class,
    ];
}
