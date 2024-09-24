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
                    <th style="color:#fff !important">{{translation('transport_type')}}</th>
                    <th style="color:#fff !important">{{translation('bus_fees')}}</th>
                    <th style="color:#fff !important">{{translation('status')}}</th>
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
                </tr>
                @endforeach
            </tbody>
        </table>
    </main>
</body>
</html>
