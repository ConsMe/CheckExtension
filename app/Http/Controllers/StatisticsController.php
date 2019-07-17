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
            'dateFrom' => 'date_format:d.m.y',
            'dateTo' => 'date_format:d.m.y',
            'checker' => 'string|exists:users,id'
        ]);
        if ($request->user()->role == 'superadmin') {
            $users = User::where('role', 'checker');
        } else {
            $users = $request->user()->checkers()->get()->pluck(['checker_id']);
            $users = User::whereIn('id', $users);
        }
        $listCheckers = $users->get()->pluck('name', 'id');
        if($request->checker) {
            $users = $users->where('id', $request->checker);
        }
        $users = $users->with('checkertasks')->get();
        $checkers = $users->pluck('checkertasks')->flatten()->pluck('id');
        $data = Log::whereIn('checker_id', $checkers);
        if ($request->dateFrom) {
            $data = $data->where('created_at', '>', Carbon::createFromFormat('d.m.y H:i', $request->dateFrom.' 00:00'));
        }
        if ($request->dateTo) {
            $data = $data->where('created_at', '<', Carbon::createFromFormat('d.m.y H:i', $request->dateTo.' 23:59'));
        }
        $data = $data->with(['checker' => function($query) {
            $query->select(['id', 'checker_id', 'url']);
        }, 'checker.user' => function($query) {
            $query->select(['id', 'name']);
        }])->orderBy('created_at', 'DESC')->paginate(100)->toArray();
        $data['checkers'] = $listCheckers;
        return $data;
    }
}
