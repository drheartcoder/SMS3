@extends('schooladmin.layout.master')                
@section('main_content')
<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="{{url('/')}}/school_admin/dashboard">{{translation('dashboard')}}</a>
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
            <i class="{{$edit_icon}}"></i>
            <li class="active">{{$page_title}}</li>
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
         <h3>
            <i class="fa {{$edit_icon}}">
            </i>{{ isset($page_title)?$page_title:"" }} 
         </h3>
         <div class="box-tool">
         </div>
      </div>
      <div class="box-content">
         @include('schooladmin.layout._operation_status')
          
            <form method="post" onsubmit="return addLoader()"  action="{{$module_url_path}}/update/{{base64_encode($id)}}"  class="form-horizontal" id="validation-form1" enctype ='multipart/form-data'>
              {{ csrf_field() }}
              

               <div  class="tab-content">

                     

                            <?php  
                          
                                    /* Locale Variable */  
                                   
                                    $template_subject = "";
                                    $template_html = "";
                                    
                                   
                                    
                                   
                                     $template_subject = $arr_data['template_subject'];
                                     $template_html = $arr_data['template_html'];
                                    
                                 
                                  ?>
                              <div>
                              <br>
                              <div class="row">
                                 
                                     
                                   <div class="form-group">
                                      <label class="col-sm-3 col-lg-2 control-label" for="email"> 
                                        {{translation('sms_template_name')}} <i class="red">*</i>
                                      </label>
                                      <div class="col-sm-6 col-lg-4 controls">
                                      
                                        <input type="text" name="template_subject" placeholder="{{ translation('enter')}} {{translation('sms_template_name')}}" value="{{$template_subject}}"  class="form-control add-stundt" data-rule-required="true"/>
                                      
                                      </div>
                                      <span class='help-block'> {{ $errors->first('template_subject') }} </span>  
                                    </div>
                                    <div class="clearfix"></div>

                                    <div class="form-group">
                                      <label class="col-sm-3 col-lg-2 control-label" for="email"> 
                                          {{translation('sms_template_body')}} <i class="red">*</i> 
                                      </label>
                                      <div class="col-sm-6 col-lg-8 controls">   
                                      
                                          <textarea name="template_html" class="form-control"  rows="10"  placeholder=" {{ translation('enter') }} {{translation('sms_template_body')}} " data-rule-required="true">{{$template_html}}</textarea>
                                      
                                      </div>
                                      <span class='help-block'> {{ $errors->first('template_html') }} </span>  

                                    </div> <div class="clearfix"></div>
                                   
                                    <div class="form-group">
                                      <label class="col-sm-3 col-lg-2 control-label" for="email"> Variables: </label>
                                      <div class="col-sm-6 col-lg-7 controls">   
                                          @if(sizeof($arr_variables)>0)
                                              @foreach($arr_variables as $variable)
                                                  <br> <label> {{ $variable }} </label> 
                                              @endforeach
                                          @endif 
                                      </div>
                                    </div><div class="clearfix"></div>
                                   
                         </div>
                      </div>
                    
         

      
           <div class="form-group">
            <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
               <a href="{{  $module_url_path }}" class="btn btn-primary">Back</a> 
                <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('update')}}</button>
            </div>
          </div>
         </form>
      </div>
      </div>
   </div>
</div>
</div>  
<!-- END Main Content --> 
@endsection