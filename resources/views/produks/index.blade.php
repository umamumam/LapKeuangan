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
                                title: "Gagal!",
                                text: "{{ session('error') }}",
                                showConfirmButton: false,
                                timer: 3000
                            });
                        });
                    </script>
                    @endif

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list"></i> Daftar Produk</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('produks.import.form') }}" class="btn btn-info btn-sm">
                                <i class="fas fa-file-import"></i> Import
                            </a>
                            <a href="{{ route('produks.export') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-file-export"></i> Export
                            </a>
                            <a href="{{ route('produks.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Tambah Produk
                            </a>
                            @if($produks->count() > 0)
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteAllModal">
                                <i class="fas fa-trash-alt"></i> Hapus Semua
                            </button>
                            @endif
                        </div>
                    </div>

                    <!-- Form Pencarian dengan 2 Kolom -->
                    <div class="card-body border-bottom">
                        <form method="GET" action="{{ route('produks.index') }}" class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label">Cari Nama Produk</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-cube"></i>
                                    </span>
                                    <input type="text"
                                           name="search_produk"
                                           class="form-control"
                                           placeholder="Masukkan nama produk..."
                                           value="{{ request('search_produk') }}">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Cari Nama Variasi</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-list-alt"></i>
                                    </span>
                                    <input type="text"
                                           name="search_variasi"
                                           class="form-control"
                                           placeholder="Masukkan nama variasi..."
                                           value="{{ request('search_variasi') }}">
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <div class="d-flex gap-2 w-100">
                                    <button type="submit" class="btn btn-primary flex-fill">
                                        <i class="fas fa-search"></i> Cari
                                    </button>
                                    @if(request('search_produk') || request('search_variasi'))
                                    <a href="{{ route('produks.index') }}" class="btn btn-secondary" title="Reset">
                                        <i class="fas fa-times"></i>
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </form>

                        <!-- Info Filter Aktif -->
                        @if(request('search_produk') || request('search_variasi'))
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-filter"></i> Filter aktif:
                                @if(request('search_produk') && request('search_variasi'))
                                Produk: <strong>"{{ request('search_produk') }}"</strong>
                                dan Variasi: <strong>"{{ request('search_variasi') }}"</strong>
                                @elseif(request('search_produk'))
                                Produk: <strong>"{{ request('search_produk') }}"</strong>
                                @elseif(request('search_variasi'))
                                Variasi: <strong>"{{ request('search_variasi') }}"</strong>
                                @endif
                            </small>
                        </div>
                        @endif
                    </div>

                    <div class="card-body" style="overflow-x:auto;">
                        @if($produks->count() > 0)
                        <!-- Info Hasil Pencarian -->
                        @if(request('search_produk') || request('search_variasi'))
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle"></i>
                            Menampilkan {{ $produks->count() }} hasil
                            @if(request('search_produk') && request('search_variasi'))
                                untuk produk "<strong>{{ request('search_produk') }}</strong>"
                                dan variasi "<strong>{{ request('search_variasi') }}</strong>"
                            @elseif(request('search_produk'))
                                untuk produk "<strong>{{ request('search_produk') }}</strong>"
                            @elseif(request('search_variasi'))
                                untuk variasi "<strong>{{ request('search_variasi') }}</strong>"
                            @endif
                        </div>
                        @endif

                        <table id="res-config" class="display table table-striped table-hover dt-responsive nowrap"
                            style="width: 100%">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>SKU Induk</th>
                                    <th>Nama Produk</th>
                                    <th>Referensi SKU</th>
                                    <th>Variasi</th>
                                    <th>HPP</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($produks as $produk)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $produk->sku_induk ?? '-' }}</td>
                                    <td>{{ $produk->nama_produk }}</td>
                                    <td>{{ $produk->nomor_referensi_sku ?? '-' }}</td>
                                    <td>{{ $produk->nama_variasi ?? '-' }}</td>
                                    <td>Rp {{ number_format($produk->hpp_produk, 0, ',', '.') }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('produks.show', $produk->id) }}"
                                                class="btn btn-info btn-sm" title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('produks.edit', $produk->id) }}"
                                                class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('produks.destroy', $produk->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
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
                            @if(request('search_produk') || request('search_variasi'))
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <p class="text-muted">
                                Tidak ditemukan hasil
                                @if(request('search_produk') && request('search_variasi'))
                                    untuk produk "<strong>{{ request('search_produk') }}</strong>"
                                    dan variasi "<strong>{{ request('search_variasi') }}</strong>"
                                @elseif(request('search_produk'))
                                    untuk produk "<strong>{{ request('search_produk') }}</strong>"
                                @elseif(request('search_variasi'))
                                    untuk variasi "<strong>{{ request('search_variasi') }}</strong>"
                                @endif
                            </p>
                            <a href="{{ route('produks.index') }}" class="btn btn-primary">
                                <i class="fas fa-times"></i> Tampilkan Semua Produk
                            </a>
                            @else
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada data produk.</p>
                            <a href="{{ route('produks.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tambah Produk Pertama
                            </a>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus Semua -->
    @if($produks->count() > 0)
    <div class="modal fade" id="deleteAllModal" tabindex="-1" aria-labelledby="deleteAllModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteAllModalLabel">Konfirmasi Hapus Semua Data Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>PERINGATAN!</strong>
                    </div>
                    <p>Anda akan menghapus <strong>semua data produk</strong> (total: {{ $produks->count() }} data).</p>
                    <p class="text-danger mb-0">Tindakan ini tidak dapat dibatalkan! Apakah Anda yakin ingin melanjutkan?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('produks.deleteAll') }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash-alt"></i> Ya, Hapus Semua
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</x-app-layout>
