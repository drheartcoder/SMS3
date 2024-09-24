@extends('schooladmin.layout.master')                
@section('main_content')
<style>
.user-td{
  position:relative;
}
</style>
<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
    <li>
      <i class="fa fa-home">
      </i>
      <a href="{{ url($school_admin_panel_slug.'/dashboard') }}"> {{translation('dashboard')}}
      </a>
    </li>
    <span class="divider">
      <i class="fa fa-angle-right">
      </i>
      <i class="fa {{$module_icon}}">
      </i>
    </span> 
    <li >  <a href="{{$module_url_path}}"> {{ isset($page_title)?$page_title:"" }}</a>
    </li>
    <span class="divider">
      <i class="fa fa-angle-right">
      </i>
      <i class="fa {{$create_icon}}">
      </i>
    </span> 
    <li class="active">{{ isset($module_title)?$module_title:"" }}
    </li>
  </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<link rel="stylesheet" type="text/css" href="" ref="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">
 <div class="page-title new-agetitle">
    <div>
        <h1><i class="{{$module_icon}}"></i>{{str_plural($module_title)}}</h1>
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
            {{str_plural(translation('student'))}}
         </h3>
         <div class="box-tool">
               
            </div>
      </div>
      <div class="box-content">
         @include('schooladmin.layout._operation_status')  
         {!! Form::open([ 'url' => $module_url_path.'/store_result/'.base64_encode($exam_id),
         'method'=>'POST',
         'id'=>'validation-form1',
         'class'=>'form-horizontal'
        
         ]) !!}
         {{ csrf_field() }}
         <div class="col-md-10">
            <div id="ajax_op_status">
            </div>
            <div class="alert alert-danger" id="no_select" style="display:none;"></div>
            <div class="alert alert-warning" id="warning_msg" style="display:none;"></div>
         </div>
         <div class="btn-toolbar pull-right clearfix">
            </div>
         <div class="clearfix"></div>
         <div class="filter-section">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">                                                                                        
                        <input class="form-control" name="search_key" id="search_key" type="text" placeholder="{{translation('search')}}...">
                    </div>
                </div>
            </div>
          </div>
         <div class="table-responsive attendance-create-table-section" style="border:0">
                        <input type="hidden" name="multi_action" value="" />
                        <input type="hidden" name="search" id="search" value="" />
                        <table class="table table-advance" id="table_module">
                            <thead>
                                <tr> 
                                    <th class="sorting_disabled">
                                        <a class="sort-descs" >{{translation('sr_no')}}</a><br>
                                        
                                    </th>
                                    <th class="sorting_disabled">
                                        <a class="sort-descs" >{{translation('student_name')}}</a><br>
                                        
                                    </th>
                                    <th class="sorting_disabled">
                                        <a class="sort-descs" >{{translation('student_number')}}</a><br>
                                        
                                    </th>
                                    <th class="sorting_disabled">
                                        <a class="sort-descs" href="#">{{translation('national_id')}}</a><br>
                                        
                                    </th>
                                    <th>
                                        <a class="sort-descs" href="#">{{translation('marks')}}</a><br>
                                    </th>
                                  </tr>
                             </thead>
                            <tbody>
                            <?php $count=1;  ?>
                              @foreach($arr_students  as $value)
                              <tr>
                                <td>
                                    {{$count++}}
                                </td>
                                <td>
                                    {{ ucfirst($value['get_user_details']['first_name'])}}
                                    {{ ucfirst($value['get_user_details']['last_name'])}}
                                </td>
                                <td>
                                    {{$value['student_no'] or '-'}}
                                </td>
                                <td>
                                    {{$value['get_user_details']['national_id'] or '-'}}
                                </td>
                                <td>
                                  <div class="form-group">
                                    <div class="col-sm-9 col-md-8 col-lg-4 controls">
                                       <input class="form-control" name="marks[{{$value['id']}}]" placeholder="{{translation('enter_marks')}}" type="text" data-rule-required='true' value="{{isset($arr_result[$value['id']])?$arr_result[$value['id']]:0}}"/>
                                       
                                    </div>
                                </div>
                                </td>

                              </tr>
                              @endforeach
                            </tbody> 
                        </table>
                        <div id="hide_row" class="alert alert-danger" style="text-align:center" hidden>{{translation('no_data_available')}}   
                        </div>
                        <div style="float:right">
                           <div class="form-group back-btn-form-block" style="display:inline-block">
                              <div class="controls">
                                 <a href="{{ $module_url_path }}" class="btn btn-primary" style="float: right;margin-top: 20px;" > <i class="fa fa-arrow-left"></i> {{translation('back')}} </a>
                              </div>
                           </div>
                           <div class="form-group back-btn-form-block" style="display:inline-block">
                              <div class="controls">
                                 
                                 <button class="btn btn-primary" style="float: right;margin-top: 20px;" >{{translation('save')}} </button>
                              </div>
                           </div>
                        </div>
                    </div>
         {!! Form::close() !!}
      </div>
   </div>
</div>
</div>
<script>

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
@stop

