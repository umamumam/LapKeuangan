<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="col-sm-12">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                    <div>
                        <h4 class="mb-0 fw-bold text-dark"><i class="fas fa-users-cog text-primary me-2"></i> Transaksi Reseller ({{ strtoupper($type) }})</h4>
                    </div>
                </div>

                <!-- Reseller Cards -->
                <div class="row">
                    @foreach($resellers as $index => $reseller)
                    @php
                    $gradients = [
                        'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                        'linear-gradient(135deg, #2193b0 0%, #6dd5ed 100%)',
                        'linear-gradient(135deg, #00b09b 0%, #96c93d 100%)',
                        'linear-gradient(135deg, #e96443 0%, #904e95 100%)',
                    ];
                    $bgGradient = $gradients[$index % count($gradients)];
                    @endphp
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <a href="{{ route('reseller_transactions.matrix', ['reseller_id' => $reseller->id, 'type' => $type]) }}" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow hover-card"
                                style="border-radius: 12px; background: {{ $bgGradient }}; position: relative; overflow: hidden; min-height: 140px;">
                                <div style="position: absolute; right: -30px; top: -30px; width: 140px; height: 140px; border-radius: 50%; background: rgba(255,255,255,0.08);"></div>
                                <div style="position: absolute; right: 50px; bottom: -50px; width: 100px; height: 100px; border-radius: 50%; background: rgba(255,255,255,0.05);"></div>
                                <div class="card-body position-relative z-1 d-flex flex-column justify-content-between p-3 text-white">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="bg-white bg-opacity-25 rounded px-2 py-1 d-flex align-items-center justify-content-center shadow-sm">
                                            <i class="fas fa-user-tie text-white" style="font-size: 1.1rem;"></i>
                                        </div>
                                        @if($reseller->sisa_nota > 0)
                                        <span class="badge bg-danger shadow-sm px-2 py-1" style="border-radius: 8px; font-size: 0.75rem;">
                                            <i class="fas fa-exclamation-triangle me-1"></i> Sisa Nota
                                        </span>
                                        @endif
                                    </div>
                                    <div class="mt-3">
                                        <h3 class="mb-1 text-white fw-bolder text-truncate" style="letter-spacing: -0.5px;" title="{{ $reseller->nama }}">
                                            {{ strtoupper($reseller->nama) }}
                                        </h3>
                                        <div class="d-flex flex-column text-white text-opacity-75 mb-1" style="font-size: 0.8rem;">
                                            @if($reseller->hutang_awal > 0)
                                            <span>Awal: Rp {{ number_format($reseller->hutang_awal, 0, ',', '.') }}</span>
                                            @endif
                                            <span>Tagihan: Rp {{ number_format($reseller->sisa_nota, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="border-top border-white border-opacity-25 pt-2 mt-2">
                                            <div style="font-size: 0.7rem; color: rgba(255,255,255,0.9);" class="mb-1 fw-medium">
                                                <i class="fas fa-shopping-bag me-1 text-white text-opacity-75"></i> Produk Terakhir
                                            </div>
                                            <div class="d-flex flex-wrap gap-1 mt-1">
                                                @forelse($reseller->barang_preview as $brg)
                                                <span class="badge bg-white text-dark bg-opacity-75 shadow-sm" style="font-size: 0.65rem; font-weight: 600;">
                                                    {{ \Illuminate\Support\Str::limit($brg, 12) }}
                                                </span>
                                                @empty
                                                <span class="text-white text-opacity-75" style="font-size: 0.7rem; font-style: italic;">Belum ada transaksi</span>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <style>
        .hover-card {
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }
        .hover-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15) !important;
        }
    </style>
</x-app-layout>