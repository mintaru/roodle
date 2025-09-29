<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class RolesAndUsersSeeder extends Seeder
{
    public function run()
    {
        // Создаём роли
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $teacherRole = Role::firstOrCreate(['name' => 'teacher']);
        $studentRole = Role::firstOrCreate(['name' => 'student']);

        // Создаём пользователя
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'username' => 'admin',
                'password' => bcrypt('password')
            ]
        );

        // Назначаем роль
        $admin->assignRole($adminRole);

        $teacher = User::firstOrCreate(
            ['email' => 'teacher@example.com'],
            [
                'name' => 'teacher',
                'username' => 'teacher',
                'password' => bcrypt('password')
            ]
        );

        // Назначаем роль
        $teacher->assignRole($teacherRole);

        $student = User::firstOrCreate(
            ['email' => 'student@example.com'],
            [
                'name' => 'student',
                'username' => 'student',
                'password' => bcrypt('password')
            ]
        );

        // Назначаем роль
        $student->assignRole($studentRole);
    }
}
