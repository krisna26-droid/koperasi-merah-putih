<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coa extends Model
{
    use HasFactory;

    protected $table = 'tb_coa';
    protected $primaryKey = 'kode_akun';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_akun',
        'nama_akun',
        'saldo_normal',
    ];

    // Relationships
    public function jurnalDetail(): HasMany
    {
        return $this->hasMany(JurnalDetail::class, 'kode_akun', 'kode_akun');
    }
}