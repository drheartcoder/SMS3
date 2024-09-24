@extends('professor.layout.master')                
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="fa fa-home"></i>
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
         @include('professor.layout._operation_status')  
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
                      <input type="hidden" name="id" value="{{$id}}" id="id">
                      <input type="hidden" name="level_class" value="{{isset($arr_details['level_class_id'])?$arr_details['level_class_id']:0}}" id="level_class">
                      <div class="form-group">
                         <label class="col-sm-3 col-lg-4"><b>{{translation('start_date')}} </b>: </label>
                         <?php
                         $st_date='';

                           if($start_date)
                            {
                              
                              $st_date = $start_date;
                            }
                            else
                            {

                              $st_date = \Session::get('start_date');
                            }
                         ?>
                         <input type="hidden" name="start_date" value="{{$st_date}}" id="start_date">
                         <div class="col-sm-6 col-lg-8 controls">
                            {{getDateFormat($st_date)}}
                         </div>
                         <div class="clearfix"></div>
                      </div>

                      <div class="form-group">
                         <label class="col-sm-3 col-lg-4"><b>{{translation('end_date')}} </b>: </label>
                         <?php
                            $end_date = '';
                            if($end_date)
                            {
                              $end_date = $end_date;
                            }
                            else
                            {
                              $end_date = date('Y-m-d');
                            }
                         ?>
                         <input type="hidden" name="end_date" value="{{$end_date}}" id="end_date">
                         <div class="col-sm-6 col-lg-8 controls"">
                            {{getDateFormat($end_date)}}
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
                  @if(isset($period_time) && count($period_time)>0)
                    @foreach($period_time as $key => $time)
                      @if($time['is_break'] == 0)
                        <th>{{translation('period')}} {{($key+1)}}<br/> ( {{getTimeFormat($time['period_start_time'])}} - {{getTimeFormat($time['period_end_time'])}} )</th>
                      @endif
                    @endforeach
                  @endif
                </tr>
              </thead>
              <tbody id="tbody">

              </tbody>
            </table>
         </div>

       {{--  <div class="col-md-12">                    
            <div class="form-group back-btn-form-block">
               <div class="controls view-btns-edts">
                  <a href="{{ $module_url_path }}/student" class="btn btn-primary" style="float: right;" > <i class="fa fa-arrow-left"></i>{{translation('back')}}</a>
               </div>
            </div>
        </div> --}}
         {!! Form::close() !!}
      </div>
    </div>
   </div>
</div>

<script>
  $(function () {
      $.ajax({
              url  :"{{ $module_url_path }}/build_table",
              type :'POST',
              data :{'start_date':$('#start_date').val() ,'end_date':$('#end_date').val() ,'id':$('#id').val() ,'level_class':$('#level_class').val(),'_token':'<?php echo csrf_token();?>'},
              success:function(data){
                $('#tbody').empty();
                $('#tbody').append(data);
              }
            });

      var date  = new Date();
      var today = date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate();
      $("#datepicker").datepicker({
          format:'yyyy-mm-dd'
      });

      $('#datepicker2').datepicker({
        format:'yyyy-mm-dd'
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