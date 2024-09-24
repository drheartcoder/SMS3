@extends('admin.layout.master')                
@section('main_content')
   
   <!-- BEGIN Breadcrumb -->
    <div id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url($admin_panel_slug.'/dashboard') }}">{{ translation('dashboard') }}</a>
            </li>
            <span class="divider">
                <i class="fa fa-angle-right"></i>
                <i class="fa fa-book"></i>
                <a href="{{ $module_url_path }}">{{ $module_title or ''}}</a>
            </span> 
            <span class="divider">
                <i class="fa fa-angle-right"></i>
                  <i class="fa fa-plus"></i>
            </span>
            <li class="active">{{ $page_title or ''}}</li>
        </ul>
    </div>
    <!-- END Breadcrumb -->
   
    <!-- BEGIN Page Title -->
    <div class="page-title new-agetitle">
        <div>
            <h1><i class="{{$module_icon}}"></i> {{ isset($module_title)?$module_title:"" }}</h1>
        </div>
    </div>
    <!-- END Page Title -->

    <!-- BEGIN Main Content -->
    <div class="row">
      <div class="col-md-12">

          <div class="box">
            <div class="box-title">
              <h3>
                <i class="{{$create_icon}}"></i>
                {{ isset($page_title)?$page_title:"" }}
            </h3>
            <div class="box-tool">
                <a data-action="collapse" href="#"></a>
                <a data-action="close" href="#"></a>
            </div>
        </div>
        <div class="box-content edit-space">

            @include('admin.layout._operation_status')  

                <form class="form-horizontal" onsubmit="return addLoader()" id="validation-form1" method="POST" action="{{ $module_url_path }}/store">
                  {{ csrf_field() }}

                  <div class="row">
                    <div class="col-lg-6">
                      <div class="row">
      						      <div class="form-group">
                              <label class="col-sm-3 col-lg-4 control-label" for="state"> 
                                  {{translation('exam_type')}}
                              </label>
                              <div class="col-sm-6 col-lg-8 controls">
                                    <input type="text" name="exam_type" data-rule-required='true' id="exam_type" value="{{old('exam_type')}}" class="form-control" placeholder="{{translation('enter_exam_type')}}" pattern="[a-zA-Z0-9 àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$">
                                    <span class='help-block'>{{ $errors->first('exam_type') }}</span> 
                              </div>
                        </div>
                        <div class="clearfix"></div> 
                      </div>
                    </div>
                  </div>
    	                   
                  <div class="form-group">
                      <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                          <a href="{{$module_url_path}}" class="btn btn-primary">{{translation('back')}}</a>
                          <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('save')}}</button>
                      </div>
                  </div>
                
                </form>
    
</div>
</div>
</div>

<!-- END Main Content -->
<script type="text/javascript">
    /*function saveTinyMceContent()
    {
      tinyMCE.triggerSave();
    }*/
    $(document).ready(function()
                      {
      tinymce.init({
        selector: 'textarea',
        height:350,
        plugins: [
          'advlist autolink lists link image charmap print preview anchor',
          'searchreplace visualblocks code fullscreen',
          'insertdatetime media table contextmenu paste code'
        ],
        valid_elements : '*[*]',
        toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image ',
        content_css: [
          '//fast.fonts.net/cssapi/e6dc9b99-64fe-4292-ad98-6974f93cd2a2.css',
          '//www.tinymce.com/css/codepen.min.css'
        ]
      }
                  );
    }
                     );
  </script>

  <script>
    $('#exam_type').on('keyup',function(){
      var exam_type   = $('#exam_type').val();
      $.ajax({
              url  :"{{ $module_url_path }}/checkExamType",
              type :'POST',
              data :{'exam_type':exam_type,'_token':'<?php echo csrf_token();?>' },
              success:function(data){
                //$('#chat-div').html(data.html);
                  if(data.status=='success')
                  {
                    $('#err_exam_type').text();
                  }
                  if(data.status=='error')
                  {
                    $('#err_exam_type').show();
                    $('#err_exam_type').text(data.msg);
                  }
              }
      });
    });
    $('#level').on('keyup',function(){
      var level   = $('#level').val();
      if (level == "") {
        $('#err_exam_type').css('display','none');
      }
    });
  </script>



     


@stop                    
