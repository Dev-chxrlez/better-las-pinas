<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
final class ReportUpdate extends Model { use HasUuids; protected $fillable=['report_id','author_id','body','visible_to_resident']; protected function casts(): array{return ['visible_to_resident'=>'boolean'];} public function author(): BelongsTo{return $this->belongsTo(User::class,'author_id');} }
