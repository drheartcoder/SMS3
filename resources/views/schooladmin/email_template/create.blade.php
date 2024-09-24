@extends('schooladmin.layout.master')            
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
    <li>
      <i class="fa fa-home"></i>
      <a href="{{ url($school_admin_panel_slug.'/dashboard') }}"> Dashboard </a>
    </li>
    <span class="divider">
      <i class="fa fa-angle-right"></i>
      <i class="fa {{$module_icon or ''}}"></i>
      <a href="{{ $module_url_path }}"> Manage {{ $module_title or ''}} </a>
    </span> 
    <span class="divider">
      <i class="fa fa-angle-right"></i>
      <i class="fa fa-plus-square-o"></i>
    </span>
    <li class="active"> {{ $page_title or ''}} </li>
  </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="fa fa-plus-square-o"></i> {{ isset($page_title)?$page_title:"" }}</h1>
    </div>
</div>
<!-- END Page Title -->

<!-- BEGIN Main Content -->
<div class="row">
  <div class="col-md-12">
    <div class="box {{ $theme_color }}">
      <div class="box-title">
        <h3>
          <i class="fa fa-plus-square-o"></i>
          {{ isset($page_title)?$page_title:"" }}
        </h3>
        <div class="box-tool">
          <a data-action="collapse" href="#"></a>
          <a data-action="close" href="#"></a>
        </div>
      </div>
     

            <div class="box-content">
        
        @include('schooladmin.layout._operation_status')
   
          <div class="tabbable">
          <form method="POST" id="validation-form" class="form-horizontal" action="{{$module_url_path}}/store" enctype="multipart/form-data">
                
                {{ csrf_field() }}              

                            <div class="row">
                              <div class="col-lg-6">
                              <div class="row">
                              <div class="form-group">
                              <label class="col-sm-3 col-lg-4 control-label"> Email Template Name 
                                <i class="red">*</i>
                              </label>
                              <div class="col-sm-4 col-lg-8 controls">   
                              
                                <input type="text" name="template_name" required=""  placeholder="Email Template Name" value="{{old('template_name') or ''}}"  class="form-control add-stundt"/>
                              
                              </div>
                                  <span class='help-block'> {{ $errors->first('template_name') }} </span>  
                              </div>
                              
                            <div class="form-group">
                              <label class="col-sm-3 col-lg-4 control-label" for="email"> Email Template Subject 
                              <i class="red">*</i>
                              </label>
                              <div class="col-sm-6 col-lg-8 controls">                     
                                <input type="text" name="template_subject" required="" placeholder="Email Template Subject" value="{{old('template_subject') or ''}}"  class="form-control add-stundt"/>
                              </div>
                              <span class='help-block'> {{ $errors->first('template_subject') }} </span>  
                            </div>

                            <div class="form-group">
                              <label class="col-sm-3 col-lg-4 control-label" for="email"> Email Template Body 
                                <i class="red">*</i>
                              </label>
                              <div class="col-sm-6 col-lg-8 controls"> 
                                  <textarea name="template_html" required="" class="form-control"
                                    value="{{old('template_html')}}" rows="10"  placeholder="Email Template Body"></textarea>
                              </div>
                              <span class='help-block'> {{ $errors->first('template_html') }} </span>  
                            </div>

                            <div class="form-group">
                              <label class="col-sm-3 col-lg-4 control-label" for="email"> Variables 
                                <i class="red">*</i> 
                              </label>
                              <div class="col-sm-6 col-lg-8 controls">   
                                {!! Form::text('variables[]',old('variables[]'),['class'=>'form-control','-required'=>'true','data-rule-maxlength'=>'500', 'placeholder'=>'Variables']) !!}  
                              </div>
                              <a class="btn btn-primary" href="javascript:void(0)" onclick="add_text_field()">
                                <i class="fa fa-plus"></i>
                              </a>
                              <a class="btn btn-danger" href="javascript:void(0)" onclick="remove_text_field(this)">
                                <i class="fa fa-minus"></i>
                              </a>
                              <span class='help-block'> {{ $errors->first('variables[]') }} </span>  
                            </div>
                                
                            
                            <div id="append_variables"></div>
                            <br>
                          </div>
                          </div>
                          </div>  

                              

                <br>
                <div class="form-group">
                      <div class="col-sm-9 col-sm-offset-3 col-lg-4 col-lg-offset-4">                      
                          <input type="submit" id="save_btn" class="btn btn btn-primary" onclick="saveTinyMceContent()" value="Save"/>                  
                    </div>
                </div>
                
              </form>
                
            </div>  
  

      </div>



    </div>
  </div>
  <!-- END Main Content -->

  <script type="text/javascript">
   function add_text_field() 
   {
       var html = "<div class='form-group appended' id='appended'><label class='col-sm-3 col-lg-2 control-label'></label><div class='col-sm-6 col-lg-4 controls'><input class='form-control' name='variables[]' data-rule-required='true' placeholder='Variables' /></div><div id='append_variables'></div></div>";
       jQuery("#append_variables").append(html);
   }

   function remove_text_field(elem)
   {
      $( ".appended:last" ).remove();
   }

    function saveTinyMceContent()
    {
      tinyMCE.triggerSave();
    }



  $(document).ready(function()
  {
    tinymce.init({
      selector: 'textarea',
      relative_urls: false,
      height:500,
      remove_script_host:false,
      convert_urls:false,
      plugins: [
        'advlist autolink lists link image charmap print preview anchor',
        'searchreplace visualblocks code fullscreen',
        'insertdatetime media table contextmenu paste code'
      ],
      toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
      content_css: [
        '//fast.fonts.net/cssapi/e6dc9b99-64fe-4292-ad98-6974f93cd2a2.css',
        '//www.tinymce.com/css/codepen.min.css'
      ]
    });
  });
 </script>
@stop