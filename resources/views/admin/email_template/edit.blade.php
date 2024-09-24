@extends('admin.layout.master')            
@section('main_content')

<style>
    .school-admin-main .btn.btn-success{right: auto; left: 15px;}
</style>

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
        <h1><i class="{{$module_icon}}"></i> {{ $module_title or ''}}</h1>
    </div>
</div>
<!-- END Page Title -->
<!-- BEGIN Breadcrumb -->

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

                    <form method="POST" id="validation-form" class="form-horizontal" action="{{$module_url_path}}/update/{{base64_encode($arr_data['id'])}}" enctype="multipart/form-data">

                        {{ csrf_field() }}

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="row">

                                    <div class="form-group">
                                        <label class="col-sm-3 col-lg-4 control-label" for="email"> {{translation('email_template_name')}}
                                        </label>
                                        <div class="col-sm-6 col-lg-8 controls">

                                            <input type="text" name="template_name" required="" placeholder="{{translation('email_template_name')}}" value="{{$arr_data['template_name'] or ''}}" class="form-control add-stundt" />

                                        </div>
                                        <span class='help-block'> {{ $errors->first('template_name') }} </span>
                                    </div>
                                    <div class="clearfix"></div>

                                    <div class="form-group">
                                        <label class="col-sm-3 col-lg-4 control-label" for="email">{{translation('email_template_from')}}
                                            <i class="red">*</i>
                                        </label>
                                        <div class="col-sm-6 col-lg-8 controls">
                                            <input type="text" name="template_from" class="form-control" data-rule-required="true" data-rule-maxlength="255" value="{{isset($arr_data['template_from'])?$arr_data['template_from']:''}}" placeholder="{{translation('email_template_from')}}">
                                        </div>
                                        <span class='help-block'> {{ $errors->first('template_from') }} </span>
                                    </div>
                                    <div class="clearfix"></div>

                                    <div class="form-group">
                                        <label class="col-sm-3 col-lg-4 control-label" for="email"> {{translation('email_template_from_email')}}
                                            <i class="red">*</i>
                                        </label>
                                        <div class="col-sm-6 col-lg-8 controls">
                                            <input type="text" name="email_template_from" class="form-control" placeholder="{{translation('email_template_from')}}" data-rule-required="true" data-rule-maxlength="255" value="{{isset($arr_data['template_from_mail'])?$arr_data['template_from_mail']:''}}">
                                        </div>
                                        <span class='help-block'> {{ $errors->first('template_from_mail') }} </span>
                                    </div>
                                    <div class="clearfix"></div>

                                    <div class="form-group">
                                        <label class="col-sm-3 col-lg-4 control-label" for="email">

                                            {{translation('email_template_subject') }}<i class="red">*</i>

                                        </label>
                                        <div class="col-sm-6 col-lg-8 controls">

                                            <input type="text" name="template_subject" required="" placeholder="{{translation('email_template_subject')}}" value="{{$arr_data['template_subject'] or ''}}" class="form-control add-stundt" />
                                        </div>
                                        <span class='help-block'> {{ $errors->first('template_subject') }} </span>
                                    </div>
                                    <div class="clearfix"></div>

                                    <div class="form-group">
                                        <label class="col-sm-3 col-lg-4 control-label" for="email">
                                            {{ translation('email_template_body') }}<i class="red">*</i>
                                        </label>
                                        <div class="col-sm-6 col-lg-8 controls">
                                           
                                            <textarea name="template_html" class="form-control" required="" rows="10" placeholder="{{translation('email_template_body')}}">{{$arr_data['template_html'] or ''}}</textarea>

                                        </div>
                                        <span class='help-block'> {{ $errors->first('template_html') }} </span>

                                    </div>
                                    <div class="clearfix"></div>

                                    <div class="form-group">
                                        <label class="col-sm-3 col-lg-4 control-label" for="email"> {{translation('variables')}}: </label>
                                        <div class="col-sm-6 col-lg-8 controls">
                                            @if(sizeof($arr_variables)>0) @foreach($arr_variables as $variable)
                                            <br>
                                            <label> {{ $variable }} </label>
                                            @endforeach @endif
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>

                                    <div class="form-group">
                                        <div class="col-sm-6 col-lg-4 col-lg-offset-4">
                                            
                                            <a class="btn btn btn-success" target="_blank" href="{{ url($module_url_path).'/view/'.base64_encode($arr_data['id']).'/'.\Session::get('locale') }}" title="Preview">
                                                <i class="fa fa-eye"></i> {{translation('preview')}}
                                            </a>
                                    
                                        </div>
                                    </div>
                                    <div class="clearfix"></div><br/><br/>
                                    <div class="form-group">
                                        <div class="col-sm-6 col-lg-4 col-lg-offset-4">
                                            <input type="submit" id="save_btn" class="btn btn btn-primary" onclick="saveTinyMceContent()" value="{{translation('update')}}" />
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                <br>

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