@extends('layouts.form')
@section('content')
<div class="pc-container">
    <div class="pc-content">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-edit"></i> Edit Pengiriman Sampel</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('pengiriman-sampels.update', $pengirimanSampel->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Baris pertama: Tanggal dan Username -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tanggal" class="form-label">Tanggal Pengiriman <span
                                            class="text-danger">*</span></label>
                                    <input type="datetime-local"
                                        class="form-control @error('tanggal') is-invalid @enderror" id="tanggal"
                                        name="tanggal"
                                        value="{{ old('tanggal', $pengirimanSampel->tanggal->format('Y-m-d\TH:i')) }}"
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
                                        id="username" name="username"
                                        value="{{ old('username', $pengirimanSampel->username) }}" required>
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
                                <small class="text-muted">Silahkan edit data sampel yang akan dikirim</small>
                            </div>
                        </div>

                        <!-- Container untuk sampel -->
                        <div id="sampel-container">
                            @php
                                // Ambil data sampel yang sudah ada
                                // Gunakan data lama untuk kompatibilitas jika belum ada relasi items
                                $sampelItems = $pengirimanSampel->pengirimanSampelItems ?? [];

                                // Untuk data lama yang masih single sampel, konversi ke format array
                                if (count($sampelItems) === 0 && $pengirimanSampel->sampel_id) {
                                    $sampelItems = [['sampel_id' => $pengirimanSampel->sampel_id, 'jumlah' => $pengirimanSampel->jumlah]];
                                }

                                $sampelCount = count($sampelItems) ?: 1;
                            @endphp

                            @for ($i = 1; $i <= 5; $i++)
                                @php
                                    $sampelItem = $sampelItems[$i-1] ?? null;
                                    $isVisible = $i <= $sampelCount;
                                    $displayStyle = $isVisible ? 'display: flex' : 'display: none';
                                    $sampelId = $sampelItem['sampel_id'] ?? $sampelItem->sampel_id ?? null;
                                    $jumlah = $sampelItem['jumlah'] ?? $sampelItem->jumlah ?? 0;
                                @endphp

                                <div class="row mb-3 sampel-row" data-index="{{ $i }}" style="{{ $displayStyle }}">
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
                                                    old('sampel'.$i.'_id', $sampelId) == $sampel->id ? 'selected' : '' }}>
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
                                                id="jumlah{{ $i }}" name="jumlah{{ $i }}"
                                                value="{{ old('jumlah'.$i, $jumlah) }}" min="0"
                                                data-index="{{ $i }}" onchange="calculateTotal()">
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
                                        name="no_resi" value="{{ old('no_resi', $pengirimanSampel->no_resi) }}" required>
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
                                        name="ongkir" value="{{ old('ongkir', $pengirimanSampel->ongkir) }}" min="0" required
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
                                        id="penerima" name="penerima" value="{{ old('penerima', $pengirimanSampel->penerima) }}" required>
                                    @error('penerima')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contact" class="form-label">Contact <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('contact') is-invalid @enderror" id="contact"
                                        name="contact" value="{{ old('contact', $pengirimanSampel->contact) }}" required>
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
                                rows="3" required>{{ old('alamat', $pengirimanSampel->alamat) }}</textarea>
                            @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tombol Action -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update
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
        // Hitung jumlah sampel yang terlihat
        let currentVisibleSamples = document.querySelectorAll('.sampel-row[style*="flex"]').length;
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

        // Update button jika sudah mencapai maksimum saat load
        if (currentVisibleSamples === maxSamples) {
            addButton.disabled = true;
            addButton.innerHTML = '<i class="fas fa-ban"></i> Maksimal 5 Sampel';
            addButton.classList.remove('btn-outline-primary');
            addButton.classList.add('btn-outline-secondary');
        }

        // Inisialisasi Choices.js untuk semua sampel yang terlihat
        for (let i = 1; i <= currentVisibleSamples; i++) {
            const selectElement = document.querySelector('#sampel' + i + '_id');
            if (selectElement) {
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
            }

            // Event listener untuk input jumlah
            const jumlahInput = document.querySelector('#jumlah' + i);
            if (jumlahInput) {
                jumlahInput.addEventListener('input', calculateTotal);
            }
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
