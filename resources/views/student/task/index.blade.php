@extends('student.layout.master')                
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
    <li>
      <i class="fa fa-home"></i>
      <a href="{{ url($student_panel_slug.'/dashboard') }}"> {{translation('dashboard')}} </a>
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
          
               <a 
                  title="{{translation('refresh')}}" 
                  href="{{ $module_url_path }}"
                  style="text-decoration:none;">
               <i class="fa fa-repeat"></i>
               </a> 
           
            </div>
      </div>
      <div class="box-content">  
          @include('student.layout._operation_status') 
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
                  <th>{{ translation('sr_no')}}</th>
                  <th>{{ translation('task_name')}}</th>
                  <th>{{ translation('submission_date')}}</th> 
                  <th>{{ translation('submission_time')}}</th> 
                  <th>{{translation('supervisor')}}</th>
                  <th>{{translation('status')}}</th>
                  <th>{{translation('action')}}</th>
                </tr>
              </thead>
              <tbody>
                @if(sizeof($arr_tasks)>0)
                  <?php $count=1; ?>
                  @foreach($arr_tasks as $data)
                  
                      <tr>
                        <td>
                        	{{$count}}
                       	</td>
                        <td>{{ isset($data['task_name']) ? $data['task_name'] : '' }}</td>
                         <td>{{ isset($data['task_submission_date']) ? getDateFormat($data['task_submission_date']) : '' }}</td>
                        <td>{{ isset($data['task_submission_time']) ? getTimeFormat($data['task_submission_time']) : '' }}</td>
                        <td>{{ isset($data['get_supervisor']['first_name']) ? ucfirst($data['get_supervisor']['first_name']) : '' }} 
                        	{{ isset($data['get_supervisor']['last_name']) ? ucfirst($data['get_supervisor']['last_name']) : '' }}</td>
                        <td>
                         
                    		<div style="margin: 0 !important;width: 200px" class="form-group">
                             <select class="form-control" onChange="changeTaskStatus(this,'{{$data['get_user']['id']}}',{{$data['id']}})" id="parent_status_{{$data['id']}}">
                                <option value="PENDING" @if($data['get_user']['status']=='PENDING') selected @endif>{{translation('pending')}}</option>
                                <option  value="DONE" @if($data['get_user']['status']=='DONE') selected @endif>{{translation('done')}}</option>
                              </select>            
                            </div>
                        </td>
                        <td>
                            <a class="green-color" href="{{$module_url_path.'/view/'.base64_encode($data['id'])}}" title="{{translation('view')}}">
                              <i class="fa fa-eye" ></i>
                            </a>

                        </td>
                        <?php $count++; ?>
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
          "searching":false,      
          "aoColumnDefs": [
                          { 
                            "bSortable": false, 
                            "aTargets": [0,1,3,4,5,6] // <-- gets last column and turns off sorting
                           } 
                        ]
            });  
            $.fn.dataTable.ext.errMode = 'none';
});


  function changeTaskStatus(obj,id,task_id){
   
    var status = $(obj).val();
    
      $.ajax({
              url  :"{{ $module_url_path }}/change_user_status",
              type :'POST',
              data :{'status': status , 'id' : id , 'task_id' : task_id, '_token':'<?php echo csrf_token();?>'},
              success:function(data){
                location.reload(true);
              }
            }); 
    
  }
</script>
@stop                    


