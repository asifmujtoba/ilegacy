@extends('layouts.master')
@section('contents')
    @include('layouts.includes.breadcrumb',['title' => 'Callers Report'])


    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Leads:</h4>

                        <div class="filter row">
                            <div class="col-lg-5">
                                <div class="input-daterange input-group" id="date-range">
                                    <input type="text" class="form-control" id="start" name="start" placeholder="From Date">
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-info b-0 text-white">TO</span>
                                    </div>
                                    <input type="text" class="form-control" name="end" id="end" placeholder="To Date">
                                    <input type="hidden" name="role" value="{{ auth()->user()->role }}" id="role">
                                </div>
                            </div>

                            <div class="col-lg-5">
                                <select name="" id="callerfilter" class="select2">
                                    <option value="">Select a Caller</option>
                                    @foreach ($callers as $caller)
                                        <option value="{{ $caller->id }}">{{ $caller->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-2">
                                <button class="btn btn-primary filter-submit">Search</button>
                                <button class="btn btn-primary" onClick="(() => location.reload(true))()"><i
                                        class="mdi mdi-refresh"></i></button>
                            </div>
                        </div>
                        

                        <div class="row">
                            <div class="col-lg-3 mt-4">
                                <div id="stats">
                                    <ul></ul>
                                </div>
                            </div>
                            <div class="col-lg-7 float-right">
                                <div class="mt-4 chart-div">
                                    <canvas id="pie-chart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 statuses mb-4 mt-2">
                                <input type="hidden" name="role" value="{{ auth()->user()->role }}" id="role">
                                <a href="javascript;" id="changeStatus" data-status="">All</a>
                                @can('isAdmin')

                                    @foreach (App\Models\Lead::statuses as $status)
                                        - <a href="javascript;" id="changeStatus" data-status="{{ $status }}">{{ ucwords($status) }}</a>
                                    @endforeach

                                @elsecan('isCaller')

                                    @foreach (App\Models\Lead::CALLER_STATUS as $status)
                                        - <a href="javascript;" id="changeStatus" data-status="{{ $status }}">{{ ucwords($status) }}</a>
                                    @endforeach

                                @endcan

                            </div>

                        </div>
                        <div class="row">
                            <table class="table" id="report_datatable">
                                <thead>
                                    <tr>
                                        <th>Lead ID</th>
                                        <th>OrderID</th>
                                        <th>Customer</th>
                                        <th>Caller </th>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Note</th>
                                        <th>Created At</th>
                                        <th>Status</th>
                                    </tr>
                                   
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="/assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
@endsection

@section('js')
    <script src="/assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="/assets/custom-assets/js/caller.js"> </script>
@endsection
