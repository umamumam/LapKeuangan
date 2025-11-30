<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-plus"></i> Tambah Pengiriman Sampel</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('pengiriman-sampels.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tanggal" class="form-label">Tanggal Pengiriman <span class="text-danger">*</span></label>
                                        <input type="datetime-local" class="form-control @error('tanggal') is-invalid @enderror"
                                            id="tanggal" name="tanggal" value="{{ old('tanggal', now()->format('Y-m-d\TH:i')) }}" required>
                                        @error('tanggal')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('username') is-invalid @enderror"
                                            id="username" name="username" value="{{ old('username') }}" required>
                                        @error('username')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="sampel_id" class="form-label">Sampel <span class="text-danger">*</span></label>
                                        <select class="form-control @error('sampel_id') is-invalid @enderror"
                                            id="sampel_id" name="sampel_id" required onchange="calculateTotal()">
                                            <option value="">Pilih Sampel</option>
                                            @foreach($sampels as $sampel)
                                                <option value="{{ $sampel->id }}"
                                                    data-harga="{{ $sampel->harga }}"
                                                    {{ old('sampel_id') == $sampel->id ? 'selected' : '' }}>
                                                    {{ $sampel->nama }} - {{ $sampel->ukuran }} (Rp {{ number_format($sampel->harga, 0, ',', '.') }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('sampel_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="jumlah" class="form-label">Jumlah <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('jumlah') is-invalid @enderror"
                                            id="jumlah" name="jumlah" value="{{ old('jumlah', 1) }}" min="1" required onchange="calculateTotal()">
                                        @error('jumlah')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="no_resi" class="form-label">No. Resi <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('no_resi') is-invalid @enderror"
                                            id="no_resi" name="no_resi" value="{{ old('no_resi') }}" required>
                                        @error('no_resi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="ongkir" class="form-label">Ongkir (Rp) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('ongkir') is-invalid @enderror"
                                            id="ongkir" name="ongkir" value="{{ old('ongkir', 0) }}" min="0" required onchange="calculateTotal()">
                                        @error('ongkir')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Total HPP</label>
                                        <div class="form-control bg-light" id="totalhpp_display">
                                            Rp 0
                                        </div>
                                        <input type="hidden" id="totalhpp" name="totalhpp" value="0">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Total Biaya</label>
                                        <div class="form-control bg-light fw-bold" id="total_biaya_display">
                                            Rp 0
                                        </div>
                                        <input type="hidden" id="total_biaya" name="total_biaya" value="0">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="penerima" class="form-label">Penerima <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('penerima') is-invalid @enderror"
                                            id="penerima" name="penerima" value="{{ old('penerima') }}" required>
                                        @error('penerima')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="contact" class="form-label">Contact <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('contact') is-invalid @enderror"
                                            id="contact" name="contact" value="{{ old('contact') }}" required>
                                        @error('contact')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('alamat') is-invalid @enderror"
                                    id="alamat" name="alamat" rows="3" required>{{ old('alamat') }}</textarea>
                                @error('alamat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                                <a href="{{ route('pengiriman-sampels.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function calculateTotal() {
            const sampelSelect = document.getElementById('sampel_id');
            const jumlahInput = document.getElementById('jumlah');
            const ongkirInput = document.getElementById('ongkir');

            const selectedOption = sampelSelect.options[sampelSelect.selectedIndex];
            const harga = selectedOption ? parseInt(selectedOption.getAttribute('data-harga')) : 0;
            const jumlah = parseInt(jumlahInput.value) || 0;
            const ongkir = parseInt(ongkirInput.value) || 0;

            const totalhpp = harga * jumlah;
            const total_biaya = totalhpp + ongkir;

            // Update display
            document.getElementById('totalhpp_display').textContent = 'Rp ' + totalhpp.toLocaleString('id-ID');
            document.getElementById('total_biaya_display').textContent = 'Rp ' + total_biaya.toLocaleString('id-ID');

            // Update hidden inputs
            document.getElementById('totalhpp').value = totalhpp;
            document.getElementById('total_biaya').value = total_biaya;
        }

        // Initialize calculation on page load
        document.addEventListener('DOMContentLoaded', function() {
            calculateTotal();
        });
    </script>
</x-app-layout>
