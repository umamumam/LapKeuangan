<div class="row">
    <div class="col-md-6">
        <h6>Informasi Periode</h6>
        <table class="table table-sm">
            <tr>
                <td><strong>Periode</strong></td>
                <td>{{ $monthlySummary->nama_periode }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal Awal</strong></td>
                <td>{{ $monthlySummary->periode_awal->format('d/m/Y H:i:s') }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal Akhir</strong></td>
                <td>{{ $monthlySummary->periode_akhir->format('d/m/Y H:i:s') }}</td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <h6>Ringkasan Keuangan</h6>
        <table class="table table-sm">
            <tr>
                <td><strong>Total Penghasilan</strong></td>
                <td>Rp {{ number_format($monthlySummary->total_penghasilan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Total HPP</strong></td>
                <td>Rp {{ number_format($monthlySummary->total_hpp, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Margin</strong></td>
                <td>Rp {{ number_format($calculatedData['margin'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Rasio Margin</strong></td>
                <td><span class="badge bg-info">{{ $calculatedData['rasio_margin'] }}%</span></td>
            </tr>
            <tr>
                <td><strong>Laba/Rugi</strong></td>
                <td>
                    <span class="badge bg-{{ $monthlySummary->laba_rugi >= 0 ? 'success' : 'danger' }}">
                        Rp {{ number_format($monthlySummary->laba_rugi, 0, ',', '.') }}
                    </span>
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-6">
        <h6>Data Orders</h6>
        <table class="table table-sm">
            <tr>
                <td><strong>Total Orders Qty</strong></td>
                <td>{{ number_format($monthlySummary->total_order_qty, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Total Return Qty</strong></td>
                <td>{{ number_format($monthlySummary->total_return_qty, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Net Quantity</strong></td>
                <td>{{ number_format($calculatedData['net_quantity'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Total Harga Produk</strong></td>
                <td>Rp {{ number_format($monthlySummary->total_harga_produk, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>AOV (Average Order Value)</strong></td>
                <td>Rp {{ number_format($calculatedData['aov'], 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <h6>Data Incomes</h6>
        <table class="table table-sm">
            <tr>
                <td><strong>Total Incomes</strong></td>
                <td>{{ number_format($monthlySummary->total_income_count, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Total Penghasilan</strong></td>
                <td>Rp {{ number_format($monthlySummary->total_penghasilan, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>
</div>
