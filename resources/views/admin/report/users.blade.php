@extends('admin.layout.master')                
@section('main_content')

<link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">
<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
      <li>
          <i class="fa fa-home"></i>
          <a href="{{ url($admin_panel_slug.'/dashboard') }}">{{translation('dashboard')}}</a>
      </li>
      <span class="divider">
          <i class="fa fa-angle-right"></i>
          <i class="fa fa-users"></i>                
      </span> 
      <li class="active">{{ isset($module_title)?$module_title:"" }}</li>
  </ul>
</div>
<!-- END Breadcrumb -->


<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="fa fa-users"></i>{{$module_title}}</h1>
    </div>
</div>
<!-- END Page Title -->


<!-- BEGIN Main Content -->
<div class="row">
<div class="col-md-12">
   <div class="box {{ $theme_color }}">
      <div class="box-title pr0">
         <h3>
            <i class="fa fa-list"></i>
            {{ isset($page_title)?$page_title:"" }}
         </h3>
         <div class="box-tool">
                <a title="{{translation('export_data')}}" 
                   href="javascript:void(0);" 
                   onclick="exportData();" 
                   style="text-decoration:none;">
                  {{translation('export')}}
                </a> 
               
         </div>
      </div>
      <div class="box-content">
         @include('admin.layout._operation_status')  
         {!! Form::open([ 'url' => $module_url_path.'/multi_action',
         'method'=>'POST',
         'enctype' =>'multipart/form-data',   
         'class'=>'form-horizontal', 
         'id'=>'frm_manage' 
         ]) !!}
         {{ csrf_field() }}
         <div class="col-md-10">
            <div class="alert alert-danger" id="no_select" style="display:none;"></div>
            <div class="alert alert-warning" id="warning_msg" style="display:none;"></div>
         </div>
         <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-6">
              <div class="form-group">
                  <div class="col-sm-2 col-lg-4 ">
                    <label class="pull-right control-label">{{translation('active')}}:</label>
                  </div>
                  <div class="col-sm-4 col-lg-8 controls ">
                    <select id="user_status" class="form-control ">
                      <option value="">{{translation('select')}}</option>
                      <option value="1">{{translation('yes')}} </option>
                      <option value="0">{{translation('no')}}</option>
                    </select>

                  </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 col-lg-4 control-label" style="margin-top:2px;">{{translation('start_date')}} </label>
                <div class="col-sm-3 col-lg-4 controls">
                  <input type="text" name="start_date"  id="start_date" class="form-control datepikr" readonly style="cursor: pointer;">

                 </div>

                 <div class="col-sm-3 col-lg-4 controls">
                  <input type="text" name="end_date"  id="end_date" class="form-control datepikr" readonly style="cursor: pointer;">
                  <span id="err_end_date" style="color: red;" class="help-block"></span>
                 </div>
                 
              </div>
            </div>

            <div class="col-sm-12 col-md-12 col-lg-6">
              <div class="form-group">
                  <div class="col-sm-2 col-lg-4">
                    <label class="pull-right control-label">{{translation('country')}}:</label>
                  </div>
                  <div class="col-sm-4 col-lg-8 controls ">
                    <div class="frmSearch relative-block">
                      <input type="text" name="country" id="country" class="form-control" data-rule-required='true' placeholder="{{translation('select')}} {{translation('country')}}" value="{{old('country')}}" autocomplete="off"/>
                      <div class="suggestion-box autoselect-drop" id="suggesstion-box-country" style="height: 200px;display: none"></div>
                    </div>

                  </div>
              </div>

              <div class="form-group">
                  <div class="col-sm-2 col-lg-4">
                    <label class="pull-right control-label">{{translation('city')}}:</label>
                  </div>
                  <div class="col-sm-4 col-lg-8 controls ">
                    <div class="frmSearch relative-block">
                      <input type="text" name="city" id="locality" class="form-control" data-rule-required='true' placeholder="{{translation('select')}} {{translation('city')}}" value="{{old('city')}}" autocomplete="off"/>
                      <div class="suggestion-box autoselect-drop" id="suggesstion-box" style="height: 200px;display: none"></div>
                    </div>

                  </div>
              </div>
              
            </div>

         </div>

            <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12" style="text-align: center">
              <div class="form-group">
               <div class="row">
                <div class="col-sm-2 col-lg-2 col-lg-offset-2" style="align-content: center" >
                  <input type="button" name="show" id="show" value="{{translation('search')}}" class="form-control btn btn-primary" onClick="getRecords();"> 
                </div>
                  </div>
              </div>
            </div>
          </div>
         <br/>
         <div class="clearfix"></div>
         <div class="table-responsive attendance-create-table-section" style="border:0" id="table_div">
            <input type="hidden" name="multi_action" value="" />
            <table class="table table-advance"  id="table4" >
              <thead>
                <tr>
                  
                  <th>{{translation('sr_no')}}</th>
                  <th>{{translation('email')}}</th> 
                  <th>{{translation('name')}}</th> 
                  <th>{{translation('phone')}}</th> 
                  <th>{{translation('date_of_birth')}}</th> 
                  <th>{{translation('gender')}}</th> 
                  <th>{{translation('address')}}</th> 
                  <th>{{translation('date')}}</th> 
                </tr>
              </thead>
              <tbody id="tbody">
                @if(sizeof($obj_arr_data)>0)
                  @foreach($obj_arr_data as $key=> $report)
                  <tr>
                    <td> {{($key+1)}}</td>
                    <td> {{ isset($report->email)?$report->email:'-' }} </td>  
                    <td> {{ isset($report->user_name) ? ucwords($report->user_name) :'-'}} </td>  
                    <td> {{ isset($report->mobile_no)? $report->mobile_no:'-' }} </td>  
                    <td> {{ isset($report->birth_date)? $report->birth_date:'-' }} </td>  
                    <td> {{ isset($report->gender)? $report->gender:'-' }} </td>  
                    <td> {{ isset($report->address)? $report->address:'-' }} </td>  
                    <td> {{ isset($report->created_at)? getDateFormat($report->created_at):'-' }} </td>  
                  </tr>
                  @endforeach
                @endif
                 
              </tbody>
            </table>
         </div>

          <div class="table-responsive" style="border:0" hidden="true" id="table_div2">
            <table class="table table-advance" id="table_module2">
            </table>
          </div>
         {!! Form::close() !!}
      </div>
    </div>
   </div>


<script>
  var city_ajax_url = "{{url('/admin')}}/get_cities?keyword=";
  var country_ajax_url = "{{url('/admin')}}/get_countries?keyword=";

  $('#start_date').datepicker({ 
            todayHighlight: true,
            autoclose: true,
            format:'yyyy-mm-dd'
  
  });

  $('#end_date').datepicker({ 
            todayHighlight: true,
            autoclose: true,
            format:'yyyy-mm-dd'
  });

</script>


 <script>

  function getRecords()
  {
        var startDate = new Date($('#start_date').val());

        var endDate = new Date($('#end_date').val());

      if(startDate != '' && endDate != '')      
      {
        if(startDate >= endDate)
        {
          $('#err_end_date').text('{{translation('end_date_must_be_greater_than_start_date')}}')  ;
        }
        else
        {
          $('#err_end_date').text(''); 
          var date        = $('#start_date').val();
          var date2       = $('#end_date').val();
          var user_status = $("#user_status").val();
          var city        = $("#locality").val();
          var country     = $("#country").val();
          
          
              $.ajax({
                    url  :"{{ $module_url_path }}/get_record/{{$type}}",
                    type :'POST',
                    data :{'start_date':date ,'end_date':date2 ,'status':user_status ,'city':city ,'country':country ,'_token':'<?php echo csrf_token();?>'},
                    success:function(data){
                        $('#tbody').empty();
                        $('#tbody').append(data);
                    }
              });
        }
      }

  }

  function exportData()
  {
    /*$userType = $('#user_role').val();
    $search_str = $('#search_str').val();*/
  //  alert($userType+'=='+$search_str);
  //window.location.href = "{{$module_url_path}}/exportNewUser?search_str="+$search_str+"&type="+$userType;


    var para = getParameter('{{$type}}');
    window.location.href = "{{$module_url_path}}/exportUsers"+para;
  }

  function getParameter(type){

    $userType = type;
    $userStatus = $('#user_status').val();
    $city = $('#locality').val();
    $country = $('#country').val();

    var start_date    = $("#start_date").val();
    var end_date      = $("#end_date").val();
    var parameters    = '';

    parameters = "?type="+$userType+"&status="+$userStatus+"&city="+$city+"&country="+$country+"&start_date="+start_date+"&end_date="+end_date;
    return parameters;
  }

  function getCity()
  {
    var country = $('#country').val();
    $.ajax({
                    url  :"{{ $module_url_path }}/get_city",
                    type :'POST',
                    data :{'country':country ,'_token':'<?php echo csrf_token();?>'},
                    success:function(data){
                        $('#city').empty();
                        $('#city').append(data);
                        $('#city').removeAttr('disabled');
                    }
              });
  }

  function hideBox(val) {
      $("#locality").val(val);
      $("#suggesstion-box").hide();
  }

   function selectCity(val) {
      $("#country").val(val);
      $("#suggesstion-box-country").hide();
   }
 </script> 
<script src="{{url('/')}}/js/city_country.js"></script>
<!-- END Main Content -->

@stop