@extends('schooladmin.layout.master')                
@section('main_content')

<link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">
<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
      <li>
          <i class="fa fa-home"></i>
          <a href="{{ url($school_admin_panel_slug.'/dashboard') }}">{{translation('dashboard')}}</a>
      </li>
      <span class="divider">
          <i class="fa fa-angle-right"></i>
          <i class="fa {{$module_icon}}"></i>                
      </span> 
      <li class="active">{{ isset($module_title)?$module_title:"" }}</li>
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
          @if($role == 'student')
            <a href="{{$module_url_path}}/view/{{$role}}" >{{translation('view')}} {{translation('attendance')}}</a>
          @else
            @if(array_key_exists('attendance.create', $arr_current_user_access) )
              <a href="{{$module_url_path}}/create/{{$role}}" >{{translation('create')}} {{translation('attendance')}}</a>
            @endif
            <a href="{{$module_url_path.'/view_staff/'.$role}}" >{{translation('view')}} {{translation('attendance')}}</a>
            
          @endif

          <div class="dropup-down-uls">
                <a href="javascript:void(0)" class="export-lists"><i class="fa fa-upload"></i> {{ translation('export')}} </a>
                <div class="export-content-links">
                    <div class="li-list-a">
                        <a href="javascript:void(0)" onClick="exportForm('pdf');">PDF</a>
                    </div>
                    <div class="li-list-a">
                        <a href="javascript:void(0)" onClick="exportForm('csv');">CSV</a>
                    </div>
                     
                </div>
            </div>

           <a class="icon-btns-block" 
              title="{{translation('refresh')}}" 
              href="{{ $module_url_path.'/professor' }}"
              style="text-decoration:none;">
           <i class="fa fa-repeat"></i>
           </a> 
         </div>
      </div>
      <div class="box-content">
         @include('schooladmin.layout._operation_status')  
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
               <div class="row">
                <label class="col-sm-3 col-lg-4 control-label">{{translation('start_date')}}</label>
                <div class="col-sm-6 col-lg-8">
                  <input type="text" name="start_date"  id="datepicker" class="form-control datepikr" data-rule-required='true' value="" data-rule-date="true" readonly style="cursor: pointer;" placeholder="{{translation('select_start_date')}}"> 
                  <span class="help-block">{{ $errors->first('start_date') }}</span>
                </div>
                  </div>
              </div>
            </div>

            <div class="col-sm-12 col-md-12 col-lg-6">
              <div class="form-group">
                <div class="row">
                <label class="col-sm-3 col-lg-4 control-label">{{translation('end_date')}}</label>
                <div class="col-sm-6 col-lg-8">
                  <input type="text" name="end_date"  id="datepicker2" class="form-control datepikr" data-rule-required='true' value="" data-rule-date="true" readonly style="cursor: pointer;"  placeholder="{{translation('select_end_date')}}">
                  <span class="help-block">{{ $errors->first('end_date') }}</span>
                  <span id="err_end_date" style="color: red"></span>
                </div>
                  </div>
              </div>
            </div>

            <div class="col-sm-12 col-md-12 col-lg-6">
              <div class="form-group">
               <div class="row">
                <label class="col-sm-3 col-lg-4 control-label">{{translation('name')}}</label>
                <div class="col-sm-6 col-lg-8">
                 <select name="name" id="name" class="form-control">
                   <option value="">{{translation('select_name')}}</option>
                    @if(isset($arr_details) && !empty($arr_details))
                      @foreach($arr_details as $key => $details)
                        @if(isset($details['get_user_details']['first_name']))
                        <option value="{{$details['user_id']}}">{{isset($details['get_user_details']['first_name'])?ucfirst($details['get_user_details']['first_name']):''}} {{isset($details['get_user_details']['last_name'])?ucfirst($details['get_user_details']['last_name']):''}}</option>
                        @endif
                      @endforeach
                    @endif
                 </select>
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
                  <div id="button">
                    <input type="button" name="show" id="show" value="{{translation('show')}}" class="form-control btn btn-primary" onClick="getRecords();"> 
                  </div>
                </div>
                  </div>
              </div>
            </div>
          </div>
         <br/>
         <div class="clearfix"></div>
         <div class="table-responsive attendance-create-table-section" style="border:0" id="table_div">
            <input type="hidden" name="multi_action" value="" />
            <input type="hidden" name="file_format" id="file_format" value="" />
            <input type="hidden" name="role" id="role" value="{{$role}}" />
            <table class="table table-advance"  id="table_module">
               <thead>
                  <tr>
                    <th>{{translation('sr_no')}} </th>
                     <th>{{translation('name')}} </th>
                     <th>{{translation('present_days')}} </th>
                     <th>{{translation('absent_days')}} </th>
                     <th>{{translation('late_days')}} </th>
                     <th>{{translation('total_days')}} </th>
                     <th>{{translation('action')}}</th>
                  </tr>
               </thead>
               <tbody id="tbody">
                <?php  $total = $key = $val = 0 ; ?>
                @if(isset($arr_details) && !empty($arr_details))

                  @foreach($arr_details as $key_val => $details)
                  <?php $no = $no2 = $no3 = $calculate = $calculate2 = $calculate3= 0; ?>
                    @if(isset($details['get_user_details']['first_name']) && !empty($details['get_user_details']['first_name']))
                        <tr>
                          <td>{{(++$val)}}</td>
                          <td>{{ucfirst($details['get_user_details']['first_name'])}} {{ucfirst($details['get_user_details']['last_name'])}}</td>
                         
                            @if(isset($data_attendance) && !empty($data_attendance))
                              

                              {{-- @for($j=$key;$j<$total;$j++) --}}
                              @foreach($data_attendance as $key => $attendance)
                                <?php
                                  if(isset($attendance['attendance']) && !empty($attendance['attendance']))
                                  {
                                    $attendance = json_decode($attendance['attendance'],true);
                                  }
                                ?>
                                @if(array_key_exists($details['user_id'], $attendance))
                                  
                                
                                  @if($attendance[$details['user_id']] == 'present')
                                    <?php $no +=1;?>

                                  @elseif($attendance[$details['user_id']] == 'absent')
                                    <?php $no2 +=1;?>

                                  @elseif($attendance[$details['user_id']] == 'late')
                                    <?php $no3 +=1;?>
                                  @endif
                                @endif

                                <?php  $key++; ?>

                              @endforeach
                            @endif
                            <?php $total=$no+$no2+$no3; ?>
                            @if($total != 0)
                               <?php 

                                    $calculate  = round(($no/$total)*100);
                                     $calculate2 = round(($no2/$total)*100);
                                     $calculate3 = round(($no3/$total)*100);
                               ?>
                            @endif
                              <td>
                                {{$no}} ({{$calculate}}%)
                              </td>
                               <td>
                                {{$no2}} ({{$calculate2}}%)
                              </td>
                               <td>
                                {{$no3}} ({{$calculate3}}%)
                              </td>
                               <td>
                                {{$no+$no2+$no3}}
                              </td>
                              <td>
                              
                               <a class="green-color" href="{{$module_url_path.'/view_details/'.$role.'/'.base64_encode($details['get_user_details']['id'])}}" title="{{translation('view')}}"><i class="fa fa-eye" ></i></a>
                              </td>
                            
                            
                        </tr>
                    @endif
                   
                  @endforeach

                @else
                  <tr>
                    <td colspan="5"><div class="alert alert-warning">{{translation('no_data_available')}}</div></td>
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
</div>

<script>
    $(function () {
      var date  = new Date();
      var today = date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate();
      $("#datepicker").datepicker({
          format:'yyyy-mm-dd',
          autoclose:true,
          todayHighlight: true,
          startDate: "{{\Session::get('start_date')}}",
          endDate: "{{\Session::get('end_date')}}",
      });

      $("#datepicker2").datepicker({
          format:'yyyy-mm-dd',
          autoclose:true,
          todayHighlight: true
      });
      
      $("#datepicker").on('change',function(){
          var newdate = $("#datepicker").val();
          $('#datepicker2').datepicker('setStartDate',newdate);
      })
    });
</script>

@if(array_key_exists('attendance.list', $arr_current_user_access))  
 <script type="text/javascript">

      function getRecords()
      {
        var startDate = new Date($('#datepicker').val());

        var endDate = new Date($('#datepicker2').val());

        if(startDate > endDate)
        {
          $('#err_end_date').text('End date must be greater than start date');
        }
        else
        {
          $('#button').attr('disabled', true);
          $('#loader').fadeIn('slow');
          $('body').addClass('loader-active');

          $('#err_end_date').text(''); 
          var date  = $('#datepicker').val();
          var date2 = $('#datepicker2').val();
          var name  = $('#name').val();
          $('#tbody').empty();
          
          if(name !='')
          {
            $('#table_module2').empty();
              $.ajax({
                    url  :"{{ $module_url_path }}/getEmployeeRecord/{{$role}}",
                    type :'POST',
                    data :{'start_date':date ,'end_date':date2 ,'name':name ,'_token':'<?php echo csrf_token();?>'},
                    success:function(data){

                        $('#table_div2').show();
                        $('#table_div').css('display','none');
                        $('#table_module2').append(data);

                        $('#loader').hide();
                        $('body').removeClass('loader-active');
                        $('#button').attr('disabled', false);
                    }
              });
          }
          else
          {
              $.ajax({
                    url  :"{{ $module_url_path }}/getRecords/{{$role}}",
                    type :'POST',
                    data :{'start_date':date ,'end_date':date2 ,'_token':'<?php echo csrf_token();?>'},
                    success:function(data){
                        $('#loader').hide();
                        $('body').removeClass('loader-active');
                        $('#button').attr('disabled', false);

                        $('#table_div2').css('display','none');
                        $('#table_div').show();
                        $('#tbody').append(data);
                    }
              });
          }
        }
      }
 </script> 

 <script>
  function exportForm(file_format)
  {
    document.getElementById('file_format').value = file_format;
    var serialize_form   = $("#frm_manage").serialize();
    window.location.href = '{{ $module_url_path }}/export?'+serialize_form+'&export=true&role={{$role}}';
  }
  /*$(document).on("change","[type='search']",function(){
      var search_hidden = $(this).val();
      alert()
      document.getElementById('search').value = search_hidden;
   });*/
</script>
@endif
<!-- END Main Content -->

@stop