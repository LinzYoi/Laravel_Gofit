<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Pegawai extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    public $timestamps = false;
    protected $table = 'pegawai';
    protected $primaryKey = 'id_pegawai';
    protected $fillable = [
        'nama_pegawai',
        'alamat_pegawai',
        'tanggal_lahir_pegawai',    
        'no_telepon_pegawai',
        'role_pegawai',   
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
