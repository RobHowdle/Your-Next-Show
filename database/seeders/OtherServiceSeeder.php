<?php

namespace Database\Seeders;

use App\Models\OtherService;
use Illuminate\Database\Seeder;

class OtherServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = fopen(base_path("database/data/other_services.csv"), "r");

        if ($csvFile === false) {
            throw new \Exception("Could not open the CSV file.");
        }

        $firstLine = true;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
            if ($firstLine) {
                $firstLine = false;
                continue;
            }

            try {
                $otherService = [
                    'name' => $data[0] ?? null,
                    'logo_url' => $data[1] ?? null,
                    'location' => $data[2] ?? null,
                    'postal_town' => $data[3] ?? null,
                    'longitude' => $data[4] ?? null,
                    'latitude' => $data[5] ?? null,
                    'other_service_id' => $data[6] ?? null,
                    'description' => $data[7] ?? "Description coming soon...",
                    'packages' => $data[8] ?? '[]',
                    'environment_type' => $data[9] ?? '[]',
                    'working_times' => $data[10] ?? '[]',
                    'members' => $data[11] ?? '[]',
                    'stream_urls' => is_string($data[12]) ? $data[12] : '{}',
                    'band_type' => is_string($data[13]) ? $data[13] : '[]',
                    'genre' => is_string($data[14]) ? $data[14] : '{}',
                    'contact_name' => $data[15] ?? 'General',
                    'contact_number' => $data[16] ?? null,
                    'contact_email' => $data[17] ?? null,
                    'contact_link' => is_string($data[18]) ? $data[18] : '{}',
                    'portfolio_link' => $data[19] ?? null,
                    'portfolio_images' => $data[20] ?? '[]',
                    'services' => $data[21] ?? null,
                ];

                // Validate JSON fields
                foreach (['packages', 'environment_type', 'working_times', 'members', 'stream_urls', 'band_type', 'genre', 'contact_link', 'portfolio_images'] as $field) {
                    if (!$this->isValidJson($otherService[$field])) {
                        $otherService[$field] = '{}';
                    }
                }

                OtherService::create($otherService);
            } catch (\Exception $e) {
                \Log::error('Failed to create other service:', [
                    'name' => $data[0] ?? 'unknown',
                    'error' => $e->getMessage(),
                    'data' => $data
                ]);
            }
        }
        fclose($csvFile);
    }

    private function isValidJson($string)
    {
        if (!is_string($string)) return false;
        json_decode($string);
        return (json_last_error() === JSON_ERROR_NONE);
    }
}
