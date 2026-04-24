<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">

            <style>
                .hover-card {
                    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
                    text-decoration: none;
                }

                .hover-card:hover {
                    transform: translateY(-5px) scale(1.02);
                    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15) !important;
                }
            </style>

            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                <div>
                    <h4 class="mb-0 fw-bold text-dark"><i class="fas fa-truck-loading text-success me-2"></i> Transaksi
                        Supplier</h4>
                </div>
            </div>

            @php
            $suppGradients = [
            'linear-gradient(135deg, #ff7e5f 0%, #feb47b 100%)', // Peach/Orange
            'linear-gradient(135deg, #ee0979 0%, #ff6a00 100%)', // Deep Orange/Red
            'linear-gradient(135deg, #F09819 0%, #EDDE5D 100%)', // Yellow/Orange
            'linear-gradient(135deg, #f12711 0%, #f5af19 100%)', // Fire
            ];

            $activeGradient = 'linear-gradient(135deg, #212529 0%, #212529 100%)'; // default dark
            if(isset($supplier)) {
            $activeSupplierIndex = $suppliers->search(function($item) use ($supplierId) {
            return $item->id == $supplierId;
            });
            if($activeSupplierIndex !== false) {
            $activeGradient = $suppGradients[$activeSupplierIndex % count($suppGradients)];
            }
            }
            @endphp

            <!-- Supplier Selection Cards -->
            <div class="row mb-4">
                @foreach($suppliers as $index => $sup)
                @php
                $bgSuppGradient = $suppGradients[$index % count($suppGradients)];
                $isActive = ($supplierId == $sup->id);
                @endphp
                <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3">
                    <a href="{{ route('supplier_transactions.index', ['supplier_id' => $sup->id]) }}"
                        class="text-decoration-none h-100">
                        <div class="card h-100 border-0 shadow-sm hover-card"
                            style="border-radius: 10px; background: {{ $bgSuppGradient }}; position: relative; overflow: hidden; min-height: 80px; 
                                   {{ $isActive ? 'transform: scale(1.03); box-shadow: 0 8px 15px rgba(0,0,0,0.2) !important; border: 2px solid white !important;' : 'opacity: 0.85;' }}">

                            <!-- Decorative abstract circle -->
                            <div
                                style="position: absolute; right: -20px; top: -20px; width: 80px; height: 80px; border-radius: 50%; background: rgba(255,255,255,0.08);">
                            </div>

                            <div
                                class="card-body position-relative z-1 d-flex align-items-center justify-content-center p-2 text-white h-100">
                                <h6 class="mb-0 fw-bolder text-center text-white" style="letter-spacing: -0.5px;">
                                    {{ strtoupper($sup->nama) }}
                                </h6>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>

            @if(isset($supplier))
            <!-- Single Main Card container for the Supplier Form & Table -->
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-body">

                    <!-- Header & Modals trigger buttons -->
                    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                        <div>
                            <h4 class="mb-1 fw-bold text-primary">{{ $supplier->nama }}</h4>
                            <p class="mb-0 text-muted small">Kelola nota belanja dan transaksi pembayaran</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm fw-bold shadow-sm"
                                data-bs-toggle="modal" data-bs-target="#modalSisaNota">
                                <i class="fas fa-edit me-1"></i> Sisa Nota Sebelumnya
                            </button>
                            <button type="button" class="btn btn-danger btn-sm fw-bold shadow-sm" data-bs-toggle="modal"
                                data-bs-target="#modalTf">
                                <i class="fas fa-money-bill-wave me-1"></i> Input TF (Bayar)
                            </button>
                        </div>
                    </div>

                    <!-- Form Input Barang -->
                    <form action="{{ route('supplier_transactions.store') }}" method="POST"
                        class="row gx-2 gy-3 align-items-end mb-4">
                        @csrf
                        <input type="hidden" name="supplier_id" value="{{ $supplier->id }}">

                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control form-control-sm"
                                value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label small fw-bold">Lsn</label>
                            <input type="number" name="lusin" id="lusin" class="form-control form-control-sm" min="0"
                                value="0" step="any">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label small fw-bold">Ptg</label>
                            <input type="number" name="potong" id="potong" class="form-control form-control-sm" min="0"
                                value="0" step="any">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Nama Barang</label>
                            <input type="text" name="nama_barang" class="form-control form-control-sm"
                                placeholder="Ketik nama barang..." required autocomplete="off">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">@Harga (Lsn)</label>
                            <input type="number" name="harga" id="harga" class="form-control form-control-sm" min="0"
                                required placeholder="Rp">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Jumlah</label>
                            <input type="text" id="jumlah_auto"
                                class="form-control form-control-sm bg-light text-primary fw-bold" readonly
                                placeholder="Rp 0">
                        </div>
                        <div class="col-md-12 mt-3 text-end">
                            <button type="submit" class="btn btn-success btn-sm fw-bold px-4"><i
                                    class="fas fa-plus"></i> Tambah</button>
                        </div>
                    </form>

                    <!-- Table Preview List -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0" id="nota-table">
                            <thead class="text-center text-white"
                                style="font-size: 0.85rem; background: {{ $activeGradient }};">
                                <tr>
                                    <th style="width: 10%;">Tanggal</th>
                                    <th colspan="2" style="width: 10%;">Banyaknya<br><small>(Ls | Ptg)</small></th>
                                    <th style="width: 25%;">Nama Barang</th>
                                    <th style="width: 11%;">@Harga</th>
                                    <th style="width: 11%;">Jumlah</th>
                                    <th style="width: 11%;">TF</th>
                                    <th style="width: 17%;">Tagihan</th>
                                    <th style="width: 5%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($supplier->hutang_awal != 0)
                                <tr class="table-warning">
                                    <td colspan="7" class="text-end fw-bold">Sisa Nota Sebelumnya</td>
                                    <td class="text-end fw-bold text-danger" style="font-size: 1.05rem;">Rp {{
                                        number_format($supplier->hutang_awal, 0, ',', '.') }}</td>
                                    <td></td>
                                </tr>
                                @endif

                                @forelse($groupedTransactions as $date => $group)
                                @php
                                $rowcount = count($group['items']);
                                $rowspan = $rowcount > 0 ? $rowcount : 1;
                                @endphp

                                @foreach($group['items'] as $index => $item)
                                <tr>
                                    @if($index === 0)
                                    <td rowspan="{{ $rowspan }}" class="align-middle text-center fw-bold bg-light">{{
                                        \Carbon\Carbon::parse($date)->format('d/m/y') }}</td>
                                    @endif

                                    <td class="text-center">{{ $item->lusin ?: '' }}</td>
                                    <td class="text-center">{{ $item->potong ?: '' }}</td>
                                    <td>
                                        {{ $item->nama_barang }}
                                        @if($item->tf > 0 && $item->jumlah == 0)
                                        <span class="badge bg-danger ms-1">TF</span>
                                        @endif
                                    </td>
                                    <td class="text-end text-muted">{{ $item->harga > 0 ? number_format($item->harga, 0,
                                        ',', '.') : '-' }}</td>
                                    <td class="text-end fw-bold">{{ $item->jumlah > 0 ? number_format($item->jumlah, 0,
                                        ',', '.') : '-' }}</td>

                                    @if($index === 0)
                                    <td rowspan="{{ $rowspan }}"
                                        class="align-middle text-end text-danger fw-bold bg-light">{{ $group['sum_tf'] >
                                        0 ? number_format($group['sum_tf'], 0, ',', '.') : '-' }}</td>
                                    <td rowspan="{{ $rowspan }}"
                                        class="align-middle text-end fw-bold text-primary bg-light"
                                        style="font-size: 1.05rem;">{{ number_format($group['tagihan'], 0, ',', '.') }}
                                    </td>
                                    @endif

                                    <td class="text-center align-middle">
                                        <form action="{{ route('supplier_transactions.destroy', $item->id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger p-1 border-0"
                                                onclick="return confirm('Hapus data ({{ $item->nama_barang }}) ini?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">Belum ada transaksi di nota ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if(count($groupedTransactions) > 0)
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="7" class="text-end fw-bold">TOTAL TAGIHAN AKHIR</td>
                                    <td class="text-end fw-bold text-danger" style="font-size: 1.2rem;">
                                        @php
                                        $lastGroup = end($groupedTransactions);
                                        $finalTagihan = $lastGroup ? $lastGroup['tagihan'] : $supplier->hutang_awal;
                                        @endphp
                                        Rp {{ number_format($finalTagihan, 0, ',', '.') }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal Sisa Nota -->
            <div class="modal fade" id="modalSisaNota" tabindex="-1">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <form action="{{ route('supplier_transactions.update_sisa') }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title fw-bold">Update Sisa Nota</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="supplier_id" value="{{ $supplier->id }}">
                                <label class="form-label small fw-bold">Sisa Nota / Tagihan Awal</label>
                                <input type="number" name="sisa_nota" class="form-control"
                                    value="{{ number_format($supplier->hutang_awal, 0, '', '') }}" required>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary btn-sm w-100">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal TF -->
            <div class="modal fade" id="modalTf" tabindex="-1">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <form action="{{ route('supplier_transactions.store_tf') }}" method="POST">
                            @csrf
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title fw-bold">Input Transfer</h5>
                                <button type="button" class="btn-close btn-close-white"
                                    data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="supplier_id" value="{{ $supplier->id }}">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Tanggal TF</label>
                                    <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}"
                                        required>
                                </div>
                                <div>
                                    <label class="form-label small fw-bold">Nominal TF</label>
                                    <input type="number" name="tf" class="form-control" min="1" placeholder="Rp"
                                        required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-danger btn-sm w-100">Simpan TF</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const lusin = document.getElementById('lusin');
                    const potong = document.getElementById('potong');
                    const harga = document.getElementById('harga');
                    const jumlahAuto = document.getElementById('jumlah_auto');

                    function calculate() {
                        const l = parseFloat(lusin.value) || 0;
                        const p = parseFloat(potong.value) || 0;
                        const h = parseFloat(harga.value) || 0;
                        
                        const total = (l * h) + (p * (h / 12));
                        jumlahAuto.value = 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.floor(total));
                    }

                    if(lusin && potong && harga) {
                        lusin.addEventListener('input', calculate);
                        potong.addEventListener('input', calculate);
                        harga.addEventListener('input', calculate);
                    }
                });
            </script>
            @endif
        </div>
    </div>
</x-app-layout>