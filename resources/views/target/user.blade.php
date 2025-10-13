@extends('layouts.app')

@section('content')
<style>
    /* ===== Styling Kartu Utama ===== */
    .card-target {
        background-color: #20B2AA;
        border: none;
        border-radius: 18px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
          border-bottom: 8px solid black;
        color: #1b1b1b;
        padding: 25px;
        transition: all 0.3s ease;
    }

    .card-target:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 28px rgba(0,0,0,0.25);
    }

    /* ===== Styling Card Aksi ===== */
    .card-action {
        background: #fffdf8;
        border-radius: 18px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        border-top: 4px solid black;
        padding: 18px;
        transition: all 0.3s ease;
    }

    .card-action:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 22px rgba(0,0,0,0.2);
    }

    .progress {
        height: 18px;
        border-radius: 12px;
        background-color: rgba(255,255,255,0.4);
        overflow: hidden;
    }

    .progress-bar {
        transition: width 1.5s ease;
        background-color: white;
    }

    .target-title {
        font-family: "Poppins", sans-serif;
        font-weight: 600;
        color: #222;
    }

    .target-amount {
        font-size: 1.05rem;
        font-weight: 500;
        color: #222;
    }

    @keyframes fadeInUp {
        0% { opacity: 0; transform: translateY(20px); }
        100% { opacity: 1; transform: translateY(0); }
    }

    .fade-in {
        animation: fadeInUp 0.6s ease forwards;
    }
</style>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark">🎯 Daftar Target</h3>
        <a href="{{ route('target.user.create') }}" class="btn btn-primary shadow-sm px-3">+ Tambah Target</a>
    </div>

    {{-- SweetAlert success --}}
    @if (session('success'))
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 2000
                });
            });
        </script>
    @endif

    <div class="row">
        @forelse($targets as $index => $t)
            @php
                $persentasi = $t->harga > 0 ? round(($t->tercapai / $t->harga) * 100) : 0;
                $persentasi = min($persentasi, 100);
                $sisa = max($t->harga - $t->tercapai, 0);
            @endphp

            <div class="col-12 col-md-6 mb-4 fade-in" style="animation-delay: {{ $index * 0.1 }}s;">
                {{-- Card Target Utama --}}
                <div class="card card-target text-center mb-3">
                    <h5 class="target-title mb-3">
                        {{ $t->name }}
                        <span class="badge 
                            @if ($t->status == 'pending') bg-warning text-dark
                            @elseif($t->status == 'proses') bg-info
                            @else bg-success @endif
                            mb-2 px-2 py-2" style="font-size: 15px">
                            {{ ucfirst($t->status) }}
                        </span>
                    </h5>

                    <div class="progress mb-3">
                        <div class="progress-bar" role="progressbar" style="width: {{ $persentasi }}%; color: black;"
                            aria-valuenow="{{ $persentasi }}" aria-valuemin="0" aria-valuemax="100">
                            {{ $persentasi }}%
                        </div>
                    </div>

                    <p class="target-amount mb-2">
                        Rp{{ number_format($t->tercapai ?? 0, 0, ',', '.') }}
                        / Rp{{ number_format($t->harga, 0, ',', '.') }}
                    </p>

                    @if ($t->link)
                        <div class="mt-2">
                            <a href="{{ $t->link }}" target="_blank" class="btn btn-outline-dark btn-sm px-3">
                                🔗 Kunjungi
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Card Aksi (terpisah) --}}
                <div class="card-action text-center">
                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                        <a href="{{ route('target.user.edit', $t->id) }}" class="btn btn-warning btn-sm px-3">
                            ✏️ Edit
                        </a>

                        <form action="{{ route('target.user.destroy', $t->id) }}" method="POST" class="formHapus d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger btn-sm px-3 btnHapus">🗑️ Hapus</button>
                        </form>

                        <button class="btn btn-success btn-sm px-3" data-bs-toggle="modal"
                            data-bs-target="#addTercapaiModal{{ $t->id }}">
                            ➕ Tambah Pencapaian
                        </button>

                        <button class="btn btn-secondary btn-sm px-3" data-bs-toggle="modal"
                            data-bs-target="#historyModal{{ $t->id }}">
                            📜 Lihat History
                        </button>
                    </div>
                </div>
            </div>

            {{-- Modal Tambah Pencapaian --}}
            <div class="modal fade" id="addTercapaiModal{{ $t->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <form action="{{ route('target.user.tercapai', $t->id) }}" method="POST" class="modal-content formTercapai">
                        @csrf
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">Tambah Pencapaian untuk {{ $t->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted mb-2">Target: <b>Rp{{ number_format($t->harga, 0, ',', '.') }}</b></p>
                            <p class="text-muted mb-3">Sudah tercapai:
                                <b>Rp{{ number_format($t->tercapai, 0, ',', '.') }}</b></p>

                            <div class="mb-3">
                                <label class="form-label">Nilai Pencapaian (Rp)</label>
                                <input type="text" class="form-control format-rupiah"
                                    placeholder="Maksimal Rp{{ number_format($sisa, 0, ',', '.') }}"
                                    data-max="{{ $sisa }}" required>
                                <input type="hidden" name="nilai_tercapai" class="nilai-asli">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Keterangan (opsional)</label>
                                <input type="text" name="keterangan" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success btnSimpanTercapai">💾 Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Modal History --}}
            <div class="modal fade" id="historyModal{{ $t->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-secondary text-white">
                            <h5 class="modal-title">History Pencapaian - {{ $t->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            @if ($t->histories->count())
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Nilai Pencapaian</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($t->histories as $i => $h)
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td>{{ $h->created_at->timezone('Asia/Jakarta')->format('d M Y H:i') }}</td>
                                                <td>Rp{{ number_format($h->nilai_tercapai, 0, ',', '.') }}</td>
                                                <td>{{ $h->keterangan ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted text-center">Belum ada history pencapaian.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center mt-5">
                <h5 class="text-muted">Belum ada target yang ditambahkan.</h5>
            </div>
        @endforelse
    </div>
</div>

{{-- SweetAlert & Logika --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Progress animasi
    document.querySelectorAll('.progress-bar').forEach(bar => {
        const val = bar.getAttribute('aria-valuenow');
        bar.style.width = '0%';
        setTimeout(() => bar.style.width = val + '%', 200);
    });

    // Format rupiah + validasi max
    document.querySelectorAll('.format-rupiah').forEach(function(input) {
        input.addEventListener('input', function() {
            let value = input.value.replace(/\./g, '');
            if (isNaN(value)) value = '0';
            const max = parseFloat(input.dataset.max);
            const num = parseFloat(value);
            if (num > max) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Nilai Terlalu Besar!',
                    text: `Tidak boleh lebih dari Rp${new Intl.NumberFormat('id-ID').format(max)}.`,
                    confirmButtonColor: '#4ED7F1'
                });
                value = max.toString();
            }
            input.closest('form').querySelector('.nilai-asli').value = value;
            input.value = new Intl.NumberFormat('id-ID').format(value);
        });
    });

    // Konfirmasi hapus
    document.querySelectorAll('.btnHapus').forEach(button => {
        button.addEventListener('click', function() {
            const form = button.closest('form');
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data ini akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        });
    });

    // Konfirmasi simpan pencapaian
    document.querySelectorAll('.formTercapai').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Simpan pencapaian?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#2e7d32'
            }).then(result => {
                if (result.isConfirmed) form.submit();
            });
        });
    });
});
</script>
@endsection
