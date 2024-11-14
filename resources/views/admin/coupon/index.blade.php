@extends('layouts/contentNavbarLayout')

@section('title', 'Diamond Link 2.0 | Coupon')

@section('content')

<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Coupons</span><a class="btn btn-primary "
        href="{{url('gfadmin/create_coupon')}}" style="float:right;color: white;">Create</a> </h4>

@php
$data = \App\Models\Coupon::orderBy('id','DESC')->paginate(10);
@endphp



<!--  Basic Bootstrap Table -->
<div class="card">
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Shop</th>
                    <th>Actual Amount </th>
                    <th>Discount Code</th>
                    <th>Discount Value</th>
                    <th>Discounted Amount</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                <?php 
                    if (empty($data)) { ?>
                <tr>
                    <td colspan="7" style="text-align: center;"> Oopps! No Data Found!</td>
                </tr>
                <?php 
                         }else{ ?>
                @foreach ($data as $user)
                <tr>
                    <td><strong>{{$user['id']}}</strong></td>
                    <td>{{$user['shop']}}</td>
                    <td><?php echo '$'.env('APP_TOTAL_CHARGE'); ?></td>
                    <td>{{$user['discount_code']}}</td>
                    <td>{{$user['discount_type'] == "Percentage" 
                        ? $user['discount_value']. "%" 
                        : $user['discount_value']. " - Flat rate" }}</td>

                    <?php
                        if ($user['discount_type'] == "Percentage") {
                            $discounted_total = env('APP_TOTAL_CHARGE') - (env('APP_TOTAL_CHARGE') * ($user['discount_value'] / 100));
                        } else {
                            $discounted_total = env('APP_TOTAL_CHARGE') - $user['discount_value'];
                        }
                    ?>
                    <td>${{$discounted_total}}</td>
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{url('gfadmin/edit_coupon/'.$user['id'])}}"><i
                                        class="bx bx-edit-alt me-1"></i>
                                    Edit</a>
                                <a class="dropdown-item"
                                    href="{{url('gfadmin/delete_coupon/'.$user['id'])}}"><i
                                        class="bx bx-trash me-1"></i>
                                    Delete</a>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
                <?php } ?>

            </tbody>
        </table>
        
    </div>
</div>
<!--/ Basic Bootstrap Table -->
{{-- Pagination --}}
<div class="d-flex justify-content-center mt-4">
    {!! $data->links() !!}
</div>
@endsection