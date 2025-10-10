<!DOCTYPE html>
<html>
<head>
    <title>Catatan Keuangan</title>
    <style>
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        .btn { padding: 6px 12px; border: none; background: blue; color: white; border-radius: 4px; text-decoration: none; }
        .btn-danger { background: red; }
    </style>
</head>
<body>
    <h2>Riwayat Transaksi</h2>
    <a href="{{ route('transaksi.create') }}" class="btn">+ Tambah Transaksi</a>
    <br><br>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Keterangan</th>
                <th>Jumlah (Rp)</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksi as $i => $t)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $t->created_at->format('d M Y H:i') }}</td>
                    <td>{{ ucfirst($t->jenis) }}</td>
                    <td>{{ $t->keterangan }}</td>
                    <td align="right">Rp {{ number_format($t->jumlah, 2, ',', '.') }}</td>
                    <td>
                        <form action="{{ route('transaksi.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Hapus transaksi ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Total Pemasukan: Rp {{ number_format($total_pemasukan, 2, ',', '.') }}</h3>
    <h3>Total Pengeluaran: Rp {{ number_format($total_pengeluaran, 2, ',', '.') }}</h3>
    <h2>Saldo Akhir: Rp {{ number_format($saldo, 2, ',', '.') }}</h2>
</body>
</html>
