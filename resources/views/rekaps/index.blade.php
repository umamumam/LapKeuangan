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

                    @if(session('error'))
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            Swal.fire({
                                icon: "error",
                                title: "Gagal!",
                                text: "{{ session('error') }}",
                                showConfirmButton: true
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

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Data Rekap Penjualan</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('rekaps.hasil') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-chart-bar"></i> Detail Rekap
                            </a>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#createRekapModal">
                                <i class="fas fa-plus"></i> Tambah Rekap
                            </button>
                        </div>
                    </div>

                    <div class="card-body" style="overflow-x:auto;">
                        <!-- Form Tambah Rekap -->
                        <div id="createFormContainer" style="display: none;">
                            <form action="{{ route('rekaps.store') }}" method="POST" class="mb-4 p-3 border rounded">
                                @csrf
                                <h6 class="mb-3"><i class="fas fa-plus"></i> Tambah Rekap Baru</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Periode <span class="text-danger">*</span></label>
                                        <select name="nama_periode" class="form-select" required>
                                            <option value="">Pilih Bulan</option>
                                            @foreach($bulanList as $bulan)
                                            <option value="{{ $bulan }}" {{ old('nama_periode')==$bulan ? 'selected' : '' }}>{{
                                                $bulan }}</option>
                                            @endforeach
                                        </select>
                                        @error('nama_periode')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tahun <span class="text-danger">*</span></label>
                                        <input type="number" name="tahun" class="form-control"
                                            value="{{ old('tahun', date('Y')) }}" min="2000" max="2100" required>
                                        @error('tahun')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Toko <span class="text-danger">*</span></label>
                                        <select name="toko_id" class="form-select" required>
                                            <option value="">Pilih Toko</option>
                                            @foreach($tokos as $toko)
                                            <option value="{{ $toko->id }}" {{ old('toko_id')==$toko->id ? 'selected' : '' }}>{{
                                                $toko->nama }}</option>
                                            @endforeach
                                        </select>
                                        @error('toko_id')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Pendapatan Shopee <span class="text-danger">*</span></label>
                                        <input type="number" name="total_pendapatan_shopee" class="form-control"
                                            value="{{ old('total_pendapatan_shopee', 0) }}" min="0" required>
                                        @error('total_pendapatan_shopee')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Pendapatan TikTok <span class="text-danger">*</span></label>
                                        <input type="number" name="total_pendapatan_tiktok" class="form-control"
                                            value="{{ old('total_pendapatan_tiktok', 0) }}" min="0" required>
                                        @error('total_pendapatan_tiktok')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Penghasilan Shopee <span class="text-danger">*</span></label>
                                        <input type="number" name="total_penghasilan_shopee" class="form-control"
                                            value="{{ old('total_penghasilan_shopee', 0) }}" min="0" required>
                                        @error('total_penghasilan_shopee')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Penghasilan TikTok <span class="text-danger">*</span></label>
                                        <input type="number" name="total_penghasilan_tiktok" class="form-control"
                                            value="{{ old('total_penghasilan_tiktok', 0) }}" min="0" required>
                                        @error('total_penghasilan_tiktok')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">HPP Shopee <span class="text-danger">*</span></label>
                                        <input type="number" name="total_hpp_shopee" class="form-control"
                                            value="{{ old('total_hpp_shopee', 0) }}" min="0" required>
                                        @error('total_hpp_shopee')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">HPP TikTok <span class="text-danger">*</span></label>
                                        <input type="number" name="total_hpp_tiktok" class="form-control"
                                            value="{{ old('total_hpp_tiktok', 0) }}" min="0" required>
                                        @error('total_hpp_tiktok')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Iklan Shopee <span class="text-danger">*</span></label>
                                        <input type="number" name="total_iklan_shopee" class="form-control"
                                            value="{{ old('total_iklan_shopee', 0) }}" min="0" required>
                                        @error('total_iklan_shopee')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Iklan TikTok <span class="text-danger">*</span></label>
                                        <input type="number" name="total_iklan_tiktok" class="form-control"
                                            value="{{ old('total_iklan_tiktok', 0) }}" min="0" required>
                                        @error('total_iklan_tiktok')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Operasional <span class="text-danger">*</span></label>
                                        <input type="number" name="operasional" class="form-control"
                                            value="{{ old('operasional', 0) }}" min="0" required>
                                        @error('operasional')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Rasio Admin Layanan Shopee (%)</label>
                                        <input type="number" step="0.01" name="rasio_admin_layanan_shopee" class="form-control"
                                            value="{{ old('rasio_admin_layanan_shopee', 0) }}" min="0" max="100" required>
                                        @error('rasio_admin_layanan_shopee')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Rasio Admin Layanan TikTok (%)</label>
                                        <input type="number" step="0.01" name="rasio_admin_layanan_tiktok" class="form-control"
                                            value="{{ old('rasio_admin_layanan_tiktok', 0) }}" min="0" max="100" required>
                                        @error('rasio_admin_layanan_tiktok')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">AOV Aktual Shopee</label>
                                        <input type="number" name="aov_aktual_shopee" class="form-control"
                                            value="{{ old('aov_aktual_shopee', 0) }}" min="0" required>
                                        @error('aov_aktual_shopee')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">AOV Aktual TikTok</label>
                                        <input type="number" name="aov_aktual_tiktok" class="form-control"
                                            value="{{ old('aov_aktual_tiktok', 0) }}" min="0" required>
                                        @error('aov_aktual_tiktok')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Basket Size Aktual Shopee</label>
                                        <input type="number" step="0.01" name="basket_size_aktual_shopee" class="form-control"
                                            value="{{ old('basket_size_aktual_shopee', 0) }}" min="0" required>
                                        @error('basket_size_aktual_shopee')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Basket Size Aktual TikTok</label>
                                        <input type="number" step="0.01" name="basket_size_aktual_tiktok" class="form-control"
                                            value="{{ old('basket_size_aktual_tiktok', 0) }}" min="0" required>
                                        @error('basket_size_aktual_tiktok')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">Tambah</button>
                                        <button type="button" class="btn btn-secondary ms-2" onclick="toggleCreateForm()">Batal</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        @if($rekaps->count() > 0)
                        <table id="res-config" class="display table table-striped table-hover dt-responsive nowrap"
                            style="width: 100%">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Periode</th>
                                    <th>Toko</th>
                                    <th>Total Pendapatan</th>
                                    <th>Total Penghasilan</th>
                                    <th>Total HPP</th>
                                    <th>Total Iklan</th>
                                    <th>Operasional</th>
                                    <th>Laba/Rugi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rekaps as $rekap)
                                @php
                                $total_pendapatan = $rekap->total_pendapatan_shopee + $rekap->total_pendapatan_tiktok;
                                $total_penghasilan = $rekap->total_penghasilan_shopee + $rekap->total_penghasilan_tiktok;
                                $total_hpp = $rekap->total_hpp_shopee + $rekap->total_hpp_tiktok;
                                $total_iklan = $rekap->total_iklan_shopee + $rekap->total_iklan_tiktok;
                                $profit = $total_penghasilan - $total_hpp - $total_iklan - $rekap->operasional;
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><strong>{{ $rekap->nama_periode }} {{ $rekap->tahun }}</strong></td>
                                    <td>{{ $rekap->toko->nama ?? '-' }}</td>
                                    <td>
                                        <div class="mb-1 fw-semibold">
                                            Rp {{ number_format($total_pendapatan, 0, ',', '.') }}
                                        </div>
                                        <div class="rekap-detail">
                                            <span class="badge bg-warning text-dark mb-1">
                                                Shopee: Rp {{ number_format($rekap->total_pendapatan_shopee, 0, ',', '.') }}
                                            </span><br>
                                            <span class="badge bg-dark">
                                                Tiktok: Rp {{ number_format($rekap->total_pendapatan_tiktok, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="mb-1 fw-semibold">
                                            Rp {{ number_format($total_penghasilan, 0, ',', '.') }}
                                        </div>
                                        <div class="rekap-detail">
                                            <span class="badge bg-warning text-dark mb-1">
                                                Shopee: Rp {{ number_format($rekap->total_penghasilan_shopee, 0, ',',
                                                '.') }}
                                            </span><br>
                                            <span class="badge bg-dark">
                                                Tiktok: Rp {{ number_format($rekap->total_penghasilan_tiktok, 0, ',',
                                                '.') }}
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="mb-1 fw-semibold">
                                            Rp {{ number_format($total_hpp, 0, ',', '.') }}
                                        </div>
                                        <div class="rekap-detail">
                                            <span class="badge bg-warning text-dark mb-1">
                                                Shopee: Rp {{ number_format($rekap->total_hpp_shopee, 0, ',', '.') }}
                                            </span><br>
                                            <span class="badge bg-dark">
                                                Tiktok: Rp {{ number_format($rekap->total_hpp_tiktok, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="mb-1 fw-semibold">
                                            Rp {{ number_format($total_iklan, 0, ',', '.') }}
                                        </div>
                                        <div class="rekap-detail">
                                            <span class="badge bg-warning text-dark mb-1">
                                                Shopee: Rp {{ number_format($rekap->total_iklan_shopee, 0, ',', '.') }}
                                            </span><br>
                                            <span class="badge bg-dark">
                                                Tiktok: Rp {{ number_format($rekap->total_iklan_tiktok, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="mb-1">Rp {{ number_format($rekap->operasional, 0, ',', '.') }}</div>
                                        <div class="rekap-detail">
                                            <small class="text-secondary">
                                                <i class="fas fa-cogs me-1"></i>Operasional
                                            </small>
                                        </div>
                                    </td>
                                    <td class="{{ $profit >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
                                        <div class="mb-1">Rp {{ number_format($profit, 0, ',', '.') }}</div>
                                        <div class="rekap-detail">
                                            <small class="{{ $profit >= 0 ? 'text-success' : 'text-danger' }}">
                                                <i
                                                    class="{{ $profit >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down' }} me-1"></i>
                                                {{ $profit >= 0 ? 'Profit' : 'Rugi' }}
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#editRekapModal{{ $rekap->id }}" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('rekaps.destroy', $rekap->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus rekap ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Edit Modal for each Rekap -->
                                <div class="modal fade" id="editRekapModal{{ $rekap->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <form action="{{ route('rekaps.update', $rekap->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">
                                                        <i class="fas fa-edit"></i> Edit Rekap
                                                    </h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Periode <span
                                                                    class="text-danger">*</span></label>
                                                            <select name="nama_periode" class="form-select" required>
                                                                <option value="">Pilih Bulan</option>
                                                                @foreach($bulanList as $bulan)
                                                                <option value="{{ $bulan }}" {{ old('nama_periode',
                                                                    $rekap->nama_periode) == $bulan ? 'selected' : ''
                                                                    }}>{{ $bulan }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('nama_periode')
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Tahun <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="number" name="tahun" class="form-control"
                                                                value="{{ old('tahun', $rekap->tahun) }}" min="2000"
                                                                max="2100" required>
                                                            @error('tahun')
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-12 mb-3">
                                                            <label class="form-label">Toko <span
                                                                    class="text-danger">*</span></label>
                                                            <select name="toko_id" class="form-select" required>
                                                                <option value="">Pilih Toko</option>
                                                                @foreach($tokos as $toko)
                                                                <option value="{{ $toko->id }}" {{ old('toko_id',
                                                                    $rekap->toko_id) == $toko->id ? 'selected' : ''
                                                                    }}>{{ $toko->nama }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('toko_id')
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Pendapatan Shopee <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="number" name="total_pendapatan_shopee"
                                                                class="form-control"
                                                                value="{{ old('total_pendapatan_shopee', $rekap->total_pendapatan_shopee) }}"
                                                                min="0" required>
                                                            @error('total_pendapatan_shopee')
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Pendapatan TikTok <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="number" name="total_pendapatan_tiktok"
                                                                class="form-control"
                                                                value="{{ old('total_pendapatan_tiktok', $rekap->total_pendapatan_tiktok) }}"
                                                                min="0" required>
                                                            @error('total_pendapatan_tiktok')
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Penghasilan Shopee <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="number" name="total_penghasilan_shopee"
                                                                class="form-control"
                                                                value="{{ old('total_penghasilan_shopee', $rekap->total_penghasilan_shopee) }}"
                                                                min="0" required>
                                                            @error('total_penghasilan_shopee')
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Penghasilan TikTok <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="number" name="total_penghasilan_tiktok"
                                                                class="form-control"
                                                                value="{{ old('total_penghasilan_tiktok', $rekap->total_penghasilan_tiktok) }}"
                                                                min="0" required>
                                                            @error('total_penghasilan_tiktok')
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">HPP Shopee <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="number" name="total_hpp_shopee"
                                                                class="form-control"
                                                                value="{{ old('total_hpp_shopee', $rekap->total_hpp_shopee) }}"
                                                                min="0" required>
                                                            @error('total_hpp_shopee')
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">HPP TikTok <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="number" name="total_hpp_tiktok"
                                                                class="form-control"
                                                                value="{{ old('total_hpp_tiktok', $rekap->total_hpp_tiktok) }}"
                                                                min="0" required>
                                                            @error('total_hpp_tiktok')
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Iklan Shopee <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="number" name="total_iklan_shopee"
                                                                class="form-control"
                                                                value="{{ old('total_iklan_shopee', $rekap->total_iklan_shopee) }}"
                                                                min="0" required>
                                                            @error('total_iklan_shopee')
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Iklan TikTok <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="number" name="total_iklan_tiktok"
                                                                class="form-control"
                                                                value="{{ old('total_iklan_tiktok', $rekap->total_iklan_tiktok) }}"
                                                                min="0" required>
                                                            @error('total_iklan_tiktok')
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-12 mb-3">
                                                            <label class="form-label">Operasional <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="number" name="operasional" class="form-control"
                                                                value="{{ old('operasional', $rekap->operasional) }}"
                                                                min="0" required>
                                                            @error('operasional')
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Rasio Admin Layanan Shopee (%)</label>
                                                            <input type="number" step="0.01" name="rasio_admin_layanan_shopee"
                                                                class="form-control"
                                                                value="{{ old('rasio_admin_layanan_shopee', $rekap->rasio_admin_layanan_shopee) }}"
                                                                min="0" max="100" required>
                                                            @error('rasio_admin_layanan_shopee')
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Rasio Admin Layanan TikTok (%)</label>
                                                            <input type="number" step="0.01" name="rasio_admin_layanan_tiktok"
                                                                class="form-control"
                                                                value="{{ old('rasio_admin_layanan_tiktok', $rekap->rasio_admin_layanan_tiktok) }}"
                                                                min="0" max="100" required>
                                                            @error('rasio_admin_layanan_tiktok')
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">AOV Aktual Shopee</label>
                                                            <input type="number" name="aov_aktual_shopee"
                                                                class="form-control"
                                                                value="{{ old('aov_aktual_shopee', $rekap->aov_aktual_shopee) }}"
                                                                min="0" required>
                                                            @error('aov_aktual_shopee')
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">AOV Aktual TikTok</label>
                                                            <input type="number" name="aov_aktual_tiktok"
                                                                class="form-control"
                                                                value="{{ old('aov_aktual_tiktok', $rekap->aov_aktual_tiktok) }}"
                                                                min="0" required>
                                                            @error('aov_aktual_tiktok')
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Basket Size Aktual Shopee</label>
                                                            <input type="number" step="0.01" name="basket_size_aktual_shopee"
                                                                class="form-control"
                                                                value="{{ old('basket_size_aktual_shopee', $rekap->basket_size_aktual_shopee) }}"
                                                                min="0" required>
                                                            @error('basket_size_aktual_shopee')
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Basket Size Aktual TikTok</label>
                                                            <input type="number" step="0.01" name="basket_size_aktual_tiktok"
                                                                class="form-control"
                                                                value="{{ old('basket_size_aktual_tiktok', $rekap->basket_size_aktual_tiktok) }}"
                                                                min="0" required>
                                                            @error('basket_size_aktual_tiktok')
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <div class="text-center py-4">
                            <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada data rekap.</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#createRekapModal">
                                <i class="fas fa-plus"></i> Tambah Rekap Pertama
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createRekapModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('rekaps.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-plus"></i> Tambah Rekap
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Periode <span class="text-danger">*</span></label>
                                <select name="nama_periode" class="form-select" required>
                                    <option value="">Pilih Bulan</option>
                                    @foreach($bulanList as $bulan)
                                    <option value="{{ $bulan }}" {{ old('nama_periode')==$bulan ? 'selected' : '' }}>{{
                                        $bulan }}</option>
                                    @endforeach
                                </select>
                                @error('nama_periode')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tahun <span class="text-danger">*</span></label>
                                <input type="number" name="tahun" class="form-control"
                                    value="{{ old('tahun', date('Y')) }}" min="2000" max="2100" required>
                                @error('tahun')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Toko <span class="text-danger">*</span></label>
                                <select name="toko_id" class="form-select" required>
                                    <option value="">Pilih Toko</option>
                                    @foreach($tokos as $toko)
                                    <option value="{{ $toko->id }}" {{ old('toko_id')==$toko->id ? 'selected' : '' }}>{{
                                        $toko->nama }}</option>
                                    @endforeach
                                </select>
                                @error('toko_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pendapatan Shopee <span class="text-danger">*</span></label>
                                <input type="number" name="total_pendapatan_shopee" class="form-control"
                                    value="{{ old('total_pendapatan_shopee', 0) }}" min="0" required>
                                @error('total_pendapatan_shopee')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pendapatan TikTok <span class="text-danger">*</span></label>
                                <input type="number" name="total_pendapatan_tiktok" class="form-control"
                                    value="{{ old('total_pendapatan_tiktok', 0) }}" min="0" required>
                                @error('total_pendapatan_tiktok')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Penghasilan Shopee <span class="text-danger">*</span></label>
                                <input type="number" name="total_penghasilan_shopee" class="form-control"
                                    value="{{ old('total_penghasilan_shopee', 0) }}" min="0" required>
                                @error('total_penghasilan_shopee')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Penghasilan TikTok <span class="text-danger">*</span></label>
                                <input type="number" name="total_penghasilan_tiktok" class="form-control"
                                    value="{{ old('total_penghasilan_tiktok', 0) }}" min="0" required>
                                @error('total_penghasilan_tiktok')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">HPP Shopee <span class="text-danger">*</span></label>
                                <input type="number" name="total_hpp_shopee" class="form-control"
                                    value="{{ old('total_hpp_shopee', 0) }}" min="0" required>
                                @error('total_hpp_shopee')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">HPP TikTok <span class="text-danger">*</span></label>
                                <input type="number" name="total_hpp_tiktok" class="form-control"
                                    value="{{ old('total_hpp_tiktok', 0) }}" min="0" required>
                                @error('total_hpp_tiktok')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Iklan Shopee <span class="text-danger">*</span></label>
                                <input type="number" name="total_iklan_shopee" class="form-control"
                                    value="{{ old('total_iklan_shopee', 0) }}" min="0" required>
                                @error('total_iklan_shopee')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Iklan TikTok <span class="text-danger">*</span></label>
                                <input type="number" name="total_iklan_tiktok" class="form-control"
                                    value="{{ old('total_iklan_tiktok', 0) }}" min="0" required>
                                @error('total_iklan_tiktok')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Operasional <span class="text-danger">*</span></label>
                                <input type="number" name="operasional" class="form-control"
                                    value="{{ old('operasional', 0) }}" min="0" required>
                                @error('operasional')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Rasio Admin Layanan Shopee (%)</label>
                                <input type="number" step="0.01" name="rasio_admin_layanan_shopee" class="form-control"
                                    value="{{ old('rasio_admin_layanan_shopee', 0) }}" min="0" max="100" required>
                                @error('rasio_admin_layanan_shopee')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Rasio Admin Layanan TikTok (%)</label>
                                <input type="number" step="0.01" name="rasio_admin_layanan_tiktok" class="form-control"
                                    value="{{ old('rasio_admin_layanan_tiktok', 0) }}" min="0" max="100" required>
                                @error('rasio_admin_layanan_tiktok')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">AOV Aktual Shopee</label>
                                <input type="number" name="aov_aktual_shopee" class="form-control"
                                    value="{{ old('aov_aktual_shopee', 0) }}" min="0" required>
                                @error('aov_aktual_shopee')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">AOV Aktual TikTok</label>
                                <input type="number" name="aov_aktual_tiktok" class="form-control"
                                    value="{{ old('aov_aktual_tiktok', 0) }}" min="0" required>
                                @error('aov_aktual_tiktok')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Basket Size Aktual Shopee</label>
                                <input type="number" step="0.01" name="basket_size_aktual_shopee" class="form-control"
                                    value="{{ old('basket_size_aktual_shopee', 0) }}" min="0" required>
                                @error('basket_size_aktual_shopee')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Basket Size Aktual TikTok</label>
                                <input type="number" step="0.01" name="basket_size_aktual_tiktok" class="form-control"
                                    value="{{ old('basket_size_aktual_tiktok', 0) }}" min="0" required>
                                @error('basket_size_aktual_tiktok')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Tambah</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    @push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Tampilkan kembali modal Edit jika ada error validasi
            @if ($errors->any())
                @foreach ($rekaps as $rekap)
                    @if ($errors->hasAny(['nama_periode', 'tahun', 'toko_id', 'total_pendapatan_shopee', 'total_pendapatan_tiktok', 'total_penghasilan_shopee', 'total_penghasilan_tiktok', 'total_hpp_shopee', 'total_hpp_tiktok', 'total_iklan_shopee', 'total_iklan_tiktok', 'operasional', 'rasio_admin_layanan_shopee', 'rasio_admin_layanan_tiktok', 'aov_aktual_shopee', 'aov_aktual_tiktok', 'basket_size_aktual_shopee', 'basket_size_aktual_tiktok']) && old('_method') === 'PUT' && request()->route('rekap') == $rekap->id)
                        const editModal = new bootstrap.Modal(document.getElementById('editRekapModal{{ $rekap->id }}'));
                        editModal.show();
                        break;
                    @endif
                @endforeach

                // Tampilkan kembali modal Create jika ada error validasi di Create
                @if ($errors->hasAny(['nama_periode', 'tahun', 'toko_id', 'total_pendapatan_shopee', 'total_pendapatan_tiktok', 'total_penghasilan_shopee', 'total_penghasilan_tiktok', 'total_hpp_shopee', 'total_hpp_tiktok', 'total_iklan_shopee', 'total_iklan_tiktok', 'operasional', 'rasio_admin_layanan_shopee', 'rasio_admin_layanan_tiktok', 'aov_aktual_shopee', 'aov_aktual_tiktok', 'basket_size_aktual_shopee', 'basket_size_aktual_tiktok']) && old('_method') !== 'PUT')
                    const createModal = new bootstrap.Modal(document.getElementById('createRekapModal'));
                    createModal.show();
                @endif
            @endif
        });
    </script>
    @endpush
</x-app-layout>
