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
            <a href="{{$module_url_path}}/create" >{{translation('add')}} {{translation('library_content')}}</a> 
               <a 
                  title="Refresh" 
                  href="{{ $module_url_path.'/manage_library_contents'}}"
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
            <table class="table table-advance"  id="table_module">
               <thead>
                  <tr>
                      <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('number')}}</a></th>
                      <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('title')}} </a></th>
                      <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('type')}} </a></th>
                      <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('no_of_copies')}} </a></th>
                      <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('available')}} </a></th>
                      <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('status')}} </a></th>    
                      <th>{{translation('action')}}</th>
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
              ordering:false,
              bFilter: true,
              ajax: {
              'url':'{{ $module_url_path.'/get_records'}}',
              'data': function(d)
                {
                }
              },
           columns: [
         
          {data: 'book_no', "orderable": false, "searchable":false},
          {data: 'title', "orderable": false, "searchable":false},
          {
            render : function(data, type, row, meta) 
            {
               return row.build_type;
            
            },
            "orderable": false, "searchable":false
          },
          {data: 'no_of_books', "orderable": false, "searchable":false},
          {data: 'available_books', "orderable": false, "searchable":false},
          {data: 'availability', "orderable": false, "searchable":false},
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