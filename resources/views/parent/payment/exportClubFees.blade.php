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
                    <th style="color:#fff !important">{{translation('club_name')}}</th>
                    <th style="color:#fff !important">{{translation('club_id')}}</th>
                    <th style="color:#fff !important">{{translation('club_type')}}</th>
                    <th style="color:#fff !important">{{translation('club_fees')}}</th>
                    <th style="color:#fff !important">{{translation('status')}}</th>
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
                </tr>
                @endforeach
            </tbody>
        </table>
    </main>
</body>
</html>
