<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OtherServicesReview;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class PhotographerDashboardController extends Controller
{
    protected function getUserId()
    {
        return Auth::id();
    }

    /**
     * Return the Dashboard
     */
    public function index($dashboardType)
    {
        $modules = collect(session('modules', []));
        $user = Auth::user();
        $role = $user->roles->first()->name;

        $pendingReviews = OtherServicesReview::with('otherService')->where('review_approved', '0')->whereNull('deleted_at')->count();
        $todoItemsCount = $user->otherService("Photography")->with(['todos' => function ($query) {
            $query->where('completed', 0)->whereNull('deleted_at');
        }])->get()->pluck('todos')->flatten()->count();

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        $jobsCount = $user->otherService('Photography')
            ->with(['jobs' => function ($query) use ($startOfWeek, $endOfWeek) {
                $query->whereBetween('job_start_date', [$startOfWeek, $endOfWeek]);
            }])
            ->get()
            ->pluck('jobs')
            ->flatten()
            ->count();

        return view('admin.dashboards.photographer-dash', [
            'userId' => $this->getUserId(),
            'dashboardType' => $dashboardType,
            'modules' => $modules,
            'pendingReviews' => $pendingReviews,
            'user' => $user,
            'role' => $role,
            'todoItemsCount' => $todoItemsCount,
            'jobsCount' => $jobsCount,
        ]);
    }
}
