<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-edit"></i> Edit Data Keuangan - {{ $monthlyFinance->nama_periode }}</h5>
                        <a href="{{ route('monthly-finances.show', $monthlyFinance->id) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form action="{{ route('monthly-finances.update', $monthlyFinance->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="periode_awal" class="form-label">Periode Awal *</label>
                                        <input type="date" class="form-control @error('periode_awal') is-invalid @enderror"
                                               id="periode_awal" name="periode_awal"
                                               value="{{ old('periode_awal', $monthlyFinance->periode_awal->format('Y-m-d')) }}" required>
                                        @error('periode_awal')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="periode_akhir" class="form-label">Periode Akhir *</label>
                                        <input type="date" class="form-control @error('periode_akhir') is-invalid @enderror"
                                               id="periode_akhir" name="periode_akhir"
                                               value="{{ old('periode_akhir', $monthlyFinance->periode_akhir->format('Y-m-d')) }}" required>
                                        @error('periode_akhir')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Nama periode: <strong id="nama-periode-preview">{{ $monthlyFinance->nama_periode }}</strong></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="total_pendapatan" class="form-label">Total Pendapatan *</label>
                                        <input type="number" class="form-control @error('total_pendapatan') is-invalid @enderror"
                                               id="total_pendapatan" name="total_pendapatan"
                                               value="{{ old('total_pendapatan', $monthlyFinance->total_pendapatan) }}" required min="0" step="1">
                                        @error('total_pendapatan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="rasio_admin_layanan" class="form-label">Rasio Admin & Layanan (%) *</label>
                                        <input type="number" step="0.01" class="form-control @error('rasio_admin_layanan') is-invalid @enderror"
                                               id="rasio_admin_layanan" name="rasio_admin_layanan"
                                               value="{{ old('rasio_admin_layanan', $monthlyFinance->rasio_admin_layanan) }}" required min="0" max="100">
                                        @error('rasio_admin_layanan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="operasional" class="form-label">Biaya Operasional *</label>
                                        <input type="number" class="form-control @error('operasional') is-invalid @enderror"
                                               id="operasional" name="operasional"
                                               value="{{ old('operasional', $monthlyFinance->operasional) }}" required min="0" step="1">
                                        @error('operasional')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="iklan" class="form-label">Biaya Iklan *</label>
                                        <input type="number" class="form-control @error('iklan') is-invalid @enderror"
                                               id="iklan" name="iklan"
                                               value="{{ old('iklan', $monthlyFinance->iklan) }}" required min="0" step="1">
                                        @error('iklan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea class="form-control @error('keterangan') is-invalid @enderror"
                                          id="keterangan" name="keterangan" rows="3">{{ old('keterangan', $monthlyFinance->keterangan) }}</textarea>
                                @error('keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Data
                                </button>
                                <a href="{{ route('monthly-finances.show', $monthlyFinance->id) }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const periodeAwal = document.getElementById('periode_awal');
        const namaPeriodePreview = document.getElementById('nama-periode-preview');

        function updateNamaPeriode() {
            if (periodeAwal.value) {
                const date = new Date(periodeAwal.value);
                const month = date.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
                namaPeriodePreview.textContent = month;
            }
        }

        periodeAwal.addEventListener('change', updateNamaPeriode);
    });
    </script>
</x-app-layout>
