<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Hasil Analisis Income</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('incomes.export-hasil') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-download"></i> Export Excel
                            </a>
                            <a href="{{ route('incomes.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Kembali ke Income
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Summary Cards -->
                        <div class="row mb-4">
                            @php
                                $totalPenghasilan = $incomes->sum('total_penghasilan');
                                $totalHpp = $incomes->sum('total_hpp');
                                $totalLaba = $incomes->sum('laba');
                                $totalPersentase = $totalPenghasilan > 0 ? ($totalLaba / $totalPenghasilan) * 100 : 0;
                            @endphp

                            <div class="col-md-3">
                                <div class="card border-primary h-100">
                                    <div class="card-body text-center">
                                        <div class="fs-2 text-primary mb-2">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </div>
                                        <h6 class="card-title text-primary">Total Penghasilan</h6>
                                        <h4 class="mb-0">Rp {{ number_format($totalPenghasilan, 0, ',', '.') }}</h4>
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
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card border-{{ $totalLaba >= 0 ? 'success' : 'danger' }} h-100">
                                    <div class="card-body text-center">
                                        <div class="fs-2 text-{{ $totalLaba >= 0 ? 'success' : 'danger' }} mb-2">
                                            <i class="fas {{ $totalLaba >= 0 ? 'fa-chart-line' : 'fa-chart-bar' }}"></i>
                                        </div>
                                        <h6 class="card-title text-{{ $totalLaba >= 0 ? 'success' : 'danger' }}">Total Laba/Rugi</h6>
                                        <h4 class="mb-0">Rp {{ number_format($totalLaba, 0, ',', '.') }}</h4>
                                        <small class="text-muted">{{ number_format($totalPersentase, 1) }}%</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card border-secondary h-100">
                                    <div class="card-body text-center">
                                        <div class="fs-2 text-secondary mb-2">
                                            <i class="fas fa-list"></i>
                                        </div>
                                        <h6 class="card-title text-secondary">Jumlah Data</h6>
                                        <h4 class="mb-0">{{ $incomes->count() }}</h4>
                                        <small class="text-muted">Pesanan</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Table Hasil -->
                        <div class="table-responsive">
                            <table id="res-config" class="display table table-striped table-hover dt-responsive nowrap" style="width: 100%">
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
                                                <span class="badge bg-{{ $income->persentase_laba >= 0 ? 'info' : 'warning' }}">
                                                    {{ number_format($income->persentase_laba, 1) }}%
                                                </span>
                                            </td>
                                            <td>{{ $income->nama_toko }}</td>
                                            <td>{{ $income->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('incomes.show', $income->id) }}"
                                                   class="btn btn-info btn-sm"
                                                   title="Lihat Detail">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
