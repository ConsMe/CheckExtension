<?php

namespace App\Http\Controllers;

use App\AdminHasChecker;
use Illuminate\Http\Request;
use App\Checker;
use App\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;

class CheckerController extends Controller
{
    public function getChecker($id)
    {
        Validator::make(['id' => $id], [
            'id' => [
                'required',
                'integer',
                Rule::exists('users')->where(function ($query) {
                    $query->where('role', 'checker');
                }),
            ]
        ])->validate();
        if (auth()->user()->role === 'admin') {
            if (!AdminHasChecker::where(['admin_id' => auth()->user()->id, 'checker_id' => $id])->first()) {
                return \redirect()->back();
            }
        }
        $checkerName = User::find($id)->name;
        $checkers = Checker::where('checker_id', $id)
            ->orderBy('id')
            ->get(['id', 'url', 'search', 'interval', 'isworking', 'started_at', 'delay']);
        $checkerId = $id;

        return view('checker', compact(['checkerName', 'checkers', 'checkerId']));
    }
}
