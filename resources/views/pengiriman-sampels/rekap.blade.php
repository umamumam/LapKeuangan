<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Rekap Pengiriman Sampel</h5>
                        <a href="{{ route('pengiriman-sampels.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <!-- Filter Bulan -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <form method="GET" action="{{ route('pengiriman-sampels.rekap') }}" class="d-flex gap-2">
                                    <div class="flex-grow-1">
                                        <label for="bulan" class="form-label">Pilih Bulan</label>
                                        <input type="month" class="form-control" id="bulan" name="bulan"
                                               value="{{ $bulan }}" onchange="this.form.submit()">
                                    </div>
                                    <div class="d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-filter"></i> Filter
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6 text-end">
                                <div class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    Periode: {{ \Carbon\Carbon::parse($bulan)->translatedFormat('F Y') }}
                                </div>
                            </div>
                        </div>

                        <!-- Statistik Ringkas -->
                        <div class="row mb-4">
                            <div class="col-xl-2 col-md-4 col-sm-6">
                                <div class="card border-primary mb-2">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4 class="mb-0 text-primary">{{ $totalPengiriman }}</h4>
                                                <small class="text-muted">Total Pengiriman</small>
                                            </div>
                                            <div class="align-self-center text-primary">
                                                <i class="fas fa-shipping-fast fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-2 col-md-4 col-sm-6">
                                <div class="card border-success mb-2">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4 class="mb-0 text-success">{{ $totalJumlah }}</h4>
                                                <small class="text-muted">Total Jumlah</small>
                                            </div>
                                            <div class="align-self-center text-success">
                                                <i class="fas fa-boxes fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-2 col-md-4 col-sm-6">
                                <div class="card border-info mb-2">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4 class="mb-0 text-info">Rp {{ number_format($totalHpp, 0, ',', '.') }}</h4>
                                                <small class="text-muted">Total HPP</small>
                                            </div>
                                            <div class="align-self-center text-info">
                                                <i class="fas fa-money-bill-wave fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-2 col-md-4 col-sm-6">
                                <div class="card border-warning mb-2">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4 class="mb-0 text-warning">Rp {{ number_format($totalOngkir, 0, ',', '.') }}</h4>
                                                <small class="text-muted">Total Ongkir</small>
                                            </div>
                                            <div class="align-self-center text-warning">
                                                <i class="fas fa-truck fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-2 col-md-4 col-sm-6">
                                <div class="card border-danger mb-2">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4 class="mb-0 text-danger">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</h4>
                                                <small class="text-muted">Total Biaya</small>
                                            </div>
                                            <div class="align-self-center text-danger">
                                                <i class="fas fa-calculator fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Navigation -->
                        <ul class="nav nav-tabs mb-4" id="rekapTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="sampel-tab" data-bs-toggle="tab" data-bs-target="#sampel" type="button" role="tab">
                                    <i class="fas fa-cube me-1"></i> Per Sampel
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="detail-tab" data-bs-toggle="tab" data-bs-target="#detail" type="button" role="tab">
                                    <i class="fas fa-list me-1"></i> Detail
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="user-tab" data-bs-toggle="tab" data-bs-target="#user" type="button" role="tab">
                                    <i class="fas fa-users me-1"></i> Per User
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content" id="rekapTabContent">

                            <!-- Tab 1: Rekap per Sampel -->
                            <div class="tab-pane fade show active" id="sampel" role="tabpanel">
                                @if($rekapPerSampel->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>#</th>
                                                <th>Sampel</th>
                                                <th>Ukuran</th>
                                                <th>Harga</th>
                                                <th>Jumlah Kirim</th>
                                                <th>Total Jumlah</th>
                                                <th>Total HPP</th>
                                                <th>Total Ongkir</th>
                                                <th>Total Biaya</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($rekapPerSampel as $sampel)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <span class="badge bg-info">{{ $sampel['nama_sampel'] }}</span>
                                                </td>
                                                <td>{{ $sampel['ukuran'] }}</td>
                                                <td>Rp {{ number_format($sampel['harga'], 0, ',', '.') }}</td>
                                                <td>
                                                    <span class="badge bg-primary">{{ $sampel['jumlah_pengiriman'] }}</span>
                                                </td>
                                                <td>{{ $sampel['total_jumlah'] }}</td>
                                                <td>Rp {{ number_format($sampel['total_hpp'], 0, ',', '.') }}</td>
                                                <td>Rp {{ number_format($sampel['total_ongkir'], 0, ',', '.') }}</td>
                                                <td><strong>Rp {{ number_format($sampel['total_biaya'], 0, ',', '.') }}</strong></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="table-secondary">
                                            <tr>
                                                <th colspan="4" class="text-end">Total:</th>
                                                <th>{{ $rekapPerSampel->sum('jumlah_pengiriman') }}</th>
                                                <th>{{ $rekapPerSampel->sum('total_jumlah') }}</th>
                                                <th>Rp {{ number_format($rekapPerSampel->sum('total_hpp'), 0, ',', '.') }}</th>
                                                <th>Rp {{ number_format($rekapPerSampel->sum('total_ongkir'), 0, ',', '.') }}</th>
                                                <th>Rp {{ number_format($rekapPerSampel->sum('total_biaya'), 0, ',', '.') }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                @else
                                <div class="text-center py-4">
                                    <i class="fas fa-cube fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Tidak ada data rekap sampel</p>
                                </div>
                                @endif
                            </div>

                            <!-- Tab 2: Detail Pengiriman -->
                            <div class="tab-pane fade" id="detail" role="tabpanel">
                                @if($rekapData->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>#</th>
                                                <th>Tanggal</th>
                                                <th>No. Resi</th>
                                                <th>Penerima</th>
                                                <th>Sampel</th>
                                                <th>Jumlah</th>
                                                <th>HPP</th>
                                                <th>Ongkir</th>
                                                <th>Total Biaya</th>
                                                <th>Username</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($rekapData as $pengiriman)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $pengiriman->tanggal->format('d/m/Y') }}</td>
                                                <td>{{ $pengiriman->no_resi }}</td>
                                                <td>{{ $pengiriman->penerima }}</td>
                                                <td>
                                                    <span class="badge bg-info">{{ $pengiriman->sampel->nama }}</span>
                                                    <small class="d-block text-muted">{{ $pengiriman->sampel->ukuran }}</small>
                                                </td>
                                                <td>{{ $pengiriman->jumlah }}</td>
                                                <td>Rp {{ number_format($pengiriman->totalhpp, 0, ',', '.') }}</td>
                                                <td>Rp {{ number_format($pengiriman->ongkir, 0, ',', '.') }}</td>
                                                <td><strong>Rp {{ number_format($pengiriman->total_biaya, 0, ',', '.') }}</strong></td>
                                                <td>{{ $pengiriman->username }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="table-secondary">
                                            <tr>
                                                <th colspan="5" class="text-end">Total:</th>
                                                <th>{{ $totalJumlah }}</th>
                                                <th>Rp {{ number_format($totalHpp, 0, ',', '.') }}</th>
                                                <th>Rp {{ number_format($totalOngkir, 0, ',', '.') }}</th>
                                                <th>Rp {{ number_format($totalBiaya, 0, ',', '.') }}</th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                @else
                                <div class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Tidak ada data pengiriman untuk periode {{ \Carbon\Carbon::parse($bulan)->translatedFormat('F Y') }}</p>
                                </div>
                                @endif
                            </div>

                            <!-- Tab 3: Rekap per User -->
                            <div class="tab-pane fade" id="user" role="tabpanel">
                                @if($rekapPerUser->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>#</th>
                                                <th>Username</th>
                                                <th>Jumlah Kirim</th>
                                                <th>Total Jumlah</th>
                                                <th>Total HPP</th>
                                                <th>Total Ongkir</th>
                                                <th>Total Biaya</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($rekapPerUser as $user)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $user['username'] }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary">{{ $user['jumlah_pengiriman'] }}</span>
                                                </td>
                                                <td>{{ $user['total_jumlah'] }}</td>
                                                <td>Rp {{ number_format($user['total_hpp'], 0, ',', '.') }}</td>
                                                <td>Rp {{ number_format($user['total_ongkir'], 0, ',', '.') }}</td>
                                                <td><strong>Rp {{ number_format($user['total_biaya'], 0, ',', '.') }}</strong></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="table-secondary">
                                            <tr>
                                                <th colspan="2" class="text-end">Total:</th>
                                                <th>{{ $rekapPerUser->sum('jumlah_pengiriman') }}</th>
                                                <th>{{ $rekapPerUser->sum('total_jumlah') }}</th>
                                                <th>Rp {{ number_format($rekapPerUser->sum('total_hpp'), 0, ',', '.') }}</th>
                                                <th>Rp {{ number_format($rekapPerUser->sum('total_ongkir'), 0, ',', '.') }}</th>
                                                <th>Rp {{ number_format($rekapPerUser->sum('total_biaya'), 0, ',', '.') }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                @else
                                <div class="text-center py-4">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Tidak ada data rekap user</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
