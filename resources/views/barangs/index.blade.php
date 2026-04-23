<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="col-sm-12">
                <div class="card">
                    @if(session('success'))
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            Swal.fire({
                                icon: "success",
                                title: "Berhasil!",
                                text: "{{ session('success') }}",
                                showConfirmButton: false,
                                timer: 3000
                            });
                        });
                    </script>
                    @endif

                    @if(session('error'))
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            Swal.fire({
                                icon: "error",
                                title: "Error!",
                                text: "{{ session('error') }}",
                                confirmButtonText: "OK"
                            });
                        });
                    </script>
                    @endif

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-boxes text-primary"></i> Daftar Barang</h5>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-danger btn-sm" id="btnDeleteAll">
                                <i class="fas fa-trash-alt"></i> Hapus Semua
                            </button>
                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                                <i class="fas fa-file-import"></i> Import
                            </button>
                            <a href="{{ route('barangs.export') }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-file-export"></i> Export
                            </a>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#createBarangModal">
                                <i class="fas fa-plus"></i> Tambah Barang
                            </button>
                        </div>
                        <form id="formDeleteAll" action="{{ route('barangs.deleteAll') }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>

                    <div class="card-body" style="overflow-x:auto;">
                        <table id="res-config" class="table table-striped table-hover dt-responsive nowrap"
                            style="width: 100%">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Reseller</th>
                                    <th>Supplier</th>
                                    <th>Nama Barang</th>
                                    <th>Ukuran</th>
                                    <th class="text-end">HPP</th>
                                    <th class="text-end">Harga Grosir</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($barangs as $barang)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $barang->reseller->nama ?? '-' }}</td>
                                    <td>{{ $barang->supplier->nama ?? '-' }}</td>
                                    <td class="fw-bold">{{ $barang->namabarang }}</td>
                                    <td>{{ $barang->ukuran }}</td>
                                    <td class="text-end text-muted">Rp {{ number_format($barang->hpp, 0, ',', '.') }}</td>
                                    <td class="text-end text-primary fw-bold">Rp {{ number_format($barang->harga_grosir, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#editBarangModal{{ $barang->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('barangs.destroy', $barang->id) }}" method="POST"
                                                onsubmit="return confirm('Hapus barang?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editBarangModal{{ $barang->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-md">
                                        <form action="{{ route('barangs.update', $barang->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-content shadow border-0" style="border-radius: 12px;">
                                                <div class="modal-header bg-warning">
                                                    <h5 class="modal-title text-dark fw-bold"><i class="fas fa-edit me-2"></i> Edit Barang</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-bold">Reseller (Opsional)</label>
                                                            <select name="reseller_id" class="form-select">
                                                                <option value="">-- Pilih Reseller --</option>
                                                                @foreach($resellers as $reseller)
                                                                    <option value="{{ $reseller->id }}" {{ $barang->reseller_id == $reseller->id ? 'selected' : '' }}>{{ $reseller->nama }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-bold">Supplier (Opsional)</label>
                                                            <select name="supplier_id" class="form-select">
                                                                <option value="">-- Pilih Supplier --</option>
                                                                @foreach($suppliers as $supplier)
                                                                    <option value="{{ $supplier->id }}" {{ $barang->supplier_id == $supplier->id ? 'selected' : '' }}>{{ $supplier->nama }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <label class="form-label fw-bold">Nama Barang</label>
                                                            <input type="text" name="namabarang" class="form-control"
                                                                value="{{ $barang->namabarang }}" required>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-bold">Ukuran</label>
                                                            <input type="text" name="ukuran"
                                                                class="form-control edit-ukuran"
                                                                value="{{ $barang->ukuran }}">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-bold">HPP</label>
                                                            <input type="number" name="hpp" class="form-control"
                                                                value="{{ $barang->hpp }}">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-bold text-primary">Harga Grosir</label>
                                                            <input type="number" name="harga_grosir"
                                                                class="form-control border-primary"
                                                                value="{{ $barang->harga_grosir }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light border-0">
                                                    <button type="submit" class="btn btn-warning px-4 fw-bold">Update Barang</button>
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

    <!-- Create Modal -->
    <div class="modal fade" id="createBarangModal" tabindex="-1">
        <div class="modal-dialog modal-md">
            <form action="{{ route('barangs.store') }}" method="POST">
                @csrf
                <div class="modal-content shadow border-0" style="border-radius: 12px;">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold"><i class="fas fa-plus me-2"></i> Tambah Barang Baru</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Reseller</label>
                                <select name="reseller_id" class="form-select">
                                    <option value="">-- Pilih Reseller --</option>
                                    @foreach($resellers as $reseller)
                                        <option value="{{ $reseller->id }}">{{ $reseller->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Supplier</label>
                                <select name="supplier_id" class="form-select">
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Nama Barang</label>
                                <input type="text" name="namabarang" id="create_namabarang" class="form-control"
                                    required placeholder="Nama barang">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Ukuran</label>
                                <input type="text" name="ukuran" id="create_ukuran" class="form-control"
                                    placeholder="Ukuran">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">HPP</label>
                                <input type="number" name="hpp" class="form-control" placeholder="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-primary">Harga Grosir</label>
                                <input type="number" name="harga_grosir" class="form-control border-primary"
                                    placeholder="0">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="submit" class="btn btn-primary px-4 fw-bold">Simpan Barang</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-file-import"></i> Import Data Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('barangs.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="file" class="form-label">Pilih File Excel</label>
                            <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.xls,.csv"
                                required>
                            <div class="form-text">
                                Format file: Excel (.xlsx, .xls) atau CSV.
                                <a href="{{ route('barangs.export') }}" class="text-decoration-none">
                                    Download template
                                </a>
                            </div>
                        </div>
                        <div class="alert alert-info">
                            <small>
                                <i class="fas fa-info-circle"></i>
                                Pastikan file memiliki kolom: **Nama Barang, Ukuran, HPP, Harga Beli Per Potong, Harga
                                Beli Per Lusin, Harga Jual Per Potong, Harga Jual Per Lusin, Harga Grosir, Keuntungan, Reseller, Supplier**
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Auto capitalize for nama barang
            document.querySelectorAll('#create_namabarang, input[name="namabarang"]').forEach(input => {
                input.addEventListener('input', function() {
                    this.value = this.value.replace(/\w\S*/g, (txt) => txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase());
                });
            });

            // Auto uppercase for ukuran
            document.querySelectorAll('#create_ukuran, .edit-ukuran').forEach(input => {
                input.addEventListener('input', function() {
                    this.value = this.value.toUpperCase();
                });
            });

            // Delete All Confirmation
            const btnDeleteAll = document.getElementById('btnDeleteAll');
            if (btnDeleteAll) {
                btnDeleteAll.addEventListener('click', function() {
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Semua data barang akan dihapus dan tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, hapus semua!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('formDeleteAll').submit();
                        }
                    });
                });
            }
        });
    </script>
</x-app-layout>
