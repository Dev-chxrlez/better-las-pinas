<?php

namespace Tests\Feature;

use App\Models\Barangay;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\StaffAssignment;
use App\Models\User;
use Database\Seeders\BarangaySeeder;
use Database\Seeders\ReportCategorySeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\DepartmentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class AuthenticationAndReportWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withHeaders([
            'Origin' => 'http://localhost:5173',
            'Referer' => 'http://localhost:5173/',
        ]);
        $this->seed([BarangaySeeder::class, DepartmentSeeder::class, ReportCategorySeeder::class, RolesAndPermissionsSeeder::class]);
    }

    public function test_resident_can_register_persist_session_and_logout(): void
    {
        $response=$this->postJson('/api/v1/auth/register',['full_name'=>'Maria Santos','email'=>'maria@example.test','password'=>'Strong!Pass123','password_confirmation'=>'Strong!Pass123']);
        $response->assertCreated()->assertJsonPath('data.user.role','resident');
        $user=User::where('email','maria@example.test')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->getJson('/api/v1/auth/me')->assertOk()->assertJsonPath('data.user.email','maria@example.test');
        $this->postJson('/api/v1/auth/logout')->assertOk();
        $this->getJson('/api/v1/auth/me')->assertUnauthorized();
    }

    public function test_duplicate_registration_and_invalid_login_are_rejected(): void
    {
        User::factory()->create(['email'=>'used@example.test']);
        $this->postJson('/api/v1/auth/register',['full_name'=>'Another User','email'=>'used@example.test','password'=>'Strong!Pass123','password_confirmation'=>'Strong!Pass123'])->assertUnprocessable()->assertJsonValidationErrors('email');
        $this->postJson('/api/v1/auth/login',['email'=>'used@example.test','password'=>'incorrect'])->assertUnprocessable();
    }

    public function test_resident_can_submit_list_and_view_only_owned_reports(): void
    {
        Storage::fake('local');
        $resident=User::factory()->create(); $resident->assignRole('resident');
        $other=User::factory()->create(); $other->assignRole('resident');
        $payload=$this->reportPayload(); $payload['photo']=UploadedFile::fake()->image('evidence.jpg');
        $created=$this->actingAs($resident)->post('/api/v1/reports',$payload,['Accept'=>'application/json']);
        $created->assertCreated();
        $report=Report::firstOrFail();
        $this->assertSame($resident->id,$report->reporter_id);
        $this->assertDatabaseHas('report_status_history',['report_id'=>$report->id,'to_status'=>'submitted']);
        $this->actingAs($resident)->getJson('/api/v1/reports')->assertOk()->assertJsonPath('data.pagination.total',1);
        $this->actingAs($resident)->getJson("/api/v1/reports/{$report->id}")->assertOk();
        $attachment=$report->attachments()->firstOrFail();
        $this->actingAs($resident)->get("/api/v1/reports/{$report->id}/attachments/{$attachment->id}")->assertOk();
        $this->actingAs($other)->getJson("/api/v1/reports/{$report->id}")->assertForbidden();
    }

    public function test_authorized_staff_can_review_assign_update_and_resident_sees_history(): void
    {
        Storage::fake('local');
        $resident=User::factory()->create(); $resident->assignRole('resident');
        $intake=User::factory()->create(); $intake->assignRole('intake-officer');
        $officer=User::factory()->create(); $officer->assignRole('barangay-officer');
        $barangay=Barangay::firstOrFail();
        StaffAssignment::create(['user_id'=>$officer->id,'barangay_id'=>$barangay->id,'is_active'=>true]);
        $payload=$this->reportPayload(); $payload['barangay_id']=$barangay->id;
        $this->actingAs($resident)->postJson('/api/v1/reports',$payload)->assertCreated();
        $report=Report::firstOrFail();
        $this->actingAs($intake)->postJson("/api/v1/staff/reports/{$report->id}/review",['decision'=>'accept','note'=>'Validated by intake.'])->assertOk();
        $this->actingAs($intake)->postJson("/api/v1/staff/reports/{$report->id}/assign",['assigned_to_user_id'=>$officer->id,'barangay_id'=>$barangay->id])->assertOk();
        $this->actingAs($officer)->postJson("/api/v1/staff/reports/{$report->id}/status",['status'=>'in_progress','note'=>'Inspection underway.'])->assertOk();
        $this->actingAs($officer)->post("/api/v1/staff/reports/{$report->id}/status",['status'=>'resolved','note'=>'Work completed.','resolution_notes'=>'Damaged surface repaired.','resolution_photo'=>UploadedFile::fake()->image('repair.jpg')],['Accept'=>'application/json'])->assertOk();
        $this->actingAs($resident)->getJson("/api/v1/reports/{$report->id}")->assertOk()->assertJsonPath('data.report.status','resolved');
        $this->actingAs($resident)->get("/api/v1/reports/{$report->id}/resolution-evidence")->assertOk();
        $this->actingAs($resident)->putJson("/api/v1/reports/{$report->id}/feedback", ['rating'=>5,'feedback'=>'The update was clear and the repair was completed.'])->assertOk()->assertJsonPath('data.report.resolution.resident_rating',5);
        $this->assertDatabaseCount('report_status_history',5);
        $this->assertDatabaseHas('audit_logs',['action'=>'report.assigned']);
        $this->assertDatabaseHas('notifications',['notifiable_id'=>$resident->id]);
    }

    public function test_unauthorized_staff_cannot_update_unassigned_report_and_admin_has_permissions(): void
    {
        $resident=User::factory()->create(); $resident->assignRole('resident');
        $officer=User::factory()->create(); $officer->assignRole('barangay-officer');
        $admin=User::factory()->create(); $admin->assignRole('system-administrator');
        $report=Report::create(array_merge($this->reportPayload(),['reference'=>'LPC-TEST-UNAUTHORIZED','reporter_id'=>$resident->id,'status'=>'submitted','submitted_at'=>now()]));
        $this->actingAs($officer)->postJson("/api/v1/staff/reports/{$report->id}/status",['status'=>'in_progress','note'=>'Should not work.'])->assertForbidden();
        $this->assertTrue($admin->can('manage users'));
        $this->actingAs($admin)->getJson('/api/v1/admin/users')->assertOk();
        $this->actingAs($admin)->postJson('/api/v1/admin/users', ['name'=>'New Intake Officer','email'=>'new.intake@example.test','password'=>'Another!Pass123','password_confirmation'=>'Another!Pass123','role'=>'intake-officer'])->assertCreated();
        $this->assertTrue(User::where('email','new.intake@example.test')->firstOrFail()->hasRole('intake-officer'));
        $this->actingAs($admin)->getJson('/api/v1/admin/analytics')->assertOk()->assertJsonPath('data.total',1);
        $this->actingAs($admin)->getJson('/api/v1/admin/audit-logs')->assertOk();
        $this->actingAs($admin)->postJson('/api/v1/admin/categories',['name'=>'Public Lighting','description'=>'Streetlight concerns.'])->assertCreated();
        $this->actingAs($officer)->getJson('/api/v1/admin/users')->assertForbidden();
        $this->actingAs($officer)->getJson('/api/v1/notifications')->assertOk();
    }

    private function reportPayload(): array
    {
        return ['category_id'=>ReportCategory::firstOrFail()->id,'barangay_id'=>Barangay::firstOrFail()->id,'title'=>'Damaged road surface','description'=>'A large damaged section of road needs inspection.','location_text'=>'Near the covered court on the main road.'];
    }
}
