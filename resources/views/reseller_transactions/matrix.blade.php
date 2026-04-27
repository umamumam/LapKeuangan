<x-app-layout>
    <div class="pc-container">
        <div class="pc-content" style="padding: 1rem; overflow: hidden;">
            <div
                class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="mb-0 fw-bolder text-primary"><i class="fas fa-file-invoice me-2"></i> Transaksi {{ strtoupper($type) }}: {{ $reseller->nama }}</h5>
                        <span class="badge bg-light text-dark border px-2 small fw-bold">
                            {{ $startDate->translatedFormat('d M Y') }} - {{ $startDate->copy()->addDays(34)->translatedFormat('d M Y') }}
                        </span>
                    </div>
                    <div class="mt-2 d-none d-md-flex gap-4 small fw-bold">
                        <span class="text-muted">BELANJA PERIODE: <span class="text-dark" id="summaryTotal">Rp 0</span></span>
                        <span class="text-muted">TAGIHAN PERIODE: <span class="text-primary" id="summarySisaPeriod">Rp 0</span></span>
                        <span class="text-muted" title="Total Hutang Keseluruhan">SISA SEMUA TAGIHAN: <span class="text-danger" id="summarySisaGlobal">Rp {{ number_format($globalSisa, 0, ',', '.') }}</span></span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-danger fw-bold" id="btnReset"><i class="fas fa-trash-alt me-1"></i> RESET</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary fw-bold" data-bs-toggle="modal"
                        data-bs-target="#modalHutangAwal"><i class="fas fa-edit me-1"></i> HUTANG AWAL</button>
                    <button type="button" class="btn btn-sm btn-outline-dark fw-bold" data-bs-toggle="modal"
                        data-bs-target="#managePeriodsModal"><i class="fas fa-calendar-alt me-1"></i> PERIODE</button>
                    <button type="button" class="btn btn-sm btn-outline-primary fw-bold" data-bs-toggle="modal"
                        data-bs-target="#rekapModal">REKAP</button>
                    <button type="button" class="btn btn-sm btn-primary fw-bold" id="btnSave">SIMPAN DATA</button>
                    <a href="{{ route('reseller_transactions.index', ['type' => $type]) }}"
                        class="btn btn-sm btn-secondary"><i class="fas fa-times"></i></a>
                </div>
            </div>

            <!-- Excel Style Tabs -->
            <div class="excel-tabs-container mb-0 px-1">
                <div class="d-flex overflow-auto hide-scrollbar">
                    @foreach($periods as $p)
                    <a href="{{ route('reseller_transactions.matrix', ['reseller_id' => $reseller->id, 'type' => $type, 'period_id' => $p->id]) }}"
                        class="excel-tab {{ $periodId == $p->id ? 'active' : '' }}">
                        <span class="tab-title">{{ $p->title }}</span>
                        <span class="tab-date">{{ \Carbon\Carbon::parse($p->start_date)->format('d/m') }} - {{
                            \Carbon\Carbon::parse($p->end_date)->format('d/m') }}</span>
                    </a>
                    @endforeach
                </div>
            </div>

            <div class="table-wrapper">
                <div class="table-container shadow-sm border rounded bg-white">
                    <form id="matrixForm">
                        <input type="hidden" name="reseller_id" value="{{ $reseller->id }}">
                        <input type="hidden" name="type" value="{{ $type }}">
                        <table class="table-matrix" id="matrixTable">
                            @php
                            $totalDays = count($dates);
                            $totalWeeks = ceil($totalDays / 7);
                            @endphp
                            <thead>
                                <tr>
                                    <th class="sticky-col-1 bg-grey" rowspan="3">NAMA BARANG</th>
                                    <th class="sticky-col-2 bg-grey text-uppercase" rowspan="3">HARGA {{ $type }}</th>
                                    @for($w=1; $w<=$totalWeeks; $w++) @php $daysInThisWeek=min(7, $totalDays -
                                        ($w-1)*7); @endphp <th class="bg-soft-purple text-uppercase"
                                        colspan="{{ $daysInThisWeek }}">MINGGU {{ $w }}</th>
                                        <th class="bg-orange" rowspan="3">JUMLAH</th>
                                        <th class="bg-orange" rowspan="3">TOTAL HARGA</th>
                                        <th class="separator" rowspan="3"></th>
                                        @endfor
                                        <th class="bg-dark text-white fw-bold" rowspan="3">GRAND TOTAL</th>
                                </tr>
                                <tr>
                                    @for($w=1; $w<=$totalWeeks; $w++) @for($d=0; $d<7; $d++) @php $idx=($w-1)*7 + $d; if
                                        ($idx>= $totalDays) break;
                                        $current = $dates[$idx];
                                        @endphp
                                        <th class="day-header {{ $current->isWeekend() ? 'weekend' : '' }}">{{
                                            $current->translatedFormat('D') }}</th>
                                        @endfor
                                        @endfor
                                </tr>
                                <tr>
                                    @for($w=1; $w<=$totalWeeks; $w++) @for($d=0; $d<7; $d++) @php $idx=($w-1)*7 + $d; if
                                        ($idx>= $totalDays) break;
                                        $current = $dates[$idx];
                                        @endphp
                                        <th class="date-header {{ $current->isWeekend() ? 'weekend' : '' }} small">{{
                                            $current->format('d M') }}</th>
                                        @endfor
                                        @endfor
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                // Normalisasi nama (trim) untuk pemetaan warna agar konsisten
                                $uniqueNames = $barangs->map(fn($b) =>
                                trim($b->namabarang))->unique()->values()->toArray();
                                $colors = [
                                '#9bc2e6', // Light Blue
                                '#ffc000',
                                '#b6d7a8',
                                '#9fc5e8',
                                '#ffe599',
                                '#ff99cc',
                                '#b4a7d6',
                                '#00ffff',
                                '#b6d7a8',
                                '#ffe599',
                                '#76a5af',
                                '#b4a7d6',
                                '#ea9999',
                                '#ffe599',
                                ];
                                $nameColorMap = [];
                                foreach ($uniqueNames as $index => $name) {
                                $nameColorMap[$name] = $colors[$index % count($colors)];
                                }
                                @endphp

                                <!-- Sisa Sebelumnya Row -->
                                @if($sisaSebelumnya != 0)
                                <tr class="bg-light-warning">
                                    <td class="sticky-col-1 fw-bold text-end" colspan="2" style="background: #fff8e1 !important;">SISA SEBELUMNYA (Tagihan Awal + Periode Lalu)</td>
                                    @for($w=1; $w<=$totalWeeks; $w++)
                                        @php
                                            $daysInThisWeek = min(7, $totalDays - ($w-1)*7);
                                        @endphp
                                        <td colspan="{{ $daysInThisWeek + 2 }}" style="background: #fff8e1 !important;" class="text-end fw-bold text-danger">
                                            @if($w == 1)
                                                Rp {{ number_format($sisaSebelumnya, 0, ',', '.') }}
                                            @endif
                                        </td>
                                        <td class="separator" style="background: #fff8e1 !important;"></td>
                                    @endfor
                                    <td class="text-end fw-bold text-danger bg-light-warning" style="background: #fff8e1 !important;">Rp {{ number_format($sisaSebelumnya, 0, ',', '.') }}</td>
                                </tr>
                                @endif

                                @foreach($barangs as $barang)
                                @php
                                $normalizedName = trim($barang->namabarang);
                                $rowColor = $nameColorMap[$normalizedName] ?? '#ffffff';
                                @endphp
                                <tr data-barang-id="{{ $barang->id }}" data-price="{{ $barang->display_price }}">
                                    <td class="sticky-col-1 fw-bold"
                                        style="background-color: {{ $rowColor }} !important;">
                                        <div class="text-dark">{{ $barang->namabarang }}</div>
                                        <div class="small text-muted fw-normal" style="font-size: 0.7rem;">Uk: {{
                                            $barang->ukuran }}</div>
                                    </td>
                                    <td class="sticky-col-2 text-end"
                                        style="background-color: {{ $rowColor }} !important;">{{
                                        number_format($barang->display_price, 0, ',', '.') }}</td>
                                    @for($w=1; $w<=$totalWeeks; $w++) @for($d=0; $d<7; $d++) @php $idx=($w-1)*7 + $d; if
                                        ($idx>= $totalDays) break;
                                        $current = $dates[$idx];
                                        $dateStr = $current->format('Y-m-d');
                                        $qty = 0;
                                        if (isset($transactions[$dateStr])) {
                                        $detail = $transactions[$dateStr]->details->where('barang_id',
                                        $barang->id)->first();
                                        $qty = $detail ? $detail->jumlah : 0;
                                        }
                                        @endphp
                                        <td class="qty-cell bg-white">
                                            <input type="number" name="data[{{ $dateStr }}][{{ $barang->id }}]"
                                                class="qty-input" value="{{ $qty }}" min="0" data-date="{{ $dateStr }}"
                                                data-week="{{ $w }}">
                                        </td>
                                        @endfor
                                        <td class="text-center fw-bold bg-light-muted week-qty-{{ $w }}">0</td>
                                        <td class="text-end fw-bold bg-light-muted week-total-{{ $w }}">0</td>
                                        <td class="separator"></td>
                                        @endfor
                                        <td class="text-end fw-bold bg-soft-orange row-grand-total">0</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="sticky-col-footer">TOTAL PER TANGGAL</td>
                                    @for($w=1; $w<=$totalWeeks; $w++) @for($d=0; $d<7; $d++) @php $idx=($w-1)*7 + $d; if
                                        ($idx>= $totalDays) break;
                                        $dateStr = $dates[$idx]->format('Y-m-d');
                                        @endphp
                                        <td class="text-center col-total-{{ $dateStr }} small">0</td>
                                        @endfor
                                        <td class="text-center bg-grey week-foot-qty-{{ $w }}">0</td>
                                        <td class="text-end bg-grey week-foot-total-{{ $w }}">0</td>
                                        <td class="separator"></td>
                                        @endfor
                                        <td class="text-end bg-dark text-white fw-bold" id="finalGrandTotal">0</td>
                                </tr>
                            </tfoot>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Rekap -->
    <div class="modal fade" id="rekapModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                <div class="modal-header bg-white border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold text-dark"><i
                            class="fas fa-file-invoice-dollar text-primary me-2"></i> Ringkasan Transaksi</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0 align-middle">
                            <thead class="bg-light-muted text-center small fw-bold text-secondary">
                                <tr>
                                    <th class="py-3" width="35%">PERIODE MINGGU</th>
                                    <th class="py-3" width="30%">NILAI BELANJA</th>
                                    <th class="py-3" width="35%">SETORAN DANA</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for($w=1; $w<=$totalWeeks; $w++) @php $wStart=$startDate->copy()->addDays(($w-1)*7);
                                    $daysInWeek = min(7, $totalDays - ($w-1)*7);
                                    $wEnd = $wStart->copy()->addDays($daysInWeek - 1);
                                    $wPays = $payments->filter(fn($p) => $p->tgl >= $wStart->format('Y-m-d') && $p->tgl
                                    <= $wEnd->format('Y-m-d'))->sum('nominal');
                                        @endphp
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-bold text-dark">MINGGU {{ $w }}</div>
                                                <div class="small text-muted">{{ $wStart->format('d M') }} - {{
                                                    $wEnd->format('d M') }}</div>
                                            </td>
                                            <td class="text-end pe-4 fw-bold text-dark minggu-total-{{ $w }}">0</td>
                                            <td class="pe-4">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="fw-bold modal-week-bayar-{{ $w }}"
                                                        data-bayar="{{ $wPays }}">Rp {{ number_format($wPays, 0, ',',
                                                        '.') }}</span>
                                                    <button
                                                        class="btn btn-sm btn-outline-primary btn-pay-week fw-bold px-3 py-1"
                                                        data-date="{{ $wEnd->format('Y-m-d') }}" data-week="{{ $w }}">
                                                        BAYAR
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @endfor
                            </tbody>
                            <tfoot class="border-top">
                                <tr class="bg-light fw-bold text-dark">
                                    <td class="ps-4 py-3">TOTAL TRANSAKSI PERIODE INI</td>
                                    <td class="text-end pe-4" id="modalTotalNota">0</td>
                                    <td class="text-end pe-4" id="modalTotalBayar">0</td>
                                </tr>
                                <tr class="bg-light-muted fw-bold">
                                    <td class="ps-4 py-3">PIUTANG AWAL</td>
                                    <td class="text-end pe-4">Rp {{ number_format($reseller->hutang_awal ?? 0, 0, ',',
                                        '.') }}</td>
                                    <td></td>
                                </tr>
                                <tr class="bg-white fw-bolder fs-5">
                                    <td class="ps-4 py-4 text-primary">SISA SALDO NOTA</td>
                                    <td class="text-end pe-4 text-primary" id="modalSisaNota">0</td>
                                    <td class="text-center pe-4">
                                        <button class="btn btn-dark btn-sm w-100 fw-bold py-2"
                                            onclick="location.reload()">REFRESH</button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Riwayat Setoran -->
                    <div class="p-4 border-top">
                        <h6 class="fw-bold mb-3 text-secondary"><i class="fas fa-history me-2"></i> RIWAYAT SETORAN PERIODE INI</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle">
                                <thead class="bg-light small">
                                    <tr>
                                        <th>TANGGAL</th>
                                        <th class="text-end">NOMINAL</th>
                                        <th class="text-center">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payments->sortByDesc('tgl') as $p)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($p->tgl)->format('d/m/Y') }}</td>
                                        <td class="text-end fw-bold text-success">Rp {{ number_format($p->nominal, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <button class="btn btn-sm btn-link text-primary p-0" onclick="editPayment({{ $p->id }}, '{{ $p->tgl }}', {{ $p->nominal }})">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-link text-danger p-0" onclick="deletePayment({{ $p->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="3" class="text-center py-3 text-muted italic small">Belum ada setoran di periode ini.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Manage Periods -->
    <div class="modal fade" id="managePeriodsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fw-bold"><i class="fas fa-calendar-alt me-2"></i> KELOLA PERIODE</h5>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="periodForm" action="{{ route('reseller_periods.store') }}" method="POST" class="mb-4 p-3 bg-light rounded shadow-sm border border-primary border-opacity-10">
                        @csrf
                        <div id="methodField"></div>
                        <h6 class="fw-bold mb-3 text-primary" id="formTitle">TAMBAH PERIODE BARU</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Judul Periode</label>
                                <input type="text" name="title" id="p_title" class="form-control form-control-sm" placeholder="Contoh: April 2026" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Tgl Awal</label>
                                <input type="date" name="start_date" id="p_start" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Tgl Akhir</label>
                                <input type="date" name="end_date" id="p_end" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-2 d-flex align-items-end gap-1">
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1 fw-bold" id="p_btn">SIMPAN</button>
                                <button type="button" class="btn btn-secondary btn-sm d-none" id="p_cancel" onclick="resetPeriodForm()"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                    </form>

                    <div class="alert alert-info py-2 px-3 small border-0 shadow-sm mb-4" style="border-radius: 10px;">
                        <i class="fas fa-info-circle me-1 text-primary"></i> 
                        <strong>Rentang Terpakai:</strong> 
                        @foreach($periods as $p)
                            <span class="badge bg-white text-dark border ms-1">{{ \Carbon\Carbon::parse($p->start_date)->format('d/m') }}-{{ \Carbon\Carbon::parse($p->end_date)->format('d/m') }}</span>
                        @endforeach
                    </div>

                    <div class="table-responsive" style="max-height: 300px;">
                        <table class="table table-sm align-middle table-hover">
                            <thead class="sticky-top bg-white">
                                <tr class="text-secondary small">
                                    <th>ID</th>
                                    <th>JUDUL</th>
                                    <th>RANGE TANGGAL</th>
                                    <th class="text-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($periods as $p)
                                <tr>
                                    <td>{{ $p->id }}</td>
                                    <td class="fw-bold">{{ $p->title }}</td>
                                    <td>{{ \Carbon\Carbon::parse($p->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($p->end_date)->format('d M Y') }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button type="button" class="btn btn-link text-primary p-0" 
                                                onclick="editPeriod({{ $p->id }}, '{{ $p->title }}', '{{ $p->start_date }}', '{{ $p->end_date }}')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('reseller_periods.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus periode ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger p-0"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center py-3 text-muted italic">Belum ada periode manual.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Hutang Awal -->
    <div class="modal fade" id="modalHutangAwal" tabindex="-1">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                <form action="{{ route('reseller_transactions.update_sisa') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-secondary text-white py-3">
                        <h5 class="modal-title fw-bold"><i class="fas fa-money-bill-alt me-2"></i> HUTANG AWAL</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <input type="hidden" name="reseller_id" value="{{ $reseller->id }}">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">HUTANG AWAL / SALDO AWAL</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="number" name="sisa_nota" class="form-control" value="{{ $reseller->hutang_awal ?? 0 }}" required>
                            </div>
                            <div class="form-text small text-muted">Nilai ini akan ditambahkan ke total tagihan reseller secara global.</div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="submit" class="btn btn-secondary w-100 fw-bold py-2 shadow-sm">SIMPAN PERUBAHAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Payment -->
    <div class="modal fade" id="editPaymentModal" tabindex="-1">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                <form id="editPaymentForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-warning text-dark py-3">
                        <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i> EDIT PEMBAYARAN</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">TANGGAL</label>
                            <input type="date" name="tgl" id="edit_p_tgl" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">NOMINAL</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="number" name="nominal" id="edit_p_nominal" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="submit" class="btn btn-warning w-100 fw-bold py-2 shadow-sm">SIMPAN PERUBAHAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Setoran (Clean White Style) -->
    <div class="modal fade" id="pModal" tabindex="-1">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                <div class="modal-header bg-white border-bottom py-3 px-4">
                    <h6 class="modal-title fw-bold text-dark">SETORAN MINGGU <span id="payWeekNum">?</span></h6>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <form id="pForm">
                    <div class="modal-body p-4">
                        <input type="hidden" name="reseller_id" value="{{ $reseller->id }}">
                        <input type="hidden" name="type" value="{{ $type }}">
                        <div class="form-group mb-3">
                            <label class="form-label small fw-bold text-secondary mb-1">TANGGAL</label>
                            <input type="date" name="tgl" id="payDateInput"
                                class="form-control form-control-sm shadow-none border" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label small fw-bold text-secondary mb-1">NOMINAL (Rp)</label>
                            <input type="number" name="nominal" id="payNominalInput"
                                class="form-control fw-bold border shadow-none" placeholder="0" required autofocus>
                        </div>
                        <div class="form-group">
                            <label class="form-label small fw-bold text-secondary mb-1">KETERANGAN</label>
                            <input type="text" name="keterangan" class="form-control form-control-sm shadow-none"
                                placeholder="Opsional">
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2 shadow-sm">SIMPAN
                            PEMBAYARAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .table-wrapper {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 8px;
            overflow: hidden;
        }

        .table-container {
            width: 100%;
            overflow: auto;
            max-height: calc(100vh - 180px);
        }

        .table-container::-webkit-scrollbar {
            height: 10px;
            width: 10px;
        }

        .table-container::-webkit-scrollbar-track {
            background: #f8f9fa;
        }

        .table-container::-webkit-scrollbar-thumb {
            background: #dee2e6;
            border-radius: 5px;
        }

        .table-matrix {
            border-collapse: separate;
            border-spacing: 0;
            width: max-content;
            font-size: 0.8rem;
        }

        .table-matrix th,
        .table-matrix td {
            border: 1px solid #eee;
            padding: 4px 8px;
            white-space: nowrap;
        }

        .sticky-col-1 {
            position: sticky;
            left: 0;
            z-index: 100;
            min-width: 220px;
            box-shadow: 1px 0 5px rgba(0, 0, 0, 0.03);
        }

        .sticky-col-2 {
            position: sticky;
            left: 220px;
            z-index: 100;
            min-width: 90px;
            border-right: 2px solid #ddd !important;
        }

        .table-matrix thead tr th {
            position: sticky;
            top: 0;
            z-index: 105;
            background: #fafafa;
            text-align: center;
        }

        .table-matrix thead tr:nth-child(2) th {
            top: 30px;
        }

        .table-matrix thead tr:nth-child(3) th {
            top: 60px;
        }

        .table-matrix thead tr th.sticky-col-1,
        .table-matrix thead tr th.sticky-col-2 {
            z-index: 120;
        }

        .sticky-col-footer {
            position: sticky;
            left: 0;
            z-index: 100;
            background: #f5f5f5 !important;
            font-weight: bold;
        }

        .qty-cell {
            padding: 0 !important;
        }

        .qty-input {
            width: 70px;
            height: 35px;
            border: none;
            text-align: center;
            font-weight: bold;
            background: transparent;
        }

        .qty-input:hover {
            background: #f8f9fa;
        }

        .qty-input:focus {
            outline: 1px solid #4e73df;
            background: #fff;
            z-index: 5;
        }

        .bg-grey {
            background-color: #ffff00 !important;
            /* Yellow */
            color: #000;
            font-weight: bold;
        }

        .bg-soft-purple {
            background-color: #ead1dc !important;
            /* Soft Pink */
            color: #000;
            font-weight: bold;
        }

        .bg-orange {
            background-color: #00ffff !important;
            /* Cyan */
            color: #000;
            font-weight: bold;
        }

        .weekend {
            background-color: #fffafa !important;
            color: #e74c3c;
        }

        .separator {
            background: #f0f0f0 !important;
            width: 8px !important;
            min-width: 8px;
            padding: 0 !important;
            border: 0 !important;
        }

        .bg-light-muted {
            background: #fafafa !important;
        }

        /* Excel Style Tabs */
        .excel-tabs-container {
            background: #f1f3f4;
            border-bottom: 1px solid #ddd;
            margin-top: 10px;
        }

        .excel-tab {
            display: flex;
            flex-direction: column;
            padding: 8px 20px;
            text-decoration: none;
            color: #5f6368;
            background: #f1f3f4;
            border-right: 1px solid #ddd;
            min-width: 120px;
            transition: all 0.2s;
            position: relative;
        }

        .excel-tab:hover {
            background: #e8eaed;
            color: #202124;
        }

        .excel-tab.active {
            background: #fff;
            color: #1a73e8;
            font-weight: bold;
            border-bottom: 2px solid #1a73e8;
        }

        .excel-tab .tab-title {
            font-size: 0.85rem;
            white-space: nowrap;
        }

        .excel-tab .tab-date {
            font-size: 0.7rem;
            opacity: 0.8;
            white-space: nowrap;
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const hAwal = {{ $sisaSebelumnya ?? 0 }};
            const bSetelah = {{ $bayarSetelahnya ?? 0 }};
            const fmt = n => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n);
            const p = v => parseFloat(v) || 0;

            const rekapModal = new bootstrap.Modal(document.getElementById('rekapModal'));
            const pModal = new bootstrap.Modal(document.getElementById('pModal'));

            const totalWeeks = {{ $totalWeeks }};

            function update() {
                let gT = 0;
                const colS = {}; 
                const weekWS = Array(totalWeeks + 1).fill(0);
                const rowWS = {}; 

                document.querySelectorAll('#matrixTable tbody tr[data-barang-id]').forEach(row => {
                    const price = p(row.dataset.price);
                    const rId = row.dataset.barangId;
                    rowWS[rId] = Array(totalWeeks + 1).fill(0);
                    let rGT = 0;

                    row.querySelectorAll('.qty-input').forEach((inp, idx) => {
                        const q = p(inp.value);
                        const d = inp.dataset.date;
                        const w = p(inp.dataset.week);
                        
                        if(!colS[d]) colS[d] = 0;
                        colS[d] += q;
                        
                        rowWS[rId][w] += q;
                        weekWS[w] += (q * price);
                        rGT += (q * price);
                    });

                    for(let w=1; w<=totalWeeks; w++) {
                        const qCell = row.querySelector(`.week-qty-${w}`);
                        const tCell = row.querySelector(`.week-total-${w}`);
                        if(qCell) qCell.textContent = rowWS[rId][w];
                        if(tCell) tCell.textContent = fmt(rowWS[rId][w] * price).replace('Rp','');
                    }
                    row.querySelector('.row-grand-total').textContent = fmt(rGT).replace('Rp','');
                    gT += rGT;
                });

                for(let w=1; w<=totalWeeks; w++) {
                    let wFQ = 0;
                    document.querySelectorAll(`.qty-input[data-week="${w}"]`).forEach(i => wFQ += p(i.value));
                    
                    const fqCell = document.querySelector(`.week-foot-qty-${w}`);
                    const ftCell = document.querySelector(`.week-foot-total-${w}`);
                    const mtCell = document.querySelector(`.minggu-total-${w}`);
                    
                    if(fqCell) fqCell.textContent = wFQ;
                    if(ftCell) ftCell.textContent = fmt(weekWS[w]).replace('Rp','');
                    if(mtCell) mtCell.textContent = fmt(weekWS[w]).replace('Rp','');
                }

                Object.keys(colS).forEach(d => {
                    const td = document.querySelector(`.col-total-${d}`);
                    if(td) td.textContent = colS[d];
                });

                document.getElementById('finalGrandTotal').textContent = fmt(gT).replace('Rp','');
                document.getElementById('summaryTotal').textContent = fmt(gT);
                document.getElementById('modalTotalNota').textContent = fmt(gT).replace('Rp','');
                
                let tPay = 0;
                for(let w=1; w<=totalWeeks; w++) {
                    const bCell = document.querySelector(`.modal-week-bayar-${w}`);
                    if(bCell) tPay += p(bCell.dataset.bayar);
                }
                document.getElementById('modalTotalBayar').textContent = fmt(tPay).replace('Rp','');
                const periodSisa = gT + hAwal - tPay;
                document.getElementById('modalSisaNota').textContent = fmt(periodSisa);
                
                document.getElementById('summaryTotal').textContent = fmt(gT);
                document.getElementById('summarySisaPeriod').textContent = fmt(Math.max(0, gT - Math.max(0, tPay - hAwal)));
                document.getElementById('summarySisaGlobal').textContent = fmt(periodSisa - bSetelah);
            }

            // Pay Week Logic
            document.querySelectorAll('.btn-pay-week').forEach(btn => {
                btn.addEventListener('click', function() {
                    const date = this.dataset.date;
                    const week = this.dataset.week;
                    const weekValueString = document.querySelector(`.minggu-total-${week}`).textContent;
                    const weekValue = p(weekValueString.replace(/[^\d]/g, ''));
                    const payValue = p(document.querySelector(`.modal-week-bayar-${week}`).dataset.bayar);
                    
                    document.getElementById('payWeekNum').textContent = week;
                    document.getElementById('payDateInput').value = date;
                    document.getElementById('payNominalInput').value = weekValue - payValue > 0 ? weekValue - payValue : '';
                    
                    rekapModal.hide();
                    pModal.show();
                });
            });

            document.querySelectorAll('.qty-input').forEach(i => i.addEventListener('input', update));
            document.getElementById('btnSave').addEventListener('click', () => {
                const fd = new FormData(document.getElementById('matrixForm'));
                Swal.fire({title:'Menyimpan...', didOpen:()=>Swal.showLoading()});
                fetch("{{ route('reseller_transactions.save_matrix') }}", {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}, body:fd}).then(r=>r.json()).then(d=>Swal.fire(d.success?'Berhasil':'Error', d.message, d.success?'success':'error'));
            });

            document.getElementById('btnReset').addEventListener('click', () => {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Semua transaksi untuk reseller ini akan DIHAPUS PERMANEN (Termasuk periode lain)!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Reset Semua!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({title:'Mereset data...', didOpen:()=>Swal.showLoading()});
                        fetch("{{ route('reseller_transactions.reset') }}", {
                            method:'POST', 
                            headers:{
                                'X-CSRF-TOKEN':'{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }, 
                            body: JSON.stringify({ reseller_id: "{{ $reseller->id }}", type: "{{ $type }}" })
                        })
                        .then(r=>r.json())
                        .then(d=>{
                            if(d.success) {
                                Swal.fire('Berhasil', d.message, 'success').then(() => location.reload());
                            } else {
                                Swal.fire('Error', d.message, 'error');
                            }
                        });
                    }
                });
            });

            document.getElementById('pForm').addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({title:'Menyimpan Setoran...', didOpen:()=>Swal.showLoading()});
                fetch("{{ route('reseller_transactions.save_payment') }}", {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}, body:new FormData(this)})
                .then(r=>r.json()).then(d=>{if(d.success) location.reload();});
            });

            // Edit Payment Logic
            const editPaymentModal = new bootstrap.Modal(document.getElementById('editPaymentModal'));
            window.editPayment = function(id, tgl, nominal) {
                const form = document.getElementById('editPaymentForm');
                form.action = `/reseller_payments/${id}`;
                document.getElementById('edit_p_tgl').value = tgl;
                document.getElementById('edit_p_nominal').value = nominal;
                rekapModal.hide();
                editPaymentModal.show();
            };

            document.getElementById('editPaymentForm').addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({title:'Memperbarui Setoran...', didOpen:()=>Swal.showLoading()});
                fetch(this.action, {
                    method:'POST', 
                    headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}, 
                    body:new FormData(this)
                })
                .then(r=>r.json()).then(d=>{if(d.success) location.reload();});
            });

            window.deletePayment = function(id) {
                fetch(`/reseller_payments/${id}`, {
                    method:'DELETE',
                    headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}
                })
                .then(r=>r.json()).then(d=>{if(d.success) location.reload();});
            };

            // Period Management Logic
            window.editPeriod = function(id, title, start, end) {
                const form = document.getElementById('periodForm');
                const titleEl = document.getElementById('formTitle');
                const btnEl = document.getElementById('p_btn');
                const cancelEl = document.getElementById('p_cancel');
                const methodEl = document.getElementById('methodField');

                form.action = `/reseller_periods/${id}`;
                methodEl.innerHTML = '@method("PUT")';
                titleEl.textContent = 'EDIT PERIODE: ' + title;
                titleEl.classList.replace('text-primary', 'text-warning');
                btnEl.textContent = 'UPDATE';
                btnEl.classList.replace('btn-primary', 'btn-warning');
                cancelEl.classList.remove('d-none');

                document.getElementById('p_title').value = title;
                document.getElementById('p_start').value = start;
                document.getElementById('p_end').value = end;
                
                document.getElementById('p_title').focus();
            };

            window.resetPeriodForm = function() {
                const form = document.getElementById('periodForm');
                const titleEl = document.getElementById('formTitle');
                const btnEl = document.getElementById('p_btn');
                const cancelEl = document.getElementById('p_cancel');
                const methodEl = document.getElementById('methodField');

                form.action = "{{ route('reseller_periods.store') }}";
                methodEl.innerHTML = '';
                titleEl.textContent = 'TAMBAH PERIODE BARU';
                titleEl.classList.replace('text-warning', 'text-primary');
                btnEl.textContent = 'SIMPAN';
                btnEl.classList.replace('btn-warning', 'btn-primary');
                cancelEl.classList.add('d-none');

                form.reset();
            };

            update();

            @if(session('success'))
                Swal.fire('Berhasil', "{{ session('success') }}", 'success');
            @endif
            @if(session('error'))
                Swal.fire('Gagal', "{{ session('error') }}", 'error');
            @endif
        });
    </script>
</x-app-layout>