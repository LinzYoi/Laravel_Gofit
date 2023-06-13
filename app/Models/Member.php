<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Member extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    public $keyType = 'string';
    public $timestamps = false;
    protected $table = 'member';
    protected $primaryKey = 'id_member';
    protected $fillable = [
        'id_member',
        'nama_member',
        'alamat_member',
        'tanggal_lahir_member',    
        'no_telepon_member',
        'jenis_kelamin_member',
        'status_member',
        'sisa_deposit_uang_member',
        'masa_berlaku_member',
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
