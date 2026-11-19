<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produk';
    protected $primaryKey = 'id_produk';

    protected $fillable = [
        'nama_produk',
        'label_produk',
        'ukuran',
        'daya',
        'id_kategori',
        'deskripsi_produk',
        'harga',
        'foto_produk',
        'stok',
        'terjual',
    ];

    protected $casts = [
        'harga' => 'integer',
        'stok' => 'integer',
        'terjual' => 'integer',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    public function diskon()
    {
        return $this->hasOne(Diskon::class, 'id_produk', 'id_produk')->where('is_active', 1);
    }

    public function allDiskon()
    {
        return $this->hasMany(Diskon::class, 'id_produk', 'id_produk');
    }

    public function keranjang()
    {
        return $this->hasMany(Keranjang::class, 'id_produk', 'id_produk');
    }

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'id_produk', 'id_produk');
    }

    public function getHargaFinalAttribute()
    {
        $diskon = $this->diskon ? $this->diskon->besar_diskon : 0;
        return max(0, $this->harga - $diskon);
    }
}

