<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Interfaces\ReportServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportServiceInterface $reportService)
    {
        $this->middleware('auth');
        $this->reportService = $reportService;
    }

    public function index()
    {
        $pendingReports = $this->reportService->findPending();
        return view('admin.reports.index', compact('pendingReports'));
    }

    public function approve($id)
    {
        try {
            $result = $this->reportService->approveReport($id);

            $message = 'The application has been approved.';

            if ($result['user_banned']) {
                $message .= 'The user was banned and notified via email.';
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function reject($id)
    {
        try {
            $this->reportService->rejectReport($id);
            return redirect()->back()->with('success', 'The application was rejected.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function reportUser(Request $request, $id)
    {
        try {
            $this->reportService->reportUser($request, $id, Auth::id());
            return redirect()->back()->with('success', __('messages.reportusermodal.successReport'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
