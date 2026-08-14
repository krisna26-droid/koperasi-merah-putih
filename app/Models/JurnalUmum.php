<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JurnalUmum extends Model
{
    use HasFactory;

    protected $table = 'tb_jurnal_umum';
    protected $primaryKey = 'id_jurnal';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_jurnal',
        'tanggal',
        'id_referensi',
        'tipe_transaksi',
        'keterangan',
        'is_reversal',
        'reversed_ref',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'is_reversal' => 'boolean',
    ];

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'username');
    }

    public function details(): HasMany
    {
        return $this->hasMany(JurnalDetail::class, 'id_jurnal', 'id_jurnal');
    }

    public function reversedJournal(): BelongsTo
    {
        return $this->belongsTo(JurnalUmum::class, 'reversed_ref', 'id_jurnal');
    }
}