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
                        <h5 class="mb-0"><i class="fas fa-chart-line"></i> Data Summary & Output</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('monthly-finances.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Tambah Data
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="res-config" class="display table table-striped table-hover dt-responsive nowrap"
                                style="width: 100%">
                                <thead class="table-primary">
                                    <tr>
                                        <th>No</th>
                                        <th>Periode</th>
                                        <th>Toko</th>
                                        <th>Marketplace</th>
                                        <th>Total Pendapatan</th>
                                        <th>Total Penghasilan</th>
                                        <th>HPP</th>
                                        <th>Operasional</th>
                                        <th>Iklan</th>
                                        <th>Laba/Rugi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($monthlyFinances as $monthly)
                                    @php
                                        $periode = $monthly->periode;
                                        $toko = $periode ? $periode->toko : null;
                                        $totalBiaya = $monthly->operasional + $monthly->iklan;
                                        if($periode) {
                                            $labaBersih = $periode->total_penghasilan - $periode->total_hpp_produk - $totalBiaya;
                                        } else {
                                            $labaBersih = $monthly->total_pendapatan - $totalBiaya;
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            @if($periode)
                                            <strong>{{ $periode->nama_periode }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ $periode->tanggal_mulai->format('d/m/Y') }} -
                                                {{ $periode->tanggal_selesai->format('d/m/Y') }}
                                            </small>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($toko)
                                                {{ $toko->nama }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($periode)
                                            <span class="badge bg-{{ $periode->marketplace == 'Shopee' ? 'warning text-dark' : 'dark' }}">
                                                {{ $periode->marketplace }}
                                            </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <!-- KOREKSI DI SINI: Hapus tag <td> berlebih -->
                                        <td>Rp {{ number_format($monthly->total_pendapatan, 0, ',', '.') }}</td>
                                        <td>
                                            @if($periode)
                                                Rp {{ number_format($periode->total_penghasilan, 0, ',', '.') }}
                                                <br>
                                                <small class="text-muted">
                                                    <div class="text-primary">Income: {{ $periode->jumlah_income }}</div>
                                                </small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($periode)
                                                Rp {{ number_format($periode->total_hpp_produk, 0, ',', '.') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>Rp {{ number_format($monthly->operasional, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($monthly->iklan, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $labaBersih >= 0 ? 'success' : 'danger' }}">
                                                <strong>Rp {{ number_format($labaBersih, 0, ',', '.') }}</strong>
                                            </span>
                                            {{-- @if($periode)
                                            <br>
                                            <small class="text-muted">
                                                <div class="text-primary">
                                                    S: Rp {{ number_format($periode->total_penghasilan_shopee - $periode->total_hpp_shopee, 0, ',', '.') }}
                                                </div>
                                                <div class="text-danger">
                                                    T: Rp {{ number_format($periode->total_penghasilan_tiktok - $periode->total_hpp_tiktok, 0, ',', '.') }}
                                                </div>
                                            </small>
                                            @endif --}}
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('monthly-finances.show', $monthly->id) }}"
                                                    class="btn btn-info btn-sm" title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('monthly-finances.edit', $monthly->id) }}"
                                                    class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('monthly-finances.destroy', $monthly->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Hapus data keuangan bulanan ini?')"
                                                        title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($monthlyFinances->isEmpty())
                        <div class="text-center py-4">
                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada data keuangan bulanan.</p>
                            <a href="{{ route('monthly-finances.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tambah Data Pertama
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
