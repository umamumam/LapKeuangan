<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Rekap Keuangan</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('monthly-finances.export') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-download"></i> Export Excel
                            </a>
                            <a href="{{ route('monthly-finances.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card border-primary h-100">
                                    <div class="card-body text-center">
                                        <div class="fs-2 text-primary mb-2">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </div>
                                        <h6 class="card-title text-primary">Total Pendapatan</h6>
                                        <h4 class="mb-0">Rp {{ number_format($totals['total_pendapatan'], 0, ',', '.') }}</h4>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card border-info h-100">
                                    <div class="card-body text-center">
                                        <div class="fs-2 text-info mb-2">
                                            <i class="fas fa-chart-line"></i>
                                        </div>
                                        <h6 class="card-title text-info">Total Penghasilan</h6>
                                        <h4 class="mb-0">Rp {{ number_format($totals['total_penghasilan'], 0, ',', '.') }}</h4>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card border-success h-100">
                                    <div class="card-body text-center">
                                        <div class="fs-2 text-success mb-2">
                                            <i class="fas fa-cubes"></i>
                                        </div>
                                        <h6 class="card-title text-success">Total HPP</h6>
                                        <h4 class="mb-0">Rp {{ number_format($totals['hpp'], 0, ',', '.') }}</h4>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card border-{{ $totals['laba_rugi'] >= 0 ? 'success' : 'danger' }} h-100">
                                    <div class="card-body text-center">
                                        <div class="fs-2 text-{{ $totals['laba_rugi'] >= 0 ? 'success' : 'danger' }} mb-2">
                                            <i class="fas {{ $totals['laba_rugi'] >= 0 ? 'fa-trophy' : 'fa-exclamation-triangle' }}"></i>
                                        </div>
                                        <h6 class="card-title text-{{ $totals['laba_rugi'] >= 0 ? 'success' : 'danger' }}">Total Laba/Rugi</h6>
                                        <h4 class="mb-0">Rp {{ number_format($totals['laba_rugi'], 0, ',', '.') }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Rata-rata Rasio -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <h6 class="card-title text-info">Rata-rata Rasio Admin</h6>
                                        <h3 class="text-info">{{ number_format($totals['rata_rata_rasio_admin'], 2) }}%</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <h6 class="card-title text-warning">Rata-rata Rasio Operasional</h6>
                                        <h3 class="text-warning">{{ number_format($totals['rata_rata_rasio_operasional'], 2) }}%</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <h6 class="card-title text-{{ $totals['rata_rata_rasio_laba'] >= 0 ? 'success' : 'danger' }}">Rata-rata Rasio Laba</h6>
                                        <h3 class="text-{{ $totals['rata_rata_rasio_laba'] >= 0 ? 'success' : 'danger' }}">{{ number_format($totals['rata_rata_rasio_laba'], 2) }}%</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Table Rekap -->
                        <div class="table-responsive">
                            <table class="display table table-striped table-hover dt-responsive nowrap" style="width: 100%">
                                <thead class="table-light">
                                    <tr>
                                        <th>Periode</th>
                                        <th>Pendapatan</th>
                                        <th>Penghasilan</th>
                                        <th>HPP</th>
                                        <th>Operasional</th>
                                        <th>Iklan</th>
                                        <th>Laba/Rugi</th>
                                        <th>Rasio Laba</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($finances as $finance)
                                        <tr>
                                            <td><strong>{{ $finance->nama_periode }}</strong></td>
                                            <td>Rp {{ number_format($finance->total_pendapatan, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($finance->total_penghasilan, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($finance->hpp, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($finance->operasional, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($finance->iklan, 0, ',', '.') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $finance->laba_rugi >= 0 ? 'success' : 'danger' }}">
                                                    Rp {{ number_format($finance->laba_rugi, 0, ',', '.') }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $finance->rasio_laba >= 0 ? 'info' : 'warning' }}">
                                                    {{ number_format($finance->rasio_laba, 2) }}%
                                                </span>
                                            </td>
                                            <td>
                                                @if($finance->laba_rugi > 0)
                                                    <span class="badge bg-success">Profit</span>
                                                @elseif($finance->laba_rugi < 0)
                                                    <span class="badge bg-danger">Rugi</span>
                                                @else
                                                    <span class="badge bg-warning">Break Even</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-secondary">
                                    <tr>
                                        <th><strong>TOTAL</strong></th>
                                        <th><strong>Rp {{ number_format($totals['total_pendapatan'], 0, ',', '.') }}</strong></th>
                                        <th><strong>Rp {{ number_format($totals['total_penghasilan'], 0, ',', '.') }}</strong></th>
                                        <th><strong>Rp {{ number_format($totals['hpp'], 0, ',', '.') }}</strong></th>
                                        <th><strong>Rp {{ number_format($totals['operasional'], 0, ',', '.') }}</strong></th>
                                        <th><strong>Rp {{ number_format($totals['iklan'], 0, ',', '.') }}</strong></th>
                                        <th>
                                            <strong class="badge bg-{{ $totals['laba_rugi'] >= 0 ? 'success' : 'danger' }}">
                                                Rp {{ number_format($totals['laba_rugi'], 0, ',', '.') }}
                                            </strong>
                                        </th>
                                        <th>
                                            <strong class="badge bg-{{ $totals['rata_rata_rasio_laba'] >= 0 ? 'info' : 'warning' }}">
                                                {{ number_format($totals['rata_rata_rasio_laba'], 2) }}%
                                            </strong>
                                        </th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
