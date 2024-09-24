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
        <i class="{{$edit_icon}}"></i>
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
              <h3><i class="{{$edit_icon}}"></i>{{$page_title}}</h3>
              <div class="box-tool">
              </div>
          </div>  
          <div class="box-content">
             @include('schooladmin.layout._operation_status')
             @if(isset($arr_data) && count($arr_data)>0)
              <form method="post" onsubmit="return addLoader()"  action="{{$module_url_path}}/update/{{$enc_id}}"  class="form-horizontal" id="validation-form1">
                  {{ csrf_field() }}

                  <div class="form-group">
                      <label class="col-sm-3 col-lg-2 control-label">{{translation('bus_driver_name')}}<i class="red">*</i></label>
                      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
                            <div class="frmSearch">
                              <select  class="form-control" name="bus_driver_id" id="bus_driver_id" data-rule-required="true">
                                <option value="">{{translation('select')}} {{translation('bus_driver_name')}}</option>
                              @if(isset($arr_driver) && $arr_driver != '')
                                  @foreach($arr_driver as $key => $driver)
                                      <option value="{{$driver->user_id}}" @if($driver->user_id == $arr_data['driver_id']) selected="" @endif >{{isset($driver->user_name) ? $driver->user_name : "NA"  }}</option>
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
                               <input type="text" name="bus_type" id="bus_type" class="form-control" data-rule-required='true' data-rule-maxlength="100" placeholder="{{translation('enter_bus_type')}}" value="{{isset($arr_data['bus_type'])?$arr_data['bus_type']:''}}" /> 
                            </div>
                          <span class="help-block"></span>
                      </div>
                  </div>

                  <div class="form-group">
                      <label class="col-sm-3 col-lg-2 control-label">{{translation('bus_number')}}<i class="red">*</i></label>
                      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
                            <div class="frmSearch">
                               <input type="text" name="bus_number" id="bus_number" class="form-control" value="{{isset($arr_data['bus_no']) && $arr_data['bus_no']!='' ? $arr_data['bus_no'] : ''}}" data-rule-required='true' data-rule-pattern="(^[a-zA-Z0-9- ]*$)" data-rule-maxlength="100" placeholder="{{translation('enter')}} {{translation('bus_number')}}" />
                              <div id="suggesstion-box" ></div>
                            </div>
                          <span class="help-block"></span>
                      </div>
                  </div>

                  <div class="form-group">
                      <label class="col-sm-3 col-lg-2 control-label">{{translation('bus_plate_number')}}<i class="red">*</i></label>
                      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
                            <div class="frmSearch">
                               <input type="text" name="bus_plate_number" id="bus_plate_number" value="{{isset($arr_data['bus_plate_no']) && $arr_data['bus_plate_no']!='' ? $arr_data['bus_plate_no'] : ''}}" class="form-control" data-rule-required='true' data-rule-pattern="(^[a-zA-Z0-9- ]*$)" data-rule-maxlength="100" placeholder="{{translation('enter')}} {{translation('bus_plate_number')}}" />
                              <div id="suggesstion-box" ></div>
                            </div>
                          <span class="help-block"></span>
                      </div>
                  </div>

                  <div class="form-group">
                      <label class="col-sm-3 col-lg-2 control-label">{{translation('bus_capacity')}}<i class="red">*</i></label>
                      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
                            <div class="frmSearch">
                               <input type="text" name="bus_capacity" id="bus_capacity" value="{{isset($arr_data['bus_capacity']) && $arr_data['bus_capacity']!='' ? $arr_data['bus_capacity'] : ''}}" class="form-control"  data-rule-digits="true" data-rule-required='true' data-rule-maxlength="3" placeholder="{{translation('enter')}} {{translation('bus_capacity')}}" />
                              <div id="suggesstion-box" ></div>
                            </div>
                          <span class="help-block"></span>
                      </div>
                  </div>                                    

                 <div class="form-group">
                  <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                      <a href="{{$module_url_path}}" class="btn btn-primary">{{translation('back')}}</a>
                      <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('update')}}</button>
                  </div>
                </div>  
              </form>
              @endif
          </div>
      </div>
  </div>
</div>
@endsection
