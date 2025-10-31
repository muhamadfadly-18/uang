<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pemasukan;
use Illuminate\Support\Facades\Auth;

class PemasukanApiController extends Controller
{
    /**
     * 🔹 ADMIN — Lihat semua data pemasukan
     */
    public function index()
    {
        $data = Pemasukan::with('user')->latest()->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Data pemasukan berhasil diambil.',
            'data' => $data
        ]);
    }

    /**
     * 🔹 ADMIN & USER — Simpan pemasukan baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'keterangan' => 'required|string|max:255',
            'jumlah'     => 'required|numeric|min:0',
        ]);

        $data = Pemasukan::create([
            'user_id'    => Auth::id(),
            'jenis'      => 'pemasukan',
            'keterangan' => $request->keterangan,
            'jumlah'     => $request->jumlah,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pemasukan berhasil ditambahkan!',
            'data' => $data
        ]);
    }

    /**
     * 🔹 ADMIN — Lihat detail pemasukan
     */
    public function show($id)
    {
        $data = Pemasukan::with('user')->find($id);

        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * 🔹 ADMIN — Update pemasukan
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'required|string|max:255',
            'jumlah'     => 'required|numeric|min:0',
        ]);

        $data = Pemasukan::find($id);
        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        $data->update([
            'user_id'    => Auth::id(),
            'keterangan' => $request->keterangan,
            'jumlah'     => $request->jumlah,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pemasukan berhasil diperbarui!',
            'data' => $data
        ]);
    }

    /**
     * 🔹 ADMIN — Hapus pemasukan
     */
    public function destroy($id)
    {
        $data = Pemasukan::find($id);

        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        $data->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Pemasukan berhasil dihapus.'
        ]);
    }

    /**
     * 🔹 USER — Lihat pemasukan miliknya sendiri
     */
    public function userIndex()
    {
        $data = Pemasukan::where('user_id', Auth::id())
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Data pemasukan pengguna berhasil diambil.',
            'data' => $data
        ]);
    }
}
