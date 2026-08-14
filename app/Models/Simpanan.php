<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Simpanan extends Model
{
    use HasFactory;

    protected $table = 'tb_simpanan';
    protected $primaryKey = 'id_simpanan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_simpanan',
        'id_anggota',
        'jenis_simpanan',
        'nominal',
        'is_jurnaled',
        'created_by',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'is_jurnaled' => 'boolean',
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
}