@extends('school_admin.layout.master')                
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="{{url('/')}}/{{ config('app.project.school_admin_panel_slug') }}/dashboard">{{translation('dashboard')}}</a>
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
        <li class="active">{{$module_title}}</li>
    </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
    <div>
        <h1>{{$page_title}}</h1>
    </div>
</div>
<!-- END Page Title -->

<!-- BEGIN Main Content -->
<div class="row">
<div class="col-md-12">
   <div class="box box-navy_blue">
        <div class="box-title">
            <h3><i class="{{$view_icon}}"></i>{{$page_title}}</h3>
            <div class="box-tool">
            </div>
        </div> 
         <div class="box-content">            
                @include('school_admin.layout._operation_status')  
                <div class="form-group img-view-pro">
                    <label class="col-sm-3 col-md-4 col-lg-3 control-label"></label>
                    <div class="col-sm-9 col-md-8 col-lg-7 controls">
                       @if(!empty($arr_data['get_survey_images']))
                        <div class="img-pro-vw">

                           <div id="myCarousel" class="carousel slide" data-ride="carousel">
                              <!-- Wrapper for slides -->
                              <div class="carousel-inner">
                                
                                   @foreach($arr_data['get_survey_images'] as $key => $images)

                                  <?php

                                  if(isset($images['survey_image']) && ($images['survey_image'])!='') 
                                  {
                                      $fileURL = '';
                                      $fileURL = $surveyUploadImagePath.'/'.$images['survey_image'];

                                      if(file_exists($fileURL))
                                      {
                                          $survey_img = resize_images_new('uploads/survey_image/',$images['survey_image'],'720','422');
                                      }
                                      else
                                      {
                                          $survey_img  = url('/').'/uploads/default.png'; 
                                      }
                                  }else{
                                       $survey_img  = url('/').'/uploads/default.png'; 
                                  }
                                   

                                  ?>
                                <div class="item @if($key==0) active @endif">
                                  <img src="{{ $survey_img }}" alt="Survey Image" class="img-responsive" />
                                </div>

                               @endforeach
                              </div>


                              <!-- Left and right controls -->
                              <a class="left carousel-control" href="#myCarousel" data-slide="prev">
                                <span class="glyphicon glyphicon-chevron-left"></span>
                                <span class="sr-only">Previous</span>
                              </a>
                              <a class="right carousel-control" href="#myCarousel" data-slide="next">
                                <span class="glyphicon glyphicon-chevron-right"></span>
                                <span class="sr-only">Next</span>
                              </a>
                            </div>
                       
                        </div>
                        @endif
                        <div class="pr-viw-contents">
                            <div class="row">
                                <div class="col-sm-12 col-md-12 col-lg-12">
                                    <div class="sb-titl-pro-surv">
                                        <div class="left-fessor-vw-left">{{translation('survey_title')}}</div>
                                        <div class="left-fessor-vw-right">: {{ $arr_data['survey_title'] or ''}}</div>
                                    </div>
                                </div>                                
                                <div class="col-sm-12 col-md-6 col-lg-6">
                                    <div class="sb-titl-pro-surv">
                                        <div class="left-fessor-vw-left">{{ translation('start_date') }}</div>
                                        <div class="left-fessor-vw-right">: <?php echo isset($arr_data['start_date'])&&$arr_data['start_date']!='0000-00-00'?getDateFormat($arr_data['start_date']):''; ?></div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-6">
                                    <div class="sb-titl-pro-surv">
                                        <div class="left-fessor-vw-left">{{ translation('end_date') }}her</div>
                                        <div class="left-fessor-vw-right">: <?php echo isset($arr_data['end_date'])&&$arr_data['end_date']!='0000-00-00'?getDateFormat($arr_data['end_date']):''; ?></div>
                                    </div>
                                </div>                                
                                <div class="col-sm-12 col-md-12 col-lg-12">
                                    <div class="sb-titl-pro-surv">
                                        <div class="left-fessor-vw-left">{{translation('survey_description')}}</div>
                                        <div class="left-fessor-vw-right">: {{$arr_data['survey_description'] or ''}}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                                    
                    <div class="col-sm-9 col-md-8 col-lg-7 col-lg-offset-3 controls">
                       <form method="POST" action="{{ $module_url_path }}/store_survey_reply/{{$enc_id}}" accept-charset="UTF-8" class="form-horizontal"  enctype="multipart/form-data"  id="validation-form1" >
                        {{ csrf_field() }}
                        <div class="row">
                         <div class="form-group">
                         <div id="optionData">
                            @if(!empty($arr_data['get_questions']))
                            <div class="col-sm-12 col-md-12 col-lg-12">
                              <div class="" id="accordion1">
                                @foreach($arr_data['get_questions'] as $key => $arr_dataRs)
                                <div class="xpanel xpanel-info">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion1" href="#collapse{{$arr_dataRs['id']}}" aria-expanded="true">{{$arr_dataRs['survey_question'] or ''}}  </a>
                                        </h4>
                                    </div>
                                    <div id="collapse{{$arr_dataRs['id']}}" class="panel-collapse collapse @if($key==0) in @endif" aria-expanded="true" style="">
                                        <div class="panel-body">
                                          
                                            <?php

                                                $arr_options = isset($arr_dataRs['question_options'])&&!empty($arr_dataRs['question_options'])?json_decode($arr_dataRs['question_options'],true):array();
                                               
                                                if(!empty($arr_options)){
                                                    foreach($arr_options as $key2 => $value ){
                                            ?>
                                            <div class="col-sm-12 col-md-12 col-lg-12">
                                                <div class="sb-titl-pro-surv ques-options-section">
                                                    <div class="left-fessor-vw-left">{{$key2+1}}</div>
                                                    <div class="left-fessor-vw-right"> {{ $value or '' }}</div>
                                                </div>
                                            </div>
                                            <?php } } ?>
                                           <div class="col-sm-12 col-md-12 col-lg-12">
                                                <div class="sb-titl-pro-surv question-type-section">
                                                    <div class="left-fessor-vw-left">{{translation('answer')}} : </div>
                                                    <div class="left-fessor-vw-right">  
                                                    <?php
                                                      
                                                    if(!empty($arr_data['get_questions_answer'][$key]) && $arr_data['get_questions_answer'][$key]['answer']!=''   ){

                                                        echo   trim($arr_data['get_questions_answer'][$key]['answer'],'"');
                                                    }else{
                                                      echo '-';
                                                    } ?>

                                                       ?> </div>
                                                    
                                                </div>
                                            </div>

                                            
                                        </div>
                                    </div>
                                </div>
                                @endforeach

                               
                            </div>
                        </div>
                        @endif
                        </div>
                      </div>


                      </div>
                    </form>
                    </div>
                <div class="col-sm-8 col-sm-offset-4 col-lg-9 col-lg-offset-3">
                      <a href="{{ url('/') }}/survey/view_response/{{ $enc_survey_id }}" class="btn btn-primary" style="float: right;margin-top: 20px;" > <i class="fa fa-arrow-left"></i> {{translation('back')}} </a>
                </div>
                <div class="clearfix"></div>
                 
                
          
           <div class="clearfix"></div>
            

            </div> 
         
            
    </div>
   </div>
</div>
@stop