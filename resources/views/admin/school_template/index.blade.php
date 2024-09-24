@extends('admin.layout.master')                
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
      <li>
          <i class="fa fa-home"></i>
          <a href="{{ url($admin_panel_slug.'/dashboard') }}">{{translation('dashboard')}}</a>
      </li>
      <span class="divider">
          <i class="fa fa-angle-right"></i>
          <i class="{{$module_icon}}"></i>                
      </span> 
      <li class="active">{{ isset($module_title)?$module_title:"" }}</li>
  </ul>
</div>
<!-- END Breadcrumb -->


<!-- BEGIN Page Title -->
<link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">
<div class="page-title new-agetitle">
        <div>
            <h1><i class="{{$module_icon}}"></i> {{ isset($module_title)?$module_title:"" }}</h1>
        </div>
    </div>
<!-- END Page Title -->


<!-- BEGIN Main Content -->
<div class="row">
<div class="col-md-12">
   <div class="box {{ $theme_color }}">
      <div class="box-title">
         <h3>
            <i class="fa fa-list"></i>
            {{ isset($page_title)?$page_title:"" }}
         </h3>
         <div class="box-tool">
            @if(array_key_exists('school_template.create', $arr_current_user_access))     
<!--            <div class="btn-group">-->
               <a href="{{ $module_url_path.'/create'}}">{{translation('add')}} {{ str_singular($module_title) }}</a> 
<!--            </div>-->
            @endif  
            @if(array_key_exists('school_template.update', $arr_current_user_access))     
               <a title="{{translation('multiple_activeunblock')}}" 
                  href="javascript:void(0);" 
                  onclick='javascript : return check_multi_action("frm_manage","{{translation('are_you_sure')}}","{{translation('do_you_really_want_to_activate_this_record').'?'}}","{{translation('yes')}}","{{translation('no')}}","{{translation('oops')}}","{{translation('please_select_the_record_to_perform_this_action')}}","activate");' 
                  style="text-decoration:none;">
               <i class="fa fa-unlock"></i>
               </a> 
               <a
                  title="{{translation('multiple_deactiveblock')}}" 
                  href="javascript:void(0);" 
                  onclick='javascript : return check_multi_action("frm_manage","{{translation('are_you_sure')}}","{{translation('do_you_really_want_to_deactivate_this_record').'?'}}","{{translation('yes')}}","{{translation('no')}}","{{translation('oops')}}","{{translation('please_select_the_record_to_perform_this_action')}}","deactivate");'
                  style="text-decoration:none;">
               <i class="fa fa-lock"></i>
               </a> 
               @endif
               @if(array_key_exists('school_template.delete', $arr_current_user_access))
              
              <a
                 title="{{translation('multiple_delete')}}" 
                 href="javascript:void(0);" 
                 onclick='javascript : return check_multi_action("frm_manage","{{translation('are_you_sure')}}","{{translation('do_you_really_want_to_delete_this_record').'?'}}","{{translation('yes')}}","{{translation('no')}}","{{translation('oops')}}","{{translation('please_select_the_record_to_perform_this_action')}}","delete");'
                 style="text-decoration:none;">
                 <i class="fa fa-trash-o"></i>
              </a>
              
              @endif
            
            
               <a 
                  title="{{translation('refresh')}}" 
                  href="{{ $module_url_path }}"
                  style="text-decoration:none;">
               <i class="fa fa-repeat"></i>
               </a> 
            
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
            <div id="ajax_op_status">
            </div>
            <div class="alert alert-danger" id="no_select" style="display:none;"></div>
            <div class="alert alert-warning" id="warning_msg" style="display:none;"></div>
         </div>

         <br/>
         <div class="clearfix"></div>
         <div class="table-responsive" style="border:0">
            <input type="hidden" name="multi_action" value="" />
            
            <table class="table table-advance"  id="table_module">
               <thead>
                  <tr>
                    
                     @if(array_key_exists('school_template.update', $arr_current_user_access) || array_key_exists('school_template.delete', $arr_current_user_access) )                             
                     <th style="width: 18px; vertical-align: initial;">
                        <div class="check-box">
                            <input type="checkbox" class="filled-in" name="selectall" id="selectall" />
                            <label for="selectall"></label>
                        </div>
                     </th>
                     @endif
                      <th>{{translation('sr_no')}}</th> 
                     <th><a class="sort-descs" href="#">{{translation('question_category')}}</a>
                        
                     </th>
                     <th><a class="sort-descs" href="#">{{translation('title')}}</a>
                       
                     </th>
                     <th><a class="sort-descs" href="#">{{translation('options')}}</a>
                       
                     </th>
                     <th><a class="sort-descs" href="#">{{translation('is_required')}}</a>
                        
                     </th>
                     @if(array_key_exists('school_template.update', $arr_current_user_access) || array_key_exists('school_template.delete', $arr_current_user_access) )
                      <th><a class="sort-descs" href="#">{{translation('action')}}</a>       </th>
                     @endif
                  </tr>
               </thead>
               <tbody id="table_body">
                   @if(isset($arr_template) && count($arr_template)>0)
                      @foreach($arr_template as $key=>$template)
                        <tr id="listItem_{{ $template['id'] }}">
                          
                          <td>
                            @if(array_key_exists('school_template.update', $arr_current_user_access) || array_key_exists('school_template.delete', $arr_current_user_access) )
                            <div class="check-box">
                              <input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_{{base64_encode($template['id'])}}" value="{{base64_encode($template['id'])}}" />
                              <label for="mult_change_{{base64_encode($template['id'])}}"> 
                              </label>
                            </div>
                            @endif
                          </td>
                          <td><?php echo $key+1 ?></td>
                          <td>
                            {{translation($template['get_question_category']['slug'])}}
                          </td>
                          <td>
                            {{isset($template['title'])?$template['title']:''}}
                          </td>
                          <td>
                            {{$template['options'] or ''}}
                          </td>
                          <td>
                            @if(isset($template['is_required']) && $template['is_required']!='0')
                              {{translation('no')}}
                            @else 
                              {{translation('yes')}}
                            @endif  
                          </td>
                          <td>
                            @if(array_key_exists('school_template.update', $arr_current_user_access))
                              @if(isset($template['is_active']) && $template['is_active'] == "0")
                                  <a class="blue-color" title="{{translation('activate')}}" href='{{$module_url_path.'/activate/'.base64_encode($template['id'])}}' 
                                  onclick="return confirm_action(this,event,'{{translation('do_you_really_want_to_activate_this_record')}}','{{translation("are_you_sure")}}','{{translation('yes')}}','{{translation("no")}}')" >
                                  <i class="fa fa-lock"></i>
                                  </a>
                              @elseif(isset($template['is_active']) && $template['is_active'] == "1")
                              
                                  <a class="light-blue-color" title="{{translation('deactivate')}}" href='{{$module_url_path.'/deactivate/'.base64_encode($template['id'])}}' onclick="return confirm_action(this,event,'{{translation('do_you_really_want_to_deactivate_this_record')}}','{{translation("are_you_sure")}}','{{translation('yes')}}','{{translation("no")}}')" ><i class="fa fa-unlock"></i></a>
                              @endif    
                              @if(isset($template['title']))
                              @if((strslug($template['title']) == 'school_name') || (strslug($template['title']) == 'school_logo') || (strslug($template['title']) == 'school_address') || (strslug($template['title']) == 'school_email'))
                                    
                                  <a style="position: relative;" class="orange-color" href="javascript:void(0)" title="{{translation('access_denied')}}"><i class="fa fa-edit" ></i><i class="fa fa-ban fa-stack-2x text-danger"></i></a>
                              @else
                                  <a class="orange-color" href="{{$module_url_path.'/edit/'.base64_encode($template['id'])}}" title="{{translation('edit')}}"><i class="fa fa-edit" ></i></a>
                              @endif
                              @endif
                            @endif

                            @if(array_key_exists('school_template.delete', $arr_current_user_access)) 
                             @if(isset($template['title']))
                              @if((strslug($template['title']) == 'school_name') || (strslug($template['title']) == 'school_logo') || (strslug($template['title']) == 'school_address') || (strslug($template['title']) == 'school_email'))
                                <a style="position: relative;" class="red-color" href="javascript:void(0)" title="{{translation('access_denied')}}" ><i class="fa fa-trash" ></i> <i class="fa fa-ban fa-stack-2x text-danger"></i></a>
                              @else
                                <a class="red-color" href="{{$module_url_path.'/delete/'.base64_encode($template['id'])}}" title="{{translation('delete')}}" onclick="return confirm_action(this,event,'{{translation('do_you_really_want_to_delete_this_record')}}','{{translation("are_you_sure")}}','{{translation('yes')}}','{{translation("no")}}')"><i class="fa fa-trash" ></i></a>
                              @endif
                              @endif
                            @endif
                          </td>
                        </tr>
                      @endforeach
                   @endif
               </tbody>
            </table>
         </div>
         {!! Form::close() !!}
      </div>
   </div>
</div>

<script type="text/javascript">
   var csrf_token      = '{{csrf_token()}}';
   $("#table_body").sortable({ 
        update : function () { 
          var order = $('#table_body').sortable('serialize');  
          order += "&_token="+csrf_token;
          $.ajax({
            url:'{{$module_url_path}}'+'/rearrange_order_number',
            method:"get",
            data:order,
            success:function(response)
            {
                if(response.status=='SUCCESS')
                {
                  location.reload();
                }
                else
                {
                  swal("Error!", response.msg, "error");
                  return false;
                }

            } 
          })
        } 
      });
</script>
@stop

