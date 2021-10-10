<?php

namespace App\Http\Controllers\Frontend;

use App\Exports\ReportsExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\User;

class ReportController extends Controller
{
    public function index()
    {
        $callers = User::where('role', User::ROLE_CALLER)->get();
        return view('report.leadRevision', compact('callers'));
    }

    public function export(Request $request)
    {
        $current_timestamp = date('d_m_Y_H_i_s', $_SERVER['REQUEST_TIME']);
        return Excel::download(new ReportsExport($request), $current_timestamp.'_reports.xlsx');
    }

    public function todaysConfirm()
    {
        return view('report.todaysConfirm');
    }

    public function dailyReports()
    {
        $callers = User::where('role', User::ROLE_CALLER)->get();
        return view('report.dailyReports', compact('callers'));
    }
    public function attendanceReports()
    {
        $callers = User::where('role', User::ROLE_CALLER)->get();
        return view('report.attendanceReports', compact('callers'));
    }
    public function callerReports()
    {
        $callers = User::where('role', User::ROLE_CALLER)->get();
        return view('report.dailyCallReports', compact('callers'));
    }
}
