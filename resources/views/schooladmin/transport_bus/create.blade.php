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
                      <label class="col-sm-3 col-lg-2 control-label">{{translation('bus_driver_name')}}<i class="red">*</i></label>
                      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
                            <div class="frmSearch">
                              <select  class="form-control" name="bus_driver_id" id="bus_driver_id" data-rule-required="true">
                                <option value="">{{translation('select')}} {{translation('bus_driver_name')}}</option>
                              @if(isset($arr_data) && $arr_data != '')
                                  @foreach($arr_data as $key => $employee)
                                      <option value="{{$employee->user_id}}">{{isset($employee->user_name)? $employee->user_name : "NA"  }}</option>
                                  @endforeach
                              @endif
                              </select>
                              <div id="suggesstion-box"></div>
                            </div>
                          <span class="help-block"></span>
                      </div>
                  </div>

                  <div class="form-group">
                      <label class="col-sm-3 col-lg-2 control-label">{{translation('bus_type')}}<i class="red">*</i></label>
                      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
                            <div class="frmSearch">
                               <input type="text" name="bus_type" id="bus_type" class="form-control" data-rule-required='true' data-rule-maxlength="100" placeholder="{{translation('enter_bus_type')}}" /> 
                            </div>
                          <span class="help-block"></span>
                      </div>
                  </div>

                  <div class="form-group">
                      <label class="col-sm-3 col-lg-2 control-label">{{translation('bus_number')}}<i class="red">*</i></label>
                      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
                            <div class="frmSearch">
                               <input type="text" name="bus_number" id="bus_number" class="form-control" data-rule-required='true' data-rule-pattern="(^[a-zA-Z0-9- ]*$)" data-rule-maxlength="100" placeholder="{{translation('enter')}} {{translation('bus_number')}}" />
                              <div id="suggesstion-box" ></div>
                            </div>
                          <span class="help-block"></span>
                      </div>
                  </div>

                  <div class="form-group">
                      <label class="col-sm-3 col-lg-2 control-label">{{translation('bus_plate_number')}}<i class="red">*</i></label>
                      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
                            <div class="frmSearch">
                               <input type="text" name="bus_plate_number" id="bus_plate_number" class="form-control" data-rule-pattern="(^[a-zA-Z0-9- ]*$)" data-rule-required='true' data-rule-maxlength="100" placeholder="{{translation('enter')}} {{translation('bus_plate_number')}}" />
                              <div id="suggesstion-box" ></div>
                            </div>
                          <span class="help-block"></span>
                      </div>
                  </div>


                  <div class="form-group">
                      <label class="col-sm-3 col-lg-2 control-label">{{translation('bus_capacity')}}<i class="red">*</i></label>
                      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
                            <div class="frmSearch">
                               <input type="text" name="bus_capacity" id="bus_capacity" class="form-control"  data-rule-digits="true" data-rule-required='true' data-rule-maxlength="3" placeholder="{{translation('enter')}} {{translation('bus_capacity')}}" />
                              <div id="suggesstion-box" ></div>
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
