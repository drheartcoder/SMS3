@extends('schooladmin.layout.master')                
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
      <li>
          <i class="fa fa-home"></i>
          <a href="{{ url($school_admin_panel_slug.'/dashboard') }}">{{translation('dashboard')}}</a>
      </li>
      <span class="divider">
          <i class="fa fa-angle-right"></i>
          <i class="{{$module_icon}}"></i>                
      </span> 
      <li class="active">{{ isset($page_title)?$page_title:"" }}</li>
  </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">
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
            {{ isset($module_title)?str_plural($module_title):"" }}
         </h3>
         <div class="box-tool">
           @if(array_key_exists('exam_period.create', $arr_current_user_access)) 
            <a href="{{$module_url_path}}/create" >{{translation('add')}} {{translation('exam_period')}}</a> 
             @endif
            @if(array_key_exists('exam_period.update', $arr_current_user_access))  
            <a title="{{translation('multiple_activate')}}" 
                href="javascript:void(0);" 
                onclick='javascript : return check_multi_action("frm_manage","{{translation('are_you_sure')}}","{{translation('do_you_really_want_to_activate_this_record').'?'}}","{{translation('yes')}}","{{translation('no')}}","{{translation('oops')}}","{{translation('please_select_the_record_to_perform_this_action')}}","activate");'
                style="text-decoration:none;">
               <i class="fa fa-unlock"></i>
            </a> 
            <a title="{{translation('multiple_deactivate')}}" 
              href="javascript:void(0);" 
              onclick='javascript : return check_multi_action("frm_manage","{{translation('are_you_sure')}}","{{translation('do_you_really_want_to_deactivate_this_record').'?'}}","{{translation('yes')}}","{{translation('no')}}","{{translation('oops')}}","{{translation('please_select_the_record_to_perform_this_action')}}","deactivate");'
              style="text-decoration:none;">
            <i class="fa fa-lock"></i>
            </a> 
            @endif
            @if(array_key_exists('exam_period.delete',$arr_current_user_access))     
               <a title="{{translation('multiple_delete')}}" 
                  href="javascript:void(0);" 
                  onclick='javascript : return check_multi_action("frm_manage","{{translation('are_you_sure')}}","{{translation('do_you_really_want_to_delete_this_record').'?'}}","{{translation('yes')}}","{{translation('no')}}","{{translation('oops')}}","{{translation('please_select_the_record_to_perform_this_action')}}","delete");'
                  style="text-decoration:none;">
               <i class="fa fa-trash-o"> </i>
               </a>
            @endif
               <a 
                  title="Refresh" 
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
            <div id="ajax_op_status">
            </div>
            <div class="alert alert-danger" id="no_select" style="display:none;"></div>
            <div class="alert alert-warning" id="warning_msg" style="display:none;"></div>
         </div>
         <div class="btn-toolbar pull-right clearfix"> </div>
         <div class="clearfix"></div>
         <div class="table-responsive" style="border:0">
            <input type="hidden" name="multi_action" value="" />
            <table class="table table-advance"  id="table_module">
               <thead>
                  <tr>
                     @if(array_key_exists('exam_period.update', $arr_current_user_access) || array_key_exists('exam_period.delete', $arr_current_user_access) )                             
                     <th style="width: 18px; vertical-align: initial;">
                        <div class="check-box">
                            <input type="checkbox" class="filled-in" name="selectall" id="selectall" />
                            <label for="selectall"></label>
                        </div>
                     </th>
                     @endif
                     <!--  <th>Sr no</th>  --> 
                     <th>{{translation('exam_period')}}
                     </th>

                    @if(array_key_exists('exam_period.update', $arr_current_user_access) || array_key_exists('exam_period.delete', $arr_current_user_access) )
                     <th width="200px">{{translation('action')}}</th>
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
<script type="text/javascript">
   function show_details(url)
   {
     window.location.href = url;
   }
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
       ordering:false,                                                                                      
       ajax: {
       'url':'{{ $module_url_path.'/get_records'}}',
       'data': function(d)
         {
        
         }
       },
       columns: [
       @if(array_key_exists('exam_period.update', $arr_current_user_access) || array_key_exists('exam_period.delete', $arr_current_user_access) ) 
       {
         render : function(data, type, row, meta) 
         {
           return row.build_checkbox;
   
         },
         "orderable": false, "searchable":false
       },
       @endif 
       /*this data comes from the controller */
       {data: 'exam_name', "orderable": false, "searchable":false},
       @if(array_key_exists('exam_period.update', $arr_current_user_access) || array_key_exists('exam_period.delete', $arr_current_user_access) )
       {
         render : function(data, type, row, meta) 
         {
           return row.build_action_btn;
   
         },
         "orderable": false, "searchable":false
       }
       @endif]
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
   
   function filter_result_by_type()
   {
     filterData();
   }
   
   function filterData()
   {
     table_module.draw();
   }
   
</script>
@stop

