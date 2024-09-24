@extends('admin.layout.master')            
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
    <li>
      <i class="fa fa-home"></i>
      <a href="{{ url($admin_panel_slug.'/dashboard') }}"> {{translation('dashboard')}} </a>
    </li>
    <span class="divider">
      <i class="fa fa-angle-right"></i>
      <i class="fa {{$module_icon or ''}}"></i>
      <a href="{{ $module_url_path }}"> {{translation('manage')}} {{ $module_title or ''}} </a>
    </span> 
    <span class="divider">
      <i class="fa fa-angle-right"></i>
      <i class="fa fa-edit"></i>
    </span>
    <li class="active"> {{ $page_title or ''}} </li>
  </ul>
</div>
<!-- END Breadcrumb -->


<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
  <div>
    <h1>{{ $page_title or ''}}</h1>
  </div>
</div>
<!-- END Page Title -->
<!-- BEGIN Main Content -->
<div class="row">
  <div class="col-md-12">
    <div class="box {{ $theme_color }}">
      <div class="box-title">
        <h3>
          <i class="fa fa-edit"></i>
          {{ $page_title or ''}}
        </h3>
        <div class="box-tool">
          <a data-action="collapse" href="#"></a>
          <a data-action="close" href="#"></a>
        </div>
      </div>

      <div class="box-content">

        @include('admin.layout._operation_status')


        <form method="POST" id="validation-form1" class="form-horizontal" action="{{$module_url_path}}/update/{{base64_encode($arr_data['id'])}}" onsubmit="return addLoader()"  enctype="multipart/form-data">

          {{ csrf_field() }}              

          <div style="border: none;" class="tab-content edit-space">



            <?php  

            /* Locale Variable */  

            $template_subject = "";
            $template_html = "";


            $template_subject = $arr_data['template_subject'];
            $template_html    = $arr_data['template_html'];


            ?>
            <div   >
              <div class="row">
               <div class="col-lg-6">
                 <div class="row">
                  <div class="form-group">
                    <label class="col-sm-3 col-lg-4 control-label" for="email"> 
                     {{translation('sms_template_name')}} <i class="red">*</i></label>
                     <div class="col-sm-6 col-lg-8 controls">
                      <input type="text" name="template_subject" placeholder="{{ translation('enter')}} {{translation('sms_template_name')}}" value="{{$template_subject}}"  class="form-control add-stundt" data-rule-required="true"/>

                    </div>
                    <span class='help-block'> {{ $errors->first('template_subject') }} </span>  
                  </div><div class="clearfix"></div>

                  <div class="form-group">
                    <label class="col-sm-3 col-lg-4 control-label" for="email"> 
                      {{translation('sms_template_body')}} <i class="red">*</i> 
                    </label>
                    <div class="col-sm-6 col-lg-8 controls">   

                      <textarea name="template_html" class="form-control"  rows="10"  placeholder=" {{ translation('enter') }} {{translation('sms_template_body')}} " data-rule-required="true">{{$template_html}}</textarea>

                    </div>
                    <span class='help-block'> {{ $errors->first('template_html') }} </span>  

                  </div> <div class="clearfix"></div>


                  <div class="form-group">
                    <label class="col-sm-3 col-lg-4 control-label" > Variables: </label>
                    <div class="col-sm-6 col-lg-8 controls">   
                      @if(sizeof($arr_variables)>0)
                      @foreach($arr_variables as $variable)
                      <br> <label> {{ $variable }} </label> 
                      @endforeach
                      @endif 
                    </div>
                  </div><div class="clearfix"></div>

                </div>
              </div>
            </div>
          </div>     
       </div>                
        <div class="form-group">
          <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
            <a href="{{$module_url_path}}" class="btn btn-primary">{{translation('back')}}</a>
            <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('update')}}</button>                     
          </div>
        </div><div class="clearfix"></div>

      </form>




    </div>

  </div>
</div>
<!-- END Main Content -->
@stop