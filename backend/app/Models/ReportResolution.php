<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
final class ReportResolution extends Model { use HasUuids; protected $fillable=['report_id','resolved_by','notes','evidence_path','resident_rating','resident_feedback','resolved_at']; protected function casts(): array{return ['resolved_at'=>'datetime'];} }
