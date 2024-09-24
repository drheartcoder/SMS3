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
        <h1><i class="fa fa-book"></i>{{$page_title}}</h1>

    </div>
</div>
<!-- END Page Title -->

<!-- BEGIN Main Content -->
{{-- <div class="row"> --}}
<div class="row">
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
          $course                    = isset($arr_data['get_course']['course_name']) ?$arr_data['get_course']['course_name']:"-";
          $homework_added_by_first_name  = isset($arr_data['homework_added_by']['first_name']) ?$arr_data['homework_added_by']['first_name']:"";
          $homework_added_by_last_name   = isset($arr_data['homework_added_by']['last_name']) ?$arr_data['homework_added_by']['last_name']:"";
          $homework_added_by_name        = $homework_added_by_first_name.' '.$homework_added_by_last_name;
          $status                    = isset($arr_data['homework_details']['status']) ?$arr_data['homework_details']['status']:"-";
          $homework_details                    = isset($arr_data['description']) ?$arr_data['description']:"-";
          $added_date                    = isset($arr_data['added_date']) ?$arr_data['added_date']:"-";
          $due_date                    = isset($arr_data['due_date']) ?$arr_data['due_date']:"-";
          if($added_date!='-')
          {
            $added_date = getDateFormat($added_date);
          }
          if($due_date!='-')
          {
            $due_date = getDateFormat($due_date);
          }

        ?>
          <div class="clearfix"></div>
          <div class="box-content">
            <div class="row">
                <div class="col-md-9">
                  
                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('course')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$course}}
                     </div>
                  </div><div class="clearfix"></div>
                 
                   <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('professor')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$homework_added_by_name}}
                     </div>
                  </div><div class="clearfix"></div>
                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('homework_details')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$homework_details}}
                     </div>
                  </div><div class="clearfix"></div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('added_date')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$added_date}}
                     </div>
                  </div><div class="clearfix"></div>
                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('due_date')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$due_date}}
                     </div>
                  </div><div class="clearfix"></div>
                   <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('status')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$status}}
                     </div>
                  </div><div class="clearfix"></div>
                    <div class="form-group">
                       <div class="col-sm-9 col-lg-12 controls">
                          <a href="{{ $module_url_path }}" class="btn btn-primary" style="float: right;margin-top: 20px;" > <i class="fa fa-arrow-left"></i> {{translation('back')}} </a>

                        </div>
                    </div><div class="clearfix"></div>



                  </div>
                </div>
            </div>
          </div>
      </div>
      </div>
    
{{-- </div> --}}
<!-- END Main Content -->
@stop

