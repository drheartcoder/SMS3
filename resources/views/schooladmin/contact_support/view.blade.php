@extends('schooladmin.layout.master')                
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
      <a href="{{ url($school_admin_panel_slug.'/dashboard') }}">{{translation('dashboard')}}
      </a>
    </li>
    <span class="divider">
      <i class="fa fa-angle-right">
      </i>
      <i class="fa fa-info-circle">
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
 
 <!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
  <div>
  <h1><i class="fa fa-info-circle"></i> {{ isset($module_title)?$module_title:"" }}</h1>
  </div>
</div>
<!-- END Page Title -->


<!-- BEGIN Main Content -->
<div class="row">
  <div class="col-lg-12">
      
          <div class="box">
          <div class="box-title">
             <h3><i class="fa fa-eye"></i> {{ isset($page_title)?$page_title:"" }}</h3>
             <div class="box-tool">
             </div>
          </div>
          <div class="box-content view-details-seciton-main details-section-main-block">
            <div class="row">
                <div class="col-md-12">                
                
                <div class="details-infor-section-block">
                    {{translation('enquiry_details')}}
                </div>
                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('enquiry_category')}} </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{ isset($arr_contact_enquiry['enquiry_category']['category_name']) && $arr_contact_enquiry['enquiry_category']['category_name'] !=""  ?$arr_contact_enquiry['enquiry_category']['category_name']:'-' }} 
                     </div>
                     <div class="clearfix"></div>
                  </div>
                  <?php 
                    
                    $first_name = isset($arr_contact_enquiry['get_user']['first_name']) ? ucfirst($arr_contact_enquiry['get_user']['first_name']) : '';
                    $last_name  = isset($arr_contact_enquiry['get_user']['last_name']) ? ucfirst($arr_contact_enquiry['get_user']['last_name']) : '';

                    $name = $first_name.' '.$last_name;
                  ?>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('name')}} </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{ $name}} 
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('enquiry_number')}} </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{ isset($arr_contact_enquiry['enquiry_no']) && $arr_contact_enquiry['enquiry_no'] !=""  ?$arr_contact_enquiry['enquiry_no']:'-' }} 
                     </div>
                     <div class="clearfix"></div>
                  </div>
                  
                  
                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('subject')}}</b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{ isset($arr_contact_enquiry['subject']) && $arr_contact_enquiry['subject'] !=""  ?$arr_contact_enquiry['subject']:'-' }} 
                     </div>
                     <div class="clearfix"></div>
                  </div>
                  
                  
                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('user_email')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{ isset($arr_contact_enquiry['email']) && $arr_contact_enquiry['email'] !=""  ?$arr_contact_enquiry['email']:'-' }} 
                     </div>
                     <div class="clearfix"></div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('contact_number')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{ isset($arr_contact_enquiry['contact_number']) && $arr_contact_enquiry['contact_number'] !=""  ?$arr_contact_enquiry['contact_number']:'-' }} 
                     </div>
                     <div class="clearfix"></div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('message')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{ isset($arr_contact_enquiry['description']) && $arr_contact_enquiry['description'] !=""  ?$arr_contact_enquiry['description']:'-' }} 
                     </div>
                     <div class="clearfix"></div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('reply')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{ isset($arr_contact_enquiry['comments']) && $arr_contact_enquiry['comments'] !=""  ?$arr_contact_enquiry['comments']:'-' }} 
                     </div>
                     <div class="clearfix"></div>
                  </div>
                </div> 
                   
              </div>
              <div class="form-group">
                     <div class="col-sm-9 col-lg-12 controls">
                        <a href="{{ $module_url_path }}" class="btn btn-primary" style="float: right;" > <i class="fa fa-arrow-left"></i> {{translation('back')}} </a><div class="clearfix"></div>
                     </div><div class="clearfix"></div>
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

