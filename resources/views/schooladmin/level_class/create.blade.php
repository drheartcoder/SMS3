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
            <i class="fa {{$create_icon}}">
            </i>{{ isset($page_title)?$page_title:"" }} 
         </h3>
         <div class="box-tool">
         </div>
      </div>
      <div class="box-content">
         @include('schooladmin.layout._operation_status')
        
            <form method="post" onsubmit="return addLoader()"  action="{{$module_url_path}}/store"  class="form-horizontal" id="validation-form1" enctype ='multipart/form-data'>
              {{ csrf_field() }}

                          <div class="row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="form-group">
                                    <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('level')}}</label>
                                    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">
                                      <div class="assignment-gray-main">
                                        <select class="form-control" name="level" data-rule-required='true' id="level">
                                          @if(isset($arr_data['level']) && $arr_data['level'] != '')
                                              <option value="">{{translation('select_level')}}</option>
                                              @foreach($arr_data['level'] as $key => $data)
                                                <option value="{{$data['level_id']}}">{{$data['level_name']}}</option>
                                              @endforeach
                                          @endif
                                        </select>
                                        <span id="err_level" style="display: none; color: red">{{ $errors->first('level')}}</span>
                                    </div>
                                    </div>
                                </div>
                               <div class="form-group">
                                    <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('class')}}</label>
                                    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">
                                        <div class="assignment-gray-main">
                                            <select class="js-example-basic-multiple form-control " multiple="multiple" name="class[]" data-rule-required='true'>
                                                 @if(isset($arr_data['class']) && $arr_data['class'] != '')
                                                    @foreach($arr_data['class'] as $key => $data)
                                                      <option value="{{$data['class_id']}}">{{$data['class_name']}}</option>
                                                    @endforeach
                                                @endif
                                             </select>
                                             <span class='help-block'>{{ $errors->first('class_name')}}</span>
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </div>                            
                          </div>  
                        <div class="row">
                          <div class="form-group">                            
                            <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 controls">
                               <a href="{{ url($school_admin_panel_slug.'/level_class') }}" class="btn btn-primary">{{translation('back')}}</a> 
                               <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('save')}}</button>
                               
                            </div>
                          </div>                    
              </div>

      
           
         </form>
      </div>
      
   </div>
</div>
</div>  

 <!--multi selection-->
      
      <script type="text/javascript">

         $(".js-example-basic-multiple").select2({
            placeholder:'   {{translation('select_class')}}'

         });
      </script> 

      <script>
        $('#level').on('change',function(){
          var val = $(this).val();
          /*alert(val);*/
          if(val != '')
          {
           $.ajax({
                  url  :"{{ $module_url_path }}/checkLevel",
                  type :'POST',
                  data :{'level':val ,'_token':'<?php echo csrf_token();?>'},
                  success:function(data){
                    if(data.status=='success')
                      {
                        $('#err_level').text();
                        $('#err_level').css('display','none');
                      }
                      if(data.status=='error')
                      {
                        $('#err_level').show();
                        $('#err_level').text('This level is already assigned');
                      }
                  }
                });
          }
        });

        
      </script>
<!-- END Main Content --> 
@endsection