<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiDepositKelas extends Model
{
    use HasFactory;

    public $keyType = 'string';
    public $timestamps = false;
    protected $table = 'transaksi_deposit_kelas';
    protected $primaryKey = 'id_deposit_kelas';
    protected $fillable = [
        'id_deposit_kelas',
        'id_pegawai',
        'id_member',
        'id_kelas',
        'id_promo',
        'tanggal_deposit_kelas',
        'jumlah_pembayaran_deposit_kelas',
        'jumlah_deposit_kelas',
        'bonus_deposit_kelas',
        'total_deposit_kelas',
        'tanggal_kadaluarsa_deposit_kelas',
    ];

    public function PegawaiForeignKey()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }
    public function MemberForeignKey()
    {
        return $this->belongsTo(Member::class, 'id_member', 'id_member');
    }
    public function KelasForeignKey()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }
    public function PromoForeignKey()
    {
        return $this->belongsTo(Promo::class, 'id_promo', 'id_promo');
    }
}
