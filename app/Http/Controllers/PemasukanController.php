<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemasukan;
use Illuminate\Support\Facades\Auth;

class PemasukanController extends Controller
{
    /**
     * 🔹 ADMIN — Lihat semua data pemasukan
     */
    public function index()
    {
       $data = Pemasukan::with('user')->latest()->get(); // Ambil semua data
        return view('pemasukan.index', compact('data'));
    }

    /**
     * 🔹 ADMIN — Form tambah pemasukan
     */
    public function create()
    {
        return view('pemasukan.create');
    }

    /**
     * 🔹 ADMIN & USER — Simpan pemasukan baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'keterangan' => 'required',
            'jumlah' => 'required|numeric',
        ]);

        Pemasukan::create([
            'user_id' => Auth::id(),
            'jenis' => 'pemasukan',
            'keterangan' => $request->keterangan,
            'jumlah' => $request->jumlah,
        ]);

        // Jika user adalah admin, kembali ke halaman admin
        if (Auth::user()->role === 'admin') {
            return redirect()->route('pemasukan.index')->with('success', 'Pemasukan berhasil ditambahkan!');
        }

        // Jika user biasa, kembali ke halaman miliknya
        return redirect()->route('pemasukan.user.index')->with('success', 'Pemasukan berhasil ditambahkan!');
    }

    /**
     * 🔹 ADMIN — Edit data pemasukan
     */
    public function edit($id)
    {
        $data = Pemasukan::findOrFail($id);
        return view('pemasukan.edit', compact('data'));
    }

    /**
     * 🔹 ADMIN — Update pemasukan
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'required',
            'jumlah' => 'required|numeric',
        ]);

        $transaksi = Pemasukan::findOrFail($id);
        $transaksi->update([
            'user_id' => Auth::id(),
            'keterangan' => $request->keterangan,
            'jumlah' => $request->jumlah,
        ]);

        return redirect()->route('pemasukan.index')->with('success', 'Pemasukan berhasil diupdate!');
    }

    /**
     * 🔹 ADMIN — Hapus pemasukan
     */
    public function destroy($id)
    {
        Pemasukan::findOrFail($id)->delete();
        return redirect()->route('pemasukan.index')->with('success', 'Pemasukan berhasil dihapus!');
    }

    /**
     * 🔹 USER — Lihat pemasukan miliknya sendiri
     */
    public function userIndex()
    {
        // Ambil hanya data milik user yang login
        $data = Pemasukan::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pemasukan.user', compact('data'));
    }
}
