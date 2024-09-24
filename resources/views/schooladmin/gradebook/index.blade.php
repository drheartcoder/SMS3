@extends('schooladmin.layout.master')                
@section('main_content')
<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
    <li>
      <i class="fa fa-home"></i>
      <a href="{{ url($school_admin_panel_slug.'/dashboard') }}"> {{translation('dashboard')}} </a>
    </li>
    <span class="divider">
    <i class="fa fa-angle-right"></i>
    <i class="{{$module_icon}}"></i>   
    </span> 
    <li class="active"> {{ $module_title or ''}} </li>
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
      <div class="box-title">
        <h3>
          <i class="fa fa-list"></i>
          {{ isset($module_title)?$page_title:"" }}
        </h3>
        <div class="box-tool">
          <a 
            title="{{translation('refresh')}}" 
            href="{{ $module_url_path }}"
            style="text-decoration:none;">
          <i class="fa fa-repeat"></i>
          </a> 
        </div>
      </div>
      <div class="box-content">
      {!! Form::open([ 'url' => $module_url_path.'/get_students',
         'method'=>'POST',
         'enctype' =>'multipart/form-data',   
         'class'=>'form-horizontal', 
         'id'=>'frm_manage' 
         ]) !!}
         {{ csrf_field() }}
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="col-sm-3 col-lg-4 control-label">{{translation('level')}}<i class="red">*</i></label>
              <div class="col-sm-9 col-lg-8 controls">
                <select name="level" id="level" class="form-control level" data-rule-required="true">
                  <option value="">{{translation('select_level')}}</option>
                  @if(isset($arr_levels) && count($arr_levels)>0)
                  @foreach($arr_levels as $value)
                  <option value="{{$value['level_id']}}" @if(\Session::has('level_id_for_gradebook') && \Session::get('level_id_for_gradebook')==$value['level_id']) selected @endif>{{$value['level_details']['level_name']}}</option>
                  @endforeach
                  @endif    
                </select>
                <span class='help-block'>{{ $errors->first('level') }}</span>    
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="col-sm-3 col-lg-4 control-label">{{translation('class')}}<i class="red">*</i></label>
              <div class="col-sm-9 col-lg-8 controls">
                <select name="class" id="class" class="form-control level-class" data-rule-required='true'>
                  <option value="">{{translation('select_class')}}</option>
                  @if(isset($arr_class))
                    @foreach($arr_class as $value)
                      <option value="{{$value['id']}}" @if(\Session::has('class_id_for_gradebook') && \Session::get('class_id_for_gradebook')==$value['id']) selected @endif>{{$value['class_details']['class_name']}}</option>
                    @endforeach
                  @endif
                </select>
                <span class='help-block'>{{ $errors->first('class')}}</span>
              </div>
            </div>
          </div>
          <div class="clearfix"> </div>
        </div>
        
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="col-sm-3 col-lg-4 control-label">{{translation('exam_period')}}<i class="red">*</i></label>
              <div class="col-sm-9 col-lg-8 controls">
                <select name="exam_period" id="" class="form-control" data-rule-required="true">
                  @if(isset($arr_exam_period) && count($arr_exam_period)<=0)
                  <option value="">{{translation('select_exam_period')}}</option>
                  @endif  
                  @if(isset($arr_exam_period) && count($arr_exam_period)>0)
                  @foreach($arr_exam_period as $value)
                  <option value="{{$value['exam_id']}}"  @if(\Session::has('exam_period') && \Session::get('exam_period')==$value['id']) selected @endif>{{$value['get_exam_period']['exam_name']}}</option>
                  @endforeach
                  @endif    
                </select>
                <span class='help-block'>{{ $errors->first('exam_period') }}</span>    
              </div>
            </div>
          </div>
        </div>
        
        <div class="row">
          <div class="form-group">
            <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
              <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('submit')}}</button>
            </div>
          </div>
        </div>
        <br>
        <div class="border-box">
          <div class="table-responsive" style="border:0">
            <input type="hidden" name="multi_action" value="" />
            <table class="table table-advance" id="table_module">
              <thead>
                <tr>
                  <th>
                    <div class="check-box">
                      <input type="checkbox" class="filled-in" name="selectall" id="selectall" />
                      <label for="selectall"></label>
                    </div>
                  </th>
                  <th>{{translation('student_name')}}</th>
                  <th>{{translation('student_number')}}</th>
                  <th>{{translation('national_id')}}</th>
                  <th>{{translation('action')}}</th>
                </tr>
              </thead>
              <tbody>
                @if(isset($arr_students) && count($arr_students)>0)
                  @foreach($arr_students as $student)
                  <tr>
                    <td>
                      <div class="check-box">
                        <input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_{{$student->id}}" value="{{$student->id}}" />
                          <label for="mult_change_{{$student->id}}"></label>
                      </div>
                    </td>
                    <td>{{ucfirst($student->get_user_details->first_name).' '.ucfirst($student->get_user_details->last_name)}}</td>
                    <td>{{$student->student_no}}</td>
                    <td>{{$student->get_user_details->national_id}}</td>
                    <td><a title="{{translation('generate_gradebook')}}" href="{{$module_url_path.'/generate_gradebook/'.base64_encode($student->id)}}"><i class="fa fa-book"></i></a></td>
                  </tr>
                  @endforeach
                @endif
              </tbody>
            </table>
            <div id="hide_row" class="alert alert-danger" style="text-align:center" @if(isset($arr_students) && count($arr_students)> 0) hidden @endif>{{translation('no_data_available')}}</div>
             <div class="col-md-12">    
                <div class="form-group back-btn-form-block">
                   <div class="controls">
                      <a href="{{ $module_url_path }}" class="btn btn-primary" style="float: right;margin-top: 20px;" > <i class="fa fa-arrow-left"></i> {{translation('back')}} </a>
                   </div>
                </div>  
              </div>
          </div>
        </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
  $(".level").on('change',function(){
    var level = $('.level').val();
    var level_class = "{{\Session::has('class_id') ? \Session::get('class_id') :0 }}";
    $(".level-class").empty();
       $.ajax({
          url  :"{{ $module_url_path }}/getClasses",
          type :'get',
          data :{'_token':'<?php echo csrf_token();?>','level':level,'level_class_id':level_class},
          success:function(data){
            
                 $(".level-class").append(data);
              
          }
    });

});


</script>
@stop                    


