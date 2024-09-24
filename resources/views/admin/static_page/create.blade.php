@extends('admin.layout.master')
    @section('main_content')
    
    <!-- BEGIN Breadcrumb -->
    <div id="breadcrumbs">
      <ul class="breadcrumb">
        <li>
          <i class="fa fa-home"></i>
          <a href="{{ url($admin_panel_slug.'/dashboard') }}">{{translation('dashboard')}}</a>
        </li>
        <span class="divider">
          <i class="fa fa-angle-right"></i>
          <i class="fa fa-sitemap"></i>
          <a href="{{ $module_url_path }}">{{ $module_title or ''}}</a>
        </span> 
        <span class="divider">
          <i class="fa fa-angle-right"></i>
          <i class="fa fa-plus-square-o"></i>
        </span>
        <li class="active">{{ $page_title or ''}}</li>
      </ul>
    </div>
    <!-- END Breadcrumb -->

    
    <!-- BEGIN Page Title -->
    <div class="page-title new-agetitle">
        <div>
            <h1><i class="fa fa-sitemap"></i> {{ isset($module_title)?$module_title:"" }}</h1>
        </div>
    </div>
    <!-- END Page Title -->

    

    <!-- BEGIN Main Content -->
    <div class="row">
      <div class="col-md-12">

        <div class="box {{ $theme_color }}">
          <div class="box-title">
            <h3>
              <i class="fa fa-plus-circle"></i>
              {{ isset($page_title)?$page_title:"" }}
            </h3>
            <div class="box-tool">
              <a data-action="collapse" href="#"></a>
              <a data-action="close" href="#"></a>
            </div>
          </div>
          <div class="box-content">

            @include('admin.layout._operation_status') 


              {!! Form::open([ 'url' => $module_url_path.'/store',
               'method'=>'POST',
               'enctype' =>'multipart/form-data',   
               'class'=>'form-horizontal', 
               'id'=>'validation-form1' 
               ]) !!} 
                     

              <div style="border: none" id="myTabContent1" class="tab-content">

                <input name="image" type="file" class="hidden tinymce_upload" onchange="">

                <div class="row">
                <div class="col-lg-6">
                <div class="row">
                <div class="form-group">
                  <label class="col-sm-3 col-lg-4 control-label" for="page_title">
                          {{translation('page_title')}} <i class="red">*</i>
                  </label>
                  <div class="col-sm-6 col-lg-8 controls">
                    
                      {!! Form::text('page_title',old('page_title'),['class'=>'form-control','placeholder'=>'Page Title','data-rule-maxlength'=>'255','data-rule-required'=>'true']) !!}


                    <span class='help-block'>{{ $errors->first('page_name') }}</span>
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-3 col-lg-4 control-label" for="meta_keyword">
                         {{translation('meta_keyword')}} <i class="red">*</i>
                  </label>
                  <div class="col-sm-6 col-lg-8 controls">
                        {!! Form::text('meta_keyword',old('meta_keyword'),['class'=>'form-control','placeholder'=>'Meta Keyword','data-rule-maxlength'=>'255','data-rule-required'=>'true']) !!}
                    <span class='help-block'>{{ $errors->first('meta_keyword') }}</span>
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-3 col-lg-4 control-label" for="meta_desc">
                          {{translation('meta_description')}} <i class="red">*</i>
                  </label>
                  <div class="col-sm-6 col-lg-8 controls">
                        {!! Form::text('meta_desc',old('meta_desc'),['class'=>'form-control','placeholder'=>'Meta Description','data-rule-required'=>'true','data-rule-maxlength'=>'255']) !!}


                    <span class='help-block'>{{ $errors->first('meta_desc') }}</span>
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-3 col-lg-4 control-label" for="page_desc">
                          {{translation('page_content')}} <i class="red">*</i>
                  </label>
                  <div class="col-sm-6 col-lg-8 controls">
                        {!! Form::textarea('page_desc',old('page_desc'),['class'=>'form-control','placeholder'=>'Page Content','data-rule-required'=>'true']) !!}
                    <span class='help-block'>{{ $errors->first('page_desc') }}</span>
                  </div>
                  
                </div>
                    </div>
                    </div>
                    </div>
            </div>            
            <div class="form-group">
              <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                <a href="{{ $module_url_path}}" class="btn btn-primary">{{translation('back')}}</a> 
                {!! Form::submit('Save',['class'=>'btn btn btn-primary','value'=>'true','onclick'=>'saveTinyMceContent()'])!!}
                
              </div>
            </div>
            {!! Form::close() !!}

        </div>
      </div>
    </div>
 
  <!-- END Main Content -->

  <script type="text/javascript">
    function saveTinyMceContent()
    {
      tinyMCE.triggerSave();
    }


    $(document).ready(function()
    {
      tinymce.init({
        selector: 'textarea',
        height:350,
        theme: "modern",
        paste_data_images: true,
        plugins: [
        'advlist autolink lists link image charmap print preview anchor',
        'searchreplace visualblocks code fullscreen',
        'insertdatetime media table contextmenu paste code'
        ],
        valid_elements : '*[*]',
        toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image ',
        image_advtab: true,
        file_picker_callback: function(callback, value, meta) {
          if (meta.filetype == 'image') {
            $('.tinymce_upload').trigger('click');
            $('.tinymce_upload').on('change', function() {
              var file = this.files[0];
              var reader = new FileReader();
              reader.onload = function(e) {
                callback(e.target.result, {
                  alt: ''
                });
              };
              reader.readAsDataURL(file);
            });
          }
        },
        content_css: [
        '//fast.fonts.net/cssapi/e6dc9b99-64fe-4292-ad98-6974f93cd2a2.css',
        '//www.tinymce.com/css/codepen.min.css'
        ]
      });  
    });

   
  </script>

  @stop
