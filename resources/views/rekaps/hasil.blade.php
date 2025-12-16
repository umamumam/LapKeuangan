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
                                title: "Error!",
                                text: "{{ session('error') }}",
                                showConfirmButton: true
                            });
                        });
                    </script>
                    @endif

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-chart-line"></i> Rekap Hasil Tahunan</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('rekaps.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-chart-bar"></i> Tambah Data Rekap
                            </a>
                        </div>
                    </div>
                    <div class="card-body" style="overflow-x:auto;">
                        <!-- Filter Form -->
                        <form method="GET" action="{{ route('rekaps.hasil') }}" class="mb-4 p-3 border rounded">
                            <h6 class="mb-3"><i class="fas fa-filter"></i> Filter Data Rekap Hasil</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Tahun</label>
                                    <select name="tahun" class="form-select">
                                        @for ($y = date('Y') - 5; $y <= date('Y') + 1; $y++)
                                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Toko</label>
                                    <select name="toko_id" class="form-select" required>
                                        <option value="">Pilih Toko</option>
                                        @foreach($tokos as $toko)
                                        <option value="{{ $toko->id }}" {{ $toko_id == $toko->id ? 'selected' : '' }}>{{ $toko->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                </div>
                            </div>
                        </form>

                        @if($toko_id)
                        <table class="table table-striped table-hover dt-responsive nowrap" style="width: 100%">
                            <thead class="table-primary">
                                <tr>
                                    <th>Kategori</th>
                                    @foreach($bulanList as $bulan)
                                    <th>{{ $bulan }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Total Pendapatan</strong></td>
                                    @foreach($bulanList as $bulan)
                                    <td>Rp {{ number_format($hasil[$bulan]['total_pendapatan'], 0, ',', '.') }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td><strong>Total Penghasilan</strong></td>
                                    @foreach($bulanList as $bulan)
                                    <td>Rp {{ number_format($hasil[$bulan]['total_penghasilan'], 0, ',', '.') }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td><strong>Total HPP</strong></td>
                                    @foreach($bulanList as $bulan)
                                    <td>Rp {{ number_format($hasil[$bulan]['total_hpp'], 0, ',', '.') }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td><strong>Operasional</strong></td>
                                    @foreach($bulanList as $bulan)
                                    <td>Rp {{ number_format($hasil[$bulan]['operasional'], 0, ',', '.') }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td><strong>Iklan</strong></td>
                                    @foreach($bulanList as $bulan)
                                    <td>Rp {{ number_format($hasil[$bulan]['iklan'], 0, ',', '.') }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td><strong>Rasio Admin dan Layanan</strong></td>
                                    @foreach($bulanList as $bulan)
                                    <td>{{ number_format($hasil[$bulan]['rasio_admin_layanan'], 2, ',', '.') }}%</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td><strong>Rasio Operasional</strong></td>
                                    @foreach($bulanList as $bulan)
                                    <td>{{ number_format($hasil[$bulan]['rasio_operasional'], 2, ',', '.') }}%</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td><strong>AOV Aktual</strong></td>
                                    @foreach($bulanList as $bulan)
                                    <td>Rp {{ number_format($hasil[$bulan]['aov_aktual'], 0, ',', '.') }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td><strong>Basket Size Aktual</strong></td>
                                    @foreach($bulanList as $bulan)
                                    <td>{{ number_format($hasil[$bulan]['basket_size_aktual'], 2, ',', '.') }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td><strong>ROAS Aktual</strong></td>
                                    @foreach($bulanList as $bulan)
                                    <td>{{ number_format($hasil[$bulan]['roas_aktual'], 2, ',', '.') }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td><strong>ACOS Aktual</strong></td>
                                    @foreach($bulanList as $bulan)
                                    <td>{{ number_format($hasil[$bulan]['acos_aktual'], 2, ',', '.') }}%</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td><strong>Rasio Margin</strong></td>
                                    @foreach($bulanList as $bulan)
                                    <td>{{ number_format($hasil[$bulan]['rasio_margin'], 2, ',', '.') }}%</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td><strong>Rasio Laba</strong></td>
                                    @foreach($bulanList as $bulan)
                                    <td>{{ number_format($hasil[$bulan]['rasio_laba'], 2, ',', '.') }}%</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td><strong>Laba</strong></td>
                                    @foreach($bulanList as $bulan)
                                    <td class="{{ $hasil[$bulan]['laba'] >= 0 ? 'text-success' : 'text-danger' }}">Rp {{ number_format($hasil[$bulan]['laba'], 0, ',', '.') }}</td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                        @else
                        <div class="text-center py-4">
                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Pilih tahun dan toko untuk melihat rekap hasil.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
