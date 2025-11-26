<!-- resources/views/toko/edit.blade.php -->
<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-edit"></i> Edit Toko</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('toko.update', $toko->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nama" class="form-label">Nama Toko <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                               id="nama" name="nama" value="{{ old('nama', $toko->nama) }}"
                                               placeholder="Masukkan nama toko" required>
                                        @error('nama')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update
                                </button>
                                <a href="{{ route('toko.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
