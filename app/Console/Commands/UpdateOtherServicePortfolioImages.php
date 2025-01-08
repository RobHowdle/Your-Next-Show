<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;


class UpdateOtherServicePortfolioImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'other-services:update-images {--dry-run : Preview the changes without updating the database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert all portfolio images in the other services table from "[]" (string) to an empty JSON array ([])';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Checking other services portfolio images...');

        // Select rows where genres is the string "[]"
        $otherServices = DB::table('other_services')
            ->where('portfolio_images', 'like', '%[]%')
            ->get(['id', 'portfolio_images']); // Retrieve ID and portfolio_images for preview

        if ($otherServices->isEmpty()) {
            $this->info('No records found with portfolio images set to "[]".');
            return Command::SUCCESS;
        }

        // Display the rows to be updated
        $this->info("Found {$otherServices->count()} rows to update:");
        foreach ($otherServices as $otherService) {
            $this->line(" - OtherService ID: {$otherService->id}, Genres: {$otherService->portfolio_images}");
        }

        // If dry-run is enabled, do not update
        if ($this->option('dry-run')) {
            $this->info('Dry run mode: No changes were made.');
            return Command::SUCCESS;
        }

        // Perform the update
        $updatedRows = DB::table('other_services')
            ->where('portfolio_images', 'like', '%[]%')
            ->update(['portfolio_images' => []]);

        $this->info("Successfully updated {$updatedRows} rows.");
        return Command::SUCCESS;
    }
}