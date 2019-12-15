<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Checker;
use App\User;
use App\Log;
use Illuminate\Support\Carbon;

class StatisticsController extends Controller
{
    public function getStatistics(Request $request)
    {
        $data = $this->getData($request);
        return view('statistics', compact(['data']));
    }

    public function getData(Request $request)
    {
        $request->validate([
            'dateFrom' => 'date_format:d.m.y H:i',
            'dateTo' => 'date_format:d.m.y H:i',
            'checker' => 'string|exists:users,id'
        ]);
        if ($request->user()->role == 'superadmin') {
            $users = User::withTrashed()->where('role', 'checker');
        } else {
            $users = $request->user()->checkers()->withTrashed()->get()->pluck(['checker_id']);
            $users = User::withTrashed()->whereIn('id', $users);
        }
        $listCheckers = $users->get()->pluck('name', 'id');
        // dd($listCheckers);
        if ($request->checker) {
            $users = $users->where('id', $request->checker);
        }
        $users = $users->with(['checkertasks' => function ($query) {
            $query->withTrashed();
        }])->get();
        $checkers = $users->pluck('checkertasks')->flatten()->pluck('id');
        $data = Log::whereIn('checker_id', $checkers);
        if ($request->dateFrom) {
            $data = $data->where('check_time', '>', Carbon::createFromFormat('d.m.y H:i', $request->dateFrom));
        }
        if ($request->dateTo) {
            $data = $data->where('check_time', '<', Carbon::createFromFormat('d.m.y H:i', $request->dateTo));
        }
        $data = $data->with(['checker' => function ($query) {
            $query->withTrashed()->select(['id', 'checker_id', 'url']);
        }, 'checker.user' => function ($query) {
            $query->withTrashed()->select(['id', 'name']);
        }])->orderBy('check_time', 'DESC')->paginate(100)->toArray();
        $data['checkers'] = $listCheckers;
        return $data;
    }
}
