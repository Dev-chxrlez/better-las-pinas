<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Engineering and Public Works', 'Environment and Natural Resources', 'Traffic Management', 'Social Welfare and Development'] as $name) {
            Department::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'is_active' => true]);
        }
    }
}
