<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * AdminMiddleware - Proteksi Akses Area Admin
 *
 * Middleware ini melindungi route yang hanya boleh diakses oleh admin yang sudah login.
 * Dipakai di routes/web.php via alias 'admin': Route::middleware('admin')->group(...)
 *
 * Cara kerja:
 * - Cek apakah session memiliki kunci 'admin_id' (diset saat login di AdminController::login)
 * - Jika TIDAK ada -> redirect ke admin/index (form login) dengan pesan error
 * - Jika ADA -> lanjutkan request ke controller ($next)
 */
class AdminMiddleware
{
    /**
     * Handle an incoming request - Titik Masuk Middleware
     *
     * Dipanggil otomatis oleh Laravel sebelum request sampai ke controller.
     * Urutan: Request -> Middleware handle() -> [jika lolos] Controller
     *
     * @param Request $request Request HTTP yang masuk
     * @param Closure $next   Closure untuk lanjut ke middleware/controller berikutnya
     * @return Response Redirect ke login ATAU response dari $next($request)
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('admin_id')) {
            return redirect()->route('admin.index')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        return $next($request);
    }
}
