@extends('professor.layout.master')                
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
    <li>
      <i class="fa fa-home"></i>
      <a href="{{url($professor_panel_slug)}}/dashboard">{{translation('dashboard')}}</a>
    </li>
    <span class="divider">
      <i class="fa fa-angle-right"></i>
    </span>
    <li>
      <i class="{{$module_icon}}"></i>
      <a href="{{$module_url_path}}">{{str_plural($page_title)}}</a>
    </li>
    <span class="divider">
      <i class="fa fa-angle-right"></i>
    </span>
    <i class="{{$create_icon}}"></i>
    <li class="active">{{$module_title}}</li>
  </ul>
</div>
<!-- END Breadcrumb -->
<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
  <div>
    <h1><i class="{{$module_icon}}"></i>{{str_plural($module_title)}}</h1>

  </div>
</div>
<!-- END Page Title -->


<!-- BEGIN Tiles -->
<div class="row">
  <div class="col-md-12">
    <div class="box  box-navy_blue">
      <div class="box-title">
        <h3><i class="{{$edit_icon}}"></i>{{$module_title}}</h3>
        <div class="box-tool">
        </div>
      </div>  
      <div class="box-content">
       @include('professor.layout._operation_status')
       <form method="post" onsubmit="return addLoader()" action="{{$module_url_path}}/update/{{base64_encode($arr_data['id'])}}"  class="form-horizontal" id="validation-form1">
        {{ csrf_field() }}


              
        
        <div class="red box-new">
          <div class="form-group">
            <label class="col-sm-3 col-lg-2 control-label">{{translation('level')}}<i class="red">*</i></label>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
              <input type="text" class="form-control" value="@if($arr_data['level_id']==0){{isset($arr_data['get_level_class']['level_details']['level_name']) ? $arr_data['get_level_class']['level_details']['level_name'] :0 }}@else{{isset($arr_data['level_details']['level_name']) ? $arr_data['level_details']['level_name'] :0 }} @endif" disabled/>
              <span class='help-block'>{{ $errors->first('level') }}</span>    
            </div>
            <div class="clearfix"></div>
          </div>
          @if($arr_data['level_id']==0)
          <div class="form-group">
            <label class="col-sm-3 col-lg-2 control-label">{{translation('class')}}</label>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
              <input type="text" class="form-control" value="{{isset($arr_data['get_level_class']['class_details']['class_name']) ? $arr_data['get_level_class']['class_details']['class_name'] :0 }}" disabled/>
              <span class='help-block'>{{ $errors->first('class')}}</span>

            </div>
            <div class="clearfix"></div>
          </div>
          @endif
        </div>

        <div class="green box-new">
        </div> 

        <div class="form-group">
          <label class="col-sm-3 col-lg-2 control-label">{{translation('task_name')}}<i class="red">*</i></label>
          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
            <input type="text" class="form-control" name="task_name" id="task_name" value="{{$arr_data['task_name']}}" data-rule-required='true'/>
            <span class='help-block'>{{ $errors->first('task_name')}}</span>
          </div>

          <div class="clearfix"></div>
        </div>   

        <div class="form-group">
          <label class="col-sm-3 col-lg-2 control-label">{{translation('task_priority')}} <i class="red">*</i></label>
          <div class="col-sm-9 col-lg-4 controls">
              <div class="radio-btns">  
                  <div class="radio-btn">
                      <input type="radio" id="high" name="priority" value="HIGH" @if($arr_data['priority'] == "HIGH") checked @endif>
                      <label for="high">{{translation('high')}}</label>
                      <div class="check"></div>
                  </div>
                  <div class="radio-btn">
                      <input type="radio" id="medium" name="priority" value="MEDIUM" @if($arr_data['priority'] == "MEDIUM") checked @endif>
                      <label for="medium">{{translation('medium')}}</label>
                      <div class="check"><div class="inside"></div></div>
                  </div>
                  <div class="radio-btn">
                      <input type="radio" id="low" name="priority" value="LOW" @if($arr_data['priority'] == "LOW") checked @endif>
                      <label for="low">{{translation('low')}}</label>
                      <div class="check"><div class="inside"></div></div>
                  </div>
              </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-3 col-lg-2 control-label">{{translation('description')}}<i class="red">*</i></label>
          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
            <textarea class="form-control" name="description" id="description" data-rule-required='true'>{{$arr_data['task_description']}}</textarea>
            <span class='help-block'>{{ $errors->first('description')}}</span>
          </div>

          <div class="clearfix"></div>
        </div>  

        <div class="form-group">
          <label class="col-sm-3 col-lg-2 control-label">{{translation('submission_date')}}<i class="red">*</i></label>
          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
            <input class="form-control datepikr" name="submission_date" id="datepicker" placeholder="{{translation('enter')}} {{strtolower(translation('submission_date'))}}" type="text" value="{{$arr_data['submission_date']}}" data-rule-required='true'/>
            <span class='help-block'>{{ $errors->first('submission_date')}}</span>
          </div>

          <div class="clearfix"></div>
        </div>

        <div class="form-group">
          <label class="col-sm-3 col-lg-2 control-label">{{translation('submission_time')}}<i class="red">*</i></label>
          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
            <input class="form-control timepicker-default" name="submission_time" placeholder="{{translation('enter')}} {{strtolower(translation('submission_time')) }}" type="text" value="{{$arr_data['submission_time']}}" data-rule-required='true'/>
            <span class='help-block'>{{ $errors->first('submission_time')}}</span>
          </div>

          <div class="clearfix"></div>
        </div>

        <div class="form-group">
          <label class="col-sm-3 col-lg-2 control-label">{{translation('supervisor')}}<i class="red">*</i></label>
          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">  
            <div class="radio-btns">  
              <div class="radio-btn">
                <input type="radio" id="employee" name="supervisor_type" value="employee" @if($arr_data['supervisor_role'] == 'employee') checked @endif>
                <label for="employee">{{translation('employee')}}</label>
                <div class="check"></div>
              </div>
              <div class="radio-btn">
                <input type="radio" id="professor" name="supervisor_type" value="professor" @if($arr_data['supervisor_role'] == 'professor') checked @endif>
                <label for="professor">{{translation('professor')}}</label>
                <div class="check"><div class="inside"></div></div>
              </div>
            </div>   
          </div>
          <div class="clearfix"></div>
        </div>

        <div class="form-group">
          <label class="col-sm-3 col-lg-2 control-label"></label>
          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">  
            <select name="supervisor" id="supervisor" class="form-control chosen" data-rule-required='true'>
                <option value="">{{translation('select')}}</option>
                @if(count($arr_employees>0))
                 @foreach($arr_employees as $value)
                  <option value="{{$value->user_id}}" @if($arr_data['task_supervisor_id']==$value->user_id) selected @endif>{{ucwords($value->user_name)}}</option>
                 @endforeach
                @endif 
              </select>
              <span class='help-block'>{{ $errors->first('supervisor')}}</span>
          <div class="clearfix"></div>
          </div>
        </div>
          
        <div class="form-group">
          <label class="col-sm-3 col-lg-2 control-label">{{translation('status')}}</label>
          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">  
            <select name="status" id="status" class="form-control" data-rule-required='true'>
                <option value='OPEN' @if($arr_data['task_status']=="OPEN") selected @endif >{{translation('open')}}</option>
                <option value='ON_HOLD' @if($arr_data['task_status']=="ON_HOLD") selected @endif>{{translation('on_hold')}}</option>
                <option value='RESOLVED' @if($arr_data['task_status']=="RESOLVED") selected @endif>{{translation('resolved')}}</option>
                <option value='CLOSED' @if($arr_data['task_status']=="CLOSED") selected @endif>{{translation('closed')}}</option>
                
              </select>
          <div class="clearfix"></div>
          </div>
        </div>

        <div class="form-group">
          <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
            <a href="{{$module_url_path}}" class="btn btn-primary">{{translation('back')}}</a>
            <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('update')}}</button>
          </div>
        </div>

      </form>
    </div>
  </div>
</div>
</div>
<script>
  $(function() {
      $( "#datepicker" ).datepicker({
          todayHighlight: true,
          autoclose: true,
      });
  }); 
  $( function() {    
      $('.timepicker-default').timepicker(); 
      
  });
  $.fn.timepicker.defaults = {
    defaultTime: 'current',
    disableFocus: false,
    disableMousewheel: false,
    isOpen: false,
    minuteStep: 15,
    modalBackdrop: false,
    orientation: { x: 'auto', y: 'auto'},
    secondStep: 15,
    showSeconds: false,
    showInputs: true,
    showMeridian: false,
    template: 'dropdown',
    appendWidgetTo: 'body',
    showWidgetOnAddonClick: true
  };
        </script>
<script>
  
  $(document).ready(function() {

    $(".level").on('change',function(){
      var level = $('.level').val();
      if(level!='')
      {
        $(".level-class").empty().not('select-class');
        $.ajax({
          url  :"{{ $module_url_path }}/getClasses",
          type :'get',
          data :{'_token':'<?php echo csrf_token();?>','level':level},
          success:function(data){
            $(".level-class").append(data);
          }
        });  
      }
    });
    $('input[name="supervisor_type"]').click(function(){
        var user = $(this).val();
        
        if(user == 'employee')
        {
          $("#supervisor").empty();  
          $.ajax({
            url  :"{{ $module_url_path }}/get_employees",
            type :'get',
            data :{'_token':'<?php echo csrf_token();?>'},
            success:function(data){
              $("#supervisor").append(data);
            }
          });
        }
        else
        {
          $("#supervisor").empty();
          $.ajax({
            url  :"{{ $module_url_path }}/get_professors",
            type :'get',
            data :{'_token':'<?php echo csrf_token();?>'},
            success:function(data){
              $("#supervisor").append(data);
              $("#supervisor").trigger("chosen:updated");
            }
          });
        }
    });  
  });    
</script>
@endsection
