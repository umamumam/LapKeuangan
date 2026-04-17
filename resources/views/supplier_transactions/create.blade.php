<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="card shadow border-0" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header border-0 text-white" style="background: linear-gradient(135deg, #f5a623 0%, #f7931e 100%);">
                    <h5 class="mb-0 text-white"><i class="fas fa-plus-circle me-2"></i> Tambah Transaksi Supplier</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('supplier_transactions.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
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
                                <input type="date" name="tgl" class="form-control" value="{{ old('tgl', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Retur (Potong)</label>
                                <input type="number" name="retur" class="form-control" value="{{ old('retur', 0) }}" min="0">
                                <small class="text-muted">Isi jumlah barang diretur (opsional).</small>
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
                                        <th style="width: 200px">HPP (Harga Beli)</th>
                                        <th style="width: 130px">Jumlah</th>
                                        <th style="width: 200px">Subtotal</th>
                                        <th style="width: 60px" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
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
                                                <input type="number" name="bayar" class="form-control fw-bold" id="bayar" value="{{ old('bayar', 0) }}" required min="0">
                                                <button type="button" class="btn btn-secondary" id="btn-uang-pas" title="Uang Pas">Pas</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="border-end-0 border-top-0"></td>
                                        <td class="text-end fw-bold align-middle">Sisa / Kurang</td>
                                        <td colspan="2">
                                            <input type="text" class="form-control fw-bold border-0 bg-transparent" id="sisa_display" readonly value="Rp 0">
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('supplier_transactions.show_supplier', $supplier->id) }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-warning text-dark fw-bold">Simpan Transaksi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="d-none" id="barangOptionsMaster">
        <option value="">-- Pilih Barang --</option>
        @foreach($barangs as $barang)
        <option value="{{ $barang->id }}" data-harga="{{ $barang->hpp ?? 0 }}">
            {{ $barang->namabarang }}{{ $barang->ukuran ? ' - ' . $barang->ukuran : '' }}
        </option>
        @endforeach
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const tableBody = document.querySelector('#detailsTable tbody');
            const addRowBtn = document.getElementById('addRowBtn');
            const barangOptionsMaster = document.getElementById('barangOptionsMaster').querySelectorAll('option');
            const totalDisplay = document.getElementById('total_uang_display');
            const bayarInput = document.getElementById('bayar');
            const sisaDisplay = document.getElementById('sisa_display');
            const btnUangPas = document.getElementById('btn-uang-pas');
            let rowIdx = 0;

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
                sisaDisplay.className = 'form-control fw-bold border-0 bg-transparent ' + (sisa > 0 ? 'text-success' : sisa < 0 ? 'text-danger' : 'text-secondary');
            }

            btnUangPas.addEventListener('click', function() {
                let total = 0;
                document.querySelectorAll('.subtotal-input').forEach(i => total += parseFloat(i.value) || 0);
                bayarInput.value = total;
                calculateTotals();
            });

            function addRow() {
                let opts = '';
                barangOptionsMaster.forEach(o => opts += o.outerHTML);
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><select name="details[${rowIdx}][barang_id]" class="form-select barang-select" required>${opts}</select></td>
                    <td><div class="input-group"><span class="input-group-text bg-light">Rp</span><input type="text" class="form-control harga-display bg-light" readonly value="0"></div></td>
                    <td><input type="number" name="details[${rowIdx}][jumlah]" class="form-control jumlah-input" value="1" required min="1"></td>
                    <td><div class="input-group"><span class="input-group-text bg-light">Rp</span><input type="text" class="form-control subtotal-display bg-light" readonly value="0"><input type="hidden" class="subtotal-input" value="0"></div></td>
                    <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row-btn"><i class="fas fa-times"></i></button></td>
                `;
                tableBody.appendChild(tr);
                rowIdx++;

                const select = tr.querySelector('.barang-select');
                const hargaDisplay = tr.querySelector('.harga-display');
                const jumlahInput = tr.querySelector('.jumlah-input');
                const subtotalDisplay = tr.querySelector('.subtotal-display');
                const subtotalInput = tr.querySelector('.subtotal-input');

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
                tr.querySelector('.remove-row-btn').addEventListener('click', function() { tr.remove(); calculateTotals(); });
            }

            addRowBtn.addEventListener('click', addRow);
            bayarInput.addEventListener('input', calculateTotals);
            addRow();
        });
    </script>
</x-app-layout>
