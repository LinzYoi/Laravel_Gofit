<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepositKelasMember extends Model
{
    use HasFactory;

    public $keyType = 'string';
    public $timestamps = false;
    protected $table = 'deposit_kelas_member';
    protected $primaryKey = 'id_deposit_kelas_member';
    protected $fillable = [
        'id_member',
        'id_kelas',
        'tanggal_kadaluarsa',
        'sisa_deposit',
    ];
    
    public function MemberForeignKey()
    {
        return $this->belongsTo(Member::class, 'id_member', 'id_member');
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
