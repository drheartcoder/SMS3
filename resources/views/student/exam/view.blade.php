@extends('student.layout.master')                
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="{{$student_panel_slug.'/dashboard'}}">{{translation('dashboard')}}</a>
        </li>
        <span class="divider">
            <i class="fa fa-angle-right"></i>
        </span>
        <li> 
          <i class="{{$module_icon}}"></i>
          <a href="{{$module_url_path}}">{{$page_title}}</a>
        </li>

        <span class="divider">
          <i class="fa fa-angle-right"></i>
        </span>
            <i class="fa fa-eye"></i>
            <li class="active">{{$module_title}}</li>
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

<!-- BEGIN Main Content -->

   <div class="box {{ $theme_color }}">
      <div class="box-title">
         <h3>
            <i class="fa fa-eye"></i>
           {{$module_title}}  
         </h3>
         <div class="box-tool">
         </div>
      </div>
      
        <?php
          
          $level                     = isset($arr_data['get_level_class']['level_details']['level_name']) ?$arr_data['get_level_class']['level_details']['level_name']:"-";
          $class                     = isset($arr_data['get_level_class']['class_details']['class_name']) ?$arr_data['get_level_class']['class_details']['class_name']:"-";
          $exam_period               = isset($arr_data['get_exam_period']['exam_name']) ?$arr_data['get_exam_period']['exam_name']:"-";
          $exam_type                 = isset($arr_data['get_exam_type']['exam_type']) ?$arr_data['get_exam_type']['exam_type']:"-";
          $assessment_scale          = isset($arr_data['get_assessment']['scale']) ? $arr_data['get_assessment']['scale'] :"-";
          $supervisor_first_name     = isset($arr_data['get_supervisor']['first_name']) ?$arr_data['get_supervisor']['first_name']:"";
          $supervisor_last_name      = isset($arr_data['get_supervisor']['last_name']) ?$arr_data['get_supervisor']['last_name']:"";
          $supervisor_name           = $supervisor_first_name.' '.$supervisor_last_name;
          $supervisor_national_id    = isset($arr_data['get_supervisor']['national_id']) ?$arr_data['get_supervisor']['national_id']:"-";
          $course                    = isset($arr_data['get_course']['course_name']) ?$arr_data['get_course']['course_name']:"-";
          $exam_name                 = isset($arr_data['exam_name']) ?$arr_data['exam_name']:"-";
          $exam_added_by_first_name  = isset($arr_data['exam_added_by']['first_name']) ?$arr_data['exam_added_by']['first_name']:"";
          $exam_added_by_last_name   = isset($arr_data['exam_added_by']['last_name']) ?$arr_data['exam_added_by']['last_name']:"";
          $exam_added_by_name        = $exam_added_by_first_name.' '.$exam_added_by_last_name;
          $exam_added_by_national_id = isset($arr_data['exam_added_by']['national_id']) ?$arr_data['exam_added_by']['national_id']:"-";
          $exam_description          = isset($arr_data['exam_description']) ?$arr_data['exam_description']:"-";
          $exam_date                 = isset($arr_data['exam_date']) ? getDateFormat($arr_data['exam_date']):"-";
          $exam_start_time           = isset($arr_data['exam_start_time']) ?$arr_data['exam_start_time']:"-";
          $exam_end_time             = isset($arr_data['exam_end_time']) ?$arr_data['exam_end_time']:"-";
          $exam_time                 =  $exam_start_time.' - '.$exam_end_time;
          $status                    = isset($arr_data['status']) ?$arr_data['status']:"-";
          $place_type                = isset($arr_data['place_type']) ?$arr_data['place_type']:"-";
          $building                  = isset($arr_data['room_assignment']['get_room_management']['tag_name']) ?$arr_data['room_assignment']['get_room_management']['tag_name']:"-";
          $floor                     = isset($arr_data['room_assignment']['get_room_management']['floor_no']) ?$arr_data['room_assignment']['get_room_management']['floor_no']:"-";
          $room_no                   = isset($arr_data['room_assignment']['room_no']) ?$arr_data['room_assignment']['room_no']:"-";
          $room_name                 = isset($arr_data['room_assignment']['room_name']) ?$arr_data['room_assignment']['room_name']:"-";
          $place_name                = isset($arr_data['place_name']) ?$arr_data['place_name']:"-";
          $academic_year             = isset($arr_data['get_academic_year']['academic_year']) ?$arr_data['get_academic_year']['academic_year']:"-";
        ?>
          <div class="clearfix"></div>
          <div class="box-content view-details-seciton-main details-section-main-block">
            <div class="row">
                <div class="col-md-12">    
                    <div class="row">
                      <div class="col-md-4">
                          <div class="details-infor-section-block">
                              {{translation('exam_details')}}
                          </div>
                            <div class="form-group">
                               <label class="control-label"><b>  {{translation('exam_name')}}  </b>: </label>
                               <div class="controls">
                                 {{$exam_name}}
                               </div>
                               <div class="clearfix"></div>
                            </div>

                            <div class="form-group">
                               <label class="control-label"><b> {{translation('exam_period')}}  </b>: </label>
                               <div class="controls">
                                  {{$exam_period}}
                               </div>
                               <div class="clearfix"></div>
                            </div>

                            <div class="form-group">
                               <label class="control-label"><b> {{translation('exam_type')}}   </b>: </label>
                               <div class="controls">
                                 {{$exam_type}}
                               </div>
                               <div class="clearfix"></div>
                            </div>
                            
                            <div class="form-group">
                               <label class="control-label"> <b> {{translation('supervisor')}} </b> :</label>
                               <div class="controls">
                                 {{$supervisor_name}}
                               </div>
                               <div class="clearfix"></div>
                            </div>

                            <div class="form-group">
                               <label class="control-label"><b> {{translation('description')}}  </b>: </label>
                               <div class="controls">
                                  {{$exam_description}}
                               </div>
                               <div class="clearfix"></div>
                            </div>
                      </div>
                      <div class="col-md-4">

                              <div class="details-infor-section-block">
                                  {{translation('exam_time_and_course')}}
                              </div>
                              <div class="form-group">
                               <label class="control-label"><b> {{translation('course')}}  </b>: </label>
                               <div class="controls">
                                  {{$course}}
                               </div>
                               <div class="clearfix"></div>
                            </div>

                            <div class="form-group">
                               <label class="control-label"> <b>{{translation('assessment_scale')}}</b> :</label>
                               <div class="controls">
                                  {{$assessment_scale}}
                               </div>
                               <div class="clearfix"></div>
                            </div>

                            <div class="form-group">
                               <label class="control-label"> <b>{{translation('exam_date')}}</b> :</label>
                               <div class="controls">
                                  {{$exam_date}}
                               </div>
                               <div class="clearfix"></div>
                            </div>
                            <div class="form-group">
                               <label class="control-label"> <b>{{translation('exam_time')}}</b> :</label>
                               <div class="controls">
                                  {{$exam_time}}
                               </div>
                               <div class="clearfix"></div>
                            </div>
                            
                            

                      </div>
                      <div class="col-md-4">

                          <div class="details-infor-section-block">
                              {{translation('exam_place')}}
                          </div>
                          
                          @if($place_type=="PREMISES")    
                          <div class="form-group">
                             <label class="control-label"><b> {{translation('building_name')}} </b>: </label>
                             <div class="controls">
                                {{$building}}
                             </div>
                             <div class="clearfix"></div>
                          </div>

                          <div class="form-group">
                             <label class="control-label"><b> {{translation('floor_no')}}  </b>: </label>
                             <div class="controls">
                                {{$floor}}
                             </div>
                             <div class="clearfix"></div>
                          </div>

                          <div class="form-group">
                             <label class="control-label"><b> {{translation('room')}}  </b>: </label>
                             <div class="controls">
                                {{$room_name}}
                             </div>
                             <div class="clearfix"></div>
                          </div>
                          @else
                          <div class="form-group">
                             <label class="control-label"><b> {{translation('place')}}  </b>: </label>
                             <div class="controls">
                                {{$place_name}}
                             </div>
                             <div class="clearfix"></div>
                          </div>
                          <div class="form-group">
                             <label class="control-label"><b> {{translation('building_name')}} </b>: </label>
                             <div class="controls">
                                {{isset($arr_data['building']) ?$arr_data['building']:"-"}}
                             </div>
                             <div class="clearfix"></div>
                          </div>

                          <div class="form-group">
                             <label class="control-label"><b> {{translation('floor_no')}}  </b>: </label>
                             <div class="controls">
                                {{isset($arr_data['floor_no']) ?$arr_data['floor_no']:"-"}}
                             </div>
                             <div class="clearfix"></div>
                          </div>

                          <div class="form-group">
                             <label class="control-label"><b> {{translation('room')}}  </b>: </label>
                             <div class="controls">
                                {{isset($arr_data['room']) ?$arr_data['room']:"-"}}
                             </div>
                             <div class="clearfix"></div>
                          </div>
                          @endif
                      </div>
                      @if($marks!='-')
                      <div class="col-md-12">
                        <div class="details-infor-section-block" style="text-align:center">
                            {{translation('result')}} : {{$marks}}
                        </div>
                      </div>    
                      @endif
                      <div class="col-md-12">    
                          <div class="form-group back-btn-form-block">
                             <div class="controls">
                                <a href="{{ $module_url_path }}" class="btn btn-primary" style="float: right;margin-top: 20px;" > <i class="fa fa-arrow-left"></i> {{translation('back')}} </a>
                             </div>
                          </div>  
                      </div>
                    </div>
                </div>
            </div>
          </div>
      </div>

    
{{-- </div> --}}
<!-- END Main Content -->
@stop

