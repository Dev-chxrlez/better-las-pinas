<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
final class Department extends Model { use HasUuids; protected $fillable=['name','slug','contact_email','is_active']; protected function casts(): array { return ['is_active'=>'boolean']; } }
