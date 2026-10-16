<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Reservasi;
use App\Support\Recaptcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    public function index()
    {
        if (session()->has('admin_id')) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.index', [
            'recaptchaRequired' => Recaptcha::required(),
        ]);
    }

    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];
        if (Recaptcha::required()) {
            $rules['g-recaptcha-response'] = 'required';
        }

        $request->validate($rules, [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'g-recaptcha-response.required' => 'Silakan selesaikan verifikasi reCAPTCHA.',
        ]);

        $captcha = Recaptcha::verify($request);
        if (! $captcha['ok']) {
            return redirect()->route('admin.index')
                ->with('error', $captcha['message'])
                ->withInput($request->only('email'));
        }

        $admin = Admin::where('email', $request->email)->first();

        if (! $admin || ! Hash::check($request->password, $admin->password)) {
            return redirect()->route('admin.index')
                ->with('error', 'Email atau password salah.')
                ->withInput($request->only('email'));
        }

        session([
            'admin_id' => $admin->id,
            'admin_nama' => $admin->nama,
        ]);

        return redirect()->route('admin.dashboard')
            ->with('message', 'Selamat datang, '.$admin->nama.'!');
    }

    public function logout()
    {
        session()->forget(['admin_id', 'admin_nama']);

        return redirect()->route('admin.index')->with('message', 'Anda telah logout.');
    }

    public function dashboard()
    {
        $totalBuku = Buku::count();
        $totalAnggota = Anggota::count();
        $peminjamanAktif = Peminjaman::where('status', Peminjaman::STATUS_DIPINJAM)->count();
        $terlambat = Peminjaman::where('status', Peminjaman::STATUS_DIPINJAM)
            ->where('tanggal_kembali', '<', now())
            ->count();
        $pendingAnggota = Anggota::where('status', Anggota::STATUS_PENDING)->count();
        $anggotaTerbaru = Anggota::orderBy('tgl_daftar', 'desc')->limit(5)->get();
        $peminjamanTerbaru = Peminjaman::with(['buku', 'anggota'])->orderBy('tanggal_pinjam', 'desc')->limit(5)->get();

        return view('admin.dashboard', compact(
            'totalBuku', 'totalAnggota', 'peminjamanAktif', 'terlambat',
            'pendingAnggota', 'anggotaTerbaru', 'peminjamanTerbaru'
        ));
    }

    public function list_user(Request $request)
    {
        $query = Anggota::query()->orderBy('tgl_daftar', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qry) use ($q) {
                $qry->where('nama', 'like', "%{$q}%")
                    ->orWhere('nis', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        $anggota = $query->paginate(15)->withQueryString();

        return view('admin.list_user', compact('anggota'));
    }

    public function update_status_anggota(Request $request)
    {
        $request->validate([
            'id_anggota' => 'required|integer',
            'status' => 'required|in:pending,aktif,nonaktif',
        ]);

        $anggota = Anggota::find($request->id_anggota);
        if (! $anggota) {
            return back()->with('error', 'Anggota tidak ditemukan.');
        }

        $anggota->status = $request->status;
        $anggota->save();
        if ($request->status !== Anggota::STATUS_AKTIF) {
            Reservasi::lepasAnggota((int) $anggota->id_anggota);
        }

        return back()->with('message', 'Status anggota "'.$anggota->nama.'" diubah menjadi '.$request->status.'.');
    }

    public function hapus_user($id)
    {
        $anggota = Anggota::find($id);
        if (! $anggota) {
            return redirect()->route('admin.list_user')->with('error', 'Anggota tidak ditemukan.');
        }

        $masihPinjam = $anggota->peminjaman()->where('status', Peminjaman::STATUS_DIPINJAM)->exists();
        if ($masihPinjam) {
            return redirect()->route('admin.list_user')
                ->with('error', 'Tidak dapat menghapus: anggota masih memiliki buku yang dipinjam.');
        }

        Reservasi::lepasAnggota((int) $anggota->id_anggota);

        // Nonaktifkan saja jika punya riwayat; hapus permanen jika belum pernah pinjam
        if ($anggota->peminjaman()->exists()) {
            $anggota->status = Anggota::STATUS_NONAKTIF;
            $anggota->save();

            return redirect()->route('admin.list_user')
                ->with('message', 'Anggota punya riwayat pinjam — status diubah menjadi nonaktif (data tetap aman).');
        }

        if ($anggota->photo_url && str_starts_with($anggota->photo_url, 'uploads/') && file_exists(public_path($anggota->photo_url))) {
            @unlink(public_path($anggota->photo_url));
        }
        $anggota->delete();

        return redirect()->route('admin.list_user')->with('message', 'Anggota berhasil dihapus.');
    }

    public function stock_buku(Request $request)
    {
        $buku = Buku::query()
            ->filterKatalog($request)
            ->withDipinjamCount()
            ->orderBy('tgl_input', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.stock_buku', array_merge(
            compact('buku'),
            Buku::filterOptions()
        ));
    }

    public function peminjaman(Request $request)
    {
        $query = Peminjaman::with(['buku', 'anggota'])->orderBy('tanggal_pinjam', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qry) use ($q) {
                $qry->whereHas('buku', fn ($b) => $b->where('judul', 'like', "%{$q}%"))
                    ->orWhereHas('anggota', fn ($a) => $a->where('nama', 'like', "%{$q}%"));
            });
        }
        if ($request->boolean('terlambat')) {
            $query->where('status', Peminjaman::STATUS_DIPINJAM)
                ->where('tanggal_kembali', '<', now());
        }

        $peminjaman = $query->paginate(20)->withQueryString();

        return view('admin.peminjaman', compact('peminjaman'));
    }

    public function update_status_peminjaman(Request $request)
    {
        $request->validate([
            'id_pinjam' => 'required|integer',
            'status' => 'required|in:Dipinjam,kembali',
        ]);

        $peminjaman = Peminjaman::find($request->id_pinjam);
        if (! $peminjaman) {
            return back()->with('error', 'Data peminjaman tidak ditemukan.');
        }

        if ($request->status === Peminjaman::STATUS_KEMBALI) {
            $peminjaman->tandaiKembali();
            $msg = 'Buku ditandai dikembalikan.';
            if ($peminjaman->denda > 0) {
                $msg .= ' Denda: Rp '.number_format($peminjaman->denda, 0, ',', '.');
            }

            return back()->with('message', $msg);
        }

        $buku = Buku::withDipinjamCount()->find($peminjaman->id_buku);
        if (! $buku) {
            return back()->with('error', 'Buku peminjaman ini sudah tidak ada.');
        }
        if (($buku->kondisi ?? 'Baik') !== 'Baik') {
            return back()->with('error', 'Buku berstatus '.$buku->kondisi.' dan tidak bisa dipinjam lagi.');
        }
        $tersedia = (int) $buku->stok - (int) ($buku->dipinjam_count ?? 0);
        if ($tersedia < 1) {
            return back()->with('error', 'Tidak bisa koreksi: stok sedang habis.');
        }
        if (Reservasi::where('id_buku', $buku->id_buku)->where('status', Reservasi::SIAP)->exists()) {
            return back()->with('error', 'Tidak bisa koreksi: buku ini sudah ditahan untuk antrian.');
        }
        $anggota = Anggota::find($peminjaman->id_anggota);
        $maxPinjam = (int) config('perpus.max_pinjam', 3);
        if ($anggota && $anggota->pinjamanAktifCount() >= $maxPinjam) {
            return back()->with('error', 'Anggota ini sudah mencapai batas pinjam ('.$maxPinjam.' buku).');
        }

        // Kembalikan ke status Dipinjam (koreksi admin)
        $peminjaman->status = Peminjaman::STATUS_DIPINJAM;
        $peminjaman->tanggal_dikembalikan = null;
        $peminjaman->denda = 0;
        $peminjaman->save();

        return back()->with('message', 'Status diubah menjadi Dipinjam.');
    }

    public function laporan()
    {
        $bukuPopuler = Buku::withCount(['peminjaman as total_pinjam'])
            ->orderByDesc('total_pinjam')
            ->limit(10)
            ->get();

        $anggotaAktif = Anggota::withCount(['peminjaman as total_pinjam'])
            ->orderByDesc('total_pinjam')
            ->limit(10)
            ->get();

        $pinjamBulanIni = Peminjaman::whereMonth('tanggal_pinjam', now()->month)
            ->whereYear('tanggal_pinjam', now()->year)
            ->count();

        $totalDenda = Peminjaman::sum('denda');
        $terlambat = Peminjaman::where('status', Peminjaman::STATUS_DIPINJAM)
            ->where('tanggal_kembali', '<', now())
            ->count();

        $perBulan = Peminjaman::selectRaw('DATE_FORMAT(tanggal_pinjam, "%Y-%m") as bulan, COUNT(*) as total')
            ->groupBy('bulan')
            ->orderBy('bulan', 'desc')
            ->limit(6)
            ->get();

        return view('admin.laporan', compact(
            'bukuPopuler', 'anggotaAktif', 'pinjamBulanIni',
            'totalDenda', 'terlambat', 'perBulan'
        ));
    }

    public function export_peminjaman(): StreamedResponse
    {
        $filename = 'peminjaman_'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Judul Buku', 'Anggota', 'Tgl Pinjam', 'Wajib Kembali', 'Tgl Dikembalikan', 'Status', 'Denda']);

            Peminjaman::with(['buku', 'anggota'])->orderBy('id_pinjam')->chunk(200, function ($rows) use ($out) {
                foreach ($rows as $item) {
                    fputcsv($out, [
                        $item->id_pinjam,
                        $item->buku->judul ?? '-',
                        $item->anggota->nama ?? '-',
                        optional($item->tanggal_pinjam)->format('Y-m-d H:i'),
                        optional($item->tanggal_kembali)->format('Y-m-d H:i'),
                        optional($item->tanggal_dikembalikan)->format('Y-m-d H:i'),
                        $item->status_label,
                        $item->denda ?? 0,
                    ]);
                }
            });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function edit_admin()
    {
        $admin = Admin::find(session('admin_id'));
        if (! $admin) {
            return redirect()->route('admin.index')->with('error', 'Sesi berakhir. Silakan login kembali.');
        }

        return view('admin.edit_admin', compact('admin'));
    }

    public function update_admin(Request $request)
    {
        $admin = Admin::find(session('admin_id'));
        if (! $admin) {
            return redirect()->route('admin.index')->with('error', 'Sesi berakhir.');
        }

        $rules = [
            'nama' => 'required|string|max:100',
            'email' => 'required|email|max:50|unique:admin,email,'.$admin->id,
            'nama_sekolah' => 'required|string|max:100',
            'alamat' => 'required|string|max:50',
            'telepon' => 'required|numeric|digits_between:10,15',
            'logo' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ];
        if ($request->filled('password')) {
            $rules['password'] = 'required|min:8|confirmed';
        }

        $request->validate($rules, [
            'nama.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah digunakan admin lain.',
            'nama_sekolah.required' => 'Nama sekolah wajib diisi.',
            'alamat.required' => 'Alamat wajib diisi.',
            'telepon.required' => 'Telepon wajib diisi.',
            'logo.image' => 'Logo harus berupa gambar (jpeg, jpg, png).',
        ]);

        $admin->nama = $request->nama;
        $admin->email = $request->email;
        $admin->nama_sekolah = $request->nama_sekolah;
        $admin->alamat = $request->alamat;
        $admin->telepon = $request->telepon;

        if ($request->filled('password')) {
            $admin->password = $request->password;
        }

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $fileName = time().'_'.$file->getClientOriginalName();
            $path = 'uploads/admin';
            $fullPath = public_path($path);
            if (! is_dir($fullPath)) {
                mkdir($fullPath, 0755, true);
            }
            if ($admin->logo_url && str_starts_with($admin->logo_url, 'uploads/') && file_exists(public_path($admin->logo_url))) {
                @unlink(public_path($admin->logo_url));
            }
            $file->move($fullPath, $fileName);
            $admin->logo_namafile = $fileName;
            $admin->logo_url = $path.'/'.$fileName;
        }

        $admin->tgl_update = now();
        $admin->save();
        session(['admin_nama' => $admin->nama]);

        return redirect()->route('admin.edit_admin')->with('message', 'Profil berhasil diperbarui.');
    }

    public function edit_user($id)
    {
        $anggota = Anggota::find($id);
        if (! $anggota) {
            return redirect()->route('admin.list_user')->with('message', 'Data tidak ditemukan');
        }

        return view('admin.edit_user', compact('anggota'));
    }

    public function update_user(Request $request)
    {
        $anggota = Anggota::find($request->id_anggota);
        if (! $anggota) {
            return redirect()->route('admin.list_user')->with('error', 'Data anggota tidak ditemukan.');
        }

        $rules = [
            'nama' => 'required|string|max:100',
            'kelas' => 'required|string|max:50',
            'nis' => 'required|string|max:20|unique:anggota,nis,'.$anggota->id_anggota.',id_anggota',
            'email' => 'required|email|unique:anggota,email,'.$anggota->id_anggota.',id_anggota',
            'telp' => 'required|string|max:15',
            'alamat' => 'required|string|max:255',
            'status' => 'required|in:pending,aktif,nonaktif',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ];
        if ($request->filled('password')) {
            $rules['password'] = 'required|min:8';
        }

        $request->validate($rules, [
            'nama.required' => 'Nama wajib diisi.',
            'email.unique' => 'Email sudah digunakan anggota lain.',
            'nis.unique' => 'NIS sudah digunakan anggota lain.',
        ]);

        $anggota->nama = $request->nama;
        $anggota->kelas = $request->kelas;
        $anggota->nis = $request->nis;
        $anggota->email = $request->email;
        $anggota->telp = $request->telp;
        $anggota->alamat = $request->alamat;
        $anggota->status = $request->status;

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
        if ($anggota->status !== Anggota::STATUS_AKTIF) {
            Reservasi::lepasAnggota((int) $anggota->id_anggota);
        }

        return redirect()->route('admin.edit_user', $anggota->id_anggota)
            ->with('message', 'Data anggota berhasil diperbarui.');
    }

    public function edit_buku($id)
    {
        $buku = Buku::find($id);
        if (! $buku) {
            return redirect()->route('admin.stock_buku')->with('error', 'Buku tidak ditemukan.');
        }

        return view('admin.edit_buku', compact('buku'));
    }

    public function update_buku(Request $request)
    {
        $buku = Buku::find($request->id_buku);
        if (! $buku) {
            return redirect()->route('admin.stock_buku')->with('error', 'Buku tidak ditemukan.');
        }

        $request->validate([
            'judul' => 'required|string|max:150',
            'pengarang' => 'required|string|max:100',
            'penerbit' => 'required|string|max:100',
            'tahun' => 'required|integer|min:1900|max:2099',
            'isbn' => 'nullable|string|max:30',
            'kategori' => 'nullable|string|max:50',
            'rak' => 'nullable|string|max:30',
            'kondisi' => 'nullable|in:Baik,Rusak,Hilang',
            'stok' => 'required|integer|min:0',
            'bukuimg' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ], [
            'judul.required' => 'Judul buku wajib diisi.',
            'pengarang.required' => 'Pengarang wajib diisi.',
            'penerbit.required' => 'Penerbit wajib diisi.',
        ]);

        $buku->judul = $request->judul;
        $buku->pengarang = $request->pengarang;
        $buku->penerbit = $request->penerbit;
        $buku->tahun = $request->tahun;
        $buku->isbn = $request->isbn;
        $buku->kategori = $request->kategori;
        $buku->rak = $request->rak;
        $sedangDipinjam = $buku->peminjaman()->where('status', Peminjaman::STATUS_DIPINJAM)->count();
        if ((int) $request->stok < $sedangDipinjam) {
            return back()->withInput()->with('error', 'Stok tidak boleh lebih kecil dari yang sedang dipinjam ('.$sedangDipinjam.').');
        }

        $buku->kondisi = $request->kondisi ?: 'Baik';
        $buku->stok = $request->stok;

        if ($request->hasFile('bukuimg')) {
            $file = $request->file('bukuimg');
            $fileName = time().'_'.$file->getClientOriginalName();
            $path = 'uploads/buku';
            $fullPath = public_path($path);
            if (! is_dir($fullPath)) {
                mkdir($fullPath, 0755, true);
            }
            if ($buku->bukuimg_url && str_starts_with($buku->bukuimg_url, 'uploads/') && file_exists(public_path($buku->bukuimg_url))) {
                @unlink(public_path($buku->bukuimg_url));
            }
            $file->move($fullPath, $fileName);
            $buku->bukuimg_name = $fileName;
            $buku->bukuimg_url = $path.'/'.$fileName;
        }

        $buku->save();

        return redirect()->route('admin.stock_buku')->with('message', 'Data buku berhasil diperbarui.');
    }

    public function hapus_buku($id)
    {
        $buku = Buku::find($id);
        if (! $buku) {
            return redirect()->route('admin.stock_buku')->with('error', 'Buku tidak ditemukan.');
        }

        $masihDipinjam = $buku->peminjaman()->where('status', Peminjaman::STATUS_DIPINJAM)->exists();
        if ($masihDipinjam) {
            return redirect()->route('admin.stock_buku')
                ->with('error', 'Tidak dapat menghapus: buku masih dipinjam. Tandai dikembalikan dulu.');
        }

        // Hapus hanya riwayat yang sudah kembali, lalu buku
        Reservasi::where('id_buku', $id)->delete();
        Peminjaman::where('id_buku', $id)->delete();
        if ($buku->bukuimg_url && str_starts_with($buku->bukuimg_url, 'uploads/') && file_exists(public_path($buku->bukuimg_url))) {
            @unlink(public_path($buku->bukuimg_url));
        }
        $judul = $buku->judul;
        $buku->delete();

        return redirect()->route('admin.stock_buku')->with('message', 'Buku "'.$judul.'" berhasil dihapus.');
    }

    public function tambah_buku()
    {
        return view('admin.tambah_buku');
    }

    public function store_buku(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:150',
            'pengarang' => 'required|string|max:100',
            'penerbit' => 'required|string|max:100',
            'tahun' => 'required|integer|min:1900|max:2099',
            'isbn' => 'nullable|string|max:30',
            'kategori' => 'nullable|string|max:50',
            'rak' => 'nullable|string|max:30',
            'kondisi' => 'nullable|in:Baik,Rusak,Hilang',
            'stok' => 'required|integer|min:0',
            'bukuimg' => 'required|image|mimes:jpeg,jpg,png|max:2048',
        ], [
            'judul.required' => 'Judul buku wajib diisi.',
            'pengarang.required' => 'Pengarang wajib diisi.',
            'penerbit.required' => 'Penerbit wajib diisi.',
            'bukuimg.required' => 'Cover buku wajib diunggah.',
            'bukuimg.image' => 'Cover buku harus berupa gambar (jpeg, jpg, png).',
        ]);

        $bukuimgName = '';
        $bukuimgUrl = '';

        if ($request->hasFile('bukuimg')) {
            $file = $request->file('bukuimg');
            $fileName = time().'_'.$file->getClientOriginalName();
            $path = 'uploads/buku';
            $fullPath = public_path($path);
            if (! is_dir($fullPath)) {
                mkdir($fullPath, 0755, true);
            }
            $file->move($fullPath, $fileName);
            $bukuimgName = $fileName;
            $bukuimgUrl = $path.'/'.$fileName;
        }

        Buku::create([
            'judul' => $request->judul,
            'pengarang' => $request->pengarang,
            'penerbit' => $request->penerbit,
            'tahun' => $request->tahun,
            'isbn' => $request->isbn,
            'kategori' => $request->kategori,
            'rak' => $request->rak,
            'kondisi' => $request->kondisi ?: 'Baik',
            'stok' => $request->stok,
            'bukuimg_name' => $bukuimgName,
            'bukuimg_url' => $bukuimgUrl,
            'tgl_input' => now(),
        ]);

        return redirect()->route('admin.stock_buku')->with('message', 'Buku berhasil ditambahkan.');
    }

    public function prosesPerpanjang(Request $request)
    {
        $request->validate([
            'id_pinjam' => 'required|integer',
            'keputusan' => 'required|in:disetujui,ditolak',
        ]);
        $pinjam = Peminjaman::find($request->id_pinjam);
        if (! $pinjam || $pinjam->perpanjang_status !== 'diajukan') {
            return back()->with('error', 'Tidak ada pengajuan perpanjangan.');
        }
        if ($pinjam->status !== Peminjaman::STATUS_DIPINJAM) {
            return back()->with('error', 'Pinjaman ini sudah dikembalikan.');
        }
        if ($request->keputusan === 'disetujui') {
            $hari = (int) ($pinjam->perpanjang_hari ?: config('perpus.lama_pinjam_hari', 7));
            $dasar = $pinjam->tanggal_kembali ?: now();
            $pinjam->tanggal_kembali = $dasar->copy()->addDays($hari);
            $pinjam->perpanjang_status = 'disetujui';
            $pinjam->save();

            return back()->with('message', 'Perpanjangan disetujui '.$hari.' hari.');
        }
        $pinjam->perpanjang_status = 'ditolak';
        $pinjam->save();

        return back()->with('message', 'Perpanjangan ditolak.');
    }

    public function lunasiDenda(Request $request)
    {
        $pinjam = Peminjaman::find($request->id_pinjam);
        if (! $pinjam || (int) $pinjam->denda <= 0) {
            return back()->with('error', 'Tidak ada denda pada data ini.');
        }
        $pinjam->denda_lunas = true;
        $pinjam->tgl_bayar_denda = now();
        $pinjam->save();

        return redirect()->route('admin.kwitansi', $pinjam->id_pinjam);
    }

    public function kwitansi($id)
    {
        $pinjam = Peminjaman::with(['buku', 'anggota'])->find($id);
        if (! $pinjam) {
            return redirect()->route('admin.peminjaman')->with('error', 'Data tidak ditemukan.');
        }

        return view('admin.kwitansi', compact('pinjam'));
    }

    public function kartu($id)
    {
        $anggota = Anggota::find($id);
        if (! $anggota) {
            return redirect()->route('admin.list_user')->with('error', 'Anggota tidak ditemukan.');
        }

        return view('user.kartu', compact('anggota'));
    }

    public function rekapKelas()
    {
        $baris = Anggota::with(['peminjaman' => function ($q) {
            $q->where('status', Peminjaman::STATUS_DIPINJAM)->with('buku');
        }])->orderBy('kelas')->orderBy('nama')->get();

        return view('admin.rekap_kelas', compact('baris'));
    }
}
