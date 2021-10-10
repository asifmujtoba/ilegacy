<?php

namespace App\Services\User;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceService
{
    public static function run(Request $request)
    {
        $attendance = Attendance::Where(function($query){
            $query->where('caller_id', Auth::user()->id)
            ->where('date', Carbon::today()->toDateString());
        })->first();

        if(!$attendance){
            Attendance::create([
                'date' => Carbon::today()->toDateString(),
                'caller_id' => Auth::user()->id,
                'checkin_time' => Carbon::now()->toTimeString()
            ]);
            return 'Success 1';
        }
        else{
            Attendance::where('caller_id', Auth::user()->id)
                        ->where('date', Carbon::today()->toDateString())
                        ->update([
                            'checkout_time'=> Carbon::now()->toTimeString(),
                        ]);
                        return 'Success 2';
        }
        // $lead = Lead::find($request['data']['lead_id'])->customer->update([
        //     'name' => $request['data']['name'],
        //     'phone' => $request['data']['phone'],
        //     'email' => $request['data']['email'],
        //     'address' => $request['data']['address'],
        // ]);

        // Lead::find($request['data']['lead_id'])->update([
        //     'status_caller' => $request['data']['callerstatus'],
        //     'quantity' => $request['data']['quantity'],
        //     'price' => $request['data']['price'],
        //     'remarks' => $request['data']['remarks'],
        // ]);
        // return $lead;
    }
}
