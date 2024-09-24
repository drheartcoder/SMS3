@extends('schooladmin.layout.master') @section('main_content')
<!-- BEGIN Breadcrumb -->

<div id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="{{ url($school_admin_panel_slug.'/dashboard') }}">{{translation('dashboard')}}</a>
        </li>
        <span class="divider">
            <i class="fa fa-angle-right"></i>
        </span>
        <li>
            <a href="{{ url($module_url_path) }}">{{$page_title}}</a>
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
        <h1><i class="{{$module_icon}}"></i>{{$page_title}}</h1>
    </div>
</div>
<!-- END Page Title -->

<link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">

<!-- BEGIN Tiles -->
<div class="box {{ $theme_color }}">
      <div class="box-title">
         <h3>
            <i class="fa fa-eye"></i>
           {{$module_title}}  
         </h3>
         <div class="box-tool">
         </div>
      </div>
       
        <?php
        
         $task_name       = isset($task['task_name']) ?$task['task_name']:"-";
         $description     = isset($task['task_description']) ?$task['task_description']:"-";
         $priority        = isset($task['priority']) ? translation(strtolower($task['priority'])) : '';
         $submission_date = isset($task['task_submission_date']) ? getDateFormat($task['task_submission_date']) : '';
         $submission_time = isset($task['task_submission_time']) ? getDateFormat($task['task_submission_time']) : '';
         $first_name      = isset($task['get_supervisor']['first_name']) ? ucfirst($task['get_supervisor']['first_name']) : '';
         $last_name       = isset($task['get_supervisor']['last_name']) ? ucfirst($task['get_supervisor']['last_name']) : '';
         $supervisor_name = $first_name.' '.$last_name;
        ?>

          <div class="clearfix"></div>
          
          <div class="clearfix"></div>
          <div class="box-content view-details-seciton-main details-section-main-block">
           <div class="row">           
                  <div class="col-md-12">  
                    <div class="details-infor-section-block">
                       {{$module_title}}
                    </div>                  
                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('task_name')}}  </b>: </label>
                     <div class="col-sm-4 col-lg-4 controls">
                        {{$task_name}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('description')}}  </b>: </label>
                     <div class="col-sm-4 col-lg-4 controls">
                        {{$description}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('task_priority')}}  </b>: </label>
                     <div class="col-sm-4 col-lg-4 controls">
                        {{$priority}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('submission_date')}}  </b>: </label>
                     <div class="col-sm-4 col-lg-4 controls">
                        {{$submission_date}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('submission_time')}}  </b>: </label>
                     <div class="col-sm-4 col-lg-4 controls">
                        {{$submission_time}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('supervisor')}}  </b>: </label>
                     <div class="col-sm-4 col-lg-4 controls">
                        {{$supervisor_name}}
                     </div>
                     <div class="clearfix"></div>
                  </div>
                  
                  @if(isset($task['is_individual']) && $task['is_individual']!=='0')
                    <div class="form-group">
                       <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('level')}}  </b>: </label>
                       <div class="col-sm-4 col-lg-4 controls">
                          @if(isset($task['level_id']) && $task['level_id']=='0')
                            {{isset($task['get_level_class']['level_details']['level_name']) ? $task['get_level_class']['level_details']['level_name']: ''}}
                          @else
                            {{isset($task['level_details']['level_name']) ? $task['level_details']['level_name']: ''}}
                          @endif  
                       </div>
                       <div class="clearfix"></div>
                    </div>  
                    @if(isset($task['level_id']) && $task['level_id']=='0')
                      <div class="form-group">
                       <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('class')}}  </b>: </label>
                       <div class="col-sm-4 col-lg-4 controls">
                          {{isset($task['get_level_class']['class_details']['class_name']) ? $task['get_level_class']['class_details']['class_name']: ''}}
                       </div>
                       <div class="clearfix"></div>
                      </div>  
                    @endif
                  @endif
                  @if($added_by=='yes' || $task['task_supervisor_id'] == $current_user )
                  <div class="form-group">

                    <label class="col-sm-4 col-lg-4 control-label"><b>{{str_plural(translation('assignee'))}}</b>: </label>
                    <div class="table-responsive attendance-create-table-section" style="border:0">
                      
                        <table class="table table-advance" id="table_module">
                            <thead>
                                <tr> 

                                    <th class="sorting_disabled">
                                        <a class="sort-descs" >{{translation('name')}}</a><br>
                                        
                                    </th>
                                    <th class="sorting_disabled">
                                        <a class="sort-descs" >{{translation('national_id')}}</a><br>
                                        
                                    </th>
                                    <th class="sorting_disabled">
                                        <a class="sort-descs" href="#">{{translation('status')}}</a><br>
                                        
                                    </th>
                                  </tr>
                             </thead>
                            <tbody>
                              @foreach($arr_data  as $value)
                              <tr>
                                <td>
                                    {{isset($value['get_user']['first_name'])?ucfirst($value['get_user']['first_name']):''}} {{isset($value['get_user']['last_name'])?ucfirst($value['get_user']['last_name']):''}}
                                </td>
                                <td>
                                    {{$value['get_user']['national_id'] or '-'}}
                                </td>
                                <td>
                                @if($task['task_supervisor_id'] == $current_user)
                                     @if($value['status']=='DONE')
                                      &nbsp;&nbsp;<span class="green-color" style="color:white">&nbsp;&nbsp;{{isset($value['status']) ?translation(strtolower($value['status'])):''}}&nbsp;&nbsp;</span>
                                     @else
                                      &nbsp;&nbsp;<span class="red-color" style="color:white">&nbsp;&nbsp;{{ $value['status'] ? translation(strtolower($value['status'])) :''}}&nbsp;&nbsp;</span>
                                     @endif
                                @else
                                   <div style="margin: 0 !important;width: 200px" class="form-group">

                                    <select class="form-control" onchange="changeStatus('{{$value['id']}}')" id="status_{{$value['id']}}">
                                          <option value="PENDING" @if($value['status']=='PENDING') selected @endif>{{translation('pending')}}</option>
                                          <option  value="DONE" @if($value['status']=='DONE') selected @endif>{{translation('done')}}</option>
                                        </select>
                                    </div>
                                @endif
                                </td>

                              </tr>
                              @endforeach
                            </tbody> 
                        </table>
                        <div id="hide_row" class="alert alert-danger" style="text-align:center" hidden>{{translation('no_data_available')}}   
                          </div>
                          <div class="col-md-12">    
                            <div class="form-group back-btn-form-block">
                               <div class="controls">
                                  <a href="{{ $module_url_path }}" class="btn btn-primary" style="float: right;margin-top: 20px;" > <i class="fa fa-arrow-left"></i> {{translation('back')}} </a>
                               </div>
                            </div>  
                          </div>
                    </div>
                     <div class="clearfix"></div>
                  </div>
                  @else
                    <div class="form-group back-btn-form-block">
                     <div class="controls">
                        <a href="{{ $module_url_path }}" class="btn btn-primary" style="float: right; margin-top: 20px"><i class="fa fa-arrow-left"></i> {{translation('back')}} </a>
                     </div>
                    </div>
                  @endif

                  </div>
                 </div> 
            </div>
          </div>

<script>
  function changeStatus(id)
  {
    var status = $("#status_"+id).val();
   
      $.ajax({
              url  :"{{ $module_url_path }}/change_user_status",
              type :'post',
              data :{'status': status , 'id' : id , '_token':'<?php echo csrf_token();?>'},
              success:function(data){
              }
            }); 
    
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

@endsection