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

                    @if(session('warning'))
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            Swal.fire({
                                icon: "warning",
                                title: "Peringatan!",
                                text: "{{ session('warning') }}",
                                showConfirmButton: true,
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
                                showConfirmButton: true,
                            });
                        });
                    </script>
                    @endif

                    @if(session('import_errors'))
                    <div class="alert alert-warning alert-dismissible fade show m-3" role="alert">
                        <h6 class="alert-heading mb-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>Beberapa data gagal diimport:
                        </h6>
                        <div class="small">
                            @foreach(session('import_errors') as $error)
                            <div class="mb-1">
                                <strong>Baris {{ $error['row'] }} ({{ $error['no_pesanan'] }}):</strong>
                                {{ $error['reason'] }}
                            </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-balance-scale"></i> Daftar Banding</h5>
                        <div class="d-flex gap-2">
                            <!-- Import Button -->
                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                                <i class="fas fa-file-import"></i> Import
                            </button>
                            <!-- Export Button -->
                            <a href="{{ route('bandings.export') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-file-export"></i> Export
                            </a>
                            {{-- <!-- Template Button -->
                            <a href="{{ route('bandings.downloadTemplate') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-download"></i> Template
                            </a> --}}
                            <!-- Tambah Data -->
                            <a href="{{ route('bandings.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Tambah Banding
                            </a>
                            <!-- Hapus Semua -->
                            @if($bandings->count() > 0)
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                data-bs-target="#deleteAllModal">
                                <i class="fas fa-trash-alt"></i> Hapus Semua
                            </button>
                            @endif
                        </div>
                    </div>
                    <div class="card-body" style="overflow-x:auto;">
                        @if($bandings->count() > 0)
                        <table id="res-config" class="display table table-striped table-hover dt-responsive nowrap"
                            style="width: 100%">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Tanggal</th>
                                    <th>No. Pesanan</th>
                                    <th>No. Pengajuan</th>
                                    <th>Username</th>
                                    <th>Marketplace</th>
                                    <th>Status Banding</th>
                                    <th>Alasan</th>
                                    <th>Ongkir</th>
                                    <th>Nama Pengirim</th>
                                    <th>No. HP</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bandings as $banding)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        @if($banding->tanggal)
                                        {{ \Carbon\Carbon::parse($banding->tanggal)->format('d/m/Y H:i') }}
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $banding->no_pesanan }}</td>
                                    <td>
                                        @if($banding->no_pengajuan)
                                        {{ $banding->no_pengajuan }}
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $banding->username }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $banding->marketplace == 'Shopee' ? 'warning' : 'danger' }}">
                                            {{ $banding->marketplace }}
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $banding->status_banding == 'Berhasil' ? 'success' : ($banding->status_banding == 'Ditinjau' ? 'warning' : 'danger') }}">
                                            {{ $banding->status_banding }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>{{ $banding->alasan }}</small>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $banding->ongkir == 'Dibebaskan' ? 'success' : ($banding->ongkir == 'Ditanggung' ? 'warning' : 'secondary') }}">
                                            {{ $banding->ongkir }}
                                        </span>
                                    </td>
                                    <td>{{ $banding->nama_pengirim }}</td>
                                    <td>
                                        @if($banding->no_hp)
                                        {{ $banding->no_hp }}
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('bandings.show', $banding->id) }}"
                                                class="btn btn-info btn-sm" title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('bandings.edit', $banding->id) }}"
                                                class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('bandings.destroy', $banding->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus data banding ini?')">
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
                            <i class="fas fa-balance-scale fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada data banding.</p>
                            <div class="d-flex justify-content-center gap-2">
                                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#importModal">
                                    <i class="fas fa-file-import"></i> Import Data
                                </button>
                                <a href="{{ route('bandings.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Tambah Banding Pertama
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Import -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">
                        <i class="fas fa-file-import me-2"></i>Import Data Banding
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('bandings.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="file" class="form-label">Pilih File Excel</label>
                            <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.xls,.csv" required>
                            <div class="form-text">
                                Format file yang didukung: .xlsx, .xls, .csv (Maksimal 10MB)
                            </div>
                        </div>
                        <div class="alert alert-info">
                            <h6 class="alert-heading mb-2">
                                <i class="fas fa-info-circle me-2"></i>Petunjuk Import:
                            </h6>
                            <ul class="mb-0 small">
                                <li>Download template terlebih dahulu untuk format yang benar</li>
                                <li>Pastikan kolom wajib seperti No. Pesanan sudah terisi</li>
                                <li>Format tanggal: DD/MM/YYYY HH:MM</li>
                                <li>Data duplikat akan ditambahkan sebagai data baru</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Batal
                        </button>
                        <a href="{{ route('bandings.downloadTemplate') }}" class="btn btn-outline-primary">
                            <i class="fas fa-download me-1"></i> Download Template
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-1"></i> Import Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus Semua -->
    @if($bandings->count() > 0)
    <div class="modal fade" id="deleteAllModal" tabindex="-1" aria-labelledby="deleteAllModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteAllModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus Semua Data
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>PERINGATAN!</strong>
                    </div>
                    <p>Anda akan menghapus <strong>semua data banding</strong> (total: {{ $bandings->count() }} data).
                    </p>
                    <p class="text-danger mb-0">Tindakan ini tidak dapat dibatalkan! Apakah Anda yakin ingin
                        melanjutkan?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Batal
                    </button>
                    <form action="{{ route('bandings.deleteAll') }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash-alt me-1"></i> Ya, Hapus Semua
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</x-app-layout>
