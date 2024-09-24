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
                <i class="fa fa-book"></i>
                <a href="{{ $module_url_path }}">{{ $module_title or ''}}</a>
            </span> 
            <span class="divider">
                <i class="fa fa-angle-right"></i>
                  <i class="fa fa-edit"></i>
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
                <i class="{{$edit_icon}}"></i>
                {{ isset($page_title)?$page_title:"" }}
            </h3>
            <div class="box-tool">
                <a data-action="collapse" href="#"></a>
                <a data-action="close" href="#"></a>
            </div>
        </div>
        <div class="box-content">

          @include('admin.layout._operation_status')  
          
                <form class="form-horizontal" onsubmit="return addLoader()" id="validation-form1" method="POST" action="{{ $module_url_path }}/update/{{$enc_id}}">
                  {{ csrf_field() }}
                  <div class="row">
                    <div class="col-lg-6">
                      <div class="row">
                            
                        <div class="form-group"> 
                            <label class="col-sm-3 col-lg-4 control-label" for="state"> 
                              {{translation('exam_type')}}
                              <i class="red">*</i>
                            </label>
                            <div class="col-sm-6 col-lg-8 controls">
                              <input type="text" name="exam_type" value="{{isset($arr_data['exam_type'])?$arr_data['exam_type']:''}}" class="form-control add-stundt" data-rule-required='true' placeholder="{{translation('enter_exam_type')}}" pattern="[a-zA-Z0-9 àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" >
                               
                              <span class='help-block'>{{ $errors->first('exam_type') }}</span> 
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
                  </div>
                </form>

         
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

@stop                    
