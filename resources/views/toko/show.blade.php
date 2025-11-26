<!-- resources/views/toko/show.blade.php -->
<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-eye"></i> Detail Toko</h5>
                        <a href="{{ route('toko.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <!-- Informasi Toko -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">ID Toko</label>
                                    <p class="form-control-plaintext">{{ $toko->id }}</p>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nama Toko</label>
                                    <p class="form-control-plaintext">{{ $toko->nama }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Total Penghasilan</label>
                                    <p class="form-control-plaintext text-success fw-bold">
                                        Rp {{ number_format($toko->total_penghasilan, 0, ',', '.') }}
                                    </p>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Jumlah Transaksi</label>
                                    <p class="form-control-plaintext">{{ $toko->jumlah_transaksi }} transaksi</p>
                                </div>
                            </div>
                        </div>

                        <!-- Daftar Incomes -->
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-money-bill-wave"></i> Riwayat Penghasilan</h6>
                            </div>
                            <div class="card-body">
                                @if($incomes->count() > 0)
                                <div class="table-responsive">
                                    <table id="res-config" class="table table-striped table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>No Pesanan</th>
                                                <th>No Pengajuan</th>
                                                <th>Total Penghasilan</th>
                                                <th>Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($incomes as $income)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $income->no_pesanan }}</td>
                                                <td>{{ $income->no_pengajuan ?? '-' }}</td>
                                                <td>Rp {{ number_format($income->total_penghasilan, 0, ',', '.') }}</td>
                                                <td>{{ $income->created_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                @else
                                <div class="text-center py-4">
                                    <i class="fas fa-receipt fa-2x text-muted mb-3"></i>
                                    <p class="text-muted">Belum ada data penghasilan untuk toko ini.</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- <div class="d-flex gap-2 mt-4">
                            <a href="{{ route('toko.edit', $toko->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('toko.destroy', $toko->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus toko ini?')">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
