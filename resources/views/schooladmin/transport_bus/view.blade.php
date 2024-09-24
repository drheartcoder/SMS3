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
                    <div class="details-infor-section-block">
                        {{translation('details')}}
                    </div> 
                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('bus_driver_name')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                      {{(isset($arr_data['driver_details']['first_name']) && isset($arr_data['driver_details']['last_name'])) && ($arr_data['driver_details']['first_name']!='' && $arr_data['driver_details']['last_name']!='') ? $arr_data['driver_details']['first_name'].' '.$arr_data['driver_details']['last_name'] : ''}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('bus_number')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                      {{isset($arr_data['bus_no']) && $arr_data['bus_no']!='' ? $arr_data['bus_no'] : ''}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('bus_plate_number')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                      {{isset($arr_data['bus_plate_no']) && $arr_data['bus_plate_no']!='' ? $arr_data['bus_plate_no'] : ''}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('bus_capacity')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                      {{isset($arr_data['bus_capacity']) && $arr_data['bus_capacity']!='' ? $arr_data['bus_capacity'] : ''}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                   

                  </div>
                  <table class="table table-advance" id="table_module">
                  <thead>
                     <tr>
                        <th>{{translation('sr_no')}}</th>
                        <th class="sorting_disabled" width="10%">
                           <a class="sort-descs" >{{translation('name')}}</a><br>
                        </th>
                        <th class="sorting_disabled" width="10%">
                           <a class="sort-descs" >{{translation('level')}}</a><br>
                        </th>
                        <th>
                            <a class="sort-descs" >{{translation('transport_type')}}</a><br>
                        </th>
                        <th class="sorting_disabled" width="35%">
                           <a class="sort-descs" >{{translation('pickup_address')}}</a><br>
                        </th>
                        <th class="sorting_disabled" width="35%">
                           <a class="sort-descs" >{{translation('drop_address')}}</a><br>
                        </th>
                     </tr>
                  </thead>
                  <tbody>
                  <?php $count=1; ?>
                  @foreach($arr_students as $student)
                  
                  <?php

                    $first_name = isset($student['student_details']['get_user_details']['first_name']) ? ucfirst($student['student_details']['get_user_details']['first_name']) : "";
                    $last_name = isset($student['student_details']['get_user_details']['last_name']) ? ucfirst($student['student_details']['get_user_details']['last_name']) : "";
                    $name = $first_name.' '.$last_name;
                    $pickup_address = isset($student['student_details']['pickup_address']) ? $student['student_details']['pickup_address'] : "";
                    $drop_address = isset($student['student_details']['drop_address']) ? $student['student_details']['drop_address'] : "";
                    $level = isset($student['student_details']['get_level_class']['level_details']['level_name']) ? $student['student_details']['get_level_class']['level_details']['level_name'] : "";
                    $type = isset($student['type'])? translation(strtolower($student['type'])): $student['type'] ; 
                    
                  ?>
                    <tr>
                      <td>
                        {{$count++}}
                      </td>
                      <td>
                        {{$name}}    
                      </td>
                      <td>
                        {{$level}}
                      </td>
                      <td>
                        {{$type}}
                      </td>
                      <td>
                        {{$pickup_address}}
                      </td>
                      <td>
                        {{$drop_address}}
                      </td>
                    </tr>
                  
                  @endforeach 
                  </tbody>
               </table>
            </div>
            <div id="hide_row" class="alert alert-danger" style="text-align:center" hidden>{{translation('no_data_available')}}   
            </div>
             <div class="form-group back-btn-form-block">
                 <div class="controls">
                    <a href="{{ $module_url_path }}" class="btn btn-primary" style="float: right;margin-top:20px;" > <i class="fa fa-arrow-left"></i>  {{translation('back')}} </a>
                 </div>
                 <div class="clearfix"></div>
              </div>
          </div>
            </div>
          
    
    
{{-- </div> --}}
<!-- END Main Content -->
@stop

