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
          <a href="{{$module_url_path}}">{{str_plural($module_title)}}</a>
        </li>
        <span class="divider">
            <i class="fa fa-angle-right"></i>
        </span>
        <i class="{{$create_icon}}"></i>
        <li class="active">{{$page_title}}</li>
    </ul>
</div>
<!-- END Breadcrumb -->
<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="{{$module_icon}}"></i>{{str_plural($module_title)}}</h1>

    </div>
</div>
<!-- END Page Title -->


     <!-- BEGIN Tiles -->
<div class="row">
  <div class="col-md-12">
      <div class="box  box-navy_blue">
          <div class="box-title">
              <h3><i class="{{$create_icon}}"></i>{{$page_title}}</h3>
              <div class="box-tool">
              </div>
          </div>  
          <div class="box-content">
             @include('schooladmin.layout._operation_status')
              <form method="post" onsubmit="return addLoader()"  action="{{$module_url_path}}/store"  class="form-horizontal" id="validation-form1">
                  {{ csrf_field() }}

                  
                  <div class="form-group">
                      <label class="col-sm-3 col-lg-2 control-label">{{translation('kid_no')}}<i class="red">*</i></label>
                      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
                            <div class="frmSearch">
                               <input type="text" name="kid_no" id="kid_no" class="form-control"  data-rule-number="true" data-rule-required='true' max="100" min="1" placeholder="{{translation('enter')}} {{translation('kid_no')}}" />
                              <div id="suggesstion-box" ></div>
                            </div>
                          <span class="help-block"></span>
                      </div>
                  </div>

                  <div class="form-group">
                      <label class="col-sm-3 col-lg-2 control-label">{{translation('discount')}}<i class="red">*</i></label>
                      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
                            <div class="frmSearch">
                              <input type="text" name="discount" id="discount" class="form-control"   data-rule-required='true' maxlength="100" placeholder="{{translation('enter')}} {{translation('discount') }}" max="100" min="1" />
                              <small>Note:{{ translation('discount_is_in_percentage') }}</small>
                              <div id="suggesstion-box"></div>
                            </div>
                          <span class="help-block"></span>
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
</div>
@endsection
