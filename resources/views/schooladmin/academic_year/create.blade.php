@extends('schooladmin.layout.master')                
@section('main_content')

    <!-- BEGIN Breadcrumb -->
    <div id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url($school_admin_panel_slug.'/dashboard') }}">{{ translation('dashboard') }}</a>
            </li>
            <span class="divider">
                <i class="fa fa-angle-right"></i>
                <i class="fa fa-book"></i>
                <a href="{{ $module_url_path }}">{{ $module_title or ''}}</a>
            </span> 
            <span class="divider">
                <i class="fa fa-angle-right"></i>
                  <i class="fa fa-plus"></i>
            </span>
            <li class="active">{{ $page_title or ''}}</li>
        </ul>
    </div>
    <!-- END Breadcrumb -->

    <!-- BEGIN Page Title -->
    <div class="page-title new-agetitle">
    <div>
        <h1><i class="fa fa-calendar"></i>{{$module_title}}</h1>
    </div>
</div>
    <!-- END Page Title -->

    
    <!-- BEGIN Main Content -->
    <div class="row">
      <div class="col-md-12">

          <div class="box">
            <div class="box-title">
              <h3>
                <i class="fa fa-plus-circle"></i>
                {{ isset($page_title)?$page_title:"" }}
            </h3>
            <div class="box-tool">
               
            </div>
        </div>
        <div class="box-content edit-space">

            @include('admin.layout._operation_status')  

			<div class="tabbable">
                <form class="form-horizontal" onsubmit="return addLoader()" id="validation-form1" method="POST" action="{{ $module_url_path }}/store">
                  {{ csrf_field() }}
                   <div class="row">
                      <div class="col-sm-12 col-md-12 col-lg-6">
                        <div class="form-group">
                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label" for="state"> 
                                  {{translation('start_date')}}<label style="color: red">*</label>
                               </label>
                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                                    <input type="text" name="start_date" data-rule-required='true' data-rule-date="true" id="datepicker" value="{{old('start_date')}}" class="form-control datepikr" placeholder="{{translation('select')}} {{translation('start_date')}}" data-rule-date="true" style="cursor: pointer;" readonly>
                                    <span class='help-block'>{{ $errors->first('start_date') }}</span> 
                                    <span id='err_exam_type' style="color: red"></span> 
                              </div>

                        </div>
                        <div class="form-group">
                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label" for="state"> 
                                  {{translation('end_date')}}<label style="color: red">*</label>
                               </label>
                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                                    <input type="text" name="end_date" data-rule-required='true'  data-rule-date="true" id="datepicker2" value="{{old('end_date')}}" class="form-control datepikr" placeholder="{{translation('select')}} {{translation('end_date')}}" data-rule-date="true"  style="cursor: pointer;" readonly>
                                    <span class='help-block'>{{ $errors->first('end_date') }}</span> <br/>
                                    <span id='err_end_date' style="color: red"></span> 
                              </div>

                        </div>
                        <input type="hidden" name="year" id="year">
                        <div class="form-group">
                              <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4"></div>
                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                                  <a href="{{$module_url_path}}" class="btn btn-primary">{{translation('back')}}</a>
                                  <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('save')}}</button>
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

<?php
$redirect = \Request::has('type')&&Request::get('type')!=''?\Request::get('type'):'';
?>
  <script>
    $(function () {
        $("#datepicker").datepicker({
            todayHighlight: true,
            autoclose: true
        });
        $("#datepicker2").datepicker({
            autoclose: true,
            
        });
    });
    @if($redirect!='')
    $(document).ready(function() {
      var URL ='';
        @if($redirect=='school')
          URL = 'school';
        @elseif($redirect=='dashboard')
          URL = 'dashboard';
        @endif

        if(URL!=''){
          window.location = '{{url('/')}}/school_admin/'+URL;
        }

        
    });
    @endif
</script>
<script>
  /*$("#datepicker2").on('blur',function(){
     
      var year='';
      var startDate = new Date($('#datepicker').val());

      var endDate = new Date($('#datepicker2').val());

      if(startDate >= endDate)
      {
        $('#err_end_date').text('End date must be greater than start date');
      }
      else
      {
        $('#err_end_date').text(''); 
      }
      
  });

  $("#datepicker").on('blur',function(){
     
      var year='';
      var startDate = new Date($('#datepicker').val());

      var endDate = new Date($('#datepicker2').val());

      if(startDate >= endDate)
      {
        $('#err_end_date').text('End date must be greater than start date');
      }
      else
      {
        $('#err_end_date').text(''); 
        

      }
      
  });*/
  
</script>

@endsection                    
