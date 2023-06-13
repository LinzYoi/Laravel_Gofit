<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class JadwalUmum extends Model
{
    use HasFactory;

    // public $timestamps = false;
    protected $table = 'jadwal_umum';
    protected $primaryKey = 'id_jadwal_umum';
    protected $fillable = [        
        'id_instruktur',
        'id_kelas',
        'tanggal',    
        'hari',    
        'jam',
        'slot_kelas',
        'created_at',
        'updated_at'
    ];

    public function InstrukturForeignKey()
    {
        return $this->belongsTo(Instruktur::class, 'id_instruktur', 'id_instruktur');
    }

    public function KelasForeignKey()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
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
