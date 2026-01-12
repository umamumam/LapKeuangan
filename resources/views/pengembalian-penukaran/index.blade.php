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

                    @if(session('warning'))
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            Swal.fire({
                                icon: "warning",
                                title: "Peringatan!",
                                text: "{{ session('warning') }}",
                                confirmButtonText: "OK"
                            });
                        });
                    </script>
                    @endif

                    @if(session('import_warning'))
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            const importData = @json(session('import_warning'));

                            // Format failed rows untuk ditampilkan
                            let failedDetails = '';
                            if (importData.failed_rows && importData.failed_rows.length > 0) {
                                failedDetails = '<br><br><strong>Data yang gagal:</strong><br>';
                                importData.failed_rows.forEach(function(row, index) {
                                    failedDetails += `${index + 1}. Baris ${row.row} - ${row.nama_pengirim}: ${row.reason}<br>`;
                                });
                            }

                            Swal.fire({
                                icon: "warning",
                                title: "Import Selesai",
                                html: importData.message + failedDetails,
                                showConfirmButton: true,
                                confirmButtonText: "OK",
                                width: '600px'
                            });
                        });
                    </script>
                    @endif

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-exchange-alt"></i> Daftar Pengembalian & Penukaran</h5>
                        <div class="d-flex gap-2">
                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                                <i class="fas fa-file-import"></i> Import
                            </button>
                            <a href="{{ route('pengembalian-penukaran.export', [
                                    'start_date' => $startDate ?? '',
                                    'end_date' => $endDate ?? '',
                                    'jenis' => request('jenis') ?? '',
                                    'marketplace' => request('marketplace') ?? ''
                                ]) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-file-export"></i> Export
                            </a>
                            @if($pengembalianPenukaran->count() > 0)
                            <button class="btn btn-danger btn-sm" onclick="confirmDeleteAll()">
                                <i class="fas fa-trash-alt"></i> Hapus Semua
                            </button>
                            <form id="deleteAllForm" action="{{ route('pengembalian-penukaran.delete-all') }}"
                                method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                            @endif
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">
                                <i class="fas fa-plus"></i> Tambah Data
                            </button>
                        </div>
                    </div>

                    <!-- Form Filter -->
                    <div class="card-header">
                        <h5 class="mb-3"><i class="fas fa-filter"></i> Filter Data</h5>
                        <form method="GET" action="{{ route('pengembalian-penukaran.index') }}" class="row g-3 align-items-end">
                            <!-- Tanggal Mulai -->
                            <div class="col-md-2">
                                <label for="start_date" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="start_date" name="start_date"
                                    value="{{ $startDate ?? now()->startOfMonth()->format('Y-m-d') }}">
                            </div>

                            <!-- Tanggal Akhir -->
                            <div class="col-md-2">
                                <label for="end_date" class="form-label">Tanggal Akhir</label>
                                <input type="date" class="form-control" id="end_date" name="end_date"
                                    value="{{ $endDate ?? now()->endOfMonth()->format('Y-m-d') }}">
                            </div>

                            <!-- Jenis -->
                            <div class="col-md-3">
                                <label for="jenis" class="form-label">Jenis</label>
                                <select class="form-select" id="jenis" name="jenis">
                                    <option value="">Semua Jenis</option>
                                    @foreach($jenisOptions as $key => $value)
                                    <option value="{{ $key }}" {{ request('jenis')==$key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Marketplace -->
                            <div class="col-md-3">
                                <label for="marketplace" class="form-label">Marketplace</label>
                                <select class="form-select" id="marketplace" name="marketplace">
                                    <option value="">Semua Marketplace</option>
                                    @foreach($marketplaceOptions as $key => $value)
                                    <option value="{{ $key }}" {{ request('marketplace')==$key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tombol Filter & Reset di samping kanan -->
                            <div class="col-md-2 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-primary flex-fill">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('pengembalian-penukaran.index') }}" class="btn btn-secondary flex-fill">
                                    <i class="fas fa-redo"></i> Reset
                                </a>
                            </div>
                        </form>
                    </div>

                    <div class="card-body" style="overflow-x:auto;">
                        @if($pengembalianPenukaran->count() > 0)
                        <table id="res-config" class="display table table-striped table-hover dt-responsive nowrap"
                            style="width: 100%">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Tanggal</th>
                                    <th>Jenis</th>
                                    <th>Marketplace</th>
                                    <th>Nama Pengirim</th>
                                    <th>No HP</th>
                                    <th>Resi Penerimaan</th>
                                    <th>Resi Pengiriman</th>
                                    <th>Pembayaran</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pengembalianPenukaran as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                    <td>
                                        @if($item->jenis == 'Pengembalian')
                                        <span class="badge bg-warning">{{ $item->jenis }}</span>
                                        @elseif($item->jenis == 'Penukaran')
                                        <span class="badge bg-info">{{ $item->jenis }}</span>
                                        @elseif($item->jenis == 'Pengiriman Gagal')
                                        <span class="badge bg-danger">{{ $item->jenis }}</span>
                                        @else
                                        <span class="badge bg-success">{{ $item->jenis }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->marketplace == 'Tiktok')
                                        <span class="badge bg-dark">{{ $item->marketplace }}</span>
                                        @elseif($item->marketplace == 'Shopee')
                                        <span class="badge bg-orange" style="background-color: #FF6B35;">{{
                                            $item->marketplace }}</span>
                                        @else
                                        <span class="badge bg-secondary">{{ $item->marketplace }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->nama_pengirim }}</td>
                                    <td>{{ $item->no_hp }}</td>
                                    <td>{{ $item->resi_penerimaan ?? '-' }}</td>
                                    <td>{{ $item->resi_pengiriman ?? '-' }}</td>
                                    <td>
                                        @if($item->pembayaran == 'Sistem')
                                        <span class="badge bg-primary">{{ $item->pembayaran }}</span>
                                        @elseif($item->pembayaran == 'Tunai')
                                        <span class="badge bg-success">{{ $item->pembayaran }}</span>
                                        @else
                                        <span class="badge bg-danger">{{ $item->pembayaran }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#editModal" data-id="{{ $item->id }}"
                                                data-tanggal="{{ \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') }}"
                                                data-jenis="{{ $item->jenis }}"
                                                data-marketplace="{{ $item->marketplace }}"
                                                data-resi_penerimaan="{{ $item->resi_penerimaan }}"
                                                data-resi_pengiriman="{{ $item->resi_pengiriman }}"
                                                data-pembayaran="{{ $item->pembayaran }}"
                                                data-nama_pengirim="{{ $item->nama_pengirim }}"
                                                data-no_hp="{{ $item->no_hp }}" data-alamat="{{ $item->alamat }}"
                                                data-keterangan="{{ $item->keterangan }}" onclick="editData(this)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('pengembalian-penukaran.destroy', $item->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <div class="text-center py-4">
                            <i class="fas fa-exchange-alt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada data pengembalian/penukaran.</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                                <i class="fas fa-plus"></i> Tambah Data Pertama
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Tambah Data Pengembalian/Penukaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('pengembalian-penukaran.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="create_tanggal" class="form-label">Tanggal</label>
                                <input type="date" class="form-control" id="create_tanggal" name="tanggal"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="create_jenis" class="form-label">Jenis</label>
                                <select class="form-select" id="create_jenis" name="jenis" required>
                                    <option value="">Pilih Jenis</option>
                                    <option value="Pengembalian">Pengembalian</option>
                                    <option value="Penukaran">Penukaran</option>
                                    <option value="Pengembalian Dana">Pengembalian Dana</option>
                                    <option value="Pengiriman Gagal">Pengiriman Gagal</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="create_marketplace" class="form-label">Marketplace</label>
                                <select class="form-select" id="create_marketplace" name="marketplace" required>
                                    <option value="">Pilih Marketplace</option>
                                    <option value="Tiktok">Tiktok</option>
                                    <option value="Shopee">Shopee</option>
                                    <option value="Reguler">Reguler</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="create_pembayaran" class="form-label">Pembayaran</label>
                                <select class="form-select" id="create_pembayaran" name="pembayaran" required>
                                    <option value="">Pilih Pembayaran</option>
                                    <option value="Sistem">Sistem</option>
                                    <option value="Tunai">Tunai</option>
                                    <option value="DFOD">DFOD</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="create_resi_penerimaan" class="form-label">Resi Penerimaan</label>
                                <input type="text" class="form-control" id="create_resi_penerimaan"
                                    name="resi_penerimaan">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="create_resi_pengiriman" class="form-label">Resi Pengiriman</label>
                                <input type="text" class="form-control" id="create_resi_pengiriman"
                                    name="resi_pengiriman">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="create_nama_pengirim" class="form-label">Nama Pengirim</label>
                                <input type="text" class="form-control" id="create_nama_pengirim" name="nama_pengirim"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="create_no_hp" class="form-label">No HP</label>
                                <input type="text" class="form-control" id="create_no_hp" name="no_hp" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="create_alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="create_alamat" name="alamat" rows="2"
                                required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="create_keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="create_keterangan" name="keterangan" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Data Pengembalian/Penukaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_tanggal" class="form-label">Tanggal</label>
                                <input type="date" class="form-control" id="edit_tanggal" name="tanggal" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_jenis" class="form-label">Jenis</label>
                                <select class="form-select" id="edit_jenis" name="jenis" required>
                                    <option value="Pengembalian">Pengembalian</option>
                                    <option value="Penukaran">Penukaran</option>
                                    <option value="Pengembalian Dana">Pengembalian Dana</option>
                                    <option value="Pengiriman Gagal">Pengiriman Gagal</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_marketplace" class="form-label">Marketplace</label>
                                <select class="form-select" id="edit_marketplace" name="marketplace" required>
                                    <option value="Tiktok">Tiktok</option>
                                    <option value="Shopee">Shopee</option>
                                    <option value="Reguler">Reguler</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_pembayaran" class="form-label">Pembayaran</label>
                                <select class="form-select" id="edit_pembayaran" name="pembayaran" required>
                                    <option value="Sistem">Sistem</option>
                                    <option value="Tunai">Tunai</option>
                                    <option value="DFOD">DFOD</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_resi_penerimaan" class="form-label">Resi Penerimaan</label>
                                <input type="text" class="form-control" id="edit_resi_penerimaan"
                                    name="resi_penerimaan">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_resi_pengiriman" class="form-label">Resi Pengiriman</label>
                                <input type="text" class="form-control" id="edit_resi_pengiriman"
                                    name="resi_pengiriman">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_nama_pengirim" class="form-label">Nama Pengirim</label>
                                <input type="text" class="form-control" id="edit_nama_pengirim" name="nama_pengirim"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_no_hp" class="form-label">No HP</label>
                                <input type="text" class="form-control" id="edit_no_hp" name="no_hp" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="edit_alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="edit_alamat" name="alamat" rows="2" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="edit_keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="edit_keterangan" name="keterangan" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-file-import"></i> Import Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('pengembalian-penukaran.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="file" class="form-label">Pilih File Excel</label>
                            <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.xls,.csv"
                                required>
                            <div class="form-text">
                                Format file: Excel (.xlsx, .xls) atau CSV.
                                <a href="{{ route('pengembalian-penukaran.export') }}" class="text-decoration-none">
                                    Download template
                                </a>
                            </div>
                        </div>
                        <div class="alert alert-info">
                            <small>
                                <i class="fas fa-info-circle"></i>
                                Pastikan file memiliki kolom: Tanggal, Jenis, Marketplace, Resi Penerimaan, Resi
                                Pengiriman, Pembayaran, Nama Pengirim, No HP, Alamat, Keterangan
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
        function editData(button) {
            const id = button.getAttribute('data-id');
            const tanggal = button.getAttribute('data-tanggal');
            const jenis = button.getAttribute('data-jenis');
            const marketplace = button.getAttribute('data-marketplace');
            const resiPenerimaan = button.getAttribute('data-resi_penerimaan');
            const resiPengiriman = button.getAttribute('data-resi_pengiriman');
            const pembayaran = button.getAttribute('data-pembayaran');
            const namaPengirim = button.getAttribute('data-nama_pengirim');
            const noHp = button.getAttribute('data-no_hp');
            const alamat = button.getAttribute('data-alamat');
            const keterangan = button.getAttribute('data-keterangan');

            // Set form action
            document.getElementById('editForm').action = `/pengembalian-penukaran/${id}`;

            // Set form values
            document.getElementById('edit_tanggal').value = tanggal;
            document.getElementById('edit_jenis').value = jenis;
            document.getElementById('edit_marketplace').value = marketplace;
            document.getElementById('edit_resi_penerimaan').value = resiPenerimaan;
            document.getElementById('edit_resi_pengiriman').value = resiPengiriman;
            document.getElementById('edit_pembayaran').value = pembayaran;
            document.getElementById('edit_nama_pengirim').value = namaPengirim;
            document.getElementById('edit_no_hp').value = noHp;
            document.getElementById('edit_alamat').value = alamat;
            document.getElementById('edit_keterangan').value = keterangan;
        }

        function confirmDeleteAll() {
            Swal.fire({
                title: 'Hapus Semua Data?',
                text: "Anda akan menghapus semua data pengembalian/penukaran. Tindakan ini tidak dapat dibatalkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus Semua!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteAllForm').submit();
                }
            });
        }

        // Reset create form when modal is closed
        document.getElementById('createModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('create_tanggal').value = '{{ date('Y-m-d') }}';
            document.getElementById('create_jenis').value = '';
            document.getElementById('create_marketplace').value = '';
            document.getElementById('create_pembayaran').value = '';
            document.getElementById('create_resi_penerimaan').value = '';
            document.getElementById('create_resi_pengiriman').value = '';
            document.getElementById('create_nama_pengirim').value = '';
            document.getElementById('create_no_hp').value = '';
            document.getElementById('create_alamat').value = '';
            document.getElementById('create_keterangan').value = '';
        });

        // Reset edit form when modal is closed
        document.getElementById('editModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('editForm').action = '';
            document.getElementById('edit_tanggal').value = '';
            document.getElementById('edit_jenis').value = '';
            document.getElementById('edit_marketplace').value = '';
            document.getElementById('edit_pembayaran').value = '';
            document.getElementById('edit_resi_penerimaan').value = '';
            document.getElementById('edit_resi_pengiriman').value = '';
            document.getElementById('edit_nama_pengirim').value = '';
            document.getElementById('edit_no_hp').value = '';
            document.getElementById('edit_alamat').value = '';
            document.getElementById('edit_keterangan').value = '';
        });

        // Auto capitalize for nama fields
        document.getElementById('create_nama_pengirim').addEventListener('input', function(e) {
            this.value = this.value.replace(/\w\S*/g, function(txt) {
                return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
            });
        });

        document.getElementById('edit_nama_pengirim').addEventListener('input', function(e) {
            this.value = this.value.replace(/\w\S*/g, function(txt) {
                return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
            });
        });

        // Format phone number (Indonesia)
        function formatPhoneNumber(phone) {
            phone = phone.replace(/\D/g, '');
            if (phone.startsWith('0')) {
                phone = '62' + phone.substring(1);
            }
            return phone;
        }

        // Phone number validation and formatting
        document.getElementById('create_no_hp').addEventListener('blur', function(e) {
            if (this.value) {
                this.value = formatPhoneNumber(this.value);
            }
        });

        document.getElementById('edit_no_hp').addEventListener('blur', function(e) {
            if (this.value) {
                this.value = formatPhoneNumber(this.value);
            }
        });
    </script>

    <style>
        .badge.bg-orange {
            background-color: #FF6B35 !important;
            color: white;
        }

        table.dataTable thead th,
        table.dataTable thead td {
            border-bottom: 1px solid #dee2e6;
        }

        table.dataTable.no-footer {
            border-bottom: 1px solid #dee2e6;
        }

        .dt-responsive {
            overflow-x: auto;
        }
    </style>
</x-app-layout>
