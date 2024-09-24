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

      <table>
        <tr>
          <th>{{translation('transaction_id')}}</th>
          <th>{{translation('type')}}</th>
          <th>{{translation('payment_done_by')}}</th>
          <th>{{translation('user_no')}}</th>
          <th>{{translation('payment_date')}}</th>
          <th>{{translation('payment_mode')}}</th>
          <th>{{translation('status')}}</th>
          <th>{{translation('amount')}} ({{config('app.project.currency')}})</th>
        </tr>
        <?php $count=1; ?>
        @foreach($arr_data as $value)
        <tr>
          <td>{{$value->order_no}}</td>
          <td>{{$value->type}}</td>
          <td>{{ucwords($value->parent_name)}}</td>
          <td style="text-align:center">{{$value->user_no!='' ? $value->user_no : '-'}}</td>
          <td>{{getDateFormat($value->payment_date)}}</td>
          <td>{{translation(strtolower($value->transaction_type))}}</td>
          <td>{{translation(strtolower($value->approval_status))}}</td>
          <td>{{$value->amount}}</td>
        </tr>
        @endforeach
      </table>
    </main>  
    
</body>
</html>