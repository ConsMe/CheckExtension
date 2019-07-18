<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\AdminHasChecker;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class SuperAdminController extends Controller
{
    public function getAdmins()
    {
        $users = $this->getAllUsers();
        return view('admins', compact(['users']));
    }

    public function getCheckers()
    {
        $checkers = $this->getAllChekers();
        return view('checkers', compact(['checkers']));
    }

    public function deleteAdmin(Request $request)
    {
        $request->validate([
            'id' => [
                'required',
                'numeric',
                Rule::exists('users')->where(function ($query) {
                    $query->where('role', 'admin');
                })
            ]
        ]);
        $user = User::where('id', $request->id)->first();
        $user->checkers()->delete();
        $user->delete();
        $users = $this->getAllUsers();
        return $users;
    }

    public function deleteChecker(Request $request)
    {
        $request->validate([
            'id' => [
                'required',
                'numeric',
                Rule::exists('users')->where(function ($query) {
                    $query->where('role', 'checker');
                })
            ]
        ]);
        $user = User::where('id', $request->id)->first();
        $user->logs()->delete();
        $user->checkertasks()->delete();
        $user->checkerrelations()->delete();
        $user->delete();
        return $this->getAllChekers();
    }

    public function stopChecker(Request $request)
    {
        $request->validate([
            'id' => [
                'required',
                'numeric',
                Rule::exists('users')->where(function ($query) {
                    $query->where('role', 'checker');
                })
            ]
        ]);
        $user = User::where('id', $request->id)->first();
        $user->checkertasks()->update(['isworking' => FALSE]);
        return $this->getAllChekers();
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'id' => [
                'required',
                'numeric',
                Rule::exists('users')->where(function ($query) {
                    $query->where('role', '!=', 'superadmin');
                }),
            ],
            'password' => ['required', 'string', 'min:8'],
        ]);
        $user = User::where('id', $request->id)->first();
        $user->update([
            'password' => Hash::make($request->password),
            'api_token' => str_random(60),
            'force_logout' => TRUE,
            'telegram_id' => NULL,
            'telegram_auth' => FALSE
        ]);
        if ($user->role == 'checker') {
            $user->checkertasks()->update(['isworking' => FALSE]);
        }
        return 'ok';
    }

    public function addChecker(Request $request)
    {
        $request->validate([
            'checker_id' => ['required', 'numeric', 'exists:users,id'],
            'admin_id' => ['required', 'numeric', 'exists:users,id'],
        ]);
        AdminHasChecker::firstOrCreate([
            'checker_id' => $request->checker_id,
            'admin_id' => $request->admin_id
        ]);
        return $this->getAllUsers();
    }

    public function removeChecker(Request $request)
    {
        $request->validate([
            'checker_id' => ['required', 'numeric'],
            'admin_id' => ['required', 'numeric'],
        ]);
        AdminHasChecker::where([
            'checker_id' => $request->checker_id,
            'admin_id' => $request->admin_id
        ])->delete();
        return $this->getAllUsers();
    }

    private function getAllUsers()
    {
        return User::whereIn('role', ['admin', 'checker'])->with('checkers.user:id,name')->get(['id', 'name', 'role'])->groupBy('role')->toArray();
    }

    private function getAllChekers()
    {
        return User::where('role', 'checker')->with('checkertasks:id,checker_id,url,isworking')->get(['id', 'name']);
    }
}
