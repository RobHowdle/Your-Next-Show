<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class UpdateGeoIpDatabase extends Command
{
    protected $signature = 'geoip:update';
    protected $description = 'Update MaxMind GeoIP2 database';

    public function handle()
    {
        $this->info('Downloading GeoIP database...');

        $url = "https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City&license_key=" . config('services.maxmind.license_key') . "&suffix=tar.gz";

        $response = Http::get($url);

        if ($response->successful()) {
            $tempFile = storage_path('app/temp.tar.gz');
            file_put_contents($tempFile, $response->body());

            $this->info('Extracting database...');

            $phar = new \PharData($tempFile);
            $phar->extractTo(storage_path('app'));

            // Move the .mmdb file to the correct location
            $extracted = glob(storage_path('app/GeoLite2-City_*/GeoLite2-City.mmdb'));
            if (!empty($extracted)) {
                rename($extracted[0], storage_path('app/GeoLite2-City.mmdb'));
            }

            // Cleanup
            unlink($tempFile);
            array_map('unlink', glob(storage_path('app/GeoLite2-City_*/*.*')));
            array_map('rmdir', glob(storage_path('app/GeoLite2-City_*')));

            $this->info('GeoIP database updated successfully!');
        } else {
            $this->error('Failed to download GeoIP database');
        }
    }
}