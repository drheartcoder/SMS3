@extends('schooladmin.layout.master') @section('main_content')
<!-- BEGIN Breadcrumb -->

<div id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="{{ url($school_admin_panel_slug.'/dashboard') }}">{{translation('dashboard')}}</a>
        </li>
        <span class="divider">
            <i class="fa fa-angle-right"></i>
        </span>
        <li>
            <a href="{{ url($module_url_path) }}">{{$module_title}}</a>
        </li>
        <span class="divider">
        <i class="fa fa-angle-right"></i>
        </span>
        <li class="active">{{ isset($module_title)?$page_title:"" }}</li>
    </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="{{$module_icon}}"></i>{{$module_title}}</h1>
    </div>
</div>
<!-- END Page Title -->


<!-- BEGIN Tiles -->
<div class="row">
   <div class="col-md-12">
      <div class="box  box-navy_blue">
         <div class="box-title">
            <h3><i class="{{$create_icon}}"></i>{{ isset($module_title)?$page_title:"" }}</h3>
            <div class="box-tool">
            </div>
         </div>
         <div class="box-content studt-padding">
            @include('professor.layout._operation_status')   
            <div class="row ">
               <br>
            <br>
            @include('schooladmin.layout._operation_status')  
            {!! Form::open([ 'url' => $module_url_path.'/store_student/'.base64_encode($obj_bus->id),
            'method'=>'POST', 
            'class'=>'form-horizontal',
            'id'=>'validation-form1'
            ]) !!}
            {{ csrf_field() }}
            <input type="hidden" name="level_class_id" id="level_class_id"/>
            <div class="display-table">
            <div class="form-group">
                  <label class="col-sm-2 col-lg-2 control-label" style="text-align:left">{{translation('transport_type')}}<i class="red">*</i></label>
                  <div class="col-sm-2 col-lg-2 controls">
                     <select name="transport_type" id="transport_type" class="form-control" data-rule-required='true'>
                        <option value="pickup" @if(\Session::get('transport_type')=='pickup') selected @endif>{{translation('pickup')}}</option>
                        <option value="drop" @if(\Session::get('transport_type')=='drop') selected @endif>{{translation('drop')}}</option>
                     </select>
                  </div>
               </div>

                <div class="form-group">
                  <label class="col-sm-2 col-lg-2 control-label" style="text-align:left">{{translation('fees')}}<i class="red">*</i></label>
                  <div class="col-sm-2 col-lg-2 controls">
                        <div class="frmSearch">
                           <input type="text" name="fees" class="form-control" data-rule-required='true' data-rule-min="1" data-rule-number="true" placeholder="{{translation('enter')}} {{translation('fees')}}" value="{{$fees!=''?$fees:''}}" />
                          <div id="suggesstion-box" ></div>
                        </div>
                      <span class="help-block"></span>
                  </div>
                </div>



               <div class="filter-section">
                  <div class="row">
                     <div class="col-md-4">
                        <div class="form-group">                                                                                        
                           <input class="form-control" name="search_key" id="search_key" type="text" placeholder="{{translation('search')}}...">
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-md-12 ajax_messages">
                  <div class="alert alert-success" id="success" style="display:none;">
                  </div>
                  <div class="alert alert-danger" id="error" style="display:none;">
                  </div>
               </div>
               <table class="table table-advance" id="table_module">
                  <thead>
                     <tr>
                        <th></th>
                        <th class="sorting_disabled" width="10%">
                           <a class="sort-descs" >{{translation('name')}}</a><br>
                        </th>
                        <th class="sorting_disabled" width="10%">
                           <a class="sort-descs" >{{translation('level')}}</a><br>
                        </th>
                        <th class="sorting_disabled" width="35%">
                           <a class="sort-descs" >{{translation('pickup_address')}}</a><br>
                        </th>
                        <th class="sorting_disabled" width="35%">
                           <a class="sort-descs" >{{translation('drop_address')}}</a><br>
                        </th>
                        @if($school_latitude!='' && $school_longitude!='')
                          <th class="sorting_disabled" width="5%">
                           <a class="sort-descs" >{{translation('pickup_distance')}}</a><br>
                          </th>
                          <th class="sorting_disabled" width="5%">
                           <a class="sort-descs" >{{translation('drop_distance')}}</a><br>
                          </th>
                        @endif

                     </tr>
                  </thead>
                  <tbody>
                  @foreach($arr_students as $student)

                  @if($student['bus_transport']==1)
                  <?php
                    $first_name = isset($student['get_user_details']['first_name']) ? ucfirst($student['get_user_details']['first_name']) : "";
                    $last_name = isset($student['get_user_details']['last_name']) ? ucfirst($student['get_user_details']['last_name']) : "";
                    $name = $first_name.' '.$last_name;
                    $pickup_address = isset($student['pickup_address']) ? $student['pickup_address'] : "";
                    $drop_address = isset($student['drop_address']) ? $student['drop_address'] : "";
                    $level = isset($student['get_level_class']['level_details']['level_name']) ? $student['get_level_class']['level_details']['level_name'] : "";
                    $pickup_location = isset($student['pickup_location']) ? $student['pickup_location'] : "";
                    $pickup_latitude = $pickup_longitude = $drop_latitude = $drop_longitude="";
                    if($pickup_location!=''){
                      $pickup_location = json_decode($pickup_location,'true');
                      $pickup_latitude = isset($pickup_location['latitude']) ? $pickup_location['latitude'] :'';
                      $pickup_longitude = isset($pickup_location['longitude']) ? $pickup_location['longitude'] :'';
                    }
                    $drop_location = isset($student['drop_location']) ? $student['drop_location'] : "";
                    if($drop_location!=''){
                      $drop_location = json_decode($drop_location,'true');
                      $drop_latitude = isset($drop_location['latitude']) ? $drop_location['latitude'] :'';
                      $drop_longitude = isset($drop_location['longitude']) ? $drop_location['longitude'] :'';
                    }
                    
                    $pickup_distance=0;
                    $drop_distance=0;
                    if($school_latitude!='' && $school_longitude!='')
                    {
                      if($pickup_latitude!="" && $pickup_longitude!=""){
                        $theta = $pickup_longitude - $school_longitude;
                        $dist = sin(deg2rad($pickup_latitude)) * sin(deg2rad($school_latitude)) +  cos(deg2rad($pickup_latitude)) * cos(deg2rad($school_latitude)) * cos(deg2rad($theta));
                        $dist = acos($dist);
                        $dist = rad2deg($dist);
                        $miles = $dist * 60 * 1.1515;
                        $pickup_distance = $miles * 1.609344;
                      }

                      if($drop_latitude!="" && $drop_longitude!=""){
                        $theta = $drop_longitude - $school_longitude;
                        $dist = sin(deg2rad($drop_latitude)) * sin(deg2rad($school_latitude)) +  cos(deg2rad($drop_latitude)) * cos(deg2rad($school_latitude)) * cos(deg2rad($theta));
                        $dist = acos($dist);
                        $dist = rad2deg($dist);
                        $miles = $dist * 60 * 1.1515;
                        $drop_distance = $miles * 1.609344;
                      }
                       
                    }


                  ?>
                    <tr>
                      <td>
                        <div class="check-box">
                            <input type="checkbox" class="filled-in" name="checked_record[]" id="check_{{$student['id']}}" value="{{$student['user_id']}}_{{round($pickup_distance,5)}}_{{round($drop_distance,5)}}" @if(in_array($student['user_id'],$student_id)) checked @endif onclick="checkCount()"/>
                            <label for="check_{{$student['id']}}"></label>
                        </div>
                      </td>
                      <td>
                        {{$name}}    
                      </td>
                      <td>
                        {{$level}}
                      </td>
                      <td>
                        {{$pickup_address}}
                      </td>
                      <td>
                        {{$drop_address}}
                      </td>
                      @if($school_latitude!='' && $school_longitude!='')
                      <td>
                        {{round($pickup_distance,2)}} km
                      </td>
                      <td>
                        {{round($drop_distance,2)}} km
                      </td>
                      @endif
                    </tr>
                  @endif  
                  @endforeach 
                  </tbody>
               </table>
            </div>
            <div id="hide_row" class="alert alert-danger" style="text-align:center" hidden>{{translation('no_data_available')}}   
            </div>
            <div style="float:right">
                <div class="form-group back-btn-form-block" style="display:inline-block">
                  <div class="controls">
                     <a href="{{ $module_url_path }}/view_map" class="btn btn-primary" style="float: right;margin-top: 20px;" > {{translation('view_map')}} </a>
                  </div>
               </div>
               <div class="form-group back-btn-form-block" style="display:inline-block">
                  <div class="controls">
                     <button class="btn btn-primary" style="float: right;margin-top: 20px;" >{{translation('auto_route')}}</button>
                  </div>
               </div>
               <div class="form-group back-btn-form-block" style="display:inline-block">
                  <div class="controls">
                     <a href="{{ $module_url_path }}" class="btn btn-primary" style="float: right;margin-top: 20px;" > <i class="fa fa-arrow-left"></i> {{translation('back')}} </a>
                  </div>
               </div>
            </div>
               
            {!! Form::close() !!}
         </div>
      </div>
   </div>
</div>    <!-- END Main Content -->
<script>

  $("#search_key").keyup(function(){
    var flag=0;
        $("tbody tr").each(function(){
          
            var td = $(this).find("td");
            $(td).each(function(){
              var data = $(this).text().trim();
              data = data.toLowerCase();

              var search_key = $("#search_key").val();
              search_key = search_key.toLowerCase();
              search_key = new RegExp(search_key) ; 
                console.log(search_key.test(data));
                if(search_key.test(data)){
                  flag=1;
                  $(this).parent().show();
                  return false;
                }
                else{
                  $(this).parent().hide();
                }
             
            });
         })
         if(flag==0)
          {
            $("#hide_row").show();
          }
          else
          {
            $("#hide_row").hide();
          }  
      });
  $("#transport_type").on("change",function(){
    var transport_type = $('#transport_type').val();
 
       $.ajax({
          url  :"{{ $module_url_path }}/get_student",
          type :'get',
          data :{'_token':'<?php echo csrf_token();?>','transport_type':transport_type,'bus_id':"{{$obj_bus->id}}"},
          success:function(data){
                 console.log(data.table);
                 $("tbody").html(data.table);
                 if(data.table==''){
                    $(".display-table").hide();
                 }
                 else{
                    $(".display-table").show();
                 }
                 if(data.fees!=''){
                   $("input[name='fees']").val(data.fees);
                 }
                 else{
                   $("input[name='fees']").val('');
                 }
                 if(data.route_name!=''){
                   $("input[name='route_name']").val(data.route_name);
                 }
                 else{
                   $("input[name='route_name']").val('');
                 }
              
          }
    });
  });
  
  function checkCount(){

    var count = $("input[name='checked_record[]']:checked").length;

    var bus_capacity = "{{isset($obj_bus->bus_capacity)?$obj_bus->bus_capacity:0}}";
    if(count>bus_capacity){
      $("#error").show();
      $("#error").text("{{translation('bus_capacity_is_over')}}");
    }
    else{
      $("#error").hide();
     $("#error").text(""); 
    }
    
  }
</script>

@endsection