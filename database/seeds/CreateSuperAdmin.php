<?php

use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\Hash;

class CreateSuperAdmin extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!User::where('role', 'superadmin')->exists()) {
            $password = str_random(15);
            User::create(['name' => 'superadmin', 'role' => 'superadmin', 'password' => Hash::make($password)]);
            echo "Remember this password for superadmin: ".$password."\n";
        } else {
            echo "Superadmin has already registered \n";
        }
    }
}
