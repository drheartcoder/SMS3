@extends('student.layout.master')                
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="{{ url($student_panel_slug) }}/dashboard">{{translation('dashboard')}}</a>
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
        <h1><i class="fa fa-book"></i> {{$page_title}}</h1>
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
        
         $level                     = isset($arr_data['get_level_class']['level_details']['level_name']) ?$arr_data['get_level_class']['level_details']['level_name']:"-";
         $class                     = isset($arr_data['get_level_class']['class_details']['class_name']) ?$arr_data['get_level_class']['class_details']['class_name']:"-";
         $course                    = isset($arr_data['get_course']['course_name']) ?$arr_data['get_course']['course_name']:"-";
         $added_date                = isset($arr_data['created_at'])?$arr_data['created_at']:"-";

         $arr_files = $arr_video = [];
         if(isset($arr_data['get_material_details']) && count($arr_data['get_material_details'])>0)
         {
              foreach ($arr_data['get_material_details'] as $value) 
              {
                  if($value['type'] == "Document")
                  { 
                      array_push($arr_files,$value['path']);
                  }
                  if($value['type'] == "Video")
                  {
                      array_push($arr_video,$value['path']);
                  }
              }
         }
        ?>
          <div class="clearfix"></div>
          <div class="box-content view-details-seciton-main details-section-main-block">
            <div class="row">
                <div class="col-md-12">
                    <div class="details-infor-section-block">
                        {{$page_title}}
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
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('course')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$course}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b>  {{translation('added_date')}} </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$added_date}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b>{{translation('document')}}</b>:</label>
                     @foreach($arr_files as $file)
                     <div class="col-sm-9 col-lg-4 controls">
                        {{$file or '-'}}
                     </div>                     
                     @endforeach
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b>{{translation('video_url')}}</b>: </label>
                     @foreach($arr_video as $video)
                     <div class="col-sm-9 col-lg-4 controls">
                        <a href="{{$video}}">{{$video}}</a>
                     </div>                     
                     @endforeach
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

