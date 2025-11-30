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
                        <h5 class="mb-0"><i class="fas fa-vial"></i> Daftar Sampel</h5>
                        <div class="d-flex gap-2">
                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                                <i class="fas fa-file-import"></i> Import
                            </button>
                            <a href="{{ route('sampels.export') }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-file-export"></i> Export
                            </a>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">
                                <i class="fas fa-plus"></i> Tambah Sampel
                            </button>
                        </div>
                    </div>

                    <div class="card-body" style="overflow-x:auto;">
                        @if($sampels->count() > 0)
                        <table id="res-config" class="display table table-striped table-hover dt-responsive nowrap"
                            style="width: 100%">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Nama Sampel</th>
                                    <th>Ukuran</th>
                                    <th>Harga</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sampels as $sampel)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $sampel->nama }}</td>
                                    <td>{{ $sampel->ukuran }}</td>
                                    <td>Rp {{ number_format($sampel->harga, 0, ',', '.') }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-warning btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editModal"
                                                    data-id="{{ $sampel->id }}"
                                                    data-nama="{{ $sampel->nama }}"
                                                    data-ukuran="{{ $sampel->ukuran }}"
                                                    data-harga="{{ $sampel->harga }}"
                                                    onclick="editSampel(this)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('sampels.destroy', $sampel->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus sampel ini?')">
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
                            <i class="fas fa-vial fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada data sampel.</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                                <i class="fas fa-plus"></i> Tambah Sampel Pertama
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Tambah Sampel Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('sampels.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="create_nama" class="form-label">Nama Sampel</label>
                            <input type="text" class="form-control" id="create_nama" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="create_ukuran" class="form-label">Ukuran</label>
                            <input type="text" class="form-control" id="create_ukuran" name="ukuran" required>
                        </div>
                        <div class="mb-3">
                            <label for="create_harga" class="form-label">Harga</label>
                            <input type="number" class="form-control" id="create_harga" name="harga" min="0" required>
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Sampel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_nama" class="form-label">Nama Sampel</label>
                            <input type="text" class="form-control" id="edit_nama" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_ukuran" class="form-label">Ukuran</label>
                            <input type="text" class="form-control" id="edit_ukuran" name="ukuran" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_harga" class="form-label">Harga</label>
                            <input type="number" class="form-control" id="edit_harga" name="harga" min="0" required>
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
                    <h5 class="modal-title"><i class="fas fa-file-import"></i> Import Data Sampel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('sampels.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="file" class="form-label">Pilih File Excel</label>
                            <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.xls,.csv" required>
                            <div class="form-text">
                                Format file: Excel (.xlsx, .xls) atau CSV.
                                <a href="{{ route('sampels.export') }}" class="text-decoration-none">
                                    Download template
                                </a>
                            </div>
                        </div>
                        <div class="alert alert-info">
                            <small>
                                <i class="fas fa-info-circle"></i>
                                Pastikan file memiliki kolom: Nama Sampel, Ukuran, Harga
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
        function editSampel(button) {
            const id = button.getAttribute('data-id');
            const nama = button.getAttribute('data-nama');
            const ukuran = button.getAttribute('data-ukuran');
            const harga = button.getAttribute('data-harga');

            // Set form action
            document.getElementById('editForm').action = `/sampels/${id}`;

            // Set form values
            document.getElementById('edit_nama').value = nama;
            document.getElementById('edit_ukuran').value = ukuran;
            document.getElementById('edit_harga').value = harga;
        }

        // Reset create form when modal is closed
        document.getElementById('createModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('create_nama').value = '';
            document.getElementById('create_ukuran').value = '';
            document.getElementById('create_harga').value = '';
        });

        // Reset edit form when modal is closed
        document.getElementById('editModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('editForm').action = '';
            document.getElementById('edit_nama').value = '';
            document.getElementById('edit_ukuran').value = '';
            document.getElementById('edit_harga').value = '';
        });

        // Auto capitalize for nama fields
        document.getElementById('create_nama').addEventListener('input', function(e) {
            this.value = this.value.replace(/\w\S*/g, function(txt) {
                return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
            });
        });

        document.getElementById('edit_nama').addEventListener('input', function(e) {
            this.value = this.value.replace(/\w\S*/g, function(txt) {
                return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
            });
        });

        // Auto uppercase for ukuran fields
        document.getElementById('create_ukuran').addEventListener('input', function(e) {
            this.value = this.value.toUpperCase();
        });

        document.getElementById('edit_ukuran').addEventListener('input', function(e) {
            this.value = this.value.toUpperCase();
        });
    </script>
</x-app-layout>
