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
                <a href="{{ $module_url_path }}/manage">{{ $module_title or ''}}</a>
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
        <h1><i class="{{$module_icon}}"></i>{{$module_title}}</h1>
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

            @include('schooladmin.layout._operation_status')  

			<div class="tabbable">
                <form class="form-horizontal" onsubmit="return addLoader()" id="validation-form1" method="POST" action="{{ $module_url_path }}/store">
                  {{ csrf_field() }}
                            <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label">{{translation('suggestion_subject')}}<i class="red">*</i></label>
                                <div class="col-sm-9 col-lg-4 controls">
                                    <input class="form-control" name="subject" type="text" placeholder="{{translation('enter')}} {{translation('suggestion_subject')}}" data-rule-required="true" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label">{{translation('suggestion_category')}} <i class="red">*</i></label>
                                <div class="col-sm-9 col-lg-4 controls">
                                    <select class="form-control" id="category" name="category" data-rule-required="true" >
                                      <option value="">{{translation('select_category')}}</option>
                                      @if(isset($arr_categories) && count($arr_categories)>0)
                                        @foreach($arr_categories as $key => $category)
                                          <option value="{{$category['id']}}">{{isset($category['category'])?ucwords($category['category']):''}}</option>
                                        @endforeach
                                      @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                               
                                <label class="col-sm-3 col-lg-2 control-label">{{translation('suggestion')}} {{translation('description')}}</label>
                                <div class="col-sm-9 col-lg-4 controls">
                                   <textarea name="description" id="description" cols="30" rows="4" class="form-control"></textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                                        <a href="javascript:void(0)" class="btn btn btn-primary">{{translation('back')}}</a>
                                          <input class="btn btn btn-primary" value="{{translation('save')}}" type="submit">
                                    </div>
                                </div>   
                            <div class="clearfix"></div>
                </form>
            </div>
    
        </div>
      </div>
    </div>
</div>
  <script>
    $(function () {
        $("#datepicker").datepicker({
            todayHighlight: true,
            autoclose: true,
        });
        $("#datepicker2").datepicker({
            autoclose: true,
            minDate : '05/28/2019',
        });
    });
</script>
<script>
  $("#datepicker2").on('blur',function(){
     
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
      
  });
  
</script>

@endsection                    
