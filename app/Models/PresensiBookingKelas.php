<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresensiBookingKelas extends Model
{
    use HasFactory;
    public $keyType = 'string';
    public $timestamps = false;
    protected $table = 'presensi_booking_kelas';
    protected $primaryKey = 'id_booking_kelas';
    protected $fillable = [
        'id_booking_kelas',
        'id_member',
        'id_jadwal_harian',        
        'tanggal_booking',
        'tanggal_yang_dibooking',   
        'jenis_pembayaran',     
        'waktu_presensi',
        'tarif',
        'status_member'
    ];

    public function MemberForeignKey()
    {
        return $this->belongsTo(Member::class, 'id_member', 'id_member');
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
