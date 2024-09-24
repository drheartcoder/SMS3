@extends('schooladmin.layout.master') 
@section('main_content')
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
        <li class="active">{{ isset($module_title)?$module_title:"" }}</li>
    </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
 <div class="page-title new-agetitle">
    <div>
        <h1><i class="fa {{$module_icon}}"></i>{{$module_title}}</h1>
    </div>
</div>


<link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">

<!-- BEGIN Tiles -->
<div class="row">
    <div class="col-md-12">
        <div class="box  box-navy_blue">
            <div class="box-title">
                <h3><i class="fa fa-list"></i>{{ isset($module_title)?$module_title:"" }}</h3><br/><br/>
            </div>
            <div class="clearfix"></div>

            <div class="box-content studt-padding">
                @include('schooladmin.layout._operation_status') 
                {!! Form::open([ 'url' => $module_url_path.'/multi_action', 'method'=>'POST', 'enctype' =>'multipart/form-data', 'class'=>'form-horizontal', 'id'=>'frm_manage' ]) !!} 
                {{ csrf_field() }}             
                <div class="col-md-12 ajax_messages">
                  <div class="alert alert-danger" id="error" style="display:none;"></div>
                  <div class="alert alert-success" id="success" style="display:none;"></div>
               </div> 
              
                <div class="row">
                    <div class="col-md-6">
                     <div class="form-group">
                          <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('frequency')}}</label>
                          <div class="col-sm-9 col-md-8 col-lg-9 controls">
                              <select name="period" id="period" class="form-control">
                                <option value="">{{translation('select_period')}}</option>
                                <option value="WEEKLY" @if(isset($period) && $period == 'WEEKLY') selected @endif>{{translation('weekly')}}</option>
                                <option value="MONTHLY" @if(isset($period) && $period == 'MONTHLY') selected @endif>{{translation('monthly')}}</option>
                                <option value="ANNUALLY" @if(isset($period) && $period == 'ANNUALLY') selected @endif>{{translation('annually')}}</option>
                            </select>
                            <span style="font-size:10px;font-weight:600"><b>{{translation('note')}}: </b>{{translation('if_any_professor_reports_student_behavior_then_you_will_not_able_to_update_the_period')}}</span>
                          </div>

                       </div>
                       
                    </div>

                     <div class="col-md-6">
                     <div class="form-group">
                          <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('current')}} {{translation('frequency')}}</label>
                          <div class="col-sm-9 col-md-8 col-lg-9 controls">
                              <input type="text" name="frequency" id="frequency" value="{{isset($period)?$period:''}}" readonly class="form-control">
                          </div>

                       </div>
                       
                    </div>
                       <div class="clearfix"></div>
                       <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-sm-3 col-md-4 col-lg-3 control-label"></label>
                            <div class="col-sm-3 col-md-4 col-lg-3 controls">  
                              <input type="button" name="show" id="show" value="{{translation('save')}}" class="form-control btn btn-primary" onClick="addPeriod();"> 
                            </div>
                        </div>
                      </div>
                </div>

                <div class="clearfix"></div>
                <hr>

                    <div class="row">
                             <div class="col-md-6">
                                  <div class="form-group">
                                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('level')}} <label style="color: red">*</label></label>
                                        <div class="col-sm-9 col-md-8 col-lg-9 controls">
                                           <select name="level" id="level" class="form-control" onChange="getClasses();" data-rule-required="true">
                                              <option value="">{{translation('select_level')}}</option>
                                              @if(isset($arr_levels) && count($arr_levels)>0)
                                                @foreach($arr_levels as $key => $level)
                                                  @if(in_array($level['level_id'],$behaviour_levels))
                                                  <option value="{{isset($level['level_details']['level_id'])?$level['level_details']['level_id']:''}}">{{isset($level['level_details']['level_name'])?$level['level_details']['level_name']:''}}</option>
                                                  @endif
                                                @endforeach
                                              @endif
                                          </select>
                                        </div>
                                    </div>
                              </div> 

                                <div class="col-md-6">
                                   <div class="form-group">
                                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('class')}} <label style="color: red">*</label></label>
                                        <div class="col-sm-9 col-md-8 col-lg-9 controls">
                                           <select name="class" id="class" class="form-control" data-rule-required="true">
                                              <option value="">{{translation('select_class')}}</option>
                                              
                                          </select>
                                        </div>
                                    </div>
                              </div>

                              <div class="col-md-6">
                                  <div class="form-group">
                                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('course')}} <label style="color: red">*</label></label>
                                        <div class="col-sm-9 col-md-8 col-lg-9 controls">
                                           <select name="course" id="course" class="form-control" data-rule-required="true">
                                              <option value="">{{translation('select_course')}}</option>
                                              {{-- @if(isset($arr_course) && count($arr_course)>0)
                                                  @foreach($arr_course as $value)
                                                      <option value="{{$value['course_id']}}" >{{$value['get_course']['course_name']}}</option>
                                                  
                                                  @endforeach
                                              @endif --}}    
                                          </select>
                                        </div>
                                    </div>
                              </div>
                              <div class="clearfix"></div>
                              <div class="col-md-6">
                                  <div class="form-group">
                                      <label class="col-sm-3 col-md-4 col-lg-3 control-label"></label>
                                      <div class="col-sm-3 col-md-4 col-lg-3 controls">
                                        <div id="button">  
                                          <input type="button" name="show" id="show" value="{{translation('show')}}" class="form-control btn btn-primary" onClick="getStudents();"> 
                                        </div>
                                      </div>
                                </div>
                              </div>
                          </div>

                          <div class="clearfix">
                          </div>                            
                          <div class="border-box" id="box" hidden="true">
                              <br>
                              <div class="filter-section">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">                            
                                            <input class="form-control" name="search_key" id="search_key" type="text" placeholder="{{translation('search')}}...">
                                        </div>
                                    </div>
                                </div>
                              </div>

                              <div class="table-responsive attendance-create-table-section" id="table_div" style="border:0;display: none;">
                                  <table class="table table-advance" id="table_module" cellpadding="35px">
                                      <thead>
                                          <tr>
                                              <th>
                                                {{translation('sr_no')}}
                                              </th>
                                              <th>
                                                  {{translation('name')}}
                                              </th>
                                              <th>
                                                  {{translation('national_id')}}
                                              </th>
                                              <th>
                                                  {{translation('average_notation')}}
                                              </th>
                                              <th>
                                                  {{translation('comment')}}
                                              </th>
                                              <th>
                                                {{translation('action')}}
                                              </th>
                                          </tr>
                                      </thead>
                                      <tbody id="tbody">
                                      </tbody>
                                  </table>
                              </div>

                              <div class="table-responsive attendance-create-table-section" id="div" style="border:0;display: none;">
                              </div>
                          </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <!-- END Main Content -->
</div>

<script>

function getClasses()
  {
      var level   =   $('#level').val();
      console.log(level);
      if(level != '')
      {
        $('#loader').fadeIn('slow');
        $('body').addClass('loader-active');
        $('#class').empty();
        $.ajax({
                url  :"{{ $module_url_path }}/getClasses",
                type :'POST',
                data :{'level':level ,'_token':'<?php echo csrf_token();?>'},
                success:function(data){
                  $('#class').append(data);
                  $('#loader').hide();
                  $('body').removeClass('loader-active');
                }
              });
      }
  }

  $('#class').on('change',function(){
     var cls = $('#class').val();
        
        $('#loader').fadeIn('slow');
        $('body').addClass('loader-active');

       $.ajax({
          url  :"{{ $module_url_path }}/get_courses",
          type :'get',
          data :{'_token':'<?php echo csrf_token();?>','class':cls},
          success:function(data){
            if(data.status == 'success')
            {
              $('#course').empty();
              $("#course").append(data.data);
            }
            else
            {
                $('#course').empty();
                $('#course').append('<option value="">{{translation('select_course')}}</option>');
                $('#error').css('display','block');
                $('#error').text(data.data);
                $('#error').show();
                setTimeout(function(){
                    $('#error').hide();
                    $('#success').hide();
                }, 3000);
            }
            $('#loader').hide();
            $('body').removeClass('loader-active');
          }
    });
  });

  function getStudents()
  {
    var level      =   $('#level').val();
    var cls_name   =   $('#class').val();
    var course     =   $('#course').val();

    if(level != '' && cls_name != '' && course !='')
    {
        $('#button').attr('disabled', true);
        $('#loader').fadeIn('slow');
        $('body').addClass('loader-active');

        $.ajax({
                  url  :"{{ $module_url_path }}/get_students_behaviour",
                  type :'POST',
                  data :{'level':level ,'class':cls_name ,'course':course,'_token':'<?php echo csrf_token();?>'},
                  success:function(data){
                    if(data.flag == true)
                    {

                      $('#div').hide();
                      $('#box').show();
                      $('#table_div').show();
                      $('#tbody').empty();
                      $('#tbody').append(data.data);
                    }
                    else if(data.flag == false)
                    {
                      $('#table_div').hide();
                      $('#div').show();
                      $('#div').append(data.data);
                    }

                    $('#button').attr('disabled','none');
                    $('#loader').hide();
                    $('body').removeClass('loader-active');
                        
                  }
                });
      }
      else
      {
        
          $('#error').css('display','block');
          $('#error').text('{{translation('select_level_class_course_first')}}');
          setTimeout(function(){
              $('#error').css('display','block');
              $('#success').css('display','block');
          }, 4000);
      }
  }

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
      })
</script>

 <script>
   
   function addPeriod()
   {
      var period = $('#period').val();
      
      $.ajax({
              url  :"{{ $module_url_path }}/store_period",
              type :'POST',
              data :{'period':period ,'_token':'<?php echo csrf_token();?>'},
              success:function(data){
                if(data.status=='success')
                {
                  
                  $('#error').hide();
                  $('#frequency').val(period);
                  $('#success').text(data.msg);
                  $('#success').show();
                  setTimeout(function(){
                      $('#error').hide();
                      $('#success').hide();
                  }, 3000);
                }
                if(data.status=='error')
                {
                  
                  $('#error').css('display','block');
                  $('#success').hide();
                  $('#error').text(data.msg);
  
                  setTimeout(function(){
                      $('#error').hide();
                      $('#success').hide();
                  }, 3000);
                }
              }
            });
   }
 </script>


@endsection