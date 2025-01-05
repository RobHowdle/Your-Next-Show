<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RemoveDupServiceUserRecords extends Command
{
    protected $signature = 'users:remove-dup-service-user-records';
    protected $description = 'Remove duplicate StandardUser entries where OtherService exists';

    public function handle()
    {
        $duplicates = DB::table('service_user as su1')
            ->join('service_user as su2', 'su1.user_id', '=', 'su2.user_id')
            ->where('su1.serviceable_type', 'App\\Models\\StandardUser')
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('su2.serviceable_type', 'App\\Models\\OtherService')
                        ->where('su2.role', 'standard');
                })->orWhereIn('su2.serviceable_type', [
                    'App\\Models\\Venue',
                    'App\\Models\\Promoter'
                ]);
            })
            ->select('su1.id', 'su1.user_id', 'su2.serviceable_type')
            ->get();

        $this->info('Found records: ' . $duplicates->count());

        foreach ($duplicates as $record) {
            $this->line("Found: User ID {$record->user_id} - Type: {$record->serviceable_type}");
        }

        if (!$duplicates->isEmpty() && $this->confirm('Delete these records?')) {
            DB::beginTransaction();
            try {
                foreach ($duplicates as $record) {
                    DB::table('service_user')->where('id', $record->id)->delete();
                    $this->info("Deleted record for user {$record->user_id}");
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error($e->getMessage());
            }
        }
    }
}