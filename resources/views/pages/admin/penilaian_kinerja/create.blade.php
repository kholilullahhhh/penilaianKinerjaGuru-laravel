@extends('layouts.app', ['title' => 'Tambah Data Penilaian Kinerja'])

@section('content')
    @push('styles')
        <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.css') }}">
        <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
    @endpush

    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Tambah Data Penilaian Kinerja</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-md-12 col-lg-8 offset-lg-2">
                        <form action="{{ route('penilaian_kinerja.store') }}" method="POST">
                            @csrf
                            <div class="card">
                                <div class="card-header">
                                    <h4>Form Tambah Penilaian Kinerja</h4>
                                </div>
                                <div class="card-body">
                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <div class="form-group">
                                        <label>Pegawai</label>
                                        <select name="user_id" class="form-control selectric" required>
                                            <option value="">-- Pilih Pegawai --</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Indikator Kinerja</label>
                                        <select name="indicator_id" class="form-control selectric" required>
                                            <option value="">-- Pilih Indikator --</option>
                                            @foreach($indicators as $indicator)
                                                <option value="{{ $indicator->id }}" {{ old('indicator_id') == $indicator->id ? 'selected' : '' }}>
                                                    {{ $indicator->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Skor Akhir</label>
                                        <input type="number" step="0.01" name="skor_akhir" class="form-control" required
                                            placeholder="Masukkan skor akhir (contoh: 85.50)" value="{{ old('skor_akhir') }}">
                                        <small class="text-muted">Gunakan titik (.) untuk desimal</small>
                                    </div>

                                    <div class="form-group">
                                        <label>Kategori</label>
                                        <select name="kategori" class="form-control selectric" required>
                                            <option value="">-- Pilih Kategori --</option>
                                            <option value="Sangat Baik" {{ old('kategori') == 'Sangat Baik' ? 'selected' : '' }}>Sangat Baik</option>
                                            <option value="Baik" {{ old('kategori') == 'Baik' ? 'selected' : '' }}>Baik</option>
                                            <option value="Cukup" {{ old('kategori') == 'Cukup' ? 'selected' : '' }}>Cukup</option>
                                            <option value="Kurang" {{ old('kategori') == 'Kurang' ? 'selected' : '' }}>Kurang</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="card-footer text-right">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                    <a href="{{ route('penilaian_kinerja.index') }}" class="btn btn-warning">Kembali</a>
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
        <script src="{{ asset('library/summernote/dist/summernote-bs4.js') }}"></script>
        <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>
        <script src="{{ asset('js/page/forms-advanced-forms.js') }}"></script>
    @endpush
@endsection