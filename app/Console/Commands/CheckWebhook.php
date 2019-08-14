<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use  GuzzleHttp\Client;
use Telegram;

class CheckWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:webhook';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if telegram bot has webhook and set it up if does not have';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $url = 'https://api.telegram.org/bot'.env('TELEGRAM_BOT_TOKEN').'/';
        $client = new Client();
        $response = $client->get($url.'getWebhookInfo');
        if ($response->getStatusCode() != 200) return;
        $body = json_decode($response->getBody(), TRUE);
        $this->info($body);
        if (!isset($body['ok']) || !$body['ok'] || !isset($body['result']['url']) || strlen(isset($body['result']['url']))) {
            $this->info('Webhook is already setted up');
            return;
        }
        Telegram::setWebhook(['url' => env('APP_URL').'/'.env('TELEGRAM_BOT_TOKEN').'/webhook']);
    }
}
