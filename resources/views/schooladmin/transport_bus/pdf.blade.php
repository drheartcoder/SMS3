

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
          <th>{{translation('sr_no')}} </th>
         <th>{{translation('bus_number')}} </th>
         <th>{{translation('bus_plate_number')}} </th>
         <th>{{translation('bus_capacity')}} </th>
         <th>{{translation('bus_driver_name')}} </th>
         <th>{{translation('pickup_fees')}} </th>
         <th>{{translation('drop_fees')}} </th>
        </tr>
        <?php $count=1; ?>
        @foreach($arr_data as $data)
        <?php
          $pickup_fees=0;
          $drop_fees=0;
          if(isset($data['get_bus_transports']) &&$data['get_bus_transports']!=null && count($data['get_bus_transports'])>0) {
              foreach($data['get_bus_transports'] as $value){
                  
                  if($value['transport_type']=="pickup") {
                      $pickup_fees =  $value['fees'];
                  }
                  if($value['transport_type']=="drop"){
                      $drop_fees = $value['fees'];
                  }
              }
          }  

        ?>
        <tr>
           <td>{{$count++}}</td>
           <td>{{$data['bus_no']}}</td>
          <td>{{$data['bus_plate_no']}}</td>
          <td>{{$data['bus_capacity']}}</td>
          <td>{{$data['driver_details']['first_name']." ".$data['driver_details']['last_name']}}</td>
          <td>{{$pickup_fees}}</td>
          <td>{{$drop_fees}}</td>
        </tr>
        @endforeach
      </table>
    </main>  
    
</body>
</html>