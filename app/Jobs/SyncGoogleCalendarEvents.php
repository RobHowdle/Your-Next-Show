<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\CalendarController;

class SyncGoogleCalendarEvents implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $eventData;
    protected $userId;

    public function __construct($eventData, $userId)
    {
        $this->eventData = $eventData;
        $this->userId = $userId;
    }

    public function handle()
    {
        try {
            $user = \App\Models\User::findOrFail($this->userId);
            \Log::info('Processing sync job for user', ['user_id' => $this->userId]);

            $request = new \Illuminate\Http\Request();
            $request->merge([
                'event_id' => $this->eventData->id,
                'title' => $this->eventData->event_name,
                'date' => $this->eventData->event_date,
                'start_time' => $this->eventData->event_start_time,
                'end_time' => $this->eventData->event_end_time,
                'location' => $this->eventData->venue->name ?? '',
                'description' => $this->eventData->description ?? '',
            ]);

            // Set user for auth
            auth()->setUser($user);

            $calendarController = new CalendarController();
            $calendarController->syncGoogleCalendar($request);
        } catch (\Exception $e) {
            \Log::error('Failed to sync event', [
                'error' => $e->getMessage(),
                'user_id' => $this->userId,
                'event_id' => $this->eventData->id
            ]);
            throw $e;
        }
    }
}