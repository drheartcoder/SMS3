@extends('schooladmin.layout.master')                
@section('main_content')
<div id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="{{ url($school_admin_panel_slug) }}/dashboard">{{translation('dashboard')}}</a>
        </li>
        <span class="divider">
            <i class="fa fa-angle-right"></i>
        </span>
        <li> 
          <i class="{{$module_icon}}"></i>
          <a href="{{$module_url_path}}">{{$module_title}}</a>
        </li>

        <span class="divider">
          <i class="fa fa-angle-right"></i>
        </span>
            <i class="fa fa-eye"></i>
            <li class="active">{{$page_title}}</li>
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
<!-- BEGIN Main Content -->
<div class="row">
  <div class="col-md-12">
    <div class="box {{ $theme_color }}">
      <div class="box-title">
        <h3>
          <i class="fa fa-eye"></i>
          {{ isset($page_title)?$page_title:"" }}
        </h3>
        <div class="box-tool">
          
        </div>
      </div>
      <div class="box-content">
          {!! Form::open([ 'url' => $module_url_path.'/generate_gradebook_for_all',
         'method'=>'POST',
         'enctype' =>'multipart/form-data',   
         'class'=>'form-horizontal', 
         'id'=>'frm_manage' 
         ]) !!}
         {{ csrf_field() }}
                               
                               <div class="row">    
                               <div class="col-md-4">
                                  <div class="tbl-txt-school">{{$school_name}}</div>
                                   <div class="table-image-school-grad">
                                      @if($school_logo!='')
                                       <img src="{{$school_logo}}" alt="" />
                                       @else
                                       <img src="{{url('/').'/images/default-old.png'}}" alt="" />
                                       @endif
                                   </div>
                               </div>
                               <div class="col-md-4">
                                    <div class="list-input-tables">
                                        <div class="imput-txt-tbl mr-none-top">{{translation('student_name')}} :</div>
                                        <div class="imput-txt-tbl-right">{{$student_name}}</div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="list-input-tables">
                                        <div class="imput-txt-tbl mr-none-top">{{translation('birth_date')}} :</div>
                                        <div class="imput-txt-tbl-right">{{$birth_date}}</div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="list-input-tables">
                                        <div class="imput-txt-tbl mr-none-top">{{translation('level')}} :</div>
                                        <div class="imput-txt-tbl-right">{{$level}} {{$class}}</div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="list-input-tables">
                                        <div class="imput-txt-tbl mr-none-top">{{translation('rank')}} :</div>
                                        <div class="imput-txt-tbl-right rank"></div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="list-input-tables">
                                        <div class="imput-txt-tbl mr-none-top">{{translation('doubling')}} :</div>
                                        <div class="imput-txt-tbl-right">{{$doubling}}</div>
                                        <div class="clearfix"></div>
                                    </div>
                                   
                               </div>
                               <div class="col-md-4">
                                   <div class="year-quartr-tx">{{translation('academic_year')}} {{$academic_year}}</div>
                                   <div class="year-quartr-tx">{{ucfirst($exam_period)}}</div>
                                   <div class="parent-details-table">
                                       <div class="list-input-tables">
                                            <div class="imput-txt-tbl mr-none-top">{{translation('mr_or_mrs')}}</div>
                                            <div class="imput-txt-tbl-right">{{$parent_name}}</div>
                                            <div class="imput-txt-tbl-right">{{$parent_address}}</div>
                                            <div class="clearfix"></div>
                                        </div>
                                   </div>
                               </div>
                               </div>
                               
                               
                                <div class="table-responsive" style="border:0">
                                    <input type="hidden" name="multi_action" value="" />
                                    <table class="table table-advance table-bordered" id="table_module">
                                        <thead>
                                            <tr>
                                                <th>
                                                     {{translation('course')}} 
                                                </th>
                                                <th>
                                                     {{translation('coefficient')}} 
                                                </th>
                                                <th colspan="5" class="padding-o-sch">
                                                  <table class="table-bordered brt-left">
                                                      <thead>
                                                          <tr>
                                                               <th class="text-center" colspan="5">{{translation('local')}} </th>
                                                          </tr>
                                                          <tr>
                                                               <th class="text-center" width="25%">{{translation('student')}} </th>
                                                               <th class="text-center" colspan="4">{{translation('class')}} </th>
                                                          </tr>
                                                          <tr>
                                                               <th class="text-center" width="25%">{{translation('average')}} </th>
                                                               <th class="text-center" width="25%">{{translation('minimum')}} </th>
                                                               <th class="text-center" width="25%">{{translation('average')}} </th>
                                                               <th class="text-center" width="25%">{{translation('maximum')}} </th>
                                                          </tr>
                                                      </thead>
                                                     
                                                  </table> 
                                                   
                                                </th>
                                                
                                                <th>
                                                   {{translation('general_appreciation_by_professor')}}
                                                </th>  
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php 
                                            $arr_current_student_marks = []; 
                                            $arr_quarterly = [];
                                            $final_array = [];
                                            $min_quarterly = 0;
                                            $max_quarterly = 0;
                                            $avg_quarterly = 0;
                                            $final_quarterly = [];
                                            $coefficinet_sum = 0;
                                            $rank=0;
                                            
                                            
                                        ?>
                                        @if(isset($courses))
                                          @foreach($courses as $course)
                                            <?php 

                                              $coefficient = get_coefficient($course['course_id']);
                                              $coefficinet_sum += $coefficient;
                                              $student_average = 0;
                                              $arr_students = [];
                                            ?>
                                            <?php 
                                                $result =get_student_average_marks($student_id,$course['course_id']);
                                            ?>
                                            <tr role="row">
                                                <td width="10%">
                                                    <div class="cuors-tlt">{{$course['get_course']['course_name']}}</div>
                                                    <div class="proprs-nm">{{get_professor_by_course($level_class_id,$course['course_id'])}}</div>

                                                </td>
                                                <td width="10%" rowspan="@if(isset($result['exams']) && count($result['exams'])>0) {{count($result['exams'])+1}}@else{{1}}@endif">
                                                  <div class="cuors-tlt">{{$coefficient}}</div>
                                                </td>
                                                <td width="10%">
                                                  <div class="cuors-tlt"></div>   
                                                </td>
                                                <td width="10%" colspan="2">
                                                  <div class="cuors-tlt"></div>
                                                </td>
                                                <td width="10%">
                                                  <div class="cuors-tlt"></div>
                                                </td>
                                                <td width="10%">
                                                  <div class="cuors-tlt"></div>
                                                </td>  
                                             
                                                <td width="30%" rowspan="@if(isset($result['exams']) && count($result['exams'])>0) {{count($result['exams'])+1}}@else{{1}}@endif">lorem ipsum dolor sit amet, consectetur adipisicing elit,  sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud</td>
                                            </tr>
                                            <?php $current_student_mark=0; ?>
                                            @if(isset($result['exams']) && count($result['exams'])>0)
                                                @foreach($result['exams'] as $exam)
                                                  <?php  
                                                    $current_student_mark += $exam['marks']; 
                                                    $temp_students_arr = json_decode($exam['students'],true);
                                                    foreach($temp_students_arr as $key=>$marks){
                                                      if(!isset($arr_students[$key])){
                                                        $arr_students[$key] = 0;
                                                      }
                                                      $arr_students[$key] += $temp_students_arr[$key];
                                                    }
                                                    
                                                  ?>
                                            <tr>
                                                <td>
                                                  {{$exam['exam_name'] or '' }}
                                                </td>
                                                <td>
                                                  {{$exam['marks'] or '' }}
                                                </td>
                                                <td colspan="2">
                                                  {{$exam['minimum'] or '' }}
                                                </td>
                                                <td>
                                                  {{$exam['average'] or '' }}
                                                </td>
                                                <td>
                                                  {{$exam['maximum'] or '' }}
                                                </td>
                                            </tr>
                                                @endforeach($exam) 
                                            @endif
                                            <?php  
                                                
                                                $avg_marks = (count($result['exams'])>0) ? $current_student_mark/count($result['exams']) : 0 ; 
                                                $arr_current_student_marks[] = $avg_marks * $coefficient;

                                                foreach($arr_students as $key=>$marks){
                                                  if(!isset($arr_students[$key])){
                                                    $arr_students[$key] = 0;
                                                  }
                                                  $temp = (count($result['exams'])>0) ? $marks/count($result['exams']) :0;
                                                  $arr_students[$key] = $temp * $coefficient; 
                                                  if(!isset($arr_quarterly[$key]['marks']))
                                                  {
                                                      $arr_quarterly[$key]['marks'] =[];
                                                  }
                                                  $arr_quarterly[$key]['marks'][] = $arr_students[$key];
                                                }
                                               
                                            ?>
                                          @endforeach
                                        @endif    
                                        <?php 

                                            $sum = array_sum($arr_current_student_marks); 
                                            $quarterly_student_avg = $coefficinet_sum>0 ? round($sum/$coefficinet_sum,2) : 0;
                                            $flag = 0;
                                              
                                            foreach($arr_quarterly as $key=>$result){
                                              if(isset($arr_quarterly[$key]['marks']))
                                              {
                                                 $temp_arr = $arr_quarterly[$key]['marks'];
                                                 $marks = $coefficinet_sum>0 ? round(array_sum($temp_arr)/$coefficinet_sum,2) : 0;
                                                 $final_quarterly[$key] = $marks;
                                              }
                                            }
                                        ?>
                                            <tr role="row">
                                                <td width="20%" colspan="2">
                                                    <div class="cuors-tlt">Quarterly Average</div>
                                                </td>
                                                <td>
                                                  {{$quarterly_student_avg}}
                                                </td>
                                                <?php

                                                    $minimum = 0;
                                                    $maximum = 0;
                                                    $average = 0;

                                                    if(count($final_quarterly)>0){
                                                      if(count($final_quarterly)==1){
                                                        $minimum = $final_quarterly;
                                                        $maximum = $final_quarterly;
                                                        $average = $final_quarterly;
                                                      }
                                                      else{
                                                        $minimum = min($final_quarterly);
                                                        $maximum = max($final_quarterly);
                                                        $average = round(array_sum($final_quarterly)/count($final_quarterly),2);
                                                      }
                                                    }

                                                ?>
                                                <td colspan="2">{{$minimum}}</td>
                                                <td>{{$average}}</td>
                                                <td colspan="2">{{$maximum}}</td>
                                                
                                            </tr>
                                            <?php 

                                                if(count($final_quarterly)>0){
                                                $final_quarterly = array_unique($final_quarterly);
                                                sort($final_quarterly);
                                                $rank ='-';
                                                if(in_array($quarterly_student_avg,$final_quarterly)){
                                                  $rank = array_search($quarterly_student_avg,$final_quarterly);
                                                  $rank = $rank+1;
                                                }
                                            }

                                            ?>
                                            <input type="rank" value="{{$rank}}" id="rank" hidden>
                                            <tr role="row">
                                                <td width="20%" colspan="2">
                                                    <div class="cuors-tlt">{{translation('number_of_periods_present')}} - <span class="count-txt-grade">{{$present}}</span></div>
                                                </td>
                                                <td width="20%" colspan="4">
                                                    <div class="cuors-tlt">{{translation('number_of_periods_absent')}} - <span class="count-txt-grade">{{$absent}}</span></div>
                                                </td>
                                                <td width="20%" colspan="3">
                                                    <div class="cuors-tlt">{{translation('number_of_periods_late')}} - <span class="count-txt-grade">{{$late}}</span></div>
                                                </td>
                                            </tr>

                                            <tr role="row">
                                              
                                                <td colspan="2">
                                                  @if(isset($fields))
                                                  @foreach($fields as  $key=>$field)
                                                    @if($field['type']=="COMPLEMENT")
                                                    <div class="list-input-tables">
                                                        
                                                          <div class="check-box">
                                                            <input type="checkbox" class="filled-in case" name="checked_record[]"  id="default_value1_{{$field['id']}}" value="{{$field['id']}}" />
                                                            <label for="default_value1_{{$field['id']}}">{{$field['default_value1']}}</label>
                                                          </div>
                                                          
                                                                                                                  
                                                    </div>
                                                    <br><br>  
                                                    <div class="list-input-tables">
                                                    <div class="check-box">
                                                            <input type="checkbox" class="filled-in case" name="checked_record[]"  id="default_value2_{{$field['id']}}" value="{{$field['id']}}" />
                                                            <label for="default_value2_{{$field['id']}}">{{$field['default_value2']}}</label>
                                                          </div>
                                                    </div>
                                                    <br><br>
                                                    <div class="list-input-tables">      
                                                           <div class="check-box">
                                                            <input type="checkbox" class="filled-in case" name="checked_record[]"  id="default_value3_{{$field['id']}}" value="{{$field['id']}}" /> 
                                                            <label for="default_value3_{{$field['id']}}">{{$field['default_value3']}}</label>
                                                          </div>
                                                    </div>      
                                                    @endif
                                                  @endforeach  
                                                  @endif
                                                </td>
                                                <td colspan="3">
                                                  @if(isset($fields))
                                                  @foreach($fields as  $key=>$field)
                                                    @if($field['type']=="WARNING")
                                                    <div class="list-input-tables">
                                                        
                                                          <div class="check-box">
                                                            <input type="checkbox" class="filled-in case" name="checked_record[]"  id="default_value1_{{$field['id']}}" value="{{$field['id']}}" />
                                                            <label for="default_value1_{{$field['id']}}">{{$field['default_value1']}}</label>
                                                          </div>
                                                          <br><br>  
                                                          <div class="check-box">
                                                            <input type="checkbox" class="filled-in case" name="checked_record[]"  id="default_value2_{{$field['id']}}" value="{{$field['id']}}" />
                                                            <label for="default_value2_{{$field['id']}}">{{$field['default_value2']}}</label>
                                                          </div>
                                                          
                                                    </div>
                                                    @endif
                                                  @endforeach  
                                                  @endif
                                                </td>
                                                <td colspan="4" class="text-right">
                                                    <div class="cuors-tlt">Classroom Council Appreciation</div>
                                                    <p class="school-gradbok-xts">Good Preformance. Keep it up!</p>
                                                     <div class="cuors-tlt princpl-cund">{{translation('principal_name')}}: <span>{{$principal_name}}</span></div>
                                                </td>
                                                
                                            </tr>
                                           
                                            <tr role="row">
                                                <td colspan="9" class="text-center">
                                                    <div class="address-tbl-school"><span>{{translation('school_address')}}: </span> {{$school_address}}</div>
                                                    <div class="school-tel-txt">
                                                        <div class="school-tel-txt-left"><span>{{translation('telephone_number')}} :</span> {{$telephone_number}}</div>
                                                        
                                                    </div>
                                                </td>
                                            </tr>
                                            
                                            
                                        </tbody>
                                    </table>
                                </div>
          {{Form::close()}}
    </div>
  </div>
</div>
</div>
<script>
var myVar = setInterval(myTimer, 1000);

function myTimer(){
  var rank = $("#rank").val();
  if(rank){
     $('.rank').text(rank); 
     stopInterval();
  }
  else{
    
  }
}

function stopInterval(){
  clearInterval(myVar);
}
  $(".level").on('change',function(){
    var level = $('.level').val();
 
    $(".level-class").empty();
       $.ajax({
          url  :"{{ $module_url_path }}/getClasses",
          type :'get',
          data :{'_token':'<?php echo csrf_token();?>','level':level},
          success:function(data){
            
                 $(".level-class").append(data);
              
          }
    });

});

 $(".level-class").on('change',function(){
    var level_class = $('.level-class').val();
 
    $("tbody").empty();
       $.ajax({
          url  :"{{ $module_url_path }}/get_students",
          type :'post',
          data :{'_token':'<?php echo csrf_token();?>','level_class':level_class},
          success:function(data){
                 if(data!=''){
                  $("#hide_row").hide();
                  $("tbody").append(data); 
                 }
                 else{
                  $("#hide_row").show();
                 }
                 
              
          }
    });

});

</script>
@stop                    


