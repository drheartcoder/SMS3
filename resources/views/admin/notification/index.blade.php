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
            <li class="active">
              {{ $module_title or ''}}
            </li>

            <span class="divider">
                <i class="fa fa-angle-right"></i>
                <i class="fa fa-list-alt"></i>                
            </span> 
            <li class="active">{{ isset($role)?translation($role):translation('all')}}</li>
           
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
                {{ isset($page_title)?$page_title:'' }}
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
            
              <a title="{{translation('multiple_delete')}}" 
                     href="javascript:void(0);" 
                     onclick='javascript : return check_multi_action("frm_manage","{{translation('are_you_sure')}}","{{translation('do_you_really_want_to_delete_this_record').'?'}}","{{translation('yes')}}","{{translation('no')}}","{{translation('oops')}}","{{translation('please_select_the_record_to_perform_this_action')}}","delete");'
                     style="text-decoration:none;">
                     <i class="fa fa-trash-o"></i>
              </a>
            
            

            
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
                  
                  
                  <th>{{translation('school_number')}}</th>
                  <th>{{translation('notification_from')}}</th> 
                  <th>{{translation('user_type')}}</th> 
                  <th>{{translation('title')}}</th> 
                  <th>{{translation('date')}}</th>
                  <th>{{translation('action')}}</th>
                </tr>
              </thead>
              <tbody>
                @if(sizeof($arr_notification)>0)
                  @foreach($arr_notification as $key=>$notification)
                  <tr>
                      <td> 
                        <div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_{{base64_encode($notification['id'])}}" value="{{base64_encode($notification['id'])}}" /><label for="mult_change_{{base64_encode($notification['id'])}}"></label></div>
                    </td> 
                    <td><?php echo isset($notification['school_id'])?$notification['school_id']:'-';  ?> </td>
                    <td> <?php echo isset($notification['user_details']['email'])?$notification['user_details']['email']:'-';  ?> </td>
                    <td> <?php echo isset($notification['user_type'])?ucfirst(str_replace('_',' ',$notification['user_type'])):'-';  ?> </td>
                    <td> {{ $notification['title'] or '' }} </td> 
                    <td> <?php echo  isset($notification['created_at'])&& $notification['created_at']!='0000-00-00 00:00:00'? getDateFormat($notification['created_at']):''; ?> </td> 

                    <td> 
                      
                        <a class="green-color" href="{{ $module_url_path.'/view/'.base64_encode($notification['id']) }}" 
                        title="{{translation('view')}}">
                        <i class="fa fa-eye" ></i>
                        </a>
                        
                      

                      
                      <a class="red-color" href="{{ $module_url_path.'/delete/'.base64_encode($notification['id']) }}" title="{{translation('delete')}}" onclick="return confirm_action(this,event,'{{translation('do_you_really_want_to_delete_this_record')}}','{{translation("are_you_sure")}}','{{translation('yes')}}','{{translation("no")}}')"><i class="fa fa-trash" ></i></a>  
                    
                      
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
          "pageLength": 10,
          ordering:false

    });        ;
  $.fn.dataTable.ext.errMode = 'none';
  
});

function show_details(url)
{ 
    window.location.href = url;
} 

$(document).ready(function(){ 
  read();
  /* call on load */

});

function read(){ 
  var type = "<?php echo Request::get('type'); ?>"; /* as its admin */ //$(this).attr('data-id');
  $.ajax({
      url : "{{ url('admin/notification/view/') }}",
      type: 'POST',
      data: {
             'type'     : type,
             '_token'   : "{{ csrf_token() }}"
            }
  }).done(function (data) {
      console.log('Success'+data.status)
      if(data.status == 'done'){
           $('.notifyCount').html(data.count);
      }

  }).fail(function () {
      console.log('Error');
  });
  
}



</script>
 
@stop                    


