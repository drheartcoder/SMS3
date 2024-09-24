@extends('schooladmin.layout.master')                
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="{{ url($school_admin_panel_slug) }}/dashboard">{{translation('dashboard')}}</a>
        </li>
        <span class="divider">
            <i class="fa fa-angle-right"></i>
        </span>
        <li> 
          <i class="{{$module_icon}}"></i>
          <a href="{{$module_url_path}}">{{$module_title}}</a>
        </li>

        <span class="divider">
          <i class="fa fa-angle-right"></i>
        </span>
            <i class="fa fa-eye"></i>
            <li class="active">{{$page_title}}</li>
    </ul>
</div>
<!-- END Breadcrumb -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="{{$module_icon}}"></i> {{$module_title}}</h1>
    </div>
</div>

<!-- BEGIN Main Content -->
{{-- <div class="row"> --}}

   <div class="box {{ $theme_color }}">
      <div class="box-title">
         <h3>
            <i class="fa fa-eye"></i>
           {{$page_title}}  
         </h3>
         <div class="box-tool">
         </div>
      </div>

          <div class="clearfix"></div>
          <div class="box-content view-details-seciton-main details-section-main-block">
            <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('bus_number')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                      {{isset($arr_data['bus_details']['bus_no']) && $arr_data['bus_details']['bus_no']!='' ? $arr_data['bus_details']['bus_no'] : ''}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('transport_type')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                      {{isset($arr_data['transport_type']) && $arr_data['transport_type']!='' ? ucwords($arr_data['transport_type']) : ''}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('route_name')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                      {{isset($arr_data['route_name']) && $arr_data['route_name']!='' ? $arr_data['route_name'] : ''}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('target_location')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                      {{isset($arr_data['target_location']) && $arr_data['target_location']!='' ? $arr_data['target_location'] : ''}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('total_stops')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                      {{isset($arr_data['route_stop_details']) ? count($arr_data['route_stop_details']) : '0'}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                    <div class="form-group back-btn-form-block">
                       <div class="controls">
                          <a href="{{ $module_url_path }}" class="btn btn-primary" style="float: right;margin: 20px 0" > <i class="fa fa-arrow-left"></i> Back </a>
                       </div>
                       <div class="clearfix"></div>
                    </div>                    
                    
                    <div id="dvMap" style="width: 100%; height: 600px"></div>

                  </div>
                </div>
            </div>
          </div>
{{-- </div> --}}
<!-- END Main Content -->
<script type="text/javascript">
    var BASE_URL               = '{{url("/")}}';
    var MODULE_URL_PATH        = '{{$module_url_path}}';
    var transport_type         = "{{isset($arr_data['transport_type']) && $arr_data['transport_type']!='' ? ucwords($arr_data['transport_type']) : ''}}";
    var destination_location   = "{{isset($arr_data['target_location']) && $arr_data['target_location']!='' ? $arr_data['target_location'] : ''}}";
    var destination_lat        = "{{isset($arr_data['target_location_lat']) && $arr_data['target_location_lat']!='' ? ucwords($arr_data['target_location_lat']) : ''}}";
    var destination_lng        = "{{isset($arr_data['target_location_lang']) && $arr_data['target_location_lang']!='' ? ucwords($arr_data['target_location_lang']) : ''}}";
    var source_location        = '{{$school_name!=false ? $school_name : "NA"}}';
    var source_lat             = '{{$school_lat!=false ? $school_lat : "NA"}}';
    var source_lng             = '{{$school_lang!=false ? $school_lang : "NA"}}';
    var arr_pickup_drop        = '{{isset($arr_data["route_stop_details"]) ? json_encode($arr_data["route_stop_details"]) : ""}}';
    arr_pickup_drop            = JSON.parse(arr_pickup_drop.replace(/&quot;/g,'"'));
    var route_id               = '{{$enc_id}}';
    var msg_pickup             = '{{translation("pickup")}}';
    var msg_drop               = '{{translation("drop")}}';
    var msg_source             = '{{translation("source")}}';
    var msg_destination        = '{{translation("destination")}}';    
    var msg_location           = '{{translation("location")}}';    
</script>

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{config('constants.GOOGLE_API_KEY')}}&libraries=places"></script>
<script src="{{ url('/') }}/js/school_admin/transport_route/SlidingMarker.js"></script>
<script src="{{ url('/') }}/js/school_admin/transport_route/markerAnimate.js"></script>
<script src="{{ url('/') }}/js/school_admin/transport_route/transport_route_view.js"></script>

@stop