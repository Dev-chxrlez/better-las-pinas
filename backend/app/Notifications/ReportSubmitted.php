<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class ReportSubmitted extends Notification
{
    use Queueable;

    public function __construct(private readonly Report $report) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'report_id' => $this->report->id,
            'reference' => $this->report->reference,
            'status' => 'submitted',
            'message' => "Report {$this->report->reference} was submitted successfully.",
        ];
    }
}
