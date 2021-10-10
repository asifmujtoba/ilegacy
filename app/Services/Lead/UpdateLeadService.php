<?php

namespace App\Services\Lead;

use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateLeadService
{
    public static function run(Request $request)
    {
        $lead = Lead::find($request['data']['lead_id'])->customer->update([
            'name' => $request['data']['name'],
            'phone' => $request['data']['phone'],
            'email' => $request['data']['email'],
            'address' => $request['data']['address'],
        ]);
        $caller_name = auth()->user()->name;
        $caller_id = auth()->user()->id;
        $current_timestamp = date('d-m-Y H:i:s', $_SERVER['REQUEST_TIME']);

        Lead::find($request['data']['lead_id'])->update([
            'status_caller' => $request['data']['callerstatus'],
            'quantity' => $request['data']['quantity'],
            'price' => $request['data']['price'],
            'note' => $request['data']['note'] . " || $caller_name($caller_id) - $current_timestamp ",

        ]);
        
        return $lead;
    }
}
