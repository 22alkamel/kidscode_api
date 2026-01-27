<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name'=>'admin','guard_name'=>'api']);
        Role::firstOrCreate(['name'=>'trainer','guard_name'=>'api']);
        Role::firstOrCreate(['name'=>'student','guard_name'=>'api']);
    }
}
