<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-edit"></i> Edit Order</h5>
                        <a href="{{ route('orders.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('orders.update', $order->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="no_pesanan" class="form-label">No. Pesanan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('no_pesanan') is-invalid @enderror"
                                        id="no_pesanan" name="no_pesanan" value="{{ old('no_pesanan', $order->no_pesanan) }}"
                                        placeholder="Masukkan nomor pesanan" required>
                                    @error('no_pesanan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="produk_id" class="form-label">Produk <span class="text-danger">*</span></label>
                                    <select class="form-control @error('produk_id') is-invalid @enderror"
                                        id="produk_id" name="produk_id" required>
                                        <option value="">Pilih Produk</option>
                                        @foreach($produks as $produk)
                                            <option value="{{ $produk->id }}" {{ old('produk_id', $order->produk_id) == $produk->id ? 'selected' : '' }}>
                                                {{ $produk->nama_produk }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('produk_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="jumlah" class="form-label">Jumlah <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('jumlah') is-invalid @enderror"
                                        id="jumlah" name="jumlah" value="{{ old('jumlah', $order->jumlah) }}" min="1" required>
                                    @error('jumlah')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="returned_quantity" class="form-label">Returned Quantity</label>
                                    <input type="number" class="form-control @error('returned_quantity') is-invalid @enderror"
                                        id="returned_quantity" name="returned_quantity" value="{{ old('returned_quantity', $order->returned_quantity) }}" min="0">
                                    @error('returned_quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="total_harga_produk" class="form-label">Total Harga Produk <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('total_harga_produk') is-invalid @enderror"
                                        id="total_harga_produk" name="total_harga_produk"
                                        value="{{ old('total_harga_produk', $order->total_harga_produk) }}"
                                        placeholder="Masukkan total harga produk"
                                        min="0" required>
                                    @error('total_harga_produk')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Total harga untuk produk ini (dalam Rupiah)</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="pesananselesai" class="form-label">Pesanan Selesai</label>
                                    <input type="datetime-local" class="form-control @error('pesananselesai') is-invalid @enderror"
                                        id="pesananselesai" name="pesananselesai"
                                        value="{{ old('pesananselesai', $order->pesananselesai ? \Carbon\Carbon::parse($order->pesananselesai)->format('Y-m-d\TH:i') : '') }}">
                                    @error('pesananselesai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Order
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
