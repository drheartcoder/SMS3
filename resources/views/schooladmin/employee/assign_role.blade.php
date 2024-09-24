@extends('schooladmin.layout.master')                
@section('main_content')
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
          <a href="{{$module_url_path}}">{{$module_title}}</a>
        </li>
        <span class="divider">
            <i class="fa fa-angle-right"></i>
        </span>
        <i class="{{$create_icon}}"></i>
        <li class="active">{{translation('assign_role')}}</li>
    </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
    <div>
        <h1>{{translation('assign_role')}}</h1>

    </div>
</div>
<!-- END Page Title -->

<!-- BEGIN Main Content -->

<div class="row">
<div class="col-md-12">
   <div class="box box-navy_blue">
      <div class="box-title">
         <h3>
            <i class="fa {{$create_icon}}">
            </i>{{ isset($page_title)?$page_title:"" }} 
         </h3>
         <div class="box-tool">
         </div>
      </div>
      <div class="box-content">
         @include('schooladmin.layout._operation_status')
         <div class="tobbable">
            <form method="post" onsubmit="return addLoader()"  action="{{$module_url_path}}/role_store"  class="form-horizontal" id="validation-form1" enctype ='multipart/form-data'>
                 {{ csrf_field() }}

<div class="row">
    <div class="col-lg-6">
            <div class="row">
               <div class="form-group">
                      <label class="col-sm-3 col-lg-4 control-label">Select Employee/Staff Name<i class="red">*</i></label>
                      <div class="col-sm-9 col-lg-8 controls">
                          <select name="emp_id" class="form-control" data-rule-required='true'>
                              <option value="">Select Employee/Staff Name</option>
                              @if(isset($employee))
                                  @foreach($employee as $key => $value)
                                    <option value="{{$value['user_id']}}">{{$value['first_name']}} {{$value['last_name']}}</option>
                                  @endforeach
                              @endif
                          </select>
                      </div>
               </div>
               <div class="form-group">
                      <label class="col-sm-3 col-lg-4 control-label">Select Role<i class="red">*</i></label>
                      <div class="col-sm-9 col-lg-8 controls">
                          <select name="role" class="form-control" data-rule-required='true'>
                              <option value="">Select Role</option>
                              @if(isset($role))
                                  @foreach($role as $key => $value)
                                    <option value="{{$value['slug']}}">{{$value['name']}}</option>
                                  @endforeach
                              @endif
                          </select>
                      </div>
               </div>
               </div>
             </div>
             </div>
              <div class="row">
               <div class="form-group">
                <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                   <a href="{{ url($school_admin_panel_slug.'/dashboard') }}" class="btn btn-primary">Back</a> 
                   <input type="submit" name="assign" value="Assign" class="btn btn-primary">
                </div>
              </div>
                </div>
                </div>
         </form>
      </div>
      </div>
   </div>
</div>
</div>  

<!-- END Main Content --> 
@endsection