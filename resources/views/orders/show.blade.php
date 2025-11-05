<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-eye"></i> Detail Order</h5>
                        <a href="{{ route('orders.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="40%">No. Pesanan</th>
                                        <td>{{ $order->no_pesanan }}</td>
                                    </tr>
                                    <tr>
                                        <th>Produk</th>
                                        <td>{{ $order->produk->nama_produk }}</td>
                                    </tr>
                                    <tr>
                                        <th>Jumlah</th>
                                        <td>{{ $order->jumlah }}</td>
                                    </tr>
                                    <tr>
                                        <th>Returned Quantity</th>
                                        <td>{{ $order->returned_quantity }}</td>
                                    </tr>
                                    <tr>
                                        <th>Total Harga Produk</th>
                                        <td>
                                            <strong>Rp {{ number_format($order->total_harga_produk, 0, ',', '.') }}</strong>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="40%">Pesanan Selesai</th>
                                        <td>
                                            @if($order->pesananselesai)
                                                {{ \Carbon\Carbon::parse($order->pesananselesai)->format('d/m/Y H:i') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Quantity Bersih</th>
                                        <td>
                                            <span class="badge bg-primary">{{ $order->jumlah - $order->returned_quantity }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Dibuat Pada</th>
                                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Diupdate Pada</th>
                                        <td>{{ $order->updated_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center gap-2 mt-4">
                            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-list me-1"></i> Lihat Semua Order
                            </a>
                            <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-1"></i> Edit Order
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
