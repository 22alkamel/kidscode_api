<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@kidscode.com',
            'password' => Hash::make('12345678'),
            'role'=>'admin'
        ]);

         User::create([
            'name' => 'student',
            'email' => 'student@gmail.com',
            'password' => Hash::make('12345678'),
            'role'=>'admin'
        ]);
    }
}
