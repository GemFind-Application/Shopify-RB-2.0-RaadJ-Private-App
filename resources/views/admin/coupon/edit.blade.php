@extends('layouts/contentNavbarLayout')

@section('title', 'Diamond Link 2.0 | Add Update')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Coupon /</span> Update<button
        onclick="window.location='{{URL::route('gfadmin.couons_index')}}'" type="submit" class="btn btn-dark "
        style="float:right">Back </button></h4>
@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<!-- Basic Layout -->
<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{url('gfadmin/update/'.$data['id'])}}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label " for="basic-icon-default-fullname">Shop</label>
                        <div class="input-group input-group-merge required">
                            <span id="basic-icon-default-fullname2" class="input-group-text"><i class='bx bx-store' ></i></span>
                            <input type="text" name="shop" class="form-control" id="basic-icon-default-fullname"
                                placeholder="e.g. gemfind-demo-store-3.myshopify.com" aria-label="John Doe"
                                value="{{$data['shop']}}"
                                aria-describedby="basic-icon-default-fullname2" />
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="basic-icon-default-company">Discount code</label>
                        <div class="input-group input-group-merge required">
                            <span id="basic-icon-default-company2" class="input-group-text"><i class='bx bxs-purchase-tag' ></i></span>
                            <input type="text" name="discount_code" id="basic-icon-default-company" class="form-control" value="{{$data['discount_code']}}"
                                placeholder="e.g. SPRINGSALE" aria-label="ACME Inc."
                                aria-describedby="basic-icon-default-company2" />
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="exampleFormControlSelect1" class="form-label">Select Discount Type</label>
                        <div class="input-group input-group-merge required">
                            <span id="basic-icon-default-company2" class="input-group-text"><i class='bx bxs-discount'></i></span>
                            <select class="form-select required" name="discount_type" id="exampleFormControlSelect1"
                            aria-label="Default select example">
                            <option value="">Please select Type</option>
                            <option value="Percentage"  @if($data['discount_type'] == "Percentage") selected @endif>Percentage</option>
                            <option value="Flat rate" @if($data['discount_type'] == "Flat rate") selected @endif>Flat rate</option>
                        </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="basic-icon-default-company">Discount value</label>
                        <div class="input-group input-group-merge required">
                            <span id="basic-icon-default-company2" class="input-group-text"><i class='bx bxs-coupon'></i></i></span>
                            <input type="text" name="discount_value" id="basic-icon-default-company" class="form-control"
                                placeholder="e.g. 10" aria-label="ACME Inc." value="{{$data['discount_value']}}"
                                aria-describedby="basic-icon-default-company2" />
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection