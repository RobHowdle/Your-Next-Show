<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ServiceUser;
use App\Models\Venue;
use App\Models\Promoter;
use App\Models\OtherService;

class UpdateServiceVerification extends Command
{
    protected $signature = 'service:verify';
    protected $description = 'Update verification status for all services with linked users';

    public function handle()
    {
        $this->info('Starting service verification update...');

        $serviceUsers = ServiceUser::with('serviceable')
            ->whereNull('deleted_at')
            ->get();

        if ($serviceUsers->isEmpty()) {
            $this->error('No service users found!');
            return Command::FAILURE;
        }

        $this->info("Found {$serviceUsers->count()} service users to process.");

        $updatedRecords = [
            'Venue' => [],
            'Promoter' => [],
            'OtherService' => []
        ];

        foreach ($serviceUsers as $serviceUser) {
            $this->info("\nProcessing service user ID: {$serviceUser->id}");
            $this->info("Serviceable type: {$serviceUser->serviceable_type}");
            $this->info("Serviceable ID: {$serviceUser->serviceable_id}");

            $model = $serviceUser->serviceable;

            if (!$model) {
                $this->warn("No model found for service user {$serviceUser->id}");
                $this->warn("Checking direct model...");

                switch ($serviceUser->serviceable_type) {
                    case 'App\\Models\\Venue':
                        $model = Venue::find($serviceUser->serviceable_id);
                        break;
                    case 'App\\Models\\Promoter':
                        $model = Promoter::find($serviceUser->serviceable_id);
                        break;
                    case 'App\\Models\\OtherService':
                        $model = OtherService::find($serviceUser->serviceable_id);
                        break;
                }

                if ($model) {
                    $this->info("Found model directly");
                } else {
                    $this->error("Model not found in database");
                    continue;
                }
            }

            try {
                $model->verify();
                $type = class_basename($model);
                $updatedRecords[$type][] = [
                    'id' => $model->id,
                    'name' => $model->name ?? 'Unknown'
                ];
                $this->info("Verified {$type} ID: {$model->id}");
            } catch (\Exception $e) {
                $this->error("Failed to verify model: " . $e->getMessage());
            }
        }

        $this->info("\n\nVerification Summary:");

        foreach ($updatedRecords as $type => $records) {
            if (!empty($records)) {
                $this->info("\n{$type}s Updated (" . count($records) . "):");
                $this->table(
                    ['ID', 'Name'],
                    collect($records)->map(fn($record) => [$record['id'], $record['name']])
                );
            }
        }

        $this->info("\nVerification update complete!");
        return Command::SUCCESS;
    }
}