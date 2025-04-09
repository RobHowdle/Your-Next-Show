<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpportunityApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'opportunity_id',
        'status',
        'application_data',
        'notes'
    ];

    protected $casts = [
        'application_data' => 'array'
    ];

    public function opportunity()
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function applicant()
    {
        return $this->morphTo();
    }
}