<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="row">
                <!-- Session Alerts -->
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
                                html: "{{ $errors->first() }}",
                                showConfirmButton: true
                            });
                        });
                </script>
                @endif

                <!-- Reseller Section -->
                <!-- Styling for cards -->
                <style>
                    .hover-card {
                        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
                        text-decoration: none;
                    }
                    .hover-card:hover {
                        transform: translateY(-8px) scale(1.02);
                        box-shadow: 0 15px 30px rgba(0,0,0,0.15) !important;
                    }
                    .action-btns {
                        opacity: 0;
                        transition: opacity 0.3s ease;
                    }
                    .hover-card:hover .action-btns {
                        opacity: 1;
                    }
                </style>

                <!-- Reseller Section -->
                <div class="d-flex justify-content-between align-items-center mt-2 mb-3 border-bottom pb-2">
                    <div>
                        <h4 class="mb-0 fw-bold text-dark"><i class="fas fa-users text-primary me-2"></i> Partner Reseller</h4>
                    </div>
                    <button type="button" class="btn btn-primary shadow-sm" style="border-radius: 8px;" data-bs-toggle="modal"
                        data-bs-target="#createResellerModal">
                        <i class="fas fa-plus me-1"></i> Tambah Reseller
                    </button>
                </div>

                <div class="row mb-5">
                    @forelse($resellers as $index => $reseller)
                    @php
                        $gradients = [
                            'linear-gradient(135deg, #4b3d8f 0%, #663dff 100%)', // Deep Purple
                            'linear-gradient(135deg, #1fa2ff 0%, #12d8fa 100%)', // Blue
                            'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)', // Green
                            'linear-gradient(135deg, #093028 0%, #237A57 100%)', // Elegant Green
                        ];
                        $bgGradient = $gradients[$index % count($gradients)];
                    @endphp
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card h-100 border-0 shadow hover-card" style="border-radius: 12px; background: {{ $bgGradient }}; position: relative; overflow: hidden; min-height: 140px;">
                            
                            <!-- Action Buttons Edit & Delete (Hover) -->
                            <div class="position-absolute top-0 end-0 p-2 z-3 d-flex gap-1 action-btns">
                                <button type="button" class="btn btn-sm btn-light border-0 shadow-sm" data-bs-toggle="modal"
                                    data-bs-target="#editResellerModal{{ $reseller->id }}" style="border-radius: 6px; width: 30px; height: 30px; padding: 0;">
                                    <i class="fas fa-edit text-warning"></i>
                                </button>
                                <form action="{{ route('resellers.destroy', $reseller->id) }}" method="POST" onsubmit="return confirm('Hapus reseller?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border-0 shadow-sm" style="border-radius: 6px; width: 30px; height: 30px; padding: 0;">
                                        <i class="fas fa-trash text-danger"></i>
                                    </button>
                                </form>
                            </div>

                            <!-- Decorative abstract circle -->
                            <div style="position: absolute; right: -30px; top: -30px; width: 140px; height: 140px; border-radius: 50%; background: rgba(255,255,255,0.08);"></div>
                            <div style="position: absolute; right: 50px; bottom: -50px; width: 100px; height: 100px; border-radius: 50%; background: rgba(255,255,255,0.05);"></div>
                            
                            <a href="{{ route('partners.reseller.show', $reseller->id) }}" class="text-decoration-none h-100">
                                <div class="card-body position-relative z-1 d-flex flex-column justify-content-between p-3 text-white h-100">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="bg-white bg-opacity-25 rounded px-2 py-1 d-flex align-items-center justify-content-center shadow-sm">
                                            <i class="fas fa-user-tie text-white" style="font-size: 1.1rem;"></i>
                                        </div>
                                    </div>

                                    <div class="mt-3 text-white">
                                        <h4 class="mb-1 fw-bolder text-truncate text-white" style="letter-spacing: -0.5px;" title="{{ $reseller->nama }}">
                                            {{ strtoupper($reseller->nama) }}
                                        </h4>
                                        <div class="border-top border-white border-opacity-25 pt-2 mt-2">
                                            @if($reseller->hutang_awal > 0)
                                            <div style="font-size: 0.75rem; color: rgba(255,255,255,0.9);" class="mb-1 fw-bold">
                                                <i class="fas fa-money-bill-wave me-1 text-white"></i> Hutang: Rp {{ number_format($reseller->hutang_awal, 0, ',', '.') }}
                                            </div>
                                            @endif
                                            <div style="font-size: 0.75rem; color: rgba(255,255,255,0.9);" class="mb-1 fw-medium">
                                                <i class="fas fa-boxes me-1 text-white text-opacity-75"></i> Produk ({{ $reseller->barangs_count }}):
                                            </div>
                                            <div class="d-flex flex-wrap gap-1 mt-1">
                                                @forelse($reseller->barangs->take(3) as $brg)
                                                <span class="badge bg-white text-dark bg-opacity-75 shadow-sm" style="font-size: 0.65rem; font-weight: 600;">
                                                    {{ Str::limit($brg->namabarang, 12) }}
                                                </span>
                                                @empty
                                                <span class="text-white text-opacity-75" style="font-size: 0.7rem; font-style: italic;">Belum ada Data</span>
                                                @endforelse
                                                
                                                @if($reseller->barangs->count() > 3)
                                                <span class="badge bg-dark bg-opacity-25 text-white border border-white border-opacity-25" style="font-size: 0.65rem;">
                                                    +{{ $reseller->barangs->count() - 3 }} lain
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Edit Reseller Modal -->
                    <div class="modal fade" id="editResellerModal{{ $reseller->id }}" tabindex="-1">
                        <div class="modal-dialog modal-md">
                            <form action="{{ route('resellers.update', $reseller->id) }}"
                                method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Reseller</h5>
                                        <button type="button" class="btn-close"
                                            data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Nama Reseller</label>
                                            <input type="text" name="nama" class="form-control"
                                                value="{{ $reseller->nama }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Hutang Awal</label>
                                            <input type="number" name="hutang_awal" class="form-control"
                                                value="{{ number_format($reseller->hutang_awal, 0, '', '') }}" placeholder="0">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <p class="text-muted fst-italic">Belum ada partner reseller.</p>
                    </div>
                    @endforelse
                </div>

                <!-- Supplier Section -->
                <div class="d-flex justify-content-between align-items-center mt-3 mb-3 border-bottom pb-2">
                    <div>
                        <h4 class="mb-0 fw-bold text-dark"><i class="fas fa-truck text-warning me-2"></i> Partner Supplier</h4>
                    </div>
                    <button type="button" class="btn btn-warning shadow-sm text-dark fw-bold" style="border-radius: 8px;" data-bs-toggle="modal"
                        data-bs-target="#createSupplierModal">
                        <i class="fas fa-plus me-1"></i> Tambah Supplier
                    </button>
                </div>

                <div class="row">
                    @forelse($suppliers as $index => $supplier)
                    @php
                        $suppGradients = [
                            'linear-gradient(135deg, #ff7e5f 0%, #feb47b 100%)', // Peach/Orange
                            'linear-gradient(135deg, #ee0979 0%, #ff6a00 100%)', // Deep Orange/Red
                            'linear-gradient(135deg, #F09819 0%, #EDDE5D 100%)', // Yellow/Orange
                            'linear-gradient(135deg, #f12711 0%, #f5af19 100%)', // Fire
                        ];
                        $bgSuppGradient = $suppGradients[$index % count($suppGradients)];
                    @endphp
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card h-100 border-0 shadow hover-card" style="border-radius: 12px; background: {{ $bgSuppGradient }}; position: relative; overflow: hidden; min-height: 140px;">
                            
                            <!-- Action Buttons Edit & Delete (Hover) -->
                            <div class="position-absolute top-0 end-0 p-2 z-3 d-flex gap-1 action-btns">
                                <button type="button" class="btn btn-sm btn-light border-0 shadow-sm" data-bs-toggle="modal"
                                    data-bs-target="#editSupplierModal{{ $supplier->id }}" style="border-radius: 6px; width: 30px; height: 30px; padding: 0;">
                                    <i class="fas fa-edit text-warning"></i>
                                </button>
                                <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" onsubmit="return confirm('Hapus supplier?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border-0 shadow-sm" style="border-radius: 6px; width: 30px; height: 30px; padding: 0;">
                                        <i class="fas fa-trash text-danger"></i>
                                    </button>
                                </form>
                            </div>

                            <!-- Decorative abstract circle -->
                            <div style="position: absolute; right: -30px; top: -30px; width: 140px; height: 140px; border-radius: 50%; background: rgba(255,255,255,0.08);"></div>
                            <div style="position: absolute; right: 50px; bottom: -50px; width: 100px; height: 100px; border-radius: 50%; background: rgba(255,255,255,0.05);"></div>
                            
                            <a href="{{ route('partners.supplier.show', $supplier->id) }}" class="text-decoration-none h-100">
                                <div class="card-body position-relative z-1 d-flex flex-column justify-content-between p-3 text-white h-100">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="bg-white bg-opacity-25 rounded px-2 py-1 d-flex align-items-center justify-content-center shadow-sm">
                                            <i class="fas fa-truck text-white" style="font-size: 1.1rem;"></i>
                                        </div>
                                    </div>

                                    <div class="mt-3 text-white">
                                        <h4 class="mb-1 fw-bolder text-truncate text-white" style="letter-spacing: -0.5px;" title="{{ $supplier->nama }}">
                                            {{ strtoupper($supplier->nama) }}
                                        </h4>
                                        <div class="border-top border-white border-opacity-25 pt-2 mt-2">
                                            @if($supplier->hutang_awal > 0)
                                            <div style="font-size: 0.75rem; color: rgba(255,255,255,0.9);" class="mb-1 fw-bold">
                                                <i class="fas fa-money-bill-wave me-1 text-white"></i> Hutang: Rp {{ number_format($supplier->hutang_awal, 0, ',', '.') }}
                                            </div>
                                            @endif
                                            <div style="font-size: 0.75rem; color: rgba(255,255,255,0.9);" class="mb-1 fw-medium">
                                                <i class="fas fa-boxes me-1 text-white text-opacity-75"></i> Produk ({{ $supplier->barangs_count }}):
                                            </div>
                                            <div class="d-flex flex-wrap gap-1 mt-1">
                                                @forelse($supplier->barangs->take(3) as $brg)
                                                <span class="badge bg-white text-dark bg-opacity-75 shadow-sm" style="font-size: 0.65rem; font-weight: 600;">
                                                    {{ Str::limit($brg->namabarang, 12) }}
                                                </span>
                                                @empty
                                                <span class="text-white text-opacity-75" style="font-size: 0.7rem; font-style: italic;">Belum ada Data</span>
                                                @endforelse
                                                
                                                @if($supplier->barangs->count() > 3)
                                                <span class="badge bg-dark bg-opacity-25 text-white border border-white border-opacity-25" style="font-size: 0.65rem;">
                                                    +{{ $supplier->barangs->count() - 3 }} lain
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Edit Supplier Modal -->
                    <div class="modal fade" id="editSupplierModal{{ $supplier->id }}" tabindex="-1">
                        <div class="modal-dialog modal-md">
                            <form action="{{ route('suppliers.update', $supplier->id) }}"
                                method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Supplier</h5>
                                        <button type="button" class="btn-close"
                                            data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Nama Supplier</label>
                                            <input type="text" name="nama" class="form-control"
                                                value="{{ $supplier->nama }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Hutang Awal</label>
                                            <input type="number" name="hutang_awal" class="form-control"
                                                value="{{ number_format($supplier->hutang_awal, 0, '', '') }}" placeholder="0">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <p class="text-muted fst-italic">Belum ada partner supplier.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Create Reseller Modal -->
    <div class="modal fade" id="createResellerModal" tabindex="-1">
        <div class="modal-dialog modal-md">
            <form action="{{ route('resellers.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Reseller</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Reseller</label>
                            <input type="text" name="nama" class="form-control" placeholder="Nama Reseller" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hutang Awal</label>
                            <input type="number" name="hutang_awal" class="form-control" placeholder="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm">Tambah</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Create Supplier Modal -->
    <div class="modal fade" id="createSupplierModal" tabindex="-1">
        <div class="modal-dialog modal-md">
            <form action="{{ route('suppliers.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Supplier</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Supplier</label>
                            <input type="text" name="nama" class="form-control" placeholder="Nama Supplier" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hutang Awal</label>
                            <input type="number" name="hutang_awal" class="form-control" placeholder="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm">Tambah</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
