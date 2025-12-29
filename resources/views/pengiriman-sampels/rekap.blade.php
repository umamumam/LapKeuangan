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
                        <!-- Filter -->
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
                            <div class="col-md-6">
                                <form method="GET" action="{{ route('pengiriman-sampels.rekap') }}" class="d-flex gap-2">
                                    <div class="flex-grow-1">
                                        <label for="toko_id" class="form-label">Filter Toko</label>
                                        <select class="form-control" id="toko_id" name="toko_id" onchange="this.form.submit()">
                                            <option value="all">Semua Toko</option>
                                            @foreach($tokoOptions as $id => $nama)
                                                <option value="{{ $id }}" {{ $tokoId == $id ? 'selected' : '' }}>
                                                    {{ $nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @if($tokoId)
                                        <input type="hidden" name="bulan" value="{{ $bulan }}">
                                    @endif
                                </form>
                            </div>
                        </div>

                        <!-- Info Filter Aktif -->
                        <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Periode:</strong>
                                    <span class="badge bg-primary ms-2 me-2">
                                        {{ \Carbon\Carbon::parse($bulan)->translatedFormat('F Y') }}
                                    </span>
                                    @if($tokoId && $tokoId !== 'all')
                                        @php
                                            $selectedToko = collect($tokoOptions)->firstWhere('id', $tokoId) ?? $tokoOptions[$tokoId] ?? '';
                                        @endphp
                                        <span class="badge bg-success ms-2 me-2">
                                            Toko: {{ $selectedToko }}
                                        </span>
                                    @endif
                                    <span class="badge bg-secondary ms-2">
                                        Total Data: {{ $totalPengiriman }}
                                    </span>
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
                                                <h4 class="mb-0 text-success">{{ $totalJumlahSampel }}</h4>
                                                <small class="text-muted">Total Jml Sampel</small>
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
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="toko-tab" data-bs-toggle="tab" data-bs-target="#toko" type="button" role="tab">
                                    <i class="fas fa-store me-1"></i> Per Toko
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content" id="rekapTabContent">

                            <!-- Tab 1: Rekap per Sampel -->
                            <div class="tab-pane fade show active" id="sampel" role="tabpanel">
                                @if(count($rekapPerSampel) > 0)
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
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $totalJumlahKirim = 0;
                                                $totalAllJumlah = 0;
                                                $totalAllHpp = 0;
                                            @endphp
                                            @foreach($rekapPerSampel as $index => $sampel)
                                                @php
                                                    $totalJumlahKirim += $sampel['jumlah_pengiriman'];
                                                    $totalAllJumlah += $sampel['total_jumlah'];
                                                    $totalAllHpp += $sampel['total_hpp'];
                                                @endphp
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
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="table-secondary">
                                            <tr>
                                                <th colspan="4" class="text-end">Total:</th>
                                                <th>{{ $totalJumlahKirim }}</th>
                                                <th>{{ $totalAllJumlah }}</th>
                                                <th>Rp {{ number_format($totalAllHpp, 0, ',', '.') }}</th>
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
                                                <th>Toko</th>
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
                                                <td>
                                                    @if($pengiriman->toko)
                                                        <span class="badge bg-secondary">{{ $pengiriman->toko->nama }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>{{ $pengiriman->no_resi }}</td>
                                                <td>{{ $pengiriman->penerima }}</td>
                                                <td>
                                                    @php
                                                        $sampelDetails = [];
                                                        for ($i = 1; $i <= 5; $i++) {
                                                            $sampel = $pengiriman->{"sampel{$i}"};
                                                            $jumlah = $pengiriman->{"jumlah{$i}"} ?? 0;
                                                            if ($sampel && $jumlah > 0) {
                                                                $sampelDetails[] = $sampel->nama . ' (x' . $jumlah . ')';
                                                            }
                                                        }
                                                    @endphp
                                                    @if(count($sampelDetails) > 0)
                                                        @foreach($sampelDetails as $detail)
                                                            <span class="badge bg-info d-block mb-1">{{ $detail }}</span>
                                                        @endforeach
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $totalJumlah = 0;
                                                        for ($i = 1; $i <= 5; $i++) {
                                                            $totalJumlah += $pengiriman->{"jumlah{$i}"} ?? 0;
                                                        }
                                                    @endphp
                                                    {{ $totalJumlah }}
                                                </td>
                                                <td>Rp {{ number_format($pengiriman->totalhpp, 0, ',', '.') }}</td>
                                                <td>Rp {{ number_format($pengiriman->ongkir, 0, ',', '.') }}</td>
                                                <td><strong>Rp {{ number_format($pengiriman->total_biaya, 0, ',', '.') }}</strong></td>
                                                <td>{{ $pengiriman->username }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="table-secondary">
                                            <tr>
                                                <th colspan="6" class="text-end">Total:</th>
                                                <th>{{ $totalJumlahSampel }}</th>
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
                                @if(count($rekapPerUser) > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>#</th>
                                                <th>Username</th>
                                                <th>Jumlah Kirim</th>
                                                <th>Total Jumlah Sampel</th>
                                                <th>Total HPP</th>
                                                <th>Total Ongkir</th>
                                                <th>Total Biaya</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $totalUserKirim = 0;
                                                $totalUserJumlah = 0;
                                                $totalUserHpp = 0;
                                                $totalUserOngkir = 0;
                                                $totalUserBiaya = 0;
                                            @endphp
                                            @foreach($rekapPerUser as $user)
                                                @php
                                                    $totalUserKirim += $user['jumlah_pengiriman'];
                                                    $totalUserHpp += $user['total_hpp'];
                                                    $totalUserOngkir += $user['total_ongkir'];
                                                    $totalUserBiaya += $user['total_biaya'];
                                                @endphp
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <span class="badge bg-secondary">{{ $user['username'] }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-primary">{{ $user['jumlah_pengiriman'] }}</span>
                                                    </td>
                                                    <td>
                                                        @php
                                                            // Hitung total jumlah sampel untuk user ini dari data rekapData
                                                            $userJumlah = 0;
                                                            foreach ($rekapData as $pengiriman) {
                                                                if ($pengiriman->username === $user['username']) {
                                                                    for ($i = 1; $i <= 5; $i++) {
                                                                        $userJumlah += $pengiriman->{"jumlah{$i}"} ?? 0;
                                                                    }
                                                                }
                                                            }
                                                            $totalUserJumlah += $userJumlah;
                                                        @endphp
                                                        {{ $userJumlah }}
                                                    </td>
                                                    <td>Rp {{ number_format($user['total_hpp'], 0, ',', '.') }}</td>
                                                    <td>Rp {{ number_format($user['total_ongkir'], 0, ',', '.') }}</td>
                                                    <td><strong>Rp {{ number_format($user['total_biaya'], 0, ',', '.') }}</strong></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="table-secondary">
                                            <tr>
                                                <th colspan="2" class="text-end">Total:</th>
                                                <th>{{ $totalUserKirim }}</th>
                                                <th>{{ $totalUserJumlah }}</th>
                                                <th>Rp {{ number_format($totalUserHpp, 0, ',', '.') }}</th>
                                                <th>Rp {{ number_format($totalUserOngkir, 0, ',', '.') }}</th>
                                                <th>Rp {{ number_format($totalUserBiaya, 0, ',', '.') }}</th>
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

                            <!-- Tab 4: Rekap per Toko -->
                            <div class="tab-pane fade" id="toko" role="tabpanel">
                                @if(count($rekapPerToko) > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>#</th>
                                                <th>Toko</th>
                                                <th>Jumlah Pengiriman</th>
                                                <th>Total Jumlah Sampel</th>
                                                <th>Total HPP</th>
                                                <th>Total Ongkir</th>
                                                <th>Total Biaya</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $totalTokoKirim = 0;
                                                $totalTokoJumlah = 0;
                                                $totalTokoHpp = 0;
                                                $totalTokoOngkir = 0;
                                                $totalTokoBiaya = 0;
                                            @endphp
                                            @foreach($rekapPerToko as $toko)
                                                @php
                                                    $totalTokoKirim += $toko['jumlah_pengiriman'];
                                                    $totalTokoHpp += $toko['total_hpp'];
                                                    $totalTokoOngkir += $toko['total_ongkir'];
                                                    $totalTokoBiaya += $toko['total_biaya'];
                                                @endphp
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <span class="badge bg-secondary">{{ $toko['nama_toko'] }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-primary">{{ $toko['jumlah_pengiriman'] }}</span>
                                                    </td>
                                                    <td>
                                                        @php
                                                            // Hitung total jumlah sampel untuk toko ini dari data rekapData
                                                            $tokoJumlah = 0;
                                                            foreach ($rekapData as $pengiriman) {
                                                                if ($pengiriman->toko_id == $toko['toko_id']) {
                                                                    for ($i = 1; $i <= 5; $i++) {
                                                                        $tokoJumlah += $pengiriman->{"jumlah{$i}"} ?? 0;
                                                                    }
                                                                }
                                                            }
                                                            $totalTokoJumlah += $tokoJumlah;
                                                        @endphp
                                                        {{ $tokoJumlah }}
                                                    </td>
                                                    <td>Rp {{ number_format($toko['total_hpp'], 0, ',', '.') }}</td>
                                                    <td>Rp {{ number_format($toko['total_ongkir'], 0, ',', '.') }}</td>
                                                    <td><strong>Rp {{ number_format($toko['total_biaya'], 0, ',', '.') }}</strong></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="table-secondary">
                                            <tr>
                                                <th colspan="2" class="text-end">Total:</th>
                                                <th>{{ $totalTokoKirim }}</th>
                                                <th>{{ $totalTokoJumlah }}</th>
                                                <th>Rp {{ number_format($totalTokoHpp, 0, ',', '.') }}</th>
                                                <th>Rp {{ number_format($totalTokoOngkir, 0, ',', '.') }}</th>
                                                <th>Rp {{ number_format($totalTokoBiaya, 0, ',', '.') }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                @else
                                <div class="text-center py-4">
                                    <i class="fas fa-store fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Tidak ada data rekap per toko</p>
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
