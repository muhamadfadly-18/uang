<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Target;
use App\Models\TargetHistory;
use Illuminate\Support\Facades\Auth;

class TargetController extends Controller
{
    public function index()
    {
        $targets = Target::with(['histories', 'user'])->get();
        return view('target.index', compact('targets'));
    }


    public function userIndex()
    {
        $targets = Target::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('target.user', compact('targets'));
    }

    public function create()
    {
        return view('target.create');
    }

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

        Target::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'harga' => $harga,
            'tercapai' => $tercapai,
            'persentasi' => $persentasi,
            'status' => 'pending', // default status
            'link' => $request->link,
        ]);

        if (Auth::user()->role === 'admin') {
            return redirect()->route('target.index')->with('success', '🎯 Target berhasil ditambahkan.');
        } else {
            return redirect()->route('target.user.index')->with('success', 'Target berhasil ditambahkan.');
        }
    }

    public function edit($id)
    {
        $target = Target::findOrFail($id);
        return view('target.edit', compact('target'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'status' => 'required|in:pending,proses,success',
            'link' => 'nullable|url',
        ]);

        $target = Target::findOrFail($id);
        $harga = $request->harga;
        $persentasi = $harga > 0 ? round(($target->tercapai / $harga) * 100) : 0;

        // Tentukan status otomatis berdasarkan pencapaian
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

        if (Auth::user()->role === 'admin') {
            return redirect()->route('target.index')->with('success', '✅ Target berhasil diperbarui.');
        } else {
            return redirect()->route('target.user.index')->with('success', 'Target berhasil diperbarui.');
        }
    }

    public function destroy($id)
    {
        $target = Target::findOrFail($id);
        $target->delete();

        return redirect()->route('target.index')->with('success', '🗑️ Target berhasil dihapus.');
    }

    public function updateTercapai(Request $request, $id)
    {
        $request->validate([
            'nilai_tercapai' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $target = Target::findOrFail($id);

        // Tambahkan nilai pencapaian baru
        $target->tercapai += $request->nilai_tercapai;

        // Hitung ulang persentasi
        $persentasi = $target->harga > 0 ? round(($target->tercapai / $target->harga) * 100) : 0;
        $target->persentasi = min($persentasi, 100);

        // Tentukan status otomatis
        if ($target->tercapai >= $target->harga) {
            $target->status = 'success';
        } elseif ($target->tercapai > 0) {
            $target->status = 'proses';
        } else {
            $target->status = 'pending';
        }

        $target->save();

        // Simpan history pencapaian
        TargetHistory::create([
            'target_id' => $id,
            'nilai_tercapai' => $request->nilai_tercapai,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->back()->with('success', 'Pencapaian berhasil ditambahkan!');
    }
}
