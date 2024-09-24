@extends('admin.layout.master')                
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="{{url('/')}}/admin/dashboard">{{translation('dashboard')}}</a>
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
        <h1><i class="{{$module_icon}}"></i>{{str_plural($module_title)}}</h1>

    </div>
</div>
<!-- END Page Title -->

            <!-- BEGIN Tiles -->
            <div class="row">
                <div class="col-md-12">

                    <div class="box  box-navy_blue">
                        <div class="box-title">
                            <h3><i class="{{$create_icon}}"></i>{{$page_title}}</h3>
                            <div class="box-tool">
                            </div>
                        </div>  
                        <div class="box-content">
                           @include('admin.layout._operation_status')
                            <form method="post" onsubmit="return addLoader()"  action="{{$module_url_path}}/store"  class="form-horizontal" id="validation-form1">
                                {{ csrf_field() }}

                                <div class="form-group">
                                    <label class="col-sm-3 col-lg-2 control-label">{{translation('role')}}<i class="red">*</i></label>
                                    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
                                          <div class="frmSearch">
                                            <input type="text" name="role" id="search-box" class="form-control" data-rule-required='true' placeholder="{{translation('role_name')}}" pattern="[a-zA-Z0-9 àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$"  value="{{old('role')}}" />
                                            <div class="suggestion-box" id="suggesstion-box"></div>
                                          </div>
                                        <span class="help-block">{{ $errors->first('role') }}</span>
                                    </div>
                                </div>
                                
                              <div class="form-group">
                                <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                                    <a href="{{$module_url_path}}" class="btn btn-primary">{{translation('back')}}</a>
                                    <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('save')}}</button>
                                </div>
                              </div>  
                            </form>
                        </div>
                    </div>
                </div>
              </div>
 <script type="text/javascript">
  
  var glob_fields_modified = false;

  function selectAll(ref)
  {
    var action = $(ref).attr('data-module-action');

    var is_checked = $(ref).is(":checked");

    var arr_input = $('input[data-module-action-ref="'+action+'"]');  

    if(is_checked)
    {
      $.each(arr_input,function(index,elem)
      {
        $(elem).prop('checked', true);
      });  
    }
    else
    {
      
      $.each(arr_input,function(index,elem)
      {
        $(elem).prop('checked', false);
      });   
    }
    
  }
  $(document).ready(function(){


  $("#search-box").keyup(function(){
      var key = $("#search-box").val();
    $.ajax({
    type: "get",
    url: "{{$module_url_path.'/'.'get_roles?keyword='}}"+key,
    data:'keyword='+$(this).val(),
    beforeSend: function(){
      $("#search-box").css("background","#FFF url(LoaderIcon.gif) no-repeat 165px");
    },
    success: function(data){
      $("#suggesstion-box").show();
      $("#suggesstion-box").html(data);
      $("#search-box").css("background","#FFF");
    }
    });
  });
});
//To select country name
function selectCountry(val) {
$("#search-box").val(val);
$("#suggesstion-box").hide();
}
</script>                
@endsection