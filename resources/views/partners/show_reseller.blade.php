<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-boxes text-primary"></i> 
                            Daftar Barang Reseller: <span class="text-primary font-bold">{{ $reseller->nama }}</span>
                        </h5>
                        <a href="{{ route('partners.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body" style="overflow-x:auto;">
                        @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        @endif
                        <table id="res-config" class="table table-striped table-hover dt-responsive nowrap" style="width: 100%">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Nama Barang</th>
                                    <th>Ukuran</th>
                                    <th>HPP</th>
                                    <th>Jual / Potong</th>
                                    <th>Keuntungan</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reseller->barangs as $barang)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $barang->namabarang }}</td>
                                    <td>{{ $barang->ukuran }}</td>
                                    <td>{{ number_format($barang->hpp) }}</td>
                                    <td>{{ number_format($barang->hargajual_perpotong) }}</td>
                                    <td>{{ number_format($barang->keuntungan) }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editBarangModal{{ $barang->id }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                    </td>
                                </tr>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editBarangModal{{ $barang->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <form action="{{ route('barangs.update', $barang->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-content text-start">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Barang</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row g-2">
                                                        <div class="col-md-6 mb-2">
                                                            <label class="form-label">Reseller</label>
                                                            <select name="reseller_id" class="form-select">
                                                                <option value="">-- Pilih Reseller --</option>
                                                                @foreach($resellers as $r)
                                                                    <option value="{{ $r->id }}" {{ $barang->reseller_id == $r->id ? 'selected' : '' }}>{{ $r->nama }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6 mb-2">
                                                            <label class="form-label">Supplier</label>
                                                            <select name="supplier_id" class="form-select">
                                                                <option value="">-- Pilih Supplier --</option>
                                                                @foreach($suppliers as $s)
                                                                    <option value="{{ $s->id }}" {{ $barang->supplier_id == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-8 mb-2">
                                                            <label class="form-label">Nama Barang</label>
                                                            <input type="text" name="namabarang" class="form-control"
                                                                value="{{ $barang->namabarang }}" required>
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <label class="form-label">Ukuran</label>
                                                            <input type="text" name="ukuran" class="form-control edit-ukuran"
                                                                value="{{ $barang->ukuran }}">
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <label class="form-label">HPP</label>
                                                            <input type="number" name="hpp" class="form-control"
                                                                value="{{ $barang->hpp }}">
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <label class="form-label">Beli Per Potong</label>
                                                            <input type="number" name="hargabeli_perpotong"
                                                                class="form-control"
                                                                value="{{ $barang->hargabeli_perpotong }}">
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <label class="form-label">Beli Per Lusin</label>
                                                            <input type="number" name="hargabeli_perlusin"
                                                                class="form-control"
                                                                value="{{ $barang->hargabeli_perlusin }}">
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <label class="form-label">Jual Per Potong</label>
                                                            <input type="number" name="hargajual_perpotong"
                                                                class="form-control"
                                                                value="{{ $barang->hargajual_perpotong }}">
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <label class="form-label">Jual Per Lusin</label>
                                                            <input type="number" name="hargajual_perlusin"
                                                                class="form-control"
                                                                value="{{ $barang->hargajual_perlusin }}">
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <label class="form-label">Keuntungan</label>
                                                            <input type="number" name="keuntungan" class="form-control"
                                                                value="{{ $barang->keuntungan }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Auto capitalize for nama barang
            document.querySelectorAll('input[name="namabarang"]').forEach(input => {
                input.addEventListener('input', function() {
                    this.value = this.value.replace(/\w\S*/g, (txt) => txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase());
                });
            });

            // Auto uppercase for ukuran
            document.querySelectorAll('.edit-ukuran').forEach(input => {
                input.addEventListener('input', function() {
                    this.value = this.value.toUpperCase();
                });
            });
        });
    </script>
</x-app-layout>
