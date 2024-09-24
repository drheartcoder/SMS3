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
          <a href="{{$module_url_path}}/manage_new_classes">{{$module_title}}</a>
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
        <h1>{{$module_title}}</h1>

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
            <form method="post" onsubmit="return addLoader()"  action="{{$module_url_path}}/store_class"  class="form-horizontal" id="validation-form1" enctype ='multipart/form-data'>
              {{ csrf_field() }}

                      
                      <br>
                          <div class="row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="form-group">
                                    <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('class_name')}}</label>
                                    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 controls">
                                        <input type="text" name="class_name" pattern="^[a-zA-Z0-9 ]+$" value="{{old('class_name')}}" data-rule-required='true' class="form-control" placeholder="{{translation('enter_class_name')}}">
                                        <span class='help-block'>{{ $errors->first('class_name')}}</span>                             
                                    </div>

                                    </div>
                                </div>
                            </div>                            
                          </div>
               
                      
                   
            <div class="row">
           <div class="form-group">            
            <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 controls">
               <a href="{{ url($school_admin_panel_slug.'/level_class/manage_new_classes') }}" class="btn btn-primary">{{translation('back')}}</a> 
              <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('save')}}</button>
            </div>
          </div>
                </div>
         </form>
      </div>
      </div>
   </div>
</div>
</div>  

 <!--multi selection-->
      
      <script type="text/javascript">
         $(".js-example-basic-multiple").select2();
         /* $('#example-getting-started').multiselect();  */
            
      </script> 
      <script>
        $('#class').on('change',function(){
          var  class_name = $('#class').val();
          //alert(class_name);
          var array =class_name.split(',');
          alert(array);
        });
      </script>

<!-- END Main Content --> 
@endsection