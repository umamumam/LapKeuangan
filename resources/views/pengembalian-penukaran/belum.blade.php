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

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <span class="pc-micon">
                                <i class="fas fa-clock"></i>
                            </span>
                            Data Pengembalian/Penukaran - Status Belum
                        </h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('pengembalian-penukaran.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-list"></i> Semua Data
                            </a>
                            <a href="{{ route('pengembalian-penukaran.ok') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-check-circle"></i> Status OK
                            </a>
                        </div>
                    </div>

                    <!-- Form Filter -->
                    <div class="card-header">
                        <h5 class="mb-3"><i class="fas fa-filter"></i> Filter Data Status Belum</h5>
                        <form method="GET" action="{{ route('pengembalian-penukaran.belum') }}" class="row g-3 align-items-end">
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
                                <a href="{{ route('pengembalian-penukaran.belum') }}" class="btn btn-secondary flex-fill">
                                    <i class="fas fa-redo"></i> Reset
                                </a>
                            </div>
                        </form>
                    </div>

                    <div class="card-body" style="overflow-x:auto;">
                        @if($pengembalianPenukaran->count() > 0)
                        <div class="alert alert-warning mb-3">
                            <i class="fas fa-exclamation-triangle"></i>
                            Terdapat {{ $pengembalianPenukaran->count() }} data dengan status "Belum" dalam periode ini.
                            Gunakan fitur <strong>Scan Resi</strong> untuk mengubah status menjadi OK.
                        </div>

                        <table id="res-config" class="display table table-striped table-hover dt-responsive nowrap"
                            style="width: 100%">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Tanggal</th>
                                    {{-- <th>Jenis</th>
                                    <th>Nama Pengirim</th>
                                    <th>No HP</th> --}}
                                    <th>Resi Penerimaan</th>
                                    <th>Resi Pengiriman</th>
                                    {{-- <th>Pembayaran</th> --}}
                                    <th>Status</th>
                                    <th>Marketplace</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pengembalianPenukaran as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                    {{-- <td>
                                        @if($item->jenis == 'Pengembalian')
                                        <span class="badge bg-warning">{{ $item->jenis }}</span>
                                        @elseif($item->jenis == 'Penukaran')
                                        <span class="badge bg-info">{{ $item->jenis }}</span>
                                        @else
                                        <span class="badge bg-success">{{ $item->jenis }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->nama_pengirim }}</td>
                                    <td>{{ $item->no_hp }}</td> --}}
                                    <td>{{ $item->resi_penerimaan ?? '-' }}</td>
                                    <td>{{ $item->resi_pengiriman ?? '-' }}</td>
                                    {{-- <td>
                                        @if($item->pembayaran == 'Sistem')
                                        <span class="badge bg-primary">{{ $item->pembayaran }}</span>
                                        @elseif($item->pembayaran == 'Tunai')
                                        <span class="badge bg-success">{{ $item->pembayaran }}</span>
                                        @else
                                        <span class="badge bg-danger">{{ $item->pembayaran }}</span>
                                        @endif
                                    </td> --}}
                                    <td>
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock"></i> Belum
                                        </span>
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
                                    <td>
                                        <button class="btn btn-success btn-sm" onclick="markAsOK('{{ $item->id }}', '{{ $item->resi_penerimaan ?: $item->resi_pengiriman }}')">
                                            <i class="fas fa-check"></i> Tandai OK
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <p class="text-success fw-bold">Selamat! Semua data sudah berstatus OK.</p>
                            <p class="text-muted">Tidak ada data dengan status "Belum" dalam periode ini.</p>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('pengembalian-penukaran.search.ok') }}" class="btn btn-success">
                                    <i class="fas fa-camera"></i> Scan Resi Baru
                                </a>
                                <a href="{{ route('pengembalian-penukaran.ok') }}" class="btn btn-primary">
                                    <i class="fas fa-check-circle"></i> Lihat Status OK
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function markAsOK(id, resi) {
            Swal.fire({
                title: 'Tandai sebagai OK?',
                text: "Status akan diubah menjadi OK untuk resi: " + resi,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Tandai OK',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kirim request update status
                    fetch('/pengembalian-penukaran/' + id + '/update-status', {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                // Refresh halaman setelah sukses
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: data.message
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan: ' + error.message
                        });
                    });
                }
            });
        }
    </script>
</x-app-layout>
