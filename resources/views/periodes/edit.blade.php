<!-- resources/views/periodes/edit.blade.php -->
<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Edit Periode: {{ $periode->nama_periode }}</h5>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('periodes.update', $periode->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label>Nama Periode *</label>
                                <input type="text" class="form-control" name="nama_periode"
                                       value="{{ old('nama_periode', $periode->nama_periode) }}" required>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label>Tanggal Mulai *</label>
                                    <input type="date" class="form-control" name="tanggal_mulai"
                                           value="{{ old('tanggal_mulai', $periode->tanggal_mulai->format('Y-m-d')) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label>Tanggal Selesai *</label>
                                    <input type="date" class="form-control" name="tanggal_selesai"
                                           value="{{ old('tanggal_selesai', $periode->tanggal_selesai->format('Y-m-d')) }}" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label>Toko *</label>
                                <select class="form-select" name="toko_id" required>
                                    <option value="">Pilih Toko</option>
                                    @foreach($tokos as $toko)
                                        <option value="{{ $toko->id }}"
                                                {{ $periode->toko_id == $toko->id ? 'selected' : '' }}>
                                            {{ $toko->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>Marketplace *</label>
                                <select class="form-select" name="marketplace" required>
                                    <option value="">Pilih Marketplace</option>
                                    <option value="Shopee" {{ $periode->marketplace == 'Shopee' ? 'selected' : '' }}>Shopee</option>
                                    <option value="Tiktok" {{ $periode->marketplace == 'Tiktok' ? 'selected' : '' }}>Tiktok</option>
                                </select>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="{{ route('periodes.index') }}" class="btn btn-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
