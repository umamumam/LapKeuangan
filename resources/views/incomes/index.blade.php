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

                    @if(session('warning'))
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            Swal.fire({
                                icon: "warning",
                                title: "Peringatan!",
                                text: "{{ session('warning') }}",
                                showConfirmButton: true,
                            });
                        });
                    </script>
                    @endif

                    @if(session('error'))
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            Swal.fire({
                                icon: "error",
                                title: "Gagal!",
                                text: "{{ session('error') }}",
                                showConfirmButton: false,
                                timer: 3000
                            });
                        });
                    </script>
                    @endif

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0"><i class="fas fa-money-bill-wave"></i> Daftar Income</h5>
                            <span class="badge bg-primary ms-3">
                                <i class="fas fa-database me-1"></i> Total: {{ $incomes->total() }}
                            </span>
                            <!-- Income Bulan Ini -->
                            <span class="badge bg-success ms-2">
                                <i class="fas fa-calendar-check me-1"></i> Penghasilan Bulan Ini: Rp {{ number_format($totalIncomeBulanIni, 0, ',', '.') }}
                            </span>
                            {{-- <span class="badge bg-info ms-2">
                                <i class="fas fa-box me-1"></i> HPP Bulan Ini: Rp {{ number_format($totalIncomeBulanIni, 0, ',', '.') }}
                            </span> --}}
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('incomes.import.form') }}" class="btn btn-info btn-sm">
                                <i class="fas fa-file-import"></i> Import
                            </a>
                            <div class="dropdown">
                                <button class="btn btn-success btn-sm dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-file-export"></i> Export
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('incomes.export') }}">
                                            <i class="fas fa-download me-2"></i> Export Semua Data
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <h6 class="dropdown-header">Export per Periode:</h6>
                                    </li>
                                    @foreach($periodes as $periode)
                                    <li>
                                        <form action="{{ route('incomes.export.periode') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="periode_id" value="{{ $periode->id }}">
                                            <button type="submit" class="dropdown-item" style="cursor: pointer;">
                                                <i class="fas fa-calendar-alt me-2"></i>
                                                {{ $periode->nama_periode }}
                                                <small class="text-muted ms-1">({{ $periode->marketplace }})</small>
                                            </button>
                                        </form>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            <a href="{{ route('incomes.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Tambah Income
                            </a>
                            @if($incomes->count() > 0)
                            <button type="button" class="btn btn-danger btn-sm" onclick="showDeleteOptions()">
                                <i class="fas fa-trash-alt"></i> Hapus
                            </button>
                            @endif
                        </div>
                    </div>
                    <div class="card-body" style="overflow-x:auto;">
                        @if($incomes->count() > 0)
                        <table id="res-config" class="display table table-striped table-hover dt-responsive nowrap"
                            style="width: 100%">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>No. Pesanan</th>
                                    <th>No. Pengajuan</th>
                                    <th>Total Penghasilan</th>
                                    <th>Total HPP</th>
                                    <th>Laba</th>
                                    <th>Jumlah Item</th>
                                    <th>Periode</th>
                                    <th>Marketplace</th>
                                    <th>Toko</th>
                                    {{-- <th>Tanggal Dibuat</th> --}}
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($incomes as $income)
                                @php
                                    $totalHpp = $income->orders->where('periode_id', $income->periode_id)->sum(function ($order) {
                                        $netQuantity = $order->jumlah - $order->returned_quantity;
                                        return $netQuantity * $order->produk->hpp_produk;
                                    });
                                    $laba = $income->total_penghasilan - $totalHpp;
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $income->no_pesanan }}</strong>
                                    </td>
                                    <td>{{ $income->no_pengajuan ?? '-' }}</td>
                                    <td>Rp {{ number_format($income->total_penghasilan, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($totalHpp, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $laba >= 0 ? 'success' : 'danger' }}">
                                            Rp {{ number_format($laba, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $income->orders->where('periode_id', $income->periode_id)->count() }} item</span>
                                    </td>
                                    <td>
                                        @if($income->periode)
                                        <div class="d-flex flex-column">
                                            <span class="badge bg-primary">{{ $income->periode->nama_periode }}</span>
                                            <small class="text-muted">
                                                {{ $income->periode->tanggal_mulai->format('d/m/Y') }} - {{ $income->periode->tanggal_selesai->format('d/m/Y') }}
                                            </small>
                                        </div>
                                        @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-times me-1"></i> Tanpa Periode
                                        </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($income->periode)
                                        <span class="badge bg-{{ $income->periode->marketplace == 'Shopee' ? 'warning' : 'info' }}">
                                            {{ $income->periode->marketplace }}
                                        </span>
                                        @else
                                        <span class="badge bg-secondary">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($income->periode && $income->periode->toko)
                                        {{ $income->periode->toko->nama }}
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    {{-- <td>{{ $income->created_at->format('d/m/Y H:i') }}</td> --}}
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('incomes.show', $income->id) }}"
                                                class="btn btn-info btn-sm" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('incomes.edit', $income->id) }}"
                                                class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm" title="Hapus"
                                                onclick="deleteIncome({{ $income->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @if(!$income->periode_id)
                                            <button type="button" class="btn btn-secondary btn-sm" title="Hitung Otomatis"
                                                onclick="calculateTotal({{ $income->id }})">
                                                <i class="fas fa-calculator"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <div class="text-center py-4">
                            <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada data income.</p>
                            <a href="{{ route('incomes.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tambah Income Pertama
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
// Delete income with SweetAlert
function deleteIncome(incomeId) {
    Swal.fire({
        title: 'Hapus Income?',
        text: 'Data income akan dihapus permanen.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/incomes/${incomeId}`;

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';

            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';

            form.appendChild(csrf);
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Calculate total automatically
function calculateTotal(incomeId) {
    Swal.fire({
        title: 'Hitung Total Penghasilan?',
        text: 'Total penghasilan akan dihitung otomatis dari order terkait.',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hitung',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#28a745',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return fetch(`/incomes/${incomeId}/calculate-total`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (response.redirected) {
                    // Handle redirect from controller
                    return { redirected: true, url: response.url };
                }
                return response.json();
            })
            .catch(error => {
                Swal.showValidationMessage(`Request failed: ${error}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            if (result.value.redirected) {
                // Redirect to the success page
                window.location.href = result.value.url;
            } else {
                // Show success message and reload
                Swal.fire({
                    title: 'Berhasil!',
                    text: result.value.message || 'Total penghasilan berhasil dihitung!',
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            }
        }
    });
}

// Show delete options
function showDeleteOptions() {
    Swal.fire({
        title: 'Pilih Jenis Hapus',
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: 'Hapus Semua Income',
        denyButtonText: 'Hapus per Periode',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d33',
        denyButtonColor: '#f39c12',
        icon: 'question'
    }).then((result) => {
        if (result.isConfirmed) {
            deleteAllIncomes();
        } else if (result.isDenied) {
            deleteByPeriode();
        }
    });
}

// Delete all incomes
function deleteAllIncomes() {
    Swal.fire({
        title: 'Hapus Semua Income?',
        html: `Yakin menghapus <strong>semua data income</strong>?<br>
               <small class="text-danger">Tindakan ini tidak dapat dibatalkan!</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus Semua',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d33',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return fetch('{{ route("incomes.deleteAll") }}', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    throw new Error(data.message || 'Gagal menghapus data');
                }
                return data;
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Berhasil!',
                text: result.value.message || 'Semua data income berhasil dihapus!',
                icon: 'success',
                timer: 3000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        }
    }).catch(error => {
        Swal.fire({
            title: 'Gagal!',
            text: error.message || 'Terjadi kesalahan saat menghapus data',
            icon: 'error'
        });
    });
}

// Delete by periode
function deleteByPeriode() {
    Swal.fire({
        title: 'Hapus Income per Periode',
        html: `
            <div class="text-start">
                <p>Anda akan menghapus semua income pada periode tertentu.</p>
                <div class="mb-3">
                    <label for="periodeSelect" class="form-label">Pilih Periode:</label>
                    <select class="form-select" id="periodeSelect">
                        <option value="">-- Pilih Periode --</option>
                        @php
                            // Ambil periode dari controller (sudah ada di $periodes)
                            $periodes = $periodes ?? \App\Models\Periode::orderBy('nama_periode', 'desc')->get();
                        @endphp
                        @foreach($periodes as $periode)
                            <option value="{{ $periode->id }}">
                                {{ $periode->nama_periode }} ({{ $periode->marketplace }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong class="ms-2">Tindakan ini tidak dapat dibatalkan!</strong>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Lanjutkan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#f39c12',
        showLoaderOnConfirm: false,
        preConfirm: () => {
            const periodeId = document.getElementById('periodeSelect').value;
            if (!periodeId) {
                Swal.showValidationMessage('Pilih periode terlebih dahulu');
                return false;
            }
            return periodeId;
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            // Konfirmasi final
            Swal.fire({
                title: 'Konfirmasi Hapus',
                html: `Yakin menghapus semua income pada periode ini?<br>
                       <small class="text-danger">Semua income akan dihapus permanen!</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus Semua',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    const formData = new FormData();
                    formData.append('periode_id', result.value);
                    formData.append('_token', '{{ csrf_token() }}');

                    return fetch('{{ route("incomes.delete.by.periode") }}', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            throw new Error(data.message || 'Gagal menghapus data');
                        }
                        return data;
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result2) => {
                if (result2.isConfirmed) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: result2.value.message || 'Income berhasil dihapus!',
                        icon: 'success',
                        timer: 3000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                }
            }).catch(error => {
                Swal.fire({
                    title: 'Gagal!',
                    text: error.message || 'Terjadi kesalahan saat menghapus data',
                    icon: 'error'
                });
            });
        }
    });
}

// Initialize DataTable
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('res-config')) {
        $('#res-config').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            },
            order: [[0, 'desc']],
            pageLength: 25,
            columnDefs: [
                { responsivePriority: 1, targets: 0 }, // Kolom #
                { responsivePriority: 2, targets: 1 }, // No. Pesanan
                { responsivePriority: 3, targets: -1 }, // Aksi
                { responsivePriority: 4, targets: 2 }, // No. Pengajuan
                { responsivePriority: 5, targets: 3 }  // Total Penghasilan
            ]
        });
    }
});
</script>
