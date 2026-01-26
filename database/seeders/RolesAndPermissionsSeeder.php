<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        $perms = [
            'manage_users','manage_programs','view_programs','create_programs','edit_programs','delete_programs',
            'view_enrollments','confirm_payments','enroll_program'
        ];
        foreach($perms as $p) Permission::firstOrCreate(['name'=>$p]);

        $admin = Role::firstOrCreate(['name'=>'admin']);
        $admin->givePermissionTo(Permission::all());

        $trainer = Role::firstOrCreate(['name'=>'trainer']);
        $trainer->givePermissionTo(['create_programs','view_programs','edit_programs','view_enrollments']);

        Role::firstOrCreate(['name'=>'student']);
    }
}
