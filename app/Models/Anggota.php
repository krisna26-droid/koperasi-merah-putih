<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Anggota extends Model
{
    use HasFactory;

    protected $table = 'tb_anggota';
    protected $primaryKey = 'id_anggota';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_anggota',
        'nama',
        'alamat',
        'no_telepon',
        'status',
        'created_by',
    ];

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'username');
    }

    public function simpanan(): HasMany
    {
        return $this->hasMany(Simpanan::class, 'id_anggota', 'id_anggota');
    }

    public function pinjaman(): HasMany
    {
        return $this->hasMany(Pinjaman::class, 'id_anggota', 'id_anggota');
    }

    public function angsuran(): HasMany
    {
        return $this->hasMany(Angsuran::class, 'id_anggota', 'id_anggota');
    }
}