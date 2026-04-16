<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="card shadow border-0" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header border-0 text-white" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
                    <h5 class="mb-0 text-white"><i class="fas fa-plus-circle me-2"></i> Tambah Transaksi Reseller</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('reseller_transactions.store') }}" method="POST">
                        @csrf

                        @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Reseller</label>
                                <input type="hidden" name="reseller_id" id="resellerSelect" value="{{ $reseller->id }}">
                                <input type="text" class="form-control bg-light text-muted" value="{{ $reseller->nama }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Transaksi</label>
                                <input type="date" name="tgl" class="form-control"
                                    value="{{ old('tgl', date('Y-m-d')) }}" required>
                            </div>
                        </div>

                        <hr>
                        <h6 class="mb-3">Detail Barang</h6>

                        <div class="table-responsive">
                            <table class="table table-bordered" id="detailsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Barang</th>
                                        <th style="width: 250px">Harga Jual / Ptg</th>
                                        <th style="width: 150px">Jumlah</th>
                                        <th style="width: 250px">Subtotal</th>
                                        <th style="width: 80px" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Rows will be added here by JS -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" class="align-middle">
                                            <button type="button" class="btn btn-info btn-sm text-white" id="addRowBtn">
                                                <i class="fas fa-plus"></i> Tambah Baris
                                            </button>
                                        </td>
                                        <td class="text-end fw-bold align-middle">Total</td>
                                        <td colspan="2">
                                            <input type="text" class="form-control fw-bold" id="total_uang_display"
                                                readonly value="Rp 0">
                                        </td>
                                    </tr>
                                        <td colspan="2" class="border-end-0"></td>
                                        <td class="text-end fw-bold align-middle">Bayar</td>
                                        <td colspan="2">
                                            <div class="input-group">
                                                <input type="number" name="bayar" class="form-control fw-bold" id="bayar"
                                                    value="{{ old('bayar', 0) }}" required min="0">
                                                <button type="button" class="btn btn-secondary" id="btn-uang-pas" title="Uang Pas">Pas</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="border-end-0"></td>
                                        <td class="text-end fw-bold align-middle">Sisa / Kurang</td>
                                        <td colspan="2">
                                            <input type="text" class="form-control fw-bold" id="sisa_kurang_display"
                                                readonly value="Rp 0">
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('reseller_transactions.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden elements for cloning -->
    <div class="d-none" id="barangOptionsMaster">
        <option value="">-- Pilih Barang --</option>
        @foreach($barangs as $barang)
        <option value="{{ $barang->id }}" data-harga="{{ $barang->hpp ?? 0 }}"
            data-reseller-id="{{ $barang->reseller_id }}">{{ $barang->namabarang }} {{ $barang->ukuran ? ' - ' .
            $barang->ukuran : '' }}</option>
        @endforeach
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const tableBody = document.querySelector('#detailsTable tbody');
            const addRowBtn = document.getElementById('addRowBtn');
            const resellerSelect = document.getElementById('resellerSelect');
            const barangOptionsMaster = document.getElementById('barangOptionsMaster').querySelectorAll('option');
            
            const totalDisplay = document.getElementById('total_uang_display');
            const bayarInput = document.getElementById('bayar');
            const sisaDisplay = document.getElementById('sisa_kurang_display');
            const btnUangPas = document.getElementById('btn-uang-pas');

            let rowIdx = 0;

            function formatRupiah(number) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
            }

            function getFilteredOptions() {
                const selectedReseller = resellerSelect.value;
                let optionsHtml = '';
                barangOptionsMaster.forEach(opt => {
                    const resellerId = opt.getAttribute('data-reseller-id');
                    // Tampilkan jika "Pilih Barang" (value kosong)
                    // ATAU reseller_id kosong / null (artinya berlaku untuk semua)
                    // ATAU reseller_id sesuai dengan yang dipilih
                    if (opt.value === "" || !resellerId || resellerId === selectedReseller) {
                        optionsHtml += opt.outerHTML;
                    }
                });
                return optionsHtml;
            }

            function filterExistingSelects() {
                const optionsHtml = getFilteredOptions();
                document.querySelectorAll('.barang-select').forEach(select => {
                    const currentValue = select.value;
                    select.innerHTML = optionsHtml;
                    
                    // Coba pertahankan value lama jika masih ada di daftar options yang baru
                    const hasOption = Array.from(select.options).some(opt => opt.value === currentValue);
                    if (hasOption && currentValue !== "") {
                        select.value = currentValue;
                    } else {
                        select.value = "";
                        // Memicu trigger change agar harga dan total ikut reset
                        select.dispatchEvent(new Event('change'));
                    }
                });
            }

            // Listen pada perubahan Reseller
            resellerSelect.addEventListener('change', filterExistingSelects);

            function calculateTotals() {
                let total = 0;
                document.querySelectorAll('.subtotal-input').forEach(input => {
                    total += parseFloat(input.value) || 0;
                });
                
                totalDisplay.value = formatRupiah(total);
                
                const bayar = parseFloat(bayarInput.value) || 0;
                const sisa = bayar - total;
                
                sisaDisplay.value = formatRupiah(sisa);
                
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
                document.querySelectorAll('.subtotal-input').forEach(input => {
                    total += parseFloat(input.value) || 0;
                });
                bayarInput.value = total;
                calculateTotals();
            });

            function addRow() {
                const optionsHtml = getFilteredOptions();
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>
                        <select name="details[${rowIdx}][barang_id]" class="form-select barang-select" required>
                            ${optionsHtml}
                        </select>
                    </td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control harga-display" readonly value="0">
                        </div>
                    </td>
                    <td>
                        <input type="number" name="details[${rowIdx}][jumlah]" class="form-control jumlah-input" value="1" required min="1">
                    </td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control subtotal-display" readonly value="0">
                            <input type="hidden" class="subtotal-input" value="0">
                        </div>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm remove-row-btn"><i class="fas fa-times"></i></button>
                    </td>
                `;
                tableBody.appendChild(tr);
                rowIdx++;

                const select = tr.querySelector('.barang-select');
                const hargaDisplay = tr.querySelector('.harga-display');
                const jumlahInput = tr.querySelector('.jumlah-input');
                const subtotalDisplay = tr.querySelector('.subtotal-display');
                const subtotalInput = tr.querySelector('.subtotal-input');
                const removeBtn = tr.querySelector('.remove-row-btn');

                function updateRow() {
                    const option = select.options[select.selectedIndex];
                    const harga = parseFloat(option.getAttribute('data-harga')) || 0;
                    const jumlah = parseFloat(jumlahInput.value) || 0;
                    const subtotal = harga * jumlah;

                    hargaDisplay.value = new Intl.NumberFormat('id-ID').format(harga);
                    subtotalDisplay.value = new Intl.NumberFormat('id-ID').format(subtotal);
                    subtotalInput.value = subtotal;

                    calculateTotals();
                }

                select.addEventListener('change', updateRow);
                jumlahInput.addEventListener('input', updateRow);
                
                removeBtn.addEventListener('click', function() {
                    tr.remove();
                    calculateTotals();
                });
            }

            addRowBtn.addEventListener('click', addRow);
            bayarInput.addEventListener('input', calculateTotals);

            // Add first row by default
            addRow();
        });
    </script>
</x-app-layout>