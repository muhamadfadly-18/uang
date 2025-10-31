<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Pengeluaran;
use App\Models\Pemasukan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use thiagoalessio\TesseractOCR\TesseractOCR;

class PengeluaranApiController extends Controller
{
    /**
     * 🔹 ADMIN — Lihat semua data pengeluaran
     */
    public function index()
    {
        $data = Pengeluaran::with('user')->latest()->paginate(10);

        return response()->json([
            'status' => 'success',
            'message' => 'Data pengeluaran berhasil diambil.',
            'data' => $data
        ]);
    }

    /**
     * 🔹 USER — Lihat hanya data miliknya sendiri
     */
    public function userIndex()
    {
        $data = Pengeluaran::where('user_id', Auth::id())
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Data pengeluaran pengguna berhasil diambil.',
            'data' => $data
        ]);
    }

    /**
     * 🔹 Simpan pengeluaran baru (admin & user)
     */
    public function store(Request $request)
    {
        $request->validate([
            'keterangan' => 'required|string|max:255',
            'jumlah'     => 'required|numeric|min:1',
            'harga'      => 'required|numeric|min:1',
        ]);

        $userId = Auth::id();
        $totalPemasukan   = Pemasukan::where('user_id', $userId)->sum('jumlah');
        $totalPengeluaran = Pengeluaran::where('user_id', $userId)->sum('total');
        $saldo = $totalPemasukan - $totalPengeluaran;

        $total = $request->harga * $request->jumlah;

        if ($total > $saldo) {
            return response()->json([
                'status' => 'error',
                'message' => 'Saldo tidak mencukupi! Saldo saat ini: Rp ' . number_format($saldo, 0, ',', '.')
            ], 400);
        }

        $data = Pengeluaran::create([
            'user_id'    => $userId,
            'jenis'      => 'pengeluaran',
            'keterangan' => $request->keterangan,
            'harga'      => $request->harga,
            'jumlah'     => $request->jumlah,
            'total'      => $total,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pengeluaran berhasil ditambahkan!',
            'data' => $data
        ]);
    }

    /**
     * 🔹 Tampilkan detail pengeluaran
     */
    public function show($id)
    {
        $data = Pengeluaran::with('user')->find($id);

        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * 🔹 Update pengeluaran
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'required|string|max:255',
            'jumlah'     => 'required|numeric|min:1',
            'harga'      => 'required|numeric|min:1',
        ]);

        $transaksi = Pengeluaran::find($id);
        if (!$transaksi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        if ($transaksi->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses ditolak.'
            ], 403);
        }

        $userId = Auth::id();
        $totalPemasukan   = Pemasukan::where('user_id', $userId)->sum('jumlah');
        $totalPengeluaran = Pengeluaran::where('user_id', $userId)
            ->where('id', '!=', $id)
            ->sum('total');
        $saldo = $totalPemasukan - $totalPengeluaran;

        $total = $request->harga * $request->jumlah;

        if ($total > $saldo) {
            return response()->json([
                'status' => 'error',
                'message' => 'Saldo tidak mencukupi untuk update ini! Saldo Anda: Rp ' . number_format($saldo, 0, ',', '.')
            ], 400);
        }

        $transaksi->update([
            'keterangan' => $request->keterangan,
            'harga'      => $request->harga,
            'jumlah'     => $request->jumlah,
            'total'      => $total,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pengeluaran berhasil diperbarui!',
            'data' => $transaksi
        ]);
    }

    /**
     * 🔹 Hapus pengeluaran
     */
    public function destroy($id)
    {
        $data = Pengeluaran::find($id);

        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        if ($data->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses ditolak.'
            ], 403);
        }

        $data->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Pengeluaran berhasil dihapus.'
        ]);
    }

    /**
     * 🔹 Scan struk (OCR → auto simpan ke database)
     */
    public function scan(Request $request)
    {
        try {
            $request->validate([
                'struk' => 'required|image|mimes:jpeg,png,jpg|max:4096',
            ]);

            $file = $request->file('struk');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();

            $destination = public_path('struk');
            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }

            $file->move($destination, $filename);
            $fullPath = $destination . '/' . $filename;

            $ocr = new TesseractOCR($fullPath);
            $ocr->executable('C:\\Program Files\\Tesseract-OCR\\tesseract.exe');
            $text = $ocr->run();

            $lines = explode("\n", $text);
            $items = [];

            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || preg_match('/telp|total|qty/i', $line)) continue;

                $parts = preg_split('/\s+/', $line);
                if (count($parts) < 3) continue;

                $harga_str = array_pop($parts);
                $harga = floatval(str_replace([',', '.'], '', $harga_str));

                $jumlah_str = array_pop($parts);
                $jumlah = intval(str_replace([',', '.'], '', $jumlah_str));

                $nama = implode(' ', $parts);
                $total = $harga * $jumlah;

                if (!empty($nama) && $harga > 0 && $jumlah > 0) {
                    $items[] = [
                        'nama'   => $nama,
                        'harga'  => $harga,
                        'jumlah' => $jumlah,
                        'total'  => $total,
                    ];
                }
            }

            if (empty($items)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada produk valid ditemukan.'
                ]);
            }

            foreach ($items as $item) {
                Pengeluaran::create([
                    'user_id'    => Auth::id(),
                    'keterangan' => $item['nama'],
                    'harga'      => $item['harga'],
                    'jumlah'     => $item['jumlah'],
                    'total'      => $item['total'],
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => count($items) . ' produk berhasil disimpan.',
                'data' => $items
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
