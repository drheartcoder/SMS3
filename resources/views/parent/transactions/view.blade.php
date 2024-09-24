@extends('parent.layout.master')                
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="{{ url($parent_panel_slug) }}/dashboard">{{translation('dashboard')}}</a>
        </li>
        <span class="divider">
            <i class="fa fa-angle-right"></i>
        </span>
        <li> 
          <i class="{{$module_icon}}"></i>
          <a href="{{$module_url_path}}">{{$page_title}}</a>
        </li>

        <span class="divider">
          <i class="fa fa-angle-right"></i>
        </span>
            <i class="fa fa-eye"></i>
            <li class="active">{{$module_title}}</li>
    </ul>
</div>
<!-- page title -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="{{$module_icon}}"></i> {{$page_title}}</h1>
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
        
          $payment_date        = isset($arr_data['payment_date']) ? getDateFormat($arr_data['payment_date']) :"-";
          $type                = isset($arr_data['type']) ?$arr_data['type']:"-"; // CANTEEN/FEES
          $first_name          = isset($arr_data['get_student']['first_name']) ? ucfirst($arr_data['get_student']['first_name']) : '';
          $last_name           = isset($arr_data['get_student']['last_name']) ? ucfirst($arr_data['get_student']['last_name']) : '';
          $student_name        = $first_name.' '.$last_name;
          $first_name          = isset($arr_data['get_parent']['first_name']) ? ucfirst($arr_data['get_parent']['first_name']) : '';
          $last_name           = isset($arr_data['get_parent']['last_name']) ? ucfirst($arr_data['get_parent']['last_name']) : '';
          $parent_name         = $first_name.' '.$last_name;
          $payment_status      = isset($arr_data['approval_status']) ? translation(strtolower($arr_data['approval_status'])):"-";
          $amount              = isset($arr_data['amount']) ?$arr_data['amount']:"-";
          $receipt_image       = isset($arr_data['receipt_image']) ?$arr_data['receipt_image']:"-";
          $bank_name           = isset($arr_data['bank_name']) ?$arr_data['bank_name']:"-";
          $cheque_number       = isset($arr_data['cheque_no']) ?$arr_data['cheque_no']:"-";
          $account_holder_name = isset($arr_data['account_holder_name']) ?$arr_data['account_holder_name']:"-";
          $transaction_type    = isset($arr_data['transaction_type']) ?translation(strtolower($arr_data['transaction_type'])):"-";
          $order_no            = isset($arr_data['order_no']) ?$arr_data['order_no']:"-";
          $user_no             = isset($arr_data['user_no']) ?$arr_data['user_no']:"-";
      
        ?>


          <div class="clearfix"></div>
          <div class="box-content view-details-seciton-main details-section-main-block">
          @include('schooladmin.layout._operation_status')  
            <div class="row">
                <div class="col-md-12">
                 <div class="details-infor-section-block">
                        {{$page_title}}
                    </div>  
                   <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('transaction_id')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$order_no}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('payment_mode')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$transaction_type}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('payment_done_by')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$parent_name}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('user_number')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$user_no}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('payment_date')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$payment_date}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('type')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$type}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('amount')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$amount}} {{config('app.project.currency')}}
                     </div>
                     <div class="clearfix"></div>
                  </div>  

                  @if($type=='WIRE_TRANSFER')

                    <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('receipt')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        <a href='{{$module_url_path.'/download_document/'.base64_encode($arr_data['id'])}}'>{{translation('receipt')}}.jpg <i class="fa fa-download"></i> </a>  
                     </div>
                     <div class="clearfix"></div>
                    </div>

                  @elseif($type=='CHEQUE')

                    <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('bank_name')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$bank_name}}
                     </div>
                     <div class="clearfix"></div>
                    </div>

                    <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('cheque_number')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$cheque_number}}
                     </div>
                     <div class="clearfix"></div>
                    </div>

                    <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('account_holder_name')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$account_holder_name}}
                     </div>
                     <div class="clearfix"></div>
                    </div>

                  @endif

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('payment_status')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">

                        @if($payment_status=="Pending")
                          <span class="label light-orange-color ">{{translation(strtolower($payment_status))}}</span>
                        @elseif($payment_status=="Paid")
                          <span class="label green-color ">{{translation(strtolower($payment_status))}}</span>
                        @elseif($payment_status=="Rejected")
                          <span class="label light-red-color ">{{translation(strtolower($payment_status))}}</span>
                        @elseif($payment_status=="Failed")
                          <span class="label red-color ">{{translation(strtolower($payment_status))}}</span>
                        @else
                          <span class="label light-blue-color ">{{translation(strtolower($payment_status))}}</span>  
                        @endif
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  @if($payment_status=="Rejected")
                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('rejection_reason')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$reason}}
                     </div>
                     <div class="clearfix"></div>
                  </div>
                  @endif
                  @if($transaction_type=="FEES")
                  <div class="table-responsive" style="border:0" >
                    <table class="table table-advance">
                      <thead>
                        <tr>
                        <th>
                          {{translation('type')}}
                        </th>
                        <th>
                          {{translation('fee_name')}}
                        </th>
                        <th>
                          {{translation('amount')}} ({{config('app.project.currency')}})
                        </th>
                        <th>
                          {{translation('month')}}
                        </th>
                        </tr>
                      </thead>
                      <tbody>
                      
                        @foreach($arr_data['get_transactions'] as $value)
                        <tr>
                          <td>
                            @if(isset($value['fees_type']))
                              @if($value['fees_type'] == "MAIN")
                                {{translation('main_fees')}}
                              
                              @elseif($value['fees_type'] == "CLUB")
                                {{translation('club_fees')}}
                              
                              @elseif($value['fees_type'] == "BUS")
                                {{translation('bus_fees')}}
                              @endif
                            @endif  
                          </td>
                          <td>
                            @if(isset($value['fees_type']))
                              @if($value['fees_type'] == "MAIN")
                                {{ isset($value['get_main_fees']['get_fees']['title']) ? $value['get_main_fees']['get_fees']['title'] :'' }}
                              
                              @elseif($value['fees_type'] == "CLUB")
                                {{isset($value['get_club_fees']['get_club']['club_name']) ? $value['get_club_fees']['get_club']['club_name'] :''}}
                              
                              @elseif($value['fees_type'] == "BUS")
                                {{isset($value['get_bus_fees']['route_details']['transport_type']) ? ucfirst($value['get_bus_fees']['route_details']['transport_type']) :''}}
                              @endif
                            @endif  
                          </td>
                          <td>
                            {{$value['amount']}}
                          </td>
                          <td>
                            {{$value['months']}}
                          </td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>     
                  </div>  
                  @endif
                   <div class="form-group back-btn-form-block">
                     <div class="controls">
                        <a href="{{ $module_url_path }}" class="btn btn-primary" style="float: right;margin-top: 20px;" > <i class="fa fa-arrow-left"></i> {{translation('back')}} </a>
                     </div>
                  </div>

                  </div>
                </div>
            </div>
          </div>
    
    
{{-- </div> --}}
<!-- END Main Content -->

@stop

