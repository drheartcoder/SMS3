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
            
        <li class="active">
          <i class="fa fa-eye"></i>
          <a href="{{$module_url_path}}/{{$role}}">{{$module_title}}</a>
        </li>

        <span class="divider">
          <i class="fa fa-angle-right"></i>
        </span>

        <li class="active">
          {{$page_title}}
        </li>

    </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="{{$module_icon}}"></i> {{$module_title}}</h1>

    </div>
</div>
<!-- END Page Title -->
   
  <div class="row">
<div class="col-md-12">
   <div class="box {{ $theme_color }}">
      <div class="box-title pr0">
         <h3>
            <i class="fa fa-list"></i>
            {{ isset($page_title)?$page_title:"" }}
         </h3>
         <div class="box-tool">
               <a class="icon-btns-block" 
                  title="{{translation('refresh')}}" 
                  href="{{ $module_url_path }}"
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
         </div><br/><br/>
         <div class="col-sm-12 col-md-12 col-lg-6">
            <div class="row">
                      <div class="form-group">
                         <label class="col-sm-3 col-lg-4"><b>{{translation($role)}} {{translation('name')}} </b>: </label>
                         <?php
                            $first_name = isset($arr_details['get_user_details']['first_name'])?$arr_details['get_user_details']['first_name']:'';
                            $last_name  = isset($arr_details['get_user_details']['last_name'])?$arr_details['get_user_details']['last_name']:'';

                         ?>
                         <div class="col-sm-6 col-lg-8 controls">
                            {{ucwords($first_name.' '.$last_name)}}
                         </div>
                         <div class="clearfix"></div>
                      </div>
                      <div class="form-group">
                         <label class="col-sm-3 col-lg-4"><b>{{translation('start_date')}} </b>: </label>
                         <?php
                         $start_date='';
                           if($start_date)
                            {
                              
                              $start_date = getDateFormat($start_date);
                            }
                            else
                            {

                              $start_date = getDateFormat(\Session::get('start_date'));
                            }
                         ?>
                         <div class="col-sm-6 col-lg-8 controls"">
                            {{$start_date}}
                         </div>
                         <div class="clearfix"></div>
                      </div>

                      <div class="form-group">
                         <label class="col-sm-3 col-lg-4"><b>{{translation('end_date')}} </b>: </label>
                         <?php
                            $end_date = '';
                            if($end_date)
                            {
                              $end_date = getDateFormat($end_date);
                            }
                            else
                            {
                              $end_date = date('d-m-Y');
                            }
                         ?>
                         <div class="col-sm-6 col-lg-8 controls"">
                            {{$end_date}}
                         </div>
                         <div class="clearfix"></div>
                      </div>
                    
            </div>
         </div>
         <br/>
         <div class="clearfix"></div>
         
         <div class="table-responsive  attendance-create-table-section" style="border:0" id="table_body" >
            <input type="hidden" name="multi_action" value="" />
            <table class="table table-advance"  id="table_module" align="center" width="70%">
              <thead>
                <tr>
                  <th>{{translation('sr_no')}}</th>
                  <th>{{translation('date')}}</th>
                  <th>{{translation('attendance')}}</th>
                </tr>
              </thead>
              <tbody>
                <?php $no = $status = 0;?>
                @if(isset($data_attendance) && count($data_attendance)>0)
                  @foreach ($data_attendance as $key => $attendance_data) 
                    @if(isset($attendance_data['attendance']) && !empty($attendance_data['attendance']))
                     <?php  $attendance = json_decode($attendance_data['attendance'],true);?>
                    @endif

                    @if(array_key_exists($arr_details['user_id'],$attendance))
                    <?php $no++;?>
                     <tr>
                      <td>{{$no}}</td>
                      <td>{{getDateFormat($attendance_data['date'])}}</td>
                      <td>
                      @if($attendance[$arr_details['user_id']] == 'present')
                        <div  style="width: 150px;text-align: center"' class="alert alert-success">
                      @endif
                      
                      @if($attendance[$arr_details['user_id']] == 'absent')
                        <div  style="width: 150px;text-align: center"' class="alert alert-danger">
                      @endif

                      @if($attendance[$arr_details['user_id']] == 'late')
                        <div  style="width: 150px;text-align: center"' class="alert alert-warning">
                      @endif
                      
                      {{ucfirst($attendance[$arr_details['user_id']])}}</div></td>
                      
                    @else
                      <?php $status++; ?>
                    @endif
                    </tr>
                    @endforeach                    
                @endif
                @if($status>0)
                  <tr><td colspan="3"><div class="alert alert-danger" style="text-align:center;">{{translation('no_data_available')}}</div></td></tr>
                @endif
              </tbody>
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
          todayHighlight: true
      });

      $('#datepicker2').datepicker({
        format:'yyyy-mm-dd',
        todayHighlight: true
      });

  });
  $("#datepicker").on('change',function(){
      var newdate = $("#datepicker").val();
      $('#datepicker2').datepicker('setStartDate',newdate);
  })
 function getData()
 {
    var date1 = $('#datepicker').val();
    var date2 = $('#datepicker2').val();
    $('#table_module').empty();
      $('#table_module1').empty();
    
    if(date1 != '' || date2 != '')
    {
      $('#button').attr('disabled', true);
      $('#loader').show();
      $('body').addClass('loader-active');

      $('#table_module').empty();
      $('#table_module1').empty();
      $.ajax({
              url  :"{{ $module_url_path }}/getStudentData",
              type :'POST',
              data :{'start_date':date1 ,'end_date':date2 ,'_token':'<?php echo csrf_token();?>'},
              success:function(data){

                $('#button').attr('disabled','none');
                $('#loader').hide();
                $('body').removeClass('loader-active')

                $('#table_module').empty();
                $('#table_module1').empty();  
                $('#table_body1').css('display','none');
                $('#table_body').show();
                $('#table_module').append(data);
              }
            });
      }
 }
  
</script>
 
@endsection