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
        <li class="active">{{ $module_title or ''}}</li>

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
        <div class="box">
            <div class="box-title">
                <h3>
                    <i class="fa fa-list"></i>
                    {{ isset($page_title)?$page_title:"" }}
                </h3>
                <div class="box-tool">
                    <div class="dropup-down-uls">
                        <a href="javascript:void(0)" class="export-lists"><i class="fa fa-upload"></i> {{ translation('export')}} </a>
                        <div class="export-content-links">
                            <div class="li-list-a">
                                <a href="javascript:void(0)" onclick="exportForm('pdf');">{{translation('pdf')}}</a>
                            </div>
                            <div class="li-list-a">
                                <a href="javascript:void(0)" onclick="exportForm('{{config("app.project.export_file_formate")}}');">{{translation('excel')}}</a>
                            </div>                             
                        </div>
                    </div>

                    @if(array_key_exists('level.create', $arr_current_user_access))
                        <a href="{{ $module_url_path.'/create' }}" title="Add {{ str_singular($module_title) }}">{{translation('add')}} {{ str_singular($module_title) }}</a> 
                    @endif      

                    @if(array_key_exists('level.update', $arr_current_user_access))
                        <a title="{{translation('multiple_deactiveblock')}}" 
                            href="javascript:void(0)"
                            onclick='javascript : return check_multi_action("frm_manage","{{translation('are_you_sure')}}","{{translation('do_you_really_want_to_deactivate_this_record').'?'}}","{{translation('yes')}}","{{translation('no')}}","{{translation('oops')}}","{{translation('please_select_the_record_to_perform_this_action')}}","deactivate");'
                            style="text-decoration:none;">
                            <i class="fa fa-lock"></i>
                        </a> 
                        <a title="{{translation('multiple_activeunblock')}}" 
                            href="javascript:void(0)"
                            onclick='javascript : return check_multi_action("frm_manage","{{translation('are_you_sure')}}","{{translation('do_you_really_want_to_activate_this_record').'?'}}","{{translation('yes')}}","{{translation('no')}}","{{translation('oops')}}","{{translation('please_select_the_record_to_perform_this_action')}}","activate");'
                            style="text-decoration:none;">
                            <i class="fa fa-unlock"></i>
                        </a>
                    @endif  
                    @if(array_key_exists('level.delete', $arr_current_user_access))
                        <a title="{{translation('multiple_delete')}}" 
                            href="javascript:void(0);" 
                            onclick='javascript : return check_multi_action("frm_manage","{{translation('are_you_sure')}}","{{translation('do_you_really_want_to_delete_this_record').'?'}}","{{translation('yes')}}","{{translation('no')}}","{{translation('oops')}}","{{translation('please_select_the_record_to_perform_this_action')}}","delete");'
                            style="text-decoration:none;">
                            <i class="fa fa-trash-o"></i>
                        </a>
                    @endif
                    <a title="{{translation('refresh')}}" 
                        href="javascript:void(0)"
                        onclick="javascript:location.reload();" 
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

                <input type="hidden" name="file_format" id="file_format" value="" />

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

                    <table class="table table-advance"  id="table_module" >
                        <thead>
                            <tr>

                                @if(array_key_exists('level.update', $arr_current_user_access) || array_key_exists('level.delete', $arr_current_user_access) )
                                <th style="width:18px"> 
                                    <div class="check-box">
                                        <input type="checkbox" class="filled-in" name="selectall" id="selectall" />
                                        <label for="selectall"></label>
                                    </div>
                                </th>
                                @endif
                                <th>
                                    {{translation('level')}}
                                </th> 
                                @if(array_key_exists('level.update', $arr_current_user_access) || array_key_exists('level.delete', $arr_current_user_access) )
                                <th width="250px">{{translation('action')}}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody id="table_body">
                            @if(isset($arr_levels) && count($arr_levels)>0)
                            @foreach($arr_levels as $key=>$level)
                            <tr id="listItem_{{ $level['id'] }}">
                                @if(array_key_exists('level.update', $arr_current_user_access) && array_key_exists('level.delete', $arr_current_user_access)) 
                                <td>
                                    <div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_{{base64_encode($level['id'])}}" value="{{base64_encode($level['id'])}}" /><label for="mult_change_{{base64_encode($level['id'])}}"></label></div>

                                </td>
                                @endif
                                <td>{{isset($level['level_name'])?$level['level_name']:''}}</td>
                                @if(array_key_exists('level.update', $arr_current_user_access) || array_key_exists('level.delete', $arr_current_user_access) )  
                                <td>
                                    @if(array_key_exists('level.update', $arr_current_user_access))
                                    @if($level['is_active'] != null && $level['is_active'] == "0")
                                    <a class="blue-color" title="{{translation('activate')}}" href="{{$module_url_path.'/activate/'.base64_encode($level['id'])}}" 
                                    onclick="return confirm_action(this,event,'{{translation('do_you_really_want_to_activate_this_record')}}',{{translation("are_you_sure")}}','{{translation('yes')}}','{{translation("no")}}')" ><i class="fa fa-lock"></i></a>

                                    @elseif($level['is_active'] != null && $level['is_active'] == "1")
                                    <a class="light-blue-color" title="{{translation('deactivate')}}"  href="{{$module_url_path.'/deactivate/'.base64_encode($level['id'])}}" onclick="return confirm_action(this,event,'{{translation('do_you_really_want_to_deactivate_this_record')}}',{{translation("are_you_sure")}}.','{{translation('yes')}}','{{translation("no")}}')" ><i class="fa fa-unlock"></i></a>
                                    @endif 
                                    <a class="orange-color" href="{{ $module_url_path.'/edit/'.base64_encode($level['id']) }}" title="{{translation("edit")}}">
                                        <i class="fa fa-edit" >
                                        </i>
                                    </a>  
                                    @endif

                                    @if(array_key_exists('level.delete', $arr_current_user_access))               
                                    <a class="red-color" href="{{$module_url_path.'/delete/'.base64_encode($level['id'])}}" title="{{translation('delete')}}" onclick="return confirm_action(this,event,'{{translation('do_you_really_want_to_delete_this_record')}}','{{translation("are_you_sure")}}','{{translation('yes')}}','{{translation("no")}}')"><i class="fa fa-trash" ></i></a>
                                    @endif  

                                </td>
                                @endif  
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                <div>
                </div>

                {!! Form::close() !!} 
            </div>
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

    function exportForm(file_format)
    {
        document.getElementById('file_format').value = file_format;
        var serialize_form   = $("#frm_manage").serialize();
        window.location.href = '{{ $module_url_path }}/export?'+serialize_form+'&export=true';
    }

</script> 
@stop