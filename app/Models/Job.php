<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;


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
        'user_id',
        'lead_time',
        'lead_time_unit',
    ];

    protected $dates = [
        'job_start_date',
        'job_end_date',
        'completed_date',
    ];

    public function pivot()
    {
        return $this->belongsToMany(OtherService::class, 'job_service', 'job_id', 'serviceable_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class);;
    }

    public function promoter()
    {
        return $this->belongsTo(Promoter::class);
    }

    public function otherServices()
    {
        return $this->belongsTo(OtherService::class);
    }
}