<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Job;
use App\Models\User;
use App\Models\Venue;
use App\Models\Promoter;
use App\Models\OtherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\JobsUpdateRequest;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class JobsController extends Controller
{
    protected function getUserId()
    {
        return Auth::id();
    }

    public function showJobs($dashboardType)
    {
        $modules = collect(session('modules', []));
        $user = Auth::user()->load(['roles', 'otherService']);
        $role = $user->roles->first()->name;
        $service = $user->otherService(ucfirst($role))->first();

        $jobs = DB::table('module_jobs')
            ->join('job_service', 'module_jobs.id', '=', 'job_service.job_id')
            ->join('other_services', function ($join) {
                $join->on('job_service.serviceable_id', '=', 'other_services.id')
                    ->where('job_service.serviceable_type', '=', \App\Models\OtherService::class);
            })
            ->where('module_jobs.user_id', '=', Auth::id())
            ->select(
                'jobs.*',
                'other_services.*',
                'job_service.job_id as pivot_job_id',
                'job_service.serviceable_id as pivot_serviceable_id',
                'job_service.serviceable_type as pivot_serviceable_type'
            )
            ->paginate(10);


        return view('admin.dashboards.show-jobs', [
            'userId' => $this->getUserId(),
            'jobs' => $jobs,
            'modules' => $modules,
            'dashboardType' => $dashboardType,
        ]);
    }

    public function newJob($dashboardType)
    {
        $modules = collect(session('modules', []));

        return view('admin.dashboards.new-job', [
            'userId' => $this->getUserId(),
            'dashboardType' => $dashboardType,
            'modules' => $modules
        ]);
    }

    public function searchClients(Request $request)
    {
        $search = $request->input('query');

        // Search users by first_name and last_name
        $users = User::where(function ($query) use ($search) {
            $query->where('first_name', 'LIKE', "%{$search}%")
                ->orWhere('last_name', 'LIKE', "%{$search}%")
                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
        })->get(['id', 'first_name', 'last_name'])
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => "{$user->first_name} {$user->last_name}",
                    'service_type' => 'User'
                ];
            });

        // Search venues
        $venues = Venue::where('name', 'LIKE', "%{$search}%")
            ->get()
            ->map(function ($venue) {
                return [
                    'id' => $venue->id,
                    'name' => $venue->name,
                    'service_type' => 'Venue'
                ];
            });

        // Search promoters
        $promoters = Promoter::where('name', 'LIKE', "%{$search}%")
            ->get()
            ->map(function ($promoter) {
                return [
                    'id' => $promoter->id,
                    'name' => $promoter->name,
                    'service_type' => 'Promoter'
                ];
            });

        // Search other services
        $otherServices = OtherService::where('name', 'LIKE', "%{$search}%")
            ->with('otherServiceList')
            ->get()
            ->map(function ($otherService) {
                return [
                    'id' => $otherService->id,
                    'name' => $otherService->name,
                    'service_type' => $otherService->otherServiceList->service_name ?? null,
                ];
            });

        // Merge all results
        $clients = collect()
            ->merge($users)
            ->merge($venues)
            ->merge($promoters)
            ->merge($otherServices);

        return response()->json($clients);
    }

    public function storeJob($dashboardType, JobsUpdateRequest $request)
    {
        $modules = collect(session('modules', []));
        $user = Auth::user()->load('roles');
        $role = $user->getRoleNames()->first();
        $validated = $request->validated();

        $jobName =  $validated['client_name'] . ' - ' . $validated['job_type'] . ' - ' . Carbon::now();
        $jobFileUrl = '';
        if (isset($validated['job_scope_file'])) {
            $jobFile = $validated['job_scope_file'];
            $jobFileExtension = $jobFile->getClientOriginalExtension() ?: $jobFile->guessExtension();
            $jobFileName = Str::slug($jobName . '.' . $jobFileExtension);

            $destinationPath = public_path('jobs/' . strtolower($role) . '/' . $user->id);

            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            $jobFile->move($destinationPath, $jobName);
            $jobFileUrl = 'jobs/' . strtolower($role) . '/' . $user->id . '/' . $jobFileName;
        }

        $job = Job::create([
            'name' => $jobName,
            'job_start_date' => $validated['job_start_date'],
            'job_end_date' => $validated['job_deadline_date'],
            'scope' => $validated['job_text_scope'],
            'scope_url' => $jobFileUrl,
            'job_type' => $validated['job_type'],
            'estimated_amount' => $validated['job_cost'],
            'final_amount' => '0.00',
            'job_status' => $validated['job_status'],
            'priority' => $validated['job_priority'],
            'user_id' => $user->id,
        ]);

        if ($job) {
            DB::table('job_service')->insert([
                'job_id' => $job->id,
                'serviceable_id' => $validated['client_search'],
                'serviceable_type' => 'App\Models\OtherService',
            ]);
        }

        return redirect()->route('admin.dashboard.job.view', [
            'userId' => $this->getUserId(),
            'dashboardType' => $dashboardType,
            'modules' => $modules,
            'job' => $job,
            'id' => $job->id
        ]);
    }

    public function viewJob($dashboardType, Job $id)
    {
        $modules = collect(session('modules', []));

        $jobId = $id->id;
        $job = Job::findOrFail($jobId);

        return view('admin.dashboards.show-job', [
            'userId' => $this->getUserId(),
            'dashboardType' => $dashboardType,
            'modules' => $modules,
            'job' => $job,
        ]);
    }
}