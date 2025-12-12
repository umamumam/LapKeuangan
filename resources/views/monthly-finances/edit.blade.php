<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-edit"></i> Edit Data Summary & Output</h5>
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

                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Periode:</strong> {{ $periode->nama_periode }} -
                                        {{ $periode->toko->nama_toko }} ({{ $periode->marketplace }})
                                        <br>
                                        <small class="text-muted">
                                            {{ $periode->tanggal_mulai->format('d/m/Y') }} -
                                            {{ $periode->tanggal_selesai->format('d/m/Y') }}
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="total_pendapatan" class="form-label">Total Pendapatan <span
                                                class="text-danger">*</span></label>
                                        <input type="number" name="total_pendapatan" id="total_pendapatan"
                                            class="form-control @error('total_pendapatan') is-invalid @enderror"
                                            value="{{ old('total_pendapatan', $monthlyFinance->total_pendapatan) }}"
                                            required min="0">
                                        @error('total_pendapatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="operasional" class="form-label">Biaya Operasional <span
                                                class="text-danger">*</span></label>
                                        <input type="number" name="operasional" id="operasional"
                                            class="form-control @error('operasional') is-invalid @enderror"
                                            value="{{ old('operasional', $monthlyFinance->operasional) }}" required
                                            min="0">
                                        @error('operasional')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="iklan" class="form-label">Biaya Iklan <span
                                                class="text-danger">*</span></label>
                                        <input type="number" name="iklan" id="iklan"
                                            class="form-control @error('iklan') is-invalid @enderror"
                                            value="{{ old('iklan', $monthlyFinance->iklan) }}" required min="0">
                                        @error('iklan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="rasio_admin_layanan" class="form-label">Rasio Admin Layanan (%)
                                            <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" name="rasio_admin_layanan"
                                            id="rasio_admin_layanan"
                                            class="form-control @error('rasio_admin_layanan') is-invalid @enderror"
                                            value="{{ old('rasio_admin_layanan', $monthlyFinance->rasio_admin_layanan) }}"
                                            required min="0" max="100">
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
                                            rows="3">{{ old('keterangan', $monthlyFinance->keterangan) }}</textarea>
                                        @error('keterangan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('monthly-finances.show', $monthlyFinance->id) }}"
                                    class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i> Perbarui
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
        // Realtime calculation preview
        const totalPendapatanInput = document.getElementById('total_pendapatan');
        const operasionalInput = document.getElementById('operasional');
        const iklanInput = document.getElementById('iklan');
        const rasioInput = document.getElementById('rasio_admin_layanan');

        const biayaAdminSpan = document.querySelector('[data-biaya-admin]');
        const totalBiayaSpan = document.querySelector('[data-total-biaya]');
        const labaRugiSpan = document.querySelector('[data-laba-rugi]');

        function updatePreview() {
            const totalPendapatan = parseFloat(totalPendapatanInput.value) || 0;
            const operasional = parseFloat(operasionalInput.value) || 0;
            const iklan = parseFloat(iklanInput.value) || 0;
            const rasio = parseFloat(rasioInput.value) || 0;

            // Calculate biaya admin
            const biayaAdmin = totalPendapatan * (rasio / 100);
            const totalBiaya = operasional + iklan + biayaAdmin;

            // Simulate laba rugi calculation
            // Note: Actual calculation uses data from periode which is not available here
            // This is just a preview based on input values
            const labaRugi = totalPendapatan - totalBiaya;

            // Update preview if elements exist
            if(biayaAdminSpan) {
                biayaAdminSpan.textContent = 'Rp ' + biayaAdmin.toLocaleString('id-ID');
            }
            if(totalBiayaSpan) {
                totalBiayaSpan.textContent = 'Rp ' + totalBiaya.toLocaleString('id-ID');
            }
            if(labaRugiSpan) {
                labaRugiSpan.textContent = 'Rp ' + labaRugi.toLocaleString('id-ID');
                labaRugiSpan.className = labaRugi >= 0 ? 'text-success' : 'text-danger';
            }
        }

        // Add event listeners
        [totalPendapatanInput, operasionalInput, iklanInput, rasioInput].forEach(input => {
            input.addEventListener('input', updatePreview);
        });

        // Initial update
        updatePreview();
    });
    </script>
</x-app-layout>
