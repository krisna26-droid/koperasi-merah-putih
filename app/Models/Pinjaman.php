<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pinjaman extends Model
{
    use HasFactory;

    protected $table = 'tb_pinjaman';
    protected $primaryKey = 'id_pinjam';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pinjam',
        'id_anggota',
        'jumlah_pinjam',
        'tenor',
        'bunga_rate',
        'pokok_per_bulan',
        'bunga_per_bulan',
        'sisa_tagihan',
        'status',
        'is_jurnaled',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'jumlah_pinjam' => 'decimal:2',
        'tenor' => 'integer',
        'bunga_rate' => 'decimal:2',
        'pokok_per_bulan' => 'decimal:2',
        'bunga_per_bulan' => 'decimal:2',
        'sisa_tagihan' => 'decimal:2',
        'is_jurnaled' => 'boolean',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class, 'id_anggota', 'id_anggota');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'username');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by', 'username');
    }

    public function angsuran(): HasMany
    {
        return $this->hasMany(Angsuran::class, 'id_pinjam', 'id_pinjam');
    }
}