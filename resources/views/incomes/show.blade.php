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
                        <h5 class="mb-0"><i class="fas fa-eye"></i> Detail Income</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('incomes.edit', $income->id) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('incomes.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Card Ringkasan Keuangan -->
                        <div class="row mb-4">
                            @php
                                // Hitung total HPP dari semua order
                                $totalHpp = 0;
                                foreach ($income->orders as $order) {
                                    $netQuantity = $order->jumlah - $order->returned_quantity;
                                    $totalHpp += $netQuantity * $order->produk->hpp_produk;
                                }

                                // Hitung laba
                                $laba = $income->total_penghasilan - $totalHpp;

                                // Hitung persentase laba
                                $persentaseLaba = $income->total_penghasilan > 0 ? ($laba / $income->total_penghasilan) * 100 : 0;
                            @endphp

                            <div class="col-md-3">
                                <div class="card border-primary h-100">
                                    <div class="card-body text-center">
                                        <div class="fs-2 text-primary mb-2">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </div>
                                        <h6 class="card-title text-primary">Total Penghasilan</h6>
                                        <h4 class="mb-0">Rp {{ number_format($income->total_penghasilan, 0, ',', '.') }}</h4>
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
                                <div class="card border-{{ $laba >= 0 ? 'success' : 'danger' }} h-100">
                                    <div class="card-body text-center">
                                        <div class="fs-2 text-{{ $laba >= 0 ? 'success' : 'danger' }} mb-2">
                                            <i class="fas {{ $laba >= 0 ? 'fa-chart-line' : 'fa-chart-bar' }}"></i>
                                        </div>
                                        <h6 class="card-title text-{{ $laba >= 0 ? 'success' : 'danger' }}">Laba / Rugi</h6>
                                        <h4 class="mb-0">Rp {{ number_format($laba, 0, ',', '.') }}</h4>
                                        <small class="text-muted">
                                            {{ number_format($persentaseLaba, 1) }}%
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="card border-secondary h-100">
                                    <div class="card-body text-center">
                                        <div class="fs-2 text-secondary mb-2">
                                            <i class="fas fa-boxes"></i>
                                        </div>
                                        <h6 class="card-title text-secondary">Jumlah Item</h6>
                                        <h4 class="mb-0">{{ $income->orders->count() }}</h4>
                                        <small class="text-muted">Produk</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Detail -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Income</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <th width="40%">No Pesanan</th>
                                                <td>{{ $income->no_pesanan }}</td>
                                            </tr>
                                            <tr>
                                                <th>No Pengajuan</th>
                                                <td>{{ $income->no_pengajuan ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Status Profit</th>
                                                <td>
                                                    @if($laba > 0)
                                                        <span class="badge bg-success">Profit</span>
                                                    @elseif($laba < 0)
                                                        <span class="badge bg-danger">Rugi</span>
                                                    @else
                                                        <span class="badge bg-warning">Break Even</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Informasi Tambahan</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <th width="40%">Dibuat</th>
                                                <td>{{ $income->created_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Diupdate</th>
                                                <td>{{ $income->updated_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Margin</th>
                                                <td>
                                                    @if($laba > 0)
                                                        <span class="text-success">
                                                            +{{ number_format($persentaseLaba, 1) }}%
                                                        </span>
                                                    @elseif($laba < 0)
                                                        <span class="text-danger">
                                                            {{ number_format($persentaseLaba, 1) }}%
                                                        </span>
                                                    @else
                                                        <span class="text-warning">0%</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detail Order -->
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-list me-2"></i>Detail Order & Perhitungan HPP</h6>
                            </div>
                            <div class="card-body" style="overflow-x:auto;">
                                <table class="display table table-striped table-hover dt-responsive nowrap" style="width: 100%">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Produk</th>
                                            <th>Jumlah</th>
                                            <th>Return</th>
                                            <th>Quantity Bersih</th>
                                            <th>HPP/Unit</th>
                                            <th>Subtotal HPP</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($income->orders as $order)
                                            @php
                                                $netQuantity = $order->jumlah - $order->returned_quantity;
                                                $subtotalHpp = $netQuantity * $order->produk->hpp_produk;
                                            @endphp
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <strong>{{ $order->produk->nama_produk }}</strong>
                                                    @if($order->produk->nama_variasi)
                                                        <br><small class="text-muted">Variasi: {{ $order->produk->nama_variasi }}</small>
                                                    @endif
                                                    <br><small class="text-muted">SKU: {{ $order->produk->sku_induk }}</small>
                                                </td>
                                                <td>{{ $order->jumlah }}</td>
                                                <td>{{ $order->returned_quantity }}</td>
                                                <td>
                                                    <span class="badge bg-primary">{{ $netQuantity }}</span>
                                                </td>
                                                <td>Rp {{ number_format($order->produk->hpp_produk, 0, ',', '.') }}</td>
                                                <td><strong>Rp {{ number_format($subtotalHpp, 0, ',', '.') }}</strong></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="6" class="text-end">Total HPP</th>
                                            <th><strong>Rp {{ number_format($totalHpp, 0, ',', '.') }}</strong></th>
                                        </tr>
                                        <tr>
                                            <th colspan="6" class="text-end">Total Penghasilan</th>
                                            <th><strong class="text-primary">Rp {{ number_format($income->total_penghasilan, 0, ',', '.') }}</strong></th>
                                        </tr>
                                        <tr class="{{ $laba >= 0 ? 'table-success' : 'table-danger' }}">
                                            <th colspan="6" class="text-end">
                                                <strong>LABA / RUGI</strong>
                                                @if($persentaseLaba != 0)
                                                    <br>
                                                    <small>({{ number_format($persentaseLaba, 1) }}%)</small>
                                                @endif
                                            </th>
                                            <th>
                                                <strong class="{{ $laba >= 0 ? 'text-success' : 'text-danger' }}">
                                                    Rp {{ number_format($laba, 0, ',', '.') }}
                                                </strong>
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Summary Cards -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Distribusi Nilai</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span>Total Penghasilan</span>
                                            <strong class="text-primary">Rp {{ number_format($income->total_penghasilan, 0, ',', '.') }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span>Total HPP</span>
                                            <strong class="text-info">Rp {{ number_format($totalHpp, 0, ',', '.') }}</strong>
                                        </div>
                                        @if($income->total_penghasilan > 0)
                                            @php
                                                $hppPercentage = ($totalHpp / $income->total_penghasilan) * 100;
                                                $labaPercentage = ($laba / $income->total_penghasilan) * 100;
                                            @endphp
                                            <div class="progress mb-3" style="height: 25px;">
                                                <div class="progress-bar bg-info" style="width: {{ $hppPercentage }}%">
                                                    <strong>HPP {{ number_format($hppPercentage, 1) }}%</strong>
                                                </div>
                                                <div class="progress-bar bg-success" style="width: {{ $labaPercentage }}%">
                                                    <strong>Laba {{ number_format($labaPercentage, 1) }}%</strong>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Summary</h6>
                                    </div>
                                    <div class="card-body text-center d-flex flex-column justify-content-center">
                                        @if($laba > 0)
                                            <i class="fas fa-trophy text-success fs-1 mb-3"></i>
                                            <h5 class="text-success mb-2">Profit</h5>
                                            <p class="text-muted mb-2">Anda mendapatkan laba sebesar</p>
                                            <h4 class="text-success mb-2">Rp {{ number_format($laba, 0, ',', '.') }}</h4>
                                            <small class="text-muted">{{ number_format($persentaseLaba, 1) }}% dari total penghasilan</small>
                                        @elseif($laba < 0)
                                            <i class="fas fa-exclamation-triangle text-danger fs-1 mb-3"></i>
                                            <h5 class="text-danger mb-2">Rugi</h5>
                                            <p class="text-muted mb-2">Anda mengalami kerugian sebesar</p>
                                            <h4 class="text-danger mb-2">Rp {{ number_format(abs($laba), 0, ',', '.') }}</h4>
                                            <small class="text-muted">{{ number_format(abs($persentaseLaba), 1) }}% dari total penghasilan</small>
                                        @else
                                            <i class="fas fa-balance-scale text-warning fs-1 mb-3"></i>
                                            <h5 class="text-warning mb-2">Break Even</h5>
                                            <p class="text-muted mb-2">Tidak ada laba atau rugi</p>
                                            <h4 class="text-warning mb-2">Rp 0</h4>
                                            <small class="text-muted">Pendapatan sama dengan HPP</small>
                                        @endif
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
