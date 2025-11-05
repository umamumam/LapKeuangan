<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-edit"></i> Edit Income</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('incomes.update', $income->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="no_pesanan" class="form-label">No Pesanan <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('no_pesanan') is-invalid @enderror"
                                            id="no_pesanan" name="no_pesanan"
                                            value="{{ old('no_pesanan', $income->no_pesanan) }}"
                                            placeholder="Masukkan no pesanan" required>
                                        @error('no_pesanan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="no_pengajuan" class="form-label">No Pengajuan</label>
                                        <input type="text"
                                            class="form-control @error('no_pengajuan') is-invalid @enderror"
                                            id="no_pengajuan" name="no_pengajuan"
                                            value="{{ old('no_pengajuan', $income->no_pengajuan ?? null) }}"
                                            placeholder="Masukkan no pengajuan">
                                        @error('no_pengajuan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="total_penghasilan" class="form-label">Total Penghasilan <span
                                                class="text-danger">*</span></label>
                                        <input type="number"
                                            class="form-control @error('total_penghasilan') is-invalid @enderror"
                                            id="total_penghasilan" name="total_penghasilan"
                                            value="{{ old('total_penghasilan', $income->total_penghasilan) }}"
                                            placeholder="Masukkan total penghasilan" required>
                                        @error('total_penghasilan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('incomes.show', $income->id) }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
