<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Exception;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();

        // Hitung saldo user
        $totalPemasukan = Pemasukan::where('user_id', $user->id)->sum('jumlah');
        $totalPengeluaran = Pengeluaran::where('user_id', $user->id)->sum('jumlah');
        $saldo = $totalPemasukan - $totalPengeluaran;

        return view('profile.edit', compact('user', 'saldo'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'nullable|min:6|confirmed',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            // 🔹 Upload dan ubah foto ke Base64 (agar bisa jalan di Vercel)
            if ($request->hasFile('photo')) {
                $image = $request->file('photo');

                // Pastikan file valid
                if (!$image->isValid()) {
                    throw new Exception('File foto tidak valid.');
                }

                $imageData = base64_encode(file_get_contents($image->getRealPath()));
                $mimeType = $image->getMimeType();
                $base64Image = 'data:' . $mimeType . ';base64,' . $imageData;

                $user->photo = $base64Image;
            }

            // 🔹 Update data user
            $user->name = $request->name;
            $user->email = $request->email;

            if ($request->password) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            return back()->with('success', 'Profil berhasil diperbarui!');
        } catch (Exception $e) {
            // 🔸 Tangani error upload atau database
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
