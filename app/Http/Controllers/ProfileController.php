<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;

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

        // Update photo
        if ($request->hasFile('photo')) {
            $filename = time() . '.' . $request->photo->extension();
            $request->photo->move(public_path('img/profile'), $filename);
            $user->photo = $filename;
        }

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}
