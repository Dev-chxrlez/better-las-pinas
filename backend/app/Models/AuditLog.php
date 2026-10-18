<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
final class AuditLog extends Model { use HasUuids; protected $fillable=['actor_id','action','subject_type','subject_id','before','after','ip_address','user_agent']; protected function casts(): array{return ['before'=>'array','after'=>'array'];} public function actor(): BelongsTo{return $this->belongsTo(User::class,'actor_id');} }
