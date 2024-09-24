@extends('admin.layout.master')                
@section('main_content')
<style type="text/css">
.fieldHeaders{
  font-weight:bold;  
}

</style>


<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
    <li>
      <i class="fa fa-home">
      </i>
      <a href="{{ url($admin_panel_slug.'/dashboard') }}">{{translation('dashboard')}}
      </a>
    </li>
    <span class="divider">
      <i class="fa fa-angle-right">
      </i>
      <i class="fa fa-info-circle faa-vertical animated-hover">
      </i>
      
      <a href="{{ $module_url_path }}" class="call_loader">{{ $module_title or ''}}
      </a>
    </span> 
    <span class="divider">
      <i class="fa fa-angle-right">
      </i>
      <i class="fa fa-eye">
      </i>
    </span> 
    <li class="active">   {{ $page_title or '' }}
    </li>
  </ul>
</div>
<!-- END Breadcrumb -->


<div class="page-title new-agetitle">
    <div>
        <h1><i class="fa fa-bell"></i> {{ isset($module_title)?$module_title:"" }}</h1>
    </div>
</div>
<!-- END Page Title -->

<!-- BEGIN Main Content -->
<div class="row">
  <div class="col-lg-12">
      
          <div class="box {{$theme_color}}">
           <div class="box-title">
             <h3>
                <i class="fa fa-eye"></i>
                {{ isset($page_title)?$page_title:"" }}
             </h3>
             <div class="box-tool">
                <a data-action="collapse" href="#"></a>
                <a data-action="close" href="#"></a>
             </div>
          </div>
          <div class="box-content view-details-seciton-main details-section-main-block">
     
    
             
                <br>
                    <table class="table table-bordered">
                      <tbody>
                            <tr>
                              <th style="width: 30%">{{translation('title')}}
                              </th>
                              <td>
                                 {{ isset($obj_arr_data['title']) && $obj_arr_data['title'] !=""  ?$obj_arr_data['title']:'-' }} 
                              </td>
                            </tr> 
                            <tr>
                              <th style="width: 30%">{{translation('type')}}
                              </th>
                              <td>
                                 {{ isset($obj_arr_data['notification_type']) && $obj_arr_data['notification_type'] !=""  ?$obj_arr_data['notification_type']:'-' }} 
                              </td>
                            </tr> 

                            <tr>
                              <th style="width: 30%">{{translation('email')}}
                              </th>
                              <td>
                                  {{ isset($obj_arr_data['user_details']['email']) && $obj_arr_data['user_details']['email'] !=""  ?$obj_arr_data['user_details']['email']:'-' }} 
                              </td>
                            </tr>

                            <tr>
                              <th style="width: 30%">{{translation('created_at')}} 
                              </th>
                              <td>
                                {{ isset($obj_arr_data['created_at']) && $obj_arr_data['created_at']!='0000-00-00 00:00:00'  ? getDateFormat($obj_arr_data['created_at']):'-' }}
                              
                              </td>
                            </tr>
                    </tbody>
                  </table>  
             
           
              <div class="form-group">
 <div class="col-sm-9 col-lg-12 controls">
  <a href="{{ $module_url_path }}" class="btn btn-primary" style="float: right;" > <i class="fa fa-arrow-left"></i> {{translation('back')}} </a>
</div>
</div>
</div>
          </div>
        </div>

  </div>
 
 
 
 


<script type="text/javascript">
    function scrollToButtom()
    {
        $("html, body").animate({ scrollTop: $(document).height() }, 1000);
    }

    $(document).ready(function()
    {
        $("#select_action").bind('change',function()
        {
            if($(this).val()=="cancel")
            {
                $("#reason_section").show();
            }
            else
            {
                $("#reason_section").hide();
            }
        });
    });
</script>
<!-- END Main Content -->
@stop

