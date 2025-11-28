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
                        <h5 class="mb-0"><i class="fas fa-chart-line"></i> Data Keuangan Bulanan</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('monthly-finances.export') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-download"></i> Export Excel
                            </a>
                            <a href="{{ route('monthly-finances.rekap') }}" class="btn btn-info btn-sm">
                                <i class="fas fa-chart-bar"></i> Rekap
                            </a>
                            <a href="{{ route('monthly-summaries.index') }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-database"></i> Manage Summary
                            </a>
                            <a href="{{ route('monthly-finances.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Tambah Data
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="res-config" class="display table table-striped table-hover dt-responsive nowrap"
                                style="width: 100%">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Periode</th>
                                        <th>Total Pendapatan</th>
                                        <th>Total Penghasilan</th>
                                        <th>HPP</th>
                                        <th>Operasional</th>
                                        <th>Iklan</th>
                                        <th>Laba/Rugi</th>
                                        {{-- <th>Status Data</th> --}}
                                        <th width="140">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($finances as $finance)
                                    @php
                                    $hasSummary = !is_null($finance->summary);
                                    $dataStatus = $hasSummary ? 'success' : 'warning';
                                    $dataText = $hasSummary ? 'Lengkap' : 'Perlu Sync';
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $finance->nama_periode }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ $finance->periode_awal->format('d/m/Y') }} - {{
                                                $finance->periode_akhir->format('d/m/Y') }}
                                            </small>
                                        </td>
                                        <td>Rp {{ number_format($finance->total_pendapatan, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($finance->total_penghasilan, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($finance->hpp, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($finance->operasional, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($finance->iklan, 0, ',', '.') }}</td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $finance->laba_rugi >= 0 ? 'success' : 'danger' }}">
                                                Rp {{ number_format($finance->laba_rugi, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        {{-- <td>
                                            <span class="badge bg-{{ $dataStatus }}">
                                                {{ $dataText }}
                                            </span>
                                        </td> --}}
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('monthly-finances.show', $finance->id) }}"
                                                    class="btn btn-info btn-sm" title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('monthly-finances.edit', $finance->id) }}"
                                                    class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if(!$hasSummary)
                                                <a href="{{ route('monthly-finances.sync', $finance->id) }}"
                                                    class="btn btn-success btn-sm" title="Sync Data"
                                                    onclick="return confirm('Generate summary data untuk {{ $finance->nama_periode }}?')">
                                                    <i class="fas fa-sync"></i>
                                                </a>
                                                @endif
                                                <form action="{{ route('monthly-finances.destroy', $finance->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Hapus data {{ $finance->nama_periode }}?')"
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

                        @if($finances->isEmpty())
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
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
