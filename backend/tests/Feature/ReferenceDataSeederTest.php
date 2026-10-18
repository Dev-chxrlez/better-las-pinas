<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class ReferenceDataSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_reference_data_contains_verified_barangays_and_categories(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(20, DB::table('barangays')->count());
        $this->assertTrue(DB::table('barangays')->where('name', 'B. F. International Village')->exists());
        $this->assertTrue(DB::table('barangays')->where('name', 'Talon Kuatro')->exists());
        $this->assertSame(8, DB::table('report_categories')->count());
        $this->assertSame(4, DB::table('departments')->count());
        $this->assertTrue(DB::table('departments')->where('name', 'Engineering and Public Works')->exists());
    }
}
