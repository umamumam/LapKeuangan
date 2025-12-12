{{-- resources/views/periodes/show.blade.php (Super Minimalis) --}}

@if(isset($periode))
<div class="p-2">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Summary: <strong>{{ $periode->nama_periode }}</strong></h5>
        <span class="badge {{ $periode->is_generated ? 'bg-light text-success border' : 'bg-light text-warning border' }}">
            {{ $periode->is_generated ? 'Generated' : 'Pending' }}
        </span>
    </div>

    <div class="row g-4">
        {{-- Ringkasan Data --}}
        <div class="col-md-6">
            <p class="text-muted small text-uppercase fw-bold mb-2">Informasi & Statistik</p>
            <ul class="list-group list-group-flush border-top border-bottom">
                <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent">
                    Toko / Marketplace <span>{{ $periode->toko->nama ?? '-' }} ({{ $periode->marketplace }})</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent">
                    Rentang Tanggal <span>{{ \Carbon\Carbon::parse($periode->tanggal_mulai)->format('d/m/y') }} - {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->format('d/m/y') }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent">
                    Jumlah Order / Transaksi <span>{{ $stats['orders_count'] }} / {{ $stats['incomes_count'] }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent">
                    Total Return <span>{{ $stats['total_return'] }} pcs</span>
                </li>
            </ul>
        </div>

        {{-- Perhitungan Finansial --}}
        <div class="col-md-6">
            <p class="text-muted small text-uppercase fw-bold mb-2">Perhitungan Laba</p>
            <div class="py-2 border-top border-bottom">
                <div class="d-flex justify-content-between mb-1">
                    <span>Penghasilan (Incomes)</span>
                    <span class="text-success">Rp {{ number_format($stats['total_penghasilan'], 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>HPP Produk (Orders)</span>
                    <span class="text-danger">- Rp {{ number_format($stats['total_harga_produk'], 0, ',', '.') }}</span>
                </div>
                @php $laba = $stats['total_penghasilan'] - $stats['total_harga_produk']; @endphp
                <div class="d-flex justify-content-between pt-2 border-top">
                    <span class="fw-bold">Estimasi Laba Kotor</span>
                    <span class="fw-bold fs-5 {{ $laba >= 0 ? 'text-success' : 'text-danger' }}">
                        Rp {{ number_format($laba, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail List dalam 2 kolom --}}
    <div class="row mt-4">
        <div class="col-md-6">
            <p class="text-muted small text-uppercase fw-bold mb-1">List Order</p>
            <div class="table-responsive" style="max-height: 250px;">
                <table class="table table-sm table-hover border-top">
                    <thead>
                        <tr class="small">
                            <th>No. Order</th>
                            <th class="text-end">HPP</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @foreach($periode->orders as $order)
                        <tr>
                            <td class="text-muted">{{ $order->no_order }}</td>
                            <td class="text-end">Rp {{ number_format($order->total_harga_produk, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-6">
            <p class="text-muted small text-uppercase fw-bold mb-1">List Income</p>
            <div class="table-responsive" style="max-height: 250px;">
                <table class="table table-sm table-hover border-top">
                    <thead>
                        <tr class="small">
                            <th>ID Transaksi</th>
                            <th class="text-end">Income</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @foreach($periode->incomes as $income)
                        <tr>
                            <td class="text-muted">{{ Str::limit($income->transaction_id ?? $income->id, 20) }}</td>
                            <td class="text-end">Rp {{ number_format($income->total_penghasilan, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@else
<p class="text-center py-5 text-muted">Data tidak ditemukan.</p>
@endif
