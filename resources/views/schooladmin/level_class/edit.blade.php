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
            <i class="{{$edit_icon}}"></i>
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

<!-- BEGIN Main Content -->

<div class="row">
<div class="col-md-12">
   <div class="box box-navy_blue">
      <div class="box-title">
         <h3>
            <i class="fa {{$edit_icon}}">
            </i>{{ isset($page_title)?$page_title:"" }} 
         </h3>
         <div class="box-tool">
         </div>
      </div>
      <div class="box-content">
         @include('schooladmin.layout._operation_status')
            <form method="post" onsubmit="return addLoader()"  action="{{$module_url_path}}/update/{{base64_encode($id)}}"  class="form-horizontal" id="validation-form1" enctype ='multipart/form-data'>
              {{ csrf_field() }}

                  <div class="row">
                            <br/>
                            <div class="form-group">
                                    <label class="col-xs-12 col-sm-4 col-md-3 col-lg-3 control-label">{{translation('level')}}</label>
                                    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
                                      <div class="assignment-gray-main">
                                        {{-- <select class="form-control" name="level" data-rule-required='true'> --}}
                                          @if(isset($arr_level) && $arr_level != '')
                                              @foreach($arr_level as $key => $data)
                                                @if(isset($arr_school_level) && $arr_school_level != '')
                                                  @if($data['level_id'] == $arr_school_level['level_id'])
                                                      <input type="hidden" name="level" value="{{$data['level_id']}}">
                                                      <input type="text" value="{{$data['level_name']}}" readonly="true" class="form-control">
                                                  @endif
                                                @endif
                                              @endforeach
                                          @endif
                                        {{-- </select> --}}
                                    </div>
                                    </div>
                                </div>
                               <div class="form-group">
                                    <label class="col-xs-12 col-sm-4 col-md-3 col-lg-3 control-label">{{translation('class')}}</label>
                                    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
                                        <div class="assignment-gray-main">
                                            <select class="js-example-basic-multiple form-control " multiple="multiple" name="class[]" data-rule-required='true'>
                                                 @if(isset($arr_class) && $arr_class != '' )
                                                    @foreach($arr_class as $key => $data)
                                                      <option value="{{$data['class_id']}}" 
                                                          @if(isset($arr_school_class) && $arr_school_class != '' )
                                                            @foreach($arr_school_class as $class_key => $class)
                                                                @if($class['class_id'] == $data['class_id']) 
                                                                    selected 
                                                                @endif
                                                            @endforeach
                                                          @endif>
                                                        {{$data['class_name']}}
                                                      </option>
                                                    @endforeach
                                                @endif
                                             </select>
                                        </div>
                                    </div>
                                </div>
                          </div> 
      
                   <div class="form-group">
                    <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 controlS"></div>
                    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
                       <a href="{{ url($school_admin_panel_slug.'/level_class') }}" class="btn btn-primary">{{translation('back')}}</a> 
                       <input type="submit" name="update" value="{{translation('update')}}" class="btn btn-primary">
                    </div>
                  </div>
         </form>
      </div>
   </div>
</div>
</div>  
<script type="text/javascript">

         $(".js-example-basic-multiple").select2({
            placeholder:'   {{translation('select_class')}}'

         });
      </script> 

<!-- END Main Content --> 
@endsection