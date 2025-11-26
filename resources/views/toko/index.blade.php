<!-- resources/views/toko/index.blade.php -->
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
                        <h5 class="mb-0"><i class="fas fa-store"></i> Daftar Toko</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('toko.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Tambah Toko
                            </a>
                        </div>
                    </div>

                    <div class="card-body" style="overflow-x:auto;">
                        @if($tokos->count() > 0)
                        <table id="res-config" class="display table table-striped table-hover dt-responsive nowrap"
                            style="width: 100%">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>ID</th>
                                    <th>Nama Toko</th>
                                    <th>Dibuat</th>
                                    <th>Diupdate</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tokos as $toko)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $toko->id }}</td>
                                    <td>{{ $toko->nama }}</td>
                                    <td>{{ $toko->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $toko->updated_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('toko.show', $toko->id) }}"
                                                class="btn btn-info btn-sm" title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('toko.edit', $toko->id) }}"
                                                class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('toko.destroy', $toko->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus toko ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <div class="text-center py-4">
                            <i class="fas fa-store fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada data toko.</p>
                            <a href="{{ route('toko.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tambah Toko Pertama
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
