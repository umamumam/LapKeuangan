<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-file-invoice text-info"></i> Detail Transaksi Reseller</h5>
                    <a href="{{ route('reseller_transactions.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <h6 class="text-uppercase text-muted mb-2">Informasi Transaksi</h6>
                            <table class="table table-sm table-borderless">
                                <tbody>
                                    <tr>
                                        <td style="width: 150px"><strong>Tanggal</strong></td>
                                        <td>: {{ date('d F Y', strtotime($resellerTransaction->tgl)) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Reseller</strong></td>
                                        <td>: <span class="badge bg-light-primary text-primary">{{ $resellerTransaction->reseller->nama ?? '-' }}</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-sm-6">
                            <h6 class="text-uppercase text-muted mb-2">Ringkasan Pembayaran</h6>
                            <table class="table table-sm table-borderless">
                                <tbody>
                                    <tr>
                                        <td style="width: 150px"><strong>Total Harga</strong></td>
                                        <td>: Rp {{ number_format($resellerTransaction->total_uang) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Bayar</strong></td>
                                        <td>: Rp {{ number_format($resellerTransaction->bayar) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Sisa / Kurang</strong></td>
                                        <td>: 
                                            @if($resellerTransaction->sisa_kurang > 0)
                                                <span class="text-success fw-bold">+ Rp {{ number_format($resellerTransaction->sisa_kurang) }}</span> (Kembalian)
                                            @elseif($resellerTransaction->sisa_kurang < 0)
                                                <span class="text-danger fw-bold">- Rp {{ number_format(abs($resellerTransaction->sisa_kurang)) }}</span> (Kurang)
                                            @else
                                                <span class="badge bg-light-secondary text-secondary">Lunas</span>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <hr>
                    <h6 class="mb-3">Daftar Barang (Total: {{ $resellerTransaction->total_barang }} item)</h6>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" style="width: 100%">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px">#</th>
                                    <th>Nama Barang</th>
                                    <th>Ukuran</th>
                                    <th>Harga Ptg</th>
                                    <th>Jumlah</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($resellerTransaction->details as $detail)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $detail->barang->namabarang ?? '-' }}</td>
                                    <td>{{ $detail->barang->ukuran ?? '-' }}</td>
                                    <td>Rp {{ number_format($detail->barang->hpp ?? 0) }}</td>
                                    <td>{{ $detail->jumlah }}</td>
                                    <td class="text-end fw-bold">Rp {{ number_format($detail->subtotal) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-primary">
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Total Keseluruhan</td>
                                    <td class="fw-bold">{{ $resellerTransaction->total_barang }}</td>
                                    <td class="text-end fw-bold">Rp {{ number_format($resellerTransaction->total_uang) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
