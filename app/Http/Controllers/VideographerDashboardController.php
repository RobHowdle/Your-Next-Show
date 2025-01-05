<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Job;
use App\Models\VideographyReviews;
use Illuminate\Support\Facades\Auth;

class VideographerDashboardController extends Controller
{
    protected function getUserId()
    {
        return Auth::id();
    }

    public function index($dashboardType)
    {
        $modules = collect(session('modules', []));
        $user = Auth::user();
        $videographer = Auth::user()->load(['roles', 'otherService']);
        $role = $videographer->roles->first()->name;

        $todoItemsCount = $videographer->otherService()->with(['todos' => function ($query) {
            $query->where('completed', 0)->whereNull('deleted_at');
        }])->get()->pluck('todos')->flatten()->count();
        $jobCount = Job::where('user_id', $this->getUserId())->whereNotIn('job_status', ['done', 'cancelled'])->count();
        $pendingReviews = VideographyReviews::with('otherService')->where('review_approved', '0')->whereNull('deleted_at')->count();

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        return view('admin.dashboards.videographer-dash', [
            'userId' => $this->getUserId(),
            'user' => $user,
            'role' => $role,
            'dashboardType' => $dashboardType,
            'modules' => $modules,
            'videographer' => $videographer,
            'todoItemsCount' => $todoItemsCount,
            'jobCount' => $jobCount,
            'pendingReviews' => $pendingReviews,
        ]);
    }
}