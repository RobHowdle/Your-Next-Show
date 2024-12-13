<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = 'jobs';

    protected $fillable = [
        'name',
        'job_start_date',
        'job_end_date',
        'scope',
        'scope_url',
        'job_type',
        'estimated_amount',
        'final_amount',
        'job_status',
        'priority',
        'user_id'
    ];

    public function services()
    {
        return $this->morphToMany(OtherService::class, 'serviceable', 'job_service', 'job_id', 'serviceable_id');
    }
}
