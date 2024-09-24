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
        <h1><i class="fa fa-book"></i>{{$page_title}}</h1>
    </div>
</div>
<!-- END Page Title -->

<link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">

<!-- BEGIN Tiles -->
        <div class="box  box-navy_blue">
            <div class="box-title">
                <h3><i class="fa fa-eye"></i>{{ isset($module_title)?$module_title:"" }}</h3>
                <div class="box-tool">
        
                </div>
            </div>

            <div class="box-content studt-padding">

                <div class="row">

                <div class="col-md-12">  
                    <div class="details-infor-section-block">
                       {{str_plural(translation('student'))}}

                    </div> 
                    <br><br>

                    <div class="form-group">
                         <label class="col-sm-2 col-lg-2 control-label" style="text-align:left"><b> {{translation('level')}}  </b>: </label>
                         <label class="col-sm-2 col-lg-2 control-label" style="text-align:left"> {{isset($arr_data['get_level_class']['level_details']['level_name']) ? $arr_data['get_level_class']['level_details']['level_name']:'' }} </label>
                         <div class="clearfix"></div>
                    </div>             
                    <div class="form-group">
                         <label class="col-sm-2 col-lg-2 control-label" style="text-align:left"><b> {{translation('class')}}  </b>: </label>
                         <label class="col-sm-2 col-lg-2 control-label" style="text-align:left"> {{isset($arr_data['get_level_class']['class_details']['class_name']) ? $arr_data['get_level_class']['class_details']['class_name'] :''}} </label>
                         <div class="clearfix"></div>
                    </div>             
                    <div class="form-group">
                         <label class="col-sm-2 col-lg-2 control-label" style="text-align:left"><b> {{translation('course')}}  </b>: </label>
                         <label class="col-sm-2 col-lg-2 control-label" style="text-align:left"> {{isset($arr_data['get_course']['course_name']) ? $arr_data['get_course']['course_name'] :''}} </label>
                         <div class="clearfix"></div>
                    </div>             
                    <div class="form-group">
                         <label class="col-sm-2 col-lg-2 control-label" style="text-align:left"><b> {{translation('added_date')}}  </b>: </label>
                         <label class="col-sm-2 col-lg-2 control-label" style="text-align:left"> {{isset($arr_data['added_date']) ? getDateFormat($arr_data['added_date']) :''}} </label>
                         <div class="clearfix"></div>
                    </div>             
                    <div class="form-group">
                         <label class="col-sm-2 col-lg-2 control-label" style="text-align:left"><b> {{translation('due_date')}}  </b>: </label>
                         <label class="col-sm-2 col-lg-2 control-label" style="text-align:left">{{isset($arr_data['due_date']) ? getDateFormat($arr_data['due_date']) :''}}</label>
                         <div class="clearfix"></div>
                    </div>             
                    <div class="form-group">
                         <label class="col-sm-2 col-lg-2 control-label" style="text-align:left"><b> {{translation('description')}}  </b>: </label>
                         <label class="col-sm-2 col-lg-2 control-label" style="text-align:left"> {{isset($arr_data['description']) ? $arr_data['description'] :''}} </label>
                         <div class="clearfix"></div>
                    </div>             
                </div> 
                </div>   
                <div class="clearfix"></div>
                  
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
                    <div class="table-responsive attendance-create-table-section" style="border:0">
                        <input type="hidden" name="multi_action" value="" />
                        <input type="hidden" name="search" id="search" value="" />
                        <table class="table table-advance" id="table_module">
                            <thead>
                                <tr> 

                                    <th class="sorting_disabled">
                                        <a class="sort-descs" >{{translation('student_name')}}</a><br>
                                        
                                    </th>
                                    <th class="sorting_disabled">
                                        <a class="sort-descs" >{{translation('student_number')}}</a><br>
                                        
                                    </th>
                                    <th class="sorting_disabled">
                                        <a class="sort-descs" href="#">{{translation('status')}}</a><br>
                                        
                                    </th>
                                  </tr>
                             </thead>
                            <tbody>
                              @foreach($arr_students  as $value)
                              <tr>
                                <td>
                                    {{$value['get_student_details']['get_user_details']['first_name'] or '-' }}
                                    {{$value['get_student_details']['get_user_details']['last_name'] or '-'}}
                                </td>
                                <td>
                                    {{$value['get_student_details']['student_no'] or '-'}}
                                </td>
                                <td>
                                         @if($value['status']=='COMPLETED' && $value['status_changed_by']=="PROFESSOR")
                                          <span class="light-blue-color" style="color:white">&nbsp;{{translation('completed')}}&nbsp;</span>
                                         @else
                                         <div style="margin: 0 !important;width: 200px" class="form-group">
                                          <select class="form-control" onchange="changeStatus('{{$value['id']}}')" id="status_{{$value['id']}}"> 
                                            <option value="PENDING" @if($value['status']=='PENDING') selected @endif>{{translation('pending')}}</option>
                                            <option  value="COMPLETED" @if($value['status']=='COMPLETED') selected @endif>{{translation('completed')}}</option>
                                            <option value="REJECTED" @if($value['status']=='REJECTED') selected @endif>{{translation('rejected')}}</option>
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

            </div>
        </div>
        <!-- Modal -->
      <div class="modal fade edit-event-main" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display:none">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title" id="myModalLabel">{{translation('rejected')}}</h4>
                  </div>
                  <div class="modal-body">
                      <div class="form-group">
                          <label class="control-label">{{translation('rejection_reason')}}</label>
                          <div class="controls">                      
                            <textarea class="form-control" name="reason" id="reason"></textarea>
                            <span class='help-block'></span>
                          </div>
                          <div class="clearfix"></div>
                          <input type="hidden" id="homework_id"/>
                      </div>
                  </div>

                  <div class="modal-footer">
                      <div class="action-button-block">                    
                          <button class="btn btn-primary" type="submit" id="btn_update">{{translation('save')}}</button>
                      </div>
                  </div>
              </div>
          </div>
      </div>
       <!-- END Main Content -->

<script>
  function changeStatus(id)
  {
    var status = $("#status_"+id).val();
    console.log(status);
    if(status=="REJECTED")
    {
        $('#myModal').modal('show');
        $("#homework_id").val(id);
    }
    else
    {
      $.ajax({
              url  :"{{ $module_url_path }}/change_status",
              type :'POST',
              data :{'status': status , 'id' : id , '_token':'<?php echo csrf_token();?>'},
              success:function(data){
              }
            }); 
    }
  }
  $("#btn_update").click(function(){
    if($("#reason").val()=="")
    { 
       $("#reason").next('span').html('{{translation('this_field_is_required')}}'); 
    }
    else
    {
      $('#myModal .close').click();
      var status = "REJECTED";
      var id = $("#homework_id").val();
      var reason = $("#reason").val();
      $.ajax({
              url  :"{{ $module_url_path }}/change_status",
              type :'POST',
              data :{'status': status , 'id' : id , 'reason' : reason , '_token':'<?php echo csrf_token();?>'},
              success:function(data){
              }
            }); 
    }
  });
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