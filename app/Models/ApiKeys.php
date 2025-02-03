<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ApiKeys extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'serviceable_type',
        'serviceable_id',
        'name',
        'key_type',
        'api_key',
        'api_secret',
        'is_active',
        'expires_at',
        'last_used_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime'
    ];

    public function serviceable()
    {
        return $this->morphTo();
    }
}