<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Exception;

class ProfileApiController extends Controller
{
    // 🔹 GET /api/profile
    public function show()
    {
        $user = Auth::user();

        // Hitung saldo user
        $totalPemasukan = Pemasukan::where('user_id', $user->id)->sum('jumlah');
        $totalPengeluaran = Pengeluaran::where('user_id', $user->id)->sum('jumlah');
        $saldo = $totalPemasukan - $totalPengeluaran;

        return response()->json([
            'status' => 'success',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'photo' => $user->photo,
            ],
            'saldo' => $saldo,
        ]);
    }

    // 🔹 PUT /api/profile
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
            // Upload foto ke base64
            if ($request->hasFile('photo')) {
                $image = $request->file('photo');
                if (!$image->isValid()) {
                    throw new Exception('File foto tidak valid.');
                }

                $imageData = base64_encode(file_get_contents($image->getRealPath()));
                $mimeType = $image->getMimeType();
                $base64Image = 'data:' . $mimeType . ';base64,' . $imageData;

                $user->photo = $base64Image;
            }

            $user->name = $request->name;
            $user->email = $request->email;

            if ($request->password) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Profil berhasil diperbarui!',
                'user' => $user,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
