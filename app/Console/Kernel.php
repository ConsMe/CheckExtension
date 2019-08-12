<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Checker;
use App\Log;
use Illuminate\Support\Carbon;
use Telegram;
use Illuminate\Support\Facades\Log as LaravelLog;

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
                'user' => function($query) {
                    $query->with('admins');
                }
            ])->get();
            $checkTime = now()->addMinutes(-1)->milliseconds(0)->seconds(0);
            foreach ($checkers as $checker) {
                $errosCount = $checker->user->max_uncompleted_errors;
                $lastLogs = $checker->logs()->orderBy('check_time', 'DESC')->limit($errosCount)->get();
                $lastLog = $lastLogs->first();
                if ($lastLog && $lastLog->check_time == $checkTime) continue;
                if ($checkTime < $checker->started_at) continue;
                if ($checkTime->diffInMinutes($checker->started_at) % $checker->interval > 0) continue;
                $newlog = Log::create([
                    'checker_id' => $checker->id,
                    'status' => -1,
                    'check_time' => $checkTime
                ]);
                $sameErrorsCount = $lastLogs->slice(0,-1)->prepend($newlog)->filter(function ($log, $key) {
                    return $log->status === -1;
                })->count();
                if ($errosCount != $sameErrorsCount) continue;
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
