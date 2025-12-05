<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="col-sm-12">
                <div class="card">
                    @if(session('success'))
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            Swal.fire({
                                icon: "success",
                                title: "Berhasil!",
                                text: "{{ session('success') }}",
                                showConfirmButton: false,
                                timer: 3000
                            });
                        });
                    </script>
                    @endif

                    @if(session('error'))
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            Swal.fire({
                                icon: "error",
                                title: "Gagal!",
                                text: "{{ session('error') }}",
                                showConfirmButton: false,
                                timer: 3000
                            });
                        });
                    </script>
                    @endif

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0"><i class="fas fa-list"></i> Daftar Income</h5>
                            <span class="badge bg-primary ms-3">
                                <i class="fas fa-database me-1"></i> Total: {{ $incomes->total() }}
                            </span>
                        </div>
                        {{-- <h5 class="mb-0"><i class="fas fa-money-bill-wave"></i> Daftar Income</h5> --}}
                        <div class="d-flex gap-2">
                            <a href="{{ route('incomes.import.form') }}" class="btn btn-info btn-sm">
                                <i class="fas fa-file-import"></i> Import
                            </a>
                            <a href="{{ route('incomes.export') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-file-export"></i> Export
                            </a>
                            <a href="{{ route('incomes.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Tambah Income
                            </a>
                            @if($incomes->count() > 0)
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteAllModal">
                                <i class="fas fa-trash-alt"></i> Hapus Semua
                            </button>
                            @endif
                        </div>
                    </div>
                    <div class="card-body" style="overflow-x:auto;">
                        @if($incomes->count() > 0)
                        <table id="res-config" class="display table table-striped table-hover dt-responsive nowrap"
                            style="width: 100%">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>No. Pesanan</th>
                                    <th>No. Pengajuan</th>
                                    <th>Total Penghasilan</th>
                                    <th>Jumlah Item</th>
                                    <th>Toko</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($incomes as $income)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $income->no_pesanan }}</strong>
                                    </td>
                                    <td>{{ $income->no_pengajuan }}</td>
                                    <td>Rp {{ number_format($income->total_penghasilan, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $income->orders->count() }} item</span>
                                    </td>
                                    <td>{{ $income->nama_toko }}</td>
                                    <td>{{ $income->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('incomes.show', $income->id) }}"
                                                class="btn btn-info btn-sm" title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('incomes.edit', $income->id) }}"
                                                class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('incomes.destroy', $income->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus income ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <div class="text-center py-4">
                            <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada data income.</p>
                            <a href="{{ route('incomes.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tambah Income Pertama
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus Semua -->
    @if($incomes->count() > 0)
    <div class="modal fade" id="deleteAllModal" tabindex="-1" aria-labelledby="deleteAllModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteAllModalLabel">Konfirmasi Hapus Semua Data Income</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>PERINGATAN!</strong>
                    </div>
                    <p>Anda akan menghapus <strong>semua data income</strong> (total: {{ $incomes->count() }} data).</p>
                    <p class="text-danger mb-0">Tindakan ini tidak dapat dibatalkan! Apakah Anda yakin ingin melanjutkan?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('incomes.deleteAll') }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash-alt"></i> Ya, Hapus Semua
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</x-app-layout>
