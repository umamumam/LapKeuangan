<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="card shadow border-0" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header border-0 text-white d-flex justify-content-between align-items-center"
                    style="background: linear-gradient(135deg, #f5a623 0%, #f7931e 100%);">
                    <h5 class="mb-0 text-white"><i class="fas fa-plus-circle me-2"></i> Tambah Transaksi Supplier</h5>
                    <div id="priceModeIndicator" class="badge bg-white text-warning py-2 px-3 shadow-sm d-none"
                        style="font-size: 0.85rem; border-radius: 20px; border: 1px solid #f5a623;">
                        <i class="fas fa-tag me-1"></i> Mode: <span id="priceModeText">Loading...</span>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('supplier_transactions.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                        @endif

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Supplier</label>
                                <input type="hidden" name="supplier_id" id="supplierSelect" value="{{ $supplier->id }}">
                                <input type="text" class="form-control bg-light text-muted"
                                    value="{{ $supplier->nama }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tanggal Transaksi</label>
                                <input type="date" name="tgl" class="form-control"
                                    value="{{ old('tgl', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Retur (Potong)</label>
                                <input type="number" name="retur" class="form-control" value="{{ old('retur', 0) }}"
                                    min="0">
                                <small class="text-muted">Isi jumlah potong barang yang diretur (opsional).</small>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label class="form-label">Bukti Transfer (Opsional)</label>
                                <input type="file" name="bukti_tf" class="form-control" accept="image/*">
                                <small class="text-muted">Upload gambar bukti transfer jika ada.</small>
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
                                            <input type="text" class="form-control fw-bold border-0 bg-transparent"
                                                id="total_uang_display" readonly value="Rp 0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="border-end-0 border-top-0"></td>
                                        <td class="text-end fw-bold align-middle">Bayar Nominal</td>
                                        <td colspan="2">
                                            <div class="input-group">
                                                <input type="number" name="bayar" class="form-control fw-bold"
                                                    id="bayar" value="{{ old('bayar', 0) }}" required min="0">
                                                <button type="button" class="btn btn-secondary" id="btn-uang-pas"
                                                    title="Uang Pas">Pas</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="border-end-0 border-top-0"></td>
                                        <td class="text-end fw-bold align-middle">Sisa / Kurang</td>
                                        <td colspan="2">
                                            <input type="text" class="form-control fw-bold border-0 bg-transparent"
                                                id="total_tagihan_display" readonly value="Rp 0">
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('supplier_transactions.show_supplier', $supplier->id) }}"
                                class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-warning text-dark fw-bold">Simpan Transaksi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <datalist id="barang-list">
        @foreach($barangs as $barang)
        <option value="{{ $barang->namabarang }}{{ $barang->ukuran ? ' - ' . $barang->ukuran : '' }}"
            data-id="{{ $barang->id }}" data-hpp="{{ $barang->hpp ?? 0 }}"
            data-beli-potong="{{ $barang->hargabeli_perpotong ?? 0 }}"
            data-beli-lusin="{{ $barang->hargabeli_perlusin ?? 0 }}" data-grosir="{{ $barang->harga_grosir ?? 0 }}"
            data-supplier-id="{{ $barang->supplier_id }}">
        </option>
        @endforeach
    </datalist>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const tableBody = document.querySelector('#detailsTable tbody');
            const addRowBtn = document.getElementById('addRowBtn');
            const supplierSelect = document.getElementById('supplierSelect');
            
            const totalDisplay = document.getElementById('total_uang_display');
            const bayarInput = document.getElementById('bayar');
            const sisaDisplay = document.getElementById('total_tagihan_display');
            const btnUangPas = document.getElementById('btn-uang-pas');

            let rowIdx = 0;
            let currentPriceMode = 'hpp';

            // Set initial UI Header & Indicator
            document.getElementById('priceModeIndicator').classList.remove('d-none');
            document.getElementById('priceModeText').textContent = 'HPP';
            document.getElementById('hargaHeader').textContent = 'HPP (Harga Beli)';


            function formatRupiah(number) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
            }

            // Gabungan Filter: Supplier & Minus-1 (menyembunyikan barang yang sudah diketik)
            function updateDatalistFilter() {
                const selectedSupplier = supplierSelect.value;
                const datalist = document.getElementById('barang-list');
                const options = datalist.querySelectorAll('option');

                // Kumpulkan semua nama barang yang ada di dalam input tabel saat ini
                const usedItems = [];
                document.querySelectorAll('.barang-input').forEach(input => {
                    if (input.value.trim() !== "") {
                        usedItems.push(input.value.trim());
                    }
                });

                options.forEach(opt => {
                    const supplierId = opt.getAttribute('data-supplier-id');
                    const isUsed = usedItems.includes(opt.value);

                    // Hanya aktifkan jika barang cocok dengan supplier ini (atau umum/null) DAN belum terpakai di baris lain
                    if ((supplierId === selectedSupplier || !supplierId) && !isUsed) {
                        opt.disabled = false;
                    } else {
                        opt.disabled = true; // Menyembunyikan dari dropdown Datalist
                    }
                });
            }

            // Panggil awal
            updateDatalistFilter();

            function calculateTotals() {
                let total = 0;
                document.querySelectorAll('.subtotal-input').forEach(input => {
                    total += parseFloat(input.value) || 0;
                });

                totalDisplay.value = formatRupiah(total);

                const bayar = parseFloat(bayarInput.value) || 0;
                const sisa = bayar - total;

                sisaDisplay.value = (sisa < 0 ? '- ' : '') + formatRupiah(Math.abs(sisa));

                if (sisa > 0) {
                    sisaDisplay.classList.remove('text-danger', 'text-secondary');
                    sisaDisplay.classList.add('text-success');
                } else if (sisa < 0) {
                    sisaDisplay.classList.remove('text-success', 'text-secondary');
                    sisaDisplay.classList.add('text-danger');
                } else {
                    sisaDisplay.classList.remove('text-success', 'text-danger');
                    sisaDisplay.classList.add('text-secondary');
                }
            }

            btnUangPas.addEventListener('click', function() {
                let total = 0;
                document.querySelectorAll('.subtotal-input').forEach(input => total += parseFloat(input.value) || 0);
                bayarInput.value = total;
                calculateTotals();
            });

            function addRow() {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>
                        <input list="barang-list" class="form-control barang-input" placeholder="Cari atau ketik barang..." required>
                        <input type="hidden" name="details[${rowIdx}][barang_id]" class="barang-id-hidden">
                    </td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-text bg-light">Rp</span>
                            <input type="text" class="form-control harga-display bg-light" readonly value="0">
                        </div>
                    </td>
                    <td>
                        <input type="number" name="details[${rowIdx}][jumlah]" class="form-control jumlah-input" value="1" required min="1">
                    </td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-text bg-light">Rp</span>
                            <input type="text" class="form-control subtotal-display bg-light" readonly value="0">
                            <input type="hidden" name="details[${rowIdx}][subtotal]" class="subtotal-input" value="0">
                        </div>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm remove-row-btn"><i class="fas fa-times"></i></button>
                    </td>
                `;
                tableBody.appendChild(tr);
                rowIdx++;

                const barangInput = tr.querySelector('.barang-input');
                const barangIdHidden = tr.querySelector('.barang-id-hidden');
                const hargaDisplay = tr.querySelector('.harga-display');
                const jumlahInput = tr.querySelector('.jumlah-input');
                const subtotalDisplay = tr.querySelector('.subtotal-display');
                const subtotalInput = tr.querySelector('.subtotal-input');
                const removeBtn = tr.querySelector('.remove-row-btn');

                function updateRow() {
                    const val = barangInput.value;
                    const datalist = document.getElementById('barang-list');
                    const option = Array.from(datalist.options).find(opt => opt.value === val);

                    let harga = 0;

                    if (option) {
                        barangIdHidden.value = option.getAttribute('data-id');

                        if (currentPriceMode === 'hpp') {
                            harga = parseFloat(option.getAttribute('data-hpp')) || 0;
                        } else if (currentPriceMode === 'beli_potong') {
                            harga = parseFloat(option.getAttribute('data-beli-potong')) || 0;
                        } else if (currentPriceMode === 'beli_lusin') {
                            harga = parseFloat(option.getAttribute('data-beli-lusin')) || 0;
                        } else if (currentPriceMode === 'grosir') {
                            harga = parseFloat(option.getAttribute('data-grosir')) || 0;
                        }
                    } else {
                        barangIdHidden.value = "";
                    }

                    const jumlah = parseFloat(jumlahInput.value) || 0;
                    const subtotal = harga * jumlah;

                    hargaDisplay.value = new Intl.NumberFormat('id-ID').format(harga);
                    subtotalDisplay.value = new Intl.NumberFormat('id-ID').format(subtotal);
                    subtotalInput.value = subtotal;

                    calculateTotals();
                    updateDatalistFilter(); // Trigger supaya barang yang baru dipilih otomatis hilang dari pilihan lain
                }

                barangInput.addEventListener('input', updateRow);
                jumlahInput.addEventListener('input', updateRow);
                
                removeBtn.addEventListener('click', function() {
                    tr.remove();
                    calculateTotals();
                    updateDatalistFilter(); // Bebaskan kembali barang yang dihapus agar muncul lagi di datalist
                });
            }

            addRowBtn.addEventListener('click', addRow);
            bayarInput.addEventListener('input', calculateTotals);

            // Add first row by default
            addRow();
        });
    </script>
</x-app-layout>