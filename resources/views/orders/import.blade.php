<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-file-import"></i> Import Data Order</h5>
                        <a href="{{ route('orders.index') }}" class="btn btn-secondary btn-sm">
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
                                <li>Isi data order sesuai dengan kolom yang tersedia</li>
                                <li>Kolom dengan tanda <span class="text-danger">*</span> wajib diisi</li>
                                <li>Nama Produk harus sama persis dengan yang ada di sistem</li>
                                <li>Untuk Periode, masukkan ID Periode yang tersedia di sistem</li>
                                <li>File harus berformat .xlsx, .xls, atau .csv</li>
                                <li>Maksimal ukuran file: 5MB</li>
                            </ul>
                        </div>

                        <!-- Template Download -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-download me-2"></i>Download Template</h6>
                                <p class="card-text">Download template Excel untuk memudahkan pengisian data.</p>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('orders.download.template') }}" class="btn btn-success">
                                        <i class="fas fa-file-excel me-1"></i> Download Template
                                    </a>
                                    <!-- Tambahkan tombol lihat periode -->
                                    <button type="button" class="btn btn-info" onclick="showPeriodes()">
                                        <i class="fas fa-eye me-1"></i> Lihat Daftar Periode
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Form Import -->
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-upload me-2"></i>Upload File Excel</h6>
                                <form action="{{ route('orders.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="file" class="form-label">Pilih File Excel <span
                                                class="text-danger">*</span></label>
                                        <input type="file" class="form-control @error('file') is-invalid @enderror"
                                            id="file" name="file" accept=".xlsx,.xls,.csv" required>
                                        @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Format file: .xlsx, .xls, .csv (maks. 5MB)</div>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times me-1"></i> Batal
                                        </a>
                                        <button type="submit" class="btn btn-primary" id="importButton">
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
                                                <td>no_pesanan</td>
                                                <td>Nomor pesanan (unik)</td>
                                                <td><span class="badge bg-danger">Ya</span></td>
                                                <td>ORD001</td>
                                            </tr>
                                            <tr>
                                                <td>no_resi</td>
                                                <td>Nomor resi pengiriman</td>
                                                <td><span class="badge bg-secondary">Tidak</span></td>
                                                <td>RES123456</td>
                                            </tr>
                                            <tr>
                                                <td>nama_produk</td>
                                                <td>Nama produk (harus sama dengan di sistem)</td>
                                                <td><span class="badge bg-danger">Ya</span></td>
                                                <td>Baju Kaos Polos</td>
                                            </tr>
                                            <tr>
                                                <td>nama_variasi</td>
                                                <td>Variasi produk (jika ada)</td>
                                                <td><span class="badge bg-secondary">Tidak</span></td>
                                                <td>L</td>
                                            </tr>
                                            <tr>
                                                <td>jumlah</td>
                                                <td>Jumlah order (minimal 1)</td>
                                                <td><span class="badge bg-danger">Ya</span></td>
                                                <td>50</td>
                                            </tr>
                                            <tr>
                                                <td>returned_quantity</td>
                                                <td>Jumlah yang direturn</td>
                                                <td><span class="badge bg-secondary">Tidak</span></td>
                                                <td>5</td>
                                            </tr>
                                            <tr>
                                                <td>total_harga_produk</td>
                                                <td>Total harga produk (dalam angka)</td>
                                                <td><span class="badge bg-danger">Ya</span></td>
                                                <td>500000</td>
                                            </tr>
                                            <tr>
                                                <td>periode_id</td>
                                                <td>ID Periode (lihat daftar di bawah)</td>
                                                <td><span class="badge bg-secondary">Tidak</span></td>
                                                <td>1</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Daftar Periode -->
                        <div class="card mt-4">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-calendar-alt me-2"></i>Daftar Periode Tersedia</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Nama Periode</th>
                                                <th>Marketplace</th>
                                                <th>Toko</th>
                                                <th>Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $periodes = \App\Models\Periode::with('toko')
                                                    ->orderBy('nama_periode', 'desc')
                                                    ->orderBy('marketplace')
                                                    ->get();
                                            @endphp
                                            @foreach($periodes as $periode)
                                            <tr>
                                                <td><strong>{{ $periode->id }}</strong></td>
                                                <td>{{ $periode->nama_periode }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $periode->marketplace == 'Shopee' ? 'warning' : 'info' }}">
                                                        {{ $periode->marketplace }}
                                                    </span>
                                                </td>
                                                <td>{{ $periode->toko->nama }}</td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($periode->tanggal_mulai)->format('d/m/Y') }} -
                                                    {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->format('d/m/Y') }}
                                                </td>
                                            </tr>
                                            @endforeach
                                            @if($periodes->count() == 0)
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">
                                                    <i class="fas fa-info-circle me-1"></i> Belum ada periode tersedia
                                                </td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-warning mt-3">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Perhatian:</strong> Gunakan ID Periode dari tabel di atas untuk mengisi kolom <code>periode_id</code> di Excel.
                                    Jika tidak ada periode, biarkan kolom kosong.
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
                                <div class="alert alert-warning">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Total ada {{ count(session('failures')) }} data yang gagal diimport.
                                    @if(session('failed_order_numbers'))
                                    <br><strong>No. Pesanan yang gagal:</strong> {{ session('failed_order_numbers') }}
                                    @endif
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No. Pesanan</th>
                                                <th>Baris</th>
                                                <th>Kolom</th>
                                                <th>Error</th>
                                                <th>Nilai</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach(session('failures') as $failure)
                                            <tr>
                                                <td>
                                                    <strong>{{ $failure['no_pesanan'] }}</strong>
                                                </td>
                                                <td>{{ $failure['row'] }}</td>
                                                <td>{{ $failure['attribute'] }}</td>
                                                <td>
                                                    <ul class="mb-0">
                                                        @foreach($failure['errors'] as $error)
                                                        <li class="text-danger">{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                </td>
                                                <td>
                                                    @php
                                                    $values = $failure['values'];
                                                    $attribute = $failure['attribute'];
                                                    $value = isset($values[$attribute]) ? $values[$attribute] :
                                                    (isset($values['nama_produk']) ? $values['nama_produk'] . ' - ' .
                                                    ($values['nama_variasi'] ?? '') : 'N/A');
                                                    @endphp
                                                    {{ $value }}
                                                </td>
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

    <!-- Modal Daftar Periode -->
    <div class="modal fade" id="periodesModal" tabindex="-1" aria-labelledby="periodesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="periodesModalLabel">
                        <i class="fas fa-calendar-alt me-2"></i>Daftar Periode
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Periode</th>
                                    <th>Marketplace</th>
                                    <th>Toko</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($periodes as $periode)
                                <tr>
                                    <td><strong class="text-primary">{{ $periode->id }}</strong></td>
                                    <td>{{ $periode->nama_periode }}</td>
                                    <td>
                                        <span class="badge bg-{{ $periode->marketplace == 'Shopee' ? 'warning' : 'info' }}">
                                            {{ $periode->marketplace }}
                                        </span>
                                    </td>
                                    <td>{{ $periode->toko->nama }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($periode->tanggal_mulai)->format('d/m/Y') }} -
                                        {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->format('d/m/Y') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Gunakan <strong>ID Periode</strong> dari tabel di atas untuk mengisi kolom <code>periode_id</code> di file Excel.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" onclick="copyPeriodesToClipboard()">
                        <i class="fas fa-copy me-1"></i> Salin Daftar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Show periodes modal
    function showPeriodes() {
        const modal = new bootstrap.Modal(document.getElementById('periodesModal'));
        modal.show();
    }

    // Copy periodes to clipboard
    function copyPeriodesToClipboard() {
        let text = "ID\tNama Periode\tMarketplace\tToko\tTanggal\n";

        @foreach($periodes as $periode)
        text += "{{ $periode->id }}\t{{ $periode->nama_periode }}\t{{ $periode->marketplace }}\t{{ $periode->toko->nama }}\t{{ \Carbon\Carbon::parse($periode->tanggal_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->format('d/m/Y') }}\n";
        @endforeach

        navigator.clipboard.writeText(text).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Daftar periode berhasil disalin ke clipboard',
                timer: 2000,
                showConfirmButton: false
            });
        }).catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Gagal menyalin ke clipboard: ' + err,
            });
        });
    }

    // Form submission with loading
    document.getElementById('importForm').addEventListener('submit', function(e) {
        const button = document.getElementById('importButton');
        const originalText = button.innerHTML;

        button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Memproses...';
        button.disabled = true;

        // Jika proses lama, tetap tampilkan loading
        setTimeout(() => {
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Sedang mengimport data...';
        }, 3000);
    });
    </script>
</x-app-layout>
