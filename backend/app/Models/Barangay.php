<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
final class Barangay extends Model { use HasUuids; protected $fillable=['name','slug','is_active']; protected function casts(): array { return ['is_active'=>'boolean']; } }
