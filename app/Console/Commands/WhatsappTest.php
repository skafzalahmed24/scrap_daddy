<?php

namespace App\Console\Commands;

use App\Services\WhatsAppService;
use Illuminate\Console\Command;

/** php artisan whatsapp:test 9876543210   -> sends a plain-text test message to that number */
class WhatsappTest extends Command
{
    protected $signature = 'whatsapp:test {number : phone number, with or without country code}';

    protected $description = 'Send a test WhatsApp message to check the ChatHub setup';

    public function handle(): int
    {
        if (!WhatsAppService::enabled()) {
            $this->error('WhatsApp is off. Set WHATSAPP_ENABLED=true and WHATSAPP_API_KEY in .env, then run: php artisan config:clear');
            return self::FAILURE;
        }

        $to = WhatsAppService::normalize($this->argument('number'));
        if (!$to) {
            $this->error('That is not a valid phone number.');
            return self::FAILURE;
        }

        $this->info("Sending to {$to} ...");

        if (WhatsAppService::sendText($to, "*123456* is your Scrap Daddy verification code.\nIt is valid for 10 minutes. Please do not share it with anyone.")) {
            $this->info('Accepted by ChatHub. Check the phone (and storage/logs/laravel.log if nothing arrives).');
            return self::SUCCESS;
        }

        $this->error('ChatHub did not accept it. See storage/logs/laravel.log (look for "WhatsApp"). Common causes: wrong API key, or the person has not chatted with your business number in the last 24 hours.');
        return self::FAILURE;
    }
}
