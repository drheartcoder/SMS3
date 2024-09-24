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
          <th>{{translation('type')}}</th>
          <th>{{translation('title')}}</th>
          <th>{{translation('start_date')}}</th>
          <th>{{translation('end_date')}}</th>
          <th>{{translation('users')}}</th>
          <th>{{translation('level')}}</th>
          <th>{{translation('class')}}</th>
        </tr>
        <?php $count=1; ?>
        @foreach($arr_data as $value)
        <tr>
          <td>{{$count++}}</td>
          <td>{{translation(strtolower($value->event_type))}}</td>
          <td>{{$value->event_title}}</td>
          <td>{{($value->event_date_from!='') && ($value->event_date_from!='0000-00-00') && ($value->event_date_from!=null) ?  date('d M Y',strtotime($value->event_date_from)) :'-'}}</td>
          <td>{{($value->event_date_to!='') && ($value->event_date_to!='0000-00-00') && ($value->event_date_to!=null) ? date('d M Y',strtotime($value->event_date_to)) :'-'}}</td>
          <td>{{$value->user_type}}</td>
          <td>{{($value->is_individual==1) && isset($value->get_level_class->level_details) ?$value->get_level_class->level_details->level_name:'-'}}</td>
          <td>{{($value->is_individual==1) && isset($value->get_level_class->class_details) ? $value->get_level_class->class_details->class_name : '-'}}</td>
        </tr>
        @endforeach
      </table>
    </main>  
    
</body>
</html>