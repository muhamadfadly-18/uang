<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;

class TransaksiController extends Controller
{
    public function dashboard()
    {
        $total_pemasukan = Transaksi::where('jenis', 'pemasukan')->sum('jumlah');
        $total_pengeluaran = Transaksi::where('jenis', 'pengeluaran')->sum('jumlah');
        $saldo = $total_pemasukan - $total_pengeluaran;

        return view('dashboard', compact('total_pemasukan', 'total_pengeluaran', 'saldo'));
    }

    public function index()
    {
        $data = Transaksi::latest()->get();
        return view('history', compact('data'));
    }

    public function pemasukan()
    {
        $data = Transaksi::where('jenis', 'pemasukan')->latest()->get();
        return view('pemasukan', compact('data'));
    }

    public function pengeluaran()
    {
        $data = Transaksi::where('jenis', 'pengeluaran')->latest()->get();
        return view('pengeluaran', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis' => 'required',
            'keterangan' => 'required',
            'jumlah' => 'required|numeric',
        ]);

        Transaksi::create($request->all());
        return back()->with('success', 'Data berhasil disimpan!');
    }

    public function destroy($id)
    {
        Transaksi::find($id)->delete();
        return back()->with('success', 'Data berhasil dihapus!');
    }
}
