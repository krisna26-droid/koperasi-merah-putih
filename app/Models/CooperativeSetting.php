<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CooperativeSetting extends Model
{
    protected $table = 'cooperative_settings';

    protected $fillable = ['name', 'address', 'logo_path', 'default_loan_interest_rate'];

    protected $attributes = [
        'name' => 'Koperasi Merah Putih',
        'address' => 'Desa Buruan, Gianyar, Bali',
        'default_loan_interest_rate' => 1.50,
    ];

    protected $casts = [
        'default_loan_interest_rate' => 'decimal:2',
    ];
}
