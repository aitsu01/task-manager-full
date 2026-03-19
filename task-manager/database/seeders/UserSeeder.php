<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{


public function run(): void
{
    $roles = Role::all();

    User::factory(10)->make()->each(function ($user) use ($roles) {
        $user->role_id = $roles->random()->id;
        $user->password = Hash::make('password');
        $user->save();
    });
}
}
