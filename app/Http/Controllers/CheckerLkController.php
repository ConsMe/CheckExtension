<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Checker;
use App\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Telegram;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log as LaravelLog;

class CheckerLkController extends Controller
{
    public function lk(Request $request) {
        return view('lk', [
            'checkerName' => $request->user()->name,
            'checkers' => $this->getMyCheckers($request),
            'extensionVersion' => env('EXTENSION_VERSION')
        ]);
    }

    public function getToken(Request $request) {
        return $request->user()->api_token;
    }

    public function addTask(Request $request) {
        $request->validate([
            'url' => 'required|string|url',
            'search' => 'required|string',
            'interval' => 'required|numeric|min:1|max:300'
        ]);
        $fields = $request->all();
        $fields['checker_id'] = $request->user()->id;
        $fields['started_at'] = $this->getNearestMinute();
        Checker::create($fields);
        return $this->getMyCheckers($request);
    }

    public function stopStart(Request $request) {
        $request->validate([
            'isworking' => 'required|boolean',
            'id' => [
                'required',
                'integer',
                Rule::exists('checkers')->where(function ($query) use ($request) {
                    $query->where('checker_id', $request->user()->id);
                }),
            ]
        ]);
        Checker::where('id', $request->id)->update(['isworking' => $request->isworking, 'started_at' => $this->getNearestMinute()]);
        return $this->getMyCheckers($request);
    }

    public function getNearestMinute()
    {
        return (now()->second > 55) ? now()->addMinutes(2)->seconds(0) : now()->addMinutes(1)->seconds(0);
    }

    public function deleteTask(Request $request) {
        $request->validate([
            'id' => [
                'required',
                'integer',
                Rule::exists('checkers')->where(function ($query) use ($request) {
                    $query->where('checker_id', $request->user()->id);
                }),
            ]
        ]);
        $checker = Checker::where('id', $request->id)->first();
        $checker->logs()->delete();
        $checker->delete();
        return $this->getMyCheckers($request);
    }

    private function getMyCheckers($request) {
        return Checker::where('checker_id', $request->user()->id)->orderBy('id')->get(['id', 'url', 'search', 'interval', 'isworking', 'started_at']);
    }

    public function writeLog(Request $request)
    {
        $request->validate([
            'id' => [
                'required',
                'integer',
                Rule::exists('checkers')->where(function ($query) use ($request) {
                    $query->where('checker_id', $request->user()->id);
                }),
            ],
            'code' => 'required|in:0,1',
            'datetime' => 'required|integer'
        ]);
        $checker = Checker::where('id', $request->id)->with([
            'logs' => function($query) {
                $query->orderBy('check_time', 'DESC')->limit(10);
            }
        ])->first();
        $lastLog = $checker->logs->first();
        $checkerTime = Carbon::createFromTimestamp($request->datetime/1000);
        $now = now()->milliseconds(0)->seconds(0);
        if ($checker->isworking && $checkerTime == $now && $lastLog != $now) {
            Log::create([
                'checker_id' => $request->id,
                'status' => $request->code,
                'check_time' => $now
            ]);
            if ($request->code == 0) {
                $checker->load([
                    'user' => function($query) {
                        $query->with('admins');
                    }
                ]);
                $errosCount = $checker->user->max_undetected_errors;
                $sameErrorsCount = Log::where(['checker_id' => $checker->id])->orderBy('check_time', 'DESC')->limit($errosCount)->get()->filter(function ($log, $key) {
                    return $log->status === 0;
                })->count();
                if ($sameErrorsCount == $errosCount) {
                    $errorText = "ELEMENT UNDETECTED \nURL: ".$checker->url;
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
            }
        }
        return $this->getMyCheckers($request);
    }

    public function download() {
        return Storage::download('ext.zip');
    }
}

