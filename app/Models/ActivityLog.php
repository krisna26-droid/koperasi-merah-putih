<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'actor_username',
        'actor_name',
        'action',
        'subject_type',
        'subject_id',
        'changed_fields',
    ];

    protected $casts = [
        'changed_fields' => 'array',
    ];
}
