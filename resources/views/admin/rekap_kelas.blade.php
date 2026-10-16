@include('inc.header_adm')
<div class="container py-4">
    <h4 class="fw-bold mb-3">Rekap Peminjaman per Kelas</h4>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Kelas</th>
                            <th>Nama</th>
                            <th>NIS</th>
                            <th>Masih dipinjam</th>
                            <th>Judul</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($baris as $a)
                        <tr>
                            <td>{{ $a->kelas }}</td>
                            <td>{{ $a->nama }}</td>
                            <td>{{ $a->nis }}</td>
                            <td>{{ $a->peminjaman->count() }}</td>
                            <td>
                                @forelse($a->peminjaman as $p)
                                    {{ $p->buku->judul ?? '-' }}@if(!$loop->last), @endif
                                @empty
                                    -
                                @endforelse
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center">Belum ada anggota</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@include('inc.footer_adm')
