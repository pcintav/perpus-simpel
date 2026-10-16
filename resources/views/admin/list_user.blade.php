@include('inc.header_adm')

<div class="container-fluid bg-light min-vh-100 py-5">
    <div class="container">

        @if(session('message'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="mb-4">
            <h4 class="fw-bold mb-1" style="color: rgb(88, 41, 150);">
                <i class="bi bi-people-fill me-2"></i>List Anggota
            </h4>
            <p class="text-muted mb-0">Data anggota perpustakaan</p>
        </div>

        <form method="GET" action="{{ route('admin.list_user') }}" class="row mb-4 g-2">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0 ps-0"
                           placeholder="Cari nama, NIS, atau email...">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending" @selected(request('status')==='pending')>Pending</option>
                    <option value="aktif" @selected(request('status')==='aktif')>Aktif</option>
                    <option value="nonaktif" @selected(request('status')==='nonaktif')>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn text-white w-100" style="background:#0f766e;">Filter</button>
            </div>
        </form>

        <!-- Table -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Foto</th>
                                <th>Nama</th>
                                <th>Kelas</th>
                                <th>NIS</th>
                                <th>Email</th>
                                <th>Telp</th>
                                <th>Tanggal Daftar</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($anggota as $row)
                            <tr class="anggota-row">
                                <td>{{ $anggota->firstItem() + $loop->index }}</td>
                                <td>
                                    @if($row->photo_url)
                                        <img src="{{ asset($row->photo_url) }}" alt="" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="bi bi-person text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="fw-semibold">{{ $row->nama }}</td>
                                <td>{{ $row->kelas }}</td>
                                <td>{{ $row->nis }}</td>
                                <td>{{ $row->email }}</td>
                                <td>{{ $row->telp }}</td>
                                <td>{{ $row->tgl_daftar ? \Carbon\Carbon::parse($row->tgl_daftar)->format('d M Y') : '-' }}</td>
                                <td>
                                    @php $st = $row->status ?? 'aktif'; @endphp
                                    @if($st === 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($st === 'nonaktif')
                                        <span class="badge bg-secondary">Nonaktif</span>
                                    @else
                                        <span class="badge bg-success">Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" title="Detail"
                                                data-bs-toggle="modal" data-bs-target="#detailModal{{ $row->id_anggota }}">
                                            <i class="bi bi-eye-fill"></i>
                                        </button>
                                        <a href="{{ route('admin.kartu', $row->id_anggota) }}" class="btn btn-outline-secondary" title="Kartu" target="_blank">
                                            <i class="bi bi-person-badge"></i>
                                        </a>
                                        <a href="{{ route('admin.edit_user', $row->id_anggota) }}" class="btn btn-outline-success" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        @if(($row->status ?? '') === 'pending')
                                        <form action="{{ route('admin.anggota.status') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="id_anggota" value="{{ $row->id_anggota }}">
                                            <input type="hidden" name="status" value="aktif">
                                            <button type="submit" class="btn btn-outline-info" title="Setujui">✓</button>
                                        </form>
                                        @endif
                                        <form action="{{ route('admin.hapus_user', $row->id_anggota) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Hapus/nonaktifkan anggota ini?');">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center py-5">
                                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                                    <p class="mt-2 mb-0">Belum ada data anggota</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $anggota->links() }}</div>

                @foreach($anggota as $row)
                <div class="modal fade" id="detailModal{{ $row->id_anggota }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content rounded-4 border-0 shadow-lg">
                            <div class="modal-header border-0 text-white" style="background: #1f4e79;">
                                <h5 class="modal-title fw-bold">Detail Anggota</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-4">
                                @if($row->photo_url)
                                    <div class="text-center mb-4">
                                        <img src="{{ asset($row->photo_url) }}" class="img-fluid rounded shadow-sm"
                                             alt="Foto" style="max-height: 200px;">
                                    </div>
                                @endif
                                <p><strong>Nama:</strong> {{ $row->nama }}</p>
                                <p><strong>Kelas:</strong> {{ $row->kelas }}</p>
                                <p><strong>NIS:</strong> {{ $row->nis }}</p>
                                <p><strong>Email:</strong> {{ $row->email }}</p>
                                <p><strong>Telp:</strong> {{ $row->telp }}</p>
                                <p><strong>Alamat:</strong> {{ $row->alamat }}</p>
                                <p><strong>Tanggal Daftar:</strong> {{ $row->tgl_daftar ? \Carbon\Carbon::parse($row->tgl_daftar)->format('d M Y') : '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>

@include('inc.footer_adm')
