<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Tambah Data Banding Baru</h5>
                        <a href="{{ route('bandings.search') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali ke Pencarian
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Data untuk nomor resi <strong>{{ $noResi }}</strong> tidak ditemukan.
                            Silakan isi form berikut untuk menambahkan data baru.
                        </div>

                        <form id="createBandingForm" action="{{ route('bandings.store') }}" method="POST">
                            @csrf

                            <!-- No Resi (pre-filled) -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="no_resi" class="form-label">No Resi</label>
                                    <input type="text" class="form-control" id="no_resi" name="no_resi"
                                           value="{{ $noResi }}" readonly>
                                    <div class="form-text">Nomor resi sudah terisi otomatis</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control" id="tanggal" name="tanggal"
                                           value="{{ now()->format('Y-m-d\TH:i') }}" required>
                                </div>
                            </div>

                            <!-- Status dan Marketplace -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="status_banding" class="form-label">Status Banding <span class="text-danger">*</span></label>
                                    <select class="form-select" id="status_banding" name="status_banding" required>
                                        <option value="">Pilih Status</option>
                                        @foreach($statusBandingOptions as $value => $label)
                                            <option value="{{ $value }}" {{ $value == 'Ditinjau' ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="marketplace" class="form-label">Marketplace <span class="text-danger">*</span></label>
                                    <select class="form-select" id="marketplace" name="marketplace" required>
                                        <option value="">Pilih Marketplace</option>
                                        @foreach($marketplaceOptions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="alasan" class="form-label">Alasan <span class="text-danger">*</span></label>
                                    <select class="form-select" id="alasan" name="alasan" required>
                                        <option value="">Pilih Alasan</option>
                                        @foreach($alasanOptions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Informasi Pesanan -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="no_pesanan" class="form-label">No Pesanan</label>
                                    <input type="text" class="form-control" id="no_pesanan" name="no_pesanan">
                                </div>
                                <div class="col-md-4">
                                    <label for="no_pengajuan" class="form-label">No Pengajuan</label>
                                    <input type="text" class="form-control" id="no_pengajuan" name="no_pengajuan">
                                </div>
                                <div class="col-md-4">
                                    <label for="ongkir" class="form-label">Ongkir <span class="text-danger">*</span></label>
                                    <select class="form-select" id="ongkir" name="ongkir" required>
                                        @foreach($ongkirOptions as $value => $label)
                                            <option value="{{ $value }}" {{ $value == '-' ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Informasi Customer -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username">
                                </div>
                                <div class="col-md-4">
                                    <label for="nama_pengirim" class="form-label">Nama Pengirim</label>
                                    <input type="text" class="form-control" id="nama_pengirim" name="nama_pengirim">
                                </div>
                                <div class="col-md-4">
                                    <label for="no_hp" class="form-label">No HP</label>
                                    <input type="text" class="form-control" id="no_hp" name="no_hp">
                                </div>
                            </div>

                            <!-- Status Penerimaan dan Alamat -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="status_penerimaan" class="form-label">Status Penerimaan <span class="text-danger">*</span></label>
                                    <select class="form-select" id="status_penerimaan" name="status_penerimaan" required>
                                        @foreach($statusPenerimaanOptions as $value => $label)
                                            <option value="{{ $value }}" {{ $value == '-' ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="alamat" name="alamat" rows="3" required placeholder="Masukkan alamat lengkap"></textarea>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('bandings.search') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-save"></i> Simpan Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('createBandingForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            // Show loading state
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            submitBtn.disabled = true;

            try {
                // Gunakan FormData biasa, bukan JSON
                const formData = new FormData(this);

                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json', // Minta response JSON
                        'X-Requested-With': 'XMLHttpRequest' // Tandai sebagai AJAX request
                    },
                    body: formData
                });

                // Handle response
                const responseText = await response.text();

                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (parseError) {
                    console.error('Gagal parse JSON:', parseError);
                    // Jika bukan JSON, mungkin success redirect
                    if (response.ok) {
                        // Assume success
                        await showSuccess('Data berhasil disimpan!');
                        return;
                    } else {
                        throw new Error('Terjadi kesalahan server. Silakan coba lagi.');
                    }
                }

                if (data.success) {
                    await showSuccess(data.message);
                } else {
                    // Handle validation errors
                    if (data.errors) {
                        const errorMessages = Object.values(data.errors).flat().join('<br>');
                        throw new Error(errorMessages);
                    } else {
                        throw new Error(data.message || 'Gagal menyimpan data');
                    }
                }

            } catch (error) {
                console.error('Error details:', error);
                await showError(error.message);
            } finally {
                // Reset button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });

        async function showSuccess(message) {
            await Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: message,
                showConfirmButton: false,
                timer: 2000
            });
            window.location.href = '{{ route("bandings.search") }}';
        }

        async function showError(message) {
            await Swal.fire({
                icon: 'error',
                title: 'Error!',
                html: `<div style="text-align: left;">
                        <strong>Gagal menyimpan data:</strong><br>
                        <small>${message}</small>
                    </div>`,
                confirmButtonText: 'OK'
            });
        }

        // Real-time validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('createBandingForm');
            const requiredFields = form.querySelectorAll('[required]');

            requiredFields.forEach(field => {
                field.addEventListener('change', function() {
                    validateField(this);
                });
                field.addEventListener('blur', function() {
                    validateField(this);
                });
            });

            function validateField(field) {
                const value = field.value.trim();
                if (value === '') {
                    field.classList.add('is-invalid');
                    field.classList.remove('is-valid');
                } else {
                    field.classList.remove('is-invalid');
                    field.classList.add('is-valid');
                }
            }

            // Validate on page load
            requiredFields.forEach(field => {
                validateField(field);
            });
        });

        // Auto-validate when user starts typing
        document.addEventListener('input', function(e) {
            if (e.target.hasAttribute('required')) {
                validateField(e.target);
            }
        });
    </script>

    <style>
        .is-valid {
            border-color: #198754 !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        .is-invalid {
            border-color: #dc3545 !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 3.6.4.4.4-.4'/%3e%3cpath d='M6 7v2'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }
    </style>
</x-app-layout>
