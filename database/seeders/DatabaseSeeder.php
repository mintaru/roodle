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


        $admin   = Role::create(['name' => 'admin']);
        $teacher = Role::create(['name' => 'teacher']);
        $student = Role::create(['name' => 'student']);
    
        // Создаём права
        Permission::create(['name' => 'edit courses']);
        Permission::create(['name' => 'view courses']);
    
        // Раздаём права ролям
        $admin->givePermissionTo(['edit courses', 'view courses']);
        $teacher->givePermissionTo(['edit courses', 'view courses']);
        $student->givePermissionTo(['view courses']);

    }
}
