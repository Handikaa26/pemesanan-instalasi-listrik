<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $table = 'bank';
    protected $primaryKey = 'id_bank';
    public $timestamps = false;

    protected $fillable = [
        'nama_bank',
        'norek',
        'atas_nama',
    ];

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'id_bank', 'id_bank');
    }
}

