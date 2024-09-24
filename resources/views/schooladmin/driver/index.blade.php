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
      <li class="active">{{ isset($page_title)?$page_title:"" }}</li>
  </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="{{$module_icon}}"></i>{{str_plural($page_title)}}</h1>
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
            {{ isset($module_title)?$module_title:"" }}
         </h3>
         <div class="box-tool">
            @if(array_key_exists('employee.create', $arr_current_user_access))  
              <a href="{{$module_url_path}}/create" >{{translation('add')}} {{translation($role)}}</a> 
            @endif  
            <div class="dropup-down-uls">
                <a href="javascript:void(0)" class="export-lists"><i class="fa fa-upload"></i> {{ translation('export')}} </a>
                <div class="export-content-links">
                    <div class="li-list-a">
                        <a href="javascript:void(0)" onclick="exportForm('pdf');">PDF</a>
                    </div>
                    <div class="li-list-a">
                        <a href="javascript:void(0)" onclick="exportForm('csv');">CSV</a>
                    </div>
                     
                </div>
            </div>
            @if(array_key_exists('employee.update', $arr_current_user_access))  
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
            @if(array_key_exists('employee.delete', $arr_current_user_access))     
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
        
         <br/>
         <div class="clearfix"></div>
         <div class="table-responsive" style="border:0">
            <input type="hidden" name="multi_action" value="" />
            <input type="hidden" name="search" id="search" value="" />
            <input type="hidden" name="file_format" id="file_format" value="" />
            <table class="table table-advance"  id="table_module">
               <thead>
                  <tr>
                     @if(array_key_exists('employee.update', $arr_current_user_access) || array_key_exists('employee.delete', $arr_current_user_access) )
                     <th style="width: 18px; vertical-align: initial;">
                        <div class="check-box">
                            <input type="checkbox" class="filled-in" name="selectall" id="selectall" />
                            <label for="selectall"></label>
                        </div>
                     </th>
                     @endif
                    
                     <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('name')}} </a><br />
                        
                     </th>
                     <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('email')}} </a><br />
                     </th>
                     <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('license_number')}} </a><br />
                     </th>
                     <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('employee_number')}} </a><br />
                        
                     </th>
                      <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('national_id')}} </a><br />
                        
                     </th>
                     <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('gender')}} </a><br />
                     </th>

                     @if(array_key_exists('employee.list', $arr_current_user_access))     
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

@if(array_key_exists('employee.list', $arr_current_user_access))  
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
              ordering:false,
              ajax: {
              'url':'{{ $module_url_path.'/get_records/'.$role}}',
              'data': function(d)
                {
                  
                }
              },
           columns: [
           {
              render : function(data, type, row, meta) 
               {
                 return row.build_checkbox;
         
               },
               "orderable": false, "searchable":false
           },

          {data: 'user_name', "orderable": false, "searchable":false},
          {data: 'email', "orderable": false, "searchable":false},
          {data: 'license_no', "orderable": false, "searchable":false},
          {data: 'employee_no', "orderable": false, "searchable":false},
          {data: 'national_id', "orderable": false, "searchable":false},
          {data: 'gender', "orderable": false, "searchable":false},

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
<!-- To Export The Data -->
<script type="text/javascript">
  function exportForm(file_format)
  {
    document.getElementById('file_format').value = file_format;
    var serialize_form   = $("#frm_manage").serialize();
    window.location.href = '{{ $module_url_path }}/export?'+serialize_form+'&export=true';
  }
  $(document).on("change","[type='search']",function(){
      var search_hidden = $(this).val();
      document.getElementById('search').value = search_hidden;
   });
</script>
<!-- To Export The Data -->
@stop