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
        <h1><i class="fa {{$module_icon}}"></i>{{$module_title}}</h1>
    </div>
</div>

<!-- END Page Title -->

<!-- BEGIN Tiles -->
            <div class="row">
                <div class="col-md-12">
                    <div class="box  box-navy_blue">
                        <div class="box-title">
                            <h3><i class="fa {{$create_icon}}"></i>{{translation('add')}} {{translation('weekly_meal')}}</h3>
                            <div class="box-tool">
                            </div>
                        </div>
                        <div class="box-content">
                            @include('schooladmin.layout._operation_status')
                            <form method="POST" action="{{$module_url_path}}/store" id="validation-form1" accept-charset="UTF-8" class="form-horizontal" id="validation-form1" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <div class="col-sm-9 col-md-8 col-lg-4 controls">
                                        <select class="form-control" data-placeholder="{{translation('select')}} {{translation('day')}}" tabindex="1" data-rule-required="true" name="day" id="day">
                                            <option value="">{{translation('select')}} {{translation('day')}}</option>
                                              @if(isset($arr_days) && count($arr_days)>0)
                                                @foreach($arr_days as $key => $days)
                                                  <option value="{{isset($days)?$days:0}}">{{$days}}</option>
                                                @endforeach
                                              @endif
                                         </select>
                                         <span for="day" class="help-block">{{ $errors->first('product_type') }}</span>
                                    </div>
                                </div>                                
                                      
                                <div class="table-responsive attendance-create-table-section" style="border:0">
                                  <table class="table table-advance"  id="table_module">
                                     <thead>
                                        <tr>
                                           <th></th>
                                           <th>{{translation('sr_no')}}</th>  
                                           <th>{{translation('product_type')}}</th>
                                           <th>{{translation('product_id')}}</th>
                                           <th>{{translation('product_name')}}</th>
                                           <th>{{translation('enter_quantity')}} </th>
                                        </tr>
                                     </thead>
                                     <tbody>
                                       @if(isset($arr_canteen_products) && count($arr_canteen_products)>0)
                                          @foreach($arr_canteen_products as $key => $product)
                                            <tr>
                                              <td>
                                                    <div class="check-box">
                                                    <input type="checkbox" class="filled-in case" name="checked_record[]" data-id="{{$product['id']}}" id="mult_change_{{base64_encode($product['id'])}}" value="{{base64_encode($product['id'])}}" />
                                                    <label for="mult_change_{{base64_encode($product['id'])}}"></label>
                                                    </div>
                                              </td>
                                              <td>{{($key+1)}}</td>
                                              <td>{{isset($product['get_product_type']['type'])?ucwords($product['get_product_type']['type']):'-'}}</td>
                                              <td>{{isset($product['product_id'])?$product['product_id']:0}}</td>
                                              <td>{{isset($product['product_name'])?ucwords($product['product_name']):'-'}}</td>
                                              <td>
                                                <input type="text" name="stock[{{base64_encode($product['id'])}}]" id="stock_{{$product['id']}}"  class="form-control commonNumber" style="width: 300px" data-rule-digit="true" placeholder="1" value="1">
                                                <br>
                                              </td>
                                            </tr>
                                          @endforeach
                                       @endif
                                     </tbody>
                                  </table>
                                                            
                                  <div class="form-group">
                                      <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                                          <a href="{{$module_url_path}}" class="btn btn btn-primary">{{translation('back')}}</a>
                                            <button class="btn btn btn-primary"  id="submit_button"  type="submit">{{translation('save')}}</button>
                                      </div>
                                  </div>
                               </div>  
                                                                        
                            </form>
                        </div>
                    </div>
                </div>  
            </div>    


 <!--page specific plugin scripts-->
        <script type="text/javascript" src="{{ url('/') }}/assets/chosen-bootstrap/chosen.jquery.min.js"></script>
        <script>
        
        $("#submit_button").on("click",function(){
          var flag=0;
            $("input[type='checkbox']").each(function(){
                var id= $(this).data('id');
                $("#stock_"+id).next('span').text('');
            });

            $("input[type='checkbox']:checked").each(function(){
              var id= $(this).data('id');
            
              if($("#stock_"+id).val()<=0){
                val = "<span for='stock_"+id+"' class='help-block'>{{translation('please_enter_a_value_greater_than_or_equal_to_1')}}</span>";
                $("#stock_"+id).after(val);
                flag=1;
              }
              else{
                $("#stock_"+id).next('span').text('');
              }
            });
            
            if(flag==1){
              return false;
            }
            else{
             return true; 
            }
        });
        </script>
@endsection
