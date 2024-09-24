@extends('admin.layout.master')    
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
    <li>
      <i class="fa fa-home">
      </i>
      <a href="{{ url($admin_panel_slug.'/dashboard') }}" class="call_loader">Dashboard
      </a>
    </li>
    <span class="divider">
      <i class="fa fa-angle-right">
      </i>
      <i class="fa {{$module_icon or ''}}">
      </i>
      <a href="{{ url($module_url_path) }}" class="call_loader">{{ $module_title or ''}}
      </a>
    </span> 
    <span class="divider">
      <i class="fa fa-angle-right">
      </i>
      <i class="fa fa-list">
      </i>
    </span>
    <li class="active">{{ $page_title or ''}}
    </li>
  </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">
<div class="page-title new-agetitle">
    <div>
        <h1><i class="fa fa-list"></i> {{ isset($module_title)?$module_title:"" }}</h1>
    </div>
</div>
<!-- END Page Title -->

<!-- BEGIN Main Content -->
<div class="row">
  <div class="col-md-12">
    <div class="box ">
      <div class="box-title">
        <h3>
          <i class="fa fa-list">
          </i>
          {{ isset($page_title)?$page_title:"" }}
        </h3>
        <div class="box-tool">
          <a title="{{translation('refresh')}}" 
                 href="javascript:void(0)"
                 onclick="javascript:location.reload();" 
                 style="text-decoration:none;">
                <i class="fa fa-repeat">
                </i>
              </a> 
        </div>
      </div>
      
      
      <div class="box-content studt-padding">
        
        @include('admin.layout._operation_status') 

        <form class="form-horizontal" id="frm_manage" method="POST" action="{{ url($module_url_path.'/multi_action') }}">
          {{ csrf_field() }}          
                    
          <br/>
          <div class="clearfix">
          </div>
           <div class="row">
                  <div class="block-new-block">
                 
                    <div class="clearfix"></div>
                  </div>
                </div>
                <div class="table-responsive" style="border:0">
                  
                  <table class="table table-advance"   id="table_module">
                    <thead>
                      <tr>                          
                          <th><a class="sort-descs sort-active" href="#">{{translation('keyword')}} </a>
                          </th>
                          <th><a class="sort-descs" href="#">{{translation('title')}}</a>
                          </th>
                          <th><a class="sort-descs" href="#">{{translation('locale')}}</a>   
                          </th>
                          
                          <th width="150px"><a class="sort-descs" href="#">{{translation('action')}}</a></th>
                      </tr>
                    </thead>
                 </table>
              </div>
      <div> 
    </div>
  </form>
</div>
</div>
</div>
<!-- END Main Content -->
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
      bFilter: true ,
      ordering:false,
      ajax: {
      'url':'{{ $module_url_path.'/get_records'}}',
      'data': function(d)
        {       
        }
      },
      columns: [
      
      
      {data: 'keyword', "orderable": true, "searchable":false},
      {data: 'title', "orderable": true, "searchable":false},
      {data: 'locale', "orderable": false, "searchable":false},      
      
      
      {
        render : function(data, type, row, meta) 
        {
          return '<a class="orange-color" href="'+row.built_edit_href+'" title="{{translation('edit')}}"><i class="fa fa-edit" ></i></a>&nbsp;';
        },
        "orderable": false, "searchable":false
      }]
    });
    

    $('input.column_filter').on( 'keyup click', function () 
    {
        filterData();
    });

    $('select.column_filter').on( 'keyup click', function () 
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

  

  function filterData()
  {
    table_module.draw();
  }

  
</script>
@stop                    
