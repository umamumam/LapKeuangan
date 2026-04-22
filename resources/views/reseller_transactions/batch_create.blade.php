<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="card shadow border-0" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header border-0 text-white d-flex justify-content-between align-items-center"
                    style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
                    <div>
                        <h5 class="mb-0 text-white"><i class="fas fa-layer-group me-2"></i> Input Batch Transaksi Reseller</h5>
                        <small class="text-white text-opacity-75">Input jumlah barang per tanggal (7 Hari Sekali)</small>
                    </div>
                    <div class="badge bg-white text-primary py-2 px-3 shadow-sm"
                        style="font-size: 0.85rem; border-radius: 20px; border: 1px solid #4e73df;">
                        <i class="fas fa-tag me-1"></i> Harga: <b>GROSIR</b>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('reseller_transactions.batch_store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="reseller_id" value="{{ $reseller->id }}">

                        <div class="mb-4 d-flex justify-content-between align-items-center bg-light p-3 rounded">
                            <div>
                                <h6 class="mb-0 fw-bold">Reseller: {{ $reseller->nama }}</h6>
                                <p class="text-muted mb-0 small">Silakan isi jumlah barang pada kolom tanggal yang sesuai.</p>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-soft-primary text-primary border border-primary px-3 py-2">
                                    <i class="fas fa-info-circle me-1"></i> Data Belum Disimpan
                                </span>
                            </div>
                        </div>

                        <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                            <table class="table table-bordered table-hover sticky-header" id="batchTable">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th rowspan="2" class="align-middle" style="min-width: 250px; position: sticky; left: 0; background: #f8f9fa; z-index: 10;">Nama Barang</th>
                                        <th rowspan="2" class="align-middle" style="min-width: 120px;">Harga Grosir (Rp)</th>
                                        <th colspan="5">Jumlah Keluar (Pcs)</th>
                                        <th rowspan="2" class="align-middle" style="min-width: 150px;">Total Baris (Rp)</th>
                                    </tr>
                                    <tr>
                                        @foreach($dates as $date)
                                        <th style="min-width: 120px;">
                                            <span class="d-block small text-muted">{{ $date->format('M Y') }}</span>
                                            {{ $date->format('d/m/y') }}
                                        </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($barangs as $barang)
                                    <tr>
                                        <td class="fw-bold bg-light" style="position: sticky; left: 0; background: #f8f9fa; z-index: 5;">
                                            {{ $barang->namabarang }}
                                            @if($barang->ukuran)
                                            <span class="badge bg-secondary ms-1" style="font-size: 0.7rem;">{{ $barang->ukuran }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end price-val" data-price="{{ $barang->harga_grosir ?? 0 }}">
                                            Rp {{ number_format($barang->harga_grosir ?? 0, 0, ',', '.') }}
                                        </td>
                                        @foreach($dates as $date)
                                        <td class="p-1">
                                            <input type="number" 
                                                name="data[{{ $date->format('Y-m-d') }}][{{ $barang->id }}]" 
                                                class="form-control form-control-sm text-center qty-input" 
                                                data-date="{{ $date->format('Y-m-d') }}"
                                                data-barang-id="{{ $barang->id }}"
                                                min="0"
                                                value="0">
                                        </td>
                                        @endforeach
                                        <td class="text-end fw-bold row-total bg-light" style="font-size: 1.1em;">Rp 0</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr class="fw-bold">
                                        <td colspan="2" class="text-center">Total Penjualan Per Tanggal</td>
                                        @foreach($dates as $date)
                                        <td class="text-end col-total-{{ $date->format('Y-m-d') }}">Rp 0</td>
                                        @endforeach
                                        <td class="text-end text-primary" id="grandTotal" style="font-size: 1.25em;">Rp 0</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('reseller_transactions.show_reseller', $reseller->id) }}"
                                class="btn btn-secondary px-4">Batal</a>
                            <button type="submit" class="btn btn-primary fw-bold px-4 shadow">
                                <i class="fas fa-save me-2"></i> Simpan Semua Transaksi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .sticky-header thead th {
            position: sticky;
            top: 0;
            background: #f8f9fa;
            z-index: 10;
            box-shadow: inset 0 -1px 0 #dee2e6;
        }
        .qty-input:focus {
            background-color: #f0f7ff;
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        .qty-input[value="0"] {
            color: #ddd;
        }
        .qty-input:not([value="0"]) {
            font-weight: bold;
            color: #224abe;
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const inputs = document.querySelectorAll('.qty-input');
            const grandTotalEl = document.getElementById('grandTotal');

            function formatRupiah(number) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
            }

            function updateTotals() {
                let grandTotal = 0;
                const colTotals = {};

                // Reset Col Totals
                document.querySelectorAll('[class^="col-total-"]').forEach(el => {
                    const date = el.className.split('col-total-')[1];
                    colTotals[date] = 0;
                });

                document.querySelectorAll('#batchTable tbody tr').forEach(row => {
                    const price = parseFloat(row.querySelector('.price-val').dataset.price) || 0;
                    let rowSumQty = 0;
                    
                    row.querySelectorAll('.qty-input').forEach(input => {
                        const qty = parseFloat(input.value) || 0;
                        const date = input.dataset.date;
                        
                        const subtotal = qty * price;
                        colTotals[date] += subtotal;
                        rowSumQty += qty;

                        // Styling for non-zero inputs
                        if (qty > 0) {
                            input.style.fontWeight = 'bold';
                            input.style.color = '#224abe';
                        } else {
                            input.style.fontWeight = 'normal';
                            input.style.color = '#ddd';
                        }
                    });

                    const rowTotal = rowSumQty * price;
                    row.querySelector('.row-total').textContent = formatRupiah(rowTotal);
                    grandTotal += rowTotal;
                });

                // Update Column Totals in Footer
                for (const date in colTotals) {
                    const el = document.querySelector('.col-total-' + date);
                    if (el) el.textContent = formatRupiah(colTotals[date]);
                }

                grandTotalEl.textContent = formatRupiah(grandTotal);
            }

            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    if (this.value < 0) this.value = 0;
                    updateTotals();
                });
                
                input.addEventListener('focus', function() {
                    this.select();
                });
            });

            // Initial calculation
            updateTotals();
        });
    </script>
</x-app-layout>
