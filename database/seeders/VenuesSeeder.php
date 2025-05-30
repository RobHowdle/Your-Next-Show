<?php

namespace Database\Seeders;

use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class VenuesSeeder extends Seeder
{
    public function run(): void
    {
        $csvFile = fopen(base_path("database/data/venues.csv"), "r");

        if ($csvFile === false) {
            throw new \Exception("Could not open the CSV file.");
        }

        // Skip the header row
        fgetcsv($csvFile, 2000, ",");

        while (($data = fgetcsv($csvFile, 4096, ",")) !== FALSE) {
            try {
                // Pre-process JSON fields with larger buffer
                $bandType = $this->processJsonField($data[8], '[]');
                $contactLink = $this->processJsonField($data[13], '{}');

                $venue = [
                    "name" => $data[0],
                    "location" => $data[1],
                    "postal_town" => $data[2],
                    "longitude" => floatval($data[3]),
                    "latitude" => floatval($data[4]),
                    'w3w' => $data[5],
                    "capacity" => intval($data[6] ?? 0),
                    "in_house_gear" => $data[7],
                    "band_type" => $bandType,
                    "genre" => '{}', // Set to empty JSON object
                    "contact_name" => $data[10] ?: 'General',
                    "contact_number" => $data[11],
                    "contact_email" => $data[12],
                    "contact_link" => $contactLink,
                    "description" => $data[14] ?: "Description coming soon...",
                    "additional_info" => $data[15],
                    "logo_url" => $data[16],
                ];

                Venue::create($venue);
            } catch (\Exception $e) {
                \Log::error('Failed to create venue:', [
                    'name' => $data[0] ?? 'unknown',
                    'error' => $e->getMessage()
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
