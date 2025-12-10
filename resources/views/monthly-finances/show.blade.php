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
                                            <i class="fas fa-shopping-cart"></i>
                                        </div>
                                        <h6 class="card-title text-primary">Pendapatan Shopee</h6>
                                        <h4 class="mb-0">Rp {{ number_format($monthlyFinance->total_pendapatan_shopee, 0, ',', '.') }}</h4>
                                        <small class="text-muted">
                                            AOV: Rp {{ number_format($calculatedData['aov_shopee'] ?? 0, 0, ',', '.') }}
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card border-info h-100">
                                    <div class="card-body text-center">
                                        <div class="fs-2 text-info mb-2">
                                            <i class="fas fa-shopping-cart"></i>
                                        </div>
                                        <h6 class="card-title text-info">Pendapatan Tiktok</h6>
                                        <h4 class="mb-0">Rp {{ number_format($monthlyFinance->total_pendapatan_tiktok, 0, ',', '.') }}</h4>
                                        <small class="text-muted">
                                            AOV: Rp {{ number_format($calculatedData['aov_tiktok'] ?? 0, 0, ',', '.') }}
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card border-success h-100">
                                    <div class="card-body text-center">
                                        <div class="fs-2 text-success mb-2">
                                            <i class="fas fa-chart-line"></i>
                                        </div>
                                        <h6 class="card-title text-success">Total Penghasilan</h6>
                                        <h4 class="mb-0">Rp {{ number_format($calculatedData['total_penghasilan'] ?? 0, 0, ',', '.') }}</h4>
                                        <small class="text-muted">
                                            Dari summary
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card border-{{ $calculatedData['laba_rugi'] >= 0 ? 'success' : 'danger' }} h-100">
                                    <div class="card-body text-center">
                                        <div class="fs-2 text-{{ $calculatedData['laba_rugi'] >= 0 ? 'success' : 'danger' }} mb-2">
                                            <i class="fas {{ $calculatedData['laba_rugi'] >= 0 ? 'fa-trophy' : 'fa-exclamation-triangle' }}"></i>
                                        </div>
                                        <h6 class="card-title text-{{ $calculatedData['laba_rugi'] >= 0 ? 'success' : 'danger' }}">Laba/Rugi Net</h6>
                                        <h4 class="mb-0">Rp {{ number_format($calculatedData['laba_rugi'] ?? 0, 0, ',', '.') }}</h4>
                                        <small class="text-muted">{{ number_format($calculatedData['rasio_laba'] ?? 0, 2) }}%</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Utama -->
                        <div class="row mb-4">
                            <div class="col-md-4">
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

                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Pendapatan & Rasio</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <th width="60%">Total Pendapatan</th>
                                                <td>Rp {{ number_format($calculatedData['total_pendapatan'] ?? 0, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Operasional</th>
                                                <td>Rp {{ number_format($monthlyFinance->operasional, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Rasio Admin</th>
                                                <td>{{ number_format($calculatedData['rasio_admin_layanan'] ?? 0, 2) }}%</td>
                                            </tr>
                                            <tr>
                                                <th>Rasio Operasional</th>
                                                <td>{{ number_format($calculatedData['rasio_operasional'] ?? 0, 2) }}%</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-ad me-2"></i>Biaya Iklan</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <th width="60%">Iklan Shopee</th>
                                                <td>Rp {{ number_format($monthlyFinance->iklan_shopee, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Iklan Tiktok</th>
                                                <td>Rp {{ number_format($monthlyFinance->iklan_tiktok, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Total Iklan</th>
                                                <td>Rp {{ number_format($calculatedData['total_iklan'] ?? 0, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Rasio Iklan</th>
                                                <td>{{ number_format($monthlyFinance->total_pendapatan > 0 ? ($calculatedData['total_iklan'] ?? 0) / $monthlyFinance->total_pendapatan * 100 : 0, 2) }}%</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detail per Marketplace -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Shopee</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <th width="60%">Pendapatan</th>
                                                <td>Rp {{ number_format($monthlyFinance->total_pendapatan_shopee, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Rasio Admin</th>
                                                <td>{{ number_format($monthlyFinance->rasio_admin_layanan_shopee, 2) }}%</td>
                                            </tr>
                                            <tr>
                                                <th>Iklan</th>
                                                <td>Rp {{ number_format($monthlyFinance->iklan_shopee, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <th>ROAS</th>
                                                <td><span class="badge bg-success">{{ number_format($calculatedData['roas_shopee'] ?? 0, 2, ',', '.') }}%</span></td>
                                            </tr>
                                            <tr>
                                                <th>ACOS</th>
                                                <td><span class="badge bg-warning">{{ number_format($calculatedData['acos_shopee'] ?? 0, 2) }}%</span></td>
                                            </tr>
                                            <tr>
                                                <th>Laba/Rugi Net</th>
                                                <td class="{{ $calculatedData['laba_rugi_net_shopee'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                    Rp {{ number_format($calculatedData['laba_rugi_net_shopee'] ?? 0, 0, ',', '.') }}
                                                    ({{ number_format($calculatedData['rasio_laba_net_shopee'] ?? 0, 2) }}%)
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0"><i class="fab fa-tiktok me-2"></i>Tiktok</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <th width="60%">Pendapatan</th>
                                                <td>Rp {{ number_format($monthlyFinance->total_pendapatan_tiktok, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Rasio Admin</th>
                                                <td>{{ number_format($monthlyFinance->rasio_admin_layanan_tiktok, 2) }}%</td>
                                            </tr>
                                            <tr>
                                                <th>Iklan</th>
                                                <td>Rp {{ number_format($monthlyFinance->iklan_tiktok, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <th>ROAS</th>
                                                <td><span class="badge bg-success">{{ number_format($calculatedData['roas_tiktok'] ?? 0) }}%</span></td>
                                            </tr>
                                            <tr>
                                                <th>ACOS</th>
                                                <td><span class="badge bg-warning">{{ number_format($calculatedData['acos_tiktok'] ?? 0, 2) }}%</span></td>
                                            </tr>
                                            <tr>
                                                <th>Laba/Rugi Net</th>
                                                <td class="{{ $calculatedData['laba_rugi_net_tiktok'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                    Rp {{ number_format($calculatedData['laba_rugi_net_tiktok'] ?? 0, 0, ',', '.') }}
                                                    ({{ number_format($calculatedData['rasio_laba_net_tiktok'] ?? 0, 2) }}%)
                                                </td>
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
                                                <th width="60%">HPP</th>
                                                <td>Rp {{ number_format($calculatedData['hpp'] ?? 0, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Laba/Rugi Kotor</th>
                                                <td>Rp {{ number_format($calculatedData['laba_rugi_kotor'] ?? 0, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Rasio Margin Kotor</th>
                                                <td><span class="badge bg-info">{{ number_format($calculatedData['rasio_margin_kotor'] ?? 0, 2) }}%</span></td>
                                            </tr>
                                            <tr>
                                                <th>Basket Size Aktual</th>
                                                <td>{{ number_format($calculatedData['basket_size_aktual'] ?? 0, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <th>Total Orders</th>
                                                <td>{{ number_format($calculatedData['total_order_qty'] ?? 0, 0, ',', '.') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Metrics Perbandingan</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <th width="60%">Status</th>
                                                <td>
                                                    @if($calculatedData['laba_rugi'] > 0)
                                                        <span class="badge bg-success">Profit</span>
                                                    @elseif($calculatedData['laba_rugi'] < 0)
                                                        <span class="badge bg-danger">Rugi</span>
                                                    @else
                                                        <span class="badge bg-warning">Break Even</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Total Penghasilan Shopee</th>
                                                <td>Rp {{ number_format($calculatedData['total_penghasilan_shopee'] ?? 0, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Total Penghasilan Tiktok</th>
                                                <td>Rp {{ number_format($calculatedData['total_penghasilan_tiktok'] ?? 0, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Order Shopee</th>
                                                <td>{{ number_format($calculatedData['total_order_shopee'] ?? 0, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Order Tiktok</th>
                                                <td>{{ number_format($calculatedData['total_order_tiktok'] ?? 0, 0, ',', '.') }}</td>
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
