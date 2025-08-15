@extends('layouts.app', ['title' => 'Edit Data Penilaian Kinerja'])

@section('content')
    @push('styles')
        <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
    @endpush

    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Edit Data Penilaian Kinerja</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-md-12">
                        <form action="{{ route('penilaian_kinerja.update', $data->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="id" value="{{ $data->id }}" class="form-control">
                            <div class="card">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Pegawai</label>
                                        <select name="user_id" class="form-control selectric" required>
                                            <option value="">-- Pilih Pegawai --</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ old('user_id', $data->user_id) == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('user_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label>Indikator Kinerja</label>
                                        <select name="indicator_id" class="form-control selectric" required>
                                            <option value="">-- Pilih Indikator --</option>
                                            @foreach($indicators as $indicator)
                                                <option value="{{ $indicator->id }}" {{ old('indicator_id', $data->indicator_id) == $indicator->id ? 'selected' : '' }}>
                                                    {{ $indicator->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('indicator_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label>Skor Akhir</label>
                                        <input type="number" step="0.01" name="skor_akhir"
                                            class="form-control @error('skor_akhir') is-invalid @enderror" required
                                            placeholder="Masukkan skor akhir (contoh: 85.50)"
                                            value="{{ old('skor_akhir', $data->skor_akhir) }}">
                                        <small class="text-muted">Gunakan titik (.) untuk desimal</small>
                                        @error('skor_akhir')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label>Kategori</label>
                                        <select name="kategori"
                                            class="form-control selectric @error('kategori') is-invalid @enderror" required>
                                            <option value="">-- Pilih Kategori --</option>
                                            <option value="Sangat Baik" {{ old('kategori', $data->kategori) == 'Sangat Baik' ? 'selected' : '' }}>Sangat Baik</option>
                                            <option value="Baik" {{ old('kategori', $data->kategori) == 'Baik' ? 'selected' : '' }}>Baik</option>
                                            <option value="Cukup" {{ old('kategori', $data->kategori) == 'Cukup' ? 'selected' : '' }}>Cukup</option>
                                            <option value="Kurang" {{ old('kategori', $data->kategori) == 'Kurang' ? 'selected' : '' }}>Kurang</option>
                                        </select>
                                        @error('kategori')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="card-footer text-right">
                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                    <a href="{{ route('penilaian_kinerja.index') }}" class="btn btn-secondary">Kembali</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
        <script src="{{ asset('library/select2/dist/js/select2.full.min.js') }}"></script>
        <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>
        <script>
            $(document).ready(function () {
                $('.selectric').selectric();
            });
        </script>
    @endpush
@endsection