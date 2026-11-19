<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'user';
    protected $primaryKey = 'id_user';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_user',
        'username',
        'email',
        'password',
        'nomor_telp',
        'tanggal_lahir',
        'foto_profil',
        'id_role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'integer',
        'tanggal_lahir' => 'date',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    public function alamat()
    {
        return $this->hasMany(Alamat::class, 'id_user', 'id_user');
    }

    public function keranjang()
    {
        return $this->hasMany(Keranjang::class, 'id_user', 'id_user');
    }

    public function favorit()
    {
        return $this->hasMany(Favorit::class, 'id_user', 'id_user');
    }

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'id_user', 'id_user');
    }

    public function isAdmin(): bool
    {
        return $this->id_role === 1;
    }
}

