<x-app-layout>
    <style>
        table.dataTable tbody tr.shown td {
            background-color: #f8f9fc !important;
            border-bottom: none !important;
            transition: background-color 0.2s ease-in-out;
        }

        table.dataTable tbody tr.child td {
            padding: 0 !important;
            background-color: #f8f9fc !important;
            border-bottom: 1px solid #eaecf4;
        }

        .btn-aksi {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            border-radius: 6px;
        }

        .child-row-wrapper {
            border-left: 4px solid #4e73df;
            border-radius: 0 0 8px 0;
            padding: 1.25rem 1.5rem;
            margin-bottom: 8px;
        }
    </style>

    <div class="pc-container">
        <div class="pc-content">

            @if(session('success'))
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        icon: "success",
                        title: "Berhasil!",
                        text: "{{ session('success') }}",
                        showConfirmButton: false,
                        timer: 3000
                    });
                });
            </script>
            @endif

            @if($hasDebt ?? false)
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center shadow-sm"
                role="alert">
                <i class="fas fa-exclamation-triangle fs-4 me-3"></i>
                <div>
                    <strong>Perhatian!</strong> Reseller ini masih memiliki <strong>tagihan/hutang (sisa
                        kurang)</strong> pada bulan ini yang belum lunas.
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div
                class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
                <div>
                    <h5 class="mb-0 fw-bold"><i class="fas fa-user-tie text-primary me-2"></i> Transaksi Reseller: {{
                        $reseller->nama }}</h5>
                </div>
                <div class="w-100 w-md-auto">
                    <form action="{{ route('reseller_transactions.show_reseller', $reseller->id) }}" method="GET"
                        class="d-flex flex-wrap gap-2 justify-content-start justify-content-md-end">
                        <select name="month" class="form-select form-select-sm shadow-none flex-grow-1 flex-md-grow-0"
                            style="width: auto; min-width: 120px;">
                            @for($m=1; $m<=12; $m++) <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{
                                $month==str_pad($m, 2, '0' , STR_PAD_LEFT) ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                                @endfor
                        </select>
                        <select name="year" class="form-select form-select-sm shadow-none flex-grow-1 flex-md-grow-0"
                            style="width: auto; min-width: 90px;">
                            @for($y=date('Y')-2; $y<=date('Y'); $y++) <option value="{{ $y }}" {{ $year==$y ? 'selected'
                                : '' }}>{{ $y }}</option>
                                @endfor
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm flex-grow-1 flex-md-grow-0 shadow-sm">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </form>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div
                            class="card-header bg-white border-0 d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 pt-3 pb-3">
                            <h6 class="mb-0 fw-bold text-muted"><i class="fas fa-bars me-2"></i> Menu Aksi</h6>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('reseller_transactions.index') }}"
                                    class="btn btn-secondary btn-sm shadow-sm">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                <button type="button" class="btn btn-success btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#payDebtModal">
                                    <i class="fas fa-money-bill-wave text-white me-1"></i> Bayar Tagihan
                                </button>
                                <a href="{{ route('reseller_transactions.create', ['reseller_id' => $reseller->id]) }}"
                                    class="btn btn-primary btn-sm shadow-sm">
                                    <i class="fas fa-plus"></i> Tambah Transaksi
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                @foreach (['minggu_1' => 'Minggu 1 (1-7)', 'minggu_2' => 'Minggu 2 (8-14)', 'minggu_3' => 'Minggu 3
                (15-21)', 'minggu_4' => 'Minggu 4 (22-28)', 'minggu_5' => 'Minggu 5 (29+)'] as $key => $label)
                <div class="col-md mb-4" style="min-width: 200px;">
                    <div class="card border-0 shadow-sm h-100"
                        style="border-radius: 12px; background: linear-gradient(to bottom right, #ffffff, #fdfdfd);">
                        <div class="card-header border-0 pb-0 bg-transparent text-center">
                            <h6 class="card-title mb-0 fw-bold text-dark" style="font-size: 0.9rem;">{{ $label }}</h6>
                        </div>
                        <div class="card-body px-3">
                            <hr class="mt-2 mb-3" style="opacity: 0.1">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted" style="font-size: 0.75rem;"><i
                                        class="fas fa-shopping-bag me-1"></i> Penjualan</small>
                                <span class="fw-bold" style="font-size: 0.9rem;">Rp {{
                                    number_format($rekap[$key]['total_uang'], 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted" style="font-size: 0.75rem;"><i
                                        class="fas fa-money-bill-wave me-1"></i> Bayar</small>
                                <span class="fw-bold text-success" style="font-size: 0.9rem;">Rp {{
                                    number_format($rekap[$key]['bayar'], 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3 pt-2"
                                style="border-top: 1px dashed #eee;">
                                <small class="fw-bold text-dark" style="font-size: 0.75rem;">{{
                                    $rekap[$key]['sisa_kurang'] < 0 ? 'Tagihan:' : 'Sisa:' }}</small>
                                        <span
                                            class="badge {{ $rekap[$key]['sisa_kurang'] >= 0 ? 'bg-primary' : 'bg-danger' }} rounded-pill px-2 py-1"
                                            style="font-size: 0.75rem;">
                                            {{ $rekap[$key]['sisa_kurang'] >= 0 ? '+' : '-' }} Rp {{
                                            number_format(abs($rekap[$key]['sisa_kurang']), 0, ',', '.') }}
                                        </span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="card shadow-sm border-0 mt-2" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-4 pb-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-history text-muted me-2"></i> Riwayat Transaksi Bulan Ini</h6>
                    
                    @php
                        $totalTagihanGlobal = abs(\App\Models\ResellerTransaction::where('reseller_id', $reseller->id)
                            ->where('sisa_kurang', '<', 0)
                            ->sum('sisa_kurang'));
                    @endphp
                    
                    @if($totalTagihanGlobal > 0)
                    <div class="px-3 py-2 rounded shadow bg-danger text-white d-flex align-items-center gap-3">
                        <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                            <span class="fw-bold text-white text-opacity-75" style="font-size: 0.75rem; letter-spacing: 0.5px; display: block; margin-bottom: -2px;">TOTAL KESELURUHAN TAGIHAN</span>
                            <h4 class="mb-0 fw-bolder text-white">Rp {{ number_format($totalTagihanGlobal, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                    @else
                    <div class="px-3 py-2 rounded shadow-sm bg-success text-white d-flex align-items-center gap-3">
                        <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <span class="fw-bold text-white" style="font-size: 0.85rem; letter-spacing: 0.5px;">TIDAK ADA TAGIHAN</span>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="card-body" style="overflow-x:auto;">
                    <table class="table table-hover align-middle nowrap" id="res-config" style="width: 100%">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Total Barang</th>
                                <th>Total Harga</th>
                                <th>Bayar</th>
                                <th>Sisa/Kurang</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $trx)
                            <tr style="border-bottom: 1px solid #f8f9fa;">
                                <td><span class="fw-medium">{{ date('d M Y', strtotime($trx->tgl)) }}</span></td>
                                <td>{{ $trx->total_barang }} item</td>
                                <td class="fw-bold">Rp {{ number_format($trx->total_uang, 0, ',', '.') }}</td>
                                <td class="text-success fw-bold">Rp {{ number_format($trx->bayar, 0, ',', '.') }}</td>
                                <td>
                                    @if($trx->sisa_kurang > 0)
                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">+
                                        Rp {{ number_format($trx->sisa_kurang, 0, ',', '.') }}</span>
                                    @elseif($trx->sisa_kurang < 0) <span
                                        class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">- Rp {{
                                        number_format(abs($trx->sisa_kurang), 0, ',', '.') }}</span>
                                        @else
                                        <span
                                            class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill">Lunas</span>
                                        @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button"
                                            class="btn btn-info btn-sm text-white btn-aksi btn-detail shadow-sm"
                                            data-id="{{ $trx->id }}" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <a href="{{ route('reseller_transactions.edit', $trx->id) }}"
                                            class="btn btn-warning btn-sm text-white btn-aksi shadow-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form action="{{ route('reseller_transactions.destroy', $trx->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus transaksi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm btn-aksi shadow-sm"
                                                title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @foreach($transactions as $trx)
            <template id="template-detail-{{ $trx->id }}">
                <div class="child-row-wrapper">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded d-flex align-items-center justify-content-center me-3"
                            style="width: 36px; height: 36px;">
                            <i class="fas fa-box-open fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">Rincian Barang</h6>
                            <small class="text-muted">{{ $trx->total_barang }} Item Terjual</small>
                        </div>
                    </div>

                    <div class="table-responsive bg-white rounded shadow-sm border border-light">
                        <table class="table table-sm table-hover mb-0 align-middle">
                            <thead class="bg-light text-muted"
                                style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                <tr>
                                    <th class="text-center py-2" style="width: 5%">#</th>
                                    <th class="py-2" style="width: 35%">Nama Produk</th>
                                    <th class="text-center py-2" style="width: 15%">Ukuran</th>
                                    <th class="py-2" style="width: 20%">Harga Satuan</th>
                                    <th class="text-center py-2" style="width: 10%">Qty</th>
                                    <th class="text-end py-2 pe-3" style="width: 15%">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 0.85rem;">
                                @foreach($trx->details as $detail)
                                <tr>
                                    <td class="text-center text-muted py-2">{{ $loop->iteration }}</td>
                                    <td class="fw-medium text-dark py-2">{{ $detail->barang->namabarang ?? '-' }}</td>
                                    <td class="text-center py-2">
                                        <span
                                            class="badge bg-light text-secondary border border-secondary border-opacity-25 fw-normal px-2 py-1">
                                            {{ $detail->barang->ukuran ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="py-2">Rp {{ number_format($detail->barang->hpp ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-center fw-bold text-primary py-2">{{ $detail->jumlah }}</td>
                                    <td class="text-end fw-bold text-dark py-2 pe-3">Rp {{
                                        number_format($detail->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="5" class="text-end text-muted fw-bold py-2">TOTAL KESELURUHAN:</td>
                                    <td class="text-end fw-bold text-primary py-2 pe-3">Rp {{
                                        number_format($trx->total_uang, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </template>
            @endforeach

        </div>
    </div>

    <!-- Modal Bayar Tagihan -->
    <div class="modal fade" id="payDebtModal" tabindex="-1" aria-labelledby="payDebtModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold" id="payDebtModalLabel">
                        <i class="fas fa-money-bill-wave me-2"></i> Bayar Tagihan (Otomatis)
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('reseller_transactions.pay_debt', $reseller->id) }}" method="POST" onsubmit="return confirm('Proses pembayaran tagihan ini?')">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="alert alert-info border-0 shadow-sm mb-4" style="font-size: 0.85rem;">
                            <i class="fas fa-info-circle me-2"></i>
                            Sistem akan secara otomatis memotong/melunaskan tagihan pada transaksi <strong>terlama</strong> yang belum lunas.
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Nominal Pembayaran</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold text-muted border-end-0">Rp</span>
                                <input type="number" name="nominal" class="form-control form-control-lg border-start-0 ps-0 text-success fw-bold" required min="1" placeholder="0">
                            </div>
                            <small class="text-muted mt-1 d-block mt-2">
                                * Masukkan jumlah uang tunai yang disetorkan.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 pt-2 pb-2">
                        <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success shadow-sm px-4 fw-bold">Konfirmasi Bayar <i class="fas fa-check ms-1"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(() => {
                var tableContainer = document.getElementById('res-config');
                if(!tableContainer) return;
                
                var table = $(tableContainer).DataTable();
                
                $(tableContainer).on('click', '.btn-detail', function () {
                    var btn = $(this);
                    var tr = btn.closest('tr');
                    var row = table.row( tr );
                    var trxId = btn.data('id');

                    if ( row.child.isShown() ) {
                        $('div.slider', row.child()).slideUp(300, function () {
                            row.child.hide();
                            tr.removeClass('shown');
                        });
                        btn.html('<i class="fas fa-eye"></i>').removeClass('btn-secondary').addClass('btn-info');
                    }
                    else {
                        var templateContent = $('#template-detail-' + trxId).html();
                        if(templateContent) {
                            row.child( '<div class="slider" style="display:none; padding: 0;">' + templateContent + '</div>' ).show();
                            tr.addClass('shown');
                            $('div.slider', row.child()).slideDown(300);
                            btn.html('<i class="fas fa-eye-slash"></i>').removeClass('btn-info').addClass('btn-secondary');
                        }
                    }
                });
            }, 500); 
        });
    </script>
</x-app-layout>