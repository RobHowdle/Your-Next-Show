<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Todo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'serviceable_id',
        'serviceable_type',
        'item',
        'due_date',
        'completed',
        'completed_at'
    ];

    protected $casts = [
        'completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function serviceable()
    {
        return $this->morphTo();
    }

    public function promoters()
    {
        return $this->morphMany(Todo::class, 'serviceable');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}