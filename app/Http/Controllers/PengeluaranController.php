<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengeluaran;
use App\Models\Pemasukan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Encoders\PngEncoder;
use thiagoalessio\TesseractOCR\TesseractOCR;

class PengeluaranController extends Controller
{
    public function index()
    {
        $data = Pengeluaran::latest()->get();
        return view('pengeluaran.index', compact('data'));
    }

    public function create()
    {
        return view('pengeluaran.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'keterangan' => 'required',
            'jumlah' => 'required|numeric|min:1',
            'harga' => 'required|numeric|min:1',
        ]);

        $totalPemasukan = Pemasukan::where('user_id', Auth::id())->sum('jumlah');
        $totalPengeluaran = Pengeluaran::where('user_id', Auth::id())->sum('total');
        $saldo = $totalPemasukan - $totalPengeluaran;

        $total = $request->harga * $request->jumlah;

        if ($total > $saldo) {
            return back()->withErrors([
                'jumlah' => 'Saldo tidak mencukupi! Saldo Anda saat ini: Rp ' . number_format($saldo, 0, ',', '.')
            ])->withInput();
        }

        Pengeluaran::create([
            'user_id' => Auth::id(),
            'jenis' => 'pengeluaran',
            'keterangan' => $request->keterangan,
            'harga' => $request->harga,
            'jumlah' => $request->jumlah,
            'total' => $total,
        ]);

        return redirect()->route('pengeluaranday.index')->with('success', 'Pengeluaran berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $data = Pengeluaran::findOrFail($id);
        return view('pengeluaran.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'required',
            'jumlah' => 'required|numeric|min:1',
            'harga' => 'required|numeric|min:1',
        ]);

        $totalPemasukan = Pemasukan::where('user_id', Auth::id())->sum('jumlah');
        $totalPengeluaran = Pengeluaran::where('user_id', Auth::id())
            ->where('id', '!=', $id)
            ->sum('total');
        $saldo = $totalPemasukan - $totalPengeluaran;

        $total = $request->harga * $request->jumlah;

        if ($total > $saldo) {
            return back()->withErrors([
                'jumlah' => 'Saldo tidak mencukupi untuk update ini! Saldo Anda: Rp ' . number_format($saldo, 0, ',', '.')
            ])->withInput();
        }

        $transaksi = Pengeluaran::findOrFail($id);
        $transaksi->update([
            'user_id' => Auth::id(),
            'keterangan' => $request->keterangan,
            'harga' => $request->harga,
            'jumlah' => $request->jumlah,
            'total' => $total,
        ]);

        return redirect()->route('pengeluaranday.index')->with('success', 'Pengeluaran berhasil diupdate!');
    }

    public function destroy($id)
    {
        Pengeluaran::findOrFail($id)->delete();
        return redirect()->route('pengeluaranday.index')->with('success', 'Pengeluaran berhasil dihapus!');
    }

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

            // Jalankan OCR
            $ocr = new TesseractOCR($fullPath);
            $ocr->executable('C:\\Program Files\\Tesseract-OCR\\tesseract.exe');
            $text = $ocr->run();

            $lines = explode("\n", $text);
            $items = [];

            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;
                if (preg_match('/telp|total|qty/i', $line)) continue;

                // Misal format: NAMA_PRODUK QTY HARGA
                $parts = preg_split('/\s+/', $line);
                if (count($parts) < 3) continue;

                $harga_satuan_str = array_pop($parts);
                $harga_satuan = floatval(str_replace([',', '.'], '', $harga_satuan_str));

                $jumlah_str = array_pop($parts);
                $jumlah = intval(str_replace([',', '.'], '', $jumlah_str));

                $nama = implode(' ', $parts);

                $total = $harga_satuan * $jumlah;

                if (!empty($nama) && $harga_satuan > 0 && $jumlah > 0) {
                    $items[] = [
                        'nama' => $nama,
                        'harga' => $harga_satuan,
                        'jumlah' => $jumlah,
                        'total' => $total,
                    ];
                }
            }

            if (count($items) == 0) {
                return redirect()->back()->with('error', 'Tidak ada produk valid ditemukan.');
            }

            foreach ($items as $item) {
                Pengeluaran::create([
                    'user_id' => Auth::id(),
                    'keterangan' => $item['nama'],
                    'harga' => $item['harga'],
                    'jumlah' => $item['jumlah'],
                    'total' => $item['total'],
                ]);
            }

            return redirect()->back()->with('success', count($items) . ' produk berhasil disimpan.');

        } catch (\Throwable $e) {
            \Log::error('Scan Struk Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
