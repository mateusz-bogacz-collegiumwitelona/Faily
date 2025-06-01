<?php

namespace App\Services;

use App\Repositories\Interfaces\ReportRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\Interfaces\CacheServiceInterface;
use App\Services\Interfaces\ReportServiceInterface;
use App\Models\Report;
use Illuminate\Http\Request;


class ReportService extends BaseService implements ReportServiceInterface
{
    protected $reportRepository;
    protected $userRepository;
    protected $cacheService;

    public function __construct(
        ReportRepositoryInterface $reportRepository,
        UserRepositoryInterface $userRepository,
        ?CacheServiceInterface $cacheService
    )
    {
        parent::__construct($reportRepository, $cacheService);
        $this->userRepository = $userRepository;
        $this->reportRepository = $reportRepository;

        $this->cacheTags = ['reports'];
        $this->cachePrefix = 'report';
    }

    public function reportUser(Request $request, $userId, $reporterId)
    {
        $user = $this->userRepository->find($userId);

        if (!$user) {
            throw new \Exception('User not found.');
        }

        if ($user->role === 'admin') {
            throw new \Exception('Unable to report admin.');
        }

        if ($userId == $reporterId) {
            throw new \Exception('You can\'t report yourself.');
        }

        $existingReport = $this->reportRepository->findByUserAndStatus($reporterId, $userId, 'pending');

        if ($existingReport) {
            throw new \Exception('You have already reported this user. Wait for the report to be verified.');
        }

        $result = $this->reportRepository->create([
            'reporter_id' => $reporterId,
            'reported_user_id' => $userId,
            'reason' => $request->reason,
        ]);

        if ($this->useCache()) {
            $this->cacheService->forget("{$this->cachePrefix}.pending.count");
            $this->cacheService->flushTags(['reports']);
        }

        return $result;
    }

    public function approveReport($id)
    {
        $report = $this->reportRepository->find($id);

        if (!$report) {
            throw new \Exception('Notification does not exist.');
        }

        $this->reportRepository->updateStatus($id, 'reviewed');

        $user = $this->userRepository->incrementReportCount($report->reported_user_id);

        if ($user->reports_count >= 3 && $user->status !== 'banned' && $user->role !== 'admin') {
            $this->userRepository->updateStatus($user->id, 'banned');
        }

        if ($this->useCache()) {
            $this->cacheService->forget("{$this->cachePrefix}.pending.count");
            $this->cacheService->forget("user.{$report->reported_user_id}");
            $this->cacheService->forget("user.banned.count");
            $this->cacheService->flushTags(['reports', 'users']);
        }
        return $report;
    }

    public function rejectReport($id)
    {
        $report = $this->reportRepository->find($id);

        if (!$report) {
            throw new \Exception('Notification does not exist.');
        }

        $this->reportRepository->updateStatus($id, 'reviewed');

        $user = $this->userRepository->incrementReportCount($report->reported_user_id);
        $userWasBanned = false;

        if ($user->reports_count >= 3 &&
            $user->status !== 'banned' &&
            $user->role !== 'admin')
        {
            $this->userRepository->updateStatus($user->id, 'banned');
            $userWasBanned = true;

            if ($user->email) {
                $this->sendBanNotification($user, $report->reason);
            }
        }

        if ($this->useCache()) {
            $this->cacheService->forget("{$this->cachePrefix}.pending.count");
            $this->cacheService->forget("user.{$report->reported_user_id}");
            $this->cacheService->forget("user.banned.count");
            $this->cacheService->flushTags(['reports', 'users']);
        }

        return [
            'report' => $report,
            'user_banned' => $userWasBanned
        ];
    }

    public function countPendingReports()
    {
        if (!$this->useCache()) {
            return $this->repository->countPending();
        }

        return $this->cacheService->remember(
            "{$this->cachePrefix}.pending.count",
            function () {
                return $this->repository->countPending();
            },
            60 * 10 //10min
        );
    }
    public function findPending()
    {
        return $this->repository->findPending();
    }

    protected function sendBanNotification($user, $reason = null)
    {
        try {
            Mail::to($user->email)->send(new UserBanned($user, $reason));
        } catch (\Exception $e) {
            \Log::error('Failed to send ban notification email: ' . $e->getMessage());
        }
    }
}
