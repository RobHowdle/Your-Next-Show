<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class PasswordCheckService
{
    protected $apiUrl = 'https://api.pwnedpasswords.com/range/';

    public function isCompromised(string $password): bool
    {
        try {
            // Hash the password using SHA-1
            $hashedPassword = strtoupper(sha1($password));
            $prefix = substr($hashedPassword, 0, 5); // Get the first 5 characters
            $suffix = substr($hashedPassword, 5); // Get the remaining characters

            // Make a request to the HIBP API
            $response = Http::get($this->apiUrl . $prefix);

            // Check if the password is in the response
            if ($response->successful()) {
                $lines = explode("\n", $response->body());
                foreach ($lines as $line) {
                    list($hashSuffix, $count) = explode(':', $line);
                    if ($hashSuffix === $suffix) {
                        return true; // Password has been compromised
                    }
                }
            }
            return false; // Password is safe
        } catch (\Exception $e) {
            Log::error('Password check failed: ' . $e->getMessage());
            return false; // Assume safe if there's an error
        }
    }
}