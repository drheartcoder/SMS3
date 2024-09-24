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
    .sweet-alert h2{margin: 100px 0 0;}
    .sweet-alert.showSweetAlert::before{background-image: url(../../images/warning-message-icn.png);background-repeat: no-repeat; background-position: center;}
    .Latest-cou-list-blo h4{padding: 30px 0;}
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
          <div class="box  box-navy_blue">
            
            <div class="box-content">

            @include('schooladmin.layout._operation_status')
            <form method="post" onsubmit="return addLoader()"  action="{{$module_url_path}}/checkout"  class="form-horizontal" id="validation-form1" enctype ='multipart/form-data'>
              {{ csrf_field() }}

              <div class="col-sm-12 col-md-8 col-lg-8">
                  <div class="row">
                    <div class="col-md-6 col-lg-6">
                      <div class="form-group">
                        <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('user_type')}}</label>
                        <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">
                          <div class="assignment-gray-main">
                            <select class="form-control" name="user_type" data-rule-required='true' id="user_type">
                                  <option value="">{{translation('select')}} {{translation('user_type')}} </option>
                                  <option value="{{config('app.project.role_slug.employee_role_slug')}}">{{translation('employee')}}</option>
                                  <option value="{{config('app.project.role_slug.professor_role_slug')}}">{{translation('professor')}}</option>
                                  <option value="{{config('app.project.role_slug.student_role_slug')}}">{{translation('student')}}</option>
                                  <option value="{{config('app.project.role_slug.parent_role_slug')}}">{{translation('parent')}}</option>
                            </select>
                            <span id="err_user_type" style="display: none; color: red"></span>
                        </div>
                        </div>
                    </div>
                  </div>
                  <div class="col-md-6 col-lg-6">
                     <div class="form-group">
                          <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('select')}} {{translation('user')}}</label>
                          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">  
                            <select name="users" id="users" class="form-control chosen" data-rule-required='true' onChange="getCartData();">
                                                         
                            </select>
                            <span class='help-block'>{{ $errors->first('users')}}</span>
                            <span id="err_user" style="color: red;display: none;">{{ $errors->first('users')}}</span>
                          <div class="clearfix"></div>
                          </div>
                      </div>
                    </div>
                </div>
            </div> 

                <div class="prent-pro-content"> 
                <div class="row">
                  <div class="col-sm-12 col-md-8 col-lg-8">
                    <div class="row">
                      <div class="details-infor-section-block">{{translation('canteen_products')}}</div>
                      <div class="clearfix"></div>
                      <div class="clearfix"></div><br/>
                        @if(isset($arr_daily_meal) && count($arr_daily_meal)>0)
                          @foreach($arr_daily_meal as $key => $daily_meal)

                               <div class="col-sm-6 col-md-4 col-lg-4 cart_item ">
                                  <div class="Latest-cou-list-blo">
                                      <div class="Latest-cou-img-blo">
                                        @if(isset($daily_meal['weekly_meal']['get_product_details']['product_image']) && $daily_meal['weekly_meal']['get_product_details']['product_image'] !='')
                                          
                                          <?php 
                                            $product_image = '';
                                            $product_image = resize_images_new('uploads/food_products/',$daily_meal['weekly_meal']['get_product_details']['product_image'],'500','300');
                                          ?>

                                          <img src="{{$product_image}}" class="Latest-list-img" alt=""/>

                                        @else
                                          <?php
                                          $product_image = '';
                                            $product_image = resize_images_new('images/','default-old.png','500','300');
                                          ?>

                                          <img src="{{$product_image}}" class="Latest-list-img" alt=""/>

                                        @endif


                                          <div class="view-info-over">
                                              <a  href="javascript:void(0);"
                                                  data-daily-id="{{isset($daily_meal['id'])? $daily_meal['id']:''}}"
                                                  data-id="{{isset($daily_meal['weekly_meal']['item_id'])? $daily_meal['weekly_meal']['item_id']:''}}"
                                                  data-price="{{isset($daily_meal['weekly_meal']['get_product_details']['price'])?$daily_meal['weekly_meal']['get_product_details']['price']:''}}"
                                                  data-qty="{{1}}"
                                                  data-name="{{isset($daily_meal['weekly_meal']['get_product_details']['product_name'])? $daily_meal['weekly_meal']['get_product_details']['product_name']:''}}"
                                                  data-maxqty="{{isset($daily_meal['stock'])? $daily_meal['stock']:''}}"
                                                  data-sold-stock="{{isset($daily_meal['available_stock'])? $daily_meal['available_stock']:''}}"
                                                  @if($daily_meal['available_stock']!=0)
                                                    onclick="add_to_cart(this)"
                                                  @endif
                                                  >
                                                  <i class="fa fa-cart"></i>
                                                  @if($daily_meal['available_stock']!=0)
                                                    {{translation('add_to_cart')}}
                                                  @else
                                                    {{translation('sold_out')}}
                                                  @endif</a>
                                          </div>

                                          <?php 
                                            $available = $daily_meal['available_stock'];
                                          ?>
                                          <input type="hidden" name="available" id="available_{{$daily_meal['id']}}" value="{{$available}}">
                                      </div>
                                      <div class="Latest-cou-text-blo margin-remve ">
                                          <a class="title-products">
                                            {{isset($daily_meal['weekly_meal']['get_product_details']['product_name'])?$daily_meal['weekly_meal']['get_product_details']['product_name']:'-'}}
                                          </a>
                                          
                                          <div class="address-block-new">
                                            {{isset($daily_meal['weekly_meal']['get_product_details']['description'])?$daily_meal['weekly_meal']['get_product_details']['description']:'-'}}
                                          </div>

                                          <div class="address-block-new">
                                            <b>{{translation('available_stock')}} :</b>
                                            {{isset($daily_meal['available_stock'])?$daily_meal['available_stock']:'-'}}
                                          </div>
                                          
                                          <div class="address-block-new"><i class="fa fa-clock-o"></i> 
                                            {{isset($daily_meal['weekly_meal']['get_product_details']['price'])?$daily_meal['weekly_meal']['get_product_details']['price']:'-'}} {{config('app.project.currency')}} 
                                          </div>
                                      </div>
                                      <div class="clearfix"></div>
                                  </div>
                              </div>
                          @endforeach
                        @else
                              <div class="col-sm-6 col-md-8 col-lg-8">
                                <div class="Latest-cou-list-blo">
                                  <div><h4 align="center" style="color: red">{{translation('oops')}}! {{translation('no_products_available_for_today')}}</h4></div>
                                </div>
                              </div>
                        @endif
                     </div>
                    </div>

                    <div class="col-sm-12 col-md-4 col-lg-4">
                        <div class="dashboard-statistics " id="cart">
                          <div class="details-infor-section-block">{{translation('view_cart')}}</div>
                              <div class="content-txt1 content-d pading-tb-pro" id="cart_div">
                                  <div class="Latest-cou-list-blo">
                                  <div><h4 align="center" style="color: red">{{translation('no_items_available_in_cart')}}</h4></div>
                                </div>
                              </div>
                        </div>
                        <div id="checkout_div" hidden="true">
                          <div class="row">
                             <div class='col-sm-12 col-md-12 col-lg-12' style="text-align: right"><div class="totle-title-admin-sub">{{translation('total')}} :</div> <div class="totle-title-admin-right"><span id="all_total"></span> {{config('app.project.currency')}}</div></div>
                             <input type="hidden" name="total" value="" id="total">
                          </div>
                            <div class="clearfix"></div>
                            <input type="submit" name="submit" value="{{translation('checkout')}}" class="btn btn-primary checkoutbt" id="checkout">
                        </div>
                    </div>
                  
                    <div class="clearfix"></div>
                </div>
            </div>
          </form>
      </div>
</div>
    

<script>

function add_to_cart(elem)
  {  
      
      var item_id     = $(elem).attr('data-id');
      var daily_id    = $(elem).attr('data-daily-id');
      var item_name   = $(elem).attr('data-name');
      var item_price  = $(elem).attr('data-price');
      var item_qty    = $(elem).attr('data-qty');
      var item_maxqty = $(elem).attr('data-maxqty');
      var stock_sold  = $(elem).attr('data-sold-stock');  
      var cust_id     = $('#users').val();  

      var user_role   = $('#user_type').val();
      var user        = $('#users').val();

      if(user != '' && user_role !='')
      {

        $('#err_user').text('');
        $('#err_user_type').text('');
        if(stock_sold <= 0)
        {
            swal({
                    title: "{{translation('warning')}}",
                    text: "{{translation('no_stock_available')}}",
                    icon: "warning",
                    confirmButtonText: '{{translation('ok')}}',
                    closeOnConfirm: true,
                    dangerMode: true,
                  });
            $(elem).attr('disabled',true);
        }
        else
        {
          $(elem).html("<b id='spinner'><i class='fa fa-spinner fa-spin'></i>{{translation('adding')}}...</b>");
          $(elem).attr('disabled', true);
          $.ajax({
                  url:"{{$module_url_path.'/add_to_cart'}}",
                  type:'POST',
                  data:{'item_id':item_id, 'item_price':item_price,  'daily_id':daily_id, 'item_qty' : item_qty, 'max_qty' : item_maxqty, 'stock_sold' : stock_sold, 'cust_id' : cust_id, '_token':'<?php echo csrf_token();?>'},           
                    success:function(data)
                    {
                      if(data.status == "success")
                      {
                        $('#cart_div').empty();
                        $('#cart_div').append(data.record);
                        $('#all_total').text(data.total);
                        $('#total').val(data.total);
                        $('#checkout_div').show();
                        if(data.record == '') 
                        {
                          $('#checkout').attr('disabled','true');
                        }
                        else
                        {
                          $('#checkout').removeAttr('disabled');
                        }
                        $(elem).html("<i class='fa fa-cart'></i>{{translation('add_to_cart')}}");
                        $(elem).attr('disabled', false);
                      }
                      if(data.status == 'error')
                      {
                        swal({
                              title: "{{translation('warning')}}",
                              text: "{{translation('data_must_be_in_range_of')}} 1 - "+data.stock,
                              icon: "warning",
                              confirmButtonText: '{{translation('ok')}}',
                              closeOnConfirm: true,
                              dangerMode: true,
                            });
                        $(elem).html("<i class='fa fa-cart'></i>{{translation('add_to_cart')}}");
                        $(elem).attr('disabled', false);
                      }
                    }

          });
        }
      }
      else if(user=='' && user_type!='')
      {
        $('#err_user_type').hide();
        $('#err_user').show();
        $('#err_user').text('this field is required');
      }
      else
      {
        $('#err_user').show();
        $('#err_user_type').show();
        $('#err_user').text('this field is required');
        $('#err_user_type').text('this field is required');
      }
    
  }  

  function updateRecord(elem,id,status,daily_meal)
  {
    
      $(elem).html("<b id='spinner'><i class='fa fa-spinner fa-spin'></i></b>");
      $(elem).attr('disabled', true);
      var qty       = $('#quantity_'+id).val();
      var available = $('#available_'+daily_meal).val();
      var id     = id;
      var status = status;
      var total_amt = $('#all_total').text();

      if(available == 0 || available == undefined)
      {
        
          swal({
                  title: "{{translation('warning')}}",
                  text: "{{translation('no_stock_available')}}",
                  icon: "warning",
                  confirmButtonText: '{{translation('ok')}}',
                  closeOnConfirm: true,
                  dangerMode: true,
              });

          if(status == 'increment')
          {
            $(elem).html('<i class="fa fa-plus" aria-hidden="true"></i>');
            $(elem).removeAttr('disabled');
          }
          else
          {
            $(elem).html('<i class="fa fa-minus" aria-hidden="true"></i>'); 
            $(elem).removeAttr('disabled');
          }
          return false;
      } 
        $.ajax({
                  url:"{{$module_url_path.'/update_quantity'}}",
                  type:'POST',
                  data:{'id':id,'total_amt':total_amt, 'status':status, '_token':'<?php echo csrf_token();?>'},           
                    success:function(data)
                    {
                      if(data.status == 'success')
                      {
                        $('#quantity_'+id).val(data.data);  
                        $('#total_'+id).text(data.price+' {{config('app.project.currency')}}');  
                        $('#all_total').text(data.total_price);
                        $('#total').val(data.total_price);
                        if(status == 'increment')
                        {
                          $(elem).html('<i class="fa fa-plus" aria-hidden="true"></i>');
                        }
                        else
                        {
                          $(elem).html('<i class="fa fa-minus" aria-hidden="true"></i>'); 
                        }
                        $(elem).attr('disabled', false);
                      }
                      if(data.status == 'error')
                      {
                        if(status == 'increment')
                        {
                          $(elem).html('<i class="fa fa-plus" aria-hidden="true"></i>');
                        }
                        else
                        {
                          $(elem).html('<i class="fa fa-minus" aria-hidden="true"></i>'); 
                        }
                        $(elem).attr('disabled', false);

                        $('#quantity_'+id).val(data.data);
                        $('#total_'+id).text(data.price+' {{config('app.project.currency')}}');  
                        $('#all_total').text(data.total_price);
                        $('#total').val(data.total_price);
                        swal({
                                title: "{{translation('warning')}}",
                                text: "{{translation('data_must_be_in_range_of')}} 1 - "+available,
                                icon: "warning",
                                confirmButtonText: '{{translation('ok')}}',
                                closeOnConfirm: true,
                                dangerMode: true,
                            });
                      }
                      
                    }

          });
  }

  function deleteRecord(id)
  {
    var total_amt   = $('#all_total').text();
    var cust_id     = $('#users').val();  
    $('#loader').fadeIn('slow');
    $('body').addClass('loader-active');
    $.ajax({
                  url:"{{$module_url_path.'/delete_quantity'}}",
                  type:'POST',
                  data:{'id':id,'cust_id':cust_id,'total_amt':total_amt,'_token':'<?php echo csrf_token();?>'},           
                    success:function(data)
                    {
                      if(data.status == 'success')
                      {
                        $('#cart_div').empty();
                        $('#cart_div').append(data.record);
                        $('#checkout').show();
                        $('#all_total').text(data.total);
                        $('#total').val(data.total);
                        if(data.record == '')
                        {
                          $('#checkout').attr('disabled','true');
                        }
                        else
                        {
                          $('#checkout').removeAttr('disabled');
                        }
                        
                      }
                      else
                      {
                        $('#cart_div').empty();
                        $('#cart_div').append(data.record);
                        $('#checkout').show();
                        $('#all_total').text(data.total);
                        $('#total').val(data.total);

                      }
                      $('#loader').hide();
                      $('body').removeClass('loader-active');
                    }

          });
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
                      $('#users').empty();
                      $('#users').append(data);
                      $("#users").trigger("chosen:updated");
                      $('#loader').hide();
                      $('body').removeClass('loader-active');
                    }

          });
  });

  function getCartData()
  {
    var cust_id     = $('#users').val(); 
    $('#loader').fadeIn('slow');
    $('body').addClass('loader-active');
    $.ajax({
                url:"{{$module_url_path.'/get_cart_data'}}",
                type:'POST',
                data:{ 'cust_id':cust_id,'_token':'<?php echo csrf_token();?>'},           
                  success:function(data)
                  {
                    $('#cart_div').empty();
                    $('#cart_div').append(data.record);
                    if(data.record == '')
                    {
                      $('#checkout').attr('disabled','true');
                    }
                    else
                    {
                      $('#checkout').removeAttr('disabled');
                    }
                    $('#all_total').text(data.total);
                    $('#total').val(data.total);
                    $('#checkout_div').show();

                    $('#loader').hide();
                    $('body').removeClass('loader-active');
                  }

        }); 
  }
  </script> 
@endsection