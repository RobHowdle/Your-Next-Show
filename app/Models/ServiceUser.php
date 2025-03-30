<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceUser extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'service_user';

    protected $fillable = [
        'user_id',
        'serviceable_id',
        'serviceable_type',
        'role_id',
    ];

    public $timestamps = true;

    public function serviceable(): MorphTo
    {
        return $this->morphTo();
    }
}