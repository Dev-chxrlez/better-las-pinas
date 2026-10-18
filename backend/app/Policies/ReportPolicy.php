<?php
namespace App\Policies;
use App\Models\Report;
use App\Models\User;
final class ReportPolicy {
 public function create(User $user): bool{return $user->can('submit report');}
 public function view(User $user,Report $report): bool{return $report->reporter_id===$user->id||$user->hasRole('system-administrator')||($user->can('review incoming reports')&&in_array($report->status,['submitted','under_review','rejected','duplicate'],true))||$report->assignments()->where('assigned_to_user_id',$user->id)->where('is_active',true)->exists();}
 public function updateStatus(User $user,Report $report): bool{return $user->hasRole('system-administrator')||($user->can('update report status')&&$report->assignments()->where('assigned_to_user_id',$user->id)->where('is_active',true)->exists());}
}
