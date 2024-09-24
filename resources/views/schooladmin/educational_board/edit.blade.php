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
                     <input class="form-control" type="text" name="name" value="{{isset($field['board'])?$field['board']:''}}" data-rule-required="true" pattern="[a-zA-Z0-9\- àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$"/>
                     <span class='help-block'>{{ $errors->first('name')}}</span>
                  </div>
               </div>
              <div class="form-group">
                  <label class="col-sm-3 col-lg-2 control-label">{{translation('professor')}}<i class="red">*</i></label>
                  <?php
                    $prof = [];
                    if(isset($field['professor']) && count($field['professor'])>0)
                    {
                        $prof = explode(',',$field['professor']);
                    }
                  ?>
                  <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">  
                      <select data-rule-required='true' class="js-example-basic-multiple form-control" multiple="multiple" name="professor[]" id="professor" placeholder= "{{translation('select_professor')}}">
                         @foreach($arr_professors as $key => $value)
                            <option value="{{$value->user_id}}" @if(isset($prof) && count($prof)>0 && in_array($value->user_id,$prof)) selected @endif>{{$value->user_name or ''}}</option>
                         @endforeach
                      </select>
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-3 col-lg-2 control-label">{{translation('employee')}}<i class="red">*</i></label>
                  <?php
                    $emp = [];
                    if(isset($field['employee']) && count($field['employee'])>0)
                    {
                        $emp = explode(',',$field['employee']);
                    }
                  ?>
                  <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">  
                      <select data-rule-required='true' class="js-example-basic-multiple form-control" multiple="multiple" name="employee[]" id="employee" placeholder= "{{translation('select_employee')}}">
                          @foreach($arr_employees as $key => $value)
                            <option value="{{$value->user_id}}" @if(isset($emp) && count($emp)>0 && in_array($value->user_id,$emp)) selected @endif>{{$value->user_name or ''}}</option>
                         @endforeach
                      </select>
                  </div>
                </div>
               
                <div class="form-group">
                    <label class="col-sm-3 col-lg-2 control-label">{{translation('school_admin')}}<i class="red">*</i></label>
                    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
                        <div class="assignment-gray-main">
                            <?php
                                $school_admin = get_school_admin();
                                $first_name = isset($school_admin->get_user_details->first_name)?ucfirst($school_admin->get_user_details->first_name):'';
                                $last_name = isset($school_admin->get_user_details->last_name)?ucfirst($school_admin->get_user_details->last_name):'';
                                $name= $first_name.' '.$last_name;
                                $id = isset($school_admin->school_id)?$school_admin->school_id:0;
                            ?>
                            <select class="form-control" name="school_admin" data-rule-required='true'>
                                <option value="0">{{translation('select')}}</option>
                                <option value="1" @if($field->school_admin=='1') selected @endif>{{$name}}</option>
                             </select>
                             <span class='help-block'>{{ $errors->first('class_name')}}</span>
                         
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
  $(".js-example-basic-multiple").select2();
   $(document).ready(function() {
  $('input[name="supervisor_type"]').click(function(){
        var user = $(this).val();
        console.log(user);
        if(user == 'employee')
        {
          $("#supervisor").empty();  
          
          $.ajax({
            url  :"{{ $module_url_path }}/get_employees",
            type :'GET',
            success:function(data){
              $("#supervisor").append(data);
              $("#supervisor").trigger("chosen:updated");
            }
          });
        }
        else
        {
          $("#supervisor").empty();
          
          $.ajax({
            url  :"{{ $module_url_path }}/get_professors",
            type :'GET',
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
