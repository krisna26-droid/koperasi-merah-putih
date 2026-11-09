<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CooperativeSetting extends Model
{
    protected $table = 'cooperative_settings';

    protected $fillable = ['name', 'address', 'logo_path'];

    protected $attributes = [
        'name' => 'Koperasi Merah Putih',
        'address' => 'Desa Buruan, Gianyar, Bali',
    ];
}
