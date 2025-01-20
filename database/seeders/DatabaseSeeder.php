<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Lecturer;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $roles = ['student', 'lecturer', 'admin'];
        foreach ($roles as $role) {
            Role::create([
                'name' => $role,
            ]);
        }

        //Tạo admin
        $admin = User::create([
            'name' => 'Nguyễn Hữu Hào',
            'email' => 'haonhph45336@fpt.edu.vn',
            'password' => Hash::make('123123'),
        ]);
        $role = Role::select('id')->where('name', 'admin')->first();
        UserRole::create([
            'user_id' => $admin->id,
            'role_id' => $role->id,
        ]);

        //Tạo Học viên
        $student = User::create([
            'name' => 'Học viên 1',
            'email' => 'hocvien1@gmail.com',
            'password' => Hash::make('123123'),
        ]);
        Student::create([
            'user_id' => $student->id,
        ]);
        $role = Role::select('id')->where('name', 'student')->first();
        UserRole::create([
            'user_id' => $student->id,
            'role_id' => $role->id,
        ]);

        //Tạo giảng viên
        $lecturer = User::create([
            'name' => 'Giảng viên A',
            'email' => 'giangviena@gmail.com',
            'password' => Hash::make('123123'),
        ]);
        Lecturer::create([
            'user_id' => $lecturer->id,
        ]);
        $role = Role::select('id')->where('name', 'lecturer')->first();
        UserRole::create([
            'user_id' => $lecturer->id,
            'role_id' => $role->id,
        ]);
    }
}
