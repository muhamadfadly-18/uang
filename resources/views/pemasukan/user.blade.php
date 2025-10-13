@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-2">
        <h2 class="fw-bold mb-0"><i class="bi bi-wallet2"></i> Data Pemasukan</h2>
        <a href="{{ route('pemasukan.user.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-circle"></i> Tambah Pemasukan
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body bg-white p-2 p-md-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>No</th>
                            <th class="text-start">Keterangan</th>
                            <th>Jumlah</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $i => $d)
                        <tr class="text-center">
                            <td>{{ $i + 1 }}</td>
                            <td class="text-start">{{ $d->keterangan }}</td>
                            <td class="text-success fw-semibold">Rp {{ number_format($d->jumlah, 0, ',', '.') }}</td>
                            <td>{{ $d->created_at->format('d M Y') }}</td>
                            <td class="text-nowrap">
                                <a href="{{ route('pemasukan.user.edit', $d->id) }}" class="btn btn-warning btn-sm me-1 mb-1 mb-md-0">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>

                                <form action="{{ route('pemasukan.user.destroy', $d->id) }}" method="POST" class="d-inline formHapus">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-sm btnHapus">
                                        <i class="bi bi-trash3"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                Belum ada data pemasukan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Notifikasi sukses
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 1500
        });
    @endif

    // Konfirmasi hapus
    document.querySelectorAll('.btnHapus').forEach(btn => {
        btn.addEventListener('click', function() {
            const form = this.closest('.formHapus');
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
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
/* Styling tambahan & responsif */
body {
    background-color: #f8f9fa;
}

.card {
    border-radius: 15px;
}

.table {
    border-radius: 10px;
    overflow: hidden;
    min-width: 500px; /* agar table bisa scroll di mobile */
}

.table th {
    font-weight: 600;
}

.table td {
    vertical-align: middle;
}

.btn {
    border-radius: 10px;
}

@media (max-width: 768px) {
    .table-responsive {
        overflow-x: auto;
    }

    .text-nowrap {
        white-space: nowrap;
    }
}
</style>
@endsection
