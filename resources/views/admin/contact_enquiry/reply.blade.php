     @extends('admin.layout.master')                


    @section('main_content')
      

      <!-- BEGIN Breadcrumb -->
      <div id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url($admin_panel_slug.'/dashboard') }}">{{translation('dashboard')}}</a>
            </li>
            <li>
                <i class="fa fa-angle-right"></i>
                <i class="fa fa-info-circle"></i>
                <a href="{{ $module_url_path }}">{{ $module_title or ''}}</a>
            </li>  
            <span class="divider">
                <i class="fa fa-angle-right"></i>
                <i class="fa fa-reply"></i>
            </span>
            <li class="active"> {{ $page_title or ''}}</li>
        </ul>
      </div>
      <!-- END Breadcrumb -->

      <!-- BEGIN Page Title -->
      <link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">
      <div class="page-title new-agetitle">
          <div>
              <h1><i class="fa fa-info-circle"></i> {{ isset($module_title)?$module_title:"" }}</h1>
          </div>
      </div>

      <!-- END Page Title -->
 
        <!-- START Main Content -->


          <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-title">
                        <h3><i class="fa fa-reply"></i>{{ isset($page_title)?$page_title:"" }}</h3>
                        <div class="box-tool">
                          
                        </div>
                    </div>
                    <div class="box-content view-details-seciton-main details-section-main-block replay-page-view">
                    	@include('admin.layout._operation_status')
                        <div class="row">
                         <div class="col-md-12"> 
                     {!! Form::open([ 'url' => $admin_panel_slug.'/contact_enquiry/send_reply',
                                 'method'=>'POST',
                                 'enctype' =>'multipart/form-data',   
                                 'class'=>'form-horizontal', 
                                 'id'=>'validation-form' 
                                ]) !!} 
                                
                                
                                
                                
                                
                                
                                <div class="user-details-section-main">
                                    <div class="details-infor-section-block"> {{translation('enquiry_reply')}}</div>
                                    
                                    <div class="form-group">
                                       <label class="control-label"><b> {{translation('enquiry_number')}} </b>: </label>
                                       <div class="controls">
                                          {{ isset($arr_contact_enquiry['enquiry_no']) && $arr_contact_enquiry['enquiry_no'] !=""  ?$arr_contact_enquiry['enquiry_no']:'-' }} 
                                       </div>
                                       <div class="clearfix"></div>
                                    </div>

                                    <div class="form-group">
                                         <label class="control-label"><b>{{translation('subject')}} </b>: </label>
                                         <div class="controls"> {{ isset($arr_contact_enquiry['subject']) && $arr_contact_enquiry['subject'] !=""  ?$arr_contact_enquiry['subject']:'NA' }}</div>
                                         <div class="clearfix"></div>
                                      </div>
                                      <div class="form-group">
                                         <label class="control-label"><b> {{translation('email')}} </b>: </label>
                                         <div class="controls">  {{ isset($arr_contact_enquiry['email']) && $arr_contact_enquiry['email'] !=""  ?$arr_contact_enquiry['email']:'NA' }}</div>
                                         <div class="clearfix"></div>
                                      </div>
                                        <input type="hidden" name="email" value="{{ isset($arr_contact_enquiry['email'])?$arr_contact_enquiry['email']:'NA' }}">
                                        <input type="hidden" name="name" value="">
                                        <input type="hidden" name="question" value="{{$arr_contact_enquiry['subject']}}" >
                                      <div class="form-group">
                                         <label class="control-label"><b> {{translation('question')}} </b>: </label>
                                         <div class="controls">  {{ isset($arr_contact_enquiry['subject']) && $arr_contact_enquiry['subject'] !=""  ?$arr_contact_enquiry['subject']:'NA' }}</div>
                                         <div class="clearfix"></div>
                                      </div>
                                      <div class="form-group">
                                         <label class="control-label"><b> {{translation('description')}} </b>: </label>
                                         <div class="controls">  {{ isset($arr_contact_enquiry['description']) && $arr_contact_enquiry['description'] !=""  ?$arr_contact_enquiry['description']:'NA' }}</div>
                                         <div class="clearfix"></div>
                                      </div>
                                      <div class="form-group">
                                         <label class="control-label"><b> {{translation('reply')}} </b>: </label>
                                         <div class="controls">  {{ isset($arr_contact_enquiry['comments']) && $arr_contact_enquiry['comments'] !=""  ?$arr_contact_enquiry['comments']:'NA' }}</div>
                                         <div class="clearfix"></div>
                                      </div>
                                      <input type="hidden" value="@if(isset($arr_contact_enquiry['id'])){{base64_encode($arr_contact_enquiry['id'])}}@else{{''}}@endif" name="q_id">
                                      <div class="form-group">
                                         <label class="control-label"><b> {{translation('answer')}}<i style="color: red;">*</i> </b>: </label>
                                         <div class="controls"> {!! Form::textarea('answer','',['class'=>'form-control','data-rule-required'=>'true', 'placeholder'=>'answer', 'id'=>"answer" ,  'rows'=>'5', 'data-parsley-errors-container'=>"#error-answer",'data-parsley-maxlength-message'=>"Answer should not be more than 1000 characters", 'data-parsley-maxlength'=>"1000" ]) !!}  <span class="help-block" id="#error-answer">{{ $errors->first('answer') }}</span></div>
                                         <div class="clearfix"></div>
                                         
                                      </div>
                                      
                                       <div class="form-group">
                                         <label class="control-label"> </label>
                                         <div class="controls">
                                              <a href="{{ $module_url_path }}"> 
                                              <a href="{{ url($module_url_path) }}" class="btn btn-primary">{{translation('back')}}</a> 
                                                <input type="submit"  class="btn btn-primary" value="{{translation('send')}}">
                                              </a> 
                                          </div>
                                         <div class="clearfix"></div>
                                      </div>
                                      
                                </div>
                                
                                
                                
<!--

                           <div class="form-group"> 
                            <label class="col-sm-3 col-lg-2 control-label" ></label>
                              <div class="col-sm-3 col-lg-3 controls">
                                  <h4><b>{{translation('enquiry_reply')}}</b></h4>
                              </div>
                          </div> 
                        
                             <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label" for="email_id">{{translation('subject')}}</label>
                                <div class="col-sm-6 col-lg-10 controls" style="padding-top:6px;">
                                   {{ isset($arr_contact_enquiry['subject']) && $arr_contact_enquiry['subject'] !=""  ?$arr_contact_enquiry['subject']:'NA' }}
                                </div>
                            </div>
-->
<!--
                             <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label" for="email_id">{{translation('email')}}</label>
                                <div class="col-sm-6 col-lg-10 controls" style="padding-top:6px;">
                                   {{ isset($arr_contact_enquiry['email']) && $arr_contact_enquiry['email'] !=""  ?$arr_contact_enquiry['email']:'NA' }}
                                </div>
                            </div>
                            <input type="hidden" name="email" value="{{ isset($arr_contact_enquiry['email'])?$arr_contact_enquiry['email']:'NA' }}">
                            <input type="hidden" name="name" value="">
                            <input type="hidden" name="question" value="{{$arr_contact_enquiry['subject']}}" >
-->

<!--
                             <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label" for="subject">{{translation('question')}}</label>
                                <div class="col-sm-6 col-lg-10 controls" style="padding-top:6px;">
                                     {{ isset($arr_contact_enquiry['subject']) && $arr_contact_enquiry['subject'] !=""  ?$arr_contact_enquiry['subject']:'NA' }}
                                </div>
                            </div>

                             <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label" for="subject">{{translation('description')}}</label>
                                <div class="col-sm-6 col-lg-10 controls" style="padding-top:6px;">
                                     {{ isset($arr_contact_enquiry['description']) && $arr_contact_enquiry['description'] !=""  ?$arr_contact_enquiry['description']:'NA' }}
                                </div>
                            </div>
-->

<!--
                             <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label" for="subject">{{translation('reply')}}</label>
                                <div class="col-sm-6 col-lg-10 controls" style="padding-top:6px;">
                                     {{ isset($arr_contact_enquiry['comments']) && $arr_contact_enquiry['comments'] !=""  ?$arr_contact_enquiry['comments']:'NA' }}
                                </div>
                            </div>
-->

<!--
                           <input type="hidden" value="@if(isset($arr_contact_enquiry['id'])){{base64_encode($arr_contact_enquiry['id'])}}@else{{''}}@endif" name="q_id">
                            <div class="form-group" style="">
                              <label class="col-sm-3 col-lg-2 control-label">{{translation('answer')}}<i style="color: red;">*</i></label>
                              <div class="col-sm-9 col-lg-4  controls" >
                                    {!! Form::textarea('answer','',['class'=>'form-control','data-rule-required'=>'true', 'placeholder'=>'answer', 'id'=>"answer" ,  'rows'=>'5', 'data-parsley-errors-container'=>"#error-answer",'data-parsley-maxlength-message'=>"Answer should not be more than 1000 characters", 'data-parsley-maxlength'=>"1000" ]) !!}

                              <span class="help-block" id="#error-answer">{{ $errors->first('answer') }}</span>
                              </div>
                           </div> 
-->


<!--
                            <div class="form-group">
                              <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                              <a href="{{ $module_url_path }}"> 
                              <a href="{{ url($module_url_path) }}" class="btn btn-primary">{{translation('back')}}</a> 
                                <input type="submit"  class="btn btn-primary" value="{{translation('send')}}">
                              </a>  
                            </div>
                            </div>
-->
                  
                        {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- END Main Content -->
<script type="text/javascript">
$('#answer').maxlength({max: 1000,showFeedback : true});

    function scrollToButtom()
    {
        $("html, body").animate({ scrollTop: $(document).height() }, 1000);
    }

    $(document).ready(function()
    {
        $("#select_action").bind('change',function()
        {
            if($(this).val()=="cancel")
            {
                $("#reason_section").show();
            }
            else
            {
                $("#reason_section").hide();
            }
        });
    });
</script>

  @stop                    


