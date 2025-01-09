<?php

namespace App\Console\Commands;

use App\Mail\TestEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestEmail extends Command
{
    protected $signature = 'mail:test {email}';
    protected $description = 'Send a test email';

    public function handle()
    {
        $email = $this->argument('email');
        Mail::to($email)->send(new TestEmail());
        $this->info("Test email sent to {$email}");
    }
}