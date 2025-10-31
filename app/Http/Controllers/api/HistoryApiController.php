<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Illuminate\Support\Facades\Auth;

class HistoryApiController extends Controller
{
    // =======================
    // TAMPILKAN SEMUA HISTORI
    // =======================
    public function index()
    {
        // Ambil semua data pemasukan beserta relasi user
        $pemasukan = Pemasukan::with('user')
            ->get(['user_id', 'keterangan', 'jumlah', 'created_at'])
            ->map(function ($item) {
                return [
                    'jenis'       => 'Pemasukan',
                    'nama_user'   => $item->user->name ?? '-',
                    'keterangan'  => $item->keterangan,
                    'jumlah'      => $item->jumlah,
                    'created_at'  => $item->created_at,
                ];
            });

        // Ambil semua data pengeluaran beserta relasi user
        $pengeluaran = Pengeluaran::with('user')
            ->get(['user_id', 'keterangan', 'jumlah', 'created_at'])
            ->map(function ($item) {
                return [
                    'jenis'       => 'Pengeluaran',
                    'nama_user'   => $item->user->name ?? '-',
                    'keterangan'  => $item->keterangan,
                    'jumlah'      => $item->jumlah,
                    'created_at'  => $item->created_at,
                ];
            });

        // Gabungkan & urutkan data dari yang terbaru
        $data = $pemasukan->merge($pengeluaran)->sortByDesc('created_at');

        return view('history.index', compact('data'));
    }

    // ===================================
    // TAMPILKAN HISTORI UNTUK USER LOGIN
    // ===================================
    public function userHistory()
    {
        $userId = Auth::id();

        // Ambil data pemasukan user
        $pemasukan = Pemasukan::where('user_id', $userId)
            ->selectRaw("'Pemasukan' as jenis, keterangan, jumlah, created_at");

        // Ambil data pengeluaran user
        $pengeluaran = Pengeluaran::where('user_id', $userId)
            ->selectRaw("'Pengeluaran' as jenis, keterangan, jumlah, created_at");

        // Gabungkan dan urutkan berdasarkan tanggal terbaru
        $data = $pemasukan
            ->unionAll($pengeluaran)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('history.user', compact('data'));
    }
}
