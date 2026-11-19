<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diskon extends Model
{
    protected $table = 'diskon';
    protected $primaryKey = 'id_diskon';
    public $timestamps = false;

    protected $fillable = [
        'id_produk',
        'besar_diskon',
        'is_active',
    ];

    protected $casts = [
        'besar_diskon' => 'integer',
        'is_active' => 'integer',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }
}

