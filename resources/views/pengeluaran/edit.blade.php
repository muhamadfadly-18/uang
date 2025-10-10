@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center rounded-top-4">
            <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Edit Pengeluaran</h4>
            <a href="{{ route('pengeluaranday.index') }}" class="btn btn-light btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="card-body bg-light">
            @php
                use App\Models\Pemasukan;
                use App\Models\Pengeluaran;
                use Illuminate\Support\Facades\Auth;

                // Hitung saldo + total lama agar bisa edit tanpa error
                $totalPemasukan = Pemasukan::where('user_id', Auth::id())->sum('jumlah');
                $totalPengeluaran = Pengeluaran::where('user_id', Auth::id())->sum('jumlah');
                $saldo = $totalPemasukan - $totalPengeluaran + $data->total;
            @endphp

            {{-- Informasi saldo --}}
            <div class="alert alert-info shadow-sm rounded-3">
                <strong>Saldo Anda Saat Ini:</strong>
                <span class="fw-bold text-success">Rp {{ number_format($saldo, 0, ',', '.') }}</span>
            </div>

            <form action="{{ route('pengeluaranday.update', $data->id) }}" method="POST" id="formEditPengeluaran">
                @csrf
                @method('PUT')

                {{-- Keterangan --}}
                <div class="mb-3">
                    <label for="keterangan" class="form-label fw-semibold">Keterangan</label>
                    <input type="text" name="keterangan" id="keterangan" 
                           class="form-control shadow-sm @error('keterangan') is-invalid @enderror"
                           value="{{ old('keterangan', $data->keterangan) }}"
                           placeholder="Masukkan keterangan" required>
                    @error('keterangan')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                @if($saldo > 0)
                {{-- Jumlah --}}
                <div class="mb-3">
                    <label for="jumlah_display" class="form-label fw-semibold">Jumlah</label>
                    <div class="input-group">
                        <span class="input-group-text bg-secondary text-white fw-bold">Rp</span>
                        <input type="text" id="jumlah_display" class="form-control shadow-sm"
                               value="{{ number_format(old('total', $data->total), 0, ',', '.') }}" required>
                    </div>
                    <input type="hidden" name="total" id="jumlah_real" value="{{ old('total', $data->total) }}">
                    @error('total')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                    <small class="text-muted">Maksimal: Rp {{ number_format($saldo, 0, ',', '.') }}</small>
                </div>
                @else
                    <input type="hidden" name="total" value="{{ $data->total }}">
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i>
                        Saldo Anda saat ini <b>Rp 0</b>, tidak dapat mengubah pengeluaran.
                    </div>
                @endif

                {{-- Tombol --}}
                <div class="text-end">
                    <button type="submit" class="btn btn-success px-4 shadow-sm">
                        <i class="bi bi-save2 me-1"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Script format angka ribuan --}}
@if($saldo > 0)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const displayInput = document.getElementById('jumlah_display');
    const realInput = document.getElementById('jumlah_real');
    const maxSaldo = {{ $saldo }};

    function formatNumber(num) {
        return num.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    displayInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\./g, '').replace(/[^0-9]/g, '');
        if(value) {
            if(parseInt(value) > maxSaldo) value = maxSaldo.toString();
            e.target.value = formatNumber(value);
        } else {
            e.target.value = '';
        }
        realInput.value = value;
    });

    document.getElementById('formEditPengeluaran').addEventListener('submit', function() {
        realInput.value = displayInput.value.replace(/\./g, '');
    });
});
</script>
@endif

{{-- CSS tambahan --}}
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
