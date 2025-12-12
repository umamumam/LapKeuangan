<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Tambah Data Summary & Output</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('monthly-finances.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="periode_id" class="form-label">Pilih Periode <span
                                                class="text-danger">*</span></label>
                                        <select name="periode_id" id="periode_id"
                                            class="form-control @error('periode_id') is-invalid @enderror" required>
                                            <option value="">-- Pilih Periode --</option>
                                            @foreach($periodes as $periode)
                                            <option value="{{ $periode->id }}" {{ old('periode_id')==$periode->id ?
                                                'selected' : '' }}>
                                                {{ $periode->nama_periode }} - {{ $periode->toko->nama_toko }} ({{
                                                $periode->marketplace }})
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('periode_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Hanya menampilkan periode yang belum memiliki data
                                            keuangan</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="total_pendapatan" class="form-label">Total Pendapatan <span
                                                class="text-danger">*</span></label>
                                        <input type="number" name="total_pendapatan" id="total_pendapatan"
                                            class="form-control @error('total_pendapatan') is-invalid @enderror"
                                            value="{{ old('total_pendapatan') }}" required min="0">
                                        @error('total_pendapatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <div class="mb-3">
                                        <label for="operasional" class="form-label">Biaya Operasional <span
                                                class="text-danger">*</span></label>
                                        <input type="number" name="operasional" id="operasional"
                                            class="form-control @error('operasional') is-invalid @enderror"
                                            value="{{ old('operasional') }}" required min="0">
                                        @error('operasional')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <div class="mb-3">
                                        <label for="iklan" class="form-label">Biaya Iklan <span
                                                class="text-danger">*</span></label>
                                        <input type="number" name="iklan" id="iklan"
                                            class="form-control @error('iklan') is-invalid @enderror"
                                            value="{{ old('iklan') }}" required min="0">
                                        @error('iklan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="rasio_admin_layanan" class="form-label">Rasio Admin Layanan (%)
                                            <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" name="rasio_admin_layanan"
                                            id="rasio_admin_layanan"
                                            class="form-control @error('rasio_admin_layanan') is-invalid @enderror"
                                            value="{{ old('rasio_admin_layanan', 0) }}" required min="0" max="100">
                                        @error('rasio_admin_layanan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="keterangan" class="form-label">Keterangan</label>
                                        <textarea name="keterangan" id="keterangan"
                                            class="form-control @error('keterangan') is-invalid @enderror"
                                            rows="3">{{ old('keterangan') }}</textarea>
                                        @error('keterangan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('monthly-finances.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
