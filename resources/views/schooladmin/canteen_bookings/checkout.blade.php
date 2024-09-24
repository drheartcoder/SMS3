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
      <i class="fa {{$view_icon}}">
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
        <h1>{{$page_title}}</h1>
    </div>
</div>

<!-- END Page Title -->

<!-- BEGIN Tiles -->
            <div class="row">
            <div class="col-md-12">
                <div class="box  box-navy_blue">
                    <div class="box-title">
                        <h3>{{-- <i class=""></i>Your Order-Checkout --}}</h3>
                    </div>
                    <div class="box-content studt-padding">
                        @include('schooladmin.layout._operation_status')
                        <form class="form-horizontal" id="frm_manage" method="POST" action="{{$module_url_path}}/store">
                           {{csrf_field()}}
                           <input type="hidden" name="user_id" value="{{$user_id}}">
                           <input type="hidden" name="user_type" value="{{$user_role}}">
                            <div class="clearfix"> </div>
                            <div class="border-box">
                                <div class="table-responsive" style="border:0">
                                    <input type="hidden" name="multi_action" value="" />
                                    <table class="table table-advance canteenorder" id="table_module">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <a>{{translation('product_name')}}</a>
                                                </th>
                                                <th>
                                                    <a>{{translation('quantity')}} </a>
                                                </th>
                                                <th>
                                                  <a>{{translation('product_price')}}</a>
                                                </th>
                                                <th>
                                                    <a>{{translation('total')}}</a>
                                                </th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                          <?php $total = 0;?>
                                            @if(isset($arr_data) && count($arr_data)>0)
                                              @foreach($arr_data as $key => $data)
                                                <?php $total = $total + $data['price']; ?>
                                                <tr role="row">
                                                    <td>
                                                      {{isset($data['get_product_details']['product_name'])?$data['get_product_details']['product_name']:''}}
                                                    </td>
                                                    <td class="sorting_1">
                                                      {{isset($data['quantity'])?$data['quantity']:''}}
                                                    </td>
                                                    <td>
                                                      {{isset($data['get_product_details']['price'])?$data['get_product_details']['price']:''}} MAD
                                                    </td>
                                                    <td>
                                                      {{isset($data['price'])?$data['price']:''}} MAD
                                                    </td>
                                                </tr>
                                              @endforeach
                                              <input type="hidden" name="total" value="{{$total}}">
                                              <tr role="row">
                                                    <td colspan="3" class="sorting_1 text-right"><b>{{translation('subtotal')}}</b></td>
                                                    <td><b>{{$total}} MAD</b></td>
                                                </tr>
                                                <tr role="row">
                                                    <td colspan="3" class="sorting_1 text-right"><b>{{translation('total_amount')}}</b></td>
                                                    <td><b>{{$total}} MAD</b></td>
                                                </tr>
                                            @endif

                                        </tbody>
                                    </table>
                                </div>

                                <div class="clearfix"></div>

                                <div class="student-cateeenorder-main-payment">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-10 col-lg-offset-1 col-md-offset-0">
                                            <div class="payment-method-redio">
                                                <div class="mrg-tpspay">
                                                    <div class="radio-btn mr-tp cash-paymnt">
                                                        <div class="paymt-radio">
                                                            <input type="radio" id="f-option" class="rdoPayment" name="selector" value="cash" onclick="show1();" checked="">
                                                            <label for="f-option"> {{translation('cash')}}</label>
                                                            <div class="check"></div>
                                                        </div>
                                                    </div>
                                                    {{-- <div class="radio-btn sectd-img">
                                                        <input type="radio" id="s-option" class="rdoPayment" name="selector" value="online" onclick="show2();">
                                                        <label for="s-option"> {{translation('online')}}</label>
                                                        <div class="check">
                                                            <div class="inside"></div>
                                                        </div>
                                                    </div> --}}
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                            <div id="div1" class="hide-block">
                                            <div class="cart-box">
                                               
                                                    <div class="first-name-block">
                                                        <div class="first-name-block">
                                                            <div class="first-txt-input">
                                                                <input name="card_type" placeholder="{{translation('card_type')}}" type="text" />
                                                            </div>
                                                        </div>
                                                        <div class="first-txt-input input-pay">
                                                            <input name="card_number" class="input-payment" placeholder="{{translation('card_number')}}" type="text" />
                                                            <img src="{{url('/')}}/images/pay.png" alt="">
                                                        </div>
                                                    </div>
                                                    <div class="first-name-block">
                                                        <div class="row">
                                                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                                <div class="first-txt-input"><input name="expiration_date" placeholder="{{translation('expiration_date')
                                                              }}" type="text" /></div>
                                                            </div>
                                                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                                <div class="first-txt-input top-space-ste"><input name="cvv" class="max-widhtfits" placeholder="CVV" type="text" />
                                                                    <div class="img-cartds"><img src="{{url('/')}}/images/credite_card.png" alt=""></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="first-name-block">
                                                        <div class="first-txt-input">
                                                            <input name="name_on_card" placeholder="{{translation('name_on_card')}}" type="text" />
                                                        </div>
                                                    </div>
                                                    <div class="first-name-block">
                                                        <div class="first-txt-input">
                                                            <input name="acceptance_of_terms_and_conditions" placeholder="{{translation('acceptance_of_terms_and_conditions')}}" type="text" />
                                                        </div>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                            <div class="clearfix"></div>
                                                    <div class="btn-d mrg-top addclass">
                                                        <button class="btn-org grays-clr" type="submit" >{{translation('pay_now')}}</button>
                                                    </div>

                                        </div>
                                    </div>

                                </div>



                                <div class="clearfix"></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- END Main Content -->
        </div>
    </div>

    

  <script type="text/javascript">
        $(document).on('click', '.rdoPayment', function(){
            var rdoPayment = $(this).val();
            if(rdoPayment=='cash'){ $('.btn-d').addClass('addclass'); }
            else{ $('.btn-d').removeClass('addclass'); }
        });
        function show1() {
            document.getElementById('div1').style.display = 'none';
            
        }
        
        function show2() {
            document.getElementById('div1').style.display = 'block';
        }
        
    
    </script>
@endsection 