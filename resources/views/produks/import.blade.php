<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-file-import"></i> Import Data Produk</h5>
                        <a href="{{ route('produks.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <!-- SweetAlert Notifications -->
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

                        @if(session('warning'))
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                Swal.fire({
                                    icon: "warning",
                                    title: "Perhatian!",
                                    text: "{{ session('warning') }}",
                                    confirmButtonText: "Mengerti"
                                });
                            });
                        </script>
                        @endif

                        @if(session('error'))
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                Swal.fire({
                                    icon: "error",
                                    title: "Gagal!",
                                    text: "{{ session('error') }}",
                                    confirmButtonText: "Mengerti"
                                });
                            });
                        </script>
                        @endif

                        <!-- Informasi Template -->
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Petunjuk Import</h6>
                            <ul class="mb-0">
                                <li>Download template Excel terlebih dahulu</li>
                                <li>Isi data produk sesuai dengan kolom yang tersedia</li>
                                <li>Kolom dengan tanda <span class="text-danger">*</span> wajib diisi</li>
                                <li>File harus berformat .xlsx, .xls, atau .csv</li>
                                <li>Maksimal ukuran file: 2MB</li>
                            </ul>
                        </div>

                        <!-- Template Download -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-download me-2"></i>Download Template</h6>
                                <p class="card-text">Download template Excel untuk memudahkan pengisian data.</p>
                                <a href="{{ route('produks.download.template') }}" class="btn btn-success">
                                    <i class="fas fa-file-excel me-1"></i> Download Template
                                </a>
                            </div>
                        </div>

                        <!-- Form Import -->
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-upload me-2"></i>Upload File Excel</h6>
                                <form action="{{ route('produks.import') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="file" class="form-label">Pilih File Excel <span class="text-danger">*</span></label>
                                        <input type="file" class="form-control @error('file') is-invalid @enderror"
                                               id="file" name="file" accept=".xlsx,.xls,.csv" required>
                                        @error('file')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Format file: .xlsx, .xls, .csv (maks. 2MB)</div>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('produks.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times me-1"></i> Batal
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-upload me-1"></i> Import Data
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Informasi Kolom -->
                        <div class="card mt-4">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-table me-2"></i>Struktur Kolom Excel</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Kolom</th>
                                                <th>Keterangan</th>
                                                <th>Wajib</th>
                                                <th>Contoh</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>sku_induk</td>
                                                <td>SKU Induk produk</td>
                                                <td>Tidak</td>
                                                <td>SKU-001</td>
                                            </tr>
                                            <tr>
                                                <td>nama_produk</td>
                                                <td>Nama lengkap produk</td>
                                                <td>Ya</td>
                                                <td>Baju Kaos Polos</td>
                                            </tr>
                                            <tr>
                                                <td>nomor_referensi_sku</td>
                                                <td>Nomor referensi SKU</td>
                                                <td>Tidak</td>
                                                <td>REF-001</td>
                                            </tr>
                                            <tr>
                                                <td>nama_variasi</td>
                                                <td>Nama variasi produk</td>
                                                <td>Tidak</td>
                                                <td>Merah, Size L</td>
                                            </tr>
                                            <tr>
                                                <td>hpp_produk</td>
                                                <td>Harga Pokok Penjualan</td>
                                                <td>Ya</td>
                                                <td>50000</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Tampilkan failures jika ada -->
                        @if(session('failures'))
                        <div class="card mt-4 border-danger">
                            <div class="card-header bg-danger text-white">
                                <h6 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Data yang Gagal Diimport</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Baris</th>
                                                <th>Kolom</th>
                                                <th>Error</th>
                                                <th>Nilai</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach(session('failures') as $failure)
                                            <tr>
                                                <td>{{ $failure->row() }}</td>
                                                <td>{{ $failure->attribute() }}</td>
                                                <td>
                                                    <ul class="mb-0">
                                                        @foreach($failure->errors() as $error)
                                                        <li class="text-danger">{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                </td>
                                                <td>{{ $failure->values()[$failure->attribute()] }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
