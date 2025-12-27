<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-plus"></i> Tambah Data Banding</h5>
                        <a href="{{ route('bandings.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form action="{{ route('bandings.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tanggal" class="form-label">Tanggal *</label>
                                        <input type="datetime-local"
                                            class="form-control @error('tanggal') is-invalid @enderror" id="tanggal"
                                            name="tanggal" value="{{ old('tanggal', now()->format('Y-m-d\TH:i')) }}"
                                            required>
                                        @error('tanggal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="marketplace" class="form-label">Marketplace *</label>
                                        <select class="form-select @error('marketplace') is-invalid @enderror"
                                            id="marketplace" name="marketplace" required>
                                            <option value="">Pilih Marketplace</option>
                                            @foreach($marketplaceOptions as $value => $label)
                                            <option value="{{ $value }}" {{ old('marketplace')==$value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('marketplace')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="no_pesanan" class="form-label">No. Pesanan</label>
                                        <input type="text"
                                            class="form-control @error('no_pesanan') is-invalid @enderror"
                                            id="no_pesanan" name="no_pesanan" value="{{ old('no_pesanan') }}">
                                        @error('no_pesanan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="no_pengajuan" class="form-label">No. Pengajuan</label>
                                        <input type="text"
                                            class="form-control @error('no_pengajuan') is-invalid @enderror"
                                            id="no_pengajuan" name="no_pengajuan" value="{{ old('no_pengajuan') }}">
                                        @error('no_pengajuan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="status_banding" class="form-label">Status Banding *</label>
                                        <select class="form-select @error('status_banding') is-invalid @enderror"
                                            id="status_banding" name="status_banding">
                                            <option value="">Pilih Status</option>
                                            @foreach($statusBandingOptions as $value => $label)
                                            <option value="{{ $value }}" {{ old('status_banding')==$value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('status_banding')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="alasan" class="form-label">Alasan *</label>
                                        <select class="form-select @error('alasan') is-invalid @enderror" id="alasan"
                                            name="alasan">
                                            <option value="">Pilih Alasan</option>
                                            @foreach($alasanOptions as $value => $label)
                                            <option value="{{ $value }}" {{ old('alasan')==$value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('alasan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="ongkir" class="form-label">Ongkir *</label>
                                        <select class="form-select @error('ongkir') is-invalid @enderror" id="ongkir"
                                            name="ongkir" required>
                                            <option value="">Pilih Status Ongkir</option>
                                            @foreach($ongkirOptions as $value => $label)
                                            <option value="{{ $value }}" {{ old('ongkir')==$value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('ongkir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- TAMBAH FIELD STATUS PENERIMAAN -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status_penerimaan" class="form-label">Status Penerimaan *</label>
                                        <select class="form-select @error('status_penerimaan') is-invalid @enderror"
                                            id="status_penerimaan" name="status_penerimaan" required>
                                            <option value="">Pilih Status Penerimaan</option>
                                            @foreach($statusPenerimaanOptions as $value => $label)
                                            <option value="{{ $value }}" {{ old('status_penerimaan')==$value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('status_penerimaan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="no_resi" class="form-label">No. Resi</label>
                                        <input type="text" class="form-control @error('no_resi') is-invalid @enderror"
                                            id="no_resi" name="no_resi" value="{{ old('no_resi') }}">
                                        @error('no_resi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control @error('username') is-invalid @enderror"
                                            id="username" name="username" value="{{ old('username') }}">
                                        @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="nama_pengirim" class="form-label">Nama Pengirim</label>
                                        <input type="text"
                                            class="form-control @error('nama_pengirim') is-invalid @enderror"
                                            id="nama_pengirim" name="nama_pengirim" value="{{ old('nama_pengirim') }}">
                                        @error('nama_pengirim')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="no_hp" class="form-label">No. HP</label>
                                        <input type="text" class="form-control @error('no_hp') is-invalid @enderror"
                                            id="no_hp" name="no_hp" value="{{ old('no_hp') }}"
                                            placeholder="Contoh: +6281234567890">
                                        @error('no_hp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat *</label>
                                <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat"
                                    name="alamat" rows="3" required>{{ old('alamat') }}</textarea>
                                @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Data
                                </button>
                                <a href="{{ route('bandings.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
