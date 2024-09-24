@extends('professor.layout.master')                
@section('main_content')
 
<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="{{url('/')}}/professor/dashboard">{{translation('professor')}}</a>
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
        <h1><i class="{{$module_icon}}"></i>{{$module_title}}</h1>

    </div>
</div>
<!-- END Page Title -->


            <!-- BEGIN Tiles -->
           <div class="row">
                <div class="col-md-12">
                    <div class="box  box-navy_blue">
                        <div class="box-title">
                            <h3><i class="{{$module_icon}}"></i>{{translation('professor')}}  {{translation('timetable')}}</h3>
                            <div class="box-tool">
                            </div>
                        </div>
                        
                        <div class="box-content">

                            <!-- tables inside this DIV could have draggable content -->
                      
                        <div class="clearfix"></div>
                            <?php
                              $weekly_days =  $weekly_days;
                              $no_td = isset($period_no) ? $period_no : 1;
                              $days = [];
                            ?>
                             
                            <div class="row">
                            <div class="col-lg-12">
                               <div class="teacher-list-section">
                                  {{translation('timetable')}}
                                </div>
                                <div class="timetable-data-new timetable-section-main professor-timetable-section" style="display: block!important;"> 
                                   @include('professor.layout._operation_status')  
                                <div class="table-responsive" style="border:1!important;">
                                  <input type="hidden" name="multi_action" value="" />

                                  @if(isset($arr_time_table) && sizeof($arr_time_table)>0)
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
                                   @if(isset($period_no) && $period_no!="")
                                      @for($i=1; $i<=$period_no; $i++)
                                        <tr> 
                                            <td>{{translation('period')}} {{$i}}</td>
                                              @if(count($weekly_days) >0)
                                               @foreach($weekly_days as $day => $day_full_name)
                                                 <td class="droppable_td" period-id="{{$i}}" >
                                                  @if(isset($arr_replaced_lectures) && count($arr_replaced_lectures)>0)
                                                   @foreach($arr_replaced_lectures as $lecture)
                                                      @if(isset($lecture['day']) && $lecture['day']==strtoupper($day) && isset($lecture['period_no']) && $lecture['period_no']==$i)
                                                          <?php 
                                                            if(!in_array(strtoupper($day), $days))
                                                            {
                                                                array_push($days,strtoupper($day));
                                                            } 
                                                          ?>
                                                          <div class="seperate_subjects" style="color: #1275ed">
                                                            {{$lecture['level_class_details']['level_details']['level_name'] or ''}} {{$lecture['level_class_details']['class_details']['class_name'] or ''}}
                                                            <br/>
                                                            {{isset($lecture['start_time'])?getTimeFormat($lecture['start_time']):''}} - {{isset($lecture['end_time'])?getTimeFormat($lecture['end_time']):''}}
                                                          </div>
                                                      @endif
                                                   @endforeach
                                                  @endif
                                                  @if(isset($arr_time_table) && sizeof($arr_time_table)>0)
                                                  
                                                    @foreach($arr_time_table as $key => $timetable)
                                                          @if(isset($timetable['day']) && $timetable['day']==strtoupper($day) && 
                                                              isset($timetable['periods_no']) && $timetable['periods_no']==$i
                                                            )

                                                            <div class="seperate_subjects">
                                                            <?php 
                                                              if(!empty($timetable['class_details'])  && isset($timetable['class_details']['class_name']) && $timetable['class_details']['class_name']!=''){
                                                                $class_name = $timetable['class_details']['class_name'];
                                                              }

                                                              if(!empty($timetable['level_details'])  && isset($timetable['level_details']['level_name']) && $timetable['level_details']['level_name']!=''){
                                                                $level_name = $timetable['level_details']['level_name'];
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
                                                            
                                                           {{$level_name}}  {{ $class_name }}  <br/>{{$subject_name}}
                                                           <br/>
                                                           {{isset($timetable['period_start_time'])?getTimeFormat($timetable['period_start_time']):'' }} - {{isset($timetable['period_end_time'])?getTimeFormat($timetable['period_end_time']):''}}
                                                            </div>
                                                          @endif
                                                    @endforeach
                                                  @endif
                                                </td>
                                        
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