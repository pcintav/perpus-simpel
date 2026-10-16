<?php

/**
 * Konfigurasi bisnis Sistem Perpustakaan (SMK RPL)
 *
 * Nilai dapat diubah di .env tanpa mengubah kode.
 */
return [

    /** Maksimal buku yang boleh dipinjam bersamaan per anggota */
    'max_pinjam' => (int) env('PERPUS_MAX_PINJAM', 3),

    /** Lama pinjam default (hari) */
    'lama_pinjam_hari' => (int) env('PERPUS_LAMA_PINJAM', 7),

    /** Denda per hari keterlambatan (rupiah) */
    'denda_per_hari' => (int) env('PERPUS_DENDA_PER_HARI', 1000),

    /**
     * Skip verifikasi Google reCAPTCHA di environment local
     * (berguna untuk lab sekolah yang memblokir Google).
     */
    'skip_recaptcha_local' => (bool) env('PERPUS_SKIP_RECAPTCHA_LOCAL', true),
];
