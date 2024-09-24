@extends('professor.layout.master')                
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="{{url('/')}}/professor/dashboard">{{translation('dashboard')}}</a>
        </li>
        <span class="divider">
            <i class="fa fa-angle-right"></i>
        </span>
        <li>
          <i class="{{$module_icon}}"></i>
          <a href="{{$module_url_path.'/'.$role}}">{{$module_title}}</a>
        </li>
        <span class="divider">
            <i class="fa fa-angle-right"></i>
        </span>
        <i class="fa fa-eye"></i>
        <li class="active">{{$view_page_title}}</li>
    </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
    <div>
        <h1>{{$module_title}}</h1>

    </div>
</div>
<!-- END Page Title -->

<!-- BEGIN Main Content -->
<div class="row">
<div class="col-md-12">
   <div class="box {{ $theme_color }}">
      <div class="box-title">
         <h3>
            
         </h3>
      </div>
      <div class="box-content">
         @include('professor.layout._operation_status')  

             <div class="row">
              <div class="col-sm-9 col-md-8 col-lg-9">
                @if($role == 'student')
                   <a href="{{$module_url_path.'/create/'.$role}}" class="btn btn"><i class="fa fa-plus-circle"></i> {{ $page_title}}</a>
              
                    <a href="{{$module_url_path.'/view/'.$role}}" class="btn btn active" id="view"><i class="fa fa-eye"></i> {{ $view_page_title}}</a>
                @endif
                </div>
            </div>
            <br/>
            <form class="form-horizontal" id="validation-form1">
                {{ csrf_field() }}
                <div class="col-md-12 ajax_messages">
                  <div class="alert alert-danger" id="error" style="display:none;"></div>
                  <div class="alert alert-success" id="success" style="display:none;"></div>
                </div>
                
                <div class="row">
                  <div class="row">
                              <div class="col-md-6">
                                   <div class="form-group">
                                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('select_level')}}</label>
                                        <div class="col-sm-9 col-md-8 col-lg-9 controls">
                                           <select name="level" id="level" class="form-control" data-rule-required="true" onChange="getClasses();">
                                              <option value="">{{translation('select_level')}}</option>
                                              @if(isset($levels) && !empty($levels))
                                                @foreach($levels as $key => $level)
                                                  <option value="{{$level['level_id']}}">{{$level['level_details']['level_name']}}</option>
                                                @endforeach
                                              @endif
                                          </select>
                                        </div>
                                    </div>
                              </div> 
                              <div class="col-md-6">
                                   <div class="form-group">
                                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('select_class')}}</label>
                                        <div class="col-sm-9 col-md-8 col-lg-9 controls">
                                           <select name="class" id="class" class="form-control" data-rule-required="true">
                                              <option value="">{{translation('select_class')}}</option>
                                          </select>
                                        </div>
                                    </div>
                              </div>
                              <div class="col-md-6">
                                  <div class="form-group">
                                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('select_date')}}</label>
                                        <div class="col-sm-9 col-md-8 col-lg-9 controls">
                                           <input class="form-control datepikr" name="date" id="datepicker" placeholder="{{translation('select_date')}}" type="text" readonly style="cursor: pointer;">
                                        </div>
                                    </div>
                              </div> 
                             <div class="col-sm-12 col-md-12 col-lg-12">
                                <div class="form-group">
                                  <div class="col-sm-3 col-md-4 col-lg-3 col-lg-offset-2">
                                      <a href="javascript:void(0)" class="btn btn btn-primary" onClick="getTimetable();">{{translation('show')}}</a>
                                  </div>
                                </div>
                              </div>
                  </div>
                  <br/><br/>

                  <div id="timetable_div" hidden="true"  class="timetable-data-new timetable-section-main professor-timetable-section">
                  </div>
                <br/>
                <br/>
                <div id="table_div2" hidden="true">
                 <div class="table-responsive  attendance-create-table-section" style="border:0">
                    <h4>{{translation('attendance')}}</h4>
                    <input type="hidden" name="role" value="{{$role}}" />
                    <table class="table table-advance"  id="table_module2">
                       
                    </table>
                 </div><br/>
                </div>
               </div>
              </form>
      </div>
    </div>
   </div>
</div>
<script>
    $(function () {
      var date  = new Date();
      var today = date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate();
      $("#datepicker").datepicker({
          todayHighlight: true,
          autoclose: true,
          format:'yyyy-mm-dd',
          startDate: "{{\Session::get('start_date')}}",
          endDate: today
      });

      $("#datepicker").val(today);
    });
</script>

<script>
  function getData(period_no)
  {
    var level   =   $('#level').val();
    var cls     =   $('#class').val();
    var period  =   period_no;
    var date    =   $('#datepicker').val();
      
      $('#table_module2').empty();
      $.ajax({
              url  :"{{ $module_url_path }}/getData",
              type :'POST',
              data :{'level':level ,'cls':cls ,'period':period ,'date':date ,'_token':'<?php echo csrf_token();?>'},
              success:function(data){
                  $('#table_module2').append(data);
                
              }
            });
 
  }

  function getClasses()
  {
      var level   =   $('#level').val();
      if(level != '')
      {
      $('#class').empty();
       $.ajax({
              url  :"{{ $module_url_path }}/getClasses",
              type :'POST',
              data :{'level':level ,'_token':'<?php echo csrf_token();?>'},
              success:function(data){
                $('#class').append(data);
                $('#timetable_div').empty();
                $('#timetable_div').append(data.data);
              }
            });
      }
  }


 function getTimetable()
 {
   var level   =   $('#level').val();
   var cls     =   $('#class').val();
   var date    =   $('#datepicker').val();
   if(level != '' && cls != '' && date !='')
   {
     $.ajax({
                url  :"{{ $module_url_path }}/getTimetable",
                type :'POST',
                data :{'level':level ,'class':cls ,'date':date
                 ,'_token':'<?php echo csrf_token();?>'},
                success:function(data){
                   $('#timetable_div').empty();
                   $('#timetable_div').show();
                   $('#table_module2').empty();
                   $('#table_div2').show();
                   $('#timetable_div').append(data.data);
                   $('#table_module2').append(data.attendance);
                }
              });
    }
    else
    {
      $('.ajax_messages').show();
          $('#error').css('display','block');
          $('#error').text('{{translation('select_level_class_date_first')}}');
          setTimeout(function(){
              $('.ajax_messages').hide();
          }, 4000);
    }
 }

 function createAttendance(period_no,subject_id,day)
 {
    $('td').css('background-color','');
    $('td').css('cursor','pointer');
    $('#td_'+day+'_'+period_no).css('background-color','#c8e9f6');
    $('#subject_id').val(subject_id);
    $('#period_no').val(period_no);
    getData(period_no);
 }
 

  /*$("#datepicker").on('changeDate',function(){
      $('#table_module2').empty();
      $('#table_body').empty();
      getTimetable();
  });*/
</script>
<!-- END Main Content --> 
@endsection