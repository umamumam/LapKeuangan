<!-- resources/views/bandings/search.blade.php -->
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
                        <h5 class="mb-0"><i class="fas fa-search"></i> Cari Data Banding</h5>
                        <a href="{{ route('bandings.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>

                    <div class="card-body">
                        <!-- Search Input Section -->
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="search-container text-center mb-4">
                                    <div class="mb-4">
                                        <label for="no_resi" class="form-label fw-bold h5">Scan atau Input No Resi</label>
                                        <div class="input-group input-group-lg">
                                            <input type="text" class="form-control form-control-lg" id="no_resi"
                                                placeholder="Masukkan nomor resi..." autofocus>
                                            <button class="btn btn-outline-primary" type="button" id="startScanner">
                                                <i class="fas fa-camera"></i>
                                            </button>
                                            <button class="btn btn-primary" type="button" id="searchBtn">
                                                <i class="fas fa-search"></i> Cari
                                            </button>
                                        </div>
                                        <div class="form-text">
                                            Tekan Enter untuk mencari atau gunakan tombol scanner
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
                                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2 text-muted h5">Mencari data...</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Result Section -->
                        <div id="searchResult">
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-search fa-4x mb-3 opacity-50"></i>
                                <h4 class="text-muted">Hasil pencarian akan muncul di sini</h4>
                                <p class="text-muted">Gunakan scanner atau input manual nomor resi</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Result Modal -->
    <div class="modal fade" id="resultModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-file-alt"></i> Detail Banding</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalBody">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/quagga@0.12.1/dist/quagga.min.js"></script>
    <script>
        class ResiScanner {
            constructor() {
                this.isScanning = false;
                this.initializeElements();
                this.initializeEventListeners();
            }

            initializeElements() {
                this.noResiInput = document.getElementById('no_resi');
                this.startScannerBtn = document.getElementById('startScanner');
                this.stopScannerBtn = document.getElementById('stopScanner');
                this.searchBtn = document.getElementById('searchBtn');
                this.scannerArea = document.getElementById('scanner-area');
                this.preview = document.getElementById('preview');
                this.loadingSpinner = document.getElementById('loadingSpinner');
                this.searchResult = document.getElementById('searchResult');
                this.resultModal = new bootstrap.Modal(document.getElementById('resultModal'));
                this.closeScannerInfo = document.getElementById('closeScannerInfo');
            }

            initializeEventListeners() {
                this.startScannerBtn.addEventListener('click', () => this.startScanner());
                this.stopScannerBtn.addEventListener('click', () => this.stopScanner());
                this.searchBtn.addEventListener('click', () => this.searchData());
                this.noResiInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') this.searchData();
                });
                if (this.closeScannerInfo) {
                    this.closeScannerInfo.addEventListener('click', () => this.stopScanner());
                }

                // Auto focus on input
                this.noResiInput.focus();
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
                        this.noResiInput.value = code;
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
                const noResi = this.noResiInput.value.trim();

                if (!noResi) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Masukkan nomor resi terlebih dahulu'
                    });
                    return;
                }

                this.showLoading(true);
                this.searchResult.innerHTML = '';

                try {
                    const response = await fetch('{{ route("bandings.search.result") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ no_resi: noResi })
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
                        this.showResult(data.data);
                    } else {
                        this.showError(data.message);
                    }
                } catch (error) {
                    console.error('Search error:', error);
                    this.showError('Terjadi kesalahan: ' + error.message);
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

            showResult(data) {
                const statusClass = {
                    'Berhasil': 'bg-success',
                    'Ditinjau': 'bg-warning',
                    'Ditolak': 'bg-danger'
                };

                const ongkirClass = {
                    'Dibebaskan': 'bg-success',
                    'Ditanggung': 'bg-warning',
                    '-': 'bg-secondary'
                };

                const html = `
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-check-circle"></i> Data Ditemukan</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6><i class="fas fa-info-circle text-primary"></i> Informasi Utama</h6>
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <td width="40%"><strong>No Pesanan</strong></td>
                                            <td>${data.no_pesanan}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>No Pengajuan</strong></td>
                                            <td>${data.no_pengajuan || '-'}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tanggal</strong></td>
                                            <td>${this.formatDate(data.tanggal)}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status</strong></td>
                                            <td>
                                                <span class="badge ${statusClass[data.status_banding] || 'bg-secondary'}">
                                                    ${data.status_banding}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6><i class="fas fa-shipping-fast text-primary"></i> Informasi Pengiriman</h6>
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <td width="40%"><strong>Ongkir</strong></td>
                                            <td>
                                                <span class="badge ${ongkirClass[data.ongkir] || 'bg-secondary'}">
                                                    ${data.ongkir}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>No Resi</strong></td>
                                            <td>${data.no_resi || '-'}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Marketplace</strong></td>
                                            <td>${data.marketplace}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Alasan</strong></td>
                                            <td>${data.alasan}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6><i class="fas fa-user text-primary"></i> Informasi Customer</h6>
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <td width="20%"><strong>Username</strong></td>
                                            <td>${data.username}</td>
                                            <td width="20%"><strong>Nama Pengirim</strong></td>
                                            <td>${data.nama_pengirim}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>No HP</strong></td>
                                            <td>${data.no_hp || '-'}</td>
                                            <td><strong>Alamat</strong></td>
                                            <td>${data.alamat}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="text-center mt-4">
                                <button class="btn btn-primary btn-lg" onclick="resiScanner.showModal(resiScanner.currentData)">
                                    <i class="fas fa-expand"></i> Lihat Detail Lengkap
                                </button>
                            </div>
                        </div>
                    </div>
                `;

                this.searchResult.innerHTML = html;
                this.currentData = data;
            }

            showModal(data) {
                const modalBody = document.getElementById('modalBody');
                if (!modalBody) return;

                const statusClass = {
                    'Berhasil': 'bg-success',
                    'Ditinjau': 'bg-warning',
                    'Ditolak': 'bg-danger'
                };

                const ongkirClass = {
                    'Dibebaskan': 'bg-success',
                    'Ditanggung': 'bg-warning',
                    '-': 'bg-secondary'
                };

                modalBody.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-info-circle text-primary"></i> Informasi Banding</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td width="40%"><strong>Tanggal</strong></td>
                                    <td>${this.formatDate(data.tanggal)}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status Banding</strong></td>
                                    <td><span class="badge ${statusClass[data.status_banding] || 'bg-secondary'}">${data.status_banding}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Ongkir</strong></td>
                                    <td><span class="badge ${ongkirClass[data.ongkir] || 'bg-secondary'}">${data.ongkir}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>No Resi</strong></td>
                                    <td>${data.no_resi || '-'}</td>
                                </tr>
                                <tr>
                                    <td><strong>No Pesanan</strong></td>
                                    <td>${data.no_pesanan}</td>
                                </tr>
                                <tr>
                                    <td><strong>No Pengajuan</strong></td>
                                    <td>${data.no_pengajuan || '-'}</td>
                                </tr>
                                <tr>
                                    <td><strong>Alasan</strong></td>
                                    <td>${data.alasan}</td>
                                </tr>
                                <tr>
                                    <td><strong>Marketplace</strong></td>
                                    <td>${data.marketplace}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-user text-primary"></i> Informasi Customer</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td width="40%"><strong>Username</strong></td>
                                    <td>${data.username}</td>
                                </tr>
                                <tr>
                                    <td><strong>Nama Pengirim</strong></td>
                                    <td>${data.nama_pengirim}</td>
                                </tr>
                                <tr>
                                    <td><strong>No HP</strong></td>
                                    <td>${data.no_hp || '-'}</td>
                                </tr>
                            </table>
                            <h6><i class="fas fa-map-marker-alt text-primary"></i> Alamat</h6>
                            <div class="border rounded p-2 bg-light">
                                <small>${data.alamat}</small>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6><i class="fas fa-history text-primary"></i> Timestamps</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td width="40%"><strong>Dibuat Pada</strong></td>
                                    <td>${this.formatDate(data.created_at)}</td>
                                </tr>
                                <tr>
                                    <td><strong>Diupdate Pada</strong></td>
                                    <td>${this.formatDate(data.updated_at)}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                `;

                this.resultModal.show();
            }

            formatDate(dateString) {
                if (!dateString) return '-';
                const date = new Date(dateString);
                return date.toLocaleString('id-ID');
            }

            showError(message) {
                const html = `
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Data Tidak Ditemukan</h5>
                        </div>
                        <div class="card-body text-center py-5">
                            <i class="fas fa-search fa-4x text-muted mb-3 opacity-50"></i>
                            <h4 class="text-danger">${message}</h4>
                            <p class="text-muted">Pastikan nomor resi benar dan coba lagi</p>
                            <button class="btn btn-primary mt-3" onclick="document.getElementById('no_resi').focus()">
                                <i class="fas fa-redo"></i> Coba Lagi
                            </button>
                        </div>
                    </div>
                `;
                this.searchResult.innerHTML = html;
            }
        }

        // Initialize scanner when page loads
        document.addEventListener('DOMContentLoaded', function() {
            window.resiScanner = new ResiScanner();
        });
    </script>
</x-app-layout>
