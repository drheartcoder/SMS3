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
      <h1><i class="{{$module_icon}}"></i>{{$module_title}}</h1>
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
               @if(array_key_exists('gradebook_fields.create', $arr_current_user_access))  
               <a href="{{$module_url_path}}/create" >{{translation("add").' '.$module_title }}</a> 
               @endif
               @if(array_key_exists('gradebook_fields.update', $arr_current_user_access))  
                  <a class="icon-btns-block" title="{{translation('multiple_activate')}}" 
                      href="javascript:void(0);" 
                      onclick='javascript : return check_multi_action("frm_manage","{{translation('are_you_sure')}}","{{translation('do_you_really_want_to_activate_this_record').'?'}}","{{translation('yes')}}","{{translation('no')}}","{{translation('oops')}}","{{translation('please_select_the_record_to_perform_this_action')}}","activate");'
                      style="text-decoration:none;">
                      <i class="fa fa-unlock"></i>
                  </a> 
                  <a class="icon-btns-block" title="{{translation('multiple_deactivate')}}" 
                    href="javascript:void(0);" 
                    onclick='javascript : return check_multi_action("frm_manage","{{translation('are_you_sure')}}","{{translation('do_you_really_want_to_deactivate_this_record').'?'}}","{{translation('yes')}}","{{translation('no')}}","{{translation('oops')}}","{{translation('please_select_the_record_to_perform_this_action')}}","deactivate");'
                    style="text-decoration:none;">
                  <i class="fa fa-lock"></i>
                  </a> 
                @endif
               @if(array_key_exists('gradebook_fields.delete',$arr_current_user_access))     
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
                        @if(array_key_exists('gradebook_fields.delete',$arr_current_user_access ) )
                        <th style="width: 5%; vertical-align: initial;">
                           <div class="check-box">
                              <input type="checkbox" class="filled-in" name="selectall" id="selectall" />
                              <label for="selectall"></label>
                           </div>
                        </th>
                        @endif
                        <th style="width:10%">{{translation('name')}}</th>
                        <th style="width:25%">{{translation('default_value')}} 1</th>
                        <th style="width:25%">{{translation('default_value')}} 2</th>
                        <th style="width:20%">{{translation('default_value')}} 3</th>
                        <th style="width:20%">{{translation('action')}}</th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach($arr_data as $value)
                     <tr>
                        @if(array_key_exists('gradebook_fields.delete',$arr_current_user_access ))
                        <td style="position: relative">
                           <div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_{{base64_encode($value['id'])}}" value="{{base64_encode($value['id'])}}" /><label for="mult_change_{{base64_encode($value['id'])}}"></label></div>
                        </td>
                        @endif
                        <td>{{$value['name']}}</td>
                        <td>{{$value['default_value1']}}</td>
                        <td>{{$value['default_value2']}}</td>
                        <td>{{$value['default_value3']}}</td>
                        <td style="position: relative;">
                           @if(array_key_exists('gradebook_fields.update',$arr_current_user_access ) )
                              <a class="orange-color" href="{{$module_url_path.'/edit/'.base64_encode($value['id'])}}" title="{{translation('edit')}}" ><i class="fa fa-edit" ></i></a>
                              @if($value['is_active']==1)
                                <a class="light-blue-color" href="{{$module_url_path.'/deactivate/'.base64_encode($value['id'])}}" title="{{translation('deactivate')}}" onclick="return confirm_action(this,event,'{{translation('do_you_really_want_to_deactivate_this_record')}}','{{translation("are_you_sure")}}','{{translation('yes')}}','{{translation("no")}}')"><i class="fa fa-unlock" ></i></a>
                              @else
                                <a class="light-red-color" href="{{$module_url_path.'/activate/'.base64_encode($value['id'])}}" title="{{translation('activate')}}" onclick="return confirm_action(this,event,'{{translation('do_you_really_want_to_activate_this_record')}}','{{translation("are_you_sure")}}','{{translation('yes')}}','{{translation("no")}}')"><i class="fa fa-lock" ></i></a>
                              @endif
                           @endif    
                           @if(array_key_exists('gradebook_fields.delete',$arr_current_user_access ) )
                           <a class="red-color" href="{{$module_url_path.'/delete/'.base64_encode($value['id'])}}" title="{{translation('delete')}}" onclick="return confirm_action(this,event,'{{translation('do_you_really_want_to_delete_this_record')}}','{{translation("are_you_sure")}}','{{translation('yes')}}','{{translation("no")}}')"><i class="fa fa-trash" ></i></a>
                           @endif


                        </td>
                     </tr>
                     @endforeach
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

  var table_module = false;
          $(document).ready(function()
          {
            table_module = $('#table_module').DataTable({
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
              ordering: false,
              
              searching:false
            });
          });


</script>
@stop                    


