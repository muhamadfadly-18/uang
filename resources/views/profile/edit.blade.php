@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                {{-- Card Profil --}}
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden">

                    {{-- Header --}}
                    <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
                        <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>Profil Saya</h5>
                    </div>

                    {{-- Body --}}
                    <div class="card-body p-0">
                        <div class="row g-0">
                            {{-- Kiri: Foto + Saldo --}}
                            <div
                                class="col-md-4 bg-light p-4 text-center d-flex flex-column align-items-center justify-content-center">
                                @php
                                    use Illuminate\Support\Str;
                                @endphp

                                @if ($user->photo)
                                    @php
                                        // Cek apakah foto base64, URL, atau nama file lokal
                                        $isBase64 = Str::startsWith($user->photo, 'data:image');
                                        $isUrl = Str::startsWith($user->photo, 'http');
                                    @endphp

                                    @if ($isBase64)
                                        {{-- 🔹 Base64 langsung --}}
                                        <img src="{{ $user->photo }}" class="rounded-circle shadow mb-3" width="120"
                                            height="120" style="object-fit: cover;">
                                    @elseif ($isUrl)
                                        {{-- 🔹 Foto dari URL (misal Cloudinary atau link luar) --}}
                                        <img src="{{ $user->photo }}" class="rounded-circle shadow mb-3" width="120"
                                            height="120" style="object-fit: cover;">
                                    @else
                                        {{-- 🔹 Foto dari folder lokal (fallback lama) --}}
                                        <img src="{{ asset('img/profile/' . $user->photo) }}"
                                            class="rounded-circle shadow mb-3" width="120" height="120"
                                            style="object-fit: cover;">
                                    @endif
                                @else
                                    {{-- 🔹 Kalau belum ada foto --}}
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=007bff&color=fff&size=120"
                                        class="rounded-circle shadow mb-3">
                                @endif


                                <h6 class="text-secondary mb-1">Saldo Saat Ini</h6>
                                <h4 class="text-success fw-bold">Rp {{ number_format($saldo, 0, ',', '.') }}</h4>
                            </div>

                            {{-- Kanan: Form --}}
                            <div class="col-md-8 p-4">
                                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Nama Lengkap</label>
                                        <input type="text" name="name" class="form-control"
                                            value="{{ $user->name }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Email</label>
                                        <input type="email" name="email" class="form-control"
                                            value="{{ $user->email }}" required>
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
                                        <input type="password" name="password" class="form-control"
                                            placeholder="Kosongkan jika tidak ingin mengubah">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Konfirmasi Password Baru</label>
                                        <input type="password" name="password_confirmation" class="form-control"
                                            placeholder="Ulangi password baru">
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

                </div> <!-- Card end -->

            </div>
        </div>
    </div>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2000
            });
        @endif

        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan!',
                html: `{!! implode('<br>', $errors->all()) !!}`,
                confirmButtonColor: '#d33',
            });
        @endif
    </script>

    <style>
        .card {
            border-radius: 15px;
            overflow: hidden;
        }

        .card-header {
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
        }

        .text-success {
            color: #28a745 !important;
        }

        @media (max-width: 768px) {
            .row.g-0 {
                flex-direction: column;
            }

            .col-md-4,
            .col-md-8 {
                max-width: 100%;
            }

            .col-md-4 {
                padding: 2rem 1rem;
            }
        }
    </style>
@endsection
