<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-plus"></i> Tambah Income Baru</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('incomes.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="no_pesanan" class="form-label">No Pesanan <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('no_pesanan') is-invalid @enderror"
                                            id="no_pesanan" name="no_pesanan" value="{{ old('no_pesanan') }}"
                                            placeholder="Masukkan no pesanan" required>
                                        @error('no_pesanan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="no_pengajuan" class="form-label">No Pengajuan</label>
                                        <input type="text"
                                            class="form-control @error('no_pengajuan') is-invalid @enderror"
                                            id="no_pengajuan" name="no_pengajuan" value="{{ old('no_pengajuan') }}"
                                            placeholder="Masukkan no pengajuan">
                                        @error('no_pengajuan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="marketplace" class="form-label">Marketplace <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control @error('marketplace') is-invalid @enderror"
                                            id="marketplace" name="marketplace" required>
                                            <option value="">Pilih Marketplace</option>
                                            <option value="Shopee" {{ old('marketplace') == 'Shopee' ? 'selected' : '' }}>Shopee</option>
                                            <option value="Tiktok" {{ old('marketplace') == 'Tiktok' ? 'selected' : '' }}>Tiktok Shop</option>
                                        </select>
                                        @error('marketplace')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="toko_id" class="form-label">Toko <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control @error('toko_id') is-invalid @enderror"
                                            id="toko_id" name="toko_id" required>
                                            <option value="">Pilih Toko</option>
                                            @foreach($tokos as $toko)
                                                <option value="{{ $toko->id }}"
                                                    {{ old('toko_id') == $toko->id ? 'selected' : '' }}>
                                                    {{ $toko->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('toko_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="total_penghasilan" class="form-label">Total Penghasilan <span
                                                class="text-danger">*</span></label>
                                        <input type="number"
                                            class="form-control @error('total_penghasilan') is-invalid @enderror"
                                            id="total_penghasilan" name="total_penghasilan"
                                            value="{{ old('total_penghasilan') }}"
                                            placeholder="Masukkan total penghasilan" required>
                                        @error('total_penghasilan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('incomes.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
