<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class JadwalHarian extends Model
{
    use HasFactory;

    // public $timestamps = false;
    protected $table = 'jadwal_harian';
    protected $primaryKey = 'id_jadwal_harian';
    protected $fillable = [
        'id_jadwal_harian',        
        'id_jadwal_umum',   
        'id_instruktur',
        'tanggal',
        'status',
        'slot_kelas',
        'created_at',
        'updated_at'
    ];

    public function JadwalUmumForeignKey()
    {
        return $this->belongsTo(JadwalUmum::class, 'id_jadwal_umum', 'id_jadwal_umum');
    }

    public function InstrukturForeignKey()
    {
        return $this->belongsTo(Instruktur::class, 'id_instruktur', 'id_instruktur');
    }

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
