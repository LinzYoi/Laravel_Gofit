<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TransaksiDepositUang extends Model
{
    use HasFactory;

    public $keyType = 'string';
    public $timestamps = false;    
    protected $table = 'transaksi_deposit_uang';
    protected $primaryKey = 'id_deposit_uang';
    protected $fillable = [        
        'id_deposit_uang',
        'id_pegawai',
        'id_member',
        'id_promo',
        'tanggal_deposit_uang',
        'sisa_deposit_uang_member_sebelum',
        'jumlah_pembayaran_deposit_uang',        
        'bonus_deposit_uang',
        'total_deposit_uang',
    ];

    public function PegawaiForeignKey()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }

    public function MemberForeignKey()
    {
        return $this->belongsTo(Member::class, 'id_member', 'id_member');
    }

    public function PromoForeignKey()
    {
        return $this->belongsTo(Promo::class, 'id_promo', 'id_promo');
    }
}
