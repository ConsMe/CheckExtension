<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\AdminHasChecker;
use App\Events\CheckerForceDisconnect;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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
        if (auth()->user()->role === 'superadmin' || auth()->user()->can_add_edit_checkers) {
            $canAddEditCheckers = 1;
        } else {
            $canAddEditCheckers = 0;
        }
        return view('checkers', compact(['checkers', 'canAddEditCheckers']));
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
            ],
            'withLogs' => 'required|boolean'
        ]);
        if (auth()->user()->role === 'admin') {
            if (!AdminHasChecker::where(['admin_id' => auth()->user()->id, 'checker_id' => $request->id])->first()) {
                $error = ValidationException::withMessages([
                    'id' => ['У вас нет прав на удаление этого чекера. Перегрузите страницу.']
                ]);
                throw $error;
            }
        }
        $user = User::where('id', $request->id)->first();
        if ($request->withLogs) {
            $user->logs()->delete();
            $user->checkertasks()->withTrashed()->forceDelete();
            $user->checkerrelations()->withTrashed()->forceDelete();
            $user->forceDelete();
        } else {
            $user->checkertasks()->delete();
            $user->checkerrelations()->delete();
            $user->delete();
        }
        event(new CheckerForceDisconnect($request->id));
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
        if (auth()->user()->role === 'admin') {
            $condition = ['admin_id' => auth()->user()->id, 'checker_id' => $request->id];
            if ($user->role !== 'checker' || !AdminHasChecker::where($condition)->first()) {
                $error = ValidationException::withMessages([
                    'id' => ['У вас нет прав на выполнение этого действия. Перегрузите страницу.']
                ]);
                throw $error;
            }
        }
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
        $isRelation =  AdminHasChecker::withTrashed()->where([
            'checker_id' => $request->checker_id,
            'admin_id' => $request->admin_id
        ])->first();
        if (!$isRelation) {
            AdminHasChecker::create([
                'checker_id' => $request->checker_id,
                'admin_id' => $request->admin_id
            ]);
        } else {
            $isRelation->update(['deleted_at' => null]);
        }
        return $this->getAllUsers();
    }

    public function removeChecker(Request $request)
    {
        $request->validate([
            'checker_id' => ['required', 'numeric'],
            'admin_id' => ['required', 'numeric'],
        ]);
        AdminHasChecker::withTrashed()->where([
            'checker_id' => $request->checker_id,
            'admin_id' => $request->admin_id
        ])->forceDelete();
        return $this->getAllUsers();
    }

    public function changeErrorsCount(Request $request)
    {
        $request->validate([
            'id' => [
                'required',
                'numeric',
                Rule::exists('users')->where(function ($query) {
                    $query->where('role', 'checker');
                }),
            ],
            'parameter' => ['required', 'in:max_undetected_errors,max_uncompleted_errors'],
            'value' => ['required', 'numeric', 'between:1,10'],
        ]);
        if (auth()->user()->role === 'admin') {
            if (!AdminHasChecker::where(['admin_id' => auth()->user()->id, 'checker_id' => $request->id])->first()) {
                $error = ValidationException::withMessages([
                    'id' => ['У вас нет прав на редактирование этого чекера. Перегрузите страницу.']
                ]);
                throw $error;
            }
        }
        User::where('id', $request->id)->update([$request->parameter => $request->value]);
        return $this->getAllChekers();
    }

    private function getAllUsers()
    {
        return User::whereIn('role', ['admin', 'checker'])
            ->with('checkers.user:id,name')
            ->get([
                'id', 'name', 'role', 'has_access_to_checkers', 'can_add_edit_checkers', 'max_allowed_checker_tasks'
            ])
            ->groupBy('role')->toArray();
    }

    private function getAllChekers()
    {
        $checkers = User::where('role', 'checker')
            ->with('checkertasks:id,checker_id,url,isworking');
        if (auth()->user()->role === 'admin') {
            $checkers->whereIn('id', auth()->user()->checkers()->pluck('checker_id'));
        }
        return $checkers->get(['id', 'name', 'max_uncompleted_errors', 'max_undetected_errors']);
    }

    public function toggleCheckersAccess(Request $request)
    {
        $data = $request->validate([
            'admin_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', 'admin');
                }),
            ],
            'has_access_to_checkers' => 'required_without:can_add_edit_checkers|boolean',
            'can_add_edit_checkers' => 'required_without:has_access_to_checkers|boolean',
        ]);
        unset($data['admin_id']);
        $user = User::find($request->admin_id);
        if ($request->has('has_access_to_checkers') && !$data['has_access_to_checkers']) {
            $data['can_add_edit_checkers'] = false;
        }
        if ($request->has_access_to_checkers) {
            $data['max_allowed_checker_tasks'] = 1;
        }
        $user->update($data);
        return $this->getAllUsers();
    }

    public function setMaxCheckers(Request $request)
    {
        $request->validate([
            'admin_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', 'admin');
                }),
            ],
            'quantity' => 'required|integer|min:1'
        ]);
        $user = User::find($request->admin_id);
        $user->update(['max_allowed_checker_tasks' => $request->quantity]);
        return $this->getAllUsers();
    }
}
