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
                <li><a href="{{$module_url_path}}">{{$module_title}}</a></li> 
                <span class="divider">
                    <i class="fa fa-angle-right"></i>
                </span> 
                <li class="active">{{$page_title}}</li>
            </ul>
        </div>
        
        <!-- END Breadcrumb -->
        <div class="box  box-navy_blue">
            <div class="box-title">
                <h3>{{$page_title}}</h3>
                <div class="box-tool">
                </div>
            </div>
            <div class="box-content">
                 @include('professor.layout._operation_status')
                  <form method="POST" action="{{$module_url_path}}/store" onsubmit="return addLoader()" id="validation-form1" accept-charset="UTF-8" class="form-horizontal" enctype="multipart/form-data" >
                       {{ csrf_field() }}  
                          <div class="form-group">
                                <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('level')}}<i class="red">*</i></label>
                                <div class="col-sm-9 col-md-8 col-lg-4 controls">
                                  <select name="level" id="level" class="form-control level" data-rule-required='true'>
                                  <option value="">{{translation('select')}} {{translation('level')}}</option>
                                    @if(isset($arr_levels) && count($arr_levels)>0)
                                        @foreach($arr_levels as $value)
                                            <option value="{{$value['level_id']}}" >{{$value['level_details']['level_name']}}</option>
                                        @endforeach
                                    @endif    
                                  </select>
                                </div>
                          </div><div class="clearfix"></div>
                  
                          <div class="form-group">
                              <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('class')}}<i class="red">*</i></label>
                              <div class="col-sm-9 col-md-8 col-lg-4 controls">
                                 <select name="class" id="class" class="form-control level-class" data-rule-required='true'>
                                    <option value="">{{translation('select')}} {{translation('class')}}</option>
                                </select>
                              </div>
                          </div><div class="clearfix"></div>

                           <div class="form-group">
                              <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('student_name')}}<i class="red">*</i></label>
                              <div class="col-sm-9 col-md-8 col-lg-4 controls">
                                 <select name="student_name" id="student_name" class="form-control student_name" data-rule-required='true'>
                                     <option value="">{{translation('select')}} {{translation('student_name')}}</option>
                                </select>
                              </div>
                          </div><div class="clearfix"></div>

                          <div class="form-group">
                              <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('student')}} {{translation('national_id')}}</label>
                              <div class="col-sm-9 col-md-8 col-lg-4 controls">
                                  <input class="form-control" name="student_national_id" id="student_national_id" type="text" placeholder="{{translation('enter')}} {{translation('student')}} {{translation('national_id')}}"   pattern="[a-zA-Z0-9 àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$"/>
                              </div>
                          </div><div class="clearfix"></div>

                          <div class="form-group">
                              <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('claim_title')}}<i class="red">*</i></label>
                              <div class="col-sm-9 col-md-8 col-lg-4 controls">
                                  <input class="form-control" name="claim_title" id="claim_title" type="text" placeholder="{{translation('enter')}} {{translation('claim_title')}}"  data-rule-required='true'/>
                              </div>
                          </div><div class="clearfix"></div>

                          <div class="form-group">
                              <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('claim')}} {{translation('description')}}<i class="red">*</i></label>
                              <div class="col-sm-9 col-md-8 col-lg-4 controls">
                                  <textarea name="description" id="description" cols="30" placeholder="{{translation('claim')}} {{translation('description')}}" rows="4" class="form-control" data-rule-required='true'></textarea>
                              </div>  
                          </div><div class="clearfix"></div>
                          
                          <div class="col-sm-8 col-sm-offset-4 col-lg-9 col-lg-offset-3">
                              <a href="{{$module_url_path}}" class="btn btn-primary">{{translation('back')}}</a>
                              <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('save')}}</button>
                          </div>
                          <div class="clearfix"></div>
               
                    </div>
                  </form>
            </div>       
          </div>
           
<script>
$(document).ready(function()
{
    var level = $('.level').val();

    if(level!='')
    {
        $(".level-class").empty();
        $.ajax({
          url  :"{{ $module_url_path }}/get_classes",
          type :'get',
          data :{'_token':'<?php echo csrf_token();?>','level':level},
          success:function(data){
            
                 $(".level-class").append(data);
              
          }
        });
    }

    var cls = $('.class').val();
    if(cls!='' && level!='')
    {
        $(".student_name").empty();
        $.ajax({
          url  :"{{ $module_url_path }}/get_students",
          type :'get',
          data :{'_token':'<?php echo csrf_token();?>','class':cls,'level':level},
          success:function(data){
                console.log(data);
                 $(".student_name").append(data);
              
          }
        });
    }
});

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

$(".level-class").on('change',function(){
    var level = $('#level').val();
    var cls = $('#class').val();
    if(cls!='' && level!='')
    {
        $(".student_name").empty();
        $.ajax({
          url  :"{{ $module_url_path }}/get_students",
          type :'get',
          data :{'_token':'<?php echo csrf_token();?>','class':cls},
          success:function(data){
                console.log(data);
                 $(".student_name").append(data);
              
          }
        });
    }
});

</script>  
@endsection