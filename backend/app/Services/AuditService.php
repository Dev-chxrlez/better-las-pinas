<?php
namespace App\Services;
use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
final class AuditService { public static function record(Request $request,string $action,Model $subject,?array $before=null,?array $after=null): void { AuditLog::create(['actor_id'=>$request->user()?->id,'action'=>$action,'subject_type'=>$subject::class,'subject_id'=>(string)$subject->getKey(),'before'=>$before,'after'=>$after,'ip_address'=>$request->ip(),'user_agent'=>$request->userAgent()]); } }
