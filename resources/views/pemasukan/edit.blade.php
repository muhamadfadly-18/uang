@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center rounded-top-4">
            <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Edit Pemasukan</h4>
            <a href="{{ route('pemasukan.index') }}" class="btn btn-light btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="card-body bg-light">
            <form action="{{ route('pemasukan.update', $data->id) }}" method="POST" id="formEditPemasukan">
                @csrf
                @method('PUT')

                {{-- Input Keterangan --}}
                <div class="mb-3">
                    <label for="keterangan" class="form-label fw-semibold">Keterangan</label>
                    <input type="text" name="keterangan" id="keterangan" class="form-control shadow-sm"
                           value="{{ old('keterangan', $data->keterangan) }}" placeholder="Masukkan keterangan" required>
                    @error('keterangan')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Input Jumlah --}}
                <div class="mb-3">
                    <label for="jumlah_display" class="form-label fw-semibold">Jumlah</label>
                    <div class="input-group">
                        <span class="input-group-text bg-secondary text-white fw-bold">Rp</span>
                        <input type="text" id="jumlah_display" class="form-control shadow-sm"
                               value="{{ number_format(old('jumlah', $data->jumlah), 0, ',', '.') }}" required>
                    </div>

                    {{-- Input hidden untuk dikirim ke server --}}
                    <input type="hidden" name="jumlah" id="jumlah_real" value="{{ old('jumlah', $data->jumlah) }}">

                    @error('jumlah')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Tombol Aksi --}}
                <div class="text-end">
                    <button type="submit" class="btn btn-success px-4 shadow-sm">
                        <i class="bi bi-save2 me-1"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Script format angka --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const displayInput = document.getElementById('jumlah_display');
    const realInput = document.getElementById('jumlah_real');

    // Fungsi format angka ribuan
    function formatNumber(num) {
        return num.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Saat user mengetik
    displayInput.addEventListener('input', function (e) {
        let value = e.target.value.replace(/\./g, '').replace(/[^0-9]/g, '');
        if (value) {
            e.target.value = formatNumber(value);
        } else {
            e.target.value = '';
        }
        realInput.value = value;
    });

    // Sebelum form dikirim
    document.getElementById('formEditPemasukan').addEventListener('submit', function () {
        realInput.value = displayInput.value.replace(/\./g, '');
    });
});
</script>

{{-- Sedikit CSS tambahan --}}
<style>
.card {
    transition: all 0.3s ease;
}
.card:hover {
    transform: translateY(-3px);
}
.input-group-text {
    border: none;
}
</style>
@endsection
