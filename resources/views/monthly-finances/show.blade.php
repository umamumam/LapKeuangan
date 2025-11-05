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
                        <h5 class="mb-0"><i class="fas fa-eye"></i> Detail Keuangan - {{ $monthlyFinance->nama_periode }}</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('monthly-finances.calculate', $monthlyFinance->id) }}"
                               class="btn btn-warning btn-sm"
                               onclick="return confirm('Hitung ulang data dari database?')">
                                <i class="fas fa-calculator"></i> Hitung Ulang
                            </a>
                            <a href="{{ route('monthly-finances.edit', $monthlyFinance->id) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('monthly-finances.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Card Ringkasan -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card border-primary h-100">
                                    <div class="card-body text-center">
                                        <div class="fs-2 text-primary mb-2">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </div>
                                        <h6 class="card-title text-primary">Total Pendapatan</h6>
                                        <h4 class="mb-0">Rp {{ number_format($monthlyFinance->total_pendapatan, 0, ',', '.') }}</h4>
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
                                        <h4 class="mb-0">Rp {{ number_format($monthlyFinance->total_penghasilan, 0, ',', '.') }}</h4>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card border-success h-100">
                                    <div class="card-body text-center">
                                        <div class="fs-2 text-success mb-2">
                                            <i class="fas fa-cubes"></i>
                                        </div>
                                        <h6 class="card-title text-success">HPP</h6>
                                        <h4 class="mb-0">Rp {{ number_format($monthlyFinance->hpp, 0, ',', '.') }}</h4>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card border-{{ $monthlyFinance->laba_rugi >= 0 ? 'success' : 'danger' }} h-100">
                                    <div class="card-body text-center">
                                        <div class="fs-2 text-{{ $monthlyFinance->laba_rugi >= 0 ? 'success' : 'danger' }} mb-2">
                                            <i class="fas {{ $monthlyFinance->laba_rugi >= 0 ? 'fa-trophy' : 'fa-exclamation-triangle' }}"></i>
                                        </div>
                                        <h6 class="card-title text-{{ $monthlyFinance->laba_rugi >= 0 ? 'success' : 'danger' }}">Laba/Rugi</h6>
                                        <h4 class="mb-0">Rp {{ number_format($monthlyFinance->laba_rugi, 0, ',', '.') }}</h4>
                                        <small class="text-muted">{{ number_format($monthlyFinance->rasio_laba, 2) }}%</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Utama -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Periode</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <th width="40%">Periode</th>
                                                <td>{{ $monthlyFinance->nama_periode }}</td>
                                            </tr>
                                            <tr>
                                                <th>Tanggal Awal</th>
                                                <td>{{ $monthlyFinance->periode_awal->format('d/m/Y') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Tanggal Akhir</th>
                                                <td>{{ $monthlyFinance->periode_akhir->format('d/m/Y') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Keterangan</th>
                                                <td>{{ $monthlyFinance->keterangan ?? '-' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Biaya & Rasio</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <th width="40%">Operasional</th>
                                                <td>Rp {{ number_format($monthlyFinance->operasional, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Iklan</th>
                                                <td>Rp {{ number_format($monthlyFinance->iklan, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Rasio Admin</th>
                                                <td>{{ number_format($monthlyFinance->rasio_admin_layanan, 2) }}%</td>
                                            </tr>
                                            <tr>
                                                <th>Rasio Operasional</th>
                                                <td>{{ number_format($monthlyFinance->rasio_operasional, 2) }}%</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Metrics -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-calculator me-2"></i>Metrics Keuangan</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <th width="60%">Rasio Margin</th>
                                                <td><span class="badge bg-info">{{ number_format($monthlyFinance->rasio_margin, 2) }}%</span></td>
                                            </tr>
                                            <tr>
                                                <th>Rasio Laba</th>
                                                <td><span class="badge bg-{{ $monthlyFinance->rasio_laba >= 0 ? 'success' : 'danger' }}">{{ number_format($monthlyFinance->rasio_laba, 2) }}%</span></td>
                                            </tr>
                                            <tr>
                                                <th>AOV Aktual</th>
                                                <td>Rp {{ number_format($monthlyFinance->aov_aktual, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Basket Size Aktual</th>
                                                <td>{{ number_format($monthlyFinance->basket_size_aktual, 2) }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Metrics Iklan</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <th width="60%">ROAS Aktual</th>
                                                <td><span class="badge bg-success">{{ number_format($monthlyFinance->roas_aktual, 2) }}%</span></td>
                                            </tr>
                                            <tr>
                                                <th>ACOS Aktual</th>
                                                <td><span class="badge bg-warning">{{ number_format($monthlyFinance->acos_aktual, 2) }}%</span></td>
                                            </tr>
                                            <tr>
                                                <th>Status</th>
                                                <td>
                                                    @if($monthlyFinance->laba_rugi > 0)
                                                        <span class="badge bg-success">Profit</span>
                                                    @elseif($monthlyFinance->laba_rugi < 0)
                                                        <span class="badge bg-danger">Rugi</span>
                                                    @else
                                                        <span class="badge bg-warning">Break Even</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Diupdate</th>
                                                <td>{{ $monthlyFinance->updated_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
