<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * AnggotaMiddleware - Proteksi Akses Area Anggota (User/Siswa)
 *
 * Middleware ini melindungi route yang hanya boleh diakses oleh anggota yang sudah login.
 * Dipakai di routes/web.php via alias 'anggota': Route::prefix('user')->middleware('anggota')
 *
 * Cara kerja:
 * - Cek apakah session memiliki kunci 'anggota_id' (diset saat login di PageController::login)
 * - Jika TIDAK ada -> redirect ke home (halaman login umum) dengan pesan error
 * - Jika ADA -> lanjutkan request ke UserController
 */
class AnggotaMiddleware
{
    /**
     * Handle an incoming request - Titik Masuk Middleware
     *
     * Dipanggil otomatis oleh Laravel sebelum request sampai ke controller.
     * Berbeda dengan AdminMiddleware: redirect ke 'home' (bukan admin/index)
     * karena anggota login dari halaman home, bukan halaman admin.
     *
     * @param Request $request Request HTTP yang masuk
     * @param Closure $next   Closure untuk lanjut ke middleware/controller berikutnya
     * @return Response Redirect ke home ATAU response dari $next($request)
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('anggota_id')) {
            return redirect()->route('home')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        return $next($request);
    }
}
