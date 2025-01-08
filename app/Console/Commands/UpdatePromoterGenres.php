<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdatePromoterGenres extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'promoters:update-genres {--dry-run : Preview the changes without updating the database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert all genres in the promoters table from "[]" (string) to an empty JSON array ([])';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Checking promoters genres...');

        // Select rows where genres is the string "[]"
        $promoters = DB::table('promoters')
            ->where('genre', 'like', '%[]%')
            ->get(['id', 'genre']); // Retrieve ID and genres for preview

        if ($promoters->isEmpty()) {
            $this->info('No records found with genres set to "[]".');
            return Command::SUCCESS;
        }

        // Display the rows to be updated
        $this->info("Found {$promoters->count()} rows to update:");
        foreach ($promoters as $promoter) {
            $this->line(" - Promoter ID: {$promoter->id}, Genres: {$promoter->genre}");
        }

        // If dry-run is enabled, do not update
        if ($this->option('dry-run')) {
            $this->info('Dry run mode: No changes were made.');
            return Command::SUCCESS;
        }

        // Perform the update
        $updatedRows = DB::table('promoters')
            ->where('genre', 'like', '%[]%')
            ->update(['genre' => json_encode([])]);

        $this->info("Successfully updated {$updatedRows} rows.");
        return Command::SUCCESS;
    }
}
