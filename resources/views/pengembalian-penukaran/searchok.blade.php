<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="col-sm-12">
                <div class="card">
                    @if(session('success'))
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            Swal.fire({
                                icon: "success",
                                title: "Berhasil!",
                                text: "{{ session('success') }}",
                                showConfirmButton: false,
                                timer: 3000
                            });
                        });
                    </script>
                    @endif

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <span class="pc-micon">
                                <i class="fas fa-check-circle"></i>
                            </span>
                            Scan Resi Pengembalian/Penukaran (Auto OK)
                        </h5>
                        <a href="{{ route('pengembalian-penukaran.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>

                    <div class="card-body">
                        <!-- Search Input Section -->
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="search-container text-center mb-4">
                                    <div class="mb-4">
                                        <label for="resi" class="form-label fw-bold h5">Scan atau Input No Resi</label>
                                        <p class="text-muted mb-3">
                                            <i class="fas fa-info-circle text-info"></i>
                                            Data yang ditemukan akan otomatis diubah statusnya menjadi <strong>OK</strong>
                                        </p>
                                        <div class="input-group input-group-lg">
                                            <input type="text" class="form-control form-control-lg" id="resi"
                                                placeholder="Masukkan nomor resi (penerimaan/pengiriman)..." autofocus>
                                            <button class="btn btn-outline-primary" type="button" id="startScanner">
                                                <i class="fas fa-camera"></i>
                                            </button>
                                            <button class="btn btn-success" type="button" id="searchBtn">
                                                <i class="fas fa-check-circle"></i> Scan & OK
                                            </button>
                                        </div>
                                        <div class="form-text">
                                            Tekan Enter untuk scan atau gunakan tombol scanner
                                        </div>
                                    </div>

                                    <!-- Scanner Area -->
                                    <div id="scanner-area" class="text-center mb-4" style="display: none;">
                                        <div class="alert alert-info mb-3">
                                            <i class="fas fa-info-circle"></i> Arahkan kamera ke barcode
                                            <button type="button" class="btn-close float-end" id="closeScannerInfo"></button>
                                        </div>
                                        <div id="preview" class="mx-auto mb-3 border rounded" style="max-width: 100%;"></div>
                                        <button class="btn btn-danger btn-sm" id="stopScanner">
                                            <i class="fas fa-stop"></i> Stop Scanner
                                        </button>
                                    </div>

                                    <!-- Loading Spinner -->
                                    <div class="text-center loading-spinner" id="loadingSpinner" style="display: none;">
                                        <div class="spinner-border text-success" role="status" style="width: 3rem; height: 3rem;">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2 text-muted h5">Mencari dan mengupdate data...</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Default Message -->
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-exchange-alt fa-4x mb-3 opacity-50"></i>
                            <h4 class="text-muted">Silahkan scan atau input nomor resi</h4>
                            <p class="text-muted">Status akan otomatis berubah menjadi <strong>OK</strong> setelah data ditemukan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Success -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-success">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-check-circle me-2"></i>Berhasil!
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                        <h5 id="successMessage"></h5>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-info-circle"></i> Informasi Data</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <td width="40%"><strong>Resi Penerimaan:</strong></td>
                                    <td id="modalResiPenerimaan"></td>
                                </tr>
                                <tr>
                                    <td><strong>Resi Pengiriman:</strong></td>
                                    <td id="modalResiPengiriman"></td>
                                </tr>
                                <tr>
                                    <td><strong>Nama Pengirim:</strong></td>
                                    <td id="modalNamaPengirim"></td>
                                </tr>
                                <tr>
                                    <td><strong>Jenis:</strong></td>
                                    <td id="modalJenis"></td>
                                </tr>
                                <tr>
                                    <td><strong>Marketplace:</strong></td>
                                    <td id="modalMarketplace"></td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal:</strong></td>
                                    <td id="modalTanggal"></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="alert alert-success">
                        <i class="fas fa-check me-2"></i>
                        <strong>Status Diterima:</strong>
                        <span class="badge bg-success ms-2">
                            <i class="fas fa-check"></i> OK
                        </span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="scannerPengembalian.scanNext()" data-bs-dismiss="modal">
                        <i class="fas fa-redo me-1"></i> Scan Berikutnya
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Error -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-danger">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>Data Tidak Ditemukan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-search fa-4x text-muted mb-3 opacity-50"></i>
                        <h5 id="errorMessage" class="text-danger"></h5>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>
                        Pastikan nomor resi benar dan coba lagi
                    </div>

                    <div class="text-center">
                        <p class="text-muted mb-3">Nomor Resi: <strong id="errorResi"></strong></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Tutup
                    </button>
                    <button type="button" class="btn btn-primary" onclick="scannerPengembalian.retry()" data-bs-dismiss="modal">
                        <i class="fas fa-redo me-1"></i> Coba Lagi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/quagga@0.12.1/dist/quagga.min.js"></script>
    <script>
        class PengembalianResiScanner {
            constructor() {
                this.isScanning = false;
                this.initializeElements();
                this.initializeEventListeners();
            }

            initializeElements() {
                this.resiInput = document.getElementById('resi');
                this.startScannerBtn = document.getElementById('startScanner');
                this.stopScannerBtn = document.getElementById('stopScanner');
                this.searchBtn = document.getElementById('searchBtn');
                this.scannerArea = document.getElementById('scanner-area');
                this.preview = document.getElementById('preview');
                this.loadingSpinner = document.getElementById('loadingSpinner');
                this.closeScannerInfo = document.getElementById('closeScannerInfo');

                // Modals
                this.successModal = new bootstrap.Modal(document.getElementById('successModal'));
                this.errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            }

            initializeEventListeners() {
                this.startScannerBtn.addEventListener('click', () => this.startScanner());
                this.stopScannerBtn.addEventListener('click', () => this.stopScanner());
                this.searchBtn.addEventListener('click', () => this.searchData());
                this.resiInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') this.searchData();
                });
                if (this.closeScannerInfo) {
                    this.closeScannerInfo.addEventListener('click', () => this.stopScanner());
                }

                // Auto focus on input
                this.resiInput.focus();
            }

            startScanner() {
                if (this.isScanning) return;

                this.scannerArea.style.display = 'block';
                this.isScanning = true;

                Quagga.init({
                    inputStream: {
                        name: "Live",
                        type: "LiveStream",
                        target: this.preview,
                        constraints: {
                            width: 400,
                            height: 300,
                            facingMode: "environment"
                        }
                    },
                    decoder: {
                        readers: ["code_128_reader", "ean_reader", "upc_reader"]
                    }
                }, (err) => {
                    if (err) {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal memulai scanner: ' + err.message
                        });
                        this.stopScanner();
                        return;
                    }
                    Quagga.start();
                });

                Quagga.onDetected((result) => {
                    const code = result.codeResult.code;
                    if (code) {
                        this.resiInput.value = code;
                        this.stopScanner();
                        this.searchData();
                    }
                });
            }

            stopScanner() {
                if (this.isScanning) {
                    try {
                        Quagga.stop();
                    } catch (e) {
                        console.log('Scanner already stopped');
                    }
                    this.isScanning = false;
                    this.scannerArea.style.display = 'none';
                }
            }

            async searchData() {
                const resi = this.resiInput.value.trim();

                if (!resi) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Masukkan nomor resi terlebih dahulu'
                    });
                    return;
                }

                this.showLoading(true);

                try {
                    const response = await fetch('{{ route("pengembalian-penukaran.search.result.ok") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ resi: resi })
                    });

                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error('Response is not JSON');
                    }

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'HTTP error! status: ' + response.status);
                    }

                    if (data.success) {
                        this.showSuccessModal(data.data, data.message);
                        // Clear input setelah berhasil
                        this.resiInput.value = '';
                        // Auto focus kembali setelah modal ditutup
                        this.successModal._element.addEventListener('hidden.bs.modal', () => {
                            setTimeout(() => this.resiInput.focus(), 100);
                        });
                    } else {
                        this.showErrorModal(data.message, resi);
                    }
                } catch (error) {
                    console.error('Search error:', error);
                    this.showErrorModal('Terjadi kesalahan: ' + error.message, resi);
                } finally {
                    this.showLoading(false);
                }
            }

            showLoading(show) {
                if (this.loadingSpinner) {
                    this.loadingSpinner.style.display = show ? 'block' : 'none';
                }
                if (this.searchBtn) {
                    this.searchBtn.disabled = show;
                }
            }

            showSuccessModal(data, message) {
                // Set modal content
                document.getElementById('successMessage').textContent = message;
                document.getElementById('modalResiPenerimaan').textContent = data.resi_penerimaan || '-';
                document.getElementById('modalResiPengiriman').textContent = data.resi_pengiriman || '-';
                document.getElementById('modalNamaPengirim').textContent = data.nama_pengirim || '-';
                document.getElementById('modalJenis').textContent = data.jenis || '-';
                document.getElementById('modalMarketplace').textContent = data.marketplace || '-';
                document.getElementById('modalTanggal').textContent = data.tanggal ?
                    new Date(data.tanggal).toLocaleDateString('id-ID') : '-';

                // Show modal
                this.successModal.show();
            }

            showErrorModal(message, resi) {
                document.getElementById('errorMessage').textContent = message;
                document.getElementById('errorResi').textContent = resi;

                // Auto focus kembali setelah modal error ditutup
                this.errorModal._element.addEventListener('hidden.bs.modal', () => {
                    this.resiInput.focus();
                    this.resiInput.select();
                });

                this.errorModal.show();
            }

            scanNext() {
                this.resiInput.focus();
            }

            retry() {
                this.resiInput.focus();
                this.resiInput.select();
            }
        }

        // Initialize scanner when page loads
        document.addEventListener('DOMContentLoaded', function() {
            window.scannerPengembalian = new PengembalianResiScanner();
        });
    </script>
</x-app-layout>
