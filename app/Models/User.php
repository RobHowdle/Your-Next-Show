<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Role;
use App\Models\StandardUser;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'date_of_birth',
        'location',
        'postal_town',
        'latitude',
        'longitude',
        'apple_calendar_synced',
        'google_access_token',
        'google_refresh_token',
        'token_expires_at',
        'mailing_preferences',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_logged_in' => 'datetime',
        'mailing_preferences' => 'array',
    ];

    protected $dates = [
        'last_logged_in',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (is_null($user->mailing_preferences)) {
                $user->mailing_preferences = [
                    'system_announcements' => true,
                    'legal_or_policy_updates' => true,
                    'account_notifications' => true,
                    'event_invitations' => true,
                    'surveys_and_feedback' => true,
                    'birthday_anniversary_holiday' => true,
                ];
            }
        });
    }

    public function hasActiveModule($user, $module)
    {
        return $user->moduleSettings()->where('module', $module)->where('status', 1)->exists();
    }

    public function linkedCompany()
    {
        $service = DB::table('service_user')
            ->where('user_id', $this->id)
            ->whereNull('deleted_at')
            ->first();
        if (!$service) {
            return null;
        }

        switch ($service->serviceable_type) {
            case 'App\Models\Promoter':
                return Promoter::find($service->serviceable_id);
            case 'App\Models\Venue':
                return Venue::find($service->serviceable_id);
            case 'App\Models\OtherService':
                return OtherService::find($service->serviceable_id);
            default:
                return null;
        }
    }


    public function services()
    {
        $promoters = $this->morphedByMany(Promoter::class, 'serviceable', 'service_user', 'user_id', 'serviceable_id')
            ->select('promoters.*', 'service_user.user_id as pivot_user_id', 'service_user.serviceable_id as pivot_serviceable_id', 'service_user.serviceable_type as pivot_serviceable_type', 'service_user.deleted_at as pivot_deleted_at')
            ->get();

        $venues = $this->morphedByMany(Venue::class, 'serviceable', 'service_user', 'user_id', 'serviceable_id')
            ->select('venues.*', 'service_user.user_id as pivot_user_id', 'service_user.serviceable_id as pivot_serviceable_id', 'service_user.serviceable_type as pivot_serviceable_type', 'service_user.deleted_at as pivot_deleted_at')
            ->get();

        return $promoters->merge($venues);
    }

    public function promoters(string $role = null): MorphToMany
    {
        $query = $this->morphedByMany(Promoter::class, 'serviceable', 'service_user', 'user_id', 'serviceable_id')->whereNull('service_user.deleted_at');

        if ($role) {
            $roleId = $this->role_id;
            if ($roleId) {
                $query->where('serviceable_id', $roleId);
            }
        }

        return $query;
    }

    public function venues(): MorphToMany
    {
        return $this->morphedByMany(Venue::class, 'serviceable', 'service_user', 'user_id', 'serviceable_id')->whereNull('service_user.deleted_at');
    }

    public function otherService(string $role = null): MorphToMany
    {
        $query = $this->morphedByMany(OtherService::class, 'serviceable', 'service_user', 'user_id', 'serviceable_id')->whereNull('service_user.deleted_at');

        if ($role) {
            $roleId = $this->getRoleIdByRole($role);
            if ($roleId) {
                $query->where('serviceable_id', $roleId);
            }
        }

        return $query;
    }

    private function getRoleIdByRole(string $role): ?int
    {
        $roleMapping = [
            'photographer' => 1,
            'videographer' => 2,
            'designer' => 3,
            'artist' => 4,
        ];

        return $roleMapping[$role] ?? null;
    }

    public function standardUser(): MorphToMany
    {
        return $this->morphedByMany(StandardUser::class, 'serviceable', 'service_user', 'user_id', 'serviceable_id')->whereNull('service_user.deleted_at');
    }

    public function todos()
    {
        return $this->morphMany(Todo::class, 'serviceable');
    }

    public function notes()
    {
        return $this->morphMany(Note::class, 'serviceable');
    }

    public function getRoleType()
    {
        $excludedRoles = ['promoter', 'venue', 'standard', 'administrator'];

        if ($this->hasRole('promoter')) {
            return Promoter::class;
        }

        if ($this->hasRole('venue')) {
            return Venue::class;
        }

        $userRoles = $this->getRoleNames()->toArray();
        $filteredRoles = array_diff($userRoles, $excludedRoles);

        if (!empty($filteredRoles)) {
            return OtherService::class;
        }

        return null;
    }

    public function moduleSettings()
    {
        return $this->hasMany(UserModuleSetting::class);
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_user');
    }
}