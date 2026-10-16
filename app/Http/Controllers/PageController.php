<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Buku;
use App\Support\Recaptcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PageController extends Controller
{
    public function home()
    {
        return view('home', [
            'recaptchaRequired' => Recaptcha::required(),
        ]);
    }

    public function login(Request $request)
    {
        $rules = [
            'email' => 'required',
            'password' => 'required',
        ];
        if (Recaptcha::required()) {
            $rules['g-recaptcha-response'] = 'required';
        }

        $request->validate($rules, [
            'email.required' => 'Email atau NIS wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'g-recaptcha-response.required' => 'Silakan selesaikan verifikasi reCAPTCHA.',
        ]);

        $captcha = Recaptcha::verify($request);
        if (! $captcha['ok']) {
            return redirect()->route('home')
                ->with('error', $captcha['message'])
                ->withInput($request->only('email'));
        }

        $input = $request->email;
        $anggota = filter_var($input, FILTER_VALIDATE_EMAIL)
            ? Anggota::where('email', $input)->first()
            : Anggota::where('nis', $input)->first();

        if (! $anggota || ! Hash::check($request->password, $anggota->password)) {
            return redirect()->route('home')
                ->with('error', 'Email/NIS atau password salah.')
                ->withInput($request->only('email'));
        }

        if (! $anggota->isAktif()) {
            $msg = ($anggota->status ?? '') === Anggota::STATUS_PENDING
                ? 'Akun Anda masih menunggu persetujuan admin.'
                : 'Akun Anda nonaktif. Hubungi admin perpustakaan.';

            return redirect()->route('home')
                ->with('error', $msg)
                ->withInput($request->only('email'));
        }

        session([
            'anggota_id' => $anggota->id_anggota,
            'anggota_nama' => $anggota->nama,
        ]);

        return redirect()->route('user.dashboard')
            ->with('message', 'Selamat datang, '.$anggota->nama.'!');
    }

    public function daftar()
    {
        return view('daftar', [
            'recaptchaRequired' => Recaptcha::required(),
        ]);
    }

    public function store_daftar(Request $request)
    {
        $rules = [
            'nama' => 'required|string|max:100',
            'kelas' => 'required|string|max:20',
            'nis' => 'required|digits_between:5,7|unique:anggota,nis',
            'email' => 'required|email|max:30|unique:anggota,email',
            'telp' => 'required|numeric|digits_between:10,15',
            'alamat' => 'required|string',
            'password' => 'required|min:8|confirmed',
            'photo' => 'required|image|mimes:jpeg,jpg,png|max:2048',
        ];
        if (Recaptcha::required()) {
            $rules['g-recaptcha-response'] = 'required';
        }

        $request->validate($rules, [
            'nama.required' => 'Nama wajib diisi.',
            'nis.required' => 'NIS wajib diisi.',
            'nis.digits_between' => 'NIS harus 5 sampai 7 angka.',
            'nis.unique' => 'NIS sudah terdaftar.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'photo.required' => 'Foto profil wajib diunggah.',
            'g-recaptcha-response.required' => 'Silakan selesaikan verifikasi reCAPTCHA.',
        ]);

        $captcha = Recaptcha::verify($request);
        if (! $captcha['ok']) {
            return redirect()->route('daftar')
                ->with('error', $captcha['message'])
                ->withInput($request->except('password', 'password_confirmation', 'photo'));
        }

        $file = $request->file('photo');
        $fileName = time().'_'.$file->getClientOriginalName();
        $path = 'uploads/anggota';
        $fullPath = public_path($path);
        if (! is_dir($fullPath)) {
            mkdir($fullPath, 0755, true);
        }
        $file->move($fullPath, $fileName);

        Anggota::create([
            'nama' => $request->nama,
            'kelas' => $request->kelas,
            'nis' => $request->nis,
            'email' => $request->email,
            'telp' => $request->telp,
            'alamat' => $request->alamat,
            'password' => $request->password,
            'photo_name' => $fileName,
            'photo_url' => $path.'/'.$fileName,
            'tgl_daftar' => now(),
            'status' => Anggota::STATUS_PENDING,
        ]);

        return redirect()->route('home')
            ->with('message', 'Pendaftaran berhasil! Akun menunggu persetujuan admin sebelum dapat login.');
    }

    public function list_buku(Request $request)
    {
        $buku = Buku::query()
            ->filterKatalog($request)
            ->withDipinjamCount()
            ->orderBy('tgl_input', 'desc')
            ->paginate(12)
            ->withQueryString();

        return view('list_buku', array_merge(
            compact('buku'),
            Buku::filterOptions()
        ));
    }

    public function logout(Request $request)
    {
        session()->forget(['anggota_id', 'anggota_nama', 'admin_id', 'admin_nama']);

        return redirect()->route('home')->with('message', 'Anda telah logout.');
    }
}
