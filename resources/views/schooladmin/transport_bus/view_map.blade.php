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
              <h3><i class="fa fa-eye"></i>{{translation('view_map')}}</h3>
              <div class="box-tool">
              </div>
          </div>  
          <div class="box-content">
             @include('schooladmin.layout._operation_status')
              <form method="post" onsubmit="return addLoader()" action="{{$module_url_path}}/add_student/{{base64_encode($arr_data['id'])}}" class="form-horizontal" id="validation-form1">
                  {{ csrf_field() }}

                  <div class="form-group">
                      <label class="col-sm-3 col-lg-2 control-label" style="text-align:left !important">{{translation('bus_number')}} :</label>
                      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
                            <div class="frmSearch">
                              <label class="control-label">{{$arr_data['bus_no']}}</label>
                              <div id="suggesstion-box"></div>
                            </div>
                          <span class="help-block" id="bus_id_error"></span>
                      </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-2 col-lg-2 control-label" style="text-align:left">{{translation('route_name')}}</label>
                    <div class="col-sm-2 col-lg-2 controls">
                        <div class="frmSearch">
                           <input type="text" name="route_name" id="route_name" class="form-control" data-rule-required='true' data-rule-pattern="(^[a-zA-Z0-9- ._]*$)" data-rule-maxlength="200" placeholder="{{translation('enter')}} {{translation('route_name')}}" value="{{isset($arr_data['get_fees_details']['route_name']) && $arr_data['get_fees_details']['route_name']!=''?$arr_data['get_fees_details']['route_name']:''}}"/>
                          <div id="suggesstion-box" ></div>
                        </div>
                      <span class="help-block"></span>
                  </div>
                </div>
                  <?php 
                    if(Session::has('transport_type')){
                      $transport_type = translation(strtolower(Session::get('transport_type')));
                    }
                    else{
                      $transport_type = '';
                    }

                  ?>

                  <div class="form-group">
                      <label class="col-sm-3 col-lg-2 control-label" style="text-align:left !important">{{translation('transport_type')}} :</label>
                      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
                            <div class="frmSearch">
                              <label class="control-label">{{$transport_type}}</label>
                              <div id="suggesstion-box"></div>
                            </div>
                          <span class="help-block" id="bus_id_error"></span>
                      </div>
                  </div>

                  <div class="form-group">
                      <label class="col-sm-3 col-lg-2 control-label" style="text-align:left !important">{{translation('fees')}} :</label>
                      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
                            <div class="frmSearch">
                              <label class="control-label">{{$arr_data['get_fees_details']['fees']}} {{config('app.project.currency')}}</label>
                              <div id="suggesstion-box"></div>
                            </div>
                          <span class="help-block" id="bus_id_error"></span>
                      </div>
                  </div>

                    <div class="form-group back-btn-form-block">
                       <div class="controls">
                          <a href="{{$module_url_path}}/add_student/{{base64_encode($arr_data['id'])}}" class="btn btn-primary" >  {{translation('reset_route')}} </a>
                          <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('update')}}</button>
                       </div>
                       <div class="clearfix"></div>
                    </div>
                    <div class="col-md-12 ajax_messages">
                      <div class="alert alert-success" id="success" style="display:none;">
                      </div>
                      <div class="alert alert-danger" id="error" style="display:none;">
                      </div>
                   </div>
              </form>
            <div id="dvMap" style="width: 1100px; height: 600px"></div>
          </div>
      </div>
  </div>
</div>
<script type="text/javascript">

    var BASE_URL                                                        = '{{url('/')}}';
    var MODULE_URL_PATH                                                 = '{{$module_url_path}}';
    var transport_type                                                  = '{{Session::get('transport_type')}}';
    var bus_id                                                          = '{{Session::get('bus_id')}}';
    var _token                                                          = '{{csrf_token()}}';
    var source_location                                                 = '{{$school_name!=false ? $school_name : "NA"}}';
    var source_lat                                                      = '{{$school_lat!=false ? $school_lat : "NA"}}';
    var source_lng                                                      = '{{$school_lang!=false ? $school_lang : "NA"}}';
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
<script src="{{ url('/') }}/js/school_admin/transport_route/transport_route_add.js"></script>
@endsection