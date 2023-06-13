<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Promo extends Model
{
    use HasFactory;

    protected $table = 'promo';
    public $timestamps = false;
    protected $primaryKey = 'id_promo';
    protected $fillable = [        
        'nama_promo',
        'jenis_promo',
        'keterangan_promo',
        'bonus_promo',
        'minimal_pembelian',
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
