<?php
namespace Tests\Feature;
use Tests\TestCase;
final class HealthEndpointTest extends TestCase { public function test_health_endpoint_returns_safe_json_response(): void { $response=$this->getJson('/api/v1/health'); $response->assertOk()->assertExactJson(['success'=>true,'message'=>'Las Piñas Connect API is healthy.','data'=>['status'=>'ok']]); } }
