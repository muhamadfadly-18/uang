@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="fw-bold mb-0"><i class="bi bi-clock-history"></i> History Transaksi Hari Ini</h2>
        </div>

        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body bg-white">
                @if (count($data) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>No</th>
                                    <th>Jenis</th>
                                    <th>Nama User</th>
                                    <th>Keterangan</th>
                                    <th>Jumlah</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $saldo = 0; @endphp
                                @foreach ($data as $i => $d)
                                    <tr class="text-center">
                                        <td>{{ $i + 1 }}</td>
                                        <td>
                                            @if (strtolower($d['jenis']) == 'pemasukan')
                                                <span class="badge bg-success px-3 py-2">Pemasukan</span>
                                            @else
                                                <span class="badge bg-danger px-3 py-2">Pengeluaran</span>
                                            @endif
                                        </td>
                                        <td>{{ $d['nama_user'] }}</td>
                                        <td class="text-start">{{ $d['keterangan'] }}</td>
                                        <td>
                                            @if (strtolower($d['jenis']) == 'pemasukan')
                                                <span class="text-success fw-semibold">+ Rp
                                                    {{ number_format($d['jumlah'], 0, ',', '.') }}</span>
                                                @php $saldo += $d['jumlah']; @endphp
                                            @else
                                                <span class="text-danger fw-semibold">- Rp
                                                    {{ number_format($d['total'], 0, ',', '.') }}</span>
                                                @php $saldo -= $d['total']; @endphp
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($d['created_at'])->format('d M Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="4" class="text-end">Saldo Hari Ini</th>
                                    <th colspan="2" class="text-success fw-bold">
                                        Rp {{ number_format($saldo, 0, ',', '.') }}
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info d-flex align-items-center justify-content-center mb-0">
                        <i class="bi bi-info-circle me-2"></i> Belum ada transaksi hari ini
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 1500
                });
            @endif
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

        .table th,
        .table td {
            vertical-align: middle;
        }

        .table-hover tbody tr:hover {
            background-color: #f3f6f9;
        }

        .badge {
            font-size: 0.9rem;
        }
    </style>
@endsection
