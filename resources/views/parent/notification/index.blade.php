@extends('parent.layout.master')                
@section('main_content')
<link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">
 

        <!-- BEGIN Breadcrumb -->
        <div id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="fa fa-home"></i>
                    <a href="{{ url($parent_panel_slug.'/dashboard') }}">{{translation('dashboard')}}</a>
                </li>
                <span class="divider">
                    <i class="fa fa-angle-right"></i>
                    <i class="fa fa-bell"></i>
                </span>
                <li class="active">{{ isset($module_title)?$module_title:"" }}</li>
            </ul>
        </div>
        <!-- END Breadcrumb -->
         <!-- BEGIN Page Title -->
        <div class="page-title new-agetitle">
            <div>
                <h1><i class="fa fa-bell"></i>{{ isset($module_title)?$module_title:"" }}</h1>
            </div>
        </div>
        <!-- END Page Title -->
        <!-- BEGIN Tiles -->
        <div class="row">
            <div class="col-md-12">
                <div class="box  box-navy_blue">
                    <div class="box-title">
                        <h3><i class="fa fa-list"></i>{{ isset($page_title)?$page_title:"" }}</h3>
                        <div class="box-tool">
                            <a href="javascript:void(0)"> <span class="cog-icon-block"> <i class="fa fa-cog"></i></span></a>
                        </div>
                    </div>
                    <div class="box-content studt-padding">
                         @include('parent.layout._operation_status')  
                         {!! Form::open([ 'url' => $module_url_path.'/multi_action',
                         'method'=>'POST',
                         'enctype' =>'multipart/form-data',   
                         'class'=>'form-horizontal', 
                         'id'=>'frm_manage' 
                         ]) !!}
                         {{ csrf_field() }}
                         
                            

                            <div class="btn-toolbar pull-right clearfix">
                               <div class="box-tool">  
              
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
                                  
                                  
                             </div>
                            </div>
                            <br/>
                            <br/>
                            <br/>
                            <div class="clearfix">
                            </div>                            
                            <div class="border-box">
                                <div class="table-responsive" style="border:0">
                                    <input type="hidden" name="multi_action" value="" />
                                    <table class="table table-advance" id="table_module">
                                        <thead>
                                          <tr>
                                                                      
                                             <th style="width: 18px; vertical-align: initial;">
                                                <div class="check-box">
                                                    <input type="checkbox" class="filled-in" name="selectall" id="selectall" />
                                                    <label for="selectall"></label>
                                                </div>
                                             </th>
                                             
                                             <!--  <th>Sr no</th>  --> 
                                             <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('notification_from')}} </a><br />
                                               
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
                                                     
                                                      <div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_{{base64_encode($notification['id'])}}" value="{{base64_encode($notification['id'])}}" /><label for="mult_change_{{base64_encode($notification['id'])}}"></label></div>
                                                      
                                                    </td>
                                                    <td>
                                                      {{ucfirst($notification['user_details']['first_name'])}} {{ucfirst($notification['user_details']['last_name'])}} 
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
                                                                 
                                                        <a class="red-color" href="{{$module_url_path.'/delete/'.base64_encode($notification['id'])}}" title="Delete" onclick="return confirm_action(this,event,'{{translation('do_you_really_want_to_delete_this_record')}}','{{translation("are_you_sure")}}','{{translation('yes')}}','{{translation("no")}}')"><i class="fa fa-trash" ></i></a>
                                                      
                                                    </td>
                                                </tr>
                                              @endforeach
                                          @endif
                                       </tbody>
                                    </table>
                                </div>

                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
<!-- END Main Content -->
</div>

 <script type="text/javascript">

      /*Script to show table data*/
          var table_module = false;
          $(document).ready(function()
          {
            table_module = $('#table_module').DataTable({
              
                "aoColumnDefs": [
                                { 
                                  "bSortable": false, 
                                  "aTargets": [0,1,2,3,4,5] // <-- gets last column and turns off sorting
                                 } 
                              ]
                   
              <?php  if(Session::get('locale') == 'fr'){ ?>  
                   ,language: {
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
        });
        $.fn.dataTable.ext.errMode = 'none';
      });
 </script> 
 {{ csrf_field() }}
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
$(document).ready(function(){ 
  read();
  /* call on load */
});

function read(){ 
  var type = "<?php echo Request::get('type'); ?>"; /* as its admin */ 
  $.ajax({
      headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
      url : "{{ url('parent/notification/view/') }}",
      type: 'POST',
      data: {'type'     : type}
  }).done(function (data) {
      console.log('Success'+data.status)
       

  }).fail(function () {
      console.log('Error');
  });
  
}

</script>
@stop