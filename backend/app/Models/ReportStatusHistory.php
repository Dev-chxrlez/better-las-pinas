<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
final class ReportStatusHistory extends Model { use HasUuids; protected $table='report_status_history'; protected $fillable=['report_id','changed_by','from_status','to_status','note','visible_to_resident']; protected function casts(): array{return ['visible_to_resident'=>'boolean'];} public function actor(): BelongsTo{return $this->belongsTo(User::class,'changed_by');} }
