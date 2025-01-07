<?php

namespace App\Console\Commands;

use App\Models\Venue;
use Illuminate\Console\Command;

class UpdateVenuesFromCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:venues-from-csv {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update venues from a CSV file';

    /**
     * Execute the console command.
     */
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
            $service = Venue::updateOrCreate(
                ['name' => $data['name']], // Search criteria
                [
                    'location' => $data['location'],
                    'postal_town' => $data['postal_town'],
                    'longitude' => $data['longitude'],
                    'latitude' => $data['latitude'],
                    'w3w' => $data['w3w'],
                    'capacity' => $data['capacity'],
                    'in_house_gear' => $data['in_house_gear'],
                    'band_type' => $data['band_type'],
                    'genre' => $data['genre'],
                    'contact_name' => $data['contact_name'],
                    'contact_number' => $data['contact_number'],
                    'contact_email' => $data['contact_email'],
                    'contact_link' => $data['contact_link'], // Valid JSON
                    'description' => $data['description'],
                    'additional_info' => $data['additional_info'],
                    'logo_url' => $data['logo_url'],
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