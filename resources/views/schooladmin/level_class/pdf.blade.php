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

      <table align="center">
        <tr>
          <th>{{translation('sr_no')}}</th>
          <th>{{translation('class_name')}}</th>
        </tr>
        <?php $count=1; ?>
        @foreach($arr_data as $value)
        <tr>
          <td>{{$count++}}</td>
          <td>{{$value->class_name}}</td>
        </tr>
        @endforeach
      </table>
    </main>  
    
</body>
</html>