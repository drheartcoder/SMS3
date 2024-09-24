@extends('student.layout.master')                
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="{{ url($student_panel_slug) }}/dashboard">{{translation('dashboard')}}</a>
        </li>
        <span class="divider">
            <i class="fa fa-angle-right"></i>
        </span>
        <li> 
          <i class="{{$module_icon}}"></i>
          <a href="{{$module_url_path}}">{{$page_title}}</a>
        </li>
    </ul>
</div>
<!-- END Breadcrumb -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="fa fa-bus"></i> {{$page_title}}</h1>
    </div>
</div>

<!-- BEGIN Main Content -->
{{-- <div class="row"> --}}

   <div class="box {{ $theme_color }}">
      <div class="box-title">
         <h3>
            <i class="fa fa-eye"></i>
           {{$module_title}}  
         </h3>
         <div class="box-tool">
         </div>
      </div>

          <div class="clearfix"></div>
            <div class="box-content view-details-seciton-main details-section-main-block">
            <div class="row">

              @if(isset($arr_bus) && count($arr_bus)>0)  
              
                @foreach($arr_bus as $data)

                <div class="@if(count($arr_bus)==1) col-md-12 @else col-md-6 @endif">
                    <div class="details-infor-section-block">
                        {{translation(strtolower($data['type']))}}
                    </div>
                    
                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('bus_driver_name')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{isset($data['bus_details']['driver_details']['first_name']) ? ucfirst($data['bus_details']['driver_details']['first_name']) :''}} {{isset($data['bus_details']['driver_details']['last_name']) ? ucfirst($data['bus_details']['driver_details']['last_name']) :''}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('mobile_no')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{isset($data['bus_details']['driver_details']['mobile_no']) ? ucfirst($data['bus_details']['driver_details']['mobile_no']) :''}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('bus_number')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{isset($data['bus_details']['bus_no'])? $data['bus_details']['bus_no']:''}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('bus_plate_number')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{isset($data['bus_details']['bus_plate_no']) ? $data['bus_details']['bus_plate_no'] :''}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('bus_fees')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{isset($data['fees_details']['fees']) ? $data['fees_details']['fees'] :''}} {{config('app.project.currency')}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                </div>
                @endforeach
              @else
                <div class="alert alert-danger" style="text-align:center">{{translation("travel_by_school_bus_and_enjoy_travelling_with_your_friends")}}!</div>
              @endif  
            </div>
            <div class="row">
              <div class="@if(count($arr_bus)==1) col-md-12 @else col-md-6 @endif">
                <div id="dvMap" style="width: 100%; height: 300px; padding:10px"></div>
              </div>  
              <div class="col-md-6">
                <div id="dvMap2" style="width: 100%; height: 300px; padding:10px"></div>
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
    var arr_count                                                       = '{{count($arr_bus)}}';
    var source_location                                                 = '{{$school_name!=false ? $school_name : "NA"}}';
    var source_lat                                                      = '{{$school_latitude!=false ? $school_latitude : "NA"}}';
    var source_lng                                                      = '{{$school_longitude!=false ? $school_longitude : "NA"}}';
    var school_latitude                                                      = '{{$school_latitude!=false ? $school_latitude : "NA"}}';
    var school_longitude                                                      = '{{$school_longitude!=false ? $school_longitude : "NA"}}';
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

var pickup_students = new Array();
var drop_students = new Array();
<?php 
    foreach($pick_route as $pick){
?>
    arr_temp = [];
    arr_temp[0] = "{{$pick[0]}}";
    arr_temp[1] = "{{$pick[1]}}";
    arr_temp[2] = "{{$pick[2]}}";
    arr_temp[3] = "{{$pick[3]}}";

    pickup_students.push(arr_temp);
<?php } ?>     
<?php 
    foreach($drop_route as $drop){
?>
    arr_temp = [];
    arr_temp[0] = "{{$drop[0]}}";
    arr_temp[1] = "{{$drop[1]}}";
    arr_temp[2] = "{{$drop[2]}}";
    arr_temp[3] = "{{$drop[3]}}";

    drop_students.push(arr_temp);
<?php } ?>     

</script>

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{config('constants.GOOGLE_API_KEY')}}&libraries=places"></script>
<script src="{{ url('/') }}/js/school_admin/transport_route/SlidingMarker.js"></script>
<script src="{{ url('/') }}/js/school_admin/transport_route/markerAnimate.js"></script>
<script src="{{ url('/') }}/js/parent/pickup_route.js"></script>
@endsection


