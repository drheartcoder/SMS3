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

<!-- page title -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="fa fa-book"></i> {{$page_title}}</h1>
    </div>
</div>
<!-- BEGIN Main Content -->
{{-- <div class="row"> --}}

   <div class="box {{ $theme_color }}">
      <div class="box-title">
         <h3>
            <i class="fa fa-list"></i>
           {{translation('claim_details')}}  
         </h3>
         <div class="box-tool">
         </div>
      </div>
      
        <?php
          $level                     = isset($arr_data['get_level_class']['level_details']['level_name']) ?$arr_data['get_level_class']['level_details']['level_name']:"-";

          $class                     = isset($arr_data['get_level_class']['class_details']['class_name']) ?$arr_data['get_level_class']['class_details']['class_name']:"-";

          $student_first_name        = isset($arr_data['get_student_details']['first_name'])?$arr_data['get_student_details']['first_name']:"";

          $student_last_name        = isset($arr_data['get_student_details']['last_name']) ?$arr_data['get_student_details']['last_name']:"";

          $student_name              = $student_first_name.' '.$student_last_name;

          $student_national_id       = isset($arr_data['student_national_id']) ?$arr_data['student_national_id']:"-";
          $description               = isset($arr_data['description']) ?$arr_data['description']:"-";
          $title                     = isset($arr_data['title']) ?$arr_data['title']:"-";
          $status                    = isset($arr_data['status']) ?$arr_data['status']:"-";

          $parent_first_name        = isset($arr_data['get_parent_details']['first_name'])?$arr_data['get_parent_details']['first_name']:"";

          $parent_last_name        = isset($arr_data['get_parent_details']['last_name']) ?$arr_data['get_parent_details']['last_name']:"";

          $parent_name              = $parent_first_name.' '.$parent_last_name;

          $professor_first_name        = isset($arr_data['get_professor_details']['first_name'])?$arr_data['get_professor_details']['first_name']:"";

          $professor_last_name        = isset($arr_data['get_professor_details']['last_name']) ?$arr_data['get_professor_details']['last_name']:"";

          $professor_name              = $professor_first_name.' '.$professor_last_name;
        ?>
          <div class="clearfix"></div>
          <div class="box-content view-details-seciton-main details-section-main-block">
            <div class="row">
                <div class="col-md-12">
                <div class="details-infor-section-block">
                        {{translation('details')}}
                    </div>  
                
                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('level')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$level}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('class')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$class or ''}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('student_name')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{isset($student_name)?$student_name:'-'}}
                     </div>
                     <div class="clearfix"></div>
                  </div>
                 
                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"> <b>{{translation('student')}} {{translation('national_id')}}</b> :</label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$student_national_id}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('parent_name')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{isset($parent_name)?$parent_name:'-'}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"> <b>{{translation('claim')}} {{translation('added_by')}}</b> :</label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{isset($professor_name)?translation('prof').'. '.$professor_name:'-'}}
                     </div><div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"> <b>{{translation('claim_title')}}</b> :</label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$title}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"> <b>{{translation('claim')}} {{translation('description')}}</b> :</label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$description}}
                     </div>
                     <div class="clearfix"></div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('status')}} </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$status}}
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

