<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keranjang extends Model
{
    protected $table = 'keranjang';
    protected $primaryKey = 'id_keranjang';

    protected $fillable = [
        'id_produk',
        'ukuran',
        'jml_beli',
        'id_user',
    ];

    protected $casts = [
        'jml_beli' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    public function getSubtotalAttribute()
    {
        return $this->produk ? ($this->produk->harga_final * $this->jml_beli) : 0;
    }
}

