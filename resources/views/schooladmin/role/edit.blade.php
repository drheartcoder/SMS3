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
            <i class="{{$edit_icon}}"></i>
        </span>
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
                            <h3><i class="{{$edit_icon}}"></i>{{$page_title}}</h3>
                            <div class="box-tool">
                            </div>
                        </div>  
                        <div class="box-content">
                           @include('admin.layout._operation_status')
                            <form method="post" action="{{$module_url_path}}/update/{{base64_encode($arr_data['id'])}}"  class="form-horizontal" id="validation-form1">
                                {{ csrf_field() }}

                                <div class="form-group">
                                    <label class="col-sm-3 col-lg-2 control-label">{{translation('role')}}<i class="red">*</i></label>
                                    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-4 controls">
                                          <div class="frmSearch">
                                            <input type="text" name="role" id="search-box" class="form-control" data-rule-required='true' placeholder="{{translation('role_name')}}" value="{{$arr_data['role_details']['name']}}" @if($arr_data['role_details']['is_approved']!="PENDING") readonly @endif/>
                                            <div id="suggesstion-box"></div>
                                          </div>
                                        <span class="help-block"></span>
                                    </div>
                                </div>

                               

                                @if(isset($arr_modules) && sizeof($arr_modules)>0)
                                <div class="form-group">
                                  <label class="col-sm-3 col-lg-2 control-label">{{translation('permissions')}} <i class="red">*</i></label>
                                  <div class="col-sm-9 col-lg-8 controls" >
                                        <div class="table-responsive" style="border:0">
                                            <table class="table border-tebls" id="table_module">
                                                <thead>
                                                  <tr>
                                                      <th style="width:5%;"></th> 
                                                      <th style="text-align:center;width:5%;">{{translation('list')}} / {{translation('view')}}</th>
                                                      <th style="text-align:center;width:5%;">{{translation('create')}}</th>
                                                      <th style="text-align:center;width:5%;">{{translation('update')}}/{{translation('multiple_action')}}</th>
                                                      <th style="text-align:center;width:5%;">{{translation('delete')}}</th>
                                                  </tr>
                                                  </tr>
                                                </thead>
                                                <tbody>
                                                  <tr class="info">
                                                          <td><b>All</b></td>
                                                          <td class="text-center">
                                                              <div class="check-box">
                                                               <input id="filled-in-box" class="filled-in" type="checkbox" data-module-action="List" onclick="selectAll(this)">
                                                                <label for="filled-in-box"></label>
                                                              </div>
                                                           </td>
                                                          <td class="text-center"> 
                                                          <div class="check-box">
                                                           <input id="filled-in-box1" class="filled-in"  type="checkbox" data-module-action="Create" onclick="selectAll(this)">
                                                           <label for="filled-in-box1"></label>
                                                           </div>
                                                           </td>
                                                          <td class="text-center"> 
                                                          <div class="check-box">
                                                          <input id="filled-in-box2" class="filled-in"  type="checkbox" data-module-action="Update" onclick="selectAll(this)">
                                                           <label for="filled-in-box2"></label>
                                                           </div>
                                                          </td>
                                                          <td class="text-center"> 
                                                          <div class="check-box">
                                                           <input id="filled-in-box3" class="filled-in"  type="checkbox" data-module-action="Delete" onclick="selectAll(this)">
                                                           <label for="filled-in-box3"></label>
                                                           </div>
                                                            </td>
                                                  </tr>     
                                      
                                                
                                                 @foreach($arr_modules as $key => $row )
                                                  <tr class="info">
                                                      <?php $slug = $row['get_modules']['slug'];  
                                                            $permissions = json_decode($arr_data['permissions'],true);  
                                                          
                                                         ?>
                                                      @if($row['get_modules']['slug'] == 'change_password' || $row['get_modules']['slug'] =='account_settings')
                                                        <td class="text-center"> 
                                                             <input  type="hidden"  name="arr_permisssion[subadmin][{{$slug}}.update]"   value="true">
                                                            </td>
                                                      @else
                                                      <td><b>{{$row['get_modules']['title']}}</b></td>     

                                                            <td class="text-center">
                                                              <div class="check-box"> 
                                                             <input id="filled-in-box4{{$key}}" class="filled-in" type="checkbox" data-module-ref="{{$slug}}" data-module-action-ref="List" name="arr_permisssion[subadmin][{{$slug}}.list]"  @if(array_key_exists($slug.'.list',$permissions)) checked @endif value="true">
                                                           <label for="filled-in-box4{{$key}}"></label>
                                                           </div>
                                                            </td>
                                                                                                                          
                                                            
                                                            <td class="text-center">
                                                            <div class="check-box">
                                                            <input id="filled-in-box5{{$key}}" class="filled-in" type="checkbox" data-module-ref="{{$slug}}" data-module-action-ref="Create" name="arr_permisssion[subadmin][{{$slug}}.create]" @if(array_key_exists($slug.'.create',$permissions)) checked @endif value="true">
                                                           <label for="filled-in-box5{{$key}}"></label>
                                                           </div>
                                                            </td>
                                                                                                                          
                                                            
                                                            <td class="text-center"> 
                                                            <div class="check-box">
                                                            <input id="filled-in-box6{{$key}}" class="filled-in" type="checkbox" data-module-ref="{{$slug}}" data-module-action-ref="Update" name="arr_permisssion[subadmin][{{$slug}}.update]" @if(array_key_exists($slug.'.update',$permissions)) checked @endif value="true">
                                                            <label for="filled-in-box6{{$key}}"></label>
                                                            </div>
                                                            </td>                                               
                                                                           
                                                            
                                                            <td class="text-center"> 
                                                            <div class="check-box">
                                                            <input id="filled-in-box7{{$key}}" class="filled-in" type="checkbox" data-module-ref="{{$slug}}" data-module-action-ref="Delete" name="arr_permisssion[subadmin][{{$slug}}.delete]" @if(array_key_exists($slug.'.delete',$permissions)) checked @endif value="true">
                                                            <label for="filled-in-box7{{$key}}"></label>
                                                            </div>
                                                            </td>
                                                    @endif        
                                                  </tr>
                                                  @endforeach
                                                
                                              </tbody>
                                            </table>
                                  
                                        </div>
                                    </div>
                                </div>
                                @endif
                              <div class="form-group">
                                <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                                    <a href="{{$module_url_path}}" class="btn btn btn-primary">{{translation('back')}}</a> 
                                    <input type="submit" name="Save" class="btn btn btn-primary" value="{{translation('update')}}">
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