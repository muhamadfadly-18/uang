<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    public function index()
    {
        // Ambil semua pemasukan (dengan relasi user)
        $pemasukan = \App\Models\Pemasukan::with('user')
            ->get(['user_id', 'keterangan', 'jumlah', 'created_at'])
            ->map(function ($item) {
                return [
                    'jenis' => 'Pemasukan',
                    'nama_user' => $item->user->name ?? '-',
                    'keterangan' => $item->keterangan,
                    'jumlah' => $item->jumlah,
                    'created_at' => $item->created_at,
                ];
            });

        // Ambil semua pengeluaran (dengan relasi user)
        $pengeluaran = \App\Models\Pengeluaran::with('user')
            ->get(['user_id', 'keterangan', 'jumlah', 'created_at'])
            ->map(function ($item) {
                return [
                    'jenis' => 'Pengeluaran',
                    'nama_user' => $item->user->name ?? '-',
                    'keterangan' => $item->keterangan,
                    'jumlah' => $item->jumlah,
                    'created_at' => $item->created_at,
                ];
            });

        // Gabungkan hasilnya dan urutkan dari terbaru
        $data = $pemasukan->merge($pengeluaran)->sortByDesc('created_at');

        return view('history.index', compact('data'));
    }


    public function userHistory()
    {
        $userId = Auth::id();

        // Ambil semua pemasukan user
        $pemasukan = Pemasukan::where('user_id', $userId)
            ->selectRaw("'Pemasukan' as jenis, keterangan, jumlah, created_at");

        // Ambil semua pengeluaran user
        $pengeluaran = Pengeluaran::where('user_id', $userId)
            ->selectRaw("'Pengeluaran' as jenis, keterangan, total, created_at");

        // Gabungkan dan urutkan berdasarkan tanggal terbaru
        $data = $pemasukan->unionAll($pengeluaran)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('history.user', compact('data'));
    }

}
