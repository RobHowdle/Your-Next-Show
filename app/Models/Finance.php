<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Finance extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'finances';

    protected $fillable = [
        'user_id',
        'serviceable_id',
        'serviceable_type',
        'finance_type',
        'name',
        'date_from',
        'date_to',
        'external_link',
        'incoming',
        'other_incoming',
        'outgoing',
        'other_outgoing',
        'desired_profit',
        'total_incoming',
        'total_outgoing',
        'total_profit',
        'total_remaining_to_desired_profit',
    ];

    public function serviceable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getYearlyStats(?int $year = null): array
    {
        $year = $year ?? Carbon::now()->year;

        $stats = $this->whereYear('date_from', $year)
            ->select([
                DB::raw('SUM(total_incoming) as yearly_income'),
                DB::raw('SUM(total_outgoing) as yearly_outgoing'),
                DB::raw('SUM(total_profit) as yearly_profit')
            ])
            ->first();

        return [
            'yearly_income' => $stats->yearly_income ?? 0,
            'yearly_outgoing' => $stats->yearly_outgoing ?? 0,
            'yearly_profit' => $stats->yearly_profit ?? 0
        ];
    }

    public static function getServiceYearlyStats(int $serviceableId, string $serviceableType, ?int $year = null): array
    {
        $year = $year ?? Carbon::now()->year;

        $stats = self::where('serviceable_id', $serviceableId)
            ->where('serviceable_type', $serviceableType)
            ->whereYear('date_from', $year)
            ->select([
                DB::raw('SUM(total_incoming) as yearly_income'),
                DB::raw('SUM(total_outgoing) as yearly_outgoing'),
                DB::raw('SUM(total_profit) as yearly_profit')
            ])
            ->first();

        return [
            'yearly_income' => $stats->yearly_income ?? 0,
            'yearly_outgoing' => $stats->yearly_outgoing ?? 0,
            'yearly_profit' => $stats->yearly_profit ?? 0
        ];
    }
}