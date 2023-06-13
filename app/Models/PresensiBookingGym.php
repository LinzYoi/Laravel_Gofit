<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresensiBookingGym extends Model
{
    use HasFactory;
    public $keyType = 'string';
    public $timestamps = false;
    protected $table = 'presensi_booking_gym';
    protected $primaryKey = 'id_booking_gym';
    protected $fillable = [
        'id_booking_gym',
        'id_member',
        'tanggal_booking',
        'tanggal_yang_dibooking',
        'slot_waktu',
        'waktu_presensi',
    ];

    public function MemberForeignKey()
    {
        return $this->belongsTo(Member::class, 'id_member', 'id_member');
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
