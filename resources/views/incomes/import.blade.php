<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-file-import"></i> Import Data Income</h5>
                    </div>
                    <div class="card-body">
                        @if(session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            {{ session('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h4>Template Format Excel</h4>
                                                <br>
                                                <p>Download template untuk memastikan format data sesuai:</p>
                                                <a href="{{ route('incomes.download-template') }}"
                                                    class="btn btn-success btn-sm">
                                                    <i class="fas fa-download"></i> Download Template
                                                </a>
                                            </div>

                                            <div class="col-md-6">
                                                <h6>Format Kolom</h6>
                                                <ul class="mb-0">
                                                    <li><strong>No Pesanan</strong>: Text (wajib, unique)</li>
                                                    <li><strong>No Pengajuan</strong>: Text (opsional)</li>
                                                    <li><strong>Total Penghasilan</strong>: Number (wajib, minimal 0)
                                                    </li>
                                                    <li><strong>Periode ID</strong>: Number (opsional)</li>
                                                </ul>
                                                <button type="button" class="btn btn-info btn-sm mt-2"
                                                    data-bs-toggle="modal" data-bs-target="#periodeModal">
                                                    <i class="fas fa-eye"></i> Lihat Daftar Periode
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <form action="{{ route('incomes.import') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="file" class="form-label">Pilih File Excel <span
                                                    class="text-danger">*</span></label>
                                            <input type="file" class="form-control @error('file') is-invalid @enderror"
                                                id="file" name="file" accept=".xlsx,.xls,.csv" required>
                                            @error('file')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Format file: .xlsx, .xls, .csv (maksimal 5MB)</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="default_periode_id" class="form-label">Default Periode
                                                (Opsional)</label>
                                            <div class="input-group">
                                                <select
                                                    class="form-control @error('default_periode_id') is-invalid @enderror"
                                                    id="default_periode_id" name="default_periode_id">
                                                    <option value="">Pilih Default Periode</option>
                                                    @foreach($periodes as $periode)
                                                    <option value="{{ $periode->id }}" {{
                                                        old('default_periode_id')==$periode->id ? 'selected' : '' }}>
                                                        {{ $periode->nama_periode }} - {{ $periode->toko->nama }} ({{
                                                        $periode->marketplace }})
                                                    </option>
                                                    @endforeach
                                                </select>
                                                <button type="button" class="btn btn-outline-primary"
                                                    data-bs-toggle="modal" data-bs-target="#periodeModal">
                                                    <i class="fas fa-list"></i>
                                                </button>
                                            </div>
                                            @error('default_periode_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">
                                                Jika kolom "Periode ID" kosong di Excel, akan menggunakan periode ini
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <a href="{{ route('incomes.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-upload"></i> Import Data
                                    </button>
                                </div>
                            </form>
                        </div>

                        @if(session('failures'))
                        <div class="mt-4">
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <h6 class="alert-heading">Import Notice:</h6>
                                <p class="mb-2">{{ session('success') ?? 'Proses import selesai dengan beberapa
                                    kegagalan.' }}</p>
                                <p class="mb-0">
                                    <strong>{{ count(session('failures')) }} data</strong> gagal diimport.
                                    <button type="button" class="btn btn-sm btn-outline-warning ms-1"
                                        data-bs-toggle="collapse" data-bs-target="#importFailures"
                                        onclick="event.preventDefault(); event.stopPropagation();">
                                        Lihat Detail
                                    </button>
                                </p>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"
                                    style="top: 1rem; right: 1rem;"></button>
                            </div>

                            <div class="collapse mt-2" id="importFailures">
                                <div class="card card-body">
                                    <h6>Data yang Gagal:</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Baris</th>
                                                    <th>No Pesanan</th>
                                                    <th>Periode ID</th>
                                                    <th>Alasan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach(session('failures') as $failure)
                                                <tr>
                                                    <td>{{ $failure['row'] }}</td>
                                                    <td>{{ $failure['no_pesanan'] }}</td>
                                                    <td>{{ $failure['periode_id'] ?? '-' }}</td>
                                                    <td class="text-danger small">{{ $failure['reason'] }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
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
    <div class="modal fade" id="periodeModal" tabindex="-1" aria-labelledby="periodeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="periodeModalLabel">
                        <i class="fas fa-calendar-alt"></i> Daftar Periode yang Tersedia
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Gunakan ID periode di bawah pada kolom "Periode ID" di file Excel
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-primary">
                                <tr>
                                    <th>ID Periode</th>
                                    <th>Nama Periode</th>
                                    <th>Toko</th>
                                    <th>Marketplace</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Jumlah Income</th>
                                    <th>Status Generate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($periodes as $periode)
                                <tr>
                                    <td><strong>{{ $periode->id }}</strong></td>
                                    <td>{{ $periode->nama_periode }}</td>
                                    <td>{{ $periode->toko->nama }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $periode->marketplace == 'Shopee' ? 'warning' : 'info' }}">
                                            {{ $periode->marketplace }}
                                        </span>
                                    </td>
                                    <td>{{ $periode->tanggal_mulai->format('d/m/Y') }}</td>
                                    <td>{{ $periode->tanggal_selesai->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $periode->incomes->count() }}</span>
                                    </td>
                                    <td>
                                        @if($periode->is_generated)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check"></i> Generated
                                        </span>
                                        @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-clock"></i> Belum
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        #periodeModal .table th {
            white-space: nowrap;
        }

        #periodeModal .table td {
            vertical-align: middle;
        }
    </style>
</x-app-layout>
