<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
final class StaffAssignment extends Model { use HasUuids; protected $fillable=['user_id','barangay_id','department_id','is_active']; protected function casts(): array { return ['is_active'=>'boolean']; } }
