@extends('professor.layout.master')                
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
    <li>
      <i class="fa fa-home"></i>
      <a href="{{url($professor_panel_slug.'/dashboard')}}">{{translation('dashboard')}}</a>
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
    <h1><i class="{{$module_icon}}"></i>{{str_plural($page_title)}}</h1>

  </div>
</div>
<!-- END Page Title -->


<!-- BEGIN Tiles -->
<div class="row">
  <div class="col-md-12">
    <div class="box  box-navy_blue">
      <div class="box-title">
        <h3><i class="{{$create_icon}}"></i>{{$module_title}}</h3>
        <div class="box-tool">
        </div>
      </div>  
      <div class="box-content">
       @include('professor.layout._operation_status')
       <form method="post" onsubmit="return addLoader()" action="{{$module_url_path}}/store"  class="form-horizontal" id="validation-form1">
        {{ csrf_field() }}

        <div class="form-group">
          <label class="col-sm-3 col-lg-2 control-label">{{translation('task_name')}}<i class="red">*</i></label>
          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
            <input type="text" class="form-control" name="task_name" pattern="[a-zA-Z àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ\-]+$"  id="task_name" data-rule-required='true'/>
            <span class='help-block'>{{ $errors->first('task_name')}}</span>
          </div>

          <div class="clearfix"></div>
        </div>  
            
        <div class="red box-new">
          <div class="form-group">
            <label class="col-sm-3 col-lg-2 control-label">{{translation('level')}}<i class="red">*</i></label>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
              <select name="level" id="level" class="form-control level" data-rule-required='true'>
                <option value='' selected id='select-level'>{{translation('select_level')}}</option>
                @if(isset($arr_levels) && count($arr_levels)>0)
                @foreach($arr_levels as $value)
                <option value="{{$value['level_id']}}" >{{$value['level_details']['level_name']}}</option>
                @endforeach
                @endif    
              </select>
              <span class='help-block'>{{ $errors->first('level') }}</span>    
            </div>
            <div class="clearfix"></div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 col-lg-2 control-label">{{translation('class')}}</label>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
              <select name="class" id="level_class" class="form-control level-class">
              <option value="">{{translation('select_class')}}</option>
              </select>

              <span class='help-block'>{{ $errors->first('class')}}</span>

            </div>
            <div class="clearfix"></div>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-3 col-lg-2 control-label">{{translation('task_priority')}} <i class="red">*</i></label>
          <div class="col-sm-9 col-lg-4 controls">
              <div class="radio-btns">  
                  <div class="radio-btn">
                      <input type="radio" id="high" name="priority" value="HIGH" checked>
                      <label for="high">{{translation('high')}}</label>
                      <div class="check"></div>
                  </div>
                  <div class="radio-btn">
                      <input type="radio" id="medium" name="priority" value="MEDIUM">
                      <label for="medium">{{translation('medium')}}</label>
                      <div class="check"><div class="inside"></div></div>
                  </div>
                  <div class="radio-btn">
                      <input type="radio" id="low" name="priority" value="LOW">
                      <label for="low">{{translation('low')}}</label>
                      <div class="check"><div class="inside"></div></div>
                  </div>
              </div>
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-3 col-lg-2 control-label">{{translation('description')}}<i class="red">*</i></label>
          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
            <textarea class="form-control" name="description" id="description" data-rule-required='true'></textarea>
            <span class='help-block'>{{ $errors->first('description')}}</span>
          </div>

          <div class="clearfix"></div>
        </div>  

        <div class="form-group">
          <label class="col-sm-3 col-lg-2 control-label">{{translation('submission_date')}}<i class="red">*</i></label>
          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
            <input class="form-control datepikr" name="submission_date" id="datepicker" placeholder="{{translation('enter')}} {{strtolower(translation('submission_date'))}}" type="text" data-rule-required='true'/>
            <span class='help-block'>{{ $errors->first('submission_date')}}</span>
          </div>

          <div class="clearfix"></div>
        </div>

        <div class="form-group">
          <label class="col-sm-3 col-lg-2 control-label">{{translation('submission_time')}}<i class="red">*</i></label>
          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
            <input class="form-control timepicker-default" name="submission_time" placeholder="{{translation('enter')}} {{strtolower(translation('submission_time')) }}" type="text" data-rule-required='true'/>
            <span class='help-block'>{{ $errors->first('submission_time')}}</span>
          </div>

          <div class="clearfix"></div>
        </div>

        <div class="form-group">
          <label class="col-sm-3 col-lg-2 control-label">{{translation('supervisor')}}<i class="red">*</i></label>
          <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">  
            <div class="radio-btns">  
              <div class="radio-btn">
                <input type="radio" id="employee" name="supervisor_type" value="employee" checked>
                <label for="employee">{{translation('employee')}}</label>
                <div class="check"></div>
              </div>
              <div class="radio-btn">
                <input type="radio" id="professor" name="supervisor_type" value="professor">
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
                  <option value="{{$value->user_id}}">{{ucwords($value->user_name)}}</option>
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
                <option value='OPEN'>{{translation('open')}}</option>
                <option value='ON_HOLD'>{{translation('on_hold')}}</option>
                <option value='RESOLVED'>{{translation('resolved')}}</option>
                <option value='CLOSED'>{{translation('closed')}}</option>
                
              </select>
              <span class='help-block'>{{ $errors->first('status')}}</span>
          <div class="clearfix"></div>
          </div>
        </div>

        <div class="form-group">
          <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
            <a href="{{$module_url_path}}" class="btn btn-primary">{{translation('back')}}</a>
            <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('save')}}</button>
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
    
</script>
@endsection
