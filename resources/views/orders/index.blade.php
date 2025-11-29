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

                    @if(session('warning'))
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            Swal.fire({
                                icon: "warning",
                                title: "Peringatan!",
                                text: "{{ session('warning') }}",
                                showConfirmButton: true,
                            });
                        });
                    </script>
                    @endif

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list"></i> Daftar Order</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('orders.import.form') }}" class="btn btn-info btn-sm">
                                <i class="fas fa-file-import"></i> Import
                            </a>
                            <a href="{{ route('orders.export') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-file-export"></i> Export
                            </a>
                            <a href="{{ route('orders.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Tambah Order
                            </a>
                            @if($orders->count() > 0)
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteAllModal">
                                <i class="fas fa-trash-alt"></i> Hapus Semua
                            </button>
                            @endif
                        </div>
                    </div>
                    <div class="card-body" style="overflow-x:auto;">
                        @if($orders->count() > 0)
                        <table id="res-config" class="display table table-striped table-hover dt-responsive nowrap"
                            style="width: 100%">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>No. Pesanan</th>
                                    <th>No. Resi</th>
                                    <th>SKU Induk</th>
                                    <th>Nama Produk</th>
                                    <th>Nomor Referensi SKU</th>
                                    <th>Nama Variasi</th>
                                    <th>HPP</th>
                                    <th>Jumlah</th>
                                    <th>Returned Qty</th>
                                    <th>Pesanan Selesai</th>
                                    <th>Total Harga Produk</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $order->no_pesanan }}</td>
                                    <td>{{ $order->no_resi ?? '-' }}</td>
                                    <td>{{ $order->produk->sku_induk }}</td>
                                    <td>{{ $order->produk->nama_produk }}</td>
                                    <td>{{ $order->produk->nomor_referensi_sku }}</td>
                                    <td>{{ $order->produk->nama_variasi }}</td>
                                    <td>{{ $order->produk->hpp_produk }}</td>
                                    <td>{{ $order->jumlah }}</td>
                                    <td>{{ $order->returned_quantity }}</td>
                                    <td>
                                        @if($order->pesananselesai)
                                        {{ \Carbon\Carbon::parse($order->pesananselesai)->format('d/m/Y H:i') }}
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $order->total_harga_produk }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-info btn-sm"
                                                title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('orders.edit', $order->id) }}"
                                                class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('orders.destroy', $order->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus order ini?')">
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
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada data order.</p>
                            <a href="{{ route('orders.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tambah Order Pertama
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus Semua -->
    @if($orders->count() > 0)
    <div class="modal fade" id="deleteAllModal" tabindex="-1" aria-labelledby="deleteAllModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteAllModalLabel">Konfirmasi Hapus Semua Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>PERINGATAN!</strong>
                    </div>
                    <p>Anda akan menghapus <strong>semua data order</strong> (total: {{ $orders->count() }} data).</p>
                    <p class="text-danger mb-0">Tindakan ini tidak dapat dibatalkan! Apakah Anda yakin ingin melanjutkan?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('orders.deleteAll') }}" method="POST" class="d-inline">
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
