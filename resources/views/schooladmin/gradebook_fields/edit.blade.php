@extends('schooladmin.layout.master')                
@section('main_content')
<style>
.sweet-alert .sa-icon.sa-error .sa-x-mark{left: 5px !important; top: -3px !important;}      
.sweet-alert .sa-icon.sa-error .sa-line.sa-left{left: 7px !important;}
.school-admin-main .btn.btn-success, .school-admin-main .btn.btn-danger{right: 15px !important; padding: 10px 12px !important;}
.school-admin-main .table.table-advance tbody tr td:last-child a{padding: 10px 12px 6px !important;}
</style>
<style>
    .chosen-container-single .chosen-single div b{right: 0px !important; }
</style>
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
    <li>
      <i class="{{$module_icon}}"></i>
      <a href="{{$module_url_path}}">{{str_plural($module_title)}}</a>
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
    <h1><i class="{{$module_icon}}"></i>{{$module_title}}</h1>

  </div>
</div>
<!-- END Page Title -->


<!-- BEGIN Tiles -->
<div class="row">
   <div class="col-md-12">
      <div class="box  box-navy_blue">
         <div class="box-title">
            <h3><i class="{{$edit_icon}}"></i>{{$page_title}}</h3>
            <div class="box-tool">
            </div>
         </div>
         <div class="box-content">
            @include('schooladmin.layout._operation_status')
            <form method="post" onsubmit="return addLoader()"  action="{{$module_url_path}}/update/{{base64_encode($field['id'])}}"  class="form-horizontal" id="validation-form1">
               {{ csrf_field() }} 
               
               <br>
               <div class="form-group">
                  <label class="col-sm-3 col-md-3 col-lg-2 control-label">{{translation('name')}}<i class="red">*</i></label>
                  <div class="col-sm-8 col-md-8 col-lg-4 controls">
                     <input class="form-control" type="text" name="name" value="{{isset($field['name'])?$field['name']:''}}" data-rule-required="true"/>
                     <span class='help-block'>{{ $errors->first('name')}}</span>
                  </div>
               </div>
              
               <div class="form-group">
                    <label class="col-sm-3 col-md-3 col-lg-2 control-label">{{translation('type')}}<i class="red">*</i></label>
                    <div class="col-sm-8 col-md-8 col-lg-4 controls">

                        <select name="type" id="type" class="form-control level" data-rule-required="true">
                        <option value="complement" @if($field['type']=='COMPLEMENT') selected @endif>{{translation('complement')}}</option>
                        <option value="warning" @if($field['type']=='WARNING') selected @endif>{{translation('warning')}}</option>   
                        </select>
                        <span class='help-block'>{{ $errors->first('type') }}</span>    
                    </div>
                </div>
                
                  <div class="form-group">
                    <label class="col-sm-3 col-md-3 col-lg-2 control-label">{{translation('default_value')}} 1)</label>
                    <div class="col-sm-8 col-md-8 col-lg-4 controls">
                       <textarea class="form-control" type="text" id="default_value1" name="default_value1" ">{{isset($field['default_value1'])?$field['default_value1']:''}}</textarea>
                       <span class='help-block'>{{ $errors->first('default_value1')}}</span>
                    </div>
                 </div>
                 <div class="form-group">
                    <label class="col-sm-3 col-md-3 col-lg-2 control-label">2)</label>
                    <div class="col-sm-8 col-md-8 col-lg-4 controls">
                       <textarea class="form-control" type="text" id="default_value2" name="default_value2" >{{isset($field['default_value2'])?$field['default_value2']:''}}</textarea>
                       <span class='help-block'>{{ $errors->first('default_value2')}}</span>
                    </div>
                 </div>
                 <div id="complement">
                   <div class="form-group">
                      <label class="col-sm-3 col-md-3 col-lg-2 control-label">3)</label>
                      <div class="col-sm-8 col-md-8 col-lg-4 controls">
                         <textarea class="form-control" type="text" id="default_value3" name="default_value3">{{isset($field['default_value3'])?$field['default_value3']:''}}</textarea>
                         <span class='help-block'>{{ $errors->first('default_value3')}}</span>
                      </div>
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

var type = $("#type").val();
  if(type=='complement'){
    $("#complement").css('display','block');
  }
  else{
    $("#complement").css('display','none');
  }

$("#type").change(function(){
  var type = $(this).val();
  if(type=='complement'){
    $("#complement").css('display','block');
    @if($field['type']=='COMPLEMENT'){
      var value1 = "{{isset($field['default_value1'])?$field['default_value1']:''}}";
      var value2 = "{{isset($field['default_value2'])?$field['default_value2']:''}}";
      var value3 = "{{isset($field['default_value3'])?$field['default_value3']:''}}";
      $("#default_value1").text(value1);
      $("#default_value2").text(value2);  
      $("#default_value3").text(value3);  
    }
    @else{
      $("#default_value1").text('');
      $("#default_value2").text('');  
      $("#default_value3").text('');  
    }
    @endif
    
  } 
  if(type=='warning'){
    $("#complement").css('display','none');
    @if($field['type']=='WARNING'){
      var value1 = "{{isset($field['default_value1'])?$field['default_value1']:''}}";
      var value2 = "{{isset($field['default_value2'])?$field['default_value2']:''}}";
      
      $("#default_value1").text(value1);
      $("#default_value2").text(value2);  
      
    }
    @else{
      $("#default_value1").text('');
      $("#default_value2").text('');  
      
    }
    @endif

  }



});
</script>
@endsection
