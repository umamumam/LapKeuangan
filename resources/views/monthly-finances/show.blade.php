<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-eye"></i> Detail Data Summary & Output</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('monthly-finances.edit', $monthlyFinance->id) }}"
                                class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            <a href="{{ route('monthly-finances.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if($monthlyFinance->periode)
                            @php
                            $periode = $monthlyFinance->periode;
                            $toko = $periode->toko;
                            @endphp
                            <!-- Detail Perhitungan -->
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h6 class="border-bottom pb-2 mb-3">
                                        <i class="fas fa-calculator me-2"></i>Detail Perhitungan
                                    </h6>
                                    <div class="alert alert-info">
                                        <strong>Rumus Perhitungan:</strong><br>
                                        <small>
                                            1. Biaya Admin = Total Pendapatan × (Rasio Admin / 100)<br>
                                            2. Total Biaya = Operasional + Iklan + Biaya Admin<br>
                                            3. Laba/Rugi = Total Penghasilan (dari Periode) - Total HPP (dari Periode) -
                                            Total Biaya
                                        </small>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h6 class="card-title">Pendapatan Bersih</h6>
                                                    <h4 class="{{ $labaBersih >= 0 ? 'text-success' : 'text-danger' }}">
                                                        {{ $labaBersih >= 0 ? '+' : '' }}Rp {{
                                                        number_format($labaBersih, 0, ',', '.') }}
                                                    </h4>
                                                    <small class="text-muted">Setelah dikurangi semua biaya</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h6 class="card-title">Margin Laba</h6>
                                                    <h4>
                                                        @if($monthlyFinance->periode && $periode->total_penghasilan > 0)
                                                        {{ number_format(($labaBersih / $periode->total_penghasilan) *
                                                        100, 2) }}%
                                                        @else
                                                        0%
                                                        @endif
                                                    </h4>
                                                    <small class="text-muted">Persentase dari total penghasilan</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h6 class="card-title">Biaya Admin</h6>
                                                    <h4 class="text-primary">
                                                        {{ number_format($monthlyFinance->rasio_admin_layanan, 2) }}%
                                                    </h4>
                                                    <small class="text-muted">Dari total pendapatan</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Informasi Periode -->
                            <div class="col-md-6">
                                <h6 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-calendar-alt me-2"></i>Informasi Periode
                                </h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="40%">Nama Periode</th>
                                        <td>: <strong>{{ $periode->nama_periode }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th>Toko</th>
                                        <td>: {{ $toko->nama ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Marketplace</th>
                                        <td>
                                            : <span
                                                class="badge bg-{{ $periode->marketplace == 'Shopee' ? 'warning text-dark' : 'dark' }}">
                                                {{ $periode->marketplace }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Periode</th>
                                        <td>
                                            : {{ $periode->tanggal_mulai->format('d/m/Y') }}
                                            s/d {{ $periode->tanggal_selesai->format('d/m/Y') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Status Generate</th>
                                        <td>
                                            : <span
                                                class="badge bg-{{ $periode->is_generated ? 'success' : 'warning' }}">
                                                {{ $periode->is_generated ? 'Telah Digenerate' : 'Belum Digenerate' }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                                @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Data periode tidak ditemukan.
                                </div>
                                @endif
                            </div>

                            <!-- Data dari Periode -->
                            @if($monthlyFinance->periode)
                            <div class="col-md-6">
                                <h6 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-database me-2"></i>Data Periode
                                </h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="60%">Total Penghasilan</th>
                                        <td class="text-end">
                                            Rp {{ number_format($periode->total_penghasilan, 0, ',', '.') }}
                                            <br>
                                            <small class="text-muted">
                                                S: Rp {{ number_format($periode->total_penghasilan_shopee, 0, ',', '.')
                                                }}
                                                | T: Rp {{ number_format($periode->total_penghasilan_tiktok, 0, ',',
                                                '.') }}
                                            </small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Total Harga Produk</th>
                                        <td class="text-end">
                                            Rp {{ number_format($periode->total_harga_produk, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Jumlah Order</th>
                                        <td class="text-end">{{ number_format($periode->jumlah_order, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Return Quantity</th>
                                        <td class="text-end">{{ number_format($periode->returned_quantity, 0, ',', '.')
                                            }}</td>
                                    </tr>
                                    <tr>
                                        <th>Jumlah Income</th>
                                        <td class="text-end">{{ number_format($periode->jumlah_income, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Total HPP Produk</th>
                                        <td class="text-end">
                                            Rp {{ number_format($periode->total_hpp_produk, 0, ',', '.') }}
                                            <br>
                                            <small class="text-muted">
                                                S: Rp {{ number_format($periode->total_hpp_shopee, 0, ',', '.') }}
                                                | T: Rp {{ number_format($periode->total_hpp_tiktok, 0, ',', '.') }}
                                            </small>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            @endif
                        </div>

                        <!-- Data Keuangan Bulanan -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h6 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-chart-line me-2"></i>Data Keuangan Bulanan
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr>
                                                <th width="60%">Total Pendapatan</th>
                                                <td class="text-end">Rp {{
                                                    number_format($monthlyFinance->total_pendapatan, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Biaya Operasional</th>
                                                <td class="text-end">Rp {{ number_format($monthlyFinance->operasional,
                                                    0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Biaya Iklan</th>
                                                <td class="text-end">Rp {{ number_format($monthlyFinance->iklan, 0, ',',
                                                    '.') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Rasio Admin Layanan</th>
                                                <td class="text-end">{{
                                                    number_format($monthlyFinance->rasio_admin_layanan, 2) }}%</td>
                                            </tr>
                                            <tr>
                                                <th>Rasio Laba</th>
                                                <td class="text-end">
                                                    {{ number_format($rasioLaba, 2) }}%
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr>
                                                <th width="60%">Rasio Operasional</th>
                                                <td class="text-end">
                                                    {{number_format($monthlyFinance->operasional /
                                                    $monthlyFinance->total_pendapatan * 100, 2) }}%
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>AOV Aktual</th>
                                                <td class="text-end">Rp {{ number_format($monthlyFinance->operasional,
                                                    0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Basket Size Aktual</th>
                                                <td class="text-end">Rp {{ number_format($monthlyFinance->iklan, 0, ',',
                                                    '.') }}</td>
                                            </tr>
                                            <tr>
                                                <th>ROAS Aktual</th>
                                                <td class="text-end">
                                                    {{number_format($monthlyFinance->total_pendapatan /
                                                    $monthlyFinance->iklan, 2) }}%
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>ACOS Aktual</th>
                                                <td class="text-end">
                                                    {{number_format($monthlyFinance->iklan /
                                                    $monthlyFinance->total_pendapatan * 100, 2) }}%
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Rasio Margin</th>
                                                <td class="text-end">
                                                    {{number_format(($monthlyFinance->total_pendapatan -
                                                    $periode->total_hpp_produk)/
                                                    $monthlyFinance->total_pendapatan * 100, 2) }}%
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- Keterangan -->
                        @if($monthlyFinance->keterangan)
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h6 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-sticky-note me-2"></i>Keterangan
                                </h6>
                                <div class="alert alert-info">
                                    {{ $monthlyFinance->keterangan }}
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Metadata -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h6 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-info-circle me-2"></i>Metadata
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <th width="40%">Dibuat Pada</th>
                                                <td>: {{ $monthlyFinance->created_at->format('d/m/Y H:i:s') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Diupdate Pada</th>
                                                <td>: {{ $monthlyFinance->updated_at->format('d/m/Y H:i:s') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr class="table-active">
                                        <th width="60%"><strong>Total Pendapatan</strong></th>
                                        <td class="text-end"><strong>Rp {{ number_format($monthlyFinance->total_pendapatan, 0, ',', '.') }}</strong></td>
                                    </tr>
                                    <tr class="table-secondary">
                                        <th><strong>Total Penghasilan (dari Periode)</strong></th>
                                        <td class="text-end">
                                            @if($monthlyFinance->periode)
                                            <strong>Rp {{ number_format($periode->total_penghasilan, 0, ',',
                                                '.') }}</strong>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="table-secondary">
                                        <th><strong>Total HPP (dari Periode)</strong></th>
                                        <td class="text-end">
                                            @if($monthlyFinance->periode)
                                            <strong>Rp {{ number_format($periode->total_hpp_produk, 0, ',', '.')
                                                }}</strong>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="{{ $labaBersih >= 0 ? 'table-success' : 'table-danger' }}">
                                        <th><strong>Laba/Rugi Bersih</strong></th>
                                        <td class="text-end">
                                            <strong class="{{ $labaBersih >= 0 ? 'text-success' : 'text-danger' }}">
                                                Rp {{ number_format($labaBersih, 0, ',', '.') }}
                                            </strong>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
