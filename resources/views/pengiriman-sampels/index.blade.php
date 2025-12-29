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
                                <strong>Baris {{ $error['row'] }}:</strong>
                                {{ $error['reason'] }}
                            </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-shipping-fast"></i> Daftar Pengiriman Sampel</h5>
                        <div class="d-flex gap-2">
                            <!-- Import Button -->
                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                data-bs-target="#importModal">
                                <i class="fas fa-file-import"></i> Import
                            </button>
                            <!-- Export Button -->
                            <a href="{{ route('pengiriman-sampels.export') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-file-export"></i> Export
                            </a>
                            <!-- Tambah Data -->
                            <a href="{{ route('pengiriman-sampels.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Tambah Pengiriman
                            </a>
                            <!-- Hapus Semua -->
                            @if($pengirimanSampels->count() > 0)
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                data-bs-target="#deleteAllModal">
                                <i class="fas fa-trash-alt"></i> Hapus Semua
                            </button>
                            @endif
                        </div>
                    </div>

                    <!-- Form Filter -->
                    <div class="card-body">
                        <form action="{{ route('pengiriman-sampels.index') }}" method="GET" class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label for="toko_id" class="form-label">Toko</label>
                                <select name="toko_id" id="toko_id" class="form-select">
                                    <option value="all">Semua Toko</option>
                                    @foreach($tokoOptions as $id => $nama)
                                    <option value="{{ $id }}" {{ $tokoId==$id ? 'selected' : '' }}>
                                        {{ $nama }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label for="start_date" class="form-label">Dari Tanggal</label>
                                <input type="date" name="start_date" id="start_date" class="form-control"
                                    value="{{ $startDate ?? now()->startOfMonth()->format('Y-m-d') }}">
                            </div>

                            <div class="col-md-2">
                                <label for="end_date" class="form-label">Sampai Tanggal</label>
                                <input type="date" name="end_date" id="end_date" class="form-control"
                                    value="{{ $endDate ?? now()->endOfMonth()->format('Y-m-d') }}">
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <div class="d-flex gap-2 w-100">
                                    <button type="submit" class="btn btn-primary w-50">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                    <a href="{{ route('pengiriman-sampels.index') }}" class="btn btn-secondary w-50">
                                        <i class="fas fa-undo"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </form>

                        <!-- Info Filter Aktif -->
                        @if($tokoId && $tokoId !== 'all' || $startDate || $endDate)
                        <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-filter me-2"></i>
                                    <strong>Filter Aktif:</strong>
                                    @if($tokoId && $tokoId !== 'all')
                                    @php
                                    $selectedToko = $tokoOptions->firstWhere('id', $tokoId) ??
                                    collect($tokoOptions)->firstWhere('id', $tokoId);
                                    @endphp
                                    <span class="badge bg-success ms-2 me-2">
                                        Toko: {{ $selectedToko->nama ?? ($tokoOptions[$tokoId] ?? '') }}
                                    </span>
                                    @endif
                                    @if($startDate)
                                    <span class="badge bg-info ms-2 me-2">
                                        Dari: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}
                                    </span>
                                    @endif
                                    @if($endDate)
                                    <span class="badge bg-info ms-2 me-2">
                                        Sampai: {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                                    </span>
                                    @endif
                                    <span class="badge bg-secondary ms-2">
                                        Total Data: {{ $pengirimanSampels->count() }}
                                    </span>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        </div>
                        @endif

                        <div style="overflow-x:auto;">
                            @if($pengirimanSampels->count() > 0)
                            <table id="res-config" class="display table table-striped table-hover dt-responsive nowrap"
                                style="width: 100%">
                                <thead class="table-primary">
                                    <tr>
                                        <th>#</th>
                                        <th>Tanggal</th>
                                        <th>Toko</th> <!-- Kolom baru -->
                                        <th>No. Resi</th>
                                        <th>Penerima</th>
                                        <th>Jumlah Sampel</th>
                                        <th>Total Jumlah</th>
                                        <th>Total HPP</th>
                                        <th>Ongkir</th>
                                        <th>Total Biaya</th>
                                        <th>Username</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pengirimanSampels as $pengiriman)
                                    @php
                                    $sampelDetails = $pengiriman->getSampelDetails();
                                    $totalJumlah = 0;
                                    foreach ($sampelDetails as $detail) {
                                    $totalJumlah += $detail['jumlah'];
                                    }
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $pengiriman->tanggal->format('d/m/Y H:i') }}</strong>
                                        </td>
                                        <td>
                                            @if($pengiriman->toko)
                                            <span class="badge bg-secondary">
                                                {{ $pengiriman->toko->nama }}
                                            </span>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $pengiriman->no_resi }}</td>
                                        <td>
                                            <strong>{{ $pengiriman->penerima }}</strong><br>
                                            <small class="text-muted">{{ $pengiriman->contact }}</small>
                                        </td>
                                        <td>
                                            @if(count($sampelDetails) > 0)
                                            <div class="mb-1">
                                                <span class="badge bg-info">{{ count($sampelDetails) }} Sampel</span>
                                            </div>
                                            <div class="small">
                                                @foreach($sampelDetails as $detail)
                                                <div class="mb-1">
                                                    <i class="fas fa-box me-1"></i>
                                                    {{ $detail['nama'] }} ({{ $detail['jumlah'] }} pcs)
                                                </div>
                                                @endforeach
                                            </div>
                                            @else
                                            <span class="badge bg-warning">Tidak ada sampel</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $totalJumlah }} pcs</strong>
                                        </td>
                                        <td>
                                            Rp {{ number_format($pengiriman->totalhpp, 0, ',', '.') }}
                                        </td>
                                        <td>
                                            Rp {{ number_format($pengiriman->ongkir, 0, ',', '.') }}
                                        </td>
                                        <td>
                                            <strong>Rp {{ number_format($pengiriman->total_biaya, 0, ',', '.')
                                                }}</strong>
                                        </td>
                                        <td>{{ $pengiriman->username }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('pengiriman-sampels.show', $pengiriman->id) }}"
                                                    class="btn btn-info btn-sm" title="Lihat">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('pengiriman-sampels.edit', $pengiriman->id) }}"
                                                    class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form
                                                    action="{{ route('pengiriman-sampels.destroy', $pengiriman->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus pengiriman ini?')">
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
                                <i class="fas fa-shipping-fast fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada data pengiriman sampel.</p>
                                @if($tokoId && $tokoId !== 'all' || $startDate || $endDate)
                                <p class="text-warning mb-3">Tidak ada data dengan filter yang dipilih.</p>
                                @endif
                                <div class="d-flex justify-content-center gap-2">
                                    <button type="button" class="btn btn-info" data-bs-toggle="modal"
                                        data-bs-target="#importModal">
                                        <i class="fas fa-file-import"></i> Import Data
                                    </button>
                                    <a href="{{ route('pengiriman-sampels.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Tambah Pengiriman Pertama
                                    </a>
                                </div>
                            </div>
                            @endif
                        </div>
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
                        <i class="fas fa-file-import me-2"></i>Import Data Pengiriman Sampel
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('pengiriman-sampels.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="file" class="form-label">Pilih File Excel</label>
                            <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.xls,.csv"
                                required>
                            <div class="form-text">
                                Format file yang didukung: .xlsx, .xls, .csv (Maksimal 10MB)
                            </div>
                        </div>

                        <!-- Tambah pilihan toko di modal import -->
                        <div class="mb-3">
                            <label for="import_toko_id" class="form-label">Pilih Toko untuk Semua Data Import</label>
                            <select class="form-select" id="import_toko_id" name="toko_id" required>
                                <option value="">Pilih Toko</option>
                                @foreach($tokoOptions as $id => $nama)
                                <option value="{{ $id }}">{{ $nama }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                Semua data dalam file Excel akan diimport ke toko yang dipilih ini
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <h6 class="alert-heading mb-2">
                                <i class="fas fa-info-circle me-2"></i>Petunjuk Import:
                            </h6>
                            <ul class="mb-0 small">
                                <li>Pastikan kolom wajib seperti No. Resi sudah terisi</li>
                                <li>Format tanggal: YYYY-MM-DD HH:MM</li>
                                <li>Format sampel: ID Sampel (gunakan koma untuk multiple sampel)</li>
                                <li>Data duplikat akan ditambahkan sebagai data baru</li>
                                <li><strong>Catatan:</strong> Kolom toko_id dalam Excel akan diabaikan, menggunakan toko
                                    yang dipilih di atas</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-1"></i> Import Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus Semua -->
    @if($pengirimanSampels->count() > 0)
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
                    <p>Anda akan menghapus <strong>semua data pengiriman sampel</strong> (total: {{
                        $pengirimanSampels->count() }} data).</p>
                    <p class="text-danger mb-0">Tindakan ini tidak dapat dibatalkan! Apakah Anda yakin ingin
                        melanjutkan?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Batal
                    </button>
                    <form action="{{ route('pengiriman-sampels.deleteAll') }}" method="POST" class="d-inline">
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

<!-- DataTables Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Validasi tanggal
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');

    if (startDateInput && endDateInput) {
        endDateInput.addEventListener('change', function() {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);

            if (startDate && endDate && startDate > endDate) {
                alert('Tanggal akhir tidak boleh sebelum tanggal mulai!');
                endDateInput.value = startDateInput.value;
            }
        });

        startDateInput.addEventListener('change', function() {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);

            if (startDate && endDate && startDate > endDate) {
                endDateInput.value = startDateInput.value;
            }
        });
    }

    // DataTable initialization
    if (document.getElementById('res-config')) {
        $('#res-config').DataTable({
            responsive: true,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Berikutnya",
                    previous: "Sebelumnya"
                }
            },
            order: [[1, 'desc']], // Urutkan berdasarkan tanggal (kolom ke-2)
            pageLength: 25
        });
    }
});
</script>
