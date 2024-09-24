@extends('schooladmin.layout.master')    
@section('main_content')
<link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/chosen-bootstrap/chosen.min.css" />



<style type="text/css">
 .profile-img{width: 130px;
height: 130px;
border-radius: 50% !important;
overflow: hidden;
padding: 0;}
.profile-img img{height: 100% !important;width: 100% ;}
</style>


<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
    <li>
      <i class="fa fa-home">
      </i>
      <a href="{{ url($school_admin_panel_slug.'/dashboard') }}"> {{translation('dashboard')}}
      </a>
    </li>

    <span class="divider">
      <i class="fa fa-angle-right">
      </i>
      <i class="fa {{$create_icon}}">
      </i>
    </span> 
    <li ><a href="{{$module_url_path}}">{{ isset($module_title)?$module_title:"" }}</a>
    </li>

    <span class="divider">
      <i class="fa fa-angle-right">
      </i>
      <i class="fa {{$module_icon}}">
      </i>
    </span> 
    <li class="active">{{ isset($page_title)?$page_title:"" }}
    </li>
    
  </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
 <div class="page-title new-agetitle">
    <div>
        <h1> <i class="fa {{$module_icon}}"></i>{{$module_title}}</h1>
    </div>
</div>

<!-- END Page Title -->

<!-- BEGIN Tiles -->
            <div class="row">
                <div class="col-md-12">
                    <div class="box  box-navy_blue">
                        <div class="box-title">
                            <h3><i class="fa {{$create_icon}}"></i>{{translation('add')}} {{translation('stock_distribution')}}</h3>
                            <div class="box-tool">
                            </div>
                        </div>
                        <div class="box-content">

                            @include('schooladmin.layout._operation_status')
                            <form method="POST" action="{{$module_url_path}}/store" accept-charset="UTF-8" class="form-horizontal" id="validation-form1" enctype="multipart/form-data" onsubmit="return addLoader()">
                                {{ csrf_field() }}
                                 
                                <div class="form-group">
                                    <label class="col-sm-3 col-lg-2 control-label">{{translation('product_name')}}<i class="red">*</i></label>
                                    <div class="col-sm-9 col-lg-4 controls">
                                       <select name="product_name" id="product_name" class="form-control" data-rule-required="true" onChange="getQuantity();">
                                          <option value="">{{translation('select')}} {{translation('product')}}</option>
                                          @if(isset($arr_products) && count($arr_products)>0)
                                            @foreach($arr_products as $key => $product)
                                              <option value="{{$product['id']}}">{{ucwords($product['product_name'])}}</option>
                                            @endforeach
                                          @endif                                          
                                      </select>
                                    </div>
                                </div>
                               
                                <div class="form-group">
                                    <label class="col-sm-3 col-lg-2 control-label">{{translation('user_type')}}<i class="red">*</i></label>
                                    <div class="col-sm-9 col-lg-4 controls">
                                       <select name="user_type" id="user_type" class="form-control" data-rule-required="true">
                                          <option value="">{{translation('select')}} {{translation('user_type')}}</option>
                                          <option value="{{config('app.project.role_slug.employee_role_slug')}}">{{translation('employee')}}</option>
                                          <option value="{{config('app.project.role_slug.professor_role_slug')}}">{{translation('professor')}}</option>                                
                                      </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 col-lg-2 control-label">{{translation('user')}}<i class="red">*</i></label>
                                    <div class="col-sm-9 col-lg-4 controls">
                                       <select name="user" id="user" class="form-control" data-rule-required="true">
                                          <option value="">{{translation('select')}} {{translation('user')}}</option>                               
                                      </select>
                                    </div>
                                </div> 
                                <div class="form-group">
                                    <label class="col-sm-3 col-lg-2 control-label">{{translation('quantity')}}</label>
                                    <div class="col-sm-9 col-lg-4 controls">
                                        <input class="form-control" name="stock_quantity" id="stock_quantity" placeholder="{{translation('select_a_product')}}" type="text" data-rule-required="true" data-rule-digits="true" readonly="" style="cursor: pointer;" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 col-lg-2 control-label">{{translation('distributed')}} {{translation('quantity')}}<i class="red">*</i></label>
                                    <div class="col-sm-9 col-lg-4 controls">
                                        <input class="form-control" name="distributed_quantity" id="distributed_quantity" placeholder="{{translation('enter')}} {{translation('quantity')}}" type="text" data-rule-required="true" data-rule-digits="true" onKeyUp="availableQuantity();"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 col-lg-2 control-label">{{translation('available')}} {{translation('quantity')}}</label>
                                    <div class="col-sm-9 col-lg-4 controls">
                                        <input class="form-control" name="quantity" id="quantity" placeholder="{{translation('available')}} {{translation('quantity')}}" type="text" readonly style="cursor: pointer;">
                                    </div>
                                </div>
                                                        
                                
                                <div class="form-group">
                                    <label class="col-sm-3 col-lg-2 control-label">{{translation('distribution_date')}}<i class="red">*</i></label>
                                    <div class="col-sm-9 col-lg-4 controls">
                                        <input class="form-control datepikr" name="date" id="datepicker" placeholder="{{translation('select_distribution_date')}}" type="text" data-rule-date="true" readonly="true" style="cursor: pointer; background-color: white;" />
                                    </div>
                                </div>
                                                                   
                                <div class="form-group">
                                    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                                        <a href="{{$module_url_path}}" class="btn btn btn-primary">{{translation('back')}}</a>
                                          {{-- <input class="btn btn btn-primary" value="{{translation('save')}}" type="submit"> --}}
                                          <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('save')}}</button>
                                    </div>
                                </div>                                
                            </form>
                        </div>
                    </div>
                </div>  
            </div>    


 <script>

  $(function () {
      var date  = new Date();
      
      $("#datepicker").datepicker({
          format:'yyyy-mm-dd',
          autoclose:true,
          todayHighlight:true,
          endDate: "{{\Session::get('end_date')}}",
          startDate: "{{\Session::get('start_date')}}"
      });

      
  });

   function totalValue()
   {
     $('#total_price').val();
      var quantity   = $('#quantity').val();
      var unit_price = $('#unit_price').val();

      if(isNaN(quantity))
      {
        quantity = 0;
      }
      if(isNaN(unit_price))
      {
        unit_price = 0;
      }
      var total = quantity * unit_price;
      $('#total_price').val(total);
   }


    $('#user_type').on('change',function(){
    var role  = $('#user_type').val();

    $('#loader').fadeIn('slow');
    $('body').addClass('loader-active');

    $.ajax({
                  url:"{{$module_url_path.'/get_users'}}",
                  type:'POST',
                  data:{'role':role,'_token':'<?php echo csrf_token();?>'},           
                    success:function(data)
                    {
                      $('#user').empty();
                      $('#user').append(data);
                      $("#user").trigger("chosen:updated");
                      $('#loader').hide();
                      $('body').removeClass('loader-active');
                    }

          });
  });

  function getQuantity()
  {
    var product = $('#product_name').val();
    $('#loader').fadeIn('slow');
    $('body').addClass('loader-active');
    $.ajax({
                  url:"{{$module_url_path.'/get_quantity'}}",
                  type:'POST',
                  data:{'product':product,'_token':'<?php echo csrf_token();?>'},           
                    success:function(data)
                    {
                      $('#stock_quantity').val(data);
                      $('#distributed_quantity').attr('data-rule-max',data);
                      $('#distributed_quantity').attr('data-msg-max','{{translation("please_enter_not_more_than")}} '+data);
                      $('#loader').hide();
                      $('body').removeClass('loader-active');
                    }

          });

  }

  function availableQuantity()
  {
    var quantity      = $('#stock_quantity').val();
    var dist_quantity = $('#distributed_quantity').val();
    var available     = quantity - dist_quantity;
    if(available >0)
    {
      $('#quantity').val(available);
    }
    else
    {
      $('#quantity').val(0); 
    }
  }
 </script>
      
@endsection
