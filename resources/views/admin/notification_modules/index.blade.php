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
                <i class="fa fa-list-alt"></i>                
            </span> 
            <li class="active">{{ $module_title or ''}}</li>
           
        </ul>
      </div>
    <!-- END Breadcrumb -->


<!-- BEGIN Page Title -->

<div class="page-title new-agetitle">
    <div>
        <h1><i class="fa fa-bell"></i> {{ isset($module_title)?$module_title:"" }}</h1>
    </div>
</div>

<!-- END Page Title -->

    <!-- BEGIN Main Content -->
    <div class="row">
      <div class="col-md-12">

          <div class="box {{$theme_color}}">
            <div class="box-title">
              <h3>
                <i class="fa fa-list"></i>
                {{ isset($page_title)?$page_title:"" }}
            </h3>
            <div class="box-tool">
                <a data-action="collapse" href="#"></a>
                <a data-action="close" href="#"></a>
            </div>
        </div>
        <div class="box-content view-details-seciton-main details-section-main-block">
          
          @include('admin.layout._operation_status')  
          
          <form class="form-horizontal" id="frm_manage" method="POST" action="{{ url($module_url_path.'/multi_action') }}">

            {{ csrf_field() }}

            <div class="col-md-10">
            

            <div id="ajax_op_status">
                
            </div>
            <div class="alert alert-danger" id="no_select" style="display:none;"></div>
            <div class="alert alert-warning" id="warning_msg" style="display:none;"></div>
          </div>
          <div class="btn-toolbar pull-right clearfix">

            <div class="box-tool">  
            
              @if(array_key_exists('notification_modules.update', $arr_current_user_access))     
              <a title="{{translation('multiple_activeunblock')}}" 
                href="javascript:void(0);" 
                onclick='javascript : return check_multi_action("frm_manage","{{translation('are_you_sure')}}","{{translation('do_you_really_want_to_activate_this_record')}}","{{translation('yes')}}","{{translation('no')}}","{{translation('oops')}}","{{translation('please_select_the_record_to_perform_this_action')}}","activate");' 
                style="text-decoration:none;">
              <i class="fa fa-unlock"></i>
              </a> 
              <a title="{{translation('multiple_deactiveblock')}}" 
                href="javascript:void(0);" 
                onclick='javascript : return check_multi_action("frm_manage","{{translation('are_you_sure')}}","{{translation('do_you_really_want_to_deactivate_this_record')}}","{{translation('yes')}}","{{translation('no')}}","{{translation('oops')}}","{{translation('please_select_the_record_to_perform_this_action')}}","deactivate");'
                style="text-decoration:none;">
              <i class="fa fa-lock"></i>
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
          <br/><br/>
          <div class="clearfix"></div>

          <div class="table-responsive" style="border:0">

            <input type="hidden" name="multi_action" value="" />

            <table class="table table-advance"  id="table_module">
              <thead>
                <tr>
                  <th style="width: 18px; vertical-align: initial;">
                        <div class="check-box">
                            <input type="checkbox" class="filled-in" name="selectall" id="selectall" />
                            <label for="selectall"></label>
                        </div>
                  </th>
                  <th>{{translation('module_title')}}</th>
                  <th>{{translation('action')}}</th>
                </tr>
              </thead>
              <tbody>
                @if(sizeof($arr_modules)>0)
                  @foreach($arr_modules as $key=>$module)
                  <tr>
                      <td> 
                        <div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_{{base64_encode($module['id'])}}" value="{{base64_encode($module['id'])}}" /><label for="mult_change_{{base64_encode($module['id'])}}"></label></div>
                    </td> 
                    <td><?php echo isset($module['module_title'])?translation($module['module_title']):'-';  ?> </td>
                    <td> 
                      
                        @if($module['is_active'] != null && $module['is_active'] == "0")
                           
                            <a class="blue-color" href="{{$module_url_path}}/activate/{{base64_encode($module['id'])}}" 
                            onclick="return confirm_action(this,event,'{{translation('do_you_really_want_to_activate_this_record')}}?','{{translation("are_you_sure")}}','{{translation('yes')}}','{{translation("no")}}')" title="{{translation('activate')}}" ><i class="fa fa-lock"></i></a>
                        
                        @elseif($module['is_active'] != null && $module['is_active'] == "1")
                        
                            <a class="light-blue-color" title="{{translation('deactivate')}}" href="{{$module_url_path}}/deactivate/{{base64_encode($module['id'])}}" onclick="return confirm_action(this,event,'{{translation('do_you_really_want_to_deactivate_this_record')}} ?','{{translation("are_you_sure")}}','{{translation('yes')}}','{{translation("no")}}')" ><i class="fa fa-unlock"></i></a>

                        @endif                   
                      
                     </td> 
                     
                  </tr>
                  @endforeach
                @endif
                 
              </tbody>
            </table>
          </div>
        <div> </div>
         
          </form>
      </div>
  </div>
</div>

<!-- END Main Content -->
<script type="text/javascript">
  $(document).ready(function()
  {
    $('#table_module').DataTable({

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

    });        ;
  $.fn.dataTable.ext.errMode = 'none';
  
});
</script>
 
@stop                    


