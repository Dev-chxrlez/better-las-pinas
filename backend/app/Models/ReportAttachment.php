<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
final class ReportAttachment extends Model { use HasUuids; protected $fillable=['report_id','uploaded_by','disk','path','original_name','mime_type','size']; }
