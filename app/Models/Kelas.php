<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Kelas extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'kelas';
    protected $primaryKey = 'id_kelas';
    protected $fillable = [        
        'nama_kelas',
        'harga_kelas',
        'kapasitas_kelas',
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
