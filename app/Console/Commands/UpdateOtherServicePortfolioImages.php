<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isNull;

class UpdateOtherServicePortfolioImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'other-services:update-records {--dry-run : Preview the changes without updating the database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

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
            ->where('genre', 'like', '%[]%')
            ->orWhere('portfolio_images', 'like', '%[]%')
            ->get(['id', 'genre', 'portfolio_images']); // Retrieve ID and portfolio_images for preview

        if ($otherServices->isEmpty()) {
            $this->info('No records found with portfolio images set to "[]".');
            return Command::SUCCESS;
        }

        // Display the rows to be updated
        $this->info("Found {$otherServices->count()} rows to update:");
        foreach ($otherServices as $otherService) {
            $this->line(" - OtherService ID: {$otherService->id}, Genres: {$otherService->genre}, Portfolio Images: {$otherService->portfolio_images}");
        }

        // If dry-run is enabled, do not update
        if ($this->option('dry-run')) {
            $this->info('Dry run mode: No changes were made.');
            return Command::SUCCESS;
        }

        $emptyRows = DB::table('other_services')
            ->whereNull('genre')
            ->orWhereNull('portfolio_images')
            ->orWhereNull('portfolio_link')
            ->get();
        if ($emptyRows->count() > 0) {
            $this->info('Found ' . $emptyRows->count() . ' rows with NULL values');

            DB::table('other_services')
                ->whereNull('genre')
                ->orWhereNull('portfolio_images')
                ->orWhereNull('portfolio_link')

                ->update(['genre' => [], 'portfolio_images' => [], 'portfolio_link' => 'https://www.yournextshow.co.uk']);

            $this->info('Successfully updated rows to empty arrays');
            return Command::SUCCESS;
        }

        $this->info('No NULL portfolio_images found');
        return Command::SUCCESS;

        // Perform the update
        $updatedRows = DB::table('other_services')
            ->where('genre', 'like', '%[]%')
            ->orWhere('portfolio_images', 'like', '%[]%')
            ->update(['genre' => [], 'portfolio_images' => []]);

        $this->info("Successfully updated {$updatedRows} rows.");
        return Command::SUCCESS;
    }
}