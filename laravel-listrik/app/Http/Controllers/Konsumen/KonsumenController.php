<?php

namespace App\Http\Controllers\Konsumen;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Keranjang;
use App\Models\Alamat;
use App\Models\Bank;
use App\Models\Pesanan;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KonsumenController extends Controller
{
    protected MidtransService $midtrans;

    public function __construct(MidtransService $midtrans)
    {
        $this->midtrans = $midtrans;
    }

    public function home()
    {
        $featured = Produk::with(['kategori', 'diskon'])->orderBy('terjual', 'desc')->take(6)->get();
        $categories = Kategori::all();
        return view('konsumen.home', compact('featured', 'categories'));
    }

    public function katalog(Request $request)
    {
        $query = Produk::with(['kategori', 'diskon']);

        if ($request->filled('kategori')) {
            $query->where('id_kategori', $request->kategori);
        }

        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_produk', 'like', "%{$keyword}%")
                  ->orWhere('deskripsi_produk', 'like', "%{$keyword}%");
            });
        }

        $products = $query->orderBy('id_produk', 'desc')->get();
        $categories = Kategori::all();
        return view('konsumen.katalog', compact('products', 'categories'));
    }

    public function detailProduk($id)
    {
        $produk = Produk::with(['kategori', 'diskon'])->findOrFail($id);
        return view('konsumen.detail_produk', compact('produk'));
    }

    public function keranjang()
    {
        $userId = Auth::user()->id_user;
        $items = Keranjang::with('produk.diskon')->where('id_user', $userId)->get();
        $grandTotal = $items->sum('subtotal');
        return view('konsumen.keranjang', compact('items', 'grandTotal'));
    }

    public function tambahKeranjang(Request $request)
    {
        $request->validate([
            'id_produk' => 'required|exists:produk,id_produk',
            'jml_beli' => 'required|integer|min:1',
        ]);

        $userId = Auth::user()->id_user;
        $cart = Keranjang::where('id_user', $userId)->where('id_produk', $request->id_produk)->first();

        if ($cart) {
            $cart->increment('jml_beli', $request->jml_beli);
        } else {
            Keranjang::create([
                'id_user' => $userId,
                'id_produk' => $request->id_produk,
                'jml_beli' => $request->jml_beli,
            ]);
        }

        return redirect()->route('keranjang')->with('success', 'Paket instalasi berhasil ditambahkan ke keranjang!');
    }

    public function updateKeranjang(Request $request)
    {
        $request->validate([
            'id_keranjang' => 'required|exists:keranjang,id_keranjang',
            'jml_beli' => 'required|integer|min:1',
        ]);

        $cart = Keranjang::where('id_user', Auth::user()->id_user)->findOrFail($request->id_keranjang);
        $cart->update(['jml_beli' => $request->jml_beli]);

        return back()->with('success', 'Jumlah paket berhasil diupdate!');
    }

    public function hapusKeranjang($id)
    {
        Keranjang::where('id_user', Auth::user()->id_user)->where('id_keranjang', $id)->delete();
        return back()->with('info', 'Paket berhasil dihapus dari keranjang.');
    }

    public function checkout()
    {
        $userId = Auth::user()->id_user;
        $items = Keranjang::with('produk.diskon')->where('id_user', $userId)->get();

        if ($items->isEmpty()) {
            return redirect()->route('keranjang')->with('warning', 'Keranjang Anda kosong.');
        }

        $grandTotal = $items->sum('subtotal');
        $daftarAlamat = Alamat::where('id_user', $userId)->get();
        $daftarBank = Bank::all();

        return view('konsumen.checkout', compact('items', 'grandTotal', 'daftarAlamat', 'daftarBank'));
    }

    public function prosesCheckout(Request $request)
    {
        $request->validate([
            'id_bank' => 'required|exists:bank,id_bank',
            'id_alamat' => 'nullable|exists:alamat,id_alamat',
            'metode_bayar' => 'required|in:manual_transfer,payment_gateway',
            'bukti_transfer' => 'nullable|image|mimes:jpeg,png,jpg|max:3072',
        ]);

        $user = Auth::user();
        $idAlamat = $request->id_alamat;

        // Jika user menambah alamat baru langsung di checkout
        if ($request->filled('new_alamat') && $request->filled('new_penerima')) {
            $newAlm = Alamat::create([
                'alamat' => $request->new_alamat,
                'penerima' => $request->new_penerima,
                'telepon_penerima' => $request->new_telepon ?? $user->nomor_telp,
                'id_user' => $user->id_user,
            ]);
            $idAlamat = $newAlm->id_alamat;
        }

        $items = Keranjang::with('produk.diskon')->where('id_user', $user->id_user)->get();
        if ($items->isEmpty()) {
            return redirect()->route('keranjang');
        }

        // Upload bukti pembayaran jika metode manual transfer
        $buktiFile = null;
        if ($request->hasFile('bukti_transfer')) {
            $file = $request->file('bukti_transfer');
            $buktiFile = 'bukti_' . time() . '_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/bukti'), $buktiFile);
        }

        $orderId = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
        $totalBayar = $items->sum('subtotal');

        // Integrasi Snap Token Midtrans jika online payment gateway
        $snapToken = null;
        if ($request->metode_bayar === 'payment_gateway') {
            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $totalBayar,
                ],
                'customer_details' => [
                    'first_name' => $user->username,
                    'email' => $user->email,
                    'phone' => $user->nomor_telp,
                ]
            ];
            $snapToken = $this->midtrans->getSnapToken($params);
        }

        DB::transaction(function () use ($items, $user, $orderId, $idAlamat, $request, $totalBayar, $buktiFile, $snapToken) {
            foreach ($items as $item) {
                Pesanan::create([
                    'id_pesanan' => $orderId,
                    'id_produk' => $item->id_produk,
                    'id_alamat' => $idAlamat,
                    'id_bank' => $request->id_bank,
                    'id_user' => $user->id_user,
                    'ukuran_produk' => $item->ukuran,
                    'jml_beli' => $item->jml_beli,
                    'total_bayar' => $totalBayar,
                    'kurir' => 'Teknisi PT. Eksavindo',
                    'jadwal_pemasangan' => null,
                    'status_keberangkatan' => 0,
                    'bukti_transfer' => $buktiFile,
                    'status_pembayaran' => 0,
                    'status_pemesanan' => 0,
                    'snap_token' => $snapToken,
                ]);

                $item->produk->decrement('stok', $item->jml_beli);
                $item->produk->increment('terjual', $item->jml_beli);
            }

            Keranjang::where('id_user', $user->id_user)->delete();
        });

        return redirect()->route('pesanan.saya')->with('success', 'Pesanan berhasil dibuat!');
    }

    public function pesananSaya()
    {
        $orders = Pesanan::with(['produk', 'bank', 'alamat'])
            ->where('id_user', Auth::user()->id_user)
            ->orderBy('created_at', 'desc')
            ->get();
        return view('konsumen.pesanan_saya', compact('orders'));
    }

    public function kuitansi($id)
    {
        $pesanan = Pesanan::with(['produk', 'bank', 'alamat', 'user'])
            ->where('id_pesanan', $id)
            ->firstOrFail();

        // Konsumen hanya bisa melihat miliknya sendiri (kecuali admin)
        if (!Auth::user()->isAdmin() && $pesanan->id_user !== Auth::user()->id_user) {
            abort(403);
        }

        return view('konsumen.kuitansi', compact('pesanan'));
    }

    /**
     * Webhook Handler dari Midtrans Payment Gateway
     */
    public function midtransCallback(Request $request)
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $orderId = $request->order_id;
        $statusCode = $request->status_code;
        $grossAmount = $request->gross_amount;
        $signatureKey = $request->signature_key;

        $mySignature = hash("sha512", $orderId . $statusCode . $grossAmount . $serverKey);
        if ($mySignature !== $signatureKey) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $pesanan = Pesanan::where('id_pesanan', $orderId)->first();
        if (!$pesanan) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $transStatus = $request->transaction_status;
        if ($transStatus == 'capture' || $transStatus == 'settlement') {
            $pesanan->update([
                'status_pembayaran' => 1, // Lunas
                'status_pemesanan' => 1   // Diproses
            ]);
        } elseif ($transStatus == 'pending') {
            $pesanan->update(['status_pembayaran' => 0]);
        } else {
            $pesanan->update([
                'status_pembayaran' => 2, // Ditolak / Batal
                'status_pemesanan' => 3
            ]);
        }

        return response()->json(['message' => 'Success']);
    }
}

