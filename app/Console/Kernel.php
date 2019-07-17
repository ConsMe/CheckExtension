<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Checker;
use App\Log;
use Illuminate\Support\Carbon;

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
            $checkers = Checker::where('isworking', TRUE)->with(['logs' => function($query) {
                $query->where('status', '>=', 0)->orderBy('created_at', 'DESC');
            }])->get();
            foreach ($checkers as $checker) {
                $lastLog = $checker->logs->first();
                if ($lastLog) {
                    if ($lastLog->created_at->addMinutes($checker->interval + 1) > now()) continue;
                }
                Log::create([
                    'checker_id' => $checker->id,
                    'status' => -1
                ]);
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
