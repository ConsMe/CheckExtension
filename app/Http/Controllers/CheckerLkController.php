<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Checker;
use App\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Telegram;
// use Illuminate\Support\Facades\Log as LaravelLog;

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
        $fields = $request->all();
        $fields['checker_id'] = $request->user()->id;
        Checker::where('id', $request->id)->update(['isworking' => $request->isworking]);
        return $this->getMyCheckers($request);
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
        return Checker::where('checker_id', $request->user()->id)->orderBy('id')->get(['id', 'url', 'search', 'interval', 'isworking']);
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
            'code' => 'required|in:0,1'
        ]);
        $checker = Checker::where('id', $request->id)->first();
        if ($checker->isworking) {
            Log::create([
                'checker_id' => $request->id,
                'status' => $request->code
            ]);
            if ($request->code == 0) {
                $checker->load([
                    'user' => function($query) {
                        $query->with('admins');
                    }
                ]);
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
        return $this->getMyCheckers($request);
    }

    public function download() {
        return Storage::download('ext.zip');
    }
}
