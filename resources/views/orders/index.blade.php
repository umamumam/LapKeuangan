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
</x-app-layout>
