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

                    @if($errors->any())
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                Swal.fire({
                                    icon: "error",
                                    title: "Gagal!",
                                    html: "Mohon periksa kembali input Anda.",
                                    showConfirmButton: true
                                });
                            });
                        </script>
                    @endif

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-store"></i> Daftar Toko</h5>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#createTokoModal">
                                <i class="fas fa-plus"></i> Tambah Toko
                            </button>
                        </div>
                    </div>

                    <div class="card-body" style="overflow-x:auto;">
                        @if($tokos->count() > 0)
                        <table id="res-config" class="display table table-striped table-hover dt-responsive nowrap"
                            style="width: 100%">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>ID</th>
                                    <th>Nama Toko</th>
                                    <th>Dibuat</th>
                                    <th>Diupdate</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tokos as $toko)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $toko->id }}</td>
                                    <td>{{ $toko->nama }}</td>
                                    <td>{{ $toko->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $toko->updated_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-warning btn-sm"
                                                data-bs-toggle="modal" data-bs-target="#editTokoModal{{ $toko->id }}"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('toko.destroy', $toko->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus toko ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <div class="modal fade" id="editTokoModal{{ $toko->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <form action="{{ route('toko.update', $toko->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">
                                                        <i class="fas fa-edit"></i> Edit Toko
                                                    </h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-2">
                                                        <label class="form-label">Nama Toko <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" name="nama" class="form-control"
                                                            value="{{ old('nama', $toko->nama) }}" required>
                                                        @error('nama')
                                                        <div class="text-danger mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <div class="text-center py-4">
                            <i class="fas fa-store fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada data toko.</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#createTokoModal">
                                <i class="fas fa-plus"></i> Tambah Toko Pertama
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createTokoModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('toko.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-plus"></i> Tambah Toko
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-2">
                            <label class="form-label">Nama Toko <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required>
                            @error('nama')
                            <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Tambah</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="viewTokoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-info-circle"></i> Detail Toko
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>ID:</strong> <span id="view-toko-id"></span></p>
                    <p><strong>Nama Toko:</strong> <span id="view-toko-nama"></span></p>
                    <p><strong>Dibuat Pada:</strong> <span id="view-toko-created"></span></p>
                    <p><strong>Diperbarui Pada:</strong> <span id="view-toko-updated"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Logika untuk menampilkan modal View Toko
            document.querySelectorAll('.view-toko').forEach(button => {
                button.addEventListener('click', function() {
                    const tokoId = this.dataset.id;
                    const url = `{{ url('toko') }}/${tokoId}`; // Gunakan url() untuk base URL

                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            document.getElementById('view-toko-id').textContent = data.id;
                            document.getElementById('view-toko-nama').textContent = data.nama;
                            document.getElementById('view-toko-created').textContent = new Date(data.created_at).toLocaleString('id-ID', { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' });
                            document.getElementById('view-toko-updated').textContent = new Date(data.updated_at).toLocaleString('id-ID', { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' });

                            const viewModal = new bootstrap.Modal(document.getElementById('viewTokoModal'));
                            viewModal.show();
                        })
                        .catch(error => console.error('Error fetching toko:', error));
                });
            });

            // Logika untuk menampilkan kembali modal Edit jika ada error validasi
            @if ($errors->any())
                @foreach ($tokos as $toko)
                    @if ($errors->has('nama') && old('_method') === 'PUT' && request()->route('toko') == $toko->id)
                        const editModal = new bootstrap.Modal(document.getElementById('editTokoModal{{ $toko->id }}'));
                        editModal.show();
                        break;
                    @endif
                @endforeach

                // Tampilkan kembali modal Create jika ada error validasi di Create
                @if ($errors->has('nama') && old('_method') !== 'PUT')
                    const createModal = new bootstrap.Modal(document.getElementById('createTokoModal'));
                    createModal.show();
                @endif
            @endif
        });
    </script>
    @endpush
</x-app-layout>
