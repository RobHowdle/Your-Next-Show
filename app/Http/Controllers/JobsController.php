<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Job;
use App\Models\User;
use App\Models\Venue;
use App\Models\Promoter;
use Illuminate\Support\Str;
use App\Models\OtherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\JobsUpdateRequest;

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
                'module_jobs.*',
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
        $user = Auth::user()->load(['roles', 'promoters', 'venues', 'otherService']);

        switch ($dashboardType) {
            case 'promoter':
                $role = $user->promoters()->first();

                if ($role) {
                    $packages = collect(json_decode($role->packages, true))
                        ->map(function ($package) {
                            return [
                                'job_type' => $package['job_type'] ?? null,
                                'name' => $package['title'] ?? null,
                                'lead_time' => $package['lead_time'] ?? null,
                                'lead_time_unit' => $package['lead_time_unit'] ?? null,
                                'price' => $package['price'] ?? null
                            ];
                        });
                    \Log::info('Packages:', $packages->toArray()); // Debug log
                };
                break;
            case 'artist':
                $role = $user->otherService('service')->first();
                if ($role) {
                    $packages = collect(json_decode($role->packages, true))
                        ->map(function ($package) {
                            return [
                                'job_type' => $package['job_type'] ?? null,
                                'name' => $package['title'] ?? null,
                                'lead_time' => $package['lead_time'] ?? null,
                                'lead_time_unit' => $package['lead_time_unit'] ?? null,
                                'price' => $package['price'] ?? null
                            ];
                        });
                    \Log::info('Packages:', $packages->toArray()); // Debug log
                };
                break;
            case 'designer':
                $role = $user->otherService('service')->first();
                if ($role) {
                    $packages = collect(json_decode($role->packages, true))
                        ->map(function ($package) {
                            return [
                                'job_type' => $package['job_type'] ?? null,
                                'name' => $package['title'] ?? null,
                                'lead_time' => $package['lead_time'] ?? null,
                                'lead_time_unit' => $package['lead_time_unit'] ?? null,
                                'price' => $package['price'] ?? null
                            ];
                        });
                    \Log::info('Packages:', $packages->toArray()); // Debug log
                };
                break;
            case 'videographer':
                $role = $user->otherService('service')->first();
                if ($role) {
                    $packages = collect(json_decode($role->packages, true))
                        ->map(function ($package) {
                            return [
                                'job_type' => $package['job_type'] ?? null,
                                'name' => $package['title'] ?? null,
                                'lead_time' => $package['lead_time'] ?? null,
                                'lead_time_unit' => $package['lead_time_unit'] ?? null,
                                'price' => $package['price'] ?? null
                            ];
                        });
                    \Log::info('Packages:', $packages->toArray()); // Debug log
                };
                break;
            case 'photographer':
                $role = $user->otherService('service')->first();
                if ($role) {
                    $packages = collect(json_decode($role->packages, true))
                        ->map(function ($package) {
                            return [
                                'job_type' => $package['job_type'] ?? null,
                                'name' => $package['title'] ?? null,
                                'lead_time' => $package['lead_time'] ?? null,
                                'lead_time_unit' => $package['lead_time_unit'] ?? null,
                                'price' => $package['price'] ?? null
                            ];
                        });
                    \Log::info('Packages:', $packages->toArray()); // Debug log
                };
                break;
            case 'venue':
                $role = $user->venues()->first();

                if ($role) {
                    $packages = collect(json_decode($role->packages, true))
                        ->map(function ($package) {
                            return [
                                'id' => $package['job_type'] ?? null,
                                'name' => $package['title'] ?? null,
                                'lead_time' => $package['lead_time'] ?? null,
                                'lead_time_unit' => $package['lead_time_unit'] ?? null,
                                'price' => $package['price'] ?? null
                            ];
                        });
                    \Log::info('Packages:', $packages->toArray()); // Debug log
                };
                break;
        }

        return view('admin.dashboards.new-job', [
            'userId' => $this->getUserId(),
            'dashboardType' => $dashboardType,
            'modules' => $modules,
            'packages' => $packages ?? collect(),
        ]);
    }

    public function storeJob($dashboardType, JobsUpdateRequest $request)
    {
        $modules = collect(session('modules', []));
        $user = Auth::user()->load('roles');
        $role = $user->getRoleNames()->first();
        $validated = $request->validated();

        $jobName =  $validated['client_name'] . ' - ' . $validated['package'] . ' - ' . Carbon::now();
        $jobFileUrl = '';
        if (isset($validated['job_scope_file'])) {
            $jobFile = $validated['job_scope_file'];
            $jobFileExtension = $jobFile->getClientOriginalExtension() ?: $jobFile->guessExtension();
            $jobFileName = Str::slug($jobName . '.' . $jobFileExtension);

            $path = 'jobs/' . strtolower($role) . '/' . $user->id;
            $jobFileUrl = $jobFile->storeAs($path, $jobFileName, 'public');
        }

        $job = Job::create([
            'name' => $jobName,
            'job_start_date' => $validated['job_start_date'],
            'job_end_date' => $validated['job_deadline_date'],
            'scope' => $validated['job_text_scope'],
            'scope_url' => $jobFileUrl,
            'job_type' => $validated['package'],
            'estimated_amount' => $validated['job_cost'],
            'final_amount' => '0.00',
            'job_status' => $validated['job_status'],
            'priority' => $validated['job_priority'],
            'user_id' => $user->id,
            'lead_time' => $validated['estimated_lead_time_value'],
            'lead_time_unit' => $validated['estimated_lead_time_unit'],
        ]);

        if ($job) {
            DB::table('job_service')->insert([
                'job_id' => $job->id,
                'serviceable_id' => $validated['client_search'],
                'serviceable_type' => $this->getServiceClass($validated['client_service']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Job created successfully',
                'redirect' => route('admin.dashboard.jobs.view', [
                    'dashboardType' => $dashboardType,
                    'job' => $job->id
                ])
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to create job'
        ], 500);
    }

    public function viewJob($dashboardType, Job $job)
    {
        $modules = collect(session('modules', []));

        // $job->load(['services' => function ($query) {
        //     $query->where('serviceable_type', 'App\Models\OtherService');
        // }, 'venue', 'promoter', 'user']);
        $job->load(['otherServices', 'venue', 'promoter', 'user', 'pivot']);

        return view('admin.dashboards.show-job', [
            'userId' => $this->getUserId(),
            'dashboardType' => $dashboardType,
            'modules' => $modules,
            'job' => $job,
        ]);
    }

    public function editJob($dashboardType, Job $job)
    {
        $modules = collect(session('modules', []));
        $user = Auth::user()->load('roles');
        $role = $user->getRoleNames()->first();
        switch ($dashboardType) {
            case 'promoter':
                $role = $user->promoters()->first();

                if ($role) {
                    $packages = collect(json_decode($role->packages, true))
                        ->map(function ($package) {
                            return [
                                'job_type' => $package['job_type'] ?? null,
                                'name' => $package['title'] ?? null,
                                'lead_time' => $package['lead_time'] ?? null,
                                'lead_time_unit' => $package['lead_time_unit'] ?? null,
                                'price' => $package['price'] ?? null
                            ];
                        });
                };
                break;
            case 'artist':
                $role = $user->otherService('service')->first();
                if ($role) {
                    $packages = collect(json_decode($role->packages, true))
                        ->map(function ($package) {
                            return [
                                'job_type' => $package['job_type'] ?? null,
                                'name' => $package['title'] ?? null,
                                'lead_time' => $package['lead_time'] ?? null,
                                'lead_time_unit' => $package['lead_time_unit'] ?? null,
                                'price' => $package['price'] ?? null
                            ];
                        });
                };
                break;
            case 'designer':
                $role = $user->otherService('service')->first();
                if ($role) {
                    $packages = collect(json_decode($role->packages, true))
                        ->map(function ($package) {
                            return [
                                'job_type' => $package['job_type'] ?? null,
                                'name' => $package['title'] ?? null,
                                'lead_time' => $package['lead_time'] ?? null,
                                'lead_time_unit' => $package['lead_time_unit'] ?? null,
                                'price' => $package['price'] ?? null
                            ];
                        });
                };
                break;
            case 'videographer':
                $role = $user->otherService('service')->first();
                if ($role) {
                    $packages = collect(json_decode($role->packages, true))
                        ->map(function ($package) {
                            return [
                                'job_type' => $package['job_type'] ?? null,
                                'name' => $package['title'] ?? null,
                                'lead_time' => $package['lead_time'] ?? null,
                                'lead_time_unit' => $package['lead_time_unit'] ?? null,
                                'price' => $package['price'] ?? null
                            ];
                        });
                };
                break;
            case 'photographer':
                $role = $user->otherService('service')->first();
                if ($role) {
                    $packages = collect(json_decode($role->packages, true))
                        ->map(function ($package) {
                            return [
                                'job_type' => $package['job_type'] ?? null,
                                'name' => $package['title'] ?? null,
                                'lead_time' => $package['lead_time'] ?? null,
                                'lead_time_unit' => $package['lead_time_unit'] ?? null,
                                'price' => $package['price'] ?? null
                            ];
                        });
                };
                break;
            case 'venue':
                $role = $user->venues()->first();

                if ($role) {
                    $packages = collect(json_decode($role->packages, true))
                        ->map(function ($package) {
                            return [
                                'id' => $package['job_type'] ?? null,
                                'name' => $package['title'] ?? null,
                                'lead_time' => $package['lead_time'] ?? null,
                                'lead_time_unit' => $package['lead_time_unit'] ?? null,
                                'price' => $package['price'] ?? null
                            ];
                        });
                };
                break;
        }

        $job->load(['otherServices', 'venue', 'promoter', 'user', 'pivot']);
        return view('admin.dashboards.edit-job', [
            'userId' => $this->getUserId(),
            'dashboardType' => $dashboardType,
            'modules' => $modules,
            'job' => $job,
            'packages' => $packages ?? collect(),
            'role' => $role,
        ]);
    }

    public function updateJob($dashboardType, Job $job, JobsUpdateRequest $request)
    {
        $validated = $request->validated();
        $changes = [];

        // Check which fields have changed
        if ($job->job_start_date != $validated['job_start_date']) {
            $changes['job_start_date'] = $validated['job_start_date'];
        }
        if ($job->job_end_date != $validated['job_deadline_date']) {
            $changes['job_end_date'] = $validated['job_deadline_date'];
        }
        if ($job->scope != $validated['job_text_scope']) {
            $changes['scope'] = $validated['job_text_scope'];
        }
        if ($job->job_type != $validated['package']) {
            $changes['job_type'] = $validated['package'];
        }
        if ($job->estimated_amount != $validated['job_cost']) {
            $changes['estimated_amount'] = $validated['job_cost'];
        }
        if ($job->job_status != $validated['job_status']) {
            $changes['job_status'] = $validated['job_status'];
        }
        if ($job->priority != $validated['job_priority']) {
            $changes['priority'] = $validated['job_priority'];
        }
        if ($job->lead_time != $validated['estimated_lead_time_value']) {
            $changes['lead_time'] = $validated['estimated_lead_time_value'];
        }
        if ($job->lead_time_unit != $validated['estimated_lead_time_unit']) {
            $changes['lead_time_unit'] = $validated['estimated_lead_time_unit'];
        }

        // Handle file upload if new file
        if (isset($validated['job_scope_file'])) {
            $jobFile = $validated['job_scope_file'];
            $jobFileExtension = $jobFile->getClientOriginalExtension() ?: $jobFile->guessExtension();
            $jobFileName = Str::slug($job->name . '.' . $jobFileExtension);

            // Delete old file if exists
            if ($job->scope_url && Storage::disk('public')->exists($job->scope_url)) {
                Storage::disk('public')->delete($job->scope_url);
            }

            $path = 'jobs/' . strtolower($dashboardType) . '/' . $job->user_id;
            $changes['scope_url'] = $jobFile->storeAs($path, $jobFileName, 'public');
        }

        // Update job service if client changed
        if (isset($validated['client_search']) && $job->pivot->first()->id != $validated['client_search']) {
            DB::table('job_service')
                ->where('job_id', $job->id)
                ->update([
                    'serviceable_id' => $validated['client_search'],
                    'serviceable_type' => $this->getServiceClass($validated['client_service']),
                    'updated_at' => now(),
                ]);
        }

        // Only update if there are changes
        if (!empty($changes)) {
            $job->update($changes);
        }

        return response()->json([
            'success' => true,
            'message' => 'Job updated successfully',
            'redirect' => route('admin.dashboard.jobs.view', [
                'dashboardType' => $dashboardType,
                'job' => $job->id
            ])
        ]);
    }

    public function completeJob($dashboardType, Job $job, Request $request)
    {
        $job->update([
            'job_status' => 'completed',
            'completed_at' => $request->completed_at,
            'final_amount' => $request->final_amount,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Job marked as completed',
            'redirect' => route('admin.dashboard.jobs.view', [
                'dashboardType' => $dashboardType,
                'job' => $job->id
            ])
        ]);
    }

    public function deleteJob($dashboardType, Job $job)
    {
        if ($job->scope_url && Storage::disk('public')->exists($job->scope_url)) {
            Storage::disk('public')->delete($job->scope_url);
        }

        $job->delete();

        return response()->json([
            'success' => true,
            'message' => 'Job deleted successfully',
            'redirect' => route('admin.dashboard.jobs', ['dashboardType' => $dashboardType])
        ]);
    }

    public function downloadFile($dashboardType, Job $job)
    {
        if (!$job->scope_url || !Storage::disk('public')->exists($job->scope_url)) {
            abort(404);
        }

        return Storage::disk('public')->download($job->scope_url);
    }

    private function getServiceClass($serviceType)
    {
        return match (strtolower($serviceType)) {
            'artist' => 'App\Models\OtherService',
            'designer' => 'App\Models\OtherService',
            'photographer' => 'App\Models\OtherService',
            'videographer' => 'App\Models\OtherService',
            'venue' => 'App\Models\Venue',
            'promoter' => 'App\Models\Promoter',
            default => null
        };
    }
}