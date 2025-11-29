<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-eye"></i> Detail Data Banding</h5>
                        <a href="{{ route('bandings.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-4">
                                    <h6 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Informasi Utama</h6>
                                    <div class="row">
                                        <div class="col-sm-6 mb-3">
                                            <label class="form-label text-muted small mb-1">Tanggal</label>
                                            <div class="fw-semibold">{{ $banding->tanggal->format('d/m/Y H:i') }}</div>
                                        </div>
                                        <div class="col-sm-6 mb-3">
                                            <label class="form-label text-muted small mb-1">Marketplace</label>
                                            <div>
                                                <span class="badge {{ $banding->marketplace == 'Shopee' ? 'bg-orange' : 'bg-red' }}">
                                                    {{ $banding->marketplace }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 mb-3">
                                            <label class="form-label text-muted small mb-1">No. Pesanan</label>
                                            <div class="fw-semibold">{{ $banding->no_pesanan }}</div>
                                        </div>
                                        <div class="col-sm-6 mb-3">
                                            <label class="form-label text-muted small mb-1">No. Pengajuan</label>
                                            <div>{{ $banding->no_pengajuan ?: '-' }}</div>
                                        </div>
                                        <div class="col-sm-6 mb-3">
                                            <label class="form-label text-muted small mb-1">No. Resi</label>
                                            <div>{{ $banding->no_resi ?: '-' }}</div>
                                        </div>
                                        <div class="col-sm-6 mb-3">
                                            <label class="form-label text-muted small mb-1">Status Banding</label>
                                            <div>
                                                <span class="badge {{ $banding->status_banding == 'Berhasil' ? 'bg-success' : ($banding->status_banding == 'Ditinjau' ? 'bg-warning' : 'bg-danger') }}">
                                                    {{ $banding->status_banding }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="border-start ps-4">
                                    <div class="mb-4">
                                        <label class="form-label text-muted small mb-1">Alasan</label>
                                        <div class="fw-semibold">{{ $banding->alasan }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted small mb-1">Status Ongkir</label>
                                        <div>
                                            <span class="badge {{ $banding->ongkir == 'Dibebaskan' ? 'bg-success' : ($banding->ongkir == 'Ditanggung' ? 'bg-warning' : 'bg-secondary') }}">
                                                {{ $banding->ongkir }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted small mb-1">Dibuat</label>
                                        <div class="small">{{ $banding->created_at->format('d/m/Y H:i') }}</div>
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label text-muted small mb-1">Diupdate</label>
                                        <div class="small">{{ $banding->updated_at->format('d/m/Y H:i') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Pengirim -->
                        <div class="border-top pt-4 mt-4">
                            <h6 class="text-primary mb-3"><i class="fas fa-user me-2"></i>Informasi Pengirim</h6>
                            <div class="row">
                                <div class="col-sm-4 mb-3">
                                    <label class="form-label text-muted small mb-1">Username</label>
                                    <div>{{ $banding->username }}</div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <label class="form-label text-muted small mb-1">Nama Pengirim</label>
                                    <div>{{ $banding->nama_pengirim }}</div>
                                </div>
                                <div class="col-sm-4 mb-3">
                                    <label class="form-label text-muted small mb-1">No. HP</label>
                                    <div>{{ $banding->no_hp ?: '-' }}</div>
                                </div>
                                <div class="col-sm-12 mb-3">
                                    <label class="form-label text-muted small mb-1">Alamat</label>
                                    <div>{{ $banding->alamat }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="border-top pt-4 mt-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('bandings.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-list me-1"></i> Daftar Banding
                                </a>
                                <a href="{{ route('bandings.edit', $banding->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    .bg-orange { background-color: #fe5000 !important; }
    .bg-red { background-color: #ff0050 !important; }
    </style>
</x-app-layout>
