<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-eye"></i> Detail Pengiriman Sampel</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('pengiriman-sampels.edit', $pengirimanSampel->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('pengiriman-sampels.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="40%">Tanggal Pengiriman</th>
                                        <td>{{ $pengirimanSampel->tanggal->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>No. Resi</th>
                                        <td><strong>{{ $pengirimanSampel->no_resi }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th>Username</th>
                                        <td>{{ $pengirimanSampel->username }}</td>
                                    </tr>
                                    <tr>
                                        <th>Jumlah Sampel</th>
                                        <td>
                                            @php
                                                $totalJumlah = 0;
                                                for ($i = 1; $i <= 5; $i++) {
                                                    $totalJumlah += $pengirimanSampel->{"jumlah{$i}"} ?? 0;
                                                }
                                            @endphp
                                            {{ $totalJumlah }} item
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Toko</th>
                                        <td>
                                            <strong>{{ $pengirimanSampel->toko ? $pengirimanSampel->toko->nama : 'N/A' }}</strong>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="40%">Total HPP</th>
                                        <td>Rp {{ number_format($pengirimanSampel->totalhpp, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Ongkir</th>
                                        <td>Rp {{ number_format($pengirimanSampel->ongkir, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Total Biaya</th>
                                        <td class="fw-bold">Rp {{ number_format($pengirimanSampel->total_biaya, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Penerima</th>
                                        <td>{{ $pengirimanSampel->penerima }}</td>
                                    </tr>
                                    <tr>
                                        <th>Contact</th>
                                        <td>{{ $pengirimanSampel->contact }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Tampilkan detail sampel 1-5 -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="fas fa-box"></i> Detail Sampel</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Sampel</th>
                                                        <th>Ukuran</th>
                                                        <th>Harga</th>
                                                        <th>Jumlah</th>
                                                        <th>Subtotal</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $counter = 0;
                                                    @endphp
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @php
                                                            $sampel = $pengirimanSampel->{"sampel{$i}"};
                                                            $jumlah = $pengirimanSampel->{"jumlah{$i}"} ?? 0;
                                                        @endphp
                                                        @if ($sampel && $jumlah > 0)
                                                            @php $counter++; @endphp
                                                            <tr>
                                                                <td>{{ $counter }}</td>
                                                                <td>{{ $sampel->nama }}</td>
                                                                <td>{{ $sampel->ukuran }}</td>
                                                                <td>Rp {{ number_format($sampel->harga, 0, ',', '.') }}</td>
                                                                <td>{{ $jumlah }}</td>
                                                                <td>Rp {{ number_format($sampel->harga * $jumlah, 0, ',', '.') }}</td>
                                                            </tr>
                                                        @endif
                                                    @endfor
                                                    @if ($counter == 0)
                                                        <tr>
                                                            <td colspan="6" class="text-center">Tidak ada data sampel</td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="fas fa-map-marker-alt"></i> Alamat Pengiriman</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-0">{{ $pengirimanSampel->alamat }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="fas fa-info-circle"></i> Informasi Tambahan</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <small class="text-muted">Dibuat pada: {{ $pengirimanSampel->created_at->format('d/m/Y H:i') }}</small>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-muted">Diupdate pada: {{ $pengirimanSampel->updated_at->format('d/m/Y H:i') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
