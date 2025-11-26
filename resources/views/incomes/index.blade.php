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
                        <h5 class="mb-0"><i class="fas fa-money-bill-wave"></i> Daftar Income</h5>
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
</x-app-layout>
