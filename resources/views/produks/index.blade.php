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
                    <!-- Di dalam card-header, tambahkan tombol import/export -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list"></i> Daftar Produk</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('produks.import.form') }}" class="btn btn-info btn-sm">
                                <i class="fas fa-file-import"></i> Import
                            </a>
                            <a href="{{ route('produks.export') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-file-export"></i> Export
                            </a>
                            <a href="{{ route('produks.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Tambah Produk
                            </a>
                        </div>
                    </div>
                    <div class="card-body" style="overflow-x:auto;">
                        @if($produks->count() > 0)
                        <table id="res-config" class="display table table-striped table-hover dt-responsive nowrap"
                            style="width: 100%">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>SKU Induk</th>
                                    <th>Nama Produk</th>
                                    <th>Referensi SKU</th>
                                    <th>Variasi</th>
                                    <th>HPP</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($produks as $produk)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $produk->sku_induk ?? '-' }}</td>
                                    <td>{{ $produk->nama_produk }}</td>
                                    <td>{{ $produk->nomor_referensi_sku ?? '-' }}</td>
                                    <td>{{ $produk->nama_variasi ?? '-' }}</td>
                                    <td>Rp {{ number_format($produk->hpp_produk, 0, ',', '.') }}</td>
                                    <td>
                                        <!-- Opsi 1: Menggunakan margin pada setiap tombol -->
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('produks.show', $produk->id) }}"
                                                class="btn btn-info btn-sm" title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('produks.edit', $produk->id) }}"
                                                class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('produks.destroy', $produk->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
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
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada data produk.</p>
                            <a href="{{ route('produks.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tambah Produk Pertama
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
