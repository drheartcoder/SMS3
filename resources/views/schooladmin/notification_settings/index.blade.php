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
            <i class="{{$edit_icon}}"></i>
        </span>
        <li class="active">{{$page_title}}</li>
    </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="{{$module_icon}}"></i>{{$page_title}}</h1>

    </div>
</div>
<!-- END Page Title -->

            <!-- BEGIN Tiles -->
            <div class="row">
                <div class="col-md-12">

                    <div class="box  box-navy_blue">
                        <div class="box-title">
                            <h3><i class="fa fa-plus-circle"></i>{{$page_title}}</h3>
                            <div class="box-tool">
                            </div>
                        </div>  
                        <div class="box-content">
                           @include('admin.layout._operation_status')
                            <form method="post" action="{{$module_url_path}}/store"  class="form-horizontal" id="validation-form1" onsubmit ="return checkAddedPermission();">
                                {{ csrf_field() }}

                                <input type="hidden" name="user_id" value="{{$arr_data['user_id'] or ''}}">
                                <input type="hidden" name="id" value="{{$arr_data['id']  or '' }}">

                               @if(isset($arr_modules) && sizeof($arr_modules)>0)
                                <div class="form-group">
                                  <label class="col-sm-3 col-lg-2 control-label"><!-- {{translation('permissions')}} <i class="red">*</i> --></label>
                                  <div class="col-sm-9 col-lg-8 controls" >
                                        <div class="table-responsive" style="border:0">
                                            <table class="table border-tebls" id="table_module">
                                                <thead>
                                                  <tr>
                                                      <th style="width:5%;"></th> 
                                                      <th style="text-align:center;width:5%;">SMS</th>
                                                      <th style="text-align:center;width:5%;">Email</th>
                                                      <th style="text-align:center;width:5%;">App</th>
                                                      
                                                  </tr>
                                                </thead>
                                                <tbody>
                                                  <tr class="info">
                                                          <td><b>All</b></td>
                                                          @if(array_key_exists('notification_settings.update', $arr_current_user_access))
                                                          <td class="text-center">
                                                              <div class="check-box">
                                                               <input id="filled-in-box" class="filled-in" type="checkbox" data-module-action="sms" onclick="selectAll(this)">
                                                                <label for="filled-in-box"></label>
                                                              </div>  
                                                           </td>
                                                          <td class="text-center"> 
                                                          <div class="check-box">
                                                           <input id="filled-in-box1" class="filled-in" type="checkbox" data-module-action="email" onclick="selectAll(this)">
                                                           <label for="filled-in-box1"></label>
                                                           </div>
                                                           </td>
                                                          <td class="text-center">
                                                          <div class="check-box"> 
                                                          <input id="filled-in-box2" class="filled-in" type="checkbox" data-module-action="app" onclick="selectAll(this)">
                                                           <label for="filled-in-box2"></label>
                                                           </div>
                                                          </td>
                                                          @else
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                          @endif
                                                  </tr>     
                                                  
                                              @if(!empty($arr_modules))
                                                @foreach($arr_modules as $key => $row )
                                                 
                                                 
                                                  <tr class="info">
                                                      <?php $slug = $row['module_title']; 
                                                            $permissions  = [];
                                                            if(isset($arr_data['notification_permission']) && $arr_data['notification_permission']!='' ){
                                                              $permissions = json_decode($arr_data['notification_permission'],true);  
                                                            }
                                                          
                                                         ?>
                                                      
                                                      <td><b>{{translation($row['module_title'])}}</b></td>     

                                                            <td class="text-center">
                                                              <div class="check-box">  
                                                              <input id="filled-in-box4{{$key}}" class="filled-in moduleWiseCheckboxSelection" type="checkbox" data-module-ref="{{$slug}}" data-module-action-ref="sms" name="arr_permisssion[notification][{{$slug}}.sms]"  @if(array_key_exists($slug.'.sms',$permissions)) checked @endif value="true" @if(!array_key_exists('notification_settings.update', $arr_current_user_access)) disabled @endif>
                                                              <label for="filled-in-box4{{$key}}"></label>
                                                              </div>
                                                            </td>
                                                                                                                          
                                                            
                                                            <td class="text-center">
                                                            <div class="check-box">  
                                                              <input id="filled-in-box5{{$key}}" class="filled-in moduleWiseCheckboxSelection" type="checkbox" data-module-ref="{{$slug}}" data-module-action-ref="email" name="arr_permisssion[notification][{{$slug}}.email]" @if(array_key_exists($slug.'.email',$permissions)) checked @endif value="true" @if(!array_key_exists('notification_settings.update', $arr_current_user_access)) disabled @endif>
                                                              <label for="filled-in-box5{{$key}}"></label>
                                                            </div>  
                                                            </td>
                                                                                                                          
                                                            
                                                            <td class="text-center"> 
                                                            <div class="check-box">  
                                                              <input id="filled-in-box6{{$key}}" class="filled-in moduleWiseCheckboxSelection" type="checkbox" data-module-ref="{{$slug}}" data-module-action-ref="app" name="arr_permisssion[notification][{{$slug}}.app]" @if(array_key_exists($slug.'.app',$permissions)) checked @endif value="true" @if(!array_key_exists('notification_settings.update', $arr_current_user_access)) disabled @endif>
                                                              <label for="filled-in-box6{{$key}}"></label>
                                                              </div>
                                                            </td>                                               
                                                        
                                                  </tr>
                                                  
                                                  @endforeach
                                                @endif
                                                <label class="help-block error-permission red" style="display:block;"></label>
                                              </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                @endif
                              @if(array_key_exists('notification_settings.update', $arr_current_user_access))
                              <div class="form-group">
                                <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                                    <!-- <a href="{{$module_url_path}}" class="btn btn btn-primary">{{translation('back')}}</a>  -->
                                    <button  type="submit" id="submit_button"  class="btn btn btn-primary">{{translation('update')}}</button>
                                </div>
                              </div>
                              @endif  
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



function checkAddedPermission()
{
    
    //var len = $('input[name="arr_permisssion[moderator][]"]:checked').length;
    var is_checked = $('.moduleWiseCheckboxSelection:checked').length;
    
    if(is_checked<=0)
    {
      //  swal("Oops..","Please select the record to perform this Action.");
      $('.error-permission').show('');
      $('.error-permission').html('Please select the permission');
      return false;
    }
    else
    {
        $('.error-permission').html('');
        addLoader();
        return true;
    }
    
}



  $(document).ready(function(){


   
});
 
</script>                
@endsection