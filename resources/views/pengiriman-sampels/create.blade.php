@extends('layouts.form')
@section('content')
<div class="pc-container">
    <div class="pc-content">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-plus"></i> Tambah Pengiriman Sampel</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('pengiriman-sampels.store') }}" method="POST">
                        @csrf

                        <!-- Baris pertama: Tanggal dan Username -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tanggal" class="form-label">Tanggal Pengiriman <span
                                            class="text-danger">*</span></label>
                                    <input type="datetime-local"
                                        class="form-control @error('tanggal') is-invalid @enderror" id="tanggal"
                                        name="tanggal" value="{{ old('tanggal', now()->format('Y-m-d\TH:i')) }}"
                                        required>
                                    @error('tanggal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('username') is-invalid @enderror"
                                        id="username" name="username" value="{{ old('username') }}" required>
                                    @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Judul Sampel -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <h6 class="mb-0">Data Sampel</h6>
                                <small class="text-muted">Silahkan isi data sampel yang akan dikirim</small>
                            </div>
                        </div>

                        <!-- Container untuk sampel -->
                        <div id="sampel-container">
                            <!-- Sampel 1 (selalu tampil) -->
                            <div class="row mb-3 sampel-row" data-index="1">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="sampel1_id" class="form-label">Sampel 1</label>
                                        <select
                                            class="form-control sampel-select @error('sampel1_id') is-invalid @enderror"
                                            id="sampel1_id" name="sampel1_id" data-trigger data-index="1"
                                            onchange="calculateTotal()">
                                            <option value="">Pilih Sampel (Opsional)</option>
                                            @foreach($sampels as $sampel)
                                            <option value="{{ $sampel->id }}" data-harga="{{ $sampel->harga }}" {{
                                                old('sampel1_id')==$sampel->id ? 'selected' : '' }}>
                                                {{ $sampel->nama }} - {{ $sampel->ukuran }} (Rp {{
                                                number_format($sampel->harga, 0, ',', '.') }})
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('sampel1_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="jumlah1" class="form-label">Jumlah Sampel 1</label>
                                        <input type="number"
                                            class="form-control jumlah-input @error('jumlah1') is-invalid @enderror"
                                            id="jumlah1" name="jumlah1" value="{{ old('jumlah1', 0) }}" min="0"
                                            data-index="1" onchange="calculateTotal()">
                                        @error('jumlah1')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Sampel 2-5 (tersembunyi awal) -->
                            @for ($i = 2; $i <= 5; $i++) <div class="row mb-3 sampel-row" data-index="{{ $i }}"
                                style="display: none;">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="sampel{{ $i }}_id" class="form-label">Sampel {{ $i }}</label>
                                        <select
                                            class="form-control sampel-select @error('sampel'.$i.'_id') is-invalid @enderror"
                                            id="sampel{{ $i }}_id" name="sampel{{ $i }}_id" data-trigger
                                            data-index="{{ $i }}" onchange="calculateTotal()">
                                            <option value="">Pilih Sampel (Opsional)</option>
                                            @foreach($sampels as $sampel)
                                            <option value="{{ $sampel->id }}" data-harga="{{ $sampel->harga }}" {{
                                                old('sampel'.$i.'_id')==$sampel->id ? 'selected' : '' }}>
                                                {{ $sampel->nama }} - {{ $sampel->ukuran }} (Rp {{
                                                number_format($sampel->harga, 0, ',', '.') }})
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('sampel'.$i.'_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="jumlah{{ $i }}" class="form-label">Jumlah Sampel {{ $i }}</label>
                                        <input type="number"
                                            class="form-control jumlah-input @error('jumlah'.$i) is-invalid @enderror"
                                            id="jumlah{{ $i }}" name="jumlah{{ $i }}" value="{{ old('jumlah'.$i, 0) }}"
                                            min="0" data-index="{{ $i }}" onchange="calculateTotal()">
                                        @error('jumlah'.$i)
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                        </div>
                        @endfor
                </div>

                <!-- Tombol Tambah Sampel -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-outline-primary" id="tambah-sampel">
                            <i class="fas fa-plus"></i> Tambah Sampel Lainnya
                        </button>
                        <small class="text-muted ms-2">Maksimal 5 sampel</small>
                    </div>
                </div>

                <!-- Baris: No Resi dan Ongkir -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="no_resi" class="form-label">No. Resi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('no_resi') is-invalid @enderror" id="no_resi"
                                name="no_resi" value="{{ old('no_resi') }}" required>
                            @error('no_resi')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="ongkir" class="form-label">Ongkir (Rp) <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('ongkir') is-invalid @enderror" id="ongkir"
                                name="ongkir" value="{{ old('ongkir', 0) }}" min="0" required
                                onchange="calculateTotal()">
                            @error('ongkir')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Baris: Penerima dan Contact -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="penerima" class="form-label">Penerima <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('penerima') is-invalid @enderror"
                                id="penerima" name="penerima" value="{{ old('penerima') }}" required>
                            @error('penerima')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="contact" class="form-label">Contact <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('contact') is-invalid @enderror" id="contact"
                                name="contact" value="{{ old('contact') }}" required>
                            @error('contact')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Alamat -->
                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat"
                        rows="3" required>{{ old('alamat') }}</textarea>
                    @error('alamat')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Tombol Action -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <a href="{{ route('pengiriman-sampels.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>

<script>
    function calculateTotal() {
        let totalhpp = 0;

        // Hitung total dari semua sampel yang tampil
        const visibleRows = document.querySelectorAll('.sampel-row:not([style*="display: none"])');

        visibleRows.forEach(row => {
            const index = row.getAttribute('data-index');
            const selectElement = document.getElementById('sampel' + index + '_id');
            const jumlahInput = document.getElementById('jumlah' + index);

            if (selectElement && jumlahInput) {
                const selectedOption = selectElement.options[selectElement.selectedIndex];
                const harga = selectedOption ? parseInt(selectedOption.getAttribute('data-harga')) || 0 : 0;
                const jumlah = parseInt(jumlahInput.value) || 0;

                totalhpp += harga * jumlah;
            }
        });

        const ongkir = parseInt(document.getElementById('ongkir').value) || 0;
        const total_biaya = totalhpp + ongkir;

        // Update display
        document.getElementById('totalhpp_display').textContent = 'Rp ' + totalhpp.toLocaleString('id-ID');
        document.getElementById('total_biaya_display').textContent = 'Rp ' + total_biaya.toLocaleString('id-ID');

        // Update hidden inputs
        document.getElementById('totalhpp').value = totalhpp;
        document.getElementById('total_biaya').value = total_biaya;
    }

    // Initialize Choices.js untuk semua select dengan data-trigger
    document.addEventListener('DOMContentLoaded', function() {
        let currentVisibleSamples = 1;
        const maxSamples = 5;
        const addButton = document.getElementById('tambah-sampel');

        // Fungsi untuk menampilkan sampel berikutnya
        function showNextSample() {
            if (currentVisibleSamples < maxSamples) {
                currentVisibleSamples++;
                const nextRow = document.querySelector(`.sampel-row[data-index="${currentVisibleSamples}"]`);
                if (nextRow) {
                    nextRow.style.display = 'flex';

                    // Inisialisasi Choices.js untuk select yang baru ditampilkan
                    const selectElement = nextRow.querySelector('select[data-trigger]');
                    if (selectElement && !selectElement.classList.contains('choices__input')) {
                        new Choices(selectElement, {
                            placeholderValue: 'Pilih Sampel (Opsional)',
                            searchPlaceholderValue: 'Cari sampel...',
                            removeItemButton: true,
                            classNames: {
                                containerOuter: 'choices',
                                containerInner: 'choices__inner',
                                input: 'choices__input',
                                inputCloned: 'choices__input--cloned',
                                list: 'choices__list',
                                listItems: 'choices__list--multiple',
                                listSingle: 'choices__list--single',
                                listDropdown: 'choices__list--dropdown',
                                item: 'choices__item',
                                itemSelectable: 'choices__item--selectable',
                                itemDisabled: 'choices__item--disabled',
                                itemChoice: 'choices__item--choice',
                                placeholder: 'choices__placeholder',
                                group: 'choices__group',
                                groupHeading: 'choices__heading',
                                button: 'choices__button',
                                activeState: 'is-active',
                                focusState: 'is-focused',
                                openState: 'is-open',
                                disabledState: 'is-disabled',
                                highlightedState: 'is-highlighted',
                                selectedState: 'is-selected',
                                flippedState: 'is-flipped',
                                loadingState: 'is-loading',
                                noResults: 'has-no-results',
                                noChoices: 'has-no-choices'
                            }
                        });

                        // Tambahkan event listener untuk perubahan
                        selectElement.addEventListener('change', calculateTotal);

                        // Tambahkan event listener untuk input jumlah
                        const jumlahInput = nextRow.querySelector('.jumlah-input');
                        if (jumlahInput) {
                            jumlahInput.addEventListener('input', calculateTotal);
                        }
                    }
                }

                // Update button jika sudah mencapai maksimum
                if (currentVisibleSamples === maxSamples) {
                    addButton.disabled = true;
                    addButton.innerHTML = '<i class="fas fa-ban"></i> Maksimal 5 Sampel';
                    addButton.classList.remove('btn-outline-primary');
                    addButton.classList.add('btn-outline-secondary');
                }
            }
        }

        // Event listener untuk tombol tambah sampel
        addButton.addEventListener('click', showNextSample);

        // Inisialisasi Choices.js untuk sampel pertama
        const firstSelect = document.querySelector('#sampel1_id');
        if (firstSelect) {
            new Choices(firstSelect, {
                placeholderValue: 'Pilih Sampel (Opsional)',
                searchPlaceholderValue: 'Cari sampel...',
                removeItemButton: true,
                classNames: {
                    containerOuter: 'choices',
                    containerInner: 'choices__inner',
                    input: 'choices__input',
                    inputCloned: 'choices__input--cloned',
                    list: 'choices__list',
                    listItems: 'choices__list--multiple',
                    listSingle: 'choices__list--single',
                    listDropdown: 'choices__list--dropdown',
                    item: 'choices__item',
                    itemSelectable: 'choices__item--selectable',
                    itemDisabled: 'choices__item--disabled',
                    itemChoice: 'choices__item--choice',
                    placeholder: 'choices__placeholder',
                    group: 'choices__group',
                    groupHeading: 'choices__heading',
                    button: 'choices__button',
                    activeState: 'is-active',
                    focusState: 'is-focused',
                    openState: 'is-open',
                    disabledState: 'is-disabled',
                    highlightedState: 'is-highlighted',
                    selectedState: 'is-selected',
                    flippedState: 'is-flipped',
                    loadingState: 'is-loading',
                    noResults: 'has-no-results',
                    noChoices: 'has-no-choices'
                }
            });

            // Tambahkan event listener untuk perubahan
            firstSelect.addEventListener('change', calculateTotal);
        }

        // Event listener untuk input jumlah sampel pertama
        const firstJumlahInput = document.querySelector('#jumlah1');
        if (firstJumlahInput) {
            firstJumlahInput.addEventListener('input', calculateTotal);
        }

        // Event listener untuk ongkir
        const ongkirInput = document.getElementById('ongkir');
        if (ongkirInput) {
            ongkirInput.addEventListener('input', calculateTotal);
        }

        // Inisialisasi perhitungan awal
        calculateTotal();
    });
</script>

@endsection
