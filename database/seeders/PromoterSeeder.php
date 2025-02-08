<?php

namespace Database\Seeders;

use App\Models\Promoter;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PromoterSeeder extends Seeder
{
    public function run(): void
    {
        $csvFile = fopen(base_path("database/data/promoters.csv"), "r");

        if ($csvFile === false) {
            throw new \Exception("Could not open the CSV file.");
        }

        // Skip the header row
        fgetcsv($csvFile, 2000, ",");

        while (($data = fgetcsv($csvFile, 4096, ",")) !== FALSE) {
            try {
                // Pre-process JSON fields with larger buffer
                $bandType = $this->processJsonField($data[9], '[]');
                $contactLink = $this->processJsonField($data[13], '{}');

                $promoter = [
                    "name" => $data[0],
                    "location" => $data[1],
                    "postal_town" => $data[2],
                    "latitude" => floatval($data[3]),
                    "longitude" => floatval($data[4]),
                    "logo_url" => $data[5],
                    "description" => $data[6] ?: "Description coming soon...",
                    "my_venues" => $data[7],
                    "genre" => '{}',
                    "band_type" => $bandType,
                    "contact_name" => $data[10] ?: 'General',
                    "contact_number" => $data[11],
                    "contact_email" => $data[12],
                    "contact_link" => $contactLink,
                ];

                Promoter::create($promoter);
            } catch (\Exception $e) {
                \Log::error('Failed to create promoter:', [
                    'name' => $data[0] ?? 'unknown',
                    'error' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
        fclose($csvFile);
    }

    private function processJsonField($value, $default)
    {
        if (empty($value)) {
            return $default;
        }

        try {
            // Handle double-encoded JSON and escaped quotes
            $cleaned = str_replace('""', '"', $value);
            $cleaned = preg_replace('/\\\\"/', '\"', $cleaned);

            // Validate JSON structure
            $decoded = json_decode($cleaned, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                return $cleaned;
            }
        } catch (\Exception $e) {
            \Log::warning('JSON processing failed:', [
                'value' => $value,
                'error' => $e->getMessage()
            ]);
        }

        return $default;
    }
}
