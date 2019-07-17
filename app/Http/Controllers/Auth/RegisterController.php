<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/admins';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $messages = [
            'name.unique' => 'Пользователь с таким именем уже существует',
        ];

        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255', 'min:5', 'unique:users'],
            // 'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required',
                'string',
                'min:8',
                // 'confirmed'
            ],
        ], $messages);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data, $role)
    {
        return User::create([
            'name' => $data['name'],
            // 'email' => $data['email'],
            'role' => $role,
            'password' => Hash::make($data['password']),
            'api_token' => str_random(60)
        ]);
    }

    public function register(Request $request, $role)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all(), $role)));

        // $this->guard()->login($user);
        if ($role == 'admin') {
            $users = User::whereIn('role', ['admin', 'checker'])->with('checkers.user:id,name')->get(['id', 'name', 'role'])->groupBy('role')->toArray();
            return $users;
        } else {
            $users = User::where('role', 'checker')->with('checkertasks:id,checker_id,url,isworking')->get(['id', 'name']);
            return $users;
        }

        // return $this->registered($request, $user) ?: redirect($this->redirectPath());
    }
}
