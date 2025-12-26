<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Home</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="../dashboard/index.html">Home</a></li>
                                <li class="breadcrumb-item"><a href="javascript: void(0)">Dashboard</a></li>
                                <li class="breadcrumb-item" aria-current="page">Home</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->
            <!-- [ Main Content ] start -->
            <div class="row">
                <!-- [ sample-page ] start -->
                <div class="col-md-6 col-xl-3">
                    <div class="card social-widget-card bg-primary">
                        <div class="card-body">
                            <h3 class="text-white m-0">{{ $statistik['total_produk'] ?? '0' }} </h3>
                            <span class="m-t-10">Total Produk</span>
                            <i class="fas fa-box"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card social-widget-card bg-info">
                        <div class="card-body">
                            <h3 class="text-white m-0">{{ $statistik['total_order'] ?? '0' }} </h3>
                            <span class="m-t-10">Total Order</span>
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card social-widget-card bg-dark">
                        <div class="card-body">
                            <h3 class="text-white m-0">{{ $statistik['total_income'] ?? '0' }} </h3>
                            <span class="m-t-10">Total Income</span>
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card social-widget-card bg-danger">
                        <div class="card-body">
                            <h3 class="text-white m-0">{{ $statistik['total_toko'] ?? '0' }} </h3>
                            <span class="m-t-10">Total Toko</span>
                            <i class="fas fa-store"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 col-xl-8">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0">Grafik Pendapatan & Penghasilan</h5>

                        <!-- Filter Form -->
                        <form method="GET" action="{{ route('dashboard') }}" class="row g-2">
                            <div class="col-auto">
                                <select name="toko_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                    @foreach($tokos as $toko)
                                    <option value="{{ $toko->id }}" {{ $tokoId==$toko->id ? 'selected' : '' }}>
                                        {{ $toko->nama }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-auto">
                                <select name="tahun" class="form-select form-select-sm" onchange="this.form.submit()">
                                    @foreach($tahunList as $tahunItem)
                                    <option value="{{ $tahunItem }}" {{ $tahun==$tahunItem ? 'selected' : '' }}>
                                        {{ $tahunItem }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>

                        <ul class="nav nav-pills justify-content-end mb-0" id="chart-tab-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="chart-tab-home-tab" data-bs-toggle="pill"
                                    data-bs-target="#chart-tab-home" type="button" role="tab"
                                    aria-controls="chart-tab-home" aria-selected="true">Pendapatan</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="chart-tab-profile-tab" data-bs-toggle="pill"
                                    data-bs-target="#chart-tab-profile" type="button" role="tab"
                                    aria-controls="chart-tab-profile" aria-selected="false">Penghasilan</button>
                            </li>
                        </ul>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="tab-content" id="chart-tab-tabContent">
                                <div class="tab-pane" id="chart-tab-home" role="tabpanel"
                                    aria-labelledby="chart-tab-home-tab" tabindex="0">
                                    <div id="visitor-chart-1"></div>
                                </div>
                                <div class="tab-pane show active" id="chart-tab-profile" role="tabpanel"
                                    aria-labelledby="chart-tab-profile-tab" tabindex="0">
                                    <div id="visitor-chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-xl-4" style="margin-top: 25px;">
                    <h5 class="mb-3">Data Lainnya</h5>
                    <div class="card">
                        <div class="list-group list-group-flush">
                            <a href="#" class="list-group-item list-group-item-action">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <div class="avtar avtar-s rounded-circle text-success bg-light-success">
                                            <i class="ti ti-database f-18"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Total Data Sampel</h6>
                                        <p class="mb-0 text-muted">{{ $dataLainnya['last_update_sampel'] ?? '-'}}</p>
                                    </div>
                                    <div class="flex-shrink-0 text-end">
                                        <h6 class="mb-1">{{ number_format($dataLainnya['total_sampel'] ?? '0') }}</h6>
                                        <p class="mb-0 text-muted">Data</p>
                                    </div>
                                </div>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <div class="avtar avtar-s rounded-circle text-primary bg-light-primary">
                                            <i class="ti ti-truck-delivery f-18"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Total Pengiriman Sampel Affiliate</h6>
                                        <p class="mb-0 text-muted">{{ $dataLainnya['last_update_pengiriman_sampel'] ?? '-'}}
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0 text-end">
                                        <h6 class="mb-1">{{ number_format($dataLainnya['total_pengiriman_sampel'] ?? '0') }}
                                        </h6>
                                        <p class="mb-0 text-muted">Pengiriman</p>
                                    </div>
                                </div>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <div class="avtar avtar-s rounded-circle text-danger bg-light-danger">
                                            <i class="ti ti-file-report f-18"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Total Daftar Banding</h6>
                                        <p class="mb-0 text-muted">{{ $dataLainnya['last_update_banding'] ?? '-'}}</p>
                                    </div>
                                    <div class="flex-shrink-0 text-end">
                                        <h6 class="mb-1">{{ number_format($dataLainnya['total_banding'] ?? '0') }}</h6>
                                        <p class="mb-0 text-muted">Banding</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-xl-12">
                    <h5 class="mb-3">&nbsp;</h5>
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-2 f-w-400 text-muted">Produk Best Seller (6 Bulan Terakhir)</h6>

                            @if($bestSellerProducts->count() > 0)
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th class="text-center" width="40">#</th>
                                            <th>Produk</th>
                                            <th class="text-center">Terjual</th>
                                            <th class="text-center">Pendapatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($bestSellerProducts as $index => $product)
                                        <tr>
                                            <td class="text-center align-middle">
                                                <span class="badge bg-primary">{{ $index + 1 }}</span>
                                            </td>
                                            <td class="align-middle">
                                                <div class="fw-semibold" style="font-size: 0.9rem;">
                                                    {{ $product->nama_produk }}
                                                </div>
                                                @if($product->nama_variasi)
                                                <small class="text-muted">Varian: {{ $product->nama_variasi
                                                    }}</small><br>
                                                @endif
                                                @if($product->sku_induk)
                                                <small class="text-muted">SKU: {{ $product->sku_induk }}</small>
                                                @endif
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="badge bg-success rounded-pill" style="font-size: 0.85rem;">
                                                    {{ number_format($product->total_terjual) }}
                                                </span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="fw-bold text-primary" style="font-size: 0.85rem;">
                                                    Rp {{ number_format($product->total_pendapatan, 0, ',', '.') }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center py-4">
                                <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada data penjualan dalam 6 bulan terakhir</p>
                            </div>
                            @endif

                            <!-- Tambahan: Ringkasan statistik -->
                            <div class="mt-3 pt-3 border-top">
                                <div class="row">
                                    <div class="col-6 text-center">
                                        <small class="text-muted">Total Produk</small>
                                        <h5 class="mb-0">{{ $bestSellerProducts->count() }}</h5>
                                    </div>
                                    <div class="col-6 text-center">
                                        <small class="text-muted">Total Terjual</small>
                                        <h5 class="mb-0">
                                            {{ number_format($bestSellerProducts->sum('total_terjual')) }}
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- <div class="col-md-12 col-xl-8">
                    <h5 class="mb-3">Recent Orders</h5>
                    <div class="card tbl-card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-borderless mb-0">
                                    <thead>
                                        <tr>
                                            <th>TRACKING NO.</th>
                                            <th>PRODUCT NAME</th>
                                            <th>TOTAL ORDER</th>
                                            <th>STATUS</th>
                                            <th class="text-end">TOTAL AMOUNT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><a href="#" class="text-muted">84564564</a></td>
                                            <td>Camera Lens</td>
                                            <td>40</td>
                                            <td><span class="d-flex align-items-center gap-2"><i
                                                        class="fas fa-circle text-danger f-10 m-r-5"></i>Rejected</span>
                                            </td>
                                            <td class="text-end">$40,570</td>
                                        </tr>
                                        <tr>
                                            <td><a href="#" class="text-muted">84564564</a></td>
                                            <td>Laptop</td>
                                            <td>300</td>
                                            <td><span class="d-flex align-items-center gap-2"><i
                                                        class="fas fa-circle text-warning f-10 m-r-5"></i>Pending</span>
                                            </td>
                                            <td class="text-end">$180,139</td>
                                        </tr>
                                        <tr>
                                            <td><a href="#" class="text-muted">84564564</a></td>
                                            <td>Mobile</td>
                                            <td>355</td>
                                            <td><span class="d-flex align-items-center gap-2"><i
                                                        class="fas fa-circle text-success f-10 m-r-5"></i>Approved</span>
                                            </td>
                                            <td class="text-end">$180,139</td>
                                        </tr>
                                        <tr>
                                            <td><a href="#" class="text-muted">84564564</a></td>
                                            <td>Camera Lens</td>
                                            <td>40</td>
                                            <td><span class="d-flex align-items-center gap-2"><i
                                                        class="fas fa-circle text-danger f-10 m-r-5"></i>Rejected</span>
                                            </td>
                                            <td class="text-end">$40,570</td>
                                        </tr>
                                        <tr>
                                            <td><a href="#" class="text-muted">84564564</a></td>
                                            <td>Laptop</td>
                                            <td>300</td>
                                            <td><span class="d-flex align-items-center gap-2"><i
                                                        class="fas fa-circle text-warning f-10 m-r-5"></i>Pending</span>
                                            </td>
                                            <td class="text-end">$180,139</td>
                                        </tr>
                                        <tr>
                                            <td><a href="#" class="text-muted">84564564</a></td>
                                            <td>Mobile</td>
                                            <td>355</td>
                                            <td><span class="d-flex align-items-center gap-2"><i
                                                        class="fas fa-circle text-success f-10 m-r-5"></i>Approved</span>
                                            </td>
                                            <td class="text-end">$180,139</td>
                                        </tr>
                                        <tr>
                                            <td><a href="#" class="text-muted">84564564</a></td>
                                            <td>Camera Lens</td>
                                            <td>40</td>
                                            <td><span class="d-flex align-items-center gap-2"><i
                                                        class="fas fa-circle text-danger f-10 m-r-5"></i>Rejected</span>
                                            </td>
                                            <td class="text-end">$40,570</td>
                                        </tr>
                                        <tr>
                                            <td><a href="#" class="text-muted">84564564</a></td>
                                            <td>Laptop</td>
                                            <td>300</td>
                                            <td><span class="d-flex align-items-center gap-2"><i
                                                        class="fas fa-circle text-warning f-10 m-r-5"></i>Pending</span>
                                            </td>
                                            <td class="text-end">$180,139</td>
                                        </tr>
                                        <tr>
                                            <td><a href="#" class="text-muted">84564564</a></td>
                                            <td>Mobile</td>
                                            <td>355</td>
                                            <td><span class="d-flex align-items-center gap-2"><i
                                                        class="fas fa-circle text-success f-10 m-r-5"></i>Approved</span>
                                            </td>
                                            <td class="text-end">$180,139</td>
                                        </tr>
                                        <tr>
                                            <td><a href="#" class="text-muted">84564564</a></td>
                                            <td>Mobile</td>
                                            <td>355</td>
                                            <td><span class="d-flex align-items-center gap-2"><i
                                                        class="fas fa-circle text-success f-10 m-r-5"></i>Approved</span>
                                            </td>
                                            <td class="text-end">$180,139</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-xl-4">
                    <h5 class="mb-3">Analytics Report</h5>
                    <div class="card">
                        <div class="list-group list-group-flush">
                            <a href="#"
                                class="list-group-item list-group-item-action d-flex align-items-center justify-content-between">Company
                                Finance Growth<span class="h5 mb-0">+45.14%</span></a>
                            <a href="#"
                                class="list-group-item list-group-item-action d-flex align-items-center justify-content-between">Company
                                Expenses Ratio<span class="h5 mb-0">0.58%</span></a>
                            <a href="#"
                                class="list-group-item list-group-item-action d-flex align-items-center justify-content-between">Business
                                Risk Cases<span class="h5 mb-0">Low</span></a>
                        </div>
                        <div class="card-body px-2">
                            <div id="analytics-report-chart"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 col-xl-8">
                    <h5 class="mb-3">Sales Report</h5>
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-2 f-w-400 text-muted">This Week Statistics</h6>
                            <h3 class="mb-0">$7,650</h3>
                            <div id="sales-report-chart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-xl-4">
                    <h5 class="mb-3">Transaction History</h5>
                    <div class="card">
                        <div class="list-group list-group-flush">
                            <a href="#" class="list-group-item list-group-item-action">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <div class="avtar avtar-s rounded-circle text-success bg-light-success">
                                            <i class="ti ti-gift f-18"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Order #002434</h6>
                                        <p class="mb-0 text-muted">Today, 2:00 AM</P>
                                    </div>
                                    <div class="flex-shrink-0 text-end">
                                        <h6 class="mb-1">+ $1,430</h6>
                                        <p class="mb-0 text-muted">78%</P>
                                    </div>
                                </div>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <div class="avtar avtar-s rounded-circle text-primary bg-light-primary">
                                            <i class="ti ti-message-circle f-18"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Order #984947</h6>
                                        <p class="mb-0 text-muted">5 August, 1:45 PM</P>
                                    </div>
                                    <div class="flex-shrink-0 text-end">
                                        <h6 class="mb-1">- $302</h6>
                                        <p class="mb-0 text-muted">8%</P>
                                    </div>
                                </div>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <div class="avtar avtar-s rounded-circle text-danger bg-light-danger">
                                            <i class="ti ti-settings f-18"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Order #988784</h6>
                                        <p class="mb-0 text-muted">7 hours ago</P>
                                    </div>
                                    <div class="flex-shrink-0 text-end">
                                        <h6 class="mb-1">- $682</h6>
                                        <p class="mb-0 text-muted">16%</P>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
    <script>
        window.chartData = @json($chartData);
    </script>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        'use strict';
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                floatchart();
            }, 500);
        });

        function floatchart() {
            // Pastikan chartData tersedia dari controller
            const chartData = window.chartData || {
                pendapatan_shopee: [0,0,0,0,0,0,0,0,0,0,0,0],
                pendapatan_tiktok: [0,0,0,0,0,0,0,0,0,0,0,0],
                penghasilan_shopee: [0,0,0,0,0,0,0,0,0,0,0,0],
                penghasilan_tiktok: [0,0,0,0,0,0,0,0,0,0,0,0],
                bulan_labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
            };

            // Chart untuk Pendapatan
            if (document.querySelector('#visitor-chart-1')) {
                var options = {
                    chart: {
                        height: 450,
                        type: 'area',
                        toolbar: {
                            show: false
                        }
                    },
                    title: {
                        text: 'Grafik Pendapatan Shopee & TikTok',
                        align: 'left',
                        margin: 10,
                        style: {
                            fontSize: '16px',
                            fontWeight: '600'
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    colors: ['#fa8c16', '#000000'],
                    series: [{
                        name: 'Shopee',
                        data: chartData.pendapatan_shopee
                    }, {
                        name: 'Tiktok',
                        data: chartData.pendapatan_tiktok
                    }],
                    stroke: {
                        curve: 'smooth',
                        width: 2
                    },
                    xaxis: {
                        categories: chartData.bulan_labels,
                    },
                    yaxis: {
                        title: {
                            text: 'Rupiah'
                        },
                        labels: {
                            formatter: function(value) {
                                return value.toLocaleString('id-ID');
                            }
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                };

                var chart = new ApexCharts(document.querySelector('#visitor-chart-1'), options);
                chart.render();
            }

            // Chart untuk Penghasilan
            if (document.querySelector('#visitor-chart')) {
                var options1 = {
                    chart: {
                        height: 450,
                        type: 'area',
                        toolbar: {
                            show: false
                        }
                    },
                    title: {
                        text: 'Grafik Penghasilan Shopee & TikTok',
                        align: 'left',
                        margin: 10,
                        style: {
                            fontSize: '16px',
                            fontWeight: '600'
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    colors: ['#fa8c16', '#000000'],
                    series: [{
                        name: 'Shopee',
                        data: chartData.penghasilan_shopee
                    }, {
                        name: 'Tiktok',
                        data: chartData.penghasilan_tiktok
                    }],
                    stroke: {
                        curve: 'smooth',
                        width: 2
                    },
                    xaxis: {
                        categories: chartData.bulan_labels,
                    },
                    yaxis: {
                        title: {
                            text: 'Rupiah'
                        },
                        labels: {
                            formatter: function(value) {
                                return value.toLocaleString('id-ID');
                            }
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                };

                var chart1 = new ApexCharts(document.querySelector('#visitor-chart'), options1);
                chart1.render();
            }

            // Chart lainnya (income overview, analytics, sales report)
            (function () {
                if (document.querySelector('#income-overview-chart')) {
                    var options = {
                        chart: {
                            type: 'bar',
                            height: 365,
                            toolbar: {
                                show: false
                            }
                        },
                        colors: ['#13c2c2'],
                        plotOptions: {
                            bar: {
                                columnWidth: '45%',
                                borderRadius: 4
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        series: [{
                            data: [80, 95, 70, 42, 65, 55, 78]
                        }],
                        stroke: {
                            curve: 'smooth',
                            width: 2
                        },
                        xaxis: {
                            categories: ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'],
                            axisBorder: {
                                show: false
                            },
                            axisTicks: {
                                show: false
                            }
                        },
                        yaxis: {
                            show: false
                        },
                        grid: {
                            show: false
                        }
                    };
                    var chart = new ApexCharts(document.querySelector('#income-overview-chart'), options);
                    chart.render();
                }
            })();

            (function () {
                if (document.querySelector('#analytics-report-chart')) {
                    var options = {
                        chart: {
                            type: 'line',
                            height: 340,
                            toolbar: {
                                show: false
                            }
                        },
                        colors: ['#faad14'],
                        plotOptions: {
                            bar: {
                                columnWidth: '45%',
                                borderRadius: 4
                            }
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 1.5
                        },
                        grid: {
                            strokeDashArray: 4
                        },
                        series: [{
                            data: [58, 90, 38, 83, 63, 75, 35, 55]
                        }],
                        xaxis: {
                            type: 'datetime',
                            categories: [
                                '2018-05-19T00:00:00.000Z',
                                '2018-06-19T00:00:00.000Z',
                                '2018-07-19T01:30:00.000Z',
                                '2018-08-19T02:30:00.000Z',
                                '2018-09-19T03:30:00.000Z',
                                '2018-10-19T04:30:00.000Z',
                                '2018-11-19T05:30:00.000Z',
                                '2018-12-19T06:30:00.000Z'
                            ],
                            labels: {
                                format: 'MMM'
                            },
                            axisBorder: {
                                show: false
                            },
                            axisTicks: {
                                show: false
                            }
                        },
                        yaxis: {
                            show: false
                        },
                    };
                    var chart = new ApexCharts(document.querySelector('#analytics-report-chart'), options);
                    chart.render();
                }
            })();

            (function () {
                if (document.querySelector('#sales-report-chart')) {
                    var options = {
                        chart: {
                            type: 'bar',
                            height: 430,
                            toolbar: {
                                show: false
                            }
                        },
                        plotOptions: {
                            bar: {
                                columnWidth: '30%',
                                borderRadius: 4
                            }
                        },
                        stroke: {
                            show: true,
                            width: 8,
                            colors: ['transparent']
                        },
                        dataLabels: {
                            enabled: false
                        },
                        legend: {
                            position: 'top',
                            horizontalAlign: 'right',
                            show: true,
                            fontFamily: `'Public Sans', sans-serif`,
                            offsetX: 10,
                            offsetY: 10,
                            labels: {
                                useSeriesColors: false
                            },
                            markers: {
                                width: 10,
                                height: 10,
                                radius: '50%',
                                offsexX: 2,
                                offsexY: 2
                            },
                            itemMargin: {
                                horizontal: 15,
                                vertical: 5
                            }
                        },
                        colors: ['#faad14', '#1890ff'],
                        series: [{
                            name: 'Net Profit',
                            data: [180, 90, 135, 114, 120, 145]
                        }, {
                            name: 'Revenue',
                            data: [120, 45, 78, 150, 168, 99]
                        }],
                        xaxis: {
                            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']
                        },
                    };
                    var chart = new ApexCharts(document.querySelector('#sales-report-chart'), options);
                    chart.render();
                }
            })();
        }
    </script>
</x-app-layout>
