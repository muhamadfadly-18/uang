<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Transaksi;

class TransaksiApiController extends Controller
{
    public function dashboard()
    {
        $total_pemasukan = Transaksi::where('jenis', 'pemasukan')->sum('jumlah');
        $total_pengeluaran = Transaksi::where('jenis', 'pengeluaran')->sum('jumlah');
        $saldo = $total_pemasukan - $total_pengeluaran;

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_pemasukan' => $total_pemasukan,
                'total_pengeluaran' => $total_pengeluaran,
                'saldo' => $saldo,
            ]
        ], 200);
    }

    public function index()
    {
        $data = Transaksi::latest()->get();
        return response()->json([
            'status' => 'success',
            'data' => $data
        ], 200);
    }

    public function pemasukan()
    {
        $data = Transaksi::where('jenis', 'pemasukan')->latest()->get();
        return response()->json([
            'status' => 'success',
            'data' => $data
        ], 200);
    }

    public function pengeluaran()
    {
        $data = Transaksi::where('jenis', 'pengeluaran')->latest()->get();
        return response()->json([
            'status' => 'success',
            'data' => $data
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis' => 'required|in:pemasukan,pengeluaran',
            'keterangan' => 'required|string',
            'jumlah' => 'required|numeric',
        ]);

        $transaksi = Transaksi::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil disimpan!',
            'data' => $transaksi
        ], 201);
    }

    public function destroy($id)
    {
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan!'
            ], 404);
        }

        $transaksi->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil dihapus!'
        ], 200);
    }
}
