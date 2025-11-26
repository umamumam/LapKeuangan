<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-eye"></i> Detail Order</h5>
                        <a href="{{ route('orders.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-4">
                                    <h6 class="text-primary mb-3"><i class="fas fa-shopping-cart me-2"></i>Informasi Order</h6>
                                    <div class="row">
                                        <div class="col-sm-6 mb-3">
                                            <label class="form-label text-muted small mb-1">No. Pesanan</label>
                                            <div class="fw-semibold">{{ $order->no_pesanan }}</div>
                                        </div>
                                        <div class="col-sm-6 mb-3">
                                            <label class="form-label text-muted small mb-1">Produk</label>
                                            <div>{{ $order->produk->nama_produk }}</div>
                                        </div>
                                        <div class="col-sm-6 mb-3">
                                            <label class="form-label text-muted small mb-1">Jumlah Order</label>
                                            <div class="fw-semibold">{{ $order->jumlah }} pcs</div>
                                        </div>
                                        <div class="col-sm-6 mb-3">
                                            <label class="form-label text-muted small mb-1">Quantity Return</label>
                                            <div class="text-danger">{{ $order->returned_quantity }} pcs</div>
                                        </div>
                                        <div class="col-sm-6 mb-3">
                                            <label class="form-label text-muted small mb-1">Quantity Bersih</label>
                                            <div>
                                                <span class="badge bg-primary">
                                                    {{ $order->jumlah - $order->returned_quantity }} pcs
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 mb-3">
                                            <label class="form-label text-muted small mb-1">Status Pesanan</label>
                                            <div>
                                                @if($order->pesananselesai)
                                                    <span class="badge bg-success">Selesai</span>
                                                @else
                                                    <span class="badge bg-warning">Proses</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="border-start ps-4">
                                    <div class="mb-4">
                                        <label class="form-label text-muted small mb-1">Total Harga Produk</label>
                                        <div class="h4 text-success fw-bold">Rp {{ number_format($order->total_harga_produk, 0, ',', '.') }}</div>
                                    </div>
                                    @if($order->pesananselesai)
                                    <div class="mb-3">
                                        <label class="form-label text-muted small mb-1">Tanggal Selesai</label>
                                        <div class="small text-success">
                                            <i class="fas fa-check-circle me-1"></i>
                                            {{ \Carbon\Carbon::parse($order->pesananselesai)->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                    @endif
                                    <div class="mb-3">
                                        <label class="form-label text-muted small mb-1">Dibuat</label>
                                        <div class="small">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label text-muted small mb-1">Diupdate</label>
                                        <div class="small">{{ $order->updated_at->format('d/m/Y H:i') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Produk -->
                        <div class="border-top pt-4 mt-4">
                            <h6 class="text-primary mb-3"><i class="fas fa-cube me-2"></i>Informasi Produk Terkait</h6>
                            <div class="row">
                                <div class="col-sm-4 mb-3">
                                    <label class="form-label text-muted small mb-1">SKU Induk</label>
                                    <div>{{ $order->produk->sku_induk ?: '-' }}</div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <label class="form-label text-muted small mb-1">Referensi SKU</label>
                                    <div>{{ $order->produk->nomor_referensi_sku ?: '-' }}</div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <label class="form-label text-muted small mb-1">Variasi</label>
                                    <div>{{ $order->produk->nama_variasi ?: '-' }}</div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <label class="form-label text-muted small mb-1">HPP Produk</label>
                                    <div class="fw-semibold">Rp {{ number_format($order->produk->hpp_produk, 0, ',', '.') }}</div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <label class="form-label text-muted small mb-1">Total HPP Order</label>
                                    <div class="fw-semibold text-info">
                                        Rp {{ number_format(($order->jumlah - $order->returned_quantity) * $order->produk->hpp_produk, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-top pt-4 mt-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-list me-1"></i> Daftar Order
                                </a>
                                <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
