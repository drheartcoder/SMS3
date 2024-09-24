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
            @if(array_key_exists('club.create', $arr_current_user_access))  
              <a href="{{$module_url_path}}/create" >{{translation("add").' '.$page_title }}</a> 
            @endif

            @if(array_key_exists('club.delete',$arr_current_user_access))     
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
            
          
          <div class="table-responsive" style="border:0">

            <table class="table table-advance"  id="table_module" >
              <thead>
                <tr>
                  @if(array_key_exists('club.delete', $arr_current_user_access) )
                  <th> <div class="check-box">
                            <input type="checkbox" class="filled-in" name="selectall" id="selectall" />
                            <label for="selectall"></label>
                        </div>
                  </th>
                  @endif
                  <th>{{ translation('club_name')}}</th>
                  <th>{{ translation('club_id')}}</th> 
                  <th>{{ translation('club_fees')}}</th> 
                  <th>{{translation('supervisor')}}</th>
                  @if(array_key_exists('club.update', $arr_current_user_access))
                    <th>{{translation('add').' '.translation('student')}}</th>
                  @endif  
                  <th>{{translation('action')}}</th>
                </tr>
              </thead>
              <tbody>
                @if(sizeof($arr_clubs)>0)
                
                  @foreach($arr_clubs as $data)
                      <tr>
                        @if(array_key_exists('club.delete', $arr_current_user_access) )
                        <td>
                          
                            @if(isset($data['get_students']) && count($data['get_students'])==0 )
                              <div class="check-box">
                              <input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_{{base64_encode($data['id'])}}" value="{{base64_encode($data['id'])}}" />
                              <label for="mult_change_{{base64_encode($data['id'])}}"></label>
                            @else
                               &nbsp;-  
                            @endif  
                          </div>
                          
                        </td>
                        @endif
                        <td>{{ isset($data['club_name']) ? $data['club_name'] : '' }}</td>
                        <td>{{ isset($data['club_no']) ? $data['club_no'] : '' }}</td>
                        <td>{{ isset($data['club_fee']) ? $data['club_fee'] : '0' }}  {{config('app.project.currency')}}</td>
                        <td>{{ isset($data['get_supervisor']['first_name']) ? ucfirst($data['get_supervisor']['first_name']) : '' }} {{ isset($data['get_supervisor']['last_name']) ? ucfirst($data['get_supervisor']['last_name']) : '' }}</td>
                        @if(array_key_exists('club.update', $arr_current_user_access))
                        <td>
                          <a  href="{{$module_url_path.'/add_student/'.base64_encode($data['id'])}}" class="light-blue-color" style="color:white">&nbsp;{{translation('add').' '.translation('student')}}&nbsp;</a>
                        </td>
                        @endif
                        <td>
                          <a class="green-color" href="{{$module_url_path.'/view/'.base64_encode($data['id'])}}" title="{{translation('view')}}">
                            <i class="fa fa-eye" ></i>
                          </a>
                          @if(array_key_exists('club.update', $arr_current_user_access))  
                           
                              <a class="orange-color" href="{{$module_url_path.'/edit/'.base64_encode($data['id'])}}" title="{{translation('edit')}}">
                                <i class="fa fa-edit" ></i>
                              </a>
                            
                          @endif
                          
                          @if(array_key_exists('club.delete', $arr_current_user_access))
                             @if(isset($data['get_students']) && count($data['get_students'])==0 )
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
          
          "searching": true,
          "pageLength": 10,   
          "sorting":false,
          'filtering':false,
            });  
           $.fn.dataTable.ext.errMode = 'none';
           $(".dataTables_filter").css("float",'right');
});
</script>
@stop                    


