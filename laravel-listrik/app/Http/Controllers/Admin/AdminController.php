<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Bank;
use App\Models\Diskon;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalPesanan = Pesanan::count();
        $menungguVerifikasi = Pesanan::where('status_pembayaran', 0)->count();
        $pesananSelesai = Pesanan::where('status_pemesanan', 2)->count();
        $totalPendapatan = Pesanan::where('status_pembayaran', 1)->sum('total_bayar');

        $latestOrders = Pesanan::with(['produk', 'user', 'bank'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return view('admin.dashboard', compact(
            'totalPesanan',
            'menungguVerifikasi',
            'pesananSelesai',
            'totalPendapatan',
            'latestOrders'
        ));
    }

    public function pesanan(Request $request)
    {
        $query = Pesanan::with(['produk', 'user', 'bank', 'alamat']);

        if ($request->filled('status_bayar')) {
            $query->where('status_pembayaran', $request->status_bayar);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.pesanan', compact('orders'));
    }

    public function detailPesanan($id)
    {
        $pesanan = Pesanan::with(['produk', 'user', 'bank', 'alamat'])->where('id_pesanan', $id)->firstOrFail();
        return view('admin.pesanan_detail', compact('pesanan'));
    }

    public function updatePesanan(Request $request, $id)
    {
        $request->validate([
            'status_pembayaran' => 'required|in:0,1,2',
            'jadwal_pemasangan' => 'nullable|string|max:100',
            'kurir' => 'nullable|string|max:100',
            'status_keberangkatan' => 'required|in:0,1,2,3',
            'status_pemesanan' => 'required|in:0,1,2,3',
        ]);

        $pesanan = Pesanan::where('id_pesanan', $id)->firstOrFail();
        $pesanan->update([
            'status_pembayaran' => $request->status_pembayaran,
            'jadwal_pemasangan' => $request->jadwal_pemasangan,
            'kurir' => $request->kurir ?? 'Teknisi PT. Eksavindo',
            'status_keberangkatan' => $request->status_keberangkatan,
            'status_pemesanan' => $request->status_pemesanan,
        ]);

        return redirect()->route('admin.pesanan.detail', $id)->with('success', 'Status pesanan & jadwal teknisi berhasil diperbarui!');
    }

    // CRUD Produk Layanan
    public function produk()
    {
        $products = Produk::with(['kategori', 'diskon'])->orderBy('id_produk', 'desc')->get();
        $categories = Kategori::all();
        return view('admin.produk', compact('products', 'categories'));
    }

    public function storeProduk(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:100',
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'deskripsi_produk' => 'required|string',
        ]);

        Produk::create($request->all());
        return back()->with('success', 'Layanan instalasi baru berhasil ditambahkan!');
    }

    public function updateProduk(Request $request, $id)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:100',
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'deskripsi_produk' => 'required|string',
        ]);

        $produk = Produk::findOrFail($id);
        $produk->update($request->all());
        return back()->with('success', 'Layanan instalasi berhasil diperbarui!');
    }

    public function destroyProduk($id)
    {
        Produk::findOrFail($id)->delete();
        return back()->with('info', 'Layanan instalasi berhasil dihapus.');
    }

    // CRUD Kategori
    public function kategori()
    {
        $categories = Kategori::withCount('produk')->get();
        return view('admin.kategori', compact('categories'));
    }

    public function storeKategori(Request $request)
    {
        $request->validate(['nama_kategori' => 'required|string|max:100']);
        Kategori::create($request->only('nama_kategori'));
        return back()->with('success', 'Kategori baru berhasil ditambahkan!');
    }

    public function updateKategori(Request $request, $id)
    {
        $request->validate(['nama_kategori' => 'required|string|max:100']);
        Kategori::findOrFail($id)->update($request->only('nama_kategori'));
        return back()->with('success', 'Kategori berhasil diubah!');
    }

    public function destroyKategori($id)
    {
        Kategori::findOrFail($id)->delete();
        return back()->with('info', 'Kategori berhasil dihapus.');
    }

    // CRUD Rekening Bank
    public function bank()
    {
        $banks = Bank::all();
        return view('admin.bank', compact('banks'));
    }

    public function storeBank(Request $request)
    {
        $request->validate([
            'nama_bank' => 'required|string|max:100',
            'norek' => 'required|string|max:150',
            'atas_nama' => 'required|string|max:100',
        ]);
        Bank::create($request->all());
        return back()->with('success', 'Rekening bank berhasil ditambahkan!');
    }

    public function updateBank(Request $request, $id)
    {
        $request->validate([
            'nama_bank' => 'required|string|max:100',
            'norek' => 'required|string|max:150',
            'atas_nama' => 'required|string|max:100',
        ]);
        Bank::findOrFail($id)->update($request->all());
        return back()->with('success', 'Rekening bank berhasil diperbarui!');
    }

    public function destroyBank($id)
    {
        Bank::findOrFail($id)->delete();
        return back()->with('info', 'Rekening bank berhasil dihapus.');
    }

    // CRUD Diskon Promo
    public function diskon()
    {
        $discounts = Diskon::with('produk')->orderBy('id_diskon', 'desc')->get();
        $products = Produk::all();
        return view('admin.diskon', compact('discounts', 'products'));
    }

    public function storeDiskon(Request $request)
    {
        $request->validate([
            'id_produk' => 'required|exists:produk,id_produk',
            'besar_diskon' => 'required|numeric|min:0',
            'is_active' => 'required|in:0,1',
        ]);
        Diskon::create($request->all());
        return back()->with('success', 'Promo diskon berhasil ditambahkan!');
    }

    public function updateDiskon(Request $request, $id)
    {
        $request->validate([
            'besar_diskon' => 'required|numeric|min:0',
            'is_active' => 'required|in:0,1',
        ]);
        Diskon::findOrFail($id)->update($request->all());
        return back()->with('success', 'Promo diskon berhasil diperbarui!');
    }

    public function destroyDiskon($id)
    {
        Diskon::findOrFail($id)->delete();
        return back()->with('info', 'Promo diskon berhasil dihapus.');
    }
}

