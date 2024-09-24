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
          <th>{{translation('student_number')}}</th>
          <th>{{translation('name')}}</th>
          <th>{{translation('email')}}</th>
          <th>{{translation('national_id')}}</th>
          <th>{{translation('level')}}</th>
          <th>{{translation('class')}}</th>
          <th>{{translation('has_left')}}</th>
        </tr>
        <?php $count=1; ?>
        @foreach($arr_data as $value)
        <tr>
          <td>{{$count++}}</td>
          <td>{{$value->student_no}}</td>
          <td>{{ucwords($value->user_name)}}</td>
          <td>{{$value->email}}</td>
          <td>{{$value->national_id}}</td>
          <td>{{$value->level_name}}</td>
          <td>{{$value->class_name}}</td>
          <?php  
            $status = "";
            if($value->has_left==1)
            {
                $status = "Yes";
            }
            elseif($value->has_left==0)
            {
                $status = "No";
            }
          ?>
          <td>{{$status}}</td>
        </tr>
        @endforeach
      </table>
    </main>  
    
</body>
</html>