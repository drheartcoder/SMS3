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

<?php
  $bus_id_fk            = isset($arr_data['bus_id_fk']) && $arr_data['bus_id_fk']!='' ? $arr_data['bus_id_fk'] : '';
  $transport_type       = isset($arr_data['transport_type']) && $arr_data['transport_type']!='' ? $arr_data['transport_type'] : '';
  $route_name           = isset($arr_data['route_name']) && $arr_data['route_name']!='' ? $arr_data['route_name'] : '';
  $target_location      = isset($arr_data['target_location']) && $arr_data['target_location']!='' ? $arr_data['target_location'] : '';
  $target_location_lat  = isset($arr_data['target_location_lat']) && $arr_data['target_location_lat']!='' ? $arr_data['target_location_lat'] : '';
  $target_location_lang = isset($arr_data['target_location_lang']) && $arr_data['target_location_lang']!='' ? $arr_data['target_location_lang'] : '';
?>
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
              <form method="post" onsubmit="return addLoader()"  action="{{$module_url_path}}/update/{{$enc_id}}"  class="form-horizontal" id="validation-form1">
                  {{ csrf_field() }}

                  <div class="form-group">
                      <label class="col-sm-3 col-lg-2 control-label">{{translation('bus_number')}}<i class="red">*</i></label>
                      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
                            <div class="frmSearch">
                              <select  class="form-control" name="bus_id" id="bus_id" data-rule-required="true">
                                <option value="">{{translation('select')}} {{translation('bus_number')}}</option>
                              @if(isset($arr_bus) && $arr_bus != '')
                                  @foreach($arr_bus as $key => $val)
                                      <option value="{{$val['id']}}" @if($val["id"]==$bus_id_fk) selected @endif>{{(isset($val['bus_no']) && isset($val['bus_plate_no'])) && $val['bus_no']!='' && $val['bus_plate_no']!='' ? $val['bus_no']." ( ".$val['bus_plate_no']." )" : "" }}</option>
                                  @endforeach
                              @endif
                              </select>
                              <div id="suggesstion-box"></div>
                            </div>
                          <span class="help-block" id="bus_id_error"></span>
                      </div>
                  </div>
                  <div class="form-group">
                      <label class="col-sm-3 col-lg-2 control-label">{{translation('transport_type')}}<i class="red">*</i></label>
                      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
                            <div class="frmSearch">
                              <select  class="form-control" name="transport_type" id="transport_type" data-rule-required="true">
                                <option value="">{{translation('select')}} {{translation('transport_type')}}</option>
                                <option value="pickup" @if($arr_data['transport_type']=='pickup') selected @endif>{{translation('pickup')}}</option>
                                <option value="drop" @if($arr_data['transport_type']=='drop') selected @endif>{{translation('drop')}}</option>
                              </select>
                              <div id="suggesstion-box"></div>
                            </div>
                          <span class="help-block"></span>
                      </div>
                  </div>

                  <div class="form-group">
                      <label class="col-sm-3 col-lg-2 control-label">{{translation('route_name')}}<i class="red">*</i></label>
                      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
                            <div class="frmSearch">
                               <input type="text" name="route_name" id="route_name" class="form-control" data-rule-required='true' data-rule-pattern="(^[a-zA-Z0-9- ._]*$)" data-rule-maxlength="200" value="{{$route_name}}" placeholder="{{translation('enter')}} {{translation('route_name')}}" />
                              <div id="suggesstion-box" ></div>
                            </div>
                          <span class="help-block"></span>
                      </div>
                  </div>

                  <div class="form-group">
                      <label class="col-sm-3 col-lg-2 control-label">{{translation('target_location')}}<i class="red">*</i></label>
                      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
                            <div class="frmSearch">
                               <input type="text" name="target_location" id="address" class="form-control" data-rule-required='true' data-rule-maxlength="500" value="{{$target_location}}" placeholder="{{translation('enter')}} {{translation('target_location')}}" />
                              <div id="suggesstion-box" ></div>
                            </div>
                          <span class="help-block"></span>
                      </div>
                  </div>

                  <div class="geo-details">
                            <input type="hidden" id="lat" name="latitude" value="{{$target_location_lat}}">
                            <input type="hidden" id="lng" name="longitude" value="{{$target_location_lang}}">
                            <input type="hidden" id="locality" name="city" >
                            <input type="hidden" id="administrative_area_level_1" name="state" >
                            <input type="hidden" id="country" name="country" >
                            <input type="hidden" id="postal_code" name="postalcode" >
                            <input type="hidden" id="json_pickup_drop_point" name="json_pickup_drop_point" value='' readonly /> 
                            <input type="hidden" id="json_arr_student" name="json_arr_student" value='' readonly /> 
                            <input type="hidden" id="bus_capacity" name="bus_capacity" value='' readonly /> 
                  </div>

                 <div class="form-group">
                  <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                      <a href="{{$module_url_path}}" class="btn btn-primary">{{translation('back')}}</a>
                      <button type="button"  id="submit_button" class="btn btn-primary">{{translation('save')}}</button>
                  </div>
                </div>  
              </form>
            {{-- <div class=""  style="width: 1550px; height: 600px"></div> --}}
             <div class="col-sm-12 col-md-12 col-lg-12">
             <div id="dvMap" style="height: 600px" class="map-block">
             </div>
             </div>
          </div>
      </div>
  </div>
</div>

<script type="text/javascript">
    var BASE_URL          = '{{url('/')}}';
    var MODULE_URL_PATH   = '{{$module_url_path}}';
    var _token            = '{{csrf_token()}}';
    var source_location   = '{{$school_name!=false ? $school_name : "NA"}}';
    var source_lat        = '{{$school_lat!=false ? $school_lat : "NA"}}';
    var source_lng        = '{{$school_lang!=false ? $school_lang : "NA"}}';    
    var route_id          = '{{$enc_id}}';
    var transport_type    = '{{$transport_type}}';
    var bus_id            = '{{$bus_id_fk}}';
    var arr_pickup_drop   = '{{isset($arr_data["route_stop_details"]) ? json_encode($arr_data["route_stop_details"]) : ""}}';
    arr_pickup_drop       = JSON.parse(arr_pickup_drop.replace(/&quot;/g,'"'));

    var msg_pickup_location_is_required                                 = '{{translation("location_is_required")}}';
    var msg_drop_location_is_required                                   = '{{translation("minimum_1_drop_location_is_required")}}';
    var msg_error                                                       = '{{translation("error")}}';
    var msg_pickup                                                      = '{{translation("pickup")}}';
    var msg_drop                                                        = '{{translation("drop")}}';
    var msg_source                                                      = '{{translation("source")}}';
    var msg_destination                                                 = '{{translation("destination")}}';
    var msg_add                                                         = '{{translation("add")}}';
    var msg_remove                                                      = '{{translation("remove")}}';
    var msg_warning                                                     = '{{translation("warning")}}';
    var msg_update_info                                                 = '{{translation("update_info")}}';
    var msg_stop_no                                                     = '{{translation("stop_no")}}';
    var msg_stop_name                                                   = '{{translation("stop_name")}}';
    var msg_landmark                                                    = '{{translation("landmark")}}';
    var msg_stop_fees                                                   = '{{translation("stop_fees")}}';
    var msg_stop_radius                                                 = '{{translation("stop_radius")}}';
    var msg_location                                                    = '{{translation("location")}}';
    var msg_no_student_assigned_to_bus                                  = '{{translation("no_student_assigned_to_bus")}}';
    var msg_maximum_15_stops_are_allowed                                = '{{translation("maximum_15_stops_are_allowed")}}';
    var msg_the_stop_no_field_cannot_be_empty                           = '{{translation("the_stop_no_field_cannot_be_empty")}}';
    var msg_the_stop_name_field_cannot_be_empty                         = '{{translation("the_stop_name_field_cannot_be_empty")}}';
    var msg_the_landmark_field_cannot_be_empty                          = '{{translation("the_landmark_field_cannot_be_empty")}}';
    var msg_the_stop_fees_field_cannot_be_empty                         = '{{translation("the_stop_fees_field_cannot_be_empty")}}';
    var msg_please_enter_valid_stop_fees                                = '{{translation("please_enter_valid_stop_fees")}}';
    var msg_the_stop_radius_field_cannot_be_empty                       = '{{translation("the_stop_radius_field_cannot_be_empty")}}';
    var msg_stop_no_field_cannot_be_duplicate                           = '{{translation("stop_no_field_cannot_be_duplicate")}}';
    var msg_no_student_found                                            = '{{translation("no_student_found")}}';
    var msg_please_fill_all_information_for_current_pickupdrop_location = '{{translation("please_fill_all_information_for_current_pickupdrop_location")}}';
    var msg_bus_capacity_is_full                                        = '{{translation("bus_capacity_is_full")}}';
    var msg_no_student_available_for_pickup                             = '{{translation("no_student_available_for_pickup")}}';
    var msg_no_student_available_for_drop                               = '{{translation("no_student_available_for_drop")}}';

</script>

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{config('constants.GOOGLE_API_KEY')}}&libraries=places"></script>
<script src="{{ url('/') }}/js/school_admin/transport_route/SlidingMarker.js"></script>
<script src="{{ url('/') }}/js/school_admin/transport_route/markerAnimate.js"></script>
<script src="{{ url('/') }}/js/school_admin/transport_route/transport_route_edit.js"></script>
@endsection