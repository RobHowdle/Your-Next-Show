<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Job extends Model
{
    protected $table = 'module_jobs';

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

    protected $dates = [
        'job_start_date',
        'job_end_date',
    ];

    public function services()
    {
        return $this->morphToMany(OtherService::class, 'serviceable', 'job_service', 'job_id', 'serviceable_id')
            ->wherePivot('serviceable_type', '=', 'App\Models\OtherService');
    }
}