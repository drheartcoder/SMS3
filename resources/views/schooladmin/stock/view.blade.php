@extends('schooladmin.layout.master')                
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="{{ url($school_admin_panel_slug) }}/dashboard">{{translation('dashboard')}}</a>
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
        
         $product_id                = isset($arr_stock['product_id']) ?$arr_stock['product_id']:"-";
         $product_name              = isset($arr_stock['product_name']) ?ucwords($arr_stock['product_name'])  :"-";
         $quantity                  = isset($arr_stock['quantity'])?$arr_stock['quantity']:"-";
         $price                     = isset($arr_stock['price'])?$arr_stock['price']:"-";
         $total_price               = isset($arr_stock['total_price'])?$arr_stock['total_price']:"-";
         $date_created              = isset($arr_stock['date_created'])?getDateFormat($arr_stock['date_created']):"-";

         
        ?>
          <div class="clearfix"></div>
          <div class="box-content view-details-seciton-main details-section-main-block">
            <div class="row">
                <div class="col-md-12">
                 <div class="details-infor-section-block">
                      {{translation('canteen_product')}}
                 </div>  

                  <div class="form-group">
                    <div class="col-sm-4 col-lg-4 ">
                      @if(isset($arr_stock['image']) && $arr_stock['image'] != "")
                        <img src="{{url('/')}}/uploads/stock_products/{{$arr_stock['image']}}" height="150px" width="150px" style="margin: 20px 0;">
                      @else
                        <img src="{{url('/')}}/images/default-old.png" height="150px" width="150px" style="margin: 20px 0;">
                      @endif               
                    </div>
                    <div class="col-sm-9 col-lg-4 controls"></div>
                    <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('product_id')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$product_id}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b>{{translation('product_name')}} </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$product_name}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b>{{translation('date_created')}} </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$date_created}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b>{{translation('quantity')}} </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$quantity}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b>{{translation('product_price')}} (MAD) </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$price}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b>{{translation('total_price')}} (MAD) </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$total_price}}
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
@stop

