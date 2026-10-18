<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class Report extends Model
{
    use HasUuids;
    public const STATUSES = ['submitted','under_review','assigned','in_progress','resolved','closed','needs_information','rejected','duplicate'];
    protected $fillable = ['reference','reporter_id','category_id','barangay_id','title','description','location_text','latitude','longitude','status','submitted_at'];
    protected function casts(): array { return ['submitted_at'=>'datetime','latitude'=>'decimal:7','longitude'=>'decimal:7']; }
    public function reporter(): BelongsTo { return $this->belongsTo(User::class, 'reporter_id'); }
    public function category(): BelongsTo { return $this->belongsTo(ReportCategory::class, 'category_id'); }
    public function barangay(): BelongsTo { return $this->belongsTo(Barangay::class); }
    public function attachments(): HasMany { return $this->hasMany(ReportAttachment::class); }
    public function assignments(): HasMany { return $this->hasMany(ReportAssignment::class); }
    public function statusHistory(): HasMany { return $this->hasMany(ReportStatusHistory::class); }
    public function updates(): HasMany { return $this->hasMany(ReportUpdate::class); }
    public function resolution(): HasOne { return $this->hasOne(ReportResolution::class); }
}
