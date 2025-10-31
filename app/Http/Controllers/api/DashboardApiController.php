<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Carbon\Carbon;

class DashboardApiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $filterTanggal = $request->input('tanggal');
        $filterBulan = $request->input('bulan', date('m'));
        $filterTahun = $request->input('tahun', date('Y'));

        $pemasukanQuery = Pemasukan::query();
        $pengeluaranQuery = Pengeluaran::query();

        // 🔐 Filter berdasarkan user (kalau bukan admin)
        if ($user->role != 'admin') {
            $pemasukanQuery->where('user_id', $user->id);
            $pengeluaranQuery->where('user_id', $user->id);
        }

        // 🎯 Filter tanggal/bulan/tahun
        if ($filterTanggal) {
            $pemasukanQuery->whereDate('created_at', $filterTanggal);
            $pengeluaranQuery->whereDate('created_at', $filterTanggal);
        } else {
            $pemasukanQuery->whereMonth('created_at', $filterBulan)
                           ->whereYear('created_at', $filterTahun);
            $pengeluaranQuery->whereMonth('created_at', $filterBulan)
                             ->whereYear('created_at', $filterTahun);
        }

        // 💰 Hitung total
        $totalPemasukan = $pemasukanQuery->sum('jumlah');
        $totalPengeluaran = $pengeluaranQuery->sum('total');
        $saldo = $totalPemasukan - $totalPengeluaran;

        // 📅 Data harian untuk grafik
        $daysInMonth = Carbon::createFromDate($filterTahun, $filterBulan, 1)->daysInMonth;
        $days = collect(range(1, $daysInMonth))->map(function ($day) use ($filterBulan, $filterTahun) {
            return Carbon::createFromDate($filterTahun, $filterBulan, $day)->format('Y-m-d');
        });

        $pemasukanData = $days->map(fn($day) => (clone $pemasukanQuery)->whereDate('created_at', $day)->sum('jumlah'));
        $pengeluaranData = $days->map(fn($day) => (clone $pengeluaranQuery)->whereDate('created_at', $day)->sum('total'));

        // 📊 Data untuk chart pie
        $pieData = [
            'pemasukan' => $totalPemasukan,
            'pengeluaran' => $totalPengeluaran,
            'saldo' => max($saldo, 0)
        ];

        // 📦 Return JSON ke FE mobile
        return response()->json([
            'status' => 'success',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role,
            ],
            'filter' => [
                'tanggal' => $filterTanggal,
                'bulan' => $filterBulan,
                'tahun' => $filterTahun,
            ],
            'data' => [
                'total_pemasukan' => $totalPemasukan,
                'total_pengeluaran' => $totalPengeluaran,
                'saldo' => $saldo,
                'pemasukan_harian' => $pemasukanData,
                'pengeluaran_harian' => $pengeluaranData,
                'days' => $days,
                'pie' => $pieData,
            ]
        ]);
    }
}
