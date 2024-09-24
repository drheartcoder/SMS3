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
          <th>{{translation('course')}}</th>
          <th>{{translation('homework_details')}}</th>
          <th>{{translation('added_date')}}</th>
          <th>{{translation('due_date')}}</th>
          <th>{{translation('status')}}</th>
          <th>{{translation('rejection_reason')}}</th>
          
        </tr>
        <?php $count=1; ?>
        @foreach($arr_data as $result)
        <tr>
          <td>{{$count++}}</td>
          <td>{{$result->course_name}}</td>
          <td>{{$result->description}}</td>
          <td>{{getDateFormat($result->added_date)}}</td>
          <td>{{getDateFormat($result->due_date)}}</td>
          <td>
            <?php

            if($result->status == 'COMPLETED' && $result->status_changed_by=="PROFESSOR")
            {
                $status = translation('completed');
            }
            else
            {

                if($result->status=='PENDING')
                {
                    $status = translation('pending');
                }
                else  if($result->status=='COMPLETED')
                {
                    $status = translation('completed');
                }
                else  if($result->status=='REJECTED')
                {
                    $status = translation('rejected');
                }
            }
            echo $status;
            ?>

          </td>
          <td>
            <?php

            $reason = '-';
            if($result->rejection_reason!='' && isset($result->rejection_reason))
            {
                $reason = $result->rejection_reason;
            }
            echo $reason;
            ?>

          </td>
           
        </tr>
        @endforeach
      </table>
    </main>  
    
</body>
</html>