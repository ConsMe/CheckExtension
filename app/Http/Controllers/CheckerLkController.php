<?php

namespace App\Http\Controllers;

use App\AdminHasChecker;
use Illuminate\Http\Request;
use App\Checker;
use App\Events\NewMessageForChecker;
use App\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Telegram;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log as LaravelLog;
use Illuminate\Validation\ValidationException;

class CheckerLkController extends Controller
{
    public function lk(Request $request) {
        return view('lk', [
            'checkerName' => $request->user()->name,
            'checkers' => $this->getCheckerTasks($request->user()->id),
            'extensionVersion' => env('EXTENSION_VERSION'),
            'id' => auth()->user()->id,
        ]);
    }

    public function getToken(Request $request) {
        return $request->user()->api_token;
    }

    public function addTask(Request $request) {
        $request->validate([
            'url' => 'required|string|url',
            'search' => 'required|string',
            'interval' => 'required|integer|min:1|max:300',
            'delay' => 'required|integer|min:1|max:300',
            'checker_id' => [
                'integer',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', 'checker');
                }),
            ]
        ]);
        if (auth()->user()->role === 'admin') {
            $hasChecker = AdminHasChecker::where([
                'admin_id' => auth()->user()->id,
                'checker_id' => $request->checker_id
            ])->first();
            if ($hasChecker) {
                $tasksQuantity = Checker::where('checker_id', $request->checker_id)->count();
            }
            if (!$hasChecker || $tasksQuantity >= auth()->user()->max_allowed_checker_tasks) {
                $error = ValidationException::withMessages([
                    'id' => $hasChecker ? ['Лимит на количество задач исчерпан']
                        : ['У вас нет прав на управление задачами этого чекера. Перегрузите страницу.']
                ]);
                throw $error;
            }
            AdminHasChecker::create(['admin_id' => auth()->user()->id, 'checker_id' => $request->checker_id]);
        }
        $fields = $request->all();
        if (auth()->user()->role === 'checker') {
            $fields['checker_id'] = $request->user()->id;
        }
        $fields['started_at'] = $this->getNearestMinute($request->delay);
        Checker::create($fields);
        $checkerTasks = $this->getCheckerTasks($fields['checker_id']);
        if (auth()->user()->role !== 'checker') {
            event(new NewMessageForChecker($fields['checker_id'], $checkerTasks));
        }
        return $checkerTasks;
    }

    public function stopStart(Request $request) {
        $request->validate([
            'isworking' => 'required|boolean',
            'id' => [
                'required',
                'integer',
                'exists:checkers'
            ]
        ]);
        $checker = Checker::find($request->id);
        if (auth()->user()->role === 'checker') {
            $request->validate([
                'id' => [
                    Rule::exists('checkers')->where(function ($query) {
                        $query->where('checker_id', auth()->user()->id);
                    }),
                ]
            ]);
        } elseif (auth()->user()->role === 'admin') {
            $hasChecker = AdminHasChecker::where([
                'admin_id' => auth()->user()->id,
                'checker_id' => $checker->checker_id
            ])->first();
            if (!$hasChecker) {
                $error = ValidationException::withMessages([
                    'id' => ['У вас нет прав на управление задачами этого чекера. Перегрузите страницу.']
                ]);
                throw $error;
            }
        }
        $startedAt = $this->getNearestMinute($checker->delay);
        $checker->update(['isworking' => $request->isworking, 'started_at' => $startedAt]);
        $checkerTasks = $this->getCheckerTasks($checker->checker_id);
        if (auth()->user()->role !== 'checker') {
            event(new NewMessageForChecker($checker->checker_id, $checkerTasks));
        }
        return $checkerTasks;
    }

    public function getNearestMinute($delay = 1)
    {
        return (now()->second > 55) ? now()->addMinutes($delay + 1)->seconds(0)
            : now()->addMinutes($delay)->seconds(0);
    }

    public function deleteTask(Request $request) {
        $request->validate([
            'id' => [
                'required',
                'integer',
                'exists:checkers'
            ],
            'withLogs' => 'required|boolean'
        ]);
        $checker = Checker::find($request->id);
        $checkerId = $checker->checker_id;
        if (auth()->user()->role === 'checker') {
            $request->validate([
                'id' => [
                    Rule::exists('checkers')->where(function ($query) {
                        $query->where('checker_id', auth()->user()->id);
                    }),
                ]
            ]);
        } elseif (auth()->user()->role === 'admin') {
            $hasChecker = AdminHasChecker::where([
                'admin_id' => auth()->user()->id,
                'checker_id' => $checkerId
            ])->first();
            if (!$hasChecker) {
                $error = ValidationException::withMessages([
                    'id' => ['У вас нет прав на управление задачами этого чекера. Перегрузите страницу.']
                ]);
                throw $error;
            }
        }
        if ($request->withLogs) {
            $checker->logs()->delete();
            $checker->forceDelete();
        } else {
            $checker->delete();
        }
        $checkerTasks = $this->getCheckerTasks($checkerId);
        if (auth()->user()->role !== 'checker') {
            event(new NewMessageForChecker($checkerId, $checkerTasks));
        }
        return $checkerTasks;
    }

    private function getCheckerTasks($id) {
        return Checker::where('checker_id', $id)
            ->orderBy('id')
            ->get(['id', 'url', 'search', 'interval', 'isworking', 'started_at', 'delay']);
    }

    public function writeLog(Request $request)
    {
        $request->validate([
            'id' => [
                'required',
                'integer',
                Rule::exists('checkers')->where(function ($query) use ($request) {
                    $query->where('checker_id', auth()->user()->id);
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
        return $this->getCheckerTasks(auth()->user()->id);
    }

    public function download() {
        return Storage::download('ext.zip');
    }
}
