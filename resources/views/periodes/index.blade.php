<!-- resources/views/periodes/index.blade.php -->
<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Summary Bulanan</h5>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-primary btn-sm" onclick="openAddPeriodeModal()">
                                <i class="fas fa-plus"></i> Tambah Periode
                            </button>
                            <button type="button" class="btn btn-success btn-sm" onclick="generateCurrentMonth()">
                                <i class="fas fa-sync"></i> Generate Bulan Ini
                            </button>
                            <button type="button" class="btn btn-warning btn-sm" onclick="generateAllPending()">
                                <i class="fas fa-play-circle"></i> Generate All Pending
                            </button>
                            <!-- Tombol Regenerate -->
                            <button type="button" class="btn btn-secondary btn-sm" onclick="regenerateAll()">
                                <i class="fas fa-redo"></i> Update Semua
                            </button>
                            {{-- <button type="button" class="btn btn-dark btn-sm" onclick="generateOrRegenerateAll()">
                                <i class="fas fa-sync-alt"></i> Generate/Update All
                            </button> --}}
                        </div>
                    </div>

                    <div class="card-body">
                        <div id="alertContainer"></div>

                        <!-- SweetAlert Notifications -->
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

                        @if($errors->any())
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                Swal.fire({
                                    icon: "error",
                                    title: "Gagal!",
                                    html: "Mohon periksa kembali input Anda.",
                                    showConfirmButton: true
                                });
                            });
                        </script>
                        @endif

                        <!-- Tab Navigasi Berdasarkan Toko -->
                        <ul class="nav nav-tabs mb-4" id="tokoTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="tab-all-toko" data-bs-toggle="tab" data-bs-target="#pane-all" type="button" role="tab" aria-controls="pane-all" aria-selected="true">
                                    <i class="fas fa-store-alt me-1"></i> Semua Toko
                                    <span class="badge bg-primary ms-1">{{ $periodes->count() }}</span>
                                </button>
                            </li>
                            @foreach($tokos as $toko)
                            @php
                                $tokoCount = $periodes->where('toko_id', $toko->id)->count();
                            @endphp
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab-toko-{{ $toko->id }}" data-bs-toggle="tab" data-bs-target="#pane-toko-{{ $toko->id }}" type="button" role="tab" aria-controls="pane-toko-{{ $toko->id }}" aria-selected="false" data-toko-id="{{ $toko->id }}">
                                    <i class="fas fa-store me-1"></i> {{ $toko->nama }}
                                    <span class="badge bg-secondary ms-1">{{ $tokoCount }}</span>
                                </button>
                            </li>
                            @endforeach
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content" id="tokoTabsContent">
                            <!-- Tab Semua Toko -->
                            <div class="tab-pane fade show active" id="pane-all" role="tabpanel" aria-labelledby="tab-all-toko">
                                @include('periodes.partials.table', [
                                    'periodesList' => $periodes,
                                    'tableId' => 'table-periode-all'
                                ])
                            </div>

                            <!-- Tab per Toko -->
                            @foreach($tokos as $toko)
                            <div class="tab-pane fade" id="pane-toko-{{ $toko->id }}" role="tabpanel" aria-labelledby="tab-toko-{{ $toko->id }}">
                                @include('periodes.partials.table', [
                                    'periodesList' => $periodes->where('toko_id', $toko->id),
                                    'tableId' => 'table-periode-' . $toko->id,
                                    'toko' => $toko
                                ])
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Generate Periode -->
    <div class="modal fade" id="generateModal" tabindex="-1" aria-labelledby="generateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="generateModalLabel">Tambah Periode Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="generateForm">
                    <div class="modal-body">
                        @csrf
                        <div class="mb-3">
                            <label for="toko_id" class="form-label">Toko</label>
                            <select class="form-select" id="toko_id" name="toko_id" required>
                                <option value="">Pilih Toko</option>
                                @foreach($tokos as $toko)
                                <option value="{{ $toko->id }}">{{ $toko->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="year" class="form-label">Tahun</label>
                            <select class="form-select" id="year" name="year" required>
                                <option value="">Pilih Tahun</option>
                                @foreach($years as $year)
                                <option value="{{ $year }}" {{ $year==date('Y') ? 'selected' : '' }}>
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
                                <option value="{{ $key }}" {{ $key==date('m') ? 'selected' : '' }}>
                                    {{ $month }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i> Tambah
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Periode</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detailModalBody">
                    <!-- Detail akan dimuat via AJAX -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Periode</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_periode_id" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_nama_periode" class="form-label">Nama Periode *</label>
                            <input type="text" class="form-control" id="edit_nama_periode" name="nama_periode" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_tanggal_mulai" class="form-label">Tanggal Mulai *</label>
                                    <input type="date" class="form-control" id="edit_tanggal_mulai" name="tanggal_mulai"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_tanggal_selesai" class="form-label">Tanggal Selesai *</label>
                                    <input type="date" class="form-control" id="edit_tanggal_selesai"
                                        name="tanggal_selesai" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_toko_id" class="form-label">Toko *</label>
                            <select class="form-select" id="edit_toko_id" name="toko_id" required>
                                <option value="">Pilih Toko</option>
                                <!-- Options akan diisi via JS -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_marketplace" class="form-label">Marketplace *</label>
                            <select class="form-select" id="edit_marketplace" name="marketplace" required>
                                <option value="">Pilih Marketplace</option>
                                <option value="Shopee">Shopee</option>
                                <option value="Tiktok">Tiktok</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Show SweetAlert message
    function showSweetAlert(title, text, icon = 'success', timer = 3000) {
        Swal.fire({
            icon: icon,
            title: title,
            text: text,
            showConfirmButton: icon === 'error',
            timer: icon === 'error' ? null : timer
        });
    }

    // Show alert message (fallback)
    function showAlert(message, type = 'success') {
        const alertContainer = document.getElementById('alertContainer');
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        alertContainer.appendChild(alert);

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

        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
        button.disabled = true;

        fetch('{{ route("periodes.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSweetAlert('Berhasil!', data.message, 'success');
                $('#generateModal').modal('hide');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                if (data.errors) {
                    let errorMessages = '';
                    for (const field in data.errors) {
                        errorMessages += data.errors[field].join('<br>') + '<br>';
                    }
                    showSweetAlert('Gagal!', errorMessages, 'error');
                } else {
                    showSweetAlert('Gagal!', data.message, 'error');
                }
            }
        })
        .catch(error => {
            showSweetAlert('Error!', 'Terjadi kesalahan: ' + error, 'error');
        })
        .finally(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        });
    });

    // Generate current month
    function generateCurrentMonth() {
        Swal.fire({
            title: 'Generate Bulan Ini?',
            text: 'Generate periode untuk bulan berjalan?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Generate',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route("periodes.generate.current") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSweetAlert('Berhasil!', data.message, 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showSweetAlert('Gagal!', data.message, 'error');
                    }
                })
                .catch(error => {
                    showSweetAlert('Error!', 'Terjadi kesalahan: ' + error, 'error');
                });
            }
        });
    }

    // Generate all pending
    function generateAllPending() {
        Swal.fire({
            title: 'Generate Semua Pending?',
            text: 'Generate semua periode yang belum di-generate?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Generate',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route("periodes.generate.all") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSweetAlert('Berhasil!', data.message, 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showSweetAlert('Gagal!', data.message, 'error');
                    }
                })
                .catch(error => {
                    showSweetAlert('Error!', 'Terjadi kesalahan: ' + error, 'error');
                });
            }
        });
    }

    // Regenerate all
    function regenerateAll() {
        Swal.fire({
            title: 'Update Semua?',
            text: 'Update data untuk SEMUA periode yang sudah di-generate?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Update',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route("periodes.regenerate.all") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSweetAlert('Berhasil!', data.message, 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showSweetAlert('Gagal!', data.message, 'error');
                    }
                })
                .catch(error => {
                    showSweetAlert('Error!', 'Terjadi kesalahan: ' + error, 'error');
                });
            }
        });
    }

    // Generate or regenerate all
    function generateOrRegenerateAll() {
        Swal.fire({
            title: 'Generate/Update Semua?',
            text: 'Generate semua periode baru DAN update semua periode yang sudah ada?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Proses Semua',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route("periodes.generate.or.regenerate.all") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSweetAlert('Berhasil!', data.message, 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showSweetAlert('Gagal!', data.message, 'error');
                    }
                })
                .catch(error => {
                    showSweetAlert('Error!', 'Terjadi kesalahan: ' + error, 'error');
                });
            }
        });
    }

    // Generate specific periode
    function generatePeriode(periodeId) {
        Swal.fire({
            title: 'Generate Periode?',
            text: 'Generate data untuk periode ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Generate',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/periodes/${periodeId}/generate`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSweetAlert('Berhasil!', data.message, 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showSweetAlert('Gagal!', data.message, 'error');
                    }
                })
                .catch(error => {
                    showSweetAlert('Error!', 'Terjadi kesalahan: ' + error, 'error');
                });
            }
        });
    }

    // Regenerate specific periode
    function regeneratePeriode(periodeId) {
        Swal.fire({
            title: 'Update Data?',
            text: 'Update data untuk periode ini? Data akan dihitung ulang.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Update',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/periodes/${periodeId}/regenerate`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSweetAlert('Berhasil!', data.message, 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showSweetAlert('Gagal!', data.message, 'error');
                    }
                })
                .catch(error => {
                    showSweetAlert('Error!', 'Terjadi kesalahan: ' + error, 'error');
                });
            }
        });
    }

    // Show detail modal
    function showDetail(periodeId) {
        fetch(`/periodes/${periodeId}`)
            .then(response => response.text())
            .then(html => {
                document.getElementById('detailModalBody').innerHTML = html;
                $('#detailModal').modal('show');
            })
            .catch(error => {
                showSweetAlert('Error!', 'Gagal memuat detail: ' + error, 'error');
            });
    }

    // Delete periode
    function deletePeriode(periodeId, periodeName) {
        Swal.fire({
            title: 'Hapus Periode?',
            html: `Yakin menghapus periode <strong>"${periodeName}"</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/periodes/${periodeId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSweetAlert('Berhasil!', data.message, 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showSweetAlert('Gagal!', data.message, 'error');
                    }
                })
                .catch(error => {
                    showSweetAlert('Error!', 'Terjadi kesalahan: ' + error, 'error');
                });
            }
        });
    }

    function openAddPeriodeModal(tokoId = null) {
        const tokoSelect = document.getElementById('toko_id');
        if (tokoId) {
            if (tokoSelect) tokoSelect.value = tokoId;
        } else {
            const activeTabBtn = document.querySelector('#tokoTabs .nav-link.active');
            if (activeTabBtn && activeTabBtn.dataset.tokoId && tokoSelect) {
                tokoSelect.value = activeTabBtn.dataset.tokoId;
            }
        }
        $('#generateModal').modal('show');
    }

    // Inisialisasi DataTable untuk setiap tabel periode
    document.addEventListener('DOMContentLoaded', function() {
        $('table.datatable-periode').each(function() {
            $(this).DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
                },
                order: [], // Mempertahankan urutan dari server (ID terbesar di paling atas)
                pageLength: 25
            });
        });

        // Event listener saat tab diganti untuk menyesuaikan lebar kolom DataTables
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            const target = $(e.target).attr('data-bs-target');
            if (target) {
                localStorage.setItem('active_periode_tab', target);
            }
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust().responsive.recalc();
        });

        // Pulihkan tab aktif terakhir yang disimpan
        const savedTab = localStorage.getItem('active_periode_tab');
        if (savedTab) {
            const triggerEl = document.querySelector(`button[data-bs-target="${savedTab}"]`);
            if (triggerEl) {
                const tab = bootstrap.Tab.getOrCreateInstance(triggerEl);
                tab.show();
            }
        }
    });

    function editPeriode(periodeId) {
        // Load data via AJAX
        fetch(`/periodes/${periodeId}/edit`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const periode = data.data.periode;
                    const tokos = data.data.tokos;

                    // Isi form
                    document.getElementById('edit_periode_id').value = periode.id;
                    document.getElementById('edit_nama_periode').value = periode.nama_periode || '';
                    document.getElementById('edit_tanggal_mulai').value = periode.tanggal_mulai || '';
                    document.getElementById('edit_tanggal_selesai').value = periode.tanggal_selesai || '';
                    document.getElementById('edit_marketplace').value = periode.marketplace || '';

                    // Isi dropdown toko
                    const tokoSelect = document.getElementById('edit_toko_id');
                    tokoSelect.innerHTML = '<option value="">Pilih Toko</option>';

                    tokos.forEach(toko => {
                        const option = document.createElement('option');
                        option.value = toko.id;
                        option.textContent = toko.nama;
                        option.selected = (toko.id == periode.toko_id);
                        tokoSelect.appendChild(option);
                    });

                    // Show modal
                    $('#editModal').modal('show');
                } else {
                    showSweetAlert('Gagal!', 'Tidak dapat memuat data periode', 'error');
                }
            })
            .catch(error => {
                showSweetAlert('Error!', 'Gagal memuat data: ' + error, 'error');
            });
    }

    // Submit form edit
    document.getElementById('editForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const periodeId = document.getElementById('edit_periode_id').value;
        const button = this.querySelector('button[type="submit"]');
        const originalText = button.innerHTML;

        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
        button.disabled = true;

        fetch(`/periodes/${periodeId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-HTTP-Method-Override': 'PUT'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSweetAlert('Berhasil!', data.message, 'success');
                $('#editModal').modal('hide');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                if (data.errors) {
                    let errorMessages = '';
                    for (const field in data.errors) {
                        errorMessages += data.errors[field].join('<br>') + '<br>';
                    }
                    showSweetAlert('Gagal!', errorMessages, 'error');
                } else {
                    showSweetAlert('Gagal!', data.message, 'error');
                }
            }
        })
        .catch(error => {
            showSweetAlert('Error!', 'Terjadi kesalahan: ' + error, 'error');
        })
        .finally(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        });
    });
    </script>

</x-app-layout>
