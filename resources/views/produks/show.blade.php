<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-eye"></i> Detail Produk</h5>
                        <a href="{{ route('produks.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-4">
                                    <h6 class="text-primary mb-3"><i class="fas fa-cube me-2"></i>Informasi Produk</h6>
                                    <div class="row">
                                        <div class="col-sm-6 mb-3">
                                            <label class="form-label text-muted small mb-1">Nama Produk</label>
                                            <div class="fw-semibold">{{ $produk->nama_produk }}</div>
                                        </div>
                                        <div class="col-sm-6 mb-3">
                                            <label class="form-label text-muted small mb-1">SKU Induk</label>
                                            <div>{{ $produk->sku_induk ?: '-' }}</div>
                                        </div>
                                        <div class="col-sm-6 mb-3">
                                            <label class="form-label text-muted small mb-1">Referensi SKU</label>
                                            <div>{{ $produk->nomor_referensi_sku ?: '-' }}</div>
                                        </div>
                                        <div class="col-sm-6 mb-3">
                                            <label class="form-label text-muted small mb-1">Variasi</label>
                                            <div>{{ $produk->nama_variasi ?: '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="border-start ps-4">
                                    <div class="mb-4">
                                        <label class="form-label text-muted small mb-1">HPP Produk</label>
                                        <div class="h4 text-success fw-bold">Rp {{ number_format($produk->hpp_produk, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted small mb-1">Dibuat</label>
                                        <div class="small">{{ $produk->created_at->format('d/m/Y H:i') }}</div>
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label text-muted small mb-1">Diupdate</label>
                                        <div class="small">{{ $produk->updated_at->format('d/m/Y H:i') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-top pt-4 mt-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('produks.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-list me-1"></i> Daftar Produk
                                </a>
                                <a href="{{ route('produks.edit', $produk->id) }}" class="btn btn-warning">
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
