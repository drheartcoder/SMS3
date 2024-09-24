<html>
  <head>
    <link rel="stylesheet" href="{{url('/')}}/css/export.css">
  </head>
  <body>
    <header>
        {{config('app.project.header')}} 
    </header> 
    <footer>
            {{config('app.project.footer')}} 
    </footer>
    <main> 
      <div align="center">
      @if($school_logo!='')
         <img src="{{$school_logo}}" alt="" />
      @else
          <img src="{{url('/').'/images/default-old.png'}}" alt="" />
      @endif
      </div>
      <br>
      <div align="center"><b>{{$school_name}}</b></div>
      <div align="center">{{$school_address}}</div>
      <div align="center">{{$school_email}}</div><br>
      <div align="center">{{$sheetTitlePDF}}</div><br>

      <table>
        <tr>
          <th>{{translation('sr_no')}}</th>
          <th>{{translation('transaction_id')}}</th>
          <th>{{translation('payment_done_by')}}</th>
          <th>{{translation('payment_date')}}</th>
          <th>{{translation('payment_mode')}}</th>
          <th>{{translation('type')}}</th>
          <th>{{translation('status')}}</th>
          <th>{{translation('amount')}} ({{config('app.project.currency')}})</th>
        </tr>
        <?php $count=1; ?>
        @foreach($arr_data as $result)
        <tr>
          <td>{{$count++}}</td>
          <td>{{$result->order_no}}</td>
          <td>{{$result->parent_name}}</td>
          <td>{{getDateFormat($result->payment_date)}}</td>
          <td>{{translation(strtolower($result->transaction_type))}}</td>

          <td>{{translation(strtolower($result->type)) }}</td>

          <td><?php

          $status ='';
          if(isset($result->approval_status)){

              if($result->approval_status=='APPROVED'){
                  $status = translation('approved');
              }
              else if($result->approval_status=='SUCCESS'){
                  $status = translation('success');
              }
              else if($result->approval_status=='FAILED'){
                  $status = translation('failed');
              }
              else if($result->approval_status=='PENDING'){
                  $status = translation('pending');
              }
              else{
                  $status = translation('rejected');
              }
          }
           echo  $status;
          ?></td>
          <td>{{ $result->amount }}</td>
        </tr>
        @endforeach
      </table>
    </main>  
    
</body>
</html>