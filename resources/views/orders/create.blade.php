<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-plus"></i> Tambah Order Baru</h5>
                        <a href="{{ route('orders.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('orders.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="no_pesanan" class="form-label">No. Pesanan <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('no_pesanan') is-invalid @enderror"
                                        id="no_pesanan" name="no_pesanan" value="{{ old('no_pesanan') }}"
                                        placeholder="Masukkan nomor pesanan" required>
                                    @error('no_pesanan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="no_resi" class="form-label">No. Resi</label>
                                    <input type="text" class="form-control @error('no_resi') is-invalid @enderror"
                                        id="no_resi" name="no_resi" value="{{ old('no_resi') }}"
                                        placeholder="Masukkan nomor resi (opsional)">
                                    @error('no_resi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="produk_id" class="form-label">Produk <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control @error('produk_id') is-invalid @enderror" id="produk_id"
                                        name="produk_id" required>
                                        <option value="">Pilih Produk</option>
                                        @foreach($produks as $produk)
                                        <option value="{{ $produk->id }}" {{ old('produk_id')==$produk->id ? 'selected'
                                            : '' }}>
                                            {{ $produk->nama_produk }} - {{ $produk->nama_variasi }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('produk_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="jumlah" class="form-label">Jumlah <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('jumlah') is-invalid @enderror"
                                        id="jumlah" name="jumlah" value="{{ old('jumlah', 1) }}" min="1" required>
                                    @error('jumlah')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="returned_quantity" class="form-label">Returned Quantity</label>
                                    <input type="number"
                                        class="form-control @error('returned_quantity') is-invalid @enderror"
                                        id="returned_quantity" name="returned_quantity"
                                        value="{{ old('returned_quantity', 0) }}" min="0">
                                    @error('returned_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Jumlah barang yang diretur</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="total_harga_produk" class="form-label">Total Harga Produk <span
                                            class="text-danger">*</span></label>
                                    <input type="number"
                                        class="form-control @error('total_harga_produk') is-invalid @enderror"
                                        id="total_harga_produk" name="total_harga_produk"
                                        value="{{ old('total_harga_produk') }}"
                                        placeholder="Masukkan total harga produk" min="0" required>
                                    @error('total_harga_produk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Total harga untuk produk ini (dalam Rupiah)</div>
                                </div>

                                                                <!-- TAMBAHKAN FIELD PERIODE -->
                                <div class="col-md-6 mb-3">
                                    <label for="periode_id" class="form-label">Periode</label>
                                    <select class="form-control @error('periode_id') is-invalid @enderror" id="periode_id"
                                        name="periode_id">
                                        <option value="">Pilih Periode (Opsional)</option>
                                        @php
                                            $periodes = \App\Models\Periode::orderBy('nama_periode', 'desc')->get();
                                        @endphp
                                        @foreach($periodes as $periode)
                                        <option value="{{ $periode->id }}" {{ old('periode_id')==$periode->id ? 'selected' : '' }}>
                                            {{ $periode->nama_periode }} ({{ $periode->marketplace }})
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('periode_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Pilih periode untuk order ini</div>
                                </div>
                            </div>

                            <!-- Validasi client-side untuk returned_quantity -->
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Perhatian:</strong> Returned quantity tidak boleh lebih besar dari jumlah.
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Order
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Validasi client-side untuk returned_quantity
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const jumlahInput = document.getElementById('jumlah');
        const returnedInput = document.getElementById('returned_quantity');

        form.addEventListener('submit', function(e) {
            const jumlah = parseInt(jumlahInput.value) || 0;
            const returned = parseInt(returnedInput.value) || 0;

            if (returned > jumlah) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: 'Returned quantity tidak boleh lebih besar dari jumlah',
                    confirmButtonColor: '#3085d6'
                });
                returnedInput.focus();
            }
        });

        // Real-time validation
        returnedInput.addEventListener('input', function() {
            const jumlah = parseInt(jumlahInput.value) || 0;
            const returned = parseInt(this.value) || 0;

            if (returned > jumlah) {
                this.classList.add('is-invalid');
                // Tampilkan pesan error kecil
                let errorDiv = this.nextElementSibling;
                if (!errorDiv || !errorDiv.classList.contains('invalid-feedback')) {
                    errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback';
                    this.parentNode.insertBefore(errorDiv, this.nextSibling);
                }
                errorDiv.textContent = 'Tidak boleh lebih besar dari jumlah';
            } else {
                this.classList.remove('is-invalid');
                const errorDiv = this.nextElementSibling;
                if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
                    errorDiv.textContent = '';
                }
            }
        });
    });
    </script>
</x-app-layout>
