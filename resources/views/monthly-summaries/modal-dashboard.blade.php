@if($currentMonth)
<div class="row">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6>Total Penghasilan</h6>
                <h4>Rp {{ number_format($currentMonth->total_penghasilan, 0, ',', '.') }}</h4>
                <small>
                    @if($growth['penghasilan'] >= 0)
                        <i class="fas fa-arrow-up"></i>
                    @else
                        <i class="fas fa-arrow-down"></i>
                    @endif
                    {{ abs($growth['penghasilan']) }}% dari bulan lalu
                </small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6>Laba/Rugi</h6>
                <h4>Rp {{ number_format($currentMonth->laba_rugi, 0, ',', '.') }}</h4>
                <small>
                    @if($growth['laba_rugi'] >= 0)
                        <i class="fas fa-arrow-up"></i>
                    @else
                        <i class="fas fa-arrow-down"></i>
                    @endif
                    {{ abs($growth['laba_rugi']) }}% dari bulan lalu
                </small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6>Total Orders</h6>
                <h4>{{ number_format($currentMonth->total_order_qty, 0, ',', '.') }}</h4>
                <small>
                    @if($growth['orders'] >= 0)
                        <i class="fas fa-arrow-up"></i>
                    @else
                        <i class="fas fa-arrow-down"></i>
                    @endif
                    {{ abs($growth['orders']) }}% dari bulan lalu
                </small>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <h6>Trend 6 Bulan Terakhir</h6>
        <div class="table-responsive">
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Periode</th>
                        <th>Penghasilan</th>
                        <th>HPP</th>
                        <th>Laba/Rugi</th>
                        <th>Margin</th>
                        <th>Orders</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($last6Months as $summary)
                    <tr>
                        <td>{{ $summary->nama_periode }}</td>
                        <td>Rp {{ number_format($summary->total_penghasilan, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($summary->total_hpp, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge bg-{{ $summary->laba_rugi >= 0 ? 'success' : 'danger' }}">
                                Rp {{ number_format($summary->laba_rugi, 0, ',', '.') }}
                            </span>
                        </td>
                        <td>{{ $summary->rasio_margin }}%</td>
                        <td>{{ number_format($summary->total_order_qty, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
<div class="text-center py-4">
    <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
    <p class="text-muted">Belum ada data summary untuk bulan ini.</p>
    <button type="button" class="btn btn-primary" onclick="generateCurrentMonth()">
        <i class="fas fa-plus"></i> Generate Summary Bulan Ini
    </button>
</div>
@endif
