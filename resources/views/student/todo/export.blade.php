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
          <th>{{translation('todo')}}</th>
          <th>{{translation('date')}}</th>
          <th>{{translation('status')}}</th>
        </tr>
        <?php $count=1; ?>
        @foreach($arr_data as $result)
        <tr>
          <td>{{$count++}}</td>
          <td>{{$result->todo_description}}</td>
          <td>{{isset($result->created_at) && sizeof($result->created_at)>0?date("Y-m-d",strtotime($result->created_at)):'-'}}</td>
          
          <td>
            <?php

             $status = '-';
            if($result->status== 1)
            {
                $status = translation('completed'); 
            }
            elseif( $result->status ==  0)
            {
                $status = translation('pending'); 
            }
            echo $status;
            ?>

          </td>
          
        </tr>
        @endforeach
      </table>
    </main>  
    
</body>
</html>