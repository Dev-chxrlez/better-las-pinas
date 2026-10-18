<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([BarangaySeeder::class, DepartmentSeeder::class, ReportCategorySeeder::class, RolesAndPermissionsSeeder::class, DemoAccountsSeeder::class]);
    }
}
