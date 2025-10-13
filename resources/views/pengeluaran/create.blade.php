@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center rounded-top-4">
            <h4 class="mb-0"><i class="bi bi-credit-card-2-back me-2"></i> Tambah Pengeluaran</h4>
              @if (Auth::user()->role == 'admin')
            <a href="{{ route('pengeluaranday.index') }}" class="btn btn-light btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            @else
            <a href="{{ route('pengeluaranday.user.index') }}" class="btn btn-light btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            @endif
        </div>

        @php
            use App\Models\Pemasukan;
            use App\Models\Pengeluaran;
            use Illuminate\Support\Facades\Auth;

            $totalPemasukan = Pemasukan::where('user_id', Auth::id())->sum('jumlah');
            $totalPengeluaran = Pengeluaran::where('user_id', Auth::id())->sum('jumlah');
            $saldo = $totalPemasukan - $totalPengeluaran;
        @endphp

        <div class="card-body bg-light">
            <div class="alert alert-info shadow-sm rounded-3">
                <strong><i class="bi bi-wallet2"></i> Saldo Anda Saat Ini:</strong>
                <span class="fw-bold text-success">Rp {{ number_format($saldo, 0, ',', '.') }}</span>
            </div>
  @if (Auth::user()->role == 'admin')
            <form action="{{ route('pengeluaranday.store') }}" method="POST" id="formPengeluaran">
            @else
            <form action="{{ route('pengeluaranday.user.store') }}" method="POST" id="formPengeluaran">
            @endif
                @csrf

                {{-- Keterangan --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Keterangan</label>
                    <input type="text" name="keterangan"
                           class="form-control shadow-sm @error('keterangan') is-invalid @enderror"
                           value="{{ old('keterangan') }}"
                           placeholder="Contoh: Beli bahan bakar" required>
                    @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @if ($saldo > 0)
                {{-- Harga per item --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Harga per item (Rp)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-secondary text-white fw-bold">Rp</span>
                        <input type="text" id="jumlah_display"
                               class="form-control shadow-sm @error('harga') is-invalid @enderror"
                               value="{{ old('harga') ? number_format(old('harga'), 0, ',', '.') : '' }}"
                               placeholder="Masukkan nominal (maks: {{ number_format($saldo, 0, ',', '.') }})"
                               required>
                    </div>
                    <input type="hidden" name="harga" id="jumlah_real" value="{{ old('harga') }}">
                    @error('harga')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Maksimal: Rp {{ number_format($saldo, 0, ',', '.') }}</small>
                </div>

                {{-- Jumlah Barang --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Jumlah Barang</label>
                    <input type="number" name="jumlah" id="jumlah_input"
                           class="form-control shadow-sm @error('jumlah') is-invalid @enderror"
                           value="{{ old('jumlah', 1) }}" min="1" required>
                    @error('jumlah')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Total otomatis --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Total (Rp)</label>
                    <input type="text" id="total_display" class="form-control" readonly>
                    <input type="hidden" name="total" id="total_real">
                </div>

                {{-- Tombol --}}
                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-success px-4 shadow-sm">
                        <i class="bi bi-check-circle me-1"></i> Simpan
                    </button>
                </div>

                @else
                <input type="hidden" name="harga" value="0">
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                    Saldo Anda saat ini <b>Rp 0</b>, tidak bisa menambah pengeluaran.
                </div>
                @endif
            </form>
        </div>
    </div>
</div>

@if ($saldo > 0)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const displayInput = document.getElementById('jumlah_display');
    const realInput = document.getElementById('jumlah_real');
    const jumlahInput = document.getElementById('jumlah_input');
    const totalDisplay = document.getElementById('total_display');
    const totalReal = document.getElementById('total_real');
    const maxSaldo = {{ $saldo }};

    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function hitungTotal() {
        let harga = parseInt(realInput.value) || 0;
        let jumlah = parseInt(jumlahInput.value) || 1;

        // Batasi harga × jumlah tidak boleh lebih dari saldo
        let total = harga * jumlah;
        if(total > maxSaldo) {
            jumlah = Math.floor(maxSaldo / harga) || 1; // sesuaikan jumlah
            total = harga * jumlah;
            jumlahInput.value = jumlah;
        }

        totalDisplay.value = formatNumber(total);
        totalReal.value = total;
    }

    displayInput.addEventListener('input', function (e) {
        let value = e.target.value.replace(/\./g, '').replace(/[^0-9]/g, '');
        if(value) {
            if(parseInt(value) > maxSaldo) value = maxSaldo.toString();
            e.target.value = formatNumber(value);
        } else {
            e.target.value = '';
        }
        realInput.value = value;
        hitungTotal();
    });

    jumlahInput.addEventListener('input', hitungTotal);

    document.getElementById('formPengeluaran').addEventListener('submit', function () {
        realInput.value = displayInput.value.replace(/\./g, '');
    });

    hitungTotal();
});
</script>
@endif

<style>
.card { transition: all 0.3s ease; }
.card:hover { transform: translateY(-3px); }
.input-group-text { border: none; }
</style>
@endsection
