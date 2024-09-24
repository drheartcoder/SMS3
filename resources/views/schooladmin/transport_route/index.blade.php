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
            
            <a href="{{$module_url_path}}/create" >{{translation('add')}} {{translation('transport_route')}}</a> 
            @if(array_key_exists('transport_route.delete',$arr_current_user_access))     
               <a title="{{translation('multiple_delete')}}" 
                  href="javascript:void(0);" 
                  onclick='javascript : return check_multiple_delete("frm_manage","{{translation('are_you_sure')}}","{{translation('do_you_really_want_to_delete_this_record').'?'}}","{{translation('yes')}}","{{translation('no')}}","{{translation('oops')}}","{{translation('please_select_the_record_to_perform_this_action')}}","delete");'
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
         {!! Form::open([ 'url' => $module_url_path.'/multiple_delete',
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
            <input type="hidden" name="multiple_delete" value="" />
            <table class="table table-advance"  id="table_module">
               <thead>
                  <tr>
                     @if(array_key_exists('transport_route.delete', $arr_current_user_access))                             
                     <th style="width: 18px; vertical-align: initial;">
                        <div class="check-box">
                            <input type="checkbox" class="filled-in" name="selectall" id="selectall" />
                            <label for="selectall"></label>
                        </div>
                     </th>
                     @endif
                     <th><a  href="#" class="sort-descs" style="color:#dedede;">{{translation('bus_number')}} </a></th>
                     <th><a  href="#" class="sort-descs" style="color:#dedede;">{{translation('bus_plate_number')}} </a></th>
                     <th><a  href="#" class="sort-descs" style="color:#dedede;">{{translation('transport_type')}} </a></th>
                     <th><a  href="#" class="sort-descs" style="color:#dedede;">{{translation('route_name')}} </a></th>
                     <th><a  href="#" class="sort-descs" style="color:#dedede;">{{translation('target_location')}} </a></th>
                     <th><a  href="#" class="sort-descs" style="color:#dedede;">{{translation('total_stops')}} </a></th>
                     

                     @if(array_key_exists('transport_route.update', $arr_current_user_access) || array_key_exists('transport_route.delete', $arr_current_user_access))     
                      <th>{{translation('action')}}</th>
                     @endif
                  </tr>
               </thead>
            </table>
         </div>
         {!! Form::close() !!}
      </div>
    </div>
   </div>
</div>

@if(array_key_exists('transport_route.update', $arr_current_user_access))  
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
              processing: true,
              serverSide: true,
              autoWidth: false,
              bFilter: true,
              ajax: {
              'url':'{{ $module_url_path.'/get_records'}}',
              },
           columns: [
          @if(array_key_exists('transport_route.delete', $arr_current_user_access))    
           {
              render : function(data, type, row, meta) 
               {
                 return row.build_checkbox;
         
               },
               "orderable": false, "searchable":false
           },
           @endif
          {data: 'bus_no', "orderable": false, "searchable":false},
          {data: 'bus_plate_no', "orderable": false, "searchable":false},
          {data: 'transport_type', "orderable": false, "searchable":false},
          {data: 'route_name', "orderable": false, "searchable":false},
          {data: 'target_location', "orderable": false, "searchable":false},
          {data: 'total_stops', "orderable": false, "searchable":false},  
          
          {
            render : function(data, type, row, meta) 
            {
              return row.build_action_btn;
            },
            "orderable": false, "searchable":false
          }
          
          ]
        });

        $('input.column_filter').on( 'keyup click', function () 
        {
            filterData();
        });

        $('#table_module').on('draw.dt',function(event)
        {
          var oTable = $('#table_module').dataTable();
          var recordLength = oTable.fnGetData().length;
          $('#record_count').html(recordLength);
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

<script type="text/javascript">
  /*---------- Multiple Delete -----------------*/

  function check_multiple_delete(frm_id,title,confirmation_msg,confirm,cancel,oops,oops_msg,action)
  {
    // var len = $('input[name="'+checked_record+'"]:checked').length;

    var len = $('input[name="checked_record[]"]:checked').length;
    var flag=1;
    var frm_ref = $("#"+frm_id);
    
    if(len<=0)
    {
      swal(oops+" ...",oops_msg);
      return false;
    }
    
    swal({
          title: title,
          text: confirmation_msg,
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: confirm,
          cancelButtonText: cancel,
          closeOnConfirm: true,
          closeOnCancel: true
        },
        function(isConfirm)
        {

          if(isConfirm)
          {
            $('input[name="multiple_delete"]').val(action);
            $(frm_ref)[0].submit();
          }
          else
          {
           return false;
          }
        }); 
  }
</script>

@stop