<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Instruktur extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    public $timestamps = false;
    protected $table = 'instruktur';
    protected $primaryKey = 'id_instruktur';
    protected $fillable = [        
        'nama_instruktur',
        'alamat_instruktur',
        'tanggal_lahir_instruktur',
        'no_telepon_instruktur',
        'gaji_instruktur',        
        'akumulasi_terlambat',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getCreatedAtAttribute()
    {
        if (is_null($this->attributes['created_at'])) {
            return Carbon::parse($this->attributes['created_at'])->format('Y-m-d H:i:s');
        }
    }

    public function getUpdateAtAttribute()
    {
        if (is_null($this->attributes['update_at'])) {
            return Carbon::parse($this->attributes['update_at'])->format('Y-m-d H:i:s');
        }
    }
}
