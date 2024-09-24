@extends('schooladmin.layout.master')                
@section('main_content')

<link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
      <li>
          <i class="fa fa-home"></i>
          <a href="{{ url($school_admin_panel_slug.'/dashboard') }}">{{translation('dashboard')}}</a>
      </li>
      <span class="divider">
          <i class="fa fa-angle-right"></i>
          <i class="fa {{$module_icon}}"></i>                
      </span> 
      <li class="active">{{ isset($module_title)?$module_title:"" }}</li>
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
      <div class="box-title pr0">
         <h3>
            <i class="fa fa-list"></i>
            {{ isset($page_title)?$page_title:"" }}
         </h3>
         <div class="box-tool">  
              @if(array_key_exists('notification.delete', $arr_current_user_access))     
               <a title="{{translation('multiple_delete')}}" 
                  href="javascript:void(0);" 
                  onclick='javascript : return check_multi_action("frm_manage","{{translation('are_you_sure')}}","{{translation('do_you_really_want_to_delete_this_record').'?'}}","{{translation('yes')}}","{{translation('no')}}","{{translation('oops')}}","{{translation('please_select_the_record_to_perform_this_action')}}","delete");'
                  style="text-decoration:none;">
               <i class="fa fa-trash-o">
               </i>
               </a>
               <a 
                  title="{{translation('refresh')}}" 
                  href="{{ $module_url_path }}"
                  style="text-decoration:none;">
               <i class="fa fa-repeat"></i>
               </a> 
               @endif
              
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
         <div class="btn-toolbar pull-right clearfix">
            <div class="btn-group"> 
               
            </div>
            <br>
         </div>
         <br/>
         <div class="clearfix"></div>
         <div class="table-responsive" style="border:0">
            <input type="hidden" name="multi_action" value="" />
            <table class="table table-advance"  id="table_module">
               <thead>
                  <tr>
                    @if(array_key_exists('notification.update', $arr_current_user_access) || array_key_exists('notification.delete', $arr_current_user_access) )                             
                     <th style="width: 18px; vertical-align: initial;">
                        <div class="check-box">
                            <input type="checkbox" class="filled-in" name="selectall" id="selectall" />
                            <label for="selectall"></label>
                        </div>
                     </th>
                     @endif
                     <!--  <th>Sr no</th>  --> 
                     <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('notification_from')}} </a><br />
                       
                     </th>
                     <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('user_type')}} </a><br />
                       
                     </th>

                     <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('type')}} </a><br />
                       
                     </th>
                     <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('title')}} </a><br />
                        
                     </th>
                     <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('date')}} </a><br />
                        
                     </th>                   
                      <th>{{translation('action')}}</th>
                  </tr>
                  
               </thead>
               <tbody>
                  @if(!empty($arr_notification))
                      @foreach($arr_notification as $key => $notification)
                        <tr>
                            <td>
                              @if(array_key_exists('notification.update',$arr_current_user_access) || array_key_exists('notification.delete',$arr_current_user_access))
                                  <div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_{{base64_encode($notification['id'])}}" value="{{base64_encode($notification['id'])}}" /><label for="mult_change_{{base64_encode($notification['id'])}}"></label></div>
                              @endif
                            </td>
                            <td>
                              {{ucfirst($notification['user_details']['first_name'])}} {{ucfirst($notification['user_details']['last_name'])}} 
                            </td>

                            <td>
                              {{ucfirst($notification['user_type'])}}
                            </td>

                            <td>
                              {{ucfirst($notification['notification_type'])}}
                            </td>

                            <td>
                              {{$notification['title']}}
                            </td>

                            <td>
                              {{getDateFormat($notification['created_at'])}}
                            </td>
                            <td>
                              @if(array_key_exists('notification.delete', $arr_current_user_access))               
                                <a class="red-color" href="{{$module_url_path.'/delete/'.base64_encode($notification['id'])}}" title="{{translation("delete")}}" onclick="return confirm_action(this,event,'{{translation('do_you_really_want_to_delete_this_record')}}','{{translation("are_you_sure")}}','{{translation('yes')}}','{{translation("no")}}')"><i class="fa fa-trash" ></i></a>
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
</div>
@if(array_key_exists('notification.list', $arr_current_user_access))  
 <script type="text/javascript">

      /*Script to show table data*/
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
              
              ordering:false,
              
        });
        $.fn.dataTable.ext.errMode = 'none';
      });
 </script> 
@endif
<!-- END Main Content -->
<script type="text/javascript">
  function show_details(url)
  {  
      window.location.href = url;
  }
 function filterData()
  {
    table_module.draw();
  }

</script>
@stop