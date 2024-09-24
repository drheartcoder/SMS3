
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
        <i class="{{$create_icon}}"></i>
        <li class="active">{{$page_title}}</li>
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
                   <a href="{{$module_url_path.'/create/'.$role }}" class="btn btn active"><i class="fa fa-plus-circle"></i> {{ $page_title}}</a>
                
                    <a href="{{$module_url_path.'/view/'.$role}}" class="btn btn" id="view"><i class="fa fa-eye"></i> {{ $view_page_title}}</a>
                </div>
            </div>
            <br/>
            <form action="{{$module_url_path}}/store" method="POST" enctype="multipart/form-data" class="form-horizontal" id="validation-form1">
                {{ csrf_field() }}

                <div class="col-md-12 ajax_messages">
                  <div class="alert alert-danger" id="error" style="display:none;"></div>
                  <div class="alert alert-success" id="success" style="display:none;"></div>
                </div>
                
                <input type="hidden" name="subject_id" value="" id="subject_id">
                <input type="hidden" name="period_no" value="" id="period_no">
                <div class="row">
                  <div class="row">
                              <div class="col-md-6">
                                   <div class="form-group">
                                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('select_level')}}  <label style="color: red;">*</label></label>
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
                                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('select_class')}}  <label style="color: red;">*</label></label>
                                        <div class="col-sm-9 col-md-8 col-lg-9 controls">
                                           <select name="class" id="class" class="form-control" data-rule-required="true">
                                              <option value="">{{translation('select_class')}}</option>
                                          </select>
                                        </div>
                                    </div>
                              </div>
                              <div class="col-md-6">
                                  <div class="form-group">
                                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('select_date')}} <label style="color: red;">*</label></label>
                                        <div class="col-sm-9 col-md-8 col-lg-9 controls">
                                          <input type="text" name="date" style="cursor: pointer" id="datepicker" class="form-control datepikr" data-rule-required='true'  placeholder="{{translation('select')}} {{translation('date')}}" data-rule-date="true" readonly>
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
                  </div>

                  <div id="timetable_div" class="timetable-data-new timetable-section-main professor-timetable-section" hidden="true">

                  </div>
                  <br/><br/>
                <div id="table_div" hidden="true">
                   <h4>{{translation('create')}} {{translation('attendance')}}</h4>
                   <br/>
                 <div class="table-responsive" style="border:0">
                    <input type="hidden" name="role" value="{{$role}}" />
                    <table class="table table-advance"  id="table_module">
                       <thead>
                          <tr>
                            {{-- <th>{{translation('sr_no')}}.</th> --}}
                             <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation($role)}} {{translation('name')}} </a><br/></th>
                             <th>{{translation('national_id')}}</th>
                             <th></th>
                             <th></th>
                             <th></th>
                          </tr>
                       </thead>
                       <tbody id="table_body">
                        
                       </tbody>
                    </table>
                 </div><br/>
                 <div class="form-group" id="btn_group">
                    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                       <a href="{{ url($module_url_path.'/'.$role) }}" class="btn btn-primary">{{translation('back')}}</a> 
                       <input type="submit" id="submit_button" name="save" value="{{translation('save')}}" class="btn btn-primary">
                    </div>
                  </div>
                </div>

                <div id="table_div2" hidden="true">
                  <h4>{{translation('update')}} {{translation('attendance')}}</h4>
                  <br/>
                 <div class="table-responsive attendance-create-table-section" style="border:0">
                    <input type="hidden" name="role" value="{{$role}}" />
                    <table class="table table-advance" id="table_module2">
                       <thead>
                          <tr>
                            <th>{{translation('sr_no')}}.</th>
                             <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation($role)}} {{translation('name')}} </a><br/></th>
                             <th>{{translation('national_id')}}</th>
                             <th></th>
                             <th></th>
                             <th></th>
                          </tr>
                       </thead>
                       <tbody id="table_body2">
                        
                       </tbody>
                    </table>
                 </div><br/>
                 <div class="form-group">
                    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                       <a href="{{ url($module_url_path.'/'.$role) }}" class="btn btn-primary">{{translation('back')}}</a> 
                       <input type="submit" id="submit_button" name="update" value="{{translation('update')}}" class="btn btn-primary">
                    </div>
                  </div>
                </div>
               </div>
              </form>
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
    var date    =   $('#datepicker').val();
    var period  =   period_no;

      $.ajax({
              url  :"{{ $module_url_path }}/getStudents",
              type :'POST',
              data :{'level':level ,'cls':cls ,'period':period ,'date':date ,'_token':'<?php echo csrf_token();?>'},
              success:function(data){
                
                if(data.flag == 'true')
                {
                  $('#table_body2').empty();
                  $('#table_div2').show();
                  $('#table_div').hide();
                  $('#table_body2').append(data.data);
                  $('#validation-form1').attr('action','{{$module_url_path}}/update/'+data.enc_id);
                }
                else
                {
                  $('#table_body').empty();
                  $('#table_div').show();
                  $('#table_div2').hide();
                  $('#table_body').append(data.data);
                }
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
                    $('#timetable_div').append(data.data);
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
  
    $('td').css('cursor','pointer');
    $('#subject_id').val(subject_id);
    $('#period_no').val(period_no);
    getData(period_no);
 }
 

</script>
<!-- END Main Content --> 
@endsection