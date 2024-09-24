@extends('admin.layout.master')                
@section('main_content')
<!-- BEGIN Page Title -->
<link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">
<div class="page-title">
  <div>
  </div>
</div>
<!-- END Page Title -->
<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
    <li>
      <i class="fa fa-home"></i>
      <a href="{{ url($admin_panel_slug.'/dashboard') }}">{{translation('dashboard')}} </a>
    </li>
    <span class="divider">
      <i class="fa fa-angle-right"></i>
      <i class="fa fa-envelope"></i>   
    </span> 
    <li class="active"> {{ $module_title or ''}} </li>
   
  </ul>
</div>
    <!-- END Page Title -->



    <!-- BEGIN Main Content -->
    <div class="row">
      <div class="col-md-12">

          <div class="box">
            <div class="box-title">
              <h3>
                <i class="fa fa-list"></i>
                {{ isset($page_title)?$page_title:"" }}
            </h3>
            <div class="box-tool">
                <a data-action="collapse" href="#"></a>
                <a data-action="close" href="#"></a>
            </div>
        </div>
        <div class="box-content">
        
          @include('admin.layout._operation_status')  

          {!! Form::open([ 'url' => $module_url_path.'/multi_action',
                                 'method'=>'POST',
                                 'enctype' =>'multipart/form-data',   
                                 'class'=>'form-horizontal', 
                                 'id'=>'frm_manage' 
                                ]) !!} 

            {{ csrf_field() }}

          <div class="col-md-10">
            <div id="ajax_op_status"></div>
             <div class="alert alert-danger" id="no_select" style="display:none;"></div>
              <div class="alert alert-warning" id="warning_msg" style="display:none;"></div>
          </div>
          <div class="btn-toolbar pull-right clearfix">
            <div class="btn-group">
            @if(array_key_exists('enquiry_category.create', $arr_current_user_access))             
             <a href="{{ $module_url_path.'/create' }}" class="btn btn-primary btn-add-new-records padding-small" title="Add {{ str_singular($module_title) }}">Add {{ str_singular($module_title) }}</a> 
            @endif
          </div>
  

            <div class="btn-group space-btm-cnt"> 

               @if( array_key_exists('contact_enquiry.update', $arr_current_user_access)) 
                             
                <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" 
                   title="Multiple Delete" 
                   href="javascript:void(0);" 
                   onclick="javascript : return check_multi_action('frm_manage','delete');"  
                   style="text-decoration:none;">
                   <i class="fa fa-trash-o"></i>
                </a>
              @endif

                <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip refrash-btns" 
                   title="Refresh" 
                   href="javascript:void(0)"
                   onclick="javascript:location.reload();" 
                   style="text-decoration:none;">
                   <i class="fa fa-repeat"></i>
                </a> 
            </div>
          </div>
          <br/>
          <div class="clearfix"></div>
          <div class="table-responsive" style="border:0">

            <input type="hidden" name="multi_action" value="" />

            <table class="table table-advance"  id="table4" >
              <thead>
                <tr>
                  
                  @if( array_key_exists('contact_enquiry.update', $arr_current_user_access)) 
                  <th style="width:18px">
                    <div class="check-box top-space-mr">
                        <input type="checkbox" class="filled-in" value="delete"  name="mult_change"  id="mult_change">
                        <label for="mult_change"></label>
                    </div>
                   </th>
                  @endif
                  <th>Sr no</th>
                  <th>Subject</th> 
                
                  @if(array_key_exists('contact_enquiry.list', $arr_current_user_access) || array_key_exists('contact_enquiry.delete', $arr_current_user_access) ) 
                   <th>Action</th>
                  @endif
                </tr>
              </thead>
              <tbody>
                @if(sizeof($arr_enquiry_category)>0)
                  @foreach($arr_enquiry_category as $key=> $contact_enquiry)
              
                  <tr>
                    @if( array_key_exists('contact_enquiry.update', $arr_current_user_access)) 
                      <td>
                       
                        <div class="check-box top-space-mr">
                                <input type="checkbox" class="filled-in" value="{{ base64_encode($contact_enquiry['id']) }}"  name="checked_record[]" id="mult_changecheck">
                                <label for="mult_changecheck"></label>
                            </div>
                          
                      </td>
                    @endif
                    <td > {{ $key+1 }} </td> 
                    <td > {{ $contact_enquiry['title'] }} </td>  
                              
                    <td> 
                   
                       @if(array_key_exists('contact_enquiry.list', $arr_current_user_access))                   
                        <a target="_blank" class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" href="{{ $module_url_path.'/view/'.base64_encode($contact_enquiry['id']) }}" 
                        title="View">
                        <i class="fa fa-edit" ></i>
                        </a>
                        
                        @endif

                        &nbsp;  
                        @if(array_key_exists('contact_enquiry.delete', $arr_current_user_access))                   
                        <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip btn-delets" href="{{ $module_url_path.'/delete/'.base64_encode($contact_enquiry['id'])}}" 
                        onclick="return confirm_action(this,event,'Do you really want to delete this record ?')"
                        title="Delete">
                        <i class="fa fa-trash" ></i>
                        </a>
                        @endif

                    </td>
                  </tr>
                  @endforeach
                @endif
                 
              </tbody>
            </table>
          </div>
        <div> </div>
         
          {!! Form::close() !!}
      </div>
  </div>
</div>


<!-- END Main Content -->
<script type="text/javascript">
$(document).ready(function() {
 var oTable = $('#table4').dataTable({
  "aoColumnDefs": [
                  { 
                    "bSortable": false, 
                    "aTargets": [0,1,3,4,5,6] // <-- gets last column and turns off sorting
                   } 
                ]
    "ordering":false            
    });        
 
});
</script>

@stop                    


