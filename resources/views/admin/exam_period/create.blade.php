@extends('admin.layout.master')    
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
    <li>
      <i class="fa fa-home">
      </i>
      <a href="{{ url($admin_panel_slug.'/dashboard') }}"> {{translation('dashboard')}}
      </a>
    </li>
    <span class="divider">
      <i class="fa fa-angle-right">
      </i>
      <i class="fa {{$module_icon}}">
      </i>
    </span> 
    <li >  <a href="{{$module_url_path}}"> {{ isset($module_title)?$module_title:"" }}</a>
    </li>
    <span class="divider">
      <i class="fa fa-angle-right">
      </i>
      <i class="fa {{$create_icon}}">
      </i>
    </span> 
    <li class="active">  {{ isset($page_title)?$page_title:"" }}
    </li>
  </ul>
</div>
<!-- END Breadcrumb -->


<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="fa {{$module_icon}}"></i> {{ isset($module_title)?$module_title:"" }} </h1>
    </div>
</div><!-- END Page Title -->

<!-- BEGIN Main Content -->
<div class="row">
  <div class="col-md-12">
    <div class="box {{ $theme_color }}">
      <div class="box-title">
        <h3>
          <i class="fa {{$create_icon}}">
          </i> {{ isset($page_title)?$page_title:"" }} 
        </h3>
        <div class="box-tool">
        </div>
      </div>
      <div class="box-content">
      

        @include('admin.layout._operation_status')
   

          <form method="POST" onsubmit="return addLoader()" id="validation-form1" class="form-horizontal" action="{{ $module_url_path}}/store" >
                
                {{ csrf_field() }}              


               
                            <div class="row">
                                <div class="col-lg-6">
                                <div class="row">

                                <div class="form-group">
                                      
                                  <label class="col-sm-3 col-lg-4 control-label" for="state">{{translation('exam_period')}}  
                                  <i class="red">*</i>
                                  </label>
                                  <div class="col-sm-4 col-lg-8 controls">
                                  
                                    <input type="text" name="name" class="form-control add-stundt"  value="{{old('name')}}" placeholder="{{translation('enter_exam_period')}}"  data-rule-required="true" pattern="[a-zA-Z0-9 àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" />
                                  
                                  
                                  <span class='help-block'>{{ $errors->first('name') }}</span>

                                </div> 
                              </div>
                                    </div>
                                </div>
                            </div>
                       
                <br>
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
  
@endsection
