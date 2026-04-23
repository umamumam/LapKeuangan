<x-app-layout>
    <div class="pc-container">
        <div class="pc-content" style="padding: 1rem; overflow: hidden;">
            <div
                class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
                <div>
                    <h5 class="mb-0 fw-bolder text-primary"><i class="fas fa-file-invoice me-2"></i> Transaksi {{ strtoupper($type) }}: {{
                        $reseller->nama }}</h5>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <span class="badge bg-light text-dark border px-2 small">
                            {{ $startDate->translatedFormat('d M Y') }} - {{
                            $startDate->copy()->addDays(34)->translatedFormat('d M Y') }}
                        </span>
                        <div class="ms-2 d-none d-md-flex gap-3 small fw-bold">
                            <span class="text-muted">TOTAL: <span class="text-dark" id="summaryTotal">Rp 0</span></span>
                            <span class="text-muted">SISA: <span class="text-danger" id="summarySisa">Rp 0</span></span>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <form action="{{ route('reseller_transactions.matrix') }}" method="GET" class="d-flex gap-1">
                        <input type="hidden" name="reseller_id" value="{{ $reseller->id }}">
                        <input type="hidden" name="type" value="{{ $type }}">
                        <select name="period_index" class="form-select form-select-sm border-primary shadow-none"
                            style="width: auto;" onchange="this.form.submit()">
                            @foreach($periods as $p)
                            <option value="{{ $p['index'] }}" {{ $periodIndex==$p['index'] ? 'selected' : '' }}>
                                {{ $p['label'] }}
                            </option>
                            @endforeach
                        </select>
                    </form>
                    <button type="button" class="btn btn-sm btn-outline-primary fw-bold" data-bs-toggle="modal"
                        data-bs-target="#rekapModal">REKAP</button>
                    <button type="button" class="btn btn-sm btn-primary fw-bold" id="btnSave">SIMPAN DATA</button>
                    <a href="{{ route('reseller_transactions.index', ['type' => $type]) }}" class="btn btn-sm btn-secondary"><i
                            class="fas fa-times"></i></a>
                </div>
            </div>

            <div class="table-wrapper">
                <div class="table-container shadow-sm border rounded bg-white">
                    <form id="matrixForm">
                        <input type="hidden" name="reseller_id" value="{{ $reseller->id }}">
                        <input type="hidden" name="type" value="{{ $type }}">
                        <table class="table-matrix" id="matrixTable">
                            <thead>
                                <tr>
                                    <th class="sticky-col-1 bg-grey" rowspan="3">NAMA BARANG</th>
                                    <th class="sticky-col-2 bg-grey text-uppercase" rowspan="3">HARGA {{ $type }}</th>
                                    @for($w=1; $w<=5; $w++) <th class="bg-soft-purple text-uppercase" colspan="7">MINGGU
                                        {{ $w }}</th>
                                        <th class="bg-orange" rowspan="3">JUMLAH</th>
                                        <th class="bg-orange" rowspan="3">TOTAL HARGA</th>
                                        <th class="separator" rowspan="3"></th>
                                        @endfor
                                        <th class="bg-dark text-white fw-bold" rowspan="3">GRAND TOTAL</th>
                                </tr>
                                <tr>
                                    @for($w=1; $w<=5; $w++) @for($d=0; $d<7; $d++) @php $current=$startDate->
                                        copy()->addDays(($w-1)*7 + $d); @endphp
                                        <th class="day-header {{ $current->isWeekend() ? 'weekend' : '' }}">{{
                                            $current->translatedFormat('D') }}</th>
                                        @endfor
                                        @endfor
                                </tr>
                                <tr>
                                    @for($w=1; $w<=5; $w++) @for($d=0; $d<7; $d++) @php $current=$startDate->
                                        copy()->addDays(($w-1)*7 + $d); @endphp
                                        <th class="date-header {{ $current->isWeekend() ? 'weekend' : '' }} small">{{
                                            $current->format('d M') }}</th>
                                        @endfor
                                        @endfor
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    // Normalisasi nama (trim) untuk pemetaan warna agar konsisten
                                    $uniqueNames = $barangs->map(fn($b) => trim($b->namabarang))->unique()->values()->toArray();
                                    $colors = [
                                        '#e8f5e9', // light green
                                        '#fff3e0', // light orange
                                        '#fce4ec', // light pink
                                        '#e1f5fe', // light blue
                                        '#f3e5f5', // light purple
                                        '#efebe9', // light brown
                                        '#fafafa', // light grey
                                        '#fffde7', // light yellow
                                        '#f1f8e9', // light lime
                                        '#e0f2f1', // light teal
                                        '#e8eaf6', // light indigo
                                        '#f9fbe7', // light lime-yellow
                                        '#fff8e1', // light amber
                                        '#fbe9e7', // light deep orange
                                    ];
                                    $nameColorMap = [];
                                    foreach ($uniqueNames as $index => $name) {
                                        $nameColorMap[$name] = $colors[$index % count($colors)];
                                    }
                                @endphp
                                @foreach($barangs as $barang)
                                @php
                                    $normalizedName = trim($barang->namabarang);
                                    $rowColor = $nameColorMap[$normalizedName] ?? '#ffffff';
                                @endphp
                                <tr data-barang-id="{{ $barang->id }}" data-price="{{ $barang->display_price }}">
                                    <td class="sticky-col-1 fw-bold" style="background-color: {{ $rowColor }} !important;">
                                        <div class="text-dark">{{ $barang->namabarang }}</div>
                                        <div class="small text-muted fw-normal" style="font-size: 0.7rem;">Uk: {{ $barang->ukuran }}</div>
                                    </td>
                                    <td class="sticky-col-2 text-end" style="background-color: {{ $rowColor }} !important;">{{ number_format($barang->display_price, 0, ',', '.') }}</td>
                                    @for($w=1; $w<=5; $w++) @for($d=0; $d<7; $d++) @php $dateStr=$startDate->
                                        copy()->addDays(($w-1)*7 + $d)->format('Y-m-d');
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
                                    @for($w=1; $w<=5; $w++) @for($d=0; $d<7; $d++) @php $dateStr=$startDate->
                                        copy()->addDays(($w-1)*7 + $d)->format('Y-m-d'); @endphp
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
                                @for($w=1; $w<=5; $w++) @php $wStart=$startDate->copy()->addDays(($w-1)*7);
                                    $wEnd = $wStart->copy()->addDays(6);
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
                </div>
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
            background-color: #f8f9fa !important;
            font-weight: bold;
        }

        .bg-soft-purple {
            background-color: #f8f9ff !important;
            color: #4e73df;
            font-weight: bold;
        }

        .bg-orange {
            background-color: #fff9f0 !important;
            color: #d68910;
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
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const hAwal = {{ $reseller->hutang_awal ?? 0 }};
            const fmt = n => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n);
            const p = v => parseFloat(v) || 0;

            const rekapModal = new bootstrap.Modal(document.getElementById('rekapModal'));
            const pModal = new bootstrap.Modal(document.getElementById('pModal'));

            function update() {
                let gT = 0;
                const colS = {}; 
                const weekWS = [0,0,0,0,0,0];
                const rowWS = {}; 

                document.querySelectorAll('#matrixTable tbody tr').forEach(row => {
                    const price = p(row.dataset.price);
                    const rId = row.dataset.barangId;
                    rowWS[rId] = [0,0,0,0,0,0];
                    let rGT = 0;

                    row.querySelectorAll('.qty-input').forEach((inp, idx) => {
                        const q = p(inp.value);
                        const d = inp.dataset.date;
                        const w = p(inp.dataset.week);
                        
                        if(!colS[d]) colS[d] = 0;
                        colS[d] += (q * price);
                        
                        rowWS[rId][w] += q;
                        weekWS[w] += (q * price);
                        rGT += (q * price);
                    });

                    for(let w=1; w<=5; w++) {
                        row.querySelector(`.week-qty-${w}`).textContent = rowWS[rId][w];
                        row.querySelector(`.week-total-${w}`).textContent = fmt(rowWS[rId][w] * price).replace('Rp','');
                    }
                    row.querySelector('.row-grand-total').textContent = fmt(rGT).replace('Rp','');
                    gT += rGT;
                });

                for(let w=1; w<=5; w++) {
                    let wFQ = 0;
                    document.querySelectorAll(`.qty-input[data-week="${w}"]`).forEach(i => wFQ += p(i.value));
                    document.querySelector(`.week-foot-qty-${w}`).textContent = wFQ;
                    document.querySelector(`.week-foot-total-${w}`).textContent = fmt(weekWS[w]).replace('Rp','');
                    document.querySelector(`.minggu-total-${w}`).textContent = fmt(weekWS[w]).replace('Rp','');
                }

                Object.keys(colS).forEach(d => {
                    const td = document.querySelector(`.col-total-${d}`);
                    if(td) td.textContent = fmt(colS[d]).replace('Rp','');
                });

                document.getElementById('finalGrandTotal').textContent = fmt(gT).replace('Rp','');
                document.getElementById('summaryTotal').textContent = fmt(gT);
                document.getElementById('modalTotalNota').textContent = fmt(gT).replace('Rp','');
                
                let tPay = 0;
                for(let w=1; w<=5; w++) tPay += p(document.querySelector(`.modal-week-bayar-${w}`).dataset.bayar);
                document.getElementById('modalTotalBayar').textContent = fmt(tPay).replace('Rp','');
                document.getElementById('modalSisaNota').textContent = fmt(gT + hAwal - tPay);
                document.getElementById('summarySisa').textContent = fmt(gT + hAwal - tPay);
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

            document.getElementById('pForm').addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({title:'Menyimpan Setoran...', didOpen:()=>Swal.showLoading()});
                fetch("{{ route('reseller_transactions.save_payment') }}", {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}, body:new FormData(this)})
                .then(r=>r.json()).then(d=>{if(d.success) location.reload();});
            });

            update();
        });
    </script>
</x-app-layout>