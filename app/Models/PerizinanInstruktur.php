<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerizinanInstruktur extends Model
{
    use HasFactory;
    // public $timestamps = false;
    protected $table = 'perizinan_instruktur';
    protected $primaryKey = 'id_perizinan';
    protected $fillable = [
        'id_instruktur',
        'id_jadwal_harian',
        'tanggal_perizinan',
        'tanggal_pembuatan_perizinan',
        'status_perizinan',
        'keterangan_perizinan',
        'tanggal_konfirmasi_perizinan'
    ];

    public function InstrukturForeignKey()
    {
        return $this->belongsTo(Instruktur::class, 'id_instruktur', 'id_instruktur');
    }

    public function JadwalHarianForeignKey()
    {
        return $this->belongsTo(JadwalHarian::class, 'id_jadwal_harian', 'id_jadwal_harian');
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
