<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();


        $admin   = Role::firstOrCreate(['name' => 'admin']);
        $teacher = Role::firstOrCreate(['name' => 'teacher']);
        $student = Role::firstOrCreate(['name' => 'student']);
    
        // Создаём права
        Permission::firstOrCreate(['name' => 'edit courses']);
        Permission::firstOrCreate(['name' => 'view courses']);
    
        // Раздаём права ролям
        $admin->givePermissionTo(['edit courses', 'view courses']);
        $teacher->givePermissionTo(['edit courses', 'view courses']);
        $student->givePermissionTo(['view courses']);

    }
}
