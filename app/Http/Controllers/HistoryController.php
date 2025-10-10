<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Carbon\Carbon;

class HistoryController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Ambil semua pemasukan hari ini
        $pemasukan = Pemasukan::whereDate('created_at', $today)
            ->selectRaw("'Pemasukan' as jenis, keterangan, jumlah, created_at");

        // Ambil semua pengeluaran hari ini
        $pengeluaran = Pengeluaran::whereDate('created_at', $today)
            ->selectRaw("'Pengeluaran' as jenis, keterangan, jumlah, created_at");

        // Gabungkan dan urutkan by tanggal terbaru
        $data = $pemasukan->unionAll($pengeluaran)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('history.index', compact('data'));
    }
}
