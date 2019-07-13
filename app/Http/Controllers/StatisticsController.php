<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function getStatistics()
    {
        return view('statistics');
    }
}
