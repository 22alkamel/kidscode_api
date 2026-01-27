<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role; // ✅ استدعاء Spatie Role

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminEmail = 'admin@kidscode.com';

        // تأكد من وجود role 'admin' في جدول roles
        Role::firstOrCreate(['name' => 'admin']);

        // تحقق إذا كان الأدمن موجود
        $admin = User::where('email', $adminEmail)->first();

        if ($admin) {
            // تحديث الأدمن الموجود
            $admin->update([
                'name' => 'Admin',
                'password' => Hash::make('12345678'),
                'emailverifiedat' => now(),
                'email_verified' => true,
                'role' => 'admin',
                'otp_verified' => true,
                'otp_code' => null,
                'otpexpiresat' => null,
            ]);
        } else {
            // إنشاء إدمن جديد
            $admin = User::create([
                'name' => 'Admin',
                'email' => $adminEmail,
                'password' => Hash::make('12345678'),
                'emailverifiedat' => now(),
                'email_verified' => true,
                'role' => 'admin',
                'otp_verified' => true,
                'otp_code' => null,
                'otpexpiresat' => null,
            ]);
        }

        // ربط الأدمن بالـ role في Spatie
        $admin->assignRole(Role::where('name','admin')->where('guard_name','api')->first());

    }
}
