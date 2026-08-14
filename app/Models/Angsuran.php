<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Angsuran extends Model
{
    use HasFactory;

    protected $table = 'tb_angsuran';
    protected $primaryKey = 'id_angsuran';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_angsuran',
        'id_pinjam',
        'id_anggota',
        'angsuran_ke',
        'bayar_pokok',
        'bayar_bunga',
        'total_bayar',
        'is_jurnaled',
        'created_by',
    ];

    protected $casts = [
        'angsuran_ke' => 'integer',
        'bayar_pokok' => 'decimal:2',
        'bayar_bunga' => 'decimal:2',
        'total_bayar' => 'decimal:2',
        'is_jurnaled' => 'boolean',
    ];

    // Relationships
    public function pinjaman(): BelongsTo
    {
        return $this->belongsTo(Pinjaman::class, 'id_pinjam', 'id_pinjam');
    }

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class, 'id_anggota', 'id_anggota');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'username');
    }
}