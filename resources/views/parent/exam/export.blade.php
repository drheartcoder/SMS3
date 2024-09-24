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
       {{ $sheetTitle or ''}}
      </div>
      

      <table>
        <tr>
          <th>{{translation('sr_no')}}</th>
          <th>{{translation('exam_number')}}</th>
          <th>{{translation('exam_period')}}</th>
          <th>{{translation('exam_type')}}</th>
          <th>{{translation('course')}}</th>
          <th>{{translation('exam_date')}}</th>
          <th>{{translation('exam_time')}}</th>
          <th>{{translation('assessment_scale')}}</th>
          <th>{{translation('result')}}</th>
        </tr>
        <?php $count=1; ?>
        @foreach($arr_data as $value)
        <tr>
          <td>{{$count++}}</td>
          <td>{{$value->exam_no}}</td>
          <td>{{$value->exam_name}}</td>
          <td>{{$value->exam_type}}</td>
          <td>{{$value->course_name}}</td>
          <td>{{getDateFormat($value->exam_date)}}</td>
          <td>{{$value->exam_start_time.' - '.$value->exam_end_time}}</td>
          <td>{{$value->scale }}</td>
          <?php  
            $marks = '-';
            if($value->result!=''){
                $arr_result= json_decode($value->result,true);

                if(array_key_exists($student_id, $arr_result) && $value->exam_date<$date)  {
                    $marks = $arr_result[$student_id];
                }    
            }
                            
          ?>
          <td>{{$marks}}</td>
        </tr>
        @endforeach
      </table>
    </main>  
    
</body>
</html>