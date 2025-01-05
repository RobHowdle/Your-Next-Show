<?php

namespace App\Services;

use App\Models\Todo;
use App\Models\Venue;
use App\Models\Promoter;
use App\Models\OtherService;

class TodoService
{
    private $typeModelMap = [
        'promoter' => Promoter::class,
        'venue' => Venue::class,
        'videographer' => ['class' => OtherService::class, 'type' => 'Videographer'],
        'artist' => ['class' => OtherService::class, 'type' => 'Artist'],
        'designer' => ['class' => OtherService::class, 'type' => 'Designer'],
        'photographer' => ['class' => OtherService::class, 'type' => 'Photographer']
    ];

    public function getUncompletedTodos($user, $dashboardType, $perPage = 6)
    {
        $query = $this->buildTodoQuery($user, $dashboardType, false);
        return $query ? $query->paginate($perPage) : collect();
    }

    private function buildTodoQuery($user, $dashboardType, $completed = false)
    {
        $modelInfo = $this->typeModelMap[$dashboardType] ?? null;

        if (!$modelInfo) {
            return null;
        }

        $query = Todo::query()
            ->where('completed', $completed)
            ->orderBy('created_at', 'DESC');

        if (is_string($modelInfo)) {
            return $query->where('serviceable_type', $modelInfo)
                ->whereIn('serviceable_id', $user->{$dashboardType . 's'}->pluck('id'));
        }

        return $query->where('serviceable_type', $modelInfo['class'])
            ->whereIn('serviceable_id', $user->otherService($modelInfo['type'])->pluck('other_services.id'));
    }

    public function getTodosByType($user, $dashboardType, $perPage, $page, $completed = false)
    {
        $query = $this->buildTodoQuery($user, $dashboardType, $completed);
        return $query ? $query->paginate($perPage, ['*'], 'page', $page) : collect();
    }

    public function hasCompletedTodos($user, $dashboardType)
    {
        $query = $this->buildTodoQuery($user, $dashboardType, true);
        return $query ? $query->exists() : false;
    }

    public function hasUncompletedTodos($user, $dashboardType)
    {
        $query = $this->buildTodoQuery($user, $dashboardType, false);
        return $query ? $query->exists() : false;
    }
}