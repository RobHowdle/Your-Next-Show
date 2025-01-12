<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OtherService; // Replace with the correct model namespace
use Illuminate\Support\Facades\Storage;

class UpdateOtherServicesFromCSV extends Command
{
    protected $signature = 'update:other-services-from-csv {file}';
    protected $description = 'Update other services from a CSV file';

    public function handle()
    {
        $filePath = base_path('database/data/' . $this->argument('file'));

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $file = fopen($filePath, 'r');
        $headers = fgetcsv($file);
        $updated = 0;
        $created = 0;

        while (($row = fgetcsv($file)) !== false) {
            $data = array_combine($headers, $row);

            // Use updateOrCreate to either update existing or create new
            $service = OtherService::updateOrCreate(
                ['name' => $data['name']], // Search criteria
                [
                    'logo_url' => $data['logo_url'],
                    'location' => $data['location'],
                    'postal_town' => $data['postal_town'],
                    'longitude' => $data['longitude'],
                    'latitude' => $data['latitude'],
                    'other_service_id' => $data['other_service_id'],
                    'description' => $data['description'],
                    'packages' => $data['packages'],
                    'environment_type' => $data['environment_type'],
                    'working_times' => $data['working_times'],
                    'members' => $data['members'],
                    'stream_urls' => $data['stream_urls'],
                    'band_type' => $data['band_type'],
                    'genre' => $data['genre'],
                    'contact_name' => $data['contact_name'],
                    'contact_number' => $data['contact_number'],
                    'contact_email' => $data['contact_email'],
                    'contact_link' => $data['contact_link'], // Valid JSON
                    'portfolio_link' => $data['portfolio_link'],
                    'portfolio_images' => $data['portfolio_images'], // Valid JSON
                    'services' => $data['services'], // Valid JSON
                ]
            );

            if ($service->wasRecentlyCreated) {
                $created++;
                $this->info("Created: {$data['name']}");
            } else {
                $updated++;
                $this->info("Updated: {$data['name']}");
            }
        }

        fclose($file);

        $this->info("Completed: Created $created records, Updated $updated records");
        return 0;
    }
}