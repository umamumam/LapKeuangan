@if($periodesList->isEmpty())
<div class="text-center py-4">
    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
    <p class="text-muted">
        @if(isset($toko))
            Belum ada periode summary untuk toko <strong>{{ $toko->nama }}</strong>.
        @else
            Belum ada periode summary.
        @endif
    </p>
    <button type="button" class="btn btn-primary btn-sm" onclick="openAddPeriodeModal({{ isset($toko) ? $toko->id : 'null' }})">
        <i class="fas fa-plus me-1"></i> Tambah Periode
    </button>
</div>
@else
<div class="table-responsive">
    <table id="{{ $tableId }}" class="display table table-striped table-hover dt-responsive nowrap datatable-periode"
        style="width: 100%">
        <thead class="table-primary">
            <tr>
                <th>Periode</th>
                <th>Toko</th>
                <th>Marketplace</th>
                <th>Penghasilan</th>
                <th>HPP</th>
                <th>Laba Kotor</th>
                <th>Status</th>
                <th width="140">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($periodesList as $periode)
            <tr>
                <td>
                    <strong>{{ $periode->nama_periode }}</strong>
                    <br>
                    <small class="text-muted">
                        {{ \Carbon\Carbon::parse($periode->tanggal_mulai)->format('d/m/Y') }} -
                        {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->format('d/m/Y') }}
                    </small>
                </td>
                <td>{{ $periode->toko->nama ?? '-' }}</td>
                <td>
                    <span
                        class="badge bg-{{ $periode->marketplace == 'Shopee' ? 'warning' : 'info' }}">
                        {{ $periode->marketplace }}
                    </span>
                </td>
                <td class="fw-bold text-success">
                    Rp {{ number_format($periode->total_penghasilan, 0, ',', '.') }}
                </td>
                <td class="text-danger">
                    Rp {{ number_format($periode->total_hpp_produk, 0, ',', '.') }}
                </td>
                <td
                    class="fw-bold {{ ($periode->total_penghasilan - $periode->total_hpp_produk) >= 0 ? 'text-success' : 'text-danger' }}">
                    Rp {{ number_format($periode->total_penghasilan -
                    $periode->total_hpp_produk, 0, ',', '.') }}
                </td>
                <td>
                    @if($periode->is_generated)
                    <span class="badge bg-success">
                        <i class="fas fa-check"></i> Generated
                        <br>
                        <small>{{ \Carbon\Carbon::parse($periode->generated_at)->format('d/m/Y H:i') }}</small>
                    </span>
                    @else
                    <span class="badge bg-warning">
                        <i class="fas fa-clock"></i> Pending
                    </span>
                    @endif
                </td>
                <td>
                    <div class="d-flex gap-1">
                        @if(!$periode->is_generated)
                        <button type="button" class="btn btn-success btn-sm"
                            onclick="generatePeriode({{ $periode->id }})" title="Generate">
                            <i class="fas fa-play"></i>
                        </button>
                        @else
                        <!-- Tombol Regenerate -->
                        <button type="button" class="btn btn-secondary btn-sm"
                            onclick="regeneratePeriode({{ $periode->id }})" title="Update Data">
                            <i class="fas fa-redo"></i>
                        </button>
                        @endif
                        <button type="button" class="btn btn-warning btn-sm" onclick="editPeriode({{ $periode->id }})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-info btn-sm"
                            onclick="showDetail({{ $periode->id }})" title="Detail">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm"
                            onclick="deletePeriode({{ $periode->id }}, '{{ $periode->nama_periode }}')"
                            title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
