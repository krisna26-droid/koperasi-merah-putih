<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JurnalDetail extends Model
{
    use HasFactory;

    protected $table = 'tb_jurnal_detail';
    protected $primaryKey = 'id_detail';

    protected $fillable = [
        'id_jurnal',
        'kode_akun',
        'posisi',
        'nominal',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
    ];

    // Relationships
    public function header(): BelongsTo
    {
        return $this->belongsTo(JurnalUmum::class, 'id_jurnal', 'id_jurnal');
    }

    public function coa(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'kode_akun', 'kode_akun');
    }
}