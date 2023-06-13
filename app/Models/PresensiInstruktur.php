<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresensiInstruktur extends Model
{
    use HasFactory;
    // public $timestamps = false;
    protected $table = 'presensi_instruktur';
    protected $primaryKey = 'id_presensi_instruktur';
    protected $fillable = [
        'id_instruktur',
        'id_kelas',
        'status_presensi',
        'waktu_mulai',
        'waktu_selesai',
        'durasi',
        'keterlambatan',
        'tanggal_presensi',
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
