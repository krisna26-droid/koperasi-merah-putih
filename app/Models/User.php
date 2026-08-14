<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'username';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'peran',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function anggotaCreated(): HasMany
    {
        return $this->hasMany(Anggota::class, 'created_by', 'username');
    }

    public function simpananCreated(): HasMany
    {
        return $this->hasMany(Simpanan::class, 'created_by', 'username');
    }

    public function pinjamanCreated(): HasMany
    {
        return $this->hasMany(Pinjaman::class, 'created_by', 'username');
    }

    public function pinjamanApproved(): HasMany
    {
        return $this->hasMany(Pinjaman::class, 'approved_by', 'username');
    }

    public function angsuranCreated(): HasMany
    {
        return $this->hasMany(Angsuran::class, 'created_by', 'username');
    }

    public function jurnalCreated(): HasMany
    {
        return $this->hasMany(JurnalUmum::class, 'created_by', 'username');
    }
}