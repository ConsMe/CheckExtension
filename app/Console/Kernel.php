<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Checker;
use App\Log;
use Illuminate\Support\Carbon;
use Telegram;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $checkers = Checker::where('isworking', TRUE)->with([
                'logs' => function($query) {
                    $query->where('status', '>=', 0)->orderBy('created_at', 'DESC');
                },
                'user' => function($query) {
                    $query->with('admins');
                }
            ])->get();
            foreach ($checkers as $checker) {
                $lastLog = $checker->logs->first();
                if ($lastLog) {
                    $difference = $lastLog->created_at->addMinutes($checker->interval)->diffInMinutes(now(), false);
                    if ($difference <= 0) continue;
                    if ($checker->interval > 1 && $difference % $checker->interval != 1) continue;
                }
                Log::create([
                    'checker_id' => $checker->id,
                    'status' => -1
                ]);
                $errorText = "CHECK UNCOMPLETED \nURL: ".$checker->url;
                $checkerTelegramId = ($checker->user->telegram_auth) ? $checker->user->telegram_id : NULL;
                if ($checkerTelegramId) {
                    Telegram::sendMessage([
                        'chat_id' => $checkerTelegramId,
                        'text' => $errorText
                    ]);
                }
                $errorText = "Чекер ".$checker->user->name."\n".$errorText;
                foreach ($checker->user->admins as $admin) {
                    if (!$admin->telegram_auth) continue;
                    Telegram::sendMessage([
                        'chat_id' => $admin->telegram_id,
                        'text' => $errorText
                    ]);
                }
            }
        })->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
