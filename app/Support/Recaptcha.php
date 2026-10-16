<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Helper verifikasi Google reCAPTCHA v2.
 * Skip otomatis di APP_ENV=local jika PERPUS_SKIP_RECAPTCHA_LOCAL=true.
 */
class Recaptcha
{
    /**
     * Verifikasi token reCAPTCHA.
     *
     * @return array{ok: bool, message: string|null}
     */
    public static function verify(Request $request): array
    {
        if (app()->environment('local') && config('perpus.skip_recaptcha_local')) {
            return ['ok' => true, 'message' => null];
        }

        $secret = config('services.recaptcha.secret');
        if (empty($secret)) {
            return ['ok' => false, 'message' => 'reCAPTCHA belum dikonfigurasi. Hubungi admin.'];
        }

        try {
            $response = Http::timeout(8)->asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secret,
                'response' => $request->input('g-recaptcha-response'),
                'remoteip' => $request->ip(),
            ]);
            $result = $response->json();
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => 'Gagal menghubungi server reCAPTCHA. Cek koneksi internet atau coba lagi.'];
        }

        if (!($result['success'] ?? false)) {
            return ['ok' => false, 'message' => 'Verifikasi reCAPTCHA gagal. Silakan selesaikan captcha dan coba lagi.'];
        }

        return ['ok' => true, 'message' => null];
    }

    /** Apakah form perlu menampilkan widget reCAPTCHA */
    public static function required(): bool
    {
        if (app()->environment('local') && config('perpus.skip_recaptcha_local')) {
            return false;
        }

        return ! empty(config('services.recaptcha.secret'));
    }
}
