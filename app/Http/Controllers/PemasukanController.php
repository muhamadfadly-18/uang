<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemasukan;
use Illuminate\Support\Facades\Auth;

class PemasukanController extends Controller
{
    public function index()
    {
        $data = Pemasukan::latest()->get(); // ambil semua data pemasukan
        return view('pemasukan.index', compact('data'));
    }

    public function create()
    {
        return view('pemasukan.create');
    }

    public function store(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'keterangan' => 'required',
            'jumlah' => 'required|numeric',
        ]);

        // Simpan data ke database
        Pemasukan::create([
            'user_id' => Auth::id(), // ambil ID user yang sedang login
            'jenis' => 'pemasukan',
            'keterangan' => $request->keterangan,
            'jumlah' => $request->jumlah
        ]);

        return redirect()->route('pemasukan.index')->with('success', 'Pemasukan berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $data = Pemasukan::findOrFail($id);
        return view('pemasukan.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        // Validasi input dari form
        $request->validate([
            'keterangan' => 'required',
            'jumlah' => 'required|numeric',
        ]);

        // Update data
        $transaksi = Pemasukan::findOrFail($id);
        $transaksi->update([
            'user_id' => Auth::id(),
            'keterangan' => $request->keterangan,
            'jumlah' => $request->jumlah,
        ]);

        return redirect()->route('pemasukan.index')->with('success', 'Pemasukan berhasil diupdate!');
    }

    public function destroy($id)
    {
        Pemasukan::findOrFail($id)->delete();
        return redirect()->route('pemasukan.index')->with('success', 'Pemasukan berhasil dihapus!');
    }
}
