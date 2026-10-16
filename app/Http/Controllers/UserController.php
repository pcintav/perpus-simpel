<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Peminjaman;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function dashboard()
    {
        $anggota = Anggota::find(session('anggota_id'));
        if (! $anggota) {
            return redirect()->route('home')->with('error', 'Sesi berakhir. Silakan login kembali.');
        }

        $peminjaman = $anggota->peminjaman()->with('buku')->orderBy('tanggal_pinjam', 'desc')->get();
        $reservasi = \App\Models\Reservasi::with('buku')
            ->where('id_anggota', $anggota->id_anggota)
            ->whereIn('status', [\App\Models\Reservasi::ANTRI, \App\Models\Reservasi::SIAP])
            ->orderBy('tanggal')
            ->get();
        $maxPinjam = (int) config('perpus.max_pinjam', 3);
        $aktifCount = $anggota->pinjamanAktifCount();

        return view('user.dashboard', compact('anggota', 'peminjaman', 'reservasi', 'maxPinjam', 'aktifCount'));
    }

    public function perpus(Request $request)
    {
        $buku = Buku::query()
            ->filterKatalog($request)
            ->withDipinjamCount()
            ->orderBy('tgl_input', 'desc')
            ->paginate(12)
            ->withQueryString();

        return view('user.perpus', array_merge(
            compact('buku'),
            Buku::filterOptions()
        ));
    }

    public function pinjam(Request $request)
    {
        $request->validate(['id_buku' => 'required|integer']);

        $anggotaId = session('anggota_id');
        if (! $anggotaId) {
            return redirect()->route('home')->with('error', 'Sesi berakhir. Silakan login kembali.');
        }

        $anggota = Anggota::find($anggotaId);
        if (! $anggota || ! $anggota->isAktif()) {
            return redirect()->route('home')->with('error', 'Akun tidak aktif. Hubungi admin.');
        }

        $maxPinjam = (int) config('perpus.max_pinjam', 3);
        if ($anggota->pinjamanAktifCount() >= $maxPinjam) {
            return back()->with('error', "Batas maksimal pinjam adalah {$maxPinjam} buku. Kembalikan salah satu terlebih dahulu.");
        }

        $buku = Buku::withDipinjamCount()->find($request->id_buku);
        if (! $buku) {
            return back()->with('error', 'Buku tidak ditemukan.');
        }

        $stokTersedia = $buku->stok - ($buku->dipinjam_count ?? 0);
        if (($buku->kondisi ?? 'Baik') !== 'Baik') {
            return back()->with('error', 'Buku ini berstatus '.($buku->kondisi).' dan tidak bisa dipinjam.');
        }
        if ($stokTersedia < 1) {
            return back()->with('error', 'Stok buku habis. Silakan ajukan reservasi.');
        }

        $siapQuery = \App\Models\Reservasi::where('id_buku', $buku->id_buku)
            ->where('status', \App\Models\Reservasi::SIAP);
        $siapCount = (clone $siapQuery)->count();
        $punyaSiap = (clone $siapQuery)->where('id_anggota', $anggotaId)->exists();
        if ($siapCount > 0 && ! $punyaSiap && $stokTersedia <= $siapCount) {
            return back()->with('error', 'Buku ini sedang ditahan untuk anggota yang sudah antri.');
        }

        $sudahPinjam = Peminjaman::where('id_anggota', $anggotaId)
            ->where('id_buku', $buku->id_buku)
            ->where('status', Peminjaman::STATUS_DIPINJAM)
            ->exists();
        if ($sudahPinjam) {
            return back()->with('error', 'Anda masih meminjam buku ini. Kembalikan terlebih dahulu.');
        }

        $lama = (int) config('perpus.lama_pinjam_hari', 7);

        Peminjaman::create([
            'id_buku' => $buku->id_buku,
            'id_anggota' => $anggotaId,
            'tanggal_pinjam' => Carbon::now(),
            'tanggal_kembali' => Carbon::now()->addDays($lama),
            'status' => Peminjaman::STATUS_DIPINJAM,
            'denda' => 0,
        ]);

        \App\Models\Reservasi::where('id_buku', $buku->id_buku)
            ->where('id_anggota', $anggotaId)
            ->whereIn('status', [\App\Models\Reservasi::ANTRI, \App\Models\Reservasi::SIAP])
            ->update(['status' => \App\Models\Reservasi::SELESAI]);

        return redirect()->back()->with('message', 'Buku "'.$buku->judul.'" berhasil dipinjam. Batas pengembalian: '.$lama.' hari.');
    }

    public function edit_akun()
    {
        $anggota = Anggota::find(session('anggota_id'));
        if (! $anggota) {
            return redirect()->route('home')->with('error', 'Sesi berakhir. Silakan login kembali.');
        }

        return view('user.edit_akun', compact('anggota'));
    }

    public function update_akun(Request $request)
    {
        $anggota = Anggota::find(session('anggota_id'));
        if (! $anggota) {
            return redirect()->route('home')->with('error', 'Sesi berakhir. Silakan login kembali.');
        }

        $rules = [
            'nama' => 'required|string|max:100',
            'kelas' => 'required|string|max:20',
            'nis' => 'required|digits_between:5,7|unique:anggota,nis,'.$anggota->id_anggota.',id_anggota',
            'email' => 'required|email|max:50|unique:anggota,email,'.$anggota->id_anggota.',id_anggota',
            'telp' => 'required|numeric|digits_between:10,15',
            'alamat' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ];
        if ($request->filled('password')) {
            $rules['password'] = 'required|min:8|confirmed';
        }

        $request->validate($rules, [
            'nama.required' => 'Nama wajib diisi.',
            'kelas.required' => 'Kelas wajib diisi.',
            'nis.required' => 'NIS wajib diisi.',
            'nis.digits_between' => 'NIS harus 5 sampai 7 angka.',
            'nis.unique' => 'NIS sudah digunakan anggota lain.',
            'email.unique' => 'Email sudah digunakan anggota lain.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'photo.image' => 'Foto harus berupa gambar (jpeg, jpg, png).',
        ]);

        $anggota->nama = $request->nama;
        $anggota->kelas = $request->kelas;
        $anggota->nis = $request->nis;
        $anggota->email = $request->email;
        $anggota->telp = $request->telp;
        $anggota->alamat = $request->alamat;

        if ($request->filled('password')) {
            $anggota->password = $request->password;
        }

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $fileName = time().'_'.$file->getClientOriginalName();
            $path = 'uploads/anggota';
            $fullPath = public_path($path);
            if (! is_dir($fullPath)) {
                mkdir($fullPath, 0755, true);
            }
            if ($anggota->photo_url && str_starts_with($anggota->photo_url, 'uploads/') && file_exists(public_path($anggota->photo_url))) {
                @unlink(public_path($anggota->photo_url));
            }
            $file->move($fullPath, $fileName);
            $anggota->photo_name = $fileName;
            $anggota->photo_url = $path.'/'.$fileName;
        }

        $anggota->save();
        session(['anggota_nama' => $anggota->nama]);

        return redirect()->route('user.edit_akun')->with('message', 'Profil berhasil diperbarui.');
    }

    public function ajukanPerpanjang(Request $request)
    {
        $request->validate(['id_pinjam' => 'required|integer']);
        $pinjam = Peminjaman::where('id_pinjam', $request->id_pinjam)
            ->where('id_anggota', session('anggota_id'))
            ->where('status', Peminjaman::STATUS_DIPINJAM)
            ->first();

        if (! $pinjam) {
            return back()->with('error', 'Data pinjaman tidak ditemukan.');
        }
        if ($pinjam->perpanjang_status === 'diajukan') {
            return back()->with('error', 'Pengajuan perpanjangan masih menunggu admin.');
        }
        if ($pinjam->perpanjang_status === 'disetujui') {
            return back()->with('error', 'Pinjaman ini sudah pernah diperpanjang.');
        }

        $pinjam->perpanjang_status = 'diajukan';
        $pinjam->perpanjang_hari = (int) config('perpus.lama_pinjam_hari', 7);
        $pinjam->save();

        return back()->with('message', 'Pengajuan perpanjangan terkirim. Menunggu persetujuan admin.');
    }

    public function reservasi(Request $request)
    {
        $request->validate(['id_buku' => 'required|integer']);
        $anggota = Anggota::find(session('anggota_id'));
        if (! $anggota || ! $anggota->isAktif()) {
            return redirect()->route('home')->with('error', 'Akun tidak aktif.');
        }

        $buku = Buku::withDipinjamCount()->find($request->id_buku);
        if (! $buku) {
            return back()->with('error', 'Buku tidak ditemukan.');
        }
        if (($buku->kondisi ?? 'Baik') !== 'Baik') {
            return back()->with('error', 'Buku ini tidak bisa direservasi.');
        }
        if (($buku->stok - ($buku->dipinjam_count ?? 0)) > 0) {
            return back()->with('error', 'Stok masih ada. Silakan pinjam langsung.');
        }

        $sudah = \App\Models\Reservasi::where('id_buku', $buku->id_buku)
            ->where('id_anggota', $anggota->id_anggota)
            ->whereIn('status', [\App\Models\Reservasi::ANTRI, \App\Models\Reservasi::SIAP])
            ->exists();
        if ($sudah) {
            return back()->with('error', 'Anda sudah mengantri buku ini.');
        }

        \App\Models\Reservasi::create([
            'id_buku' => $buku->id_buku,
            'id_anggota' => $anggota->id_anggota,
            'tanggal' => now(),
            'status' => \App\Models\Reservasi::ANTRI,
        ]);

        return back()->with('message', 'Reservasi tercatat. Cek dashboard saat buku siap diambil.');
    }

    public function batalReservasi(Request $request)
    {
        $row = \App\Models\Reservasi::where('id_reservasi', $request->id_reservasi)
            ->where('id_anggota', session('anggota_id'))
            ->whereIn('status', [\App\Models\Reservasi::ANTRI, \App\Models\Reservasi::SIAP])
            ->first();
        if (! $row) {
            return back()->with('error', 'Reservasi tidak ditemukan.');
        }
        $idBuku = (int) $row->id_buku;
        $wasSiap = $row->status === \App\Models\Reservasi::SIAP;
        $row->status = \App\Models\Reservasi::BATAL;
        $row->save();
        if ($wasSiap) {
            \App\Models\Reservasi::siapkanBerikutnya($idBuku);
        }

        return back()->with('message', 'Reservasi dibatalkan.');
    }

    public function kartu()
    {
        $anggota = Anggota::find(session('anggota_id'));
        if (! $anggota) {
            return redirect()->route('home');
        }

        return view('user.kartu', compact('anggota'));
    }
}
