@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            {{-- Notifikasi --}}
            @if (session('success'))
                <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
            @endif

            {{-- Card Profile --}}
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
                    <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>Profil Saya</h5>
                </div>

                <div class="card-body bg-light">
                    <div class="text-center mb-4">
                        @if ($user->photo)
                            <img src="{{ asset('img/profile/' . $user->photo) }}" class="rounded-circle shadow" width="120" height="120" style="object-fit: cover;">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=007bff&color=fff&size=120" class="rounded-circle shadow">
                        @endif
                    </div>

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Foto Profil</label>
                            <input type="file" name="photo" class="form-control">
                            <small class="text-muted">Format: JPG, JPEG, PNG (max 2MB)</small>
                        </div>

                        <hr>

                        <h6 class="fw-bold text-secondary mt-3">Ubah Password</h6>
                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru">
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
.card {
    border-radius: 15px;
}
.card-header {
    border-top-left-radius: 15px;
    border-top-right-radius: 15px;
}
.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}
</style>
@endsection
