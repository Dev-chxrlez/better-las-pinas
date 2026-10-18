<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
final class ReportAssignment extends Model { use HasUuids; protected $fillable=['report_id','assigned_to_user_id','assigned_by_user_id','barangay_id','department_id','is_active','assigned_at']; protected function casts(): array{return ['is_active'=>'boolean','assigned_at'=>'datetime'];} public function assignee(): BelongsTo{return $this->belongsTo(User::class,'assigned_to_user_id');} }
