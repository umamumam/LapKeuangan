<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-plus"></i> Tambah Produk Baru</h5>
                        <a href="{{ route('produks.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('produks.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="sku_induk" class="form-label">SKU Induk</label>
                                    <input type="text" class="form-control @error('sku_induk') is-invalid @enderror"
                                        id="sku_induk" name="sku_induk" value="{{ old('sku_induk') }}"
                                        placeholder="Masukkan SKU induk">
                                    @error('sku_induk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="nomor_referensi_sku" class="form-label">Nomor Referensi SKU</label>
                                    <input type="text"
                                        class="form-control @error('nomor_referensi_sku') is-invalid @enderror"
                                        id="nomor_referensi_sku" name="nomor_referensi_sku"
                                        value="{{ old('nomor_referensi_sku') }}"
                                        placeholder="Masukkan nomor referensi SKU">
                                    @error('nomor_referensi_sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="nama_produk" class="form-label">Nama Produk <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nama_produk') is-invalid @enderror"
                                        id="nama_produk" name="nama_produk" value="{{ old('nama_produk') }}"
                                        placeholder="Masukkan nama produk" required>
                                    @error('nama_produk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="nama_variasi" class="form-label">Nama Variasi</label>
                                    <input type="text" class="form-control @error('nama_variasi') is-invalid @enderror"
                                        id="nama_variasi" name="nama_variasi" value="{{ old('nama_variasi') }}"
                                        placeholder="Masukkan nama variasi">
                                    @error('nama_variasi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="hpp_produk" class="form-label">HPP Produk <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('hpp_produk') is-invalid @enderror"
                                        id="hpp_produk" name="hpp_produk" value="{{ old('hpp_produk', 0) }}" min="0"
                                        required>
                                    @error('hpp_produk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Produk
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
