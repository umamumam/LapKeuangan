<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-plus"></i> Tambah Data Keuangan Bulanan</h5>
                        <a href="{{ route('monthly-finances.index') }}" class="btn btn-secondary btn-sm">
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

                        <form action="{{ route('monthly-finances.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="periode_awal" class="form-label">Periode Awal *</label>
                                        <input type="date" class="form-control @error('periode_awal') is-invalid @enderror"
                                               id="periode_awal" name="periode_awal"
                                               value="{{ old('periode_awal', $defaultAwal ?? '') }}" required>
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
                                               value="{{ old('periode_akhir', $defaultAkhir ?? '') }}" required>
                                        @error('periode_akhir')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Nama periode: <strong id="nama-periode-preview">{{ $defaultNama ?? '' }}</strong></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tambahan kolom untuk Shopee -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="total_pendapatan_shopee" class="form-label">Pendapatan Shopee *</label>
                                        <input type="number" class="form-control @error('total_pendapatan_shopee') is-invalid @enderror"
                                               id="total_pendapatan_shopee" name="total_pendapatan_shopee"
                                               value="{{ old('total_pendapatan_shopee') }}" required min="0" step="1">
                                        @error('total_pendapatan_shopee')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="rasio_admin_layanan_shopee" class="form-label">Rasio Admin Shopee (%) *</label>
                                        <input type="number" step="0.01" class="form-control @error('rasio_admin_layanan_shopee') is-invalid @enderror"
                                               id="rasio_admin_layanan_shopee" name="rasio_admin_layanan_shopee"
                                               value="{{ old('rasio_admin_layanan_shopee') }}" required min="0" max="100">
                                        @error('rasio_admin_layanan_shopee')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="iklan_shopee" class="form-label">Iklan Shopee *</label>
                                        <input type="number" class="form-control @error('iklan_shopee') is-invalid @enderror"
                                               id="iklan_shopee" name="iklan_shopee"
                                               value="{{ old('iklan_shopee') }}" required min="0" step="1">
                                        @error('iklan_shopee')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Tambahan kolom untuk Tiktok -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="total_pendapatan_tiktok" class="form-label">Pendapatan Tiktok *</label>
                                        <input type="number" class="form-control @error('total_pendapatan_tiktok') is-invalid @enderror"
                                               id="total_pendapatan_tiktok" name="total_pendapatan_tiktok"
                                               value="{{ old('total_pendapatan_tiktok') }}" required min="0" step="1">
                                        @error('total_pendapatan_tiktok')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="rasio_admin_layanan_tiktok" class="form-label">Rasio Admin Tiktok (%) *</label>
                                        <input type="number" step="0.01" class="form-control @error('rasio_admin_layanan_tiktok') is-invalid @enderror"
                                               id="rasio_admin_layanan_tiktok" name="rasio_admin_layanan_tiktok"
                                               value="{{ old('rasio_admin_layanan_tiktok') }}" required min="0" max="100">
                                        @error('rasio_admin_layanan_tiktok')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="iklan_tiktok" class="form-label">Iklan Tiktok *</label>
                                        <input type="number" class="form-control @error('iklan_tiktok') is-invalid @enderror"
                                               id="iklan_tiktok" name="iklan_tiktok"
                                               value="{{ old('iklan_tiktok') }}" required min="0" step="1">
                                        @error('iklan_tiktok')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Kolom yang sudah ada (untuk backward compatibility) -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="total_pendapatan" class="form-label">Total Pendapatan (Otomatis)</label>
                                        <input type="number" class="form-control" id="total_pendapatan" readonly>
                                        <div class="form-text">Pendapatan Shopee + Tiktok</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="rasio_admin_layanan" class="form-label">Rasio Admin Rata-rata (Otomatis)</label>
                                        <input type="number" class="form-control" id="rasio_admin_layanan" readonly>
                                        <div class="form-text">Rata-rata tertimbang Shopee & Tiktok</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="operasional" class="form-label">Biaya Operasional *</label>
                                        <input type="number" class="form-control @error('operasional') is-invalid @enderror"
                                               id="operasional" name="operasional"
                                               value="{{ old('operasional') }}" required min="0" step="1">
                                        @error('operasional')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Gaji, listrik, internet, transportasi, packing, dll</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="iklan" class="form-label">Total Iklan (Otomatis)</label>
                                        <input type="number" class="form-control" id="iklan" readonly>
                                        <div class="form-text">Iklan Shopee + Tiktok</div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea class="form-control @error('keterangan') is-invalid @enderror"
                                          id="keterangan" name="keterangan" rows="3">{{ old('keterangan') }}</textarea>
                                @error('keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Data
                                </button>
                                <a href="{{ route('monthly-finances.index') }}" class="btn btn-secondary">
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
        const periodeAkhir = document.getElementById('periode_akhir');
        const namaPeriodePreview = document.getElementById('nama-periode-preview');

        // Elements for Shopee
        const pendapatanShopee = document.getElementById('total_pendapatan_shopee');
        const rasioShopee = document.getElementById('rasio_admin_layanan_shopee');
        const iklanShopee = document.getElementById('iklan_shopee');

        // Elements for Tiktok
        const pendapatanTiktok = document.getElementById('total_pendapatan_tiktok');
        const rasioTiktok = document.getElementById('rasio_admin_layanan_tiktok');
        const iklanTiktok = document.getElementById('iklan_tiktok');

        // Auto-calculated fields
        const totalPendapatan = document.getElementById('total_pendapatan');
        const rataRataRasio = document.getElementById('rasio_admin_layanan');
        const totalIklan = document.getElementById('iklan');

        function updateNamaPeriode() {
            if (periodeAwal.value) {
                const date = new Date(periodeAwal.value);
                const month = date.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
                namaPeriodePreview.textContent = month;

                // Auto-set periode akhir ke akhir bulan jika kosong
                if (!periodeAkhir.value) {
                    const lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
                    periodeAkhir.value = lastDay.toISOString().split('T')[0];
                }
            }
        }

        function updateCalculatedFields() {
            // Calculate total pendapatan
            const shopeePendapatan = parseInt(pendapatanShopee.value) || 0;
            const tiktokPendapatan = parseInt(pendapatanTiktok.value) || 0;
            const totalPendapatanValue = shopeePendapatan + tiktokPendapatan;
            totalPendapatan.value = totalPendapatanValue;

            // Calculate total iklan
            const shopeeIklan = parseInt(iklanShopee.value) || 0;
            const tiktokIklan = parseInt(iklanTiktok.value) || 0;
            const totalIklanValue = shopeeIklan + tiktokIklan;
            totalIklan.value = totalIklanValue;

            // Calculate weighted average rasio
            const shopeeRasio = parseFloat(rasioShopee.value) || 0;
            const tiktokRasio = parseFloat(rasioTiktok.value) || 0;

            if (totalPendapatanValue > 0) {
                const adminShopee = shopeePendapatan * (shopeeRasio / 100);
                const adminTiktok = tiktokPendapatan * (tiktokRasio / 100);
                const totalAdmin = adminShopee + adminTiktok;
                const weightedAverage = (totalAdmin / totalPendapatanValue) * 100;
                rataRataRasio.value = weightedAverage.toFixed(2);
            } else {
                rataRataRasio.value = 0;
            }
        }

        // Initialize events
        periodeAwal.addEventListener('change', updateNamaPeriode);

        // Add event listeners for calculations
        [pendapatanShopee, pendapatanTiktok, rasioShopee, rasioTiktok, iklanShopee, iklanTiktok].forEach(input => {
            input.addEventListener('input', updateCalculatedFields);
        });

        // Initialize on page load
        updateNamaPeriode();
        updateCalculatedFields();
    });
    </script>
</x-app-layout>
