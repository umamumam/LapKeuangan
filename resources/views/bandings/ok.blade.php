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

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0" style="color: green"><i class="fas fa-check-circle"></i> Daftar Status OK (Sudah Diterima)</h5>
                        <div class="d-flex gap-2">
                            <!-- Export Button -->
                            {{-- <a href="{{ route('bandings.export') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-file-export"></i> Export
                            </a> --}}
                        </div>
                    </div>

                    <!-- Form Filter -->
                    <div class="card-body">
                        <form action="{{ route('bandings.ok') }}" method="GET" class="row g-3 mb-4"> <!-- UBAH ROUTE -->
                            <div class="col-md-2">
                                <label for="marketplace" class="form-label">Marketplace</label>
                                <select name="marketplace" id="marketplace" class="form-select">
                                    <option value="all">Semua Marketplace</option>
                                    @foreach($marketplaceOptions as $value => $label)
                                    <option value="{{ $value }}" {{ $marketplace==$value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
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
                                <label for="status_diterima" class="form-label">Status Diterima</label>
                                <select name="status_diterima" id="status_diterima" class="form-select">
                                    <option value="all">Semua Status</option>
                                    @foreach($statusDiterimaOptions as $value => $label)
                                    <option value="{{ $value }}" {{ $statusDiterima==$value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label for="start_date" class="form-label">Dari Tanggal</label>
                                <input type="date" name="start_date" id="start_date" class="form-control"
                                    value="{{ $startDate }}">
                            </div>

                            <div class="col-md-2">
                                <label for="end_date" class="form-label">Sampai Tanggal</label>
                                <input type="date" name="end_date" id="end_date" class="form-control"
                                    value="{{ $endDate }}">
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <div class="d-flex gap-2 w-100">
                                    <button type="submit" class="btn btn-primary w-50">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                    <a href="{{ route('bandings.ok') }}" class="btn btn-secondary w-50"> <!-- UBAH ROUTE -->
                                        <i class="fas fa-undo"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </form>

                        <!-- Info Filter Aktif -->
                        @if($marketplace && $marketplace !== 'all' || $tokoId && $tokoId !== 'all' || $statusDiterima &&
                        $statusDiterima !== 'all' || $startDate || $endDate)
                        <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-filter me-2"></i>
                                    <strong>Filter Aktif:</strong>
                                    @if($marketplace && $marketplace !== 'all')
                                    <span class="badge bg-primary ms-2 me-2">
                                        {{ $marketplaceOptions[$marketplace] ?? $marketplace }}
                                    </span>
                                    @endif
                                    @if($tokoId && $tokoId !== 'all')
                                    <span class="badge bg-success ms-2 me-2">
                                        Toko: {{ $tokoOptions[$tokoId] ?? '' }}
                                    </span>
                                    @endif
                                    @if($statusDiterima && $statusDiterima !== 'all')
                                    <span class="badge bg-success ms-2 me-2">
                                        Status: {{ $statusDiterimaOptions[$statusDiterima] ?? $statusDiterima }}
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
                                        Total Data: {{ $bandings->count() }}
                                    </span>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        </div>
                        @endif

                        <div class="table-responsive" style="overflow-x:auto;">
                            @if($bandings->count() > 0)
                            <table id="res-config" class="display table table-striped table-hover dt-responsive nowrap"
                                style="width: 100%">
                                <thead class="table-primary">
                                    <tr>
                                        <th>#</th>
                                        <th>Tanggal</th>
                                        <th>No. Resi</th>
                                        <th>No. Pesanan</th>
                                        {{-- <th>No. Pengajuan</th>
                                        <th>Username</th>
                                        <th>Status Banding</th> --}}
                                        <th>Status Diterima</th> <!-- TAMBAH KOLOM -->
                                        <th>Marketplace</th>
                                        <th>Toko</th>
                                        {{-- <th>Alasan</th>
                                        <th>Ongkir</th>
                                        <th>Nama Pengirim</th>
                                        <th>No. HP</th>
                                        <th>Aksi</th> --}}
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
                                        <td>{{ $banding->no_resi }}</td>
                                        <td>{{ $banding->no_pesanan }}</td>
                                        {{-- <td>
                                            @if($banding->no_pengajuan)
                                            {{ $banding->no_pengajuan }}
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $banding->username }}</td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $banding->status_banding == 'Berhasil' ? 'success' : ($banding->status_banding == 'Ditinjau' ? 'warning' : 'danger') }}">
                                                {{ $banding->status_banding }}
                                            </span>
                                        </td> --}}
                                        <td>
                                            <span class="badge bg-success">
                                                {{ $banding->statusditerima }} <!-- GUNAKAN statusditerima BUKAN status_diterima -->
                                            </span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $banding->marketplace == 'Shopee' ? 'warning' : 'danger' }}">
                                                {{ $banding->marketplace }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($banding->toko)
                                            <span class="badge bg-secondary">
                                                {{ $banding->toko->nama }}
                                            </span>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        {{-- <td>
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
                                            </div>
                                        </td> --}}
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @else
                            <div class="text-center py-4">
                                <i class="fas fa-check-circle fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada data dengan status OK.</p>
                                @if($marketplace && $marketplace !== 'all' || $tokoId && $tokoId !== 'all' ||
                                $statusDiterima && $statusDiterima !== 'all' || $startDate || $endDate)
                                <p class="text-warning mb-3">Tidak ada data dengan filter yang dipilih.</p>
                                @endif
                                <a href="{{ route('bandings.index') }}" class="btn btn-primary">
                                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Utama
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- HAPUS MODAL IMPORT & DELETE ALL (TIDAK DIBUTUHKAN DI SINI) -->

</x-app-layout>

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
            order: [[1, 'desc']], // Sort by date
            pageLength: 25
        });
    }
});
</script>
