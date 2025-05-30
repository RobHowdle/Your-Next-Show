<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Note extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'notes';

    protected $fillable = [
        'serviceable_id',
        'serviceable_type',
        'name',
        'text',
        'date',
        'is_todo',
        'completed',
        'completed_at',
    ];

    protected $casts = [
        'date' => 'datetime',
        'completed_at' => 'datetime',
        'is_todo' => 'boolean',
        'completed' => 'boolean',
    ];

    public function serviceable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function markAsCompleted()
    {
        $this->completed = true;
        $this->completed_at = now();
        $this->save();
    }
}