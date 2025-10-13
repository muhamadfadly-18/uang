@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header bg-primary text-white rounded-top-4 d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="bi bi-plus-circle me-2"></i> Tambah Target Baru</h4>
              @if (Auth::user()->role == 'admin')
            <a href="{{ route('target.index') }}" class="btn btn-light btn-sm rounded-pill">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            @else
            <a href="{{ route('target.user.index') }}" class="btn btn-light btn-sm rounded-pill">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            @endif
        </div>

        <div class="card-body p-4">
            @if ($errors->any())
                <div class="alert alert-danger rounded-3">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (Auth::user()->role == 'admin')
            <form action="{{ route('target.store') }}" method="POST" id="formTarget">
            @else
            <form action="{{ route('target.user.store') }}" method="POST" id="formTarget">
            @endif
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama Target</label>
                        <input type="text" name="name" class="form-control rounded-3 shadow-sm" placeholder="Masukkan nama target" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Harga (Rp)</label>
                        <input type="text" id="hargaFormat" class="form-control rounded-3 shadow-sm" placeholder="Masukkan harga target" required>
                        <input type="hidden" name="harga" id="hargaAsli">
                    </div>

                    {{-- Persentasi dihapus dari inputan, karena akan dihitung otomatis di backend --}}
                

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Link (Opsional)</label>
                        <input type="url" name="link" class="form-control rounded-3 shadow-sm" placeholder="https://contoh.com">
                        <small class="text-muted">Masukkan link ke produk, toko, atau referensi lain (misal Shopee).</small>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm">
                        <i class="bi bi-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .card {
        transition: all 0.3s ease-in-out;
    }
    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 0.75rem 1.5rem rgba(0,0,0,0.1);
    }
    label {
        color: #0d6efd;
    }
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const hargaFormat = document.getElementById('hargaFormat');
    const hargaAsli = document.getElementById('hargaAsli');

    hargaFormat.addEventListener('input', function (e) {
        let value = this.value.replace(/\D/g, ''); // Hapus semua selain angka
        if (value) {
            // Format angka jadi format ribuan
            this.value = new Intl.NumberFormat('id-ID').format(value);
        } else {
            this.value = '';
        }
        hargaAsli.value = value; // Simpan angka mentah ke input hidden
    });
});
</script>
@endsection
