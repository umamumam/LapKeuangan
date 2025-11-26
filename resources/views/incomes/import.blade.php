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
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h6>Template Format Excel</h6>
                                        <p>Download template untuk memastikan format data sesuai:</p>
                                        <a href="{{ route('incomes.download-template') }}"
                                            class="btn btn-success btn-sm">
                                            <i class="fas fa-download"></i> Download Template
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h6>Format Kolom</h6>
                                        <ul class="mb-0">
                                            <li><strong>No Pesanan</strong>: Text (wajib, unique)</li>
                                            <li><strong>No Pengajuan</strong>: Text (opsional)</li>
                                            <li><strong>Total Penghasilan</strong>: Number (wajib)</li>
                                            <li><strong>Toko ID</strong>: Number (opsional, gunakan default jika kosong)</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Daftar Toko -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-store"></i> Daftar Toko yang Tersedia</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>ID Toko</th>
                                                        <th>Nama Toko</th>
                                                        <th>Jumlah Income</th>
                                                        <th>Dibuat</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($tokos as $toko)
                                                    <tr>
                                                        <td><strong>{{ $toko->id }}</strong></td>
                                                        <td>{{ $toko->nama }}</td>
                                                        <td>{{ $toko->incomes->count() }}</td>
                                                        <td>{{ $toko->created_at->format('d/m/Y') }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle"></i>
                                            Gunakan ID toko di atas pada kolom "Toko ID" di file Excel
                                        </small>
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
                                            <label for="file" class="form-label">Pilih File Excel <span class="text-danger">*</span></label>
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
                                            <label for="default_toko_id" class="form-label">Default Toko (Opsional)</label>
                                            <select class="form-control @error('default_toko_id') is-invalid @enderror"
                                                id="default_toko_id" name="default_toko_id">
                                                <option value="">Pilih Default Toko</option>
                                                @foreach($tokos as $toko)
                                                    <option value="{{ $toko->id }}"
                                                        {{ old('default_toko_id') == $toko->id ? 'selected' : '' }}>
                                                        {{ $toko->nama }} (ID: {{ $toko->id }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('default_toko_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">
                                                Jika kolom "Toko ID" kosong di Excel, akan menggunakan toko ini
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
                                <p class="mb-2">{{ session('success') ?? 'Proses import selesai dengan beberapa kegagalan.' }}</p>
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
                                                    <th>Toko ID</th>
                                                    <th>Alasan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach(session('failures') as $failure)
                                                <tr>
                                                    <td>{{ $failure['row'] }}</td>
                                                    <td>{{ $failure['no_pesanan'] }}</td>
                                                    <td>{{ $failure['toko_id'] ?? '-' }}</td>
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
</x-app-layout>
