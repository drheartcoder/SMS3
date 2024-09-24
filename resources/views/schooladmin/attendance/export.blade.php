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
      <div align="center">
        <div class="form-group">
          <div class="row">
            
              @if($start_date!= '' && $end_date != '')
                <label class="col-sm-3 col-lg-4 control-label"><b>{{translation('date')}}</b> </label>
                <div class="col-sm-6 col-lg-8 controls">
                  {{$start_date}} - {{$end_date}}
                </div>
              @elseif($start_date!='' && $end_date=='')
                <label class="col-sm-3 col-lg-4 control-label"><b>{{translation('date')}}</b> </label>
                <div class="col-sm-6 col-lg-8 controls">
                  {{date('d-m-Y',strtotime($start_date))}} - {{date('d-m-Y')}}
                </div>
              @elseif($start_date== '' && $end_date!='')
                <label class="col-sm-3 col-lg-4 control-label"><b>{{translation('date')}}</b> </label>
                <div class="col-sm-6 col-lg-8 controls">
                  @if(isset($data_attendance) && !empty($data_attendance))
                    {{date('d-m-Y',strtotime($data_attendance[0]['date']))}}@endif - {{date('d-m-Y')}}  
                </div>
              @else
                <div class="col-sm-6 col-lg-8 controls">
                  {{translation('overall_attendance_report_for')}} {{translation($role)}}
                </div>          
              @endif
          </div>
        </div>
      </div>
      <table align="center">
        <thead>
            <tr>
              <th>{{translation('sr_no')}} </th>
               <th>{{translation('name')}} </th>
               <th>{{translation($role)}} {{translation('number')}} </th>
               <th>{{translation('present_days')}} </th>
               <th>{{translation('absent_days')}} </th>
               <th>{{translation('late_days')}} </th>
               <th>{{translation('total_days')}} </th>
            </tr>
         </thead>
         <tbody id="tbody">
          <?php  $total = $key = $val = 0 ; ?>
          @if(isset($arr_details) && !empty($arr_details))

            @foreach($arr_details as $key_val => $details)
            <?php $no = $no2 = $no3 = $calculate = $calculate2 = $calculate3= 0; ?>
              @if(isset($details['get_user_details']['first_name']) && !empty($details['get_user_details']['first_name']))
                  <tr>
                    <td>{{(++$val)}}</td>
                    <td>{{ucfirst($details['get_user_details']['first_name'])}} {{ucfirst($details['get_user_details']['last_name'])}}</td>
                    @if($role == config('app.project.role_slug.professor_role_slug'))
                      <td>{{ucfirst($details['professor_no'])}} </td>
                    @elseif($role == config('app.project.role_slug.employee_role_slug'))
                      <td>{{ucfirst($details['employee_no'])}} </td>
                    @endif
                      @if(isset($data_attendance) && !empty($data_attendance))
                        
                        @foreach($data_attendance as $key => $attendance)
                          <?php
                            if(isset($attendance['attendance']) && !empty($attendance['attendance']))
                            {
                              $attendance = json_decode($attendance['attendance'],true);
                            }
                          ?>
                          @if(array_key_exists($details['user_id'], $attendance))
                            
                          
                            @if($attendance[$details['user_id']] == 'present')
                              <?php $no +=1;?>

                            @elseif($attendance[$details['user_id']] == 'absent')
                              <?php $no2 +=1;?>

                            @elseif($attendance[$details['user_id']] == 'late')
                              <?php $no3 +=1;?>
                            @endif
                          @endif

                          <?php  $key++; ?>

                        @endforeach
                      @endif
                      <?php $total=$no+$no2+$no3; ?>
                      @if($total != 0)
                         <?php 

                              $calculate  = round(($no/$total)*100);
                               $calculate2 = round(($no2/$total)*100);
                               $calculate3 = round(($no3/$total)*100);
                         ?>
                      @endif
                        <td>
                          {{$no}} ({{$calculate}}%)
                        </td>
                         <td>
                          {{$no2}} ({{$calculate2}}%)
                        </td>
                         <td>
                          {{$no3}} ({{$calculate3}}%)
                        </td>
                         <td>
                          {{$no+$no2+$no3}}
                        </td>
                      
                      
                  </tr>
              @endif
             
            @endforeach

          @else
            <tr>
              <td colspan="5"><div class="alert alert-warning">{{translation('no_data_available')}}</div></td>
          @endif
         </tbody>
      </table>
    </main>  
    
</body>
</html>