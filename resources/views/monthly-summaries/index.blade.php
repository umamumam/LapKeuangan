<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Summary Bulanan</h5>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#dashboardModal">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </button>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#generateModal">
                                <i class="fas fa-plus"></i> Generate
                            </button>
                            <button type="button" class="btn btn-success btn-sm" onclick="generateCurrentMonth()">
                                <i class="fas fa-sync"></i> Bulan Ini
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div id="alertContainer"></div>

                        @if($summaries->isEmpty())
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada summary bulanan.</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateModal">
                                <i class="fas fa-plus"></i> Generate Summary Pertama
                            </button>
                        </div>
                        @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Periode</th>
                                        <th>Penghasilan</th>
                                        <th>HPP</th>
                                        <th>Income</th>
                                        <th width="80">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($summaries as $summary)
                                    <tr class="table-active">
                                        <td>
                                            <strong>{{ $summary->nama_periode }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ $summary->periode_awal->format('d/m/Y') }} - {{ $summary->periode_akhir->format('d/m/Y') }}
                                            </small>
                                        </td>
                                        <td class="fw-bold text-success">
                                            Rp {{ number_format($summary->total_penghasilan, 0, ',', '.') }}
                                        </td>
                                        <td class="text-danger">
                                            Rp {{ number_format($summary->total_hpp, 0, ',', '.') }}
                                        </td>
                                        <td>
                                            {{ number_format($summary->total_income_count, 0, ',', '.') }}
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <button type="button" class="btn btn-info btn-sm"
                                                        onclick="showDetail({{ $summary->id }})" title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm"
                                                        onclick="deleteSummary({{ $summary->id }}, '{{ $summary->nama_periode }}')"
                                                        title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr class="table-light">
                                        <td class="border-0 py-1">
                                            <small class="text-muted">Detail Penjualan</small>
                                        </td>
                                        <td class="border-0 py-1">
                                            <div class="d-flex justify-content-between mb-0">
                                                <small>
                                                    <i class="fa-brands fa-tiktok text-warning me-1"></i>Shopee:
                                                </small>
                                                <small class="fw-semibold">
                                                    Rp {{ number_format($summary->total_penghasilan_shopee, 0, ',', '.') }}
                                                </small>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <small>
                                                    <i class="fa-brands fa-tiktok text-info me-1"></i>Tiktok:
                                                </small>
                                                <small class="fw-semibold">
                                                    Rp {{ number_format($summary->total_penghasilan_tiktok, 0, ',', '.') }}
                                                </small>
                                            </div>
                                        </td>
                                        <td class="border-0 py-1">
                                            <div class="d-flex justify-content-between mb-0">
                                                <small>Shopee:</small>
                                                <small class="text-danger">
                                                    Rp {{ number_format($summary->total_hpp_shopee, 0, ',', '.') }}
                                                </small>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <small>Tiktok:</small>
                                                <small class="text-danger">
                                                    Rp {{ number_format($summary->total_hpp_tiktok, 0, ',', '.') }}
                                                </small>
                                            </div>
                                        </td>
                                        <td class="border-0 py-1">
                                            <div class="d-flex justify-content-between mb-0">
                                                <small>
                                                    <i class="fa-brands fa-tiktok text-primary me-1"></i>Shopee:
                                                </small>
                                                <small class="fw-semibold">
                                                    {{ number_format($summary->getOrdersCountByMarketplace('Shopee'), 0, ',', '.') }}
                                                </small>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <small>
                                                    <i class="fa-brands fa-tiktok text-info me-1"></i>Tiktok:
                                                </small>
                                                <small class="fw-semibold">
                                                    {{ number_format($summary->getOrdersCountByMarketplace('Tiktok'), 0, ',', '.') }}
                                                </small>
                                            </div>
                                        </td>
                                        <td class="border-0 py-1"></td>
                                    </tr>

                                    <tr>
                                        <td colspan="6" class="border-0 py-2"></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="generateModal" tabindex="-1" aria-labelledby="generateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="generateModalLabel">Generate Summary</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="generateForm">
                    <div class="modal-body">
                        @csrf
                        <div class="mb-3">
                            <label for="year" class="form-label">Tahun</label>
                            <select class="form-select" id="year" name="year" required>
                                <option value="">Pilih Tahun</option>
                                @foreach($years as $year)
                                    <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="month" class="form-label">Bulan</label>
                            <select class="form-select" id="month" name="month" required>
                                <option value="">Pilih Bulan</option>
                                @foreach($months as $key => $month)
                                    <option value="{{ $key }}" {{ $key == date('m') ? 'selected' : '' }}>
                                        {{ $month }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-play me-1"></i> Generate
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Summary</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detailModalBody">
                    </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="dashboardModal" tabindex="-1" aria-labelledby="dashboardModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dashboardModalLabel">Dashboard Summary</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="dashboardModalBody">
                    </div>
            </div>
        </div>
    </div>

    <script>
        // Show alert message
        function showAlert(message, type = 'success') {
            const alertContainer = document.getElementById('alertContainer');
            const alert = document.createElement('div');
            alert.className = `alert alert-${type} alert-dismissible fade show`;
            alert.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            alertContainer.appendChild(alert);

            // Auto remove after 5 seconds
            setTimeout(() => {
                alert.remove();
            }, 5000);
        }

        // Generate for specific month
        document.getElementById('generateForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const button = this.querySelector('button[type="submit"]');
            const originalText = button.innerHTML;

            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
            button.disabled = true;

            fetch('{{ route("monthly-summaries.generate") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    $('#generateModal').modal('hide');
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showAlert(data.message, 'danger');
                }
            })
            .catch(error => {
                showAlert('Terjadi kesalahan: ' + error, 'danger');
            })
            .finally(() => {
                button.innerHTML = originalText;
                button.disabled = false;
            });
        });

        // Generate current month
        function generateCurrentMonth() {
            if (!confirm('Generate summary untuk bulan berjalan?')) return;

            fetch('{{ route("monthly-summaries.generate.current") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.message, 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        showAlert(data.message, 'danger');
                    }
                })
                .catch(error => {
                    showAlert('Terjadi kesalahan: ' + error, 'danger');
                });
        }

        // Show detail modal
        function showDetail(summaryId) {
            fetch(`/monthly-summaries/${summaryId}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('detailModalBody').innerHTML = html;
                    $('#detailModal').modal('show');
                })
                .catch(error => {
                    showAlert('Gagal memuat detail: ' + error, 'danger');
                });
        }

        // Delete summary
        function deleteSummary(summaryId, summaryName) {
            if (!confirm(`Hapus summary "${summaryName}"?`)) return;

            fetch(`/monthly-summaries/${summaryId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showAlert(data.message, 'danger');
                }
            })
            .catch(error => {
                showAlert('Terjadi kesalahan: ' + error, 'danger');
            });
        }

        // Load dashboard modal content
        $('#dashboardModal').on('show.bs.modal', function () {
            fetch('{{ route("monthly-summaries.dashboard") }}')
                .then(response => response.text())
                .then(html => {
                    document.getElementById('dashboardModalBody').innerHTML = html;
                })
                .catch(error => {
                    document.getElementById('dashboardModalBody').innerHTML =
                        '<div class="alert alert-danger">Gagal memuat dashboard</div>';
                });
        });
    </script>
</x-app-layout>
