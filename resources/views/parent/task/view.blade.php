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
<!-- END Breadcrumb -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="fa fa-tasks"></i> {{$page_title}}</h1>
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

         $task_name        = isset($arr_data['task_name']) ?$arr_data['task_name']:"-";
         $task_description = isset($arr_data['task_description']) ?$arr_data['task_description']:"-";
         $priority         = isset($arr_data['priority']) ?$arr_data['priority']:"-";
         $users            = isset($arr_data['user_role']) ?$arr_data['user_role'] : '-';
         $date             = isset($arr_data['task_submission_date']) ?getDateFormat($arr_data['task_submission_date']) : '-';
         $time             = isset($arr_data['task_submission_time']) ?getTimeFormat($arr_data['task_submission_time']) : '-';


        ?>
          <div class="clearfix"></div>
          <div class="box-content view-details-seciton-main details-section-main-block">
            <div class="row">
                <div class="col-md-12">
                    <div class="details-infor-section-block">
                        {{$page_title}}
                    </div>
                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('task_name')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$task_name}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('description')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$task_description or ''}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('task_priority')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$priority}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('users')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$users}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('task_submission_date')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$date}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('task_submission_time')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$time}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

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

