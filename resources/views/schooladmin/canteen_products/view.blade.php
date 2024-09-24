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
        <h1><i class="{{$module_icon}}"></i> {{$module_title}}</h1>
    </div>
</div>

<!-- BEGIN Main Content -->
{{-- <div class="row"> --}}

   <div class="box {{ $theme_color }}">
      <div class="box-title">
         <h3>
            <i class="fa fa-eye"></i>
           {{$page_title}}  
         </h3>
         <div class="box-tool">
         </div>
      </div>

        <?php
        
         $product_id                = isset($arr_canteen_item['product_id']) ?$arr_canteen_item['product_id']:"-";
         $product_type              = isset($arr_canteen_item['get_product_type']['type']) ?ucwords($arr_canteen_item['get_product_type']['type']):"-";
         $product_name              = isset($arr_canteen_item['product_name']) ?ucwords($arr_canteen_item['product_name'])  :"-";
         $description               = isset($arr_canteen_item['description'])?$arr_canteen_item['description']:"-";
         $price                     = isset($arr_canteen_item['price'])?$arr_canteen_item['price']:"-";

         
        ?>
          <div class="clearfix"></div>
          <div class="box-content view-details-seciton-main details-section-main-block">
            <div class="row">
                <div class="col-md-12">
                 <div class="details-infor-section-block">
                      {{translation('details')}}
                 </div>  

                  <div class="form-group">
                    <div class="col-sm-4 col-lg-4 ">
                      @if(isset($arr_canteen_item['product_image']) && $arr_canteen_item['product_image'] != "")
                        <img src="{{url('/')}}/uploads/food_products/{{$arr_canteen_item['product_image']}}" height="150px" width="150px" style="margin: 20px 0;">
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
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('product_type')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$product_type}}
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
                     <label class="col-sm-4 col-lg-4 control-label"><b>{{translation('product_price')}} ({{config('app.project.currency')}}) </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$price}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b>{{translation('description')}} </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$description}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  
                    <div class="form-group back-btn-form-block">
                       <div class="controls">
                          <a href="{{ $module_url_path}}" class="btn btn-primary" style="float: right;margin-top: 20px;" > <i class="fa fa-arrow-left"></i> {{translation('back')}} </a>
                       </div>
                    </div>

                  </div>
                </div>
            </div>
          </div>
    
    
{{-- </div> --}}
<!-- END Main Content -->
@stop

