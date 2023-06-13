<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TransaksiAktivasi extends Model
{
    use HasFactory;

    public $keyType = 'string';
    public $timestamps = false;        
    protected $table = 'transaksi_aktivasi';
    protected $primaryKey = 'id_aktivasi';
    protected $fillable = [
        'id_aktivasi',
        'id_pegawai',
        'id_member',
        'tanggal_aktivasi',
        'tanggal_kadaluarsa',
        'jumlah_pembayaran_aktivasi',
        'jenis_pembayaran_aktivasi',
    ];

    public function PegawaiForeignKey()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }
    
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
