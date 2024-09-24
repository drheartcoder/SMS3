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
         <div align="center">{{$school_email}}</div>
         <br>
         <table>
            <thead>
               <tr>
                  <th>&nbsp;</th>
                  @if(count($weekly_days) >0)
                  @foreach($weekly_days as $day => $day_full_name)
                  <th>{{ translation(strtolower($day)) }}</th>
                  @endforeach
                  @endif
               </tr>
            </thead>
            <tbody>
               <?php $countPeriodTimimgArray  = 0;
                  $period_start_time = $period_end_time = '00:00:00'; 
                  
                  ?>
               @if(isset($session_num_of_periods) && $session_num_of_periods!="")
               @for($i=1; $i<=$session_num_of_periods; $i++)
               <?php
                  if($arr_periods_timing[$countPeriodTimimgArray]['period_no'] == $i){
                    $period_start_time = $arr_periods_timing[$countPeriodTimimgArray]['period_start_time'];
                    $period_end_time = $arr_periods_timing[$countPeriodTimimgArray]['period_end_time'];
                  }
                  $isBreak = $arr_periods_timing[$countPeriodTimimgArray]['is_break'];
                  ++$countPeriodTimimgArray;
                  ?>
               <tr>
                  <td>
                     <div>{{translation('period')}} {{$i}} </div>
                     <div>{{getTimeFormat($period_start_time)}} - {{getTimeFormat($period_end_time)}}</div>
                  </td>
                  @if(count($weekly_days) >0)
                  @foreach($weekly_days as $day => $day_full_name)
                  @if(isset($arr_holiday) && in_array($day,$arr_holiday))
                  @if($i==1)
                  <td rowspan="{{$session_num_of_periods}}" class="holiday" >
                     {{ translation("holiday") }} 
                  </td>
                  @endif
                  @elseif($isBreak == 1)
                  <td  class="break">
                     {{ translation("break") }} 
                  </td>
                  @else 
                  <td class="droppable_td" period-id="{{$i}}" period-day="{{$day}}" period-start-time="{{$period_start_time}}" period-end-time="{{$period_end_time}}" > 
                     @if(isset($arr_time_table) && sizeof($arr_time_table)>0)
                     @foreach($arr_time_table as $key => $timetable)
                     @if(isset($timetable['day']) && $timetable['day']==strtoupper($day) && 
                     isset($timetable['periods_no']) && $timetable['periods_no']==$i
                     )
                     <?php 
                        $middle_name = $first_name  = $last_name  ='';
                        if(isset($timetable['user_details']['first_name']) && $timetable['user_details']['first_name']!="")
                        {
                          $first_name = ucfirst($timetable['user_details']['first_name']);
                        
                        }
                        
                        if(isset($timetable['user_details']['last_name']) && $timetable['user_details']['last_name']!="")
                        {
                          $last_name = ucfirst($timetable['user_details']['last_name']);
                        
                        }
                        if(isset($timetable['professor_subjects']['course_name']) && $timetable['professor_subjects']['course_name']!="")
                        {
                          $subject_name = $timetable['professor_subjects']['course_name'];
                        }
                        else
                        {
                          $subject_name = "NA";
                        }
                        
                        ?>
                     {{$first_name.' '.$last_name}} <br/><span style="font-size:8px;">{{$subject_name}}</span>
                    
                     @endif
                     @endforeach
                     @endif
                  </td>
                  @endif
                  @endforeach
                  @endif
               </tr>
               @endfor
               @endif
            </tbody>
         </table>
      </main>
   </body>
</html> 