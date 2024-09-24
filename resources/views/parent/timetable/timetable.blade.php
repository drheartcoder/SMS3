  @extends('parent.layout.master')                
@section('main_content')
 
<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="{{url('/')}}/parent/dashboard">{{translation('dashboard')}}</a>
        </li>
        <span class="divider">
            <i class="fa fa-angle-right"></i>
        </span>
        <li>
          <i class="{{$module_icon}}"></i>
          {{$module_title}}
        </li>
       
    </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="{{$module_icon}}"></i>{{$page_title}}</h1>

    </div>
</div>
<!-- END Page Title -->


            <!-- BEGIN Tiles -->
           <div class="row">
                <div class="col-md-12">
                    <div class="box  box-navy_blue">
                        <div class="box-title">
                            <h3><i class="{{$module_icon}}"></i>{{$page_title}}</h3>
                            <div class="box-tool">
                            </div>
                        </div>
                         
                         @if(!empty($arr_periods) && !empty($arr_time_table))
                        <div class="project-timetable">
                           <span> {{translation('school_timing')}}</span>  {{ $arr_periods['school_start_time'] }} - {{ $arr_periods['school_end_time'] }}
                        </div>
                        @endif
                        <div class="box-content">

                            <!-- tables inside this DIV could have draggable content -->
                        <div class="clearfix"></div>
                            <?php
                              $weekly_days =  $weekly_days;
                              $no_td = isset($period_no) ? $period_no : 1;
                             
                            ?>
                             
                            <div class="row">
                                    <div class="col-lg-12">
                                       <div class="teacher-list-section">
                                          
                                        </div>
                                        
                                        <div class="timetable-data-new timetable-section-main professor-timetable-section" style="display: block!important;"> 
                                        @include('parent.layout._operation_status')  
                                        <div class="table-responsive" style="border:1!important;">
                                          <input type="hidden" name="multi_action" value="" />
                                           @if(!empty($arr_periods) && !empty($arr_time_table))
                                           
                                          <table class="table table-advance"  id="table_module" style="border:1!important;">
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
                                              <?php $countPeriodTimimgArray  = 0; $period_start_time = $period_end_time = '00:00:00'; ?>   
                                              @if(isset($period_no) && $period_no!="")
                                                  @for($i=1; $i<=$period_no; $i++)
                                                   <?php

                                                      if(  !empty($arr_periods_timing) && $arr_periods_timing[$countPeriodTimimgArray]['period_no'] == $i){
                                                        $period_start_time = $arr_periods_timing[$countPeriodTimimgArray]['period_start_time'];
                                                        $period_end_time = $arr_periods_timing[$countPeriodTimimgArray]['period_end_time'];
                                                      }
                                                      $isBreak = !empty($arr_periods_timing)?$arr_periods_timing[$countPeriodTimimgArray]['is_break']:0;
                                                      ++$countPeriodTimimgArray;
                                                      ?>
                                                    <tr> 
                                                        <td><div class="font-time-tb">{{translation('period')}} {{$i}}</div><div class="font-time-tm-tbl">{{getTimeFormat($period_start_time)}} - {{getTimeFormat($period_end_time)}}</div></td>

                                                            @if(count($weekly_days) >0)
                                                             @foreach($weekly_days as $day => $day_full_name)
                                                              
                                                                     
                                                                     
                                                                  @if(isset($arr_holiday) && in_array($day,$arr_holiday))
                                                  
                                                                     @if($i==1)
                                                                        <td rowspan="{{$period_no}}"
                                                                        class="sunday-holiday-section"
                                                                        > {{ translation("holiday") }}</td>
                                                                     @endif
                                                                  @elseif($isBreak == 1)
                                                                  <td  class="other-holiday-section">{{ translation("break") }} </td>
                                                                  @else
                                                                   <td class="droppable_td" period-id="{{$i}}" >
                                                                       


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
                                                                              
                                                                              {{$first_name.' '.$last_name}} <br/>{{$subject_name}}
                                                                           
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

                                          @endif
                                       </div>
                                     </div>
                              </div>
                        </div>
                      </div>
                    </div>
                </div>
               </div>
          <!-- END Main Content -->
 
@endsection