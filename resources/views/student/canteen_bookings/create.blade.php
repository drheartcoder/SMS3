@extends('student.layout.master')    
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
          .Latest-cou-list-blo h4{padding: 40px 0;}
</style>

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
    <li>
      <i class="fa fa-home">
      </i>
      <a href="{{ url($student_panel_slug.'/dashboard') }}"> {{translation('dashboard')}}
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
        <h1><li class="{{$module_icon}}"></li> {{$module_title or ''}}</h1>
    </div>
</div>

<!-- END Page Title -->

<!-- BEGIN Tiles -->
            <div class="box  box-navy_blue">
            @include('student.layout._operation_status')
            <form method="post" onsubmit="return addLoader()"  action="{{$module_url_path}}/checkout"  class="form-horizontal" id="validation-form1" enctype ='multipart/form-data'>
              {{ csrf_field() }}

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
                                            {{isset($daily_meal['weekly_meal']['get_product_details']['price'])?$daily_meal['weekly_meal']['get_product_details']['price']:'-'}} MAD 
                                          </div>
                                      </div>
                                      <div class="clearfix"></div>
                                  </div>
                              </div>
                          @endforeach
                        @else
                              <div class="Latest-cou-list-blo">
                                  <div><h4 align="center" style="color: red">{{translation('oops')}}! {{translation('no_products_available_for_today')}}</h4></div>
                                </div>
                        @endif
                     </div>
                    </div>

                    <div class="col-sm-12 col-md-4 col-lg-4">
                        <div class="dashboard-statistics " id="cart">
                          <div class="details-infor-section-block">{{translation('view_cart')}}</div>
                              <div class="" id="cart_div">
                                  <?php $total=0;?>
                                @if(isset($arr_cart_data) && count($arr_cart_data)>0)
                                  @foreach($arr_cart_data as $key => $data)
                                 <?php $total = $total + $data['price'];?>
                                 <div class="content-list-li">
                                    <div class="img-content-prnt">
                                      @if(isset($data['get_product_details']['product_image']) && $data['get_product_details']['product_image'] != '' && file_exists($base_path.$data['get_product_details']['product_image']))
                                        <?php
                                          $image_path = $img_path.$data['get_product_details']['product_image'];
                                        ?>
                                        <img src="{{$image_path}}" alt="" />
                                      @else
                                        <img src="{{url('/')}}/images/default-old.png" alt="" />
                                      @endif
                                      </div>
                                      <div class="txts-content-prnt">
                                        <div class="product-title-listname">
                                          {{isset($data['get_product_details']['product_name'])?ucwords($data['get_product_details']['product_name']):''}}
                                        </div>
                                        <div class="main-prices">
                                          <div class="totl-pric-nm">
                                            {{translation('unit_price')}}
                                          </div>
                                          <div class="price-tlt">
                                            {{isset($data['get_product_details']['price'])?$data['get_product_details']['price']:0}} MAD
                                          </div>
                                        </div>
                                        <div class="main-prices">
                                          <div class="totl-pric-nm">
                                            {{translation('total_price')}}
                                          </div>
                                          <div class="price-tlt" id="total_{{$data['id']}}">
                                            {{isset($data['price'])?$data['price']:0}} MAD
                                          </div>
                                        </div>
                                        <div class="parent-qnty-info">
                                          <button class="guest-btn btn-plus-guest"  onClick="updateRecord(this,{{$data['id']}},'increment',{{$data['daily_meal_id']}});"  type="button" ><i class="fa fa-plus" aria-hidden="true"></i></button>

                                          <input class="guest-input" value="{{isset($data['quantity'])?$data['quantity']:0}}" type="text" disabled id="quantity_{{$data['id']}}">

                                          <button class="guest-btn btn-minus-guest" onClick="updateRecord(this,{{$data['id']}},'decrement',{{$data['daily_meal_id']}});" type="button" ><i class="fa fa-minus" aria-hidden="true"></i></button>

                                        </div>
                                        <div class="close-btnsadd">
                                          <a href="javascript:void(0)" class="closebtn-cteen" onClick="deleteRecord({{$data['id']}});"></a>
                                        </div>
                                      </div>
                                    </div>
                                 
                                  @endforeach
                                @else
                                  <div class="clearfix"></div>
                                  <div class="clearfix"></div>
                                  <div class="clearfix"></div>

                                  <div class="col-sm-6 col-md-8 col-lg-8">
                                    <div class="Latest-cou-list-blo">
                                      <div><h4 align="center" style="color: red">{{translation('no_items_available_in_cart')}}</h4></div>
                                    </div>
                                  </div>
                                @endif
                              </div>
                        </div>
                        <div id="checkout_div" hidden="true">
                          <div class="row">
                            <div class='col-sm-6 col-md-4 col-lg-4'></div>
                             <div class='col-sm-6 col-md-4 col-lg-4' style="text-align: right"><h4>{{translation('total')}} :</h4></div>
                             <div class='col-sm-6 col-md-4 col-lg-4' style="text-align: left"><span id="all_total">{{isset($total)?number_format($total,2):0}}</span> MAD</div>
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

    

<script>

$(document).ready(function(){
 <?php if(isset($arr_cart_data) && count($arr_cart_data)>0){?>
    $('#checkout_div').show();
  <?php }?>
});
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
                      $(elem).html("<i class='fa fa-cart'></i>{{translation('add_to_cart')}}");
                      $(elem).attr('disabled', false);
                      swal({
                            title: "{{translation('warning')}}",
                            text: "{{translation('data_must_be_in_range_of')}} 1 - "+data.stock,
                            icon: "warning",
                            confirmButtonText: '{{translation('ok')}}',
                            closeOnConfirm: true,
                            dangerMode: true,
                          });
                    }
                  }

        });
      }
        
    
  }  

  function updateRecord(elem,id,status,$daily_meal)
  {
      $(elem).html("<b id='spinner'><i class='fa fa-spinner fa-spin'></i></b>");
      $(elem).attr('disabled', true);
      var qty       = $('#quantity_'+id).val();
      var available = $('#available_'+$daily_meal).val();
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
                        $('#total_'+id).text(data.price+' MAD');  
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
                        $('#quantity_'+id).val(data.data);
                        $('#total_'+id).text(data.price+' MAD');  
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
    $.ajax({
                  url:"{{$module_url_path.'/delete_quantity'}}",
                  type:'POST',
                  data:{'id':id,'total_amt':total_amt,'_token':'<?php echo csrf_token();?>'},           
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
                      
                    }

          });
  }

  $('#user_type').on('change',function(){
    var role  = $('#user_type').val();
    $.ajax({
                  url:"{{$module_url_path.'/get_users'}}",
                  type:'POST',
                  data:{'role':role,'_token':'<?php echo csrf_token();?>'},           
                    success:function(data)
                    {
                      $('#users').empty();
                      $('#users').append(data);
                      $("#users").trigger("chosen:updated");
                    }

          });
  });

  function getCartData()
  {
    var cust_id     = $('#users').val(); 
    $.ajax({
                url:"{{$module_url_path.'/get_cart_data'}}",
                type:'POST',
                data:{ 'cust_id':cust_id,'_token':'<?php echo csrf_token();?>'},           
                  success:function(data)
                  {
                    $('#cart_div').empty();
                    $('#cart_div').append(data.record);
                    $('#all_total').text(data.total);
                    $('#total').val(data.total);
                    $('#checkout_div').show();
                  }

        }); 
  }
  </script> 
@endsection