<html>
<head>
    <link rel="stylesheet" href="{{url('/')}}/css/export.css">
</head>
<body>
    <header>
        {{config('app.project.header')}} 
    </header> 

    <div align="center">
        {{$sheetTitle or ''}}
    </div>
    <br>
    <footer>
        {{config('app.project.footer')}} 
    </footer>
    <main>
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
        <table class="table table-advance" align="center">
            <thead>
                <tr style="background-color:#495B79">
                    <th style="color:#fff !important">{{translation('title')}}</th>
                    <th style="color:#fff !important">{{translation('frequency')}}</th>
                    <th style="color:#fff !important">{{translation('month')}}</th>
                    <th style="color:#fff !important">{{translation('optional')}}</th>
                    <th style="color:#fff !important">{{translation('amount')}}</th>
                    <th style="color:#fff !important">{{translation('status')}}</th>
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
                        YES
                        @else
                        NO
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
                        YES
                        @else
                        NO
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
                        YES
                        @else
                        NO
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
                        YES
                        @else
                        NO
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
                </tr>

                @endif
                @endforeach
            </tbody>
        </table>
    </main>
</body>
</html>
