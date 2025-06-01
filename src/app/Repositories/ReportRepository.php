<?php

namespace App\Repositories;

use App\Models\Report;
use App\Repositories\Interfaces\ReportRepositoryInterface;

class ReportRepository extends BaseRepository implements ReportRepositoryInterface
{
    public function model()
    {
        return Report::class;
    }

    public function findPending()
    {
        return $this->model::where('status', 'pending')
            ->with(['reporter', 'reportedUser'])
            ->latest()
            ->paginate(9);
    }

    public function findByUserAndStatus($reporterId, $reportedUserId, $status)
    {
        return $this->model::where('reporter_id', $reporterId)
            ->where('reported_user_id', $reportedUserId)
            ->where('status', $status)
            ->first();
    }

    public function updateStatus($id, $status)
    {
        $report = $this->find($id);
        $report->status = $status;
        $report->save();
        return $report;
    }

    public function countPending()
    {
        return $this->model::where('status', 'pending')->count();
    }
}
