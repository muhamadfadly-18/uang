@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header bg-primary text-white rounded-top-4 d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Edit Target</h4>
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
            <form action="{{ route('target.update', $target->id) }}" method="POST">
            @else
            <form action="{{ route('target.user.update', $target->id) }}" method="POST">
            @endif
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama Target</label>
                        <input type="text" name="name" class="form-control rounded-3 shadow-sm" 
                               value="{{ $target->name }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Harga (Rp)</label>
                        <input type="number" name="harga" class="form-control rounded-3 shadow-sm" 
                               value="{{ $target->harga }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Persentasi (%)</label>
                        <input type="number" name="persentasi" class="form-control rounded-3 shadow-sm" 
                               min="0" max="100" value="{{ $target->persentasi }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select rounded-3 shadow-sm" required>
                            <option value="pending" {{ $target->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="proses" {{ $target->status == 'proses' ? 'selected' : '' }}>Proses</option>
                            <option value="success" {{ $target->status == 'success' ? 'selected' : '' }}>Success</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Link (Opsional)</label>
                        <input type="url" name="link" class="form-control rounded-3 shadow-sm" 
                               value="{{ $target->link }}" placeholder="https://contoh.com">
                        <small class="text-muted">Masukkan link ke toko / produk (misal Shopee atau Tokopedia).</small>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm">
                        <i class="bi bi-save me-1"></i> Update
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
@endsection
