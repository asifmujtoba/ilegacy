<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Lead;
use App\Models\Timeline;
use App\Models\User;
use App\Services\GlobalProductIdService;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function getReports()
    {
        $query = Lead::with(['caller']);

        $startDate = date('Y-m-d', strtotime(request()->get('from')));
        $endDate = date('Y-m-d', strtotime(request()->get('to')));
        if (request()->get('from') && request()->get('to')) {
            $query->whereDate('updated_at', '>=', $startDate)
                    ->whereDate('updated_at', '<=', $endDate);
        }

        if (request()->get('status')) {
            $query->where('status_caller', request()->get('status'));
        }

        if (request()->get('phone')) {
            $query->whereHas('customer', function ($customer) {
                $customer->where('phone', 'like', '%' . request()->get('phone') . '%');
            });
        }

        if (request()->get('orderId')) {
            $query->where('order_id', 'like', '%' . request()->get('orderId') . '%');
        }
        if (GlobalProductIdService::get()) {
            $query->where('product_id', GlobalProductIdService::get());
        }
        if (request()->get('caller_filter')){
            $query->where('caller_id',  request()->get('caller_filter'));
        }
        if (request()->get('confirm')) {
            $query->where('status_caller', Lead::CONFIRMED);
        }

        // It will take only the leads that have caller ID ( that means processed Leads )
        $query->where('caller_id', '!=', 0)->orderBy('updated_at', 'desc');

        // It will take only the leads that had been processed by a caller
        $query->whereHas('caller', function ($query) {
            return $query->where('role', 'caller');
        })->get();

        return DataTables::of($query)
        // ->addColumn('customer_phone', function (Lead $lead) {
            // return $lead->customer ? $lead->customer->phone : '';
        // })
        ->editColumn('customer_id', function (Lead $lead) {
            return $lead->customer ? $lead->customer->name : '';
        })
        ->editColumn('product_id', function (Lead $lead) {
            return $lead->product ? $lead->product->name : '';
        })
        ->editColumn('caller_id', function (Lead $lead) {
            return $lead->caller ? $lead->caller->name : '';
        })
        ->editColumn('created_at', function (Lead $lead) {
            return Carbon::parse($lead->updated_at);
        })
        ->editColumn('note', function(Lead $lead){
            $note_text = $lead->note && strpos($lead->note, '||') ? explode('||', $lead->note)[0] : $lead->note;
            
            return $note_text;
        })
        ->addColumn('action', function (Lead $lead) {
            $html = '<a href="'. route('single.timeline.view', $lead->id) .'"  class="btn btn-primary text-center"> Lead\'s History</a>';
            return $html;
        })
        ->rawColumns(['action', 'note'])
        ->make(true);
    }
    
    public function getReportsAttendance()
    {
        $query = Attendance::with(['caller']);

        $startDate = date('Y-m-d', strtotime(request()->get('from')));
        $endDate = date('Y-m-d', strtotime(request()->get('to')));
        if (request()->get('from') && request()->get('to')) {
            $query->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate);
        }

        if (request()->get('status')) {
            $query->where('status_caller', request()->get('status'));
        }

        if (request()->get('caller_filter')){
            $query->where('caller_id',  request()->get('caller_filter'));
        }
       

        // It will take only the leads that have caller ID ( that means processed Leads )
        $query->where('caller_id', '!=', 0)->orderBy('updated_at', 'desc');

        // It will take only the leads that had been processed by a caller
        $query->whereHas('caller', function ($query) {
            return $query->where('role', 'caller');
        })->get();

        return DataTables::of($query)
      
        ->editColumn('date', function (Attendance $attendance) {
            return $attendance->date ? $attendance ->date : '';
        })
        ->editColumn('caller_id', function (Attendance $attendance) {
            return $attendance->caller ? $attendance->caller->name : '';
        })
        ->editColumn('checkin_time', function (Attendance $attendance) {
            return $attendance->checkin_time ? $attendance->checkin_time : '';
        })
        
        ->editColumn('checkout_time', function (Attendance $attendance) {
            return $attendance->checkout_time ? $attendance->checkout_time : '';
        })
        ->make(true);
    }

    public function getReportsCaller()
    {
           
        $query = Timeline::select('caller_id', DB::raw('count(*) as total'))
                        ->groupBy('caller_id')
                        ->get();
       
        // $startDate = date('Y-m-d', strtotime(request()->get('from')));
        // $endDate = date('Y-m-d', strtotime(request()->get('to')));
        // if (request()->get('from') && request()->get('to')) {
        //     $query->whereDate('updated_at', '>=', $startDate)
        //             ->whereDate('updated_at', '<=', $endDate);
        // }
       return $query;
    }
    public function getReportsCustom()
    {
        $query = Lead::with(['caller']);

        $startDate = date('Y-m-d', strtotime(request()->get('from')));
        $endDate = date('Y-m-d', strtotime(request()->get('to')));
        if (request()->get('from') && request()->get('to')) {
            $query->whereDate('updated_at', '>=', $startDate)
                    ->whereDate('updated_at', '<=', $endDate);
        }

        if (request()->get('status')) {
            $query->where('status_caller', request()->get('status'));
        }

        if (request()->get('phone')) {
            $query->whereHas('customer', function ($customer) {
                $customer->where('phone', 'like', '%' . request()->get('phone') . '%');
            });
        }
        if (request()->get('caller_filter')){
            $query->where('caller_id',  request()->get('caller_filter'));
        }

        if (request()->get('orderId')) {
            $query->where('order_id', 'like', '%' . request()->get('orderId') . '%');
        }

        if (GlobalProductIdService::get()) {
            $query->where('product_id', GlobalProductIdService::get());
        }



        $query->where('caller_id', '!=', 0)->orderBy('updated_at', 'desc');

        return DataTables::of($query)
        // ->addColumn('customer_phone', function (Lead $lead) {
            // return $lead->customer ? $lead->customer->phone : '';
        // })
        ->editColumn('product_id', function (Lead $lead) {
            return $lead->product ? $lead->product->name : '';
        })
        ->editColumn('order_id', function (Lead $lead) {
            return $lead->order_id ? $lead->order_id : '';
        })
        ->editColumn('customer_id', function (Lead $lead) {
            return $lead->customer ? $lead->customer->name : '';
        })
        
        ->editColumn('caller_id', function (Lead $lead) {
            return $lead->caller ? $lead->caller->name : '';
        })
        ->editColumn('price', function (Lead $lead) {
            return $lead->price ? $lead->price : 0;
        })
        ->editColumn('quantity', function (Lead $lead) {
            return $lead->quantity ? $lead->quantity : 0;
        })
       
        ->editColumn('created_at', function (Lead $lead) {
            return Carbon::parse($lead->created_at);
        })
        ->editColumn('status_caller', function (Lead $lead) {
            $html = '<span class="status-color" style="background: '. Lead::COLORS[$lead->status_caller] .'"></span>';
            return $html;
        })
        ->rawColumns(['status_caller'])
        ->make(true);
    }
    
}
