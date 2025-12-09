<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Hasil Analisis Income</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('incomes.export-hasil') }}?{{ http_build_query(request()->query()) }}"
                                class="btn btn-success btn-sm">
                                <i class="fas fa-download"></i> Export Excel
                            </a>
                            <a href="{{ route('incomes.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Kembali ke Income
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filter Section -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-filter"></i> Filter Data</h6>
                            </div>
                            <div class="card-body">
                                <form method="GET" action="{{ route('incomes.detailhasil') }}">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="toko_id" class="form-label">Filter Toko</label>
                                                <select class="form-control" id="toko_id" name="toko_id">
                                                    <option value="">Semua Toko</option>
                                                    @foreach($tokos as $toko)
                                                    <option value="{{ $toko->id }}" {{ request('toko_id')==$toko->id ?
                                                        'selected' : '' }}>
                                                        {{ $toko->nama }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="start_date" class="form-label">Tanggal Mulai</label>
                                                <input type="date" class="form-control" id="start_date"
                                                    name="start_date"
                                                    value="{{ request('start_date', $startDate ?? '') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="end_date" class="form-label">Tanggal Akhir</label>
                                                <input type="date" class="form-control" id="end_date" name="end_date"
                                                    value="{{ request('end_date', $endDate ?? '') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="mb-3">
                                                <label class="form-label">&nbsp;</label>
                                                <div class="d-grid gap-2">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-search"></i> Filter
                                                    </button>
                                                    <a href="{{ route('incomes.hasil') }}" class="btn btn-secondary">
                                                        <i class="fas fa-refresh"></i> Reset
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Summary Cards -->
                        <div class="row mb-4">
                            @php
                            $totalPenghasilan = $incomes->sum('total_penghasilan');
                            $totalHpp = $incomes->sum('total_hpp');
                            $totalLaba = $incomes->sum('laba');
                            $totalPersentase = $totalPenghasilan > 0 ? ($totalLaba / $totalPenghasilan) * 100 : 0;

                            // Info filter aktif
                            $filterAktif = [];
                            if (request('toko_id')) {
                            $tokoTerpilih = $tokos->firstWhere('id', request('toko_id'));
                            $filterAktif[] = 'Toko: ' . ($tokoTerpilih->nama ?? 'Tidak Ditemukan');
                            }
                            if (request('start_date')) {
                            $filterAktif[] = 'Dari: ' . \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y');
                            }
                            if (request('end_date')) {
                            $filterAktif[] = 'Sampai: ' . \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y');
                            }
                            @endphp

                            @if(count($filterAktif) > 0)
                            <div class="col-12 mb-3">
                                <div class="alert alert-info py-2">
                                    <small>
                                        <i class="fas fa-info-circle"></i>
                                        Filter aktif: {{ implode(' | ', $filterAktif) }}
                                    </small>
                                </div>
                            </div>
                            @endif

                            <div class="col-md-3">
                                <div class="card border-primary h-100">
                                    <div class="card-body text-center">
                                        <div class="fs-2 text-primary mb-2">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </div>
                                        <h6 class="card-title text-primary">Total Penghasilan</h6>
                                        <h4 class="mb-0">Rp {{ number_format($totalPenghasilan, 0, ',', '.') }}</h4>
                                        <small class="text-muted">{{ $incomes->count() }} pesanan</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card border-info h-100">
                                    <div class="card-body text-center">
                                        <div class="fs-2 text-info mb-2">
                                            <i class="fas fa-cubes"></i>
                                        </div>
                                        <h6 class="card-title text-info">Total HPP</h6>
                                        <h4 class="mb-0">Rp {{ number_format($totalHpp, 0, ',', '.') }}</h4>
                                        <small class="text-muted">{{ number_format($totalHpp > 0 ? ($totalHpp /
                                            $totalPenghasilan) * 100 : 0, 1) }}% dari penghasilan</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card border-{{ $totalLaba >= 0 ? 'success' : 'danger' }} h-100">
                                    <div class="card-body text-center">
                                        <div class="fs-2 text-{{ $totalLaba >= 0 ? 'success' : 'danger' }} mb-2">
                                            <i class="fas {{ $totalLaba >= 0 ? 'fa-chart-line' : 'fa-chart-bar' }}"></i>
                                        </div>
                                        <h6 class="card-title text-{{ $totalLaba >= 0 ? 'success' : 'danger' }}">Total
                                            Laba/Rugi</h6>
                                        <h4 class="mb-0">Rp {{ number_format($totalLaba, 0, ',', '.') }}</h4>
                                        <small class="text-muted">{{ number_format($totalPersentase, 1) }}%</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card border-secondary h-100">
                                    <div class="card-body text-center">
                                        <div class="fs-2 text-secondary mb-2">
                                            <i class="fas fa-store"></i>
                                        </div>
                                        <h6 class="card-title text-secondary">Jumlah Toko</h6>
                                        <h4 class="mb-0">{{ $incomes->pluck('toko_id')->unique()->count() }}</h4>
                                        <small class="text-muted">Toko aktif</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Table Hasil -->
                        <div class="table-responsive">
                            <table id="res-config" class="display table table-striped table-hover dt-responsive nowrap"
                                style="width: 100%">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>No Pesanan</th>
                                        <th>No Pengajuan</th>
                                        <th>Total Penghasilan</th>
                                        <th>HPP</th>
                                        <th>Laba/Rugi</th>
                                        <th>Persentase</th>
                                        <th>Toko</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($incomes as $income)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $income->no_pesanan }}</strong>
                                        </td>
                                        <td>{{ $income->no_pengajuan ?? '-' }}</td>
                                        <td>Rp {{ number_format($income->total_penghasilan, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($income->total_hpp, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $income->laba >= 0 ? 'success' : 'danger' }}">
                                                Rp {{ number_format($income->laba, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $income->persentase_laba >= 0 ? 'info' : 'warning' }}">
                                                {{ number_format($income->persentase_laba, 1) }}%
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">
                                                {{ $income->toko->nama ?? 'Tidak Ditemukan' }}
                                            </span>
                                        </td>
                                        <td>{{ $income->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('incomes.show', $income->id) }}"
                                                class="btn btn-info btn-sm" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-secondary">
                                    <tr>
                                        <th colspan="3" class="text-end">TOTAL:</th>
                                        <th>Rp {{ number_format($totalPenghasilan, 0, ',', '.') }}</th>
                                        <th>Rp {{ number_format($totalHpp, 0, ',', '.') }}</th>
                                        <th>
                                            <span class="badge bg-{{ $totalLaba >= 0 ? 'success' : 'danger' }}">
                                                Rp {{ number_format($totalLaba, 0, ',', '.') }}
                                            </span>
                                        </th>
                                        <th>
                                            <span class="badge bg-{{ $totalPersentase >= 0 ? 'info' : 'warning' }}">
                                                {{ number_format($totalPersentase, 1) }}%
                                            </span>
                                        </th>
                                        <th colspan="3"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        @if($incomes->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Tidak ada data</h5>
                            <p class="text-muted">Tidak ditemukan data income dengan filter yang dipilih.</p>
                            <a href="{{ route('incomes.hasil') }}" class="btn btn-primary">
                                <i class="fas fa-refresh"></i> Tampilkan Semua Data
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
