<?php

namespace App\Console\Commands;

use App\Models\OtherService;
use App\Models\Artist;
use App\Models\Venue;
use App\Models\Promoter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class NormalizeGenreData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:normalize-genres {--model=all} {--id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Normalize genre data to ensure proper JSON format';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $modelType = $this->option('model');
        $specificId = $this->option('id');
        $isVerbose = $this->option('verbose');

        $emptyGenreStructure = json_encode([]);
        $defaultGenreStructure = json_encode([
            "Rock" => [
                "all" => false,
                "subgenres" => []
            ]
        ]);

        $successCount = 0;
        $errorCount = 0;

        $this->info('Starting genre data normalization...');

        // Get genre list for validation
        $genreList = [];
        try {
            $genreData = json_decode(file_get_contents(public_path('text/genre_list.json')), true);
            if (isset($genreData['genres'])) {
                $genreList = $genreData['genres'];
            }
        } catch (\Exception $e) {
            $this->warn('Could not load genre list from file: ' . $e->getMessage());
        }

        // Process OtherService models (Artists, Photographers, etc.)
        if ($modelType === 'all' || $modelType === 'otherservice') {
            $query = OtherService::query();

            if ($specificId) {
                $query->where('id', $specificId);
            }

            $services = $query->get();
            $this->info("Processing {$services->count()} OtherService records...");

            $bar = $this->output->createProgressBar($services->count());
            $bar->start();

            foreach ($services as $service) {
                try {
                    $this->normalizeGenreData($service, $defaultGenreStructure, $emptyGenreStructure, $genreList);
                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    if ($isVerbose) {
                        $this->newLine();
                        $this->error("Error processing OtherService ID {$service->id}: {$e->getMessage()}");
                    }
                    Log::error("Genre normalization error for OtherService ID {$service->id}: {$e->getMessage()}");
                }
                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);
        }

        // Process Venue models
        if ($modelType === 'all' || $modelType === 'venue') {
            $query = Venue::query();

            if ($specificId) {
                $query->where('id', $specificId);
            }

            $venues = $query->get();
            $this->info("Processing {$venues->count()} Venue records...");

            $bar = $this->output->createProgressBar($venues->count());
            $bar->start();

            foreach ($venues as $venue) {
                try {
                    $this->normalizeGenreData($venue, $defaultGenreStructure, $emptyGenreStructure, $genreList);
                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    if ($isVerbose) {
                        $this->newLine();
                        $this->error("Error processing Venue ID {$venue->id}: {$e->getMessage()}");
                    }
                    Log::error("Genre normalization error for Venue ID {$venue->id}: {$e->getMessage()}");
                }
                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);
        }

        // Process Promoter models
        if ($modelType === 'all' || $modelType === 'promoter') {
            $query = Promoter::query();

            if ($specificId) {
                $query->where('id', $specificId);
            }

            $promoters = $query->get();
            $this->info("Processing {$promoters->count()} Promoter records...");

            $bar = $this->output->createProgressBar($promoters->count());
            $bar->start();

            foreach ($promoters as $promoter) {
                try {
                    $this->normalizeGenreData($promoter, $defaultGenreStructure, $emptyGenreStructure, $genreList);
                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    if ($isVerbose) {
                        $this->newLine();
                        $this->error("Error processing Promoter ID {$promoter->id}: {$e->getMessage()}");
                    }
                    Log::error("Genre normalization error for Promoter ID {$promoter->id}: {$e->getMessage()}");
                }
                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);
        }

        $this->info("Genre normalization completed:");
        $this->info("Success: {$successCount} records normalized");
        if ($errorCount > 0) {
            $this->warn("Errors: {$errorCount} records failed");
        }

        return 0;
    }

    /**
     * Normalize the genre data for a given model
     * 
     * @param mixed $model The model instance to update
     * @param string $defaultStructure Default JSON structure for normal genres
     * @param string $emptyStructure Empty JSON structure
     * @param array $validGenres List of valid genres
     * @return void
     */
    private function normalizeGenreData($model, $defaultStructure, $emptyStructure, array $validGenres)
    {
        $currentGenre = $model->genre;

        // If it's already a properly formatted array, leave it alone
        if (is_array($currentGenre) && $this->isValidGenreFormat($currentGenre)) {
            return; // Already good
        }

        // If it's a string that can be decoded to a valid array, use that
        if (is_string($currentGenre)) {
            try {
                $decoded = json_decode($currentGenre, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && $this->isValidGenreFormat($decoded)) {
                    return; // Already good
                }
            } catch (\Exception $e) {
                // Will be handled by the default case
            }
        }

        // For everything else, use empty or default structure
        try {
            if (empty($currentGenre) || $currentGenre === 'null' || $currentGenre === '[]') {
                $model->genre = $emptyStructure; // Use the parameter passed to this method
            } else {
                $model->genre = $defaultStructure; // Use the parameter passed to this method
            }

            $model->save();
        } catch (\Exception $e) {
            Log::error("Error saving model: " . $e->getMessage(), [
                'model_id' => $model->id,
                'model_type' => get_class($model)
            ]);
            throw $e; // Re-throw so the caller can handle it
        }
    }

    /**
     * Check if the genre data has the expected structure
     * 
     * @param array $data Genre data to check
     * @return bool Whether the structure is valid
     */
    private function isValidGenreFormat(array $data): bool
    {
        if (empty($data)) return true; // Empty array is valid

        foreach ($data as $genreName => $genreData) {
            // Each genre should be an array with 'all' and 'subgenres' keys
            if (!is_array($genreData)) {
                return false;
            }

            // Check for required keys
            if (!isset($genreData['all']) || !isset($genreData['subgenres'])) {
                return false;
            }

            // 'all' should be boolean or string boolean
            if (!is_bool($genreData['all']) && $genreData['all'] !== 'true' && $genreData['all'] !== 'false') {
                return false;
            }

            // 'subgenres' should be an array
            if (!is_array($genreData['subgenres'])) {
                return false;
            }
        }

        return true;
    }
}