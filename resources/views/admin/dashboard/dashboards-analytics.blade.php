@extends('layouts/contentNavbarLayout')

@section('title', 'Diamond Link 2.0 | Dashboard')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}">
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
@endsection

@section('content')
<div class="row"> 
  <div class="col-lg-12 col-md-12 order-1">
    <div class="row">
      <div class="col-lg-6 col-md-12 col-6 mb-4">
        <div class="card">
          <div class="card-body">
            <!-- <div class="card-title d-flex align-items-start justify-content-between">
              <div class="avatar flex-shrink-0">
                <img src="{{asset('assets/img/icons/unicons/chart-success.png')}}" alt="chart success" class="rounded">
              </div>
            </div> -->
            <span class="fw-semibold d-block mb-1">Total Coupons</span>
            <h3 class="card-title mb-2">{{DB::table('coupon')->count()}}</h3>
          </div>
                    </div>
      </div>
      <div class="col-lg-6 col-md-12 col-6 mb-4">
        <div class="card">
          <div class="card-body">
            <!-- <div class="card-title d-flex align-items-start justify-content-between">
              <div class="avatar flex-shrink-0">
                <img src="{{asset('assets/img/icons/unicons/chart-success.png')}}" alt="chart success" class="rounded">
              </div>
            </div> -->
            <span class="fw-semibold d-block mb-1">Total Registered Stores</span>
            <h3 class="card-title mb-2">{{DB::table('users')->count()}}</h3>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Total Revenue -->
</div>

@endsection
