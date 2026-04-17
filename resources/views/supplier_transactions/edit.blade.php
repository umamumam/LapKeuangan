<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="card shadow border-0" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header border-0 text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <h5 class="mb-0 text-white"><i class="fas fa-edit me-2"></i> Edit Transaksi Supplier</h5>
                    <div class="d-flex align-items-center gap-2">
                        <span id="priceModeIndicator" class="badge bg-white text-danger py-2 px-3 shadow-sm" style="font-size: 0.82rem; border-radius: 20px; border: 1px solid #f5576c;">
                            <i class="fas fa-tag me-1"></i> Mode: <span id="priceModeText">Default</span>
                        </span>
                        <button type="button" id="btnPilihHarga" class="btn btn-sm bg-white text-danger fw-bold shadow-sm" style="border-radius: 20px; border: 1px solid #f5576c;">
                            <i class="fas fa-sync-alt me-1"></i> Pilih Mode Harga
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('supplier_transactions.update', $supplierTransaction->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                        @endif

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Supplier</label>
                                <input type="hidden" name="supplier_id" id="supplierSelect" value="{{ $supplier->id }}">
                                <input type="text" class="form-control bg-light text-muted" value="{{ $supplier->nama }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tanggal Transaksi</label>
                                <input type="date" name="tgl" class="form-control" value="{{ old('tgl', $supplierTransaction->tgl) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Retur (Potong)</label>
                                <input type="number" name="retur" class="form-control" value="{{ old('retur', $supplierTransaction->retur) }}" min="0">
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label class="form-label">Bukti Transfer (Opsional)</label>
                                @if($supplierTransaction->bukti_tf)
                                <div class="mb-2">
                                    <a href="{{ asset('storage/' . $supplierTransaction->bukti_tf) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $supplierTransaction->bukti_tf) }}" alt="Bukti TF" class="img-thumbnail" style="max-height: 150px;">
                                    </a>
                                </div>
                                @endif
                                <input type="file" name="bukti_tf" class="form-control" accept="image/*">
                                <small class="text-muted">Upload gambar baru untuk mengganti yang lama.</small>
                            </div>
                        </div>

                        <hr>
                        <h6 class="mb-3">Detail Barang</h6>

                        <div class="table-responsive">
                            <table class="table table-bordered" id="detailsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Barang</th>
                                        <th style="width: 250px" id="hargaHeader">HPP (Harga Beli)</th>
                                        <th style="width: 150px">Jumlah</th>
                                        <th style="width: 250px">Subtotal</th>
                                        <th style="width: 80px" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($supplierTransaction->details as $index => $detail)
                                    <tr>
                                        <td>
                                            <select name="details[{{ $index }}][barang_id]" class="form-select barang-select" required>
                                                <option value="">-- Pilih Barang --</option>
                                                @foreach($barangs as $barang)
                                                <option value="{{ $barang->id }}"
                                                    data-hpp="{{ $barang->hpp ?? 0 }}"
                                                    data-beli-potong="{{ $barang->hargabeli_perpotong ?? 0 }}"
                                                    data-beli-lusin="{{ $barang->hargabeli_perlusin ?? 0 }}"
                                                    data-grosir="{{ $barang->harga_grosir ?? 0 }}"
                                                    data-supplier-id="{{ $barang->supplier_id }}"
                                                    {{ $detail->barang_id == $barang->id ? 'selected' : '' }}>
                                                    {{ $barang->namabarang }}{{ $barang->ukuran ? ' - ' . $barang->ukuran : '' }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light">Rp</span>
                                                <input type="text" class="form-control harga-display bg-light" readonly value="{{ number_format($detail->subtotal / ($detail->jumlah ?: 1), 0, ',', '.') }}">
                                            </div>
                                        </td>
                                        <td>
                                            <input type="number" name="details[{{ $index }}][jumlah]" class="form-control jumlah-input" value="{{ $detail->jumlah }}" required min="1">
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light">Rp</span>
                                                <input type="text" class="form-control subtotal-display bg-light" readonly value="{{ number_format($detail->subtotal, 0, ',', '.') }}">
                                                <input type="hidden" name="details[{{ $index }}][subtotal]" class="subtotal-input" value="{{ $detail->subtotal }}">
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-sm remove-row-btn"><i class="fas fa-times"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" class="align-middle border-end-0">
                                            <button type="button" class="btn btn-info btn-sm text-white" id="addRowBtn">
                                                <i class="fas fa-plus"></i> Tambah Baris
                                            </button>
                                        </td>
                                        <td class="text-end fw-bold align-middle border-start-0">Total Tagihan</td>
                                        <td colspan="2">
                                            <input type="text" class="form-control fw-bold border-0 bg-transparent" id="total_uang_display" readonly value="Rp 0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="border-end-0 border-top-0"></td>
                                        <td class="text-end fw-bold align-middle">Bayar Nominal</td>
                                        <td colspan="2">
                                            <div class="input-group">
                                                <input type="number" name="bayar" class="form-control fw-bold" id="bayar" value="{{ old('bayar', $supplierTransaction->bayar) }}" required min="0">
                                                <button type="button" class="btn btn-secondary" id="btn-uang-pas">Pas</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="border-end-0 border-top-0"></td>
                                        <td class="text-end fw-bold align-middle">Sisa / Kurang</td>
                                        <td colspan="2">
                                            <input type="text" class="form-control fw-bold border-0 bg-transparent" id="total_tagihan_display" readonly value="Rp 0">
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('supplier_transactions.show_supplier', $supplier->id) }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-warning text-dark fw-bold">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="d-none" id="barangOptionsMaster">
        <option value="">-- Pilih Barang --</option>
        @foreach($barangs as $barang)
        <option value="{{ $barang->id }}"
            data-hpp="{{ $barang->hpp ?? 0 }}"
            data-beli-potong="{{ $barang->hargabeli_perpotong ?? 0 }}"
            data-beli-lusin="{{ $barang->hargabeli_perlusin ?? 0 }}"
            data-grosir="{{ $barang->harga_grosir ?? 0 }}"
            data-supplier-id="{{ $barang->supplier_id }}">
            {{ $barang->namabarang }}{{ $barang->ukuran ? ' - ' . $barang->ukuran : '' }}
        </option>
        @endforeach
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const tableBody = document.querySelector('#detailsTable tbody');
            const addRowBtn = document.getElementById('addRowBtn');
            const supplierIdValue = '{{ $supplier->id }}';
            const barangOptionsMaster = document.getElementById('barangOptionsMaster').querySelectorAll('option');
            const totalDisplay = document.getElementById('total_uang_display');
            const bayarInput = document.getElementById('bayar');
            const sisaDisplay = document.getElementById('total_tagihan_display');
            const btnUangPas = document.getElementById('btn-uang-pas');
            const btnPilihHarga = document.getElementById('btnPilihHarga');

            let rowIdx = {{ $supplierTransaction->details->count() }};
            // Default mode = hpp (harga beli)
            let currentPriceMode = 'hpp';

            const modeLabels = {
                'hpp': 'HPP',
                'beli_potong': 'Harga Beli Per Potong',
                'beli_lusin': 'Harga Beli Per Lusin',
                'grosir': 'Harga Grosir',
            };

            function getFilteredOptions() {
                let optionsHtml = '';
                barangOptionsMaster.forEach(opt => {
                    if (opt.value === "") { optionsHtml += opt.outerHTML; return; }
                    // Supplier: hanya barang milik supplier ini
                    if (opt.getAttribute('data-supplier-id') === supplierIdValue) {
                        optionsHtml += opt.outerHTML;
                    }
                });
                return optionsHtml;
            }

            function formatRupiah(n) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n);
            }

            function calculateTotals() {
                let total = 0;
                document.querySelectorAll('.subtotal-input').forEach(i => total += parseFloat(i.value) || 0);
                totalDisplay.value = formatRupiah(total);
                const bayar = parseFloat(bayarInput.value) || 0;
                const sisa = bayar - total;
                sisaDisplay.value = (sisa < 0 ? '- ' : '') + formatRupiah(Math.abs(sisa));
                if (sisa > 0) { sisaDisplay.classList.remove('text-danger', 'text-secondary'); sisaDisplay.classList.add('text-success'); }
                else if (sisa < 0) { sisaDisplay.classList.remove('text-success', 'text-secondary'); sisaDisplay.classList.add('text-danger'); }
                else { sisaDisplay.classList.remove('text-success', 'text-danger'); sisaDisplay.classList.add('text-secondary'); }
            }

            function getHargaFromOption(option) {
                if (currentPriceMode === 'hpp') return parseFloat(option.getAttribute('data-hpp')) || 0;
                if (currentPriceMode === 'beli_potong') return parseFloat(option.getAttribute('data-beli-potong')) || 0;
                if (currentPriceMode === 'beli_lusin') return parseFloat(option.getAttribute('data-beli-lusin')) || 0;
                if (currentPriceMode === 'grosir') return parseFloat(option.getAttribute('data-grosir')) || 0;
                return 0;
            }

            function initRowEvents(tr) {
                const select = tr.querySelector('.barang-select');
                const hargaDisplay = tr.querySelector('.harga-display');
                const jumlahInput = tr.querySelector('.jumlah-input');
                const subtotalDisplay = tr.querySelector('.subtotal-display');
                const subtotalInput = tr.querySelector('.subtotal-input');
                const removeBtn = tr.querySelector('.remove-row-btn');

                function updateRow() {
                    const option = select.options[select.selectedIndex];
                    const harga = getHargaFromOption(option);
                    const jumlah = parseFloat(jumlahInput.value) || 0;
                    const subtotal = harga * jumlah;
                    hargaDisplay.value = new Intl.NumberFormat('id-ID').format(harga);
                    subtotalDisplay.value = new Intl.NumberFormat('id-ID').format(subtotal);
                    subtotalInput.value = subtotal;
                    calculateTotals();
                }

                select.addEventListener('change', updateRow);
                jumlahInput.addEventListener('input', updateRow);
                removeBtn.addEventListener('click', () => { tr.remove(); calculateTotals(); });
            }

            function addRow() {
                const optionsHtml = getFilteredOptions();
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><select name="details[${rowIdx}][barang_id]" class="form-select barang-select" required>${optionsHtml}</select></td>
                    <td><div class="input-group"><span class="input-group-text bg-light">Rp</span><input type="text" class="form-control harga-display bg-light" readonly value="0"></div></td>
                    <td><input type="number" name="details[${rowIdx}][jumlah]" class="form-control jumlah-input" value="1" required min="1"></td>
                    <td><div class="input-group"><span class="input-group-text bg-light">Rp</span><input type="text" class="form-control subtotal-display bg-light" readonly value="0"><input type="hidden" name="details[${rowIdx}][subtotal]" class="subtotal-input" value="0"></div></td>
                    <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row-btn"><i class="fas fa-times"></i></button></td>
                `;
                tableBody.appendChild(tr);
                rowIdx++;
                initRowEvents(tr);
            }

            // Tombol pilih mode harga (tidak auto popup)
            btnPilihHarga.addEventListener('click', function() {
                Swal.fire({
                    title: 'Pilih Mode Harga',
                    html: `
                        <p>Pilih jenis harga. Data subtotal akan dihitung ulang.</p>
                        <div class="d-grid gap-2 mt-3">
                            <button type="button" class="btn btn-outline-primary price-mode-btn" data-mode="hpp">HPP</button>
                            <button type="button" class="btn btn-outline-primary price-mode-btn" data-mode="beli_potong">Harga Beli Per Potong</button>
                            <button type="button" class="btn btn-outline-primary price-mode-btn" data-mode="beli_lusin">Harga Beli Per Lusin</button>
                            <button type="button" class="btn btn-outline-primary price-mode-btn" data-mode="grosir">Harga Grosir</button>
                        </div>
                    `,
                    showConfirmButton: false,
                    allowOutsideClick: true,
                    didOpen: () => {
                        Swal.getHtmlContainer().querySelectorAll('.price-mode-btn').forEach(btn => {
                            btn.addEventListener('click', () => {
                                currentPriceMode = btn.getAttribute('data-mode');
                                Swal.close();
                                document.getElementById('priceModeText').textContent = modeLabels[currentPriceMode] || currentPriceMode;
                                document.getElementById('hargaHeader').textContent = modeLabels[currentPriceMode] || currentPriceMode;
                                document.querySelectorAll('#detailsTable tbody tr').forEach(tr => {
                                    const sel = tr.querySelector('.barang-select');
                                    if (sel) sel.dispatchEvent(new Event('change'));
                                });
                                Swal.fire({ toast: true, position: 'top-end', icon: 'info', title: 'Mode: ' + (modeLabels[currentPriceMode] || currentPriceMode), showConfirmButton: false, timer: 2500 });
                            });
                        });
                    }
                });
            });

            btnUangPas.addEventListener('click', function() {
                let total = 0;
                document.querySelectorAll('.subtotal-input').forEach(i => total += parseFloat(i.value) || 0);
                bayarInput.value = total;
                calculateTotals();
            });

            addRowBtn.addEventListener('click', addRow);
            bayarInput.addEventListener('input', calculateTotals);

            // Rebuild semua select yang sudah ada dengan opsi terfilter (konsisten dengan create)
            document.querySelectorAll('#detailsTable tbody tr').forEach(tr => {
                const select = tr.querySelector('.barang-select');
                if (select) {
                    const currentValue = select.value;
                    select.innerHTML = getFilteredOptions();
                    if (currentValue && select.querySelector(`option[value="${currentValue}"]`)) {
                        select.value = currentValue;
                    }
                }
            });

            document.querySelectorAll('#detailsTable tbody tr').forEach(tr => initRowEvents(tr));
            calculateTotals();
        });
    </script>
</x-app-layout>
