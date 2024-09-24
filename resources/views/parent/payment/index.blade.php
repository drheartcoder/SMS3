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
            <li class="active">{{$page_title}}</li>
        </li>
    </ul>
</div>
<!-- END Breadcrumb -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="{{$module_icon}}"></i> {{$page_title}}</h1>
    </div>
</div>
<!-- BEGIN Main Content -->

<div class="box {{ $theme_color }}">
    <div class="box-title">
        <h3>
            <i class="fa fa-list"></i>
            {{$page_title}}  
        </h3>
        <div class="box-tool">
        </div>
    </div>

    <div class="clearfix"></div>
    <div class="box-content view-details-seciton-main details-section-main-block">
        @include('parent.layout._operation_status')
        <div class="row">
            {!! Form::open([ 'url' => $module_url_path.'/checkout',
            'method'=>'POST',
            'id'=>"validation-form1" 
            ]) !!}
            {{ csrf_field() }}
            <?php 
            $datetime1 = date_create($start_date);
            $datetime2 = date_create($end_date);

            $interval = date_diff($datetime2, $datetime1);
            if($interval->format('%y')>0 ){
                $total_months = $interval->format('%y') * 12;  
            }
            else{
                $total_months = $interval->format('%m');   
            }
            $check_count = 0;
            ?>
            @if(isset($arr_fees) && count($arr_fees)>0)
            <div class="col-md-12">
                <div class="main-fees-section" style="color:#495b79">
                    <div style="display:inline">{{translation('main_fees')}} </div>
                    <div style="display:inline !important; float:right !important; font-size: 17px !important; font-weight:400;">
                        {{translation('brotherhood_benefit')}} : {{$percent_discount}} %
                    </div>
                    <div style="float: right; font-size: 15px;">
                        <div class="dropup-down-uls" style="padding: 0px 20px;">
                            <a href="javascript:void(0)" class="export-lists"><i class="fa fa-upload"></i> {{ translation('export')}} </a>
                            <div class="export-content-links">
                                <div class="li-list-a">
                                    <a href="javascript:void(0)" onclick="exportMainFeesForm('pdf');">{{translation('pdf')}}</a>
                                </div>
                                <div class="li-list-a">
                                    <a href="javascript:void(0)" onclick="exportMainFeesForm('{{config("app.project.export_file_formate")}}');">{{translation('excel')}}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="table-responsive" style="border:0" >
                    
                    <input type="hidden" name="file_format" id="file_format" value="" />
                    <input type="hidden" name="search" id="search" value="" />
                    
                    <table class="table table-advance">
                        <thead>
                            <tr style="background-color:#495B79">
                                <th style="color:#fff !important">{{translation('title')}}</th>
                                <th style="color:#fff !important">{{translation('frequency')}}</th>
                                <th style="color:#fff !important">{{translation('month')}}</th>
                                <th style="color:#fff !important">{{translation('optional')}}</th>
                                <th style="color:#fff !important">{{translation('amount')}}</th>
                                <th style="color:#fff !important">{{translation('status')}}</th>
                                <th style="color:#fff !important"></th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($arr_fees as $fee)
                            <?php 
                                $discount=0;
                                if($percent_discount !=0){
                                    $discount = $fee['amount']*$percent_discount/100;  
                                }

                                $final_amount = $fee['amount'] - $discount;

                            ?>
                                @if(isset($fee['frequency']) && $fee['frequency']=='MONTHLY')
                                    <?php  
                                    $count = 1;
                                    $current_month=date('F Y', strtotime($start_date. ' + 1 month'));
                                    $month = date('F', strtotime($start_date. ' + 1 month'));
                                    $arr_month_fees =[];
                                    foreach($fee['fees_transaction'] as $transaction){
                                        if(isset($transaction['months'])){
                                            $arr_month_fees[$transaction['months']] = $transaction['get_transaction_details']['approval_status'];

                                        }
                                    }
                                    ?>

                                    @while($count<=$total_months)
                                        <?php $paid_status = translation('unpaid');  
                                        $checked = false;

                                        if(array_key_exists($month,$arr_month_fees)){
                                            $paid_status = $arr_month_fees[$month];

                                            ($arr_month_fees[$month]);
                                            if($paid_status == 'SUCCESS' || $paid_status == 'APPROVED'){
                                                $paid_status = translation('paid'); 
                                                $checked = true; 
                                            }
                                            if($paid_status == 'FAILED'){
                                                $paid_status = translation('failed');  
                                            }
                                            if($paid_status == 'REJECTED'){
                                                $paid_status = translation('rejected');  
                                            }
                                            if($paid_status == 'PENDING'){
                                                $paid_status = translation('pending');  
                                            }
                                        }

                                        ?>
                                    <tr>
                                        <td>{{isset($fee['get_fees']['title']) ? $fee['get_fees']['title'] :'' }}</td>
                                        <td>{{isset($fee['frequency']) ? $fee['frequency'] :'' }}</td>
                                        <td>{{$current_month}}</td>
                                        <td>&nbsp;&nbsp;  
                                            @if(isset($fee['is_optional']) && $fee['is_optional']=='1')
                                            <i class="fa fa-check"></i>
                                            @else
                                            <i class="fa fa-times"></i>
                                            @endif    
                                        </td>
                                        <td>
                                            @if($final_amount!=$fee['amount'])
                                            <strike><span class="red">{{number_format($fee['amount'],2)}}</span></strike>
                                            @endif
                                            <span class="green">{{number_format($final_amount,2)}}</span>
                                        </td>  
                                        <td>
                                            @if($paid_status=="Pending")
                                            <span class="label light-orange-color ">{{$paid_status}}</span>
                                            @elseif($paid_status=="Paid")
                                            <span class="label green-color ">{{$paid_status}}</span>
                                            @elseif($paid_status=="Rejected")
                                            <span class="label light-red-color ">{{$paid_status}}</span>
                                            @elseif($paid_status=="Failed")
                                            <span class="label red-color ">{{$paid_status}}</span>
                                            @else
                                            <span class="label light-blue-color ">{{$paid_status}}</span>  
                                            @endif 
                                        </td>
                                        <td>
                                            <div class="check-box"><input type="checkbox" class="filled-in case" name="checked_main_fees[]" id="mult_change_{{$check_count}}" value="{{$fee['id']}}_{{$month}}_{{$final_amount}}" @if($checked) checked disabled @endif /><label for="mult_change_{{$check_count}}"></label></div>
                                        </td>
                                    </tr>
                                        <?php 
                                        $count++;
                                        $check_count++;
                                        $current_month=date('F Y', strtotime($current_month. '+ 1 month' )); 
                                        $month = date('F', strtotime($month. ' + 1 month'));
                                        ?>
                                    @endwhile
                                @elseif(isset($fee['frequency']) && $fee['frequency']=='BIMONTHLY')
                                    <?php  
                                    $count = 1;
                                    $current_month=date('F Y', strtotime($start_date. ' + 2 month'));
                                    $month = date('F', strtotime($start_date. ' + 2 month'));
                                    $arr_month_fees =[];
                                    foreach($fee['fees_transaction'] as $transaction){
                                        if(isset($transaction['months'])){
                                            $arr_month_fees[$transaction['months']] = $transaction['get_transaction_details']['approval_status'];
                                        }
                                    }
                                    ?>
                                    @while($count<=$total_months)
                                        <?php $paid_status = translation('unpaid');  
                                        $checked = false;
                                        if(array_key_exists($month,$arr_month_fees)){
                                            $paid_status = $arr_month_fees[$month];
                                            if($paid_status == 'SUCCESS' || $paid_status == 'APPROVED'){
                                                $paid_status = translation('paid'); 
                                                $checked = true; 
                                            }
                                            if($paid_status == 'FAILED'){
                                                $paid_status = translation('failed');  
                                            }
                                            if($paid_status == 'REJECTED'){
                                                $paid_status = translation('rejected');  
                                            }
                                            if($paid_status == 'PENDING'){
                                                $paid_status = translation('pending');  
                                            }
                                        }

                                        ?>
                                        <tr>

                                            <td>{{isset($fee['get_fees']['title']) ? $fee['get_fees']['title'] :'' }}</td>
                                            <td>{{isset($fee['frequency']) ? $fee['frequency'] :'' }}</td>
                                            <td>{{$current_month}}</td>
                                            <td>&nbsp;&nbsp;  
                                                @if(isset($fee['is_optional']) && $fee['is_optional']=='1')
                                                <i class="fa fa-check"></i>
                                                @else
                                                <i class="fa fa-times"></i>
                                                @endif    
                                            </td>
                                            <td>
                                                @if($final_amount!=$fee['amount'])
                                                <strike><span class="red">{{number_format($fee['amount'],2)}}</span></strike>
                                                @endif
                                                <span class="green">{{number_format($final_amount,2)}}</span>
                                            </td>  
                                            <td>

                                                @if($paid_status=="Pending")
                                                <span class="label light-orange-color ">{{$paid_status}}</span>
                                                @elseif($paid_status=="Paid")
                                                <span class="label green-color ">{{$paid_status}}</span>
                                                @elseif($paid_status=="Rejected")
                                                <span class="label light-red-color ">{{$paid_status}}</span>
                                                @elseif($paid_status=="Failed")
                                                <span class="label red-color ">{{$paid_status}}</span>
                                                @else
                                                <span class="label light-blue-color ">{{$paid_status}}</span>  
                                                @endif

                                            </td>

                                            <td>
                                                <div class="check-box"><input type="checkbox" class="filled-in case" name="checked_main_fees[]" id="mult_change_{{$check_count}}" value="{{$fee['id']}}_{{$month}}_{{$final_amount}}" @if($checked) checked disabled @endif /><label for="mult_change_{{$check_count}}"></label></div>
                                            </td>
                                        </tr>
                                        <?php 
                                        $count+=2;  
                                        $current_month=date('F Y', strtotime($current_month. '+ 2 month' ));
                                        $month = date('F', strtotime($month. ' + 2 month'));
                                        $check_count++;
                                        ?>
                                    @endwhile
                                @elseif(isset($fee['frequency']) && $fee['frequency']=='QUARTERLY')
                                    <?php  
                                    $count = 1;
                                    $current_month=date('F Y', strtotime($start_date. ' + 3 month'));
                                    $month = date('F', strtotime($start_date. ' + 3 month'));
                                    $arr_month_fees =[];
                                    foreach($fee['fees_transaction'] as $transaction){
                                        if(isset($transaction['months'])){
                                            $arr_month_fees[$transaction['months']] = $transaction['get_transaction_details']['approval_status'];
                                        }
                                    }
                                    ?>
                                    @while($count<=$total_months)
                                        <?php $paid_status = translation('unpaid');  
                                        $checked = false;
                                        if(array_key_exists($month,$arr_month_fees)){
                                            $paid_status = $arr_month_fees[$month];
                                            if($paid_status == 'SUCCESS' || $paid_status == 'APPROVED'){
                                                $paid_status = translation('paid'); 
                                                $checked = true; 
                                            }
                                            if($paid_status == 'FAILED'){
                                                $paid_status = translation('failed');  
                                            }
                                            if($paid_status == 'REJECTED'){
                                                $paid_status = translation('rejected');  
                                            }
                                            if($paid_status == 'PENDING'){
                                                $paid_status = translation('pending');  
                                            }
                                        }

                                        ?>
                                        <tr>
                                            <td>{{isset($fee['get_fees']['title']) ? $fee['get_fees']['title'] :'' }}</td>
                                            <td>{{isset($fee['frequency']) ? $fee['frequency'] :'' }}</td>
                                            <td>{{$current_month}}</td>
                                            <td>&nbsp;&nbsp;  
                                                @if(isset($fee['is_optional']) && $fee['is_optional']=='1')
                                                <i class="fa fa-check"></i>
                                                @else
                                                <i class="fa fa-times"></i>
                                                @endif    
                                            </td>
                                            <td>
                                                @if($final_amount!=$fee['amount'])
                                                <strike><span class="red">{{number_format($fee['amount'],2)}}</span></strike>
                                                @endif
                                                <span class="green">{{number_format($final_amount,2)}}</span>
                                            </td>  
                                            <td>

                                                @if($paid_status=="Pending")
                                                <span class="label light-orange-color ">{{$paid_status}}</span>
                                                @elseif($paid_status=="Paid")
                                                <span class="label green-color ">{{$paid_status}}</span>
                                                @elseif($paid_status=="Rejected")
                                                <span class="label light-red-color ">{{$paid_status}}</span>
                                                @elseif($paid_status=="Failed")
                                                <span class="label red-color ">{{$paid_status}}</span>
                                                @else
                                                <span class="label light-blue-color ">{{$paid_status}}</span>  
                                                @endif

                                            </td>
                                            <td>
                                                <div class="check-box"><input type="checkbox" class="filled-in case" name="checked_main_fees[]" id="mult_change_{{$check_count}}" value="{{$fee['id']}}_{{$month}}_{{$final_amount}}" @if($checked) checked disabled @endif/><label for="mult_change_{{$check_count}}"></label></div>
                                            </td>
                                        </tr>
                                        <?php 
                                        $count+=3;  
                                        $current_month=date('F Y', strtotime($current_month. '+ 3 month' ));
                                        $month = date('F', strtotime($month. ' + 3 month'));
                                        $check_count++;
                                        ?>
                                    @endwhile
                                @elseif(isset($fee['frequency']) && $fee['frequency']=='ANNUALLY')
                                    <?php $check_count++; 
                                    $current_month=date('F Y', strtotime($start_date. ' + 1 month'));
                                    $month = date('F', strtotime($start_date. ' + 1 month'));
                                    $arr_month_fees =[];
                                    foreach($fee['fees_transaction'] as $transaction){
                                        if(isset($transaction['months'])){
                                            $arr_month_fees[$transaction['months']] = $transaction['get_transaction_details']['approval_status'];
                                        }
                                    }
                                    $paid_status = translation('unpaid');
                                    $checked = false;  
                                    if(array_key_exists($month,$arr_month_fees)){
                                        $paid_status = $arr_month_fees[$month];
                                        if($paid_status == 'SUCCESS' || $paid_status == 'APPROVED'){
                                            $paid_status = translation('paid'); 
                                            $checked = true; 
                                        }
                                        if($paid_status == 'FAILED'){
                                            $paid_status = translation('failed');  
                                        }
                                        if($paid_status == 'REJECTED'){
                                            $paid_status = translation('rejected');  
                                        }
                                        if($paid_status == 'PENDING'){
                                            $paid_status = translation('pending');  
                                        }
                                    }

                                    ?>
                                    <tr>
                                        <td>{{isset($fee['get_fees']['title']) ? $fee['get_fees']['title'] :'' }}</td>
                                        <td>{{isset($fee['frequency']) ? $fee['frequency'] :'' }}</td>
                                        <td>{{$current_month}}</td>
                                        <td>&nbsp;&nbsp;  
                                            @if(isset($fee['is_optional']) && $fee['is_optional']=='1')
                                            <i class="fa fa-check"></i>
                                            @else
                                            <i class="fa fa-times"></i>
                                            @endif    
                                        </td>
                                        <td>
                                            @if($final_amount!=$fee['amount'])
                                            <strike><span class="red">{{number_format($fee['amount'],2)}}</span></strike>
                                            @endif
                                            <span class="green">{{number_format($final_amount,2)}}</span>
                                        </td>  

                                        <td>@if($paid_status=="Pending")
                                            <span class="label light-orange-color ">{{$paid_status}}</span>
                                            @elseif($paid_status=="Paid")
                                            <span class="label green-color ">{{$paid_status}}</span>
                                            @elseif($paid_status=="Rejected")
                                            <span class="label light-red-color ">{{$paid_status}}</span>
                                            @elseif($paid_status=="Failed")
                                            <span class="label red-color ">{{$paid_status}}</span>
                                            @else
                                            <span class="label light-blue-color ">{{$paid_status}}</span>  
                                            @endif
                                        </td>
                                        <td>
                                            <div class="check-box"><input type="checkbox" class="filled-in case" name="checked_main_fees[]" id="mult_change_{{$check_count}}" value="{{$fee['id']}}_{{$month}}_{{$final_amount}}" @if($checked) checked disabled @endif /><label for="mult_change_{{$check_count}}"></label></div>
                                        </td>
                                    </tr>

                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            @if(isset($arr_club_fees) && count($arr_club_fees)>0)
            <div class="col-md-12">   
                <div class="main-fees-section" style="color:#495b79; float: left;">
                    {{translation('club_fees')}}
                </div>
                <div style="float: right; font-size: 15px;">
                    <div class="dropup-down-uls" style="padding: 0px 20px;">
                        <a href="javascript:void(0)" class="export-lists"><i class="fa fa-upload"></i> {{ translation('export')}} </a>
                        <div class="export-content-links">
                            <div class="li-list-a">
                                <a href="javascript:void(0)" onclick="exportClubFeesForm('pdf');">{{translation('pdf')}}</a>
                            </div>
                            <div class="li-list-a">
                                <a href="javascript:void(0)" onclick="exportClubFeesForm('{{config("app.project.export_file_formate")}}');">{{translation('excel')}}</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>

                <div class="table-responsive" style="border:0" >
                    <table class="table table-advance">
                        <thead>
                            <tr style="background-color:#495B79">
                                <th style="color:#fff !important">{{translation('club_name')}}</th>
                                <th style="color:#fff !important">{{translation('club_id')}}</th>
                                <th style="color:#fff !important">{{translation('club_type')}}</th>
                                <th style="color:#fff !important">{{translation('club_fees')}}</th>
                                <th style="color:#fff !important">{{translation('status')}}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody> 
                            @foreach($arr_club_fees as $club)
                            <?php $check_count++; 
                                $paid_status = translation('unpaid');
                                $checked = false;

                                foreach($club['get_fees_transactions'] as $transaction){
                                    if(isset($transaction['get_transaction_details']['approval_status'])){
                                        $paid_status = $transaction['get_transaction_details']['approval_status'];
                                    }
                                }

                                if($paid_status == 'SUCCESS' || $paid_status == 'APPROVED'){
                                    $paid_status = translation('paid'); 
                                    $checked = true; 
                                }
                                if($paid_status == 'FAILED'){
                                    $paid_status = translation('failed');  
                                }
                                if($paid_status == 'REJECTED'){
                                    $paid_status = translation('rejected');  
                                }
                                if($paid_status == 'PENDING'){

                                    $paid_status = translation('pending');  
                                }

                            ?>
                            <tr>
                                <td>{{isset($club['get_club']['club_name']) ?$club['get_club']['club_name']:"-"}}</td>
                                <td>{{isset($club['get_club']['club_no']) ?$club['get_club']['club_no']:"-"}} </td>
                                <td>
                                    @if(isset($club['get_club']['is_free']))
                                    @if($club['get_club']['is_free']=='PAID')
                                    {{translation('paid')}}
                                    @else
                                    {{translation('free')}}
                                    @endif
                                    @endif
                                </td>

                                <td>{{isset($club['get_club']['club_fee']) ?$club['get_club']['club_fee']:0}} {{config('app.project.currency')}}</td>

                                <td>
                                    @if(isset($club['get_club']['is_free']) && $club['get_club']['is_free']!='FREE') @if($paid_status=="Pending")
                                    <span class="label light-orange-color ">{{$paid_status}}</span>
                                    @elseif($paid_status=="Paid")
                                    <span class="label green-color ">{{$paid_status}}</span>
                                    @elseif($paid_status=="Rejected")
                                    <span class="label light-red-color ">{{$paid_status}}</span>
                                    @elseif($paid_status=="Failed")
                                    <span class="label red-color ">{{$paid_status}}</span>
                                    @else
                                    <span class="label light-blue-color ">{{$paid_status}}</span>  
                                    @endif
                                @endif</td>

                                <td>
                                    @if(isset($club['get_club']['is_free']) && $club['get_club']['is_free']!='FREE')
                                    <div class="check-box"><input type="checkbox" class="filled-in case" name="checked_club_fees[]" id="mult_change_{{$check_count}}" value="{{$club['id']}}_{{$club['get_club']['club_fee']}}" @if($checked) checked disabled @endif /><label for="mult_change_{{$check_count}}"></label></div>
                                    @endif  
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
            @endif

            @if(isset($arr_bus_fees) && count($arr_bus_fees)>0)
            <div class="col-md-12">   
                <div class="main-fees-section" style="color:#495b79; float: left;">
                    {{translation('bus_fees')}}
                </div>
                <div style="float: right; font-size: 15px;">
                    <div class="dropup-down-uls" style="padding: 0px 20px;">
                        <a href="javascript:void(0)" class="export-lists"><i class="fa fa-upload"></i> {{ translation('export')}} </a>
                        <div class="export-content-links">
                            <div class="li-list-a">
                                <a href="javascript:void(0)" onclick="exportBusFeesForm('pdf');">{{translation('pdf')}}</a>
                            </div>
                            <div class="li-list-a">
                                <a href="javascript:void(0)" onclick="exportBusFeesForm('{{config("app.project.export_file_formate")}}');">{{translation('excel')}}</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>

                <div class="table-responsive" style="border:0" >
                    <table class="table table-advance">
                        <thead>
                            <tr style="background-color:#495B79">
                                <th style="color:#fff !important">{{translation('transport_type')}}</th>
                                <th style="color:#fff !important">{{translation('bus_fees')}}</th>
                                <th style="color:#fff !important">{{translation('status')}}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody> 
                            @foreach($arr_bus_fees as $bus)
                            <?php $check_count++; 
                                $paid_status = translation('unpaid'); 
                                $checked = false;

                                foreach($bus['get_fees_transactions'] as $transaction){
                                    if(isset($transaction['get_transaction_details']['approval_status'])){
                                        $paid_status = $transaction['get_transaction_details']['approval_status'];
                                    }
                                }

                                if($paid_status == 'SUCCESS' || $paid_status == 'APPROVED'){
                                    $paid_status = translation('paid'); 
                                    $checked = true; 
                                }
                                if($paid_status == 'FAILED'){
                                    $paid_status = translation('failed');  
                                }
                                if($paid_status == 'REJECTED'){
                                    $paid_status = translation('rejected');  
                                }
                                if($paid_status == 'PENDING'){
                                    $paid_status = translation('pending');  
                                }


                            ?>

                            <tr>
                                <td>{{isset($bus['type']) ? translation($bus['type']):"-"}}</td>
                                <td>{{isset($bus['fees_details']['fees']) ?$bus['fees_details']['fees'] :0}} {{config('app.project.currency')}}</td>
                                <td>
                                    @if($paid_status=="Pending")
                                    <span class="label light-orange-color ">{{$paid_status}}</span>
                                    @elseif($paid_status=="Paid")
                                    <span class="label green-color ">{{$paid_status}}</span>
                                    @elseif($paid_status=="Rejected")
                                    <span class="label light-red-color ">{{$paid_status}}</span>
                                    @elseif($paid_status=="Failed")
                                    <span class="label red-color ">{{$paid_status}}</span>
                                    @else
                                    <span class="label light-blue-color ">{{$paid_status}}</span>  
                                    @endif  
                                </td>
                                <td>
                                    <div class="check-box"><input type="checkbox" class="filled-in case" name="checked_bus_fees[]" id="mult_change_{{$check_count}}" value="{{$bus['id']}}_{{$bus['fees_details']['fees']}}" @if($checked) checked disabled @endif  /><label for="mult_change_{{$check_count}}"></label></div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
            @if((isset($arr_bus_fees) && count($arr_bus_fees)>0) || (isset($arr_club_fees) && count($arr_club_fees)>0) || (isset($arr_fees) && count($arr_fees)>0))
            <div class="col-md-12">  
                <div class="form-group back-btn-form-block">
                    <div class="controls">
                        <button  class="btn btn-primary" style="float: right;margin-top: 20px;" >{{translation('pay')}} </button>

                    </div>
                </div>
            </div>
            @endif
            {!! Form::close() !!}       

        </div>
    </div>
</div>
<!-- END Main Content -->

<script type="text/javascript">
    function exportMainFeesForm(file_format)
    {
        document.getElementById('file_format').value = file_format;
        var serialize_form   = $("#validation-form1").serialize();
        window.location.href = '{{ $module_url_path }}/exportMainFees?'+serialize_form+'&export=true';
    }
    function exportClubFeesForm(file_format)
    {
        document.getElementById('file_format').value = file_format;
        var serialize_form   = $("#validation-form1").serialize();
        window.location.href = '{{ $module_url_path }}/exportClubFees?'+serialize_form+'&export=true';
    }
    function exportBusFeesForm(file_format)
    {
        document.getElementById('file_format').value = file_format;
        var serialize_form   = $("#validation-form1").serialize();
        window.location.href = '{{ $module_url_path }}/exportBusFees?'+serialize_form+'&export=true';
    }
</script>

@stop