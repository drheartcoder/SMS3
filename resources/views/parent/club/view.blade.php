@extends('parent.layout.master')                
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="{{ url($parent_panel_slug) }}/dashboard">{{translation('dashboard')}}</a>
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
<!-- page title -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="fa fa-users"></i> {{$page_title}}</h1>
    </div>
</div>

<!-- BEGIN Main Content -->
{{-- <div class="row"> --}}

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
        
         $club_name    = isset($arr_data['club_name']) ?$arr_data['club_name']:"-";
         $club_id      = isset($arr_data['club_no']) ?$arr_data['club_no']:"-";
         $first_name   = isset($arr_data['get_supervisor']['first_name']) ? ucfirst($arr_data['get_supervisor']['first_name']) : '';
         $last_name    = isset($arr_data['get_supervisor']['last_name']) ? ucfirst($arr_data['get_supervisor']['last_name']) : '';
         $supervisor   = $first_name.' '.$last_name;
         $club_type    = isset($arr_data['is_free']) ?$arr_data['is_free']:"-";
         $place        = isset($arr_data['place']) ?$arr_data['place']:"-";
         $description  = isset($arr_data['description']) ?$arr_data['description']:"-";
         $arr_students = isset($arr_data['get_students']) ? $arr_data['get_students']:[];

        
        ?>


          <div class="clearfix"></div>
          <div class="box-content view-details-seciton-main details-section-main-block">
            <div class="row">
                <div class="col-md-12">
                 <div class="details-infor-section-block">
                        {{$page_title}}
                    </div>  
                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('club_name')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$club_name}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('club_id')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$club_id or ''}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('supervisor')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$supervisor}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('club_type')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$club_type}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('place')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$place}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('description')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$description}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  @if(count($arr_students)>0)
                  <div class="form-group">

                    <label class="col-sm-4 col-lg-4 control-label"><b>{{str_plural(translation('student'))}}</b>: </label>
                    <div class="col-sm-4 col-lg-4 table-responsive attendance-create-table-section" style="border:0" >
                     <table class="table table-advance">
                     <thead>
                      <tr style="background-color:#495B79">
                        <th style="color:#fff !important">{{translation('level')}}</th>
                        <th style="color:#fff !important">{{translation('class')}}</th>
                        <th style="color:#fff !important">{{translation('student_name')}}</th>
                      </tr>
                     </thead>
                     <tbody> 
                     @foreach($arr_students as $student)
                      <tr >
                        <td>{{isset($student['get_level_class']['level_details']['level_name']) ? $student['get_level_class']['level_details']['level_name'] :'' }}</td>
                        <td>{{isset($student['get_level_class']['class_details']['class_name']) ? $student['get_level_class']['class_details']['class_name'] :'' }}</td>
                        <td>{{isset($student['get_user_details']['first_name']) ? ucfirst($student['get_user_details']['first_name']) :'' }} {{isset($student['get_user_details']['last_name']) ? ucfirst($student['get_user_details']['last_name']) :'' }}</td>
                      </tr>
                     @endforeach
                     </tbody>
                     </table>
                    </div> 
                     <div class="clearfix"></div>
                  </div>
                  @endif
                 

                    <div class="form-group back-btn-form-block">
                       <div class="controls">
                          <a href="{{ $module_url_path }}" class="btn btn-primary" style="float: right;margin-top: 20px;" > <i class="fa fa-arrow-left"></i> {{translation('back')}} </a>
                       </div>
                    </div>

                  </div>
                </div>
            </div>
          </div>
    
    
{{-- </div> --}}
<!-- END Main Content -->
@stop

