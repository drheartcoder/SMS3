@extends('schooladmin.layout.master')                
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
    <li>
      <i class="fa fa-home"></i>
      <a href="{{ url($school_admin_panel_slug.'/dashboard') }}"> {{translation('dashboard')}} </a>
    </li>
    <span class="divider">
      <i class="fa fa-angle-right"></i>
      <i class="{{$module_icon}}"></i>   
    </span> 
    <li class="active"> {{ $module_title or ''}} </li>
   
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

<!-- BEGIN Main Content -->
<div class="row">
  <div class="col-md-12">
    <div class="box {{ $theme_color }}">
      <div class="box-title">
        <h3>
          <i class="fa fa-list"></i>
          {{ isset($module_title)?$module_title:"" }}
        </h3>
        <div class="box-tool">
            @if(array_key_exists('task.create', $arr_current_user_access))  
              <a href="{{$module_url_path}}/create" >{{translation("add").' '.$page_title }}</a> 
            @endif

            @if(array_key_exists('task.delete',$arr_current_user_access))     
               <a title="{{translation('multiple_delete')}}" 
                  href="javascript:void(0);" 
                  onclick='javascript : return check_multi_action("frm_manage","{{translation('are_you_sure')}}","{{translation('do_you_really_want_to_delete_this_record').'?'}}","{{translation('yes')}}","{{translation('no')}}","{{translation('oops')}}","{{translation('please_select_the_record_to_perform_this_action')}}","delete");'
                  style="text-decoration:none;">
               <i class="fa fa-trash-o"> </i>
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
          @include('schooladmin.layout._operation_status') 
          {!! Form::open([ 'url' => $module_url_path.'/multi_action',
                                 'method'=>'POST',
                                 'enctype' =>'multipart/form-data',   
                                 'class'=>'form-horizontal', 
                                 'id'=>'frm_manage' 
                                ]) !!} 

            {{ csrf_field() }}
            <div class="col-md-10">
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
                @if(array_key_exists('task.delete', $arr_current_user_access))
                  <th> <div class="check-box">
                            <input type="checkbox" class="filled-in" name="selectall" id="selectall" />
                            <label for="selectall"></label>
                        </div></th>
                @endif        
                  <th>{{ translation('task_name')}}</th>
                  <th>{{ translation('submission_date')}}</th> 
                  <th>{{ translation('submission_time')}}</th> 
                  <th>{{translation('supervisor')}}</th>
                  <th>{{translation('status')}}</th>
                  <th>{{translation('total_done')}} %</th>
                  <th>{{translation('action')}}</th>
                </tr>
              </thead>
              <tbody>
                @if(sizeof($arr_tasks)>0)
                
                  @foreach($arr_tasks as $data)
                      <tr>
                        @if(array_key_exists('task.delete', $arr_current_user_access))
                        <td>
                          @if($data['added_by']==$current_user)
                        	<div class="check-box">
                        		  <input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_{{base64_encode($data['id'])}}" value="{{base64_encode($data['id'])}}" />
                        		  <label for="mult_change_{{base64_encode($data['id'])}}"></label>    
                        	</div>
                          @else
                              -
                          @endif
                       	</td>
                        @endif
                        <td>{{ isset($data['task_name']) ? $data['task_name'] : '' }}</td>
                        <td>{{ isset($data['task_submission_date']) ? getDateFormat($data['task_submission_date']) : '' }}</td>
                        <td>{{ isset($data['task_submission_time']) ? getTimeFormat($data['task_submission_time']) : '' }}</td>
                        <td>{{ isset($data['get_supervisor']['first_name']) ? ucfirst($data['get_supervisor']['first_name']) : '' }} 
                        	{{ isset($data['get_supervisor']['last_name']) ? ucfirst($data['get_supervisor']['last_name']) : '' }}</td>
                        <td>
                    		<div style="margin: 0 !important;width: 200px" class="form-group">

                                	@if($data['added_by']==$current_user)
                                    <select class="form-control" onchange="changeStatus('{{$data['id']}}')" id="status_{{$data['id']}}">
                                        <option value="OPEN" @if(isset($data['task_status']) &&  $data['task_status']=='OPEN') selected @endif>{{translation('open')}}</option>
                                        <option  value="ON_HOLD" @if(isset($data['task_status']) && $data['task_status']=='ON_HOLD') selected @endif>{{translation('on_hold')}}</option>
                                        <option value="RESOLVED" @if(isset($data['task_status']) && $data['task_status']=='RESOLVED') selected @endif>{{translation('resolved')}}</option>
                                        <option value="CLOSED" @if(isset($data['task_status']) && $data['task_status']=='CLOSED') selected @endif>{{translation('closed')}}</option>
                                    </select>
                                  @elseif($data['get_user']['status'])
                                     <select class="form-control" onchange="changeTaskStatus('{{$current_user}}')" id="professor_status">
                                        <option value="PENDING" @if($data['get_user']['status']=='PENDING') selected @endif>{{translation('pending')}}</option>
                                        <option  value="DONE" @if($data['get_user']['status']=='DONE') selected @endif>{{translation('done')}}</option>
                                      </select>
                                  @else 
                                       @if($data['task_status']=='OPEN')
                                        &nbsp;&nbsp;<span class="light-orange-color" style="color:white">&nbsp;&nbsp;{{translation(strtolower($data['task_status']))}}&nbsp;&nbsp;</span>
                                       @elseif($data['task_status']=='ON_HOLD')
                                        &nbsp;&nbsp;<span class="light-blue-color" style="color:white">&nbsp;&nbsp;{{translation(strtolower($data['task_status']))}}&nbsp;&nbsp;</span>
                                       @elseif($data['task_status']=='RESOLVED')
                                        &nbsp;&nbsp;<span class="green-color" style="color:white">&nbsp;&nbsp;{{translation(strtolower($data['task_status']))}}&nbsp;&nbsp;</span>
                                       @else
                                        &nbsp;&nbsp;<span class="red-color" style="color:white">&nbsp;&nbsp;{{translation(strtolower($data['task_status']))}}&nbsp;&nbsp;</span>
                                       @endif
                                  @endif     
                            </div>
                        </td>
                        <td><?php
                                    $complete_count = 0;
                                    $total_count = 0;
                                    $percentage = 0;

                                    if(isset($data['get_task_users']) && count($data['get_task_users'])>0){
                                      $total_count = count($data['get_task_users']);
                                      foreach($data['get_task_users'] as $value){
                                        if($value['status']=="DONE"){
                                          $complete_count++;
                                        }
                                      }  
                                    }
                                    if($total_count!=0){
                                      $percentage = ($complete_count/$total_count)*100;
                                    }
                                    
                                 ?>{{round($percentage,2)}} %</td>


                        <td>
                          <a class="green-color" href="{{$module_url_path.'/view/'.base64_encode($data['id'])}}" title="{{translation('view')}}">
                            <i class="fa fa-eye" ></i>
                          </a>
                          @if(array_key_exists('task.update', $arr_current_user_access))  
                            @if($data['added_by']==$current_user)
                              <a class="orange-color" href="{{$module_url_path.'/edit/'.base64_encode($data['id'])}}" title="{{translation('edit')}}">
                                <i class="fa fa-edit" ></i>
                              </a>
                            @else
                              <a style="position: relative;" class="orange-color" href="javascript:void(0)" title="{{translation('access_denied')}}" ><i class="fa fa-edit" ></i><i class="fa fa-ban fa-stack-2x text-danger"></i></a>  
                            @endif  
                          @endif
                          @if(array_key_exists('task.delete', $arr_current_user_access))  
                            @if($data['added_by']==$current_user)
                              <a class="red-color" href="{{$module_url_path.'/delete/'.base64_encode($data['id'])}}" title="{{translation('delete')}}" onclick="return confirm_action(this,event,'{{translation('do_you_really_want_to_delete_this_record')}}','{{translation("are_you_sure")}}','{{translation('yes')}}','{{translation("no")}}')"><i class="fa fa-trash" ></i></a>
                            @else
                              <a style="position: relative;" class="red-color" href="javascript:void(0)" title="{{translation('access_denied')}}" ><i class="fa fa-trash" ></i><i class="fa fa-ban fa-stack-2x text-danger"></i></a>
                            @endif  
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
</div>

<script>

$(document).ready(function() {
	var oTable = $('#table_module').dataTable({
   <?php  if(Session::get('locale') == 'fr'){ ?>  
                   language: {
                     "sProcessing": "Traitement en cours ...",
                     "sLengthMenu": "Afficher _MENU_ lignes",
                     "sZeroRecords": "Aucun résultat trouvé",
                     "sEmptyTable": "Aucune donnée disponible",
                     "sInfo": "Lignes _START_ à _END_ sur _TOTAL_",
                     "sInfoEmpty": "Aucune ligne affichée",
                     "sInfoFiltered": "(Filtrer un maximum de_MAX_)",
                     "sInfoPostFix": "",
                     "sSearch": "Chercher:",
                     "sUrl": "",
                     "sInfoThousands": ",",
                     "sLoadingRecords": "Chargement...",
                     "oPaginate": {
                       "sFirst": "Premier", "sLast": "Dernier", "sNext": "Suivant", "sPrevious": "Précédent"
                       },
                     "oAria": {
                       "sSortAscending": ": Trier par ordre croissant", "sSortDescending": ": Trier par ordre décroissant"
                       }
                   } ,
              <?php } ?>
          "pageLength": 10,
          "bFilter":true,      
          "sortable":false,
          "ordering":false
            });  
            $.fn.dataTable.ext.errMode = 'none';
            $(".dataTables_filter").css("float",'right');
});

function changeStatus(id)
  {
    var status = $("#status_"+id).val();
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
</script>
@stop                    


