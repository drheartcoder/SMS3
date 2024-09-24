@extends('professor.layout.master')    
@section('main_content')  
  
        <!-- BEGIN Breadcrumb -->
        <div id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <a href="{{ url($professor_panel_slug.'/dashboard') }}"> {{translation('dashboard')}}</a>
                </li>
                <span class="divider">
                    <i class="fa fa-angle-right"></i>
                </span> 
                <li><a href="{{$module_url_path}}">{{translation('homework')}}</a></li> 
                <span class="divider">
                    <i class="fa fa-angle-right"></i>
                </span> 
                <li class="active"> {{translation('add')}} {{translation('homework')}}</li>
            </ul>
        </div>
        
        <!-- BEGIN Page Title -->
          <div class="page-title new-agetitle">
              <div>
                  <h1><i class="fa fa-book"></i> {{translation('homework')}}  </h1>
              </div>
          </div>
          <!-- END Page Title -->

        <!-- END Breadcrumb -->
        <div class="box  box-navy_blue">
            <div class="box-title">
                <h3> <i class="fa fa-plus-circle"></i> {{translation('add')}} {{translation('homework')}}</h3>
                <div class="box-tool">
                </div>
            </div>
            <div class="box-content">
             @include('professor.layout._operation_status')
              <form method="POST" action="{{$module_url_path}}/store" onsubmit="return addLoader()" id="validation-form1" accept-charset="UTF-8" class="form-horizontal" enctype="multipart/form-data" >
                   {{ csrf_field() }}  
                <div class="form-group">
                      <label class="col-sm-3 col-lg-2 control-label">{{translation('level')}}<i class="red">*</i></label>
                      <div class="col-sm-9 col-md-8 col-lg-4 controls">
                        <select name="level" id="level" class="form-control level" data-rule-required='true'>
                        <option value="">{{translation('select_level')}}</option>
                          @if(isset($arr_levels) && count($arr_levels)>0)
                              @foreach($arr_levels as $value)
                                  <option value="{{$value['level_id']}}" >{{$value['level_details']['level_name']}}</option>
                              @endforeach
                          @endif    
                        </select>
                      </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 col-lg-2 control-label">{{translation('class')}}<i class="red">*</i></label>
                    <div class="col-sm-9 col-md-8 col-lg-4 controls">
                      <select name="class" id="class" class="form-control level-class" data-rule-required='true' onChange="getCourses();">
                      <option value="">{{translation('select_class')}}</option>
                                                      
                      </select>
                    </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-3 col-lg-2 control-label">{{translation('course')}}<i class="red">*</i></label>
                  <div class="col-sm-9 col-md-8 col-lg-4 controls">
                     <select name="course" id="course" class="form-control course" data-rule-required='true'>
                      <option value="">{{translation('select_course')}}</option>
                           
                        </select>
                        <span class='help-block'>{{ $errors->first('level') }}</span>    
                  </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 col-lg-2 control-label">{{translation('homework_details')}}<i class="red">*</i></label>
                    <div class="col-sm-9 col-md-8 col-lg-4 controls">
                        <textarea class="form-control" name="homework_details" placeholder="{{translation('enter')}} {{translation('homework_details')}}" type="text" data-rule-required='true' rows="4"></textarea>
                        <span class='help-block'>{{ $errors->first('homework_details') }}</span>  
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 col-lg-2 control-label">{{translation('due_date')}}<i class="red">*</i></label>
                    <div class="col-sm-9 col-md-8 col-lg-4 controls">
                       <input class="form-control datepikr" id="datepicker"  name="due_date" placeholder="{{translation('enter')}} {{translation('due_date')}}" type="text" data-rule-required='true' autocomplete="off" />
                       <span class='help-block'>{{ $errors->first('due_date') }}</span>   
                    </div>
                </div>     
                <div class="col-sm-8 col-sm-offset-4 col-lg-9 col-lg-offset-3">
                    <a href="{{$module_url_path}}" class="btn btn-primary">{{translation('back')}}</a>
                    <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('save')}}</button>
                </div>
                <div class="clearfix"></div>
              </form>
              </span></div></div>
           
<script>
  $(".level").on('change',function(){
      var level = $('.level').val();
   
      $(".level-class").empty();
         $.ajax({
            url  :"{{ $module_url_path }}/get_classes",
            type :'get',
            data :{'_token':'<?php echo csrf_token();?>','level':level},
            success:function(data){
              
                   $(".level-class").append(data);
                
            }
      });

  });
  $(function() {
            $( "#datepicker" ).datepicker({
                todayHighlight: true,
                autoclose: true,
                startDate:new Date()
            });
        });
</script>


<script>
  function getCourses() {
    var level      =   $('#level').val();
    var cls_name   =   $('#class').val();

    $('#course').empty();
    $.ajax({
              url  :"{{ $module_url_path }}/get_courses",
              type :'POST',
              data :{'level':level ,'class':cls_name ,'_token':'<?php echo csrf_token();?>'},
              success:function(data){
                $('#course').empty();
                $('#course').append(data);
              }
            });
  }
</script>
@endsection