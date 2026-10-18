<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
final class ReportCategory extends Model { use HasUuids; protected $fillable=['name','slug','description','sort_order','is_active']; protected function casts(): array { return ['is_active'=>'boolean']; } public function reports(): HasMany{return $this->hasMany(Report::class,'category_id');} }
