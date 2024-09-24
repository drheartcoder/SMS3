@extends('student.layout.master')                
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="{{ url($student_panel_slug) }}/dashboard">{{translation('dashboard')}}</a>
        </li>
        <span class="divider">
            <i class="fa fa-angle-right"></i>
        </span>
        <li> 
          <i class="{{$module_icon}}"></i>
          <a href="{{$module_url_path}}">{{$module_title}}</a>
        </li>

        <span class="divider">
          <i class="fa fa-angle-right"></i>
        </span>
            <i class="fa fa-eye"></i>
            <li class="active">{{$page_title}}</li>
    </ul>
</div>
<!-- END Breadcrumb -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="fa fa-book"></i> {{$page_title}}</h1>
    </div>
</div>

<!-- BEGIN Main Content -->
{{-- <div class="row"> --}}

   <div class="box {{ $theme_color }}">
      <div class="box-title">
         <h3>
            <i class="fa fa-eye"></i>
           {{$module_title}}  
         </h3>
         <div class="box-tool">
         </div>
      </div>

        <?php
        
          $order_no               = isset($arr_data['order_no']) ?$arr_data['order_no']:"-";

          $first_name             = isset($arr_data['get_user_details']['first_name']) ?$arr_data['get_user_details']['first_name']:"-";
          $last_name              = isset($arr_data['get_user_details']['last_name']) ?$arr_data['get_user_details']['last_name']:"-";
          $name                   = ucfirst($first_name).' '.ucfirst($last_name);

          $national_id            = isset($arr_data['get_user_details']['national_id']) ?$arr_data['get_user_details']['national_id']:"-";

          $order_date             = isset($arr_data['created_at']) ?getDateFormat($arr_data['created_at']):"-";

          $order_type             = isset($arr_data['order_type']) ?$arr_data['order_type']:"-";

          $price                  = isset($arr_data['total_price'])?$arr_data['total_price']:"-";

         
        ?>
          <div class="clearfix"></div>
          <div class="box-content view-details-seciton-main details-section-main-block">
            <div class="row">
                <div class="col-md-12">
                 <div class="details-infor-section-block">
                      {{translation('canteen_product')}}
                 </div>  

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('order_no')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$order_no}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('customer_name')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$name}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b>{{translation('national_id')}} </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$national_id}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b>{{translation('order_date')}}</b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$order_date}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b>{{translation('order_type')}} </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$order_type}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b>{{translation('delivery_status')}}</b>: </label>
                     <div class="col-sm-9 col-lg-3 controls">
                        @if(isset($arr_data['delivery_status']) && $arr_data['delivery_status'] !='')
                            {{$arr_data['delivery_status']}}
                        @endif
                     </div>
                     <div class="clearfix"></div>
                  </div>  

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b>{{translation('payment_status')}}</b>: </label>
                     <div class="col-sm-9 col-lg-3 controls">
                        @if(isset($arr_data['payment_status']) && $arr_data['payment_status'] !='')
                            {{$arr_data['payment_status']}}
                        @endif
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b>{{translation('total_amount')}} (MAD) </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$price}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">

                    <label class="col-sm-4 col-lg-4 control-label"><b>{{translation('booking_details')}}</b>: </label>
                    <div class="col-sm-4 col-lg-6 table-responsive attendance-create-table-section" style="border:0">
                     <table class="table table-advance">
                         <thead>
                            <tr style="background-color:#495B79">
                              <th style="color:#fff !important">{{translation('sr_no')}}.</th>
                              <th style="color:#fff !important">{{translation('product_name')}}</th>
                              <th style="color:#fff !important">{{translation('quantity')}}</th>
                            </tr>
                         </thead>
                         <tbody> 
                          @if(isset($arr_data['get_bookings_details']) && count($arr_data['get_bookings_details'])>0)
                            @foreach($arr_data['get_bookings_details'] as $key => $data)
                              <tr>
                                <td>
                                  {{($key+1)}}
                                </td>

                                <td>
                                  {{isset($data['product_details']['product_name'])?ucwords($data['product_details']['product_name']):''}}
                                </td>

                                <td>
                                  {{isset($data['quantity'])?ucwords($data['quantity']):''}}
                                </td>
                              </tr>
                            @endforeach
                          @endif
                         </tbody>
                     </table>
                    </div> 
                     <div class="clearfix"></div>
                  </div>

                  
                    <div class="form-group back-btn-form-block">
                       <div class="controls">
                          <a href="{{ $module_url_path}}" class="btn btn-primary" style="float: right;margin-top: 20px;" > <i class="fa fa-arrow-left"></i> Back </a>
                       </div>
                    </div>

                  </div>
                </div>
            </div>
          </div>
    
    
{{-- </div> --}}
<!-- END Main Content -->
<
@stop

