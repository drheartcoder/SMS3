@extends('professor.layout.master') @section('main_content')
<!-- BEGIN Breadcrumb -->

<div id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="{{ url($professor_panel_slug.'/dashboard') }}">{{translation('dashboard')}}</a>
        </li>
        <span class="divider">
            <i class="fa fa-angle-right"></i>
        </span>
        <li class="active">{{ isset($module_title)?$module_title:"" }}</li>
    </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="fa fa-file"></i>{{translation('student_behaviour')}}</h1>
    </div>
</div>
<!-- END Page Title -->

<link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">

<!-- BEGIN Tiles -->
<div class="row">
    <div class="col-md-12">
        <div class="box  box-navy_blue">
            <div class="box-title">
                <h3><i class="fa fa-list"></i>{{ isset($module_title)?$module_title:"" }}</h3>
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

                    @if (array_key_exists('student_behaviour.create', $arr_current_user_access))
                    <a href="{{ $module_url_path.'/create' }}">{{translation('add')}} {{ $module_title }}</a>
                    @endif

                </div>
            </div>
            <div class="box-content studt-padding">
                @include('professor.layout._operation_status'){!! Form::open([ 'url' => $module_url_path.'/multi_action', 'method'=>'POST', 'enctype' =>'multipart/form-data', 'class'=>'form-horizontal', 'id'=>'frm_manage' ]) !!} {{ csrf_field() }}             

                <div class="col-md-12 ajax_messages">
                    <div class="alert alert-danger" id="error" style="display:none;"></div>
                    <div class="alert alert-success" id="success" style="display:none;"></div>
                </div>

                <div class="clearfix"></div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('level')}} <label style="color: red">*</label></label>
                            <div class="col-sm-9 col-md-8 col-lg-9 controls">
                                <select name="level" id="level" class="form-control" onChange="getClasses();" data-rule-required="true">
                                    <option value="">{{translation('select_level')}}</option>
                                    @if(isset($arr_levels) && count($arr_levels)>0)

                                    @foreach($arr_levels as $key => $level)
                                    <option value="{{isset($level['level_details']['level_id'])?$level['level_details']['level_id']:''}}">{{isset($level['level_details']['level_name'])?$level['level_details']['level_name']:''}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div> 

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('class')}} <label style="color: red">*</label></label>
                            <div class="col-sm-9 col-md-8 col-lg-9 controls">
                                <select name="class" id="class" class="form-control" onChange="getCourses();" data-rule-required="true">
                                    <option value="">{{translation('select_class')}}</option>

                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('course')}} <label style="color: red">*</label></label>
                            <div class="col-sm-9 col-md-8 col-lg-9 controls">
                                <select name="course" id="course" class="form-control" data-rule-required="true">
                                    <option value="">{{translation('select_course')}}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('behaviour_period')}}</label>
                            <div class="col-sm-9 col-md-8 col-lg-9 controls">
                                <input type="text" class="form-control" name="period" id="period" value="{{isset($period)?$period:''}}" readonly style="cursor: pointer;">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-sm-3 col-md-4 col-lg-3 control-label"></label>
                            <div class="col-sm-3 col-md-4 col-lg-3 controls">  
                                <div id="button">
                                    <input type="button" name="show" id="show" value="{{translation('show')}}" class="form-control btn btn-primary" onClick="getStudents();"> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="clearfix">
                </div>                            
                <div class="border-box" id="box" hidden="true">
                    <br>
                    <div class="filter-section">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">                            
                                    <input class="form-control" name="search_key" id="search_key" type="text" placeholder="{{translation('search')}}...">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive attendance-create-table-section" id="table_div" style="border:0;display: none;">

                        <input type="hidden" name="search" id="search" value="" />
                        <input type="hidden" name="file_format" id="file_format" value="" />

                        <table class="table table-advance" id="table_module" cellpadding="35px">
                            <thead>
                                <tr>
                                    <th>
                                        {{translation('sr_no')}}
                                    </th>
                                    <th>
                                        {{translation('name')}}
                                    </th>
                                    <th>
                                        {{translation('national_id')}}
                                    </th>
                                    <th>
                                        {{translation('average_notation')}}
                                    </th>
                                    <th>
                                        {{translation('comment')}}
                                    </th>
                                    <th>
                                        {{translation('action')}}
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="tbody">
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="div" style="border:0;display: none;">
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <!-- END Main Content -->
</div>

<script>

    function getClasses()
    {
        var level   =   $('#level').val();
        if(level != '')
        {
            $('#class').empty();
            $.ajax({
                url  :"{{ $module_url_path }}/getClasses",
                type :'POST',
                data :{'level':level ,'_token':'<?php echo csrf_token();?>'},
                success:function(data){
                    $('#class').append(data);
                }
            });
        }
    }

    function getCourses() {
        var level      =   $('#level').val();
        var cls_name   =   $('#class').val();

        $('#course').empty();
        $.ajax({
            url  :"{{ $module_url_path }}/get_courses",
            type :'POST',
            data :{'level':level ,'class':cls_name ,'_token':'<?php echo csrf_token();?>'},
            success:function(data){
                $('#course').empty();
                $('#course').append(data);
            }
        });
    }

    function getStudents()
    {
        var level      =   $('#level').val();
        var cls_name   =   $('#class').val();
        var course     =   $('#course').val();

        if(level != '' && cls_name != '' && course !='')
        {
            $('#button').html("<a class='form-control btn btn-primary'><i class='fa fa-spinner fa-spin'></i> {{translation('processing')}}...</a>");
            $('#button').attr('disabled', true);

            $.ajax({
                url  :"{{ $module_url_path }}/get_students_behaviour",
                type :'POST',
                data :{'level':level ,'class':cls_name ,'course':course,'_token':'<?php echo csrf_token();?>'},
                success:function(data){
                    if(data.flag == true)
                    {
                        $('#div').hide();
                        $('#box').show();
                        $('#table_div').show();
                        $('#tbody').empty();
                        $('#tbody').append(data.data);
                    }
                    else if(data.flag == false)
                    {

                        $('#table_div').hide();
                        $('#div').show();
                        $('#div').append(data.data);
                    }
                    $('#button').html('<input type="button" name="show" id="show" value="{{translation('show')}}" class="form-control btn btn-primary" onClick="getStudents();"> ');
                    $('#button').attr('disabled','none');
                }
            });
        }
        else
        {
            $('.ajax_messages').show();
            $('#error').css('display','block');
            $('#error').text('{{translation('select_level_class_course_first')}}');
            setTimeout(function(){
                $('.ajax_messages').hide();
            }, 4000);
        }
    }

    $("#search_key").keyup(function(){
        var flag=0;
        $("tbody tr").each(function(){

            var td = $(this).find("td");
            $(td).each(function(){
                var data = $(this).text().trim();
                data = data.toLowerCase();

                var search_key = $("#search_key").val();
                search_key = search_key.toLowerCase();
                search_key = new RegExp(search_key) ; 
                console.log(search_key.test(data));
                if(search_key.test(data)){
                    flag=1;
                    $(this).parent().show();
                    return false;
                }
                else{
                    $(this).parent().hide();
                }
                console.log(data);


            });
        })
        if(flag==0)
        {
            $("#hide_row").show();
        }
        else
        {
            $("#hide_row").hide();
        }  
    })
</script>

<script type="text/javascript">
    function exportForm(file_format)
    {
        var level      =   $('#level').val();
        var cls_name   =   $('#class').val();
        var course     =   $('#course').val();

        if(level != '' && cls_name != '' && course !='')
        {
            document.getElementById('file_format').value = file_format;
            var serialize_form   = $("#frm_manage").serialize();
            window.location.href = '{{ $module_url_path }}/export?'+serialize_form+'&export=true';
        }
        else
        {
            $('.ajax_messages').show();
            $('#error').css('display','block');
            $('#error').text('{{translation('select_level_class_course_first')}}');
            setTimeout(function(){
                $('.ajax_messages').hide();
            }, 4000);
        }
    }
    $(document).on("change","[type='search']",function(){
        var search_hidden = $(this).val();
        document.getElementById('search').value = search_hidden;
    });

</script>


@endsection