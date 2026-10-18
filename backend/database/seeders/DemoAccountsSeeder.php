<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Barangay;
use App\Models\Department;
use App\Models\StaffAssignment;
use Illuminate\Database\Seeder;

final class DemoAccountsSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) return;
        $password = env('DEMO_ACCOUNT_PASSWORD');
        if (! is_string($password) || strlen($password) < 12) { $this->command?->warn('Demo accounts skipped: set DEMO_ACCOUNT_PASSWORD to at least 12 characters.'); return; }
        $accounts = [
            ['role'=>'system-administrator','name'=>'Demo System Administrator','email'=>env('DEMO_ADMIN_EMAIL','admin.demo@example.test')],
            ['role'=>'intake-officer','name'=>'Demo Intake Officer','email'=>env('DEMO_INTAKE_EMAIL','intake.demo@example.test')],
            ['role'=>'barangay-officer','name'=>'Demo Barangay Officer','email'=>env('DEMO_BARANGAY_EMAIL','barangay.demo@example.test')],
            ['role'=>'city-department-officer','name'=>'Demo Department Officer','email'=>env('DEMO_DEPARTMENT_EMAIL','department.demo@example.test')],
            ['role'=>'resident','name'=>'Demo Resident','email'=>env('DEMO_RESIDENT_EMAIL','resident.demo@example.test')],
        ];
        foreach ($accounts as $account) {
            $user = User::updateOrCreate(['email'=>$account['email']], ['name'=>$account['name'],'password'=>$password]);
            $user->syncRoles([$account['role']]);
            StaffAssignment::where('user_id', $user->id)->update(['is_active' => false]);
            if ($account['role'] === 'barangay-officer' && ($barangay = Barangay::query()->first())) StaffAssignment::create(['user_id'=>$user->id,'barangay_id'=>$barangay->id,'is_active'=>true]);
            if ($account['role'] === 'city-department-officer' && ($department = Department::query()->first())) StaffAssignment::create(['user_id'=>$user->id,'department_id'=>$department->id,'is_active'=>true]);
        }
    }
}
