@extends('schooladmin.layout.master')                
@section('main_content')

<link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">
<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
      <li>
          <i class="fa fa-home"></i>
          <a href="{{ url($school_admin_panel_slug.'/dashboard') }}">{{translation('dashboard')}}</a>
      </li>

      <span class="divider">
          <i class="fa fa-angle-right"></i>               
      </span> 
        
      <li>
      <i class=""></i>
      <a href="{{$module_url_path}}">{{ isset($module_title)?$module_title:"" }}</a>
      </li>

      <span class="divider">
          <i class="fa fa-angle-right"></i> 
      </span> 
      <li class="active">{{ isset($page_title)?$page_title:"" }}</li>
  </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="fa {{$module_icon}}"></i>{{$module_title}}</h1>
    </div>
</div>
<!-- END Page Title -->

<!-- BEGIN Main Content -->
<div class="row">
<div class="col-md-12">
   <div class="box {{ $theme_color }}">
      <div class="box-title pr0">
         <h3>
            <i class="fa fa-plus-circle"></i>
            {{ isset($page_title)?$page_title:"" }}
         </h3>
      </div>
      <div class="box-content">
         @include('schooladmin.layout._operation_status')  
         {!! Form::open([ 'url' => $module_url_path.'/store',
         'method'=>'POST',
         'enctype' =>'multipart/form-data',   
         'class'=>'form-horizontal', 
         'id'=>'validation-form1' 
         ]) !!}
         {{ csrf_field() }}
         <div class="col-md-12 ajax_messages">
            <div class="alert alert-danger" id="error" style="display:none;"></div>
            <div class="alert alert-success" id="success" style="display:none;"></div>
         </div>
         
         <div class="row">

            <div class="form-group">
              <div class="col-sm-9 col-md-8 col-lg-4 controls">
                <input id="date" name="date" Placeholder="{{translation('select_date')}}" value="" class="form-control datepikr" type="text" data-rule-required="true" readonly style="cursor: pointer" >                  
              </div>
            </div>
          </div>

         <br/>
         <div class="clearfix"></div>
         <div class="table-responsive attendance-create-table-section" style="border:0">
            <input type="hidden" name="multi_action" value="" />
            <table class="table table-advance"  id="table_module">
               <thead>
                  <tr>
                    @if(array_key_exists('canteen.update', $arr_current_user_access) || array_key_exists('canteen.delete', $arr_current_user_access) )                             
                       <th style="width: 18px; vertical-align: initial;">
                          <div class="check-box">
                              <input type="checkbox" class="filled-in" name="selectall" id="selectall" />
                              <label for="selectall"></label>
                          </div>
                       </th>
                     @endif
                     <th>{{translation('product_image')}}</th>
                     <th>{{translation('product_type')}}</th>
                     <th>{{translation('product_id')}}</th>
                     <th>{{translation('product_name')}}</th>
                     <th>{{translation('weekly_stock')}}</th>
                     <th>{{translation('daily_stock')}}</th>
                  </tr>
               </thead>
               <tbody id="tbody">
                 @if(isset($arr_weekly_meals) && count($arr_weekly_meals)>0)
                  @foreach($arr_weekly_meals as $key =>$weekly_meal)
                  
                    <tr>
                      <td>
                        <div class="check-box">
                            <input type="checkbox" class="filled-in case" name="checked_record[]"  id="{{base64_encode($weekly_meal['id'])}}" value="{{base64_encode($weekly_meal['id'])}}" />
                            <label for="{{base64_encode($weekly_meal['id'])}}"></label>
                        </div>
                      </td>
                      <td>
                          @if(isset($weekly_meal['get_product_details']['product_image']) && $weekly_meal['get_product_details']['product_image'] != '' && file_exists($base_path.$weekly_meal['get_product_details']['product_image']))
                              <img src="{{url('/')}}/uploads/food_products/{{$weekly_meal['get_product_details']['product_image']}}" height="40px" width="50px">
                          @else
                              <img src="{{url('/')}}/images/default_food.jpg" height="40px" width="50px">
                          @endif
                      </td>
                      <td>{{isset($weekly_meal['get_product_details']['get_product_type']['type'])?ucwords($weekly_meal['get_product_details']['get_product_type']['type']):'-'}}</td>
                      <td>{{isset($weekly_meal['get_product_details']['product_id'])?$weekly_meal['get_product_details']['product_id']:0}}</td>
                      <td>{{isset($weekly_meal['get_product_details']['product_name'])?ucwords($weekly_meal['get_product_details']['product_name']):'-'}}</td>
                      <td>{{isset($weekly_meal['stock'])?$weekly_meal['stock']:0}}</td>
                      <td>
                        <input type="text" name="stock[{{base64_encode($weekly_meal['id'])}}]" id="stock_{{$weekly_meal['id']}}" class="form-control commonNumber" style="width: 300px" data-rule-number="true" data-rule-min="0" value="{{$weekly_meal['stock']}}">
                      </td>
                    </tr>
                  @endforeach
                 @endif
               </tbody>
            </table>
            <div id="hide_row" class="alert alert-danger" style="text-align:center" @if(isset($arr_weekly_meals) && count($arr_weekly_meals)>0) hidden @endif>{{translation('no_data_available')}}</div>  
              <div class="form-group">
                  <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                      <a href="{{$module_url_path}}" class="btn btn btn-primary">{{translation('back')}}</a>
                        <button class="btn btn btn-primary" type="submit">{{translation('save')}}</button>
                  </div>
              </div>
         </div>
         {!! Form::close() !!}
      </div>
   </div>
 </div>
</div>

<script>
  $("#date").datepicker({
              todayHighlight: true,
              autoclose: true,
              format:'yyyy-mm-dd',
              startDate:new Date()
            });

  $("#date").on('changeDate',function(){
      var date = $("#date").val();
      $.ajax({
            url  :"{{ $module_url_path }}/get_weekly_meals",
            type :'POST',
            data :{'date':date ,'_token':'<?php echo csrf_token();?>'},
            success:function(data){
              $('#tbody').empty();
              if(data!=''){
                
                $('#tbody').append(data);  
                $("#hide_row").hide();
              }
              else{
                $("#hide_row").show();
              }
            }
          });
    });

  var date  = new Date();
  var today = date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate();
  $('#date').val(today);

</script>

<!-- END Main Content -->

@stop