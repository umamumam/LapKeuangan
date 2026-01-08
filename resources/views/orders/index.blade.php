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

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0"><i class="fas fa-list"></i> Daftar Order</h5>
                            <span class="badge bg-primary ms-3">
                                <i class="fas fa-database me-1"></i> Total: {{ $orders->total() }}
                            </span>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('orders.import.form') }}" class="btn btn-info btn-sm">
                                <i class="fas fa-file-import"></i> Import
                            </a>
                            <div class="dropdown">
                                <button class="btn btn-success btn-sm dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-file-export"></i> Export
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('orders.export') }}">
                                            <i class="fas fa-download me-2"></i> Export Semua Data
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <h6 class="dropdown-header">Export per Periode:</h6>
                                    </li>
                                    @foreach($periodes as $periode)
                                    <li>
                                        <form action="{{ route('orders.export.periode') }}" method="POST" class="d-inline">
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
                            <a href="{{ route('orders.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Tambah Order
                            </a>
                            @if($orders->count() > 0)
                            <button type="button" class="btn btn-danger btn-sm" onclick="showDeleteOptions()">
                                <i class="fas fa-trash-alt"></i> Hapus
                            </button>
                            @endif
                        </div>
                    </div>
                    <div class="card-body" style="overflow-x:auto;">
                        @if($orders->count() > 0)
                        <table id="res-config" class="display table table-striped table-hover dt-responsive nowrap"
                            style="width: 100%">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>No. Pesanan</th>
                                    <th>No. Resi</th>
                                    <th>Produk</th>
                                    <th>HPP</th>
                                    <th>Jumlah</th>
                                    <th>Returned Qty</th>
                                    <th>Total Harga Produk</th>
                                    <th>Periode</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $order->no_pesanan }}</td>
                                    <td>{{ $order->no_resi ?? '-' }}</td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <strong>{{ $order->produk->nama_produk }}</strong>
                                            @if($order->produk->nama_variasi)
                                            <small class="text-muted">Variasi: {{ $order->produk->nama_variasi }}</small>
                                            @endif
                                            <small class="text-muted">SKU: {{ $order->produk->sku_induk }}</small>
                                        </div>
                                    </td>
                                    <td>{{ $order->produk->hpp_produk }}</td>
                                    <td>{{ $order->jumlah }}</td>
                                    <td>{{ $order->returned_quantity }}</td>
                                    <td>{{ $order->total_harga_produk }}</td>
                                    <td>
                                        @if($order->periode)
                                        <div class="d-flex flex-column">
                                            <span class="badge bg-{{ $order->periode->marketplace == 'Shopee' ? 'warning' : 'info' }}">
                                                {{ $order->periode->marketplace }}
                                            </span>
                                            <small class="text-muted">
                                                {{ $order->periode->nama_periode }}
                                            </small>
                                        </div>
                                        @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-times me-1"></i> Tanpa Periode
                                        </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-info btn-sm"
                                                title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('orders.edit', $order->id) }}"
                                                class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm" title="Hapus"
                                                onclick="deleteOrder({{ $order->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <div class="text-center py-4">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada data order.</p>
                            <a href="{{ route('orders.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tambah Order Pertama
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
// Delete order with SweetAlert
function deleteOrder(orderId) {
    Swal.fire({
        title: 'Hapus Order?',
        text: 'Data order akan dihapus permanen.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/orders/${orderId}`;

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

// Show delete options
function showDeleteOptions() {
    Swal.fire({
        title: 'Pilih Jenis Hapus',
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: 'Hapus Semua Order',
        denyButtonText: 'Hapus per Periode',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d33',
        denyButtonColor: '#f39c12',
        icon: 'question'
    }).then((result) => {
        if (result.isConfirmed) {
            deleteAllOrders();
        } else if (result.isDenied) {
            deleteByPeriode();
        }
    });
}

// Delete all orders
function deleteAllOrders() {
    Swal.fire({
        title: 'Hapus Semua Order?',
        html: `Yakin menghapus <strong>semua data order</strong>?<br>
               <small class="text-danger">Tindakan ini tidak dapat dibatalkan!</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus Semua',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d33',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return fetch('{{ route("orders.deleteAll") }}', {
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
                text: result.value.message || 'Semua data order berhasil dihapus!',
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

// Delete by periode - versi sederhana
function deleteByPeriode() {
    Swal.fire({
        title: 'Hapus Order per Periode',
        html: `
            <div class="text-start">
                <p>Anda akan menghapus semua order pada periode tertentu.</p>
                <div class="mb-3">
                    <label for="periodeSelect" class="form-label">Pilih Periode:</label>
                    <select class="form-select" id="periodeSelect">
                        <option value="">-- Pilih Periode --</option>
                        @php
                            $periodes = \App\Models\Periode::orderBy('nama_periode', 'desc')->get();
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
                html: `Yakin menghapus semua order pada periode ini?<br>
                       <small class="text-danger">Semua order akan dihapus permanen!</small>`,
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

                    return fetch('{{ route("orders.delete.by.periode") }}', {
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
                        text: result2.value.message || 'Order berhasil dihapus!',
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
            pageLength: 25
        });
    }
});
</script>
