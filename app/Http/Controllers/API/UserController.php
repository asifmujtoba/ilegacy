<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Services\User\AttendanceService;

class UserController extends Controller
{
    public function getUsers()
    {
        $query = User::all();

        return DataTables::of($query)
                ->addColumn('action', function (User $user) {
                    return "
                    <a href='" . route('users.edit', $user->id) . "' class='btn btn-primary'><i class='mdi mdi-pencil'></i></a>
                    <a href='" . route('users.destroy', $user->id) . "' class='btn btn-danger delete-user'><i class='mdi mdi-delete'></i></a>
                    ";
                })
                ->rawColumns(['action'])
                ->make(true);
    }

    public function getTrashUsers()
    {
        $query = User::onlyTrashed();

        return DataTables::of($query)
                ->addColumn('action', function (User $user) {
                    return "<a href='" . route('users.restore', $user->id) . "' class='btn btn-success'>Restore</a>";
                })
                ->rawColumns(['action'])
                ->make(true);
    }
    public function getAttendance()
    {
        $query = Attendance::Where(function($query){
            $query->where('caller_id', Auth::user()->id)
            ->where('date', Carbon::today()->toDateString());
        })->first();
        if(!$query){
            return Null;
        }
        return response()->json(compact('query'));
    }

    public function registerAttendance(Request $request)
    {
       return AttendanceService::run($request);
    }
}
