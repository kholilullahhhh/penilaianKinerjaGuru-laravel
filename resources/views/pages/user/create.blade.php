@extends('layouts.app', ['title' => 'Isi Absensi Rapat'])

@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Isi Absensi Rapat</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Form Absensi</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('user.absensi.store') }}" method="POST">
                                    @csrf
                                    
                                    <div class="form-group">
                                        <label for="agenda_id">Pilih Agenda Rapat</label>
                                        <select class="form-control @error('agenda_id') is-invalid @enderror" 
                                                id="agenda_id" name="agenda_id" required>
                                            <option value="">-- Pilih Agenda --</option>
                                            @foreach($agendas as $agenda)
                                                <option value="{{ $agenda->id }}" 
                                                    {{ old('agenda_id') == $agenda->id ? 'selected' : '' }}>
                                                    {{ $agenda->judul }} ({{ $agenda->tanggal->format('d M Y') }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('agenda_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Status Kehadiran</label>
                                        <div class="selectgroup w-100">
                                            <label class="selectgroup-item">
                                                <input type="radio" name="kehadiran" value="hadir" 
                                                       class="selectgroup-input" checked>
                                                <span class="selectgroup-button">Hadir</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="radio" name="kehadiran" value="tidak_hadir" 
                                                       class="selectgroup-input">
                                                <span class="selectgroup-button">Tidak Hadir</span>
                                            </label>
                                            <label class="selectgroup-item">
                                                <input type="radio" name="kehadiran" value="izin" 
                                                       class="selectgroup-input">
                                                <span class="selectgroup-button">Izin</span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="keterangan">Keterangan (Opsional)</label>
                                        <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                                  id="keterangan" name="keterangan" rows="3">{{ old('keterangan') }}</textarea>
                                        @error('keterangan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="form-group text-right">
                                        <button type="submit" class="btn btn-primary">Simpan Absensi</button>
                                        <a href="{{ route('user.absensi.index') }}" class="btn btn-secondary">Batal</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection