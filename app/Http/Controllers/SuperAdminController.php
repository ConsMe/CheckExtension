<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function getAdmins()
    {
        return view('admins');
    }

    public function getCheckers()
    {
        return view('checkers');
    }
}
