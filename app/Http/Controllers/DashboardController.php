<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $filterTanggal = $request->input('tanggal');
        $filterBulan = $request->input('bulan', date('m'));
        $filterTahun = $request->input('tahun', date('Y'));

        $pemasukanQuery = Pemasukan::query();
        $pengeluaranQuery = Pengeluaran::query();

        if ($user->role != 'admin') {
            $pemasukanQuery->where('user_id', $user->id);
            $pengeluaranQuery->where('user_id', $user->id);
        }

        if ($filterTanggal) {
            $pemasukanQuery->whereDate('created_at', $filterTanggal);
            $pengeluaranQuery->whereDate('created_at', $filterTanggal);
        } else {
            $pemasukanQuery->whereMonth('created_at', $filterBulan)->whereYear('created_at', $filterTahun);
            $pengeluaranQuery->whereMonth('created_at', $filterBulan)->whereYear('created_at', $filterTahun);
        }

        $totalPemasukan = $pemasukanQuery->sum('jumlah');
        $totalPengeluaran = $pengeluaranQuery->sum('total');
        $saldo = $totalPemasukan - $totalPengeluaran;

        $days = collect(range(1, 31))->map(function($day) use ($filterBulan, $filterTahun) {
            return Carbon::createFromDate($filterTahun, $filterBulan, $day)->format('Y-m-d');
        });

        $pemasukanData = $days->map(fn($day) => (clone $pemasukanQuery)->whereDate('created_at', $day)->sum('jumlah'));
        $pengeluaranData = $days->map(fn($day) => (clone $pengeluaranQuery)->whereDate('created_at', $day)->sum('jumlah'));

        // Data untuk chart pie
        $pieData = [
            'pemasukan' => $totalPemasukan,
            'pengeluaran' => $totalPengeluaran,
            'saldo' => max($saldo, 0)
        ];

        return view('dashboard', compact(
            'totalPemasukan',
            'totalPengeluaran',
            'saldo',
            'pemasukanData',
            'pengeluaranData',
            'days',
            'filterTanggal',
            'filterBulan',
            'filterTahun',
            'pieData'
        ));
    }
}
