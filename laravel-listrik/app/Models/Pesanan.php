<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $table = 'pesanan';
    protected $primaryKey = 'id_pesanan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pesanan',
        'id_produk',
        'id_alamat',
        'id_bank',
        'id_user',
        'ukuran_produk',
        'jml_beli',
        'total_bayar',
        'kurir',
        'jadwal_pemasangan',
        'status_keberangkatan', // 0: Menunggu, 1: Teknisi Ditugaskan, 2: Menuju Lokasi, 3: Selesai
        'bukti_transfer',
        'status_pembayaran',    // 0: Menunggu, 1: Lunas/Terverifikasi, 2: Ditolak
        'status_pemesanan',     // 0: Baru, 1: Diproses, 2: Selesai, 3: Batal
        'snap_token',           // Integrasi Online Payment Gateway (Midtrans)
    ];

    protected $casts = [
        'jml_beli' => 'integer',
        'total_bayar' => 'integer',
        'status_keberangkatan' => 'integer',
        'status_pembayaran' => 'integer',
        'status_pemesanan' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    public function alamat()
    {
        return $this->belongsTo(Alamat::class, 'id_alamat', 'id_alamat');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'id_bank', 'id_bank');
    }
}

