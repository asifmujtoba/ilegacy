<?php

namespace App\Exports;

use App\Models\Lead;
use App\Services\GlobalProductIdService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class LeadsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    use Exportable;

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = Lead::select('id', 'product_id', 'order_id', 'created_at', 'customer_id', 'customer_id', 'customer_id', 'note')->with('caller', 'product', 'customer');

        $startDate = date('Y-m-d', strtotime($this->request['start']));
        $endDate = date('Y-m-d', strtotime($this->request['end']));
        if ($this->request['start'] && $this->request['end']) {
            $query->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate);
        }

        $status = $this->request['status'];
        if ($this->request['status']) {
            $query->where('status_caller', $status);
        }

        if ($this->request['phone']) {
            $query->whereHas('customer', function ($customer) {
                $customer->where('phone', 'like', '%' . $this->request['phone'] . '%');
            });
        }

        if ($this->request['orderId']) {
            $query->where('order_id', 'like', '%' . $this->request['orderId'] . '%');
        }

        if (GlobalProductIdService::get()) {
            $query->where('product_id', GlobalProductIdService::get());
        }

        $query = $query->get();

        $data = [];

        foreach ($query as $value) {
            $data[]= $value;
        }

        return collect($data);
    }

    public function map($lead): array
    {
        return [
            $lead->id,
            $lead->product->name ?? '',
            $lead->order_id,
            $lead->created_at,
            $lead->customer->name,
            $lead->customer->phone,
            $lead->customer->address,
            $lead->note,
         ];
    }

    public function headings() : array
    {
        return ["Lead ID", "Product", "Order ID ","Created At" , "Customer" , "Phone" , "Address" , "Note"];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function (AfterSheet $event) {
                $cellRange = 'A1:W1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
            },
        ];
    }
}
