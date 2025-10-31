<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Target;
use App\Models\TargetHistory;
use Illuminate\Support\Facades\Auth;

class TargetApiController extends Controller
{
    /**
     * 🔹 ADMIN — Ambil semua data target
     */
    public function index()
    {
        $targets = Target::with(['histories', 'user'])->get();

        return response()->json([
            'success' => true,
            'data' => $targets
        ], 200);
    }

    /**
     * 🔹 USER — Ambil target milik user login
     */
    public function userIndex()
    {
        $targets = Target::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $targets
        ], 200);
    }

    /**
     * 🔹 Tambah target baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'status' => 'nullable|in:pending,proses,success',
            'link' => 'nullable|url',
        ]);

        $harga = $request->harga;
        $tercapai = 0;
        $persentasi = $harga > 0 ? round(($tercapai / $harga) * 100) : 0;

        $target = Target::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'harga' => $harga,
            'tercapai' => $tercapai,
            'persentasi' => $persentasi,
            'status' => 'pending',
            'link' => $request->link,
        ]);

        return response()->json([
            'success' => true,
            'message' => '🎯 Target berhasil ditambahkan.',
            'data' => $target
        ], 201);
    }

    /**
     * 🔹 Detail target berdasarkan ID
     */
    public function show($id)
    {
        $target = Target::with(['histories', 'user'])->find($id);

        if (!$target) {
            return response()->json(['success' => false, 'message' => 'Target tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $target
        ], 200);
    }

    /**
     * 🔹 Update target
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'status' => 'required|in:pending,proses,success',
            'link' => 'nullable|url',
        ]);

        $target = Target::find($id);

        if (!$target) {
            return response()->json(['success' => false, 'message' => 'Target tidak ditemukan'], 404);
        }

        $harga = $request->harga;
        $persentasi = $harga > 0 ? round(($target->tercapai / $harga) * 100) : 0;

        if ($target->tercapai >= $harga) {
            $status = 'success';
        } elseif ($target->tercapai > 0) {
            $status = 'proses';
        } else {
            $status = 'pending';
        }

        $target->update([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'harga' => $harga,
            'persentasi' => $persentasi,
            'status' => $status,
            'link' => $request->link,
        ]);

        return response()->json([
            'success' => true,
            'message' => '✅ Target berhasil diperbarui.',
            'data' => $target
        ], 200);
    }

    /**
     * 🔹 Hapus target
     */
    public function destroy($id)
    {
        $target = Target::find($id);

        if (!$target) {
            return response()->json(['success' => false, 'message' => 'Target tidak ditemukan'], 404);
        }

        $target->delete();

        return response()->json([
            'success' => true,
            'message' => '🗑️ Target berhasil dihapus.'
        ], 200);
    }

    /**
     * 🔹 Tambah pencapaian target (updateTercapai)
     */
    public function updateTercapai(Request $request, $id)
    {
        $request->validate([
            'nilai_tercapai' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $target = Target::find($id);

        if (!$target) {
            return response()->json(['success' => false, 'message' => 'Target tidak ditemukan'], 404);
        }

        $target->tercapai += $request->nilai_tercapai;

        $persentasi = $target->harga > 0 ? round(($target->tercapai / $target->harga) * 100) : 0;
        $target->persentasi = min($persentasi, 100);

        if ($target->tercapai >= $target->harga) {
            $target->status = 'success';
        } elseif ($target->tercapai > 0) {
            $target->status = 'proses';
        } else {
            $target->status = 'pending';
        }

        $target->save();

        $history = TargetHistory::create([
            'target_id' => $id,
            'nilai_tercapai' => $request->nilai_tercapai,
            'keterangan' => $request->keterangan,
        ]);

        return response()->json([
            'success' => true,
            'message' => '📈 Pencapaian berhasil ditambahkan!',
            'data' => [
                'target' => $target,
                'history' => $history
            ]
        ], 200);
    }
}
