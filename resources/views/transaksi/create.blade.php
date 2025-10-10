<!DOCTYPE html>
<html>
<head>
    <title>Tambah Transaksi</title>
</head>
<body>
    <h2>Tambah Transaksi</h2>
    <form action="{{ route('transaksi.store') }}" method="POST">
        @csrf
        <label>Jenis Transaksi:</label><br>
        <select name="jenis" required>
            <option value="pemasukan">Pemasukan</option>
            <option value="pengeluaran">Pengeluaran</option>
        </select><br><br>

        <label>Keterangan:</label><br>
        <input type="text" name="keterangan" required><br><br>

        <label>Jumlah (Rp):</label><br>
        <input type="number" name="jumlah" step="0.01" required><br><br>

        <button type="submit">Simpan</button>
    </form>

    <p><a href="{{ route('transaksi.index') }}">← Kembali</a></p>
</body>
</html>
