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
          <th>{{translation('sr_no')}}</th>
          <th>{{translation('order_no')}}</th>
          <th>{{translation('date')}}</th>
          <th>{{translation('total_amount')}} MAD</th>
          <th>{{translation('order_type')}}</th>
        </tr>
        <?php $count=1; ?>
        @foreach($arr_data as $result)
        <tr>
          <td>{{$count++}}</td>
          <td>{{$result->order_no}}</td>
          <td><?php echo  isset($result->created_at)&&$result->created_at!=''?date("Y-m-d",strtotime($result->created_at)):'-'; ?></td>
          <td>{{$result->total_price}}</td>
          <td>
          <?php

            $order_type = '-';
            if(($result->order_type)== 'ONLINE')
            {
                $order_type = translation('online'); 
            }
            elseif(($result->order_type)== 'CASH')
            {
                $order_type = translation('cash'); 
            }
            echo $order_type;
            ?>
          </td>
        </tr>
        @endforeach
      </table>
    </main>  
    
</body>
</html>