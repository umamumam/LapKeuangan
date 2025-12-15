<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="col-sm-12">
                <div class="card shadow-sm border-0"> <div class="card-header d-flex justify-content-between align-items-center border-bottom bg-white p-3">
                        <h5 class="mb-0 text-dark"><i class="fas fa-chart-line me-2 text-primary"></i> Hasil Analisis Income (Detail)</h5>
                        <div class="d-flex gap-2">
                            @if(request('periode_id'))
                            <a href="{{ route('incomes.export-hasil') }}?periode_id={{ request('periode_id') }}"
                                class="btn btn-outline-success btn-sm">
                                <i class="fas fa-download me-1"></i> Export Excel
                            </a>
                            @endif
                            <a href="{{ route('incomes.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="p-3 mb-4 rounded border bg-light-subtle">
                            <h6 class="pb-2 mb-3 border-bottom text-dark"><i class="fas fa-filter me-2 text-secondary"></i> Filter Data</h6>
                            <form method="GET" action="{{ route('incomes.detailhasil') }}">
                                <div class="row align-items-end">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="periode_id" class="form-label small text-muted">Pilih Periode</label>
                                            <select class="form-control form-select form-select-sm" id="periode_id"
                                                name="periode_id" required>
                                                <option value="">-- Pilih Periode --</option>
                                                @foreach($periodes as $periode)
                                                <option value="{{ $periode->id }}" {{
                                                    request('periode_id')==$periode->id ? 'selected' : '' }}>
                                                    {{ $periode->nama_periode }}
                                                    @if($periode->toko)
                                                    ({{ $periode->toko->nama }})
                                                    @endif
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3 d-flex gap-2">
                                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                                <i class="fas fa-search me-1"></i> Tampilkan
                                            </button>
                                            @if(request('periode_id'))
                                            <a href="{{ route('incomes.detailhasil') }}"
                                                class="btn btn-outline-secondary btn-sm flex-shrink-0" title="Reset Filter">
                                                <i class="fas fa-refresh"></i>
                                            </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @if(!request('periode_id') && $incomes->isEmpty())
                        <div class="text-center py-5 border rounded bg-white">
                            <i class="fas fa-filter fa-4x text-secondary mb-3"></i>
                            <h4 class="text-dark">Silakan Pilih Periode</h4>
                            <p class="text-muted">Pilih periode dari *dropdown* di atas untuk menampilkan detail analisis
                                income.</p>
                        </div>
                        @elseif(request('periode_id'))
                        @php
                        $periodeTerpilih = $periodes->firstWhere('id', request('periode_id'));
                        $totalPersentase = $totalPenghasilan > 0 ? ($totalLaba / $totalPenghasilan) * 100 : 0;
                        @endphp

                        <div class="alert alert-light border-start border-3 border-primary shadow-sm mb-4 py-2">
                            <h6 class="mb-0 text-dark fw-bold">
                                <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                **Periode Analisis**: {{ $periodeTerpilih->nama_periode ?? 'Periode Tidak
                                Ditemukan' }}
                            </h6>
                            @if($periodeTerpilih && $periodeTerpilih->toko)
                            <small class="text-muted">Toko: **{{ $periodeTerpilih->toko->nama }}**</small>
                            @endif
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4 mb-3">
                                <div class="card h-100 shadow-sm border-0 border-top border-2 border-primary">
                                    <div class="card-body text-center p-3"> <div class="fs-4 text-primary mb-1"> <i class="fas fa-money-bill-wave"></i>
                                        </div>
                                        <p class="card-title text-uppercase small text-muted mb-1">Total Penghasilan</p>
                                        <h4 class="mb-0 text-dark fw-bold">Rp {{ number_format($totalPenghasilan, 0, ',', '.') }}</h4>
                                        <small class="text-secondary">{{ $incomes->total() }} Data Income</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="card h-100 shadow-sm border-0 border-top border-2 border-info">
                                    <div class="card-body text-center p-3">
                                        <div class="fs-4 text-info mb-1">
                                            <i class="fas fa-cubes"></i>
                                        </div>
                                        <p class="card-title text-uppercase small text-muted mb-1">Total HPP</p>
                                        <h4 class="mb-0 text-dark fw-bold">Rp {{ number_format($totalHpp, 0, ',', '.') }}</h4>
                                        <small class="text-secondary">Rasio: {{ number_format($totalPenghasilan > 0 ?
                                            ($totalHpp / $totalPenghasilan) * 100 : 0, 1) }}%</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="card h-100 shadow-sm border-0 border-top border-2 border-{{ $totalLaba >= 0 ? 'success' : 'danger' }}">
                                    <div class="card-body text-center p-3">
                                        <div class="fs-4 text-{{ $totalLaba >= 0 ? 'success' : 'danger' }} mb-1">
                                            <i class="fas {{ $totalLaba >= 0 ? 'fa-hand-holding-usd' : 'fa-exclamation-triangle' }}"></i>
                                        </div>
                                        <p class="card-title text-uppercase small text-muted mb-1">Total Laba/Rugi</p>
                                        <h4 class="mb-0 text-dark fw-bold">Rp {{ number_format($totalLaba, 0, ',', '.') }}</h4>
                                        <small class="text-muted">Margin Bersih: **<span class="text-{{ $totalLaba >= 0 ? 'success' : 'danger' }}">{{ number_format($totalPersentase, 1)
                                                }}%</span>**</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive shadow-sm border rounded">
                            <table class="table table-sm table-striped table-hover mb-0">
                                <thead class="table-light border-bottom border-dark"> <tr class="text-uppercase small">
                                        <th>No</th>
                                        <th>No Pesanan</th>
                                        <th>No Pengajuan</th>
                                        <th class="text-end">Penghasilan</th>
                                        <th class="text-end">HPP</th>
                                        <th class="text-end">Laba/Rugi</th>
                                        <th class="text-center">Margin</th>
                                        <th class="text-center">Tanggal</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($incomes->isEmpty())
                                    <tr>
                                        <td colspan="9" class="text-center py-4 text-muted">
                                            <i class="fas fa-info-circle me-1"></i> Tidak ditemukan data income untuk
                                            periode ini.
                                        </td>
                                    </tr>
                                    @else
                                    @foreach ($incomes as $income)
                                    @php
                                    $hpp = $income->orders->sum(function($order) {
                                    if ($order->produk) {
                                    $netQuantity = $order->jumlah - $order->returned_quantity;
                                    return $netQuantity * $order->produk->hpp_produk;
                                    }
                                    return 0;
                                    });
                                    $laba = $income->total_penghasilan - $hpp;
                                    $margin = $income->total_penghasilan > 0 ? ($laba / $income->total_penghasilan) *
                                    100 : 0;
                                    @endphp
                                    <tr>
                                        <td class="small">{{ ($incomes->currentPage() - 1) * $incomes->perPage() + $loop->iteration }}
                                        </td>
                                        <td class="small">
                                            <span class="text-dark">{{ $income->no_pesanan }}</span>
                                        </td>
                                        <td class="small">{{ $income->no_pengajuan ?? '-' }}</td>
                                        <td class="text-end small text-dark">Rp {{ number_format($income->total_penghasilan, 0, ',',
                                            '.') }}</td>
                                        <td class="text-end small text-dark">Rp {{ number_format($hpp, 0, ',', '.') }}</td>
                                        <td class="text-end">
                                            <span class="badge text-{{ $laba >= 0 ? 'success' : 'danger' }} fw-bold p-0 bg-transparent">
                                                Rp {{ number_format($laba, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge text-{{ $margin >= 0 ? 'info' : 'warning' }} p-0 bg-transparent">
                                                {{ number_format($margin, 1) }}%
                                            </span>
                                        </td>
                                        <td class="text-center small text-muted">{{ $income->created_at->format('d/m/Y') }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('incomes.show', $income->id) }}"
                                                class="btn btn-outline-info btn-xs" title="Lihat Detail">
                                                <i class="fas fa-eye small"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                                @if(!$incomes->isEmpty())
                                <tfoot class="bg-light fw-bold border-top">
                                    <tr class="small">
                                        <th colspan="3" class="text-end text-dark">GRAND TOTAL:</th>
                                        <th class="text-end text-primary">Rp {{ number_format($totalPenghasilan, 0,
                                            ',', '.') }}</th>
                                        <th class="text-end text-info">Rp {{ number_format($totalHpp, 0, ',', '.') }}</th>
                                        <th class="text-end">
                                            <span class="text-{{ $totalLaba >= 0 ? 'success' : 'danger' }} fs-6">
                                                Rp {{ number_format($totalLaba, 0, ',', '.') }}
                                            </span>
                                        </th>
                                        <th class="text-center">
                                            <span class="text-{{ $totalPersentase >= 0 ? 'info' : 'warning' }} fs-6">
                                                {{ number_format($totalPersentase, 1) }}%
                                            </span>
                                        </th>
                                        <th colspan="2"></th>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>

                            <div class="d-flex justify-content-between align-items-center p-2 border-top bg-white">
                                <div class="small text-muted">
                                    Menampilkan {{ $incomes->firstItem() ?? 0 }} - {{ $incomes->lastItem() ?? 0 }}
                                    dari **{{ $incomes->total() }}** data
                                </div>
                                <div class="pagination-container">
                                    {{ $incomes->appends(request()->query())->links('pagination::bootstrap-5') }}
                                    </div>
                            </div>
                            </div>

                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
