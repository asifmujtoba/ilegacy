<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Lead;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\Types\Null_;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $attendance = Attendance::query()
                                  ->where('caller_id', Auth::user()->id)
                                  ->where('date', Carbon::today()->toDateString())
                                  ->first();
        
        if($attendance && $attendance->checkout_time == Null){
            $attendance = 'Check Out';
        }
        else if($attendance && $attendance->checkout_time !== Null){
            $attendance = 'Checked Out';
        }
        else{
            $attendance = 'Check In';
        }
        $products = Product::all();
        return view('products-home', compact('products', 'attendance'));
    }

    public function temp()
    {
        set_time_limit(420);
        $leads = Lead::skip(79754)->take(60)->get();
        $count = 0;

        // Lead::chunk(10000, function ($leads) use ($count) {
        foreach ($leads as $lead) {
            $count++;
            var_dump($count);

            // $phone_last     = substr($lead->phone, -7);
            // $duplicate      = null;
            $duplicate_lead = Lead::query()
                                  ->where('id', '!=', $lead->id)
                                  ->where('customer_id', '=', $lead->customer_id)
                                  ->where('product_id', '=', $lead->product_id)
                                  ->orderBy('created_at', 'ASC')
                                  ->first();

            $first_customer = Lead::query()
                                //   ->where('id', '!=', $lead->id)
                                  ->where('customer_id', '=', $lead->customer_id)
                                  ->orderBy('created_at', 'ASC')
                                  ->first();
            if ($duplicate_lead) {
                $lead->duplicate_id       = $duplicate_lead->id;
                $lead->save();
            } else {
                $lead->duplicate_id       = null;
                $lead->save();
            }

            if ($first_customer) {
                $first_customer->duplicate_id       = null;
                $first_customer->save();
            }
        }
        // });
    }
}
