@extends('layouts.app')

@section('content')
    <div class="container mt-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold"><i class="bi bi-people"></i> Daftar User</h4>
            <a href="{{ route('users.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-circle"></i> Tambah User
            </a>
        </div>

        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body bg-white">
                @if (session('success'))
                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: '{{ session('success') }}',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        });
                    </script>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-center">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th width="150">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $key => $user)
                                <tr class="text-center">
                                    <td>{{ $key + 1 }}</td>
                                    <td class="text-start">{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if ($user->role == 'admin')
                                            <span class="badge bg-primary px-3 py-2">Admin</span>
                                        @elseif($user->role == 'superadmin')
                                            <span class="badge bg-success px-3 py-2">Super Admin</span>
                                        @else
                                            <span class="badge bg-secondary px-3 py-2">{{ ucfirst($user->role) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            {{-- Tombol Edit --}}
                                            <a href="{{ route('users.edit', $user->id) }}"
                                                class="btn btn-sm btn-warning shadow-sm">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>

                                            {{-- Tombol Delete --}}
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                                class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger shadow-sm btn-delete">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">
                                        <i class="bi bi-info-circle"></i> Belum ada user
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- SweetAlert2 Delete Confirmation --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.btn-delete').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    Swal.fire({
                        title: 'Yakin ingin menghapus?',
                        text: "Data user yang dihapus tidak bisa dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>

    <style>
        body {
            background-color: #f8f9fa;
        }

        .card {
            border-radius: 15px;
        }

        .table {
            border-radius: 10px;
            overflow: hidden;
        }

        .table-hover tbody tr:hover {
            background-color: #f3f6f9;
        }

        .badge {
            font-size: 0.85rem;
        }

        .btn {
            transition: all 0.2s ease;
        }

        .btn:hover {
            transform: scale(1.05);
        }
    </style>
@endsection
