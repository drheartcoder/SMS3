@extends('admin.layout.master')                
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
    <li>
      <i class="fa fa-home">
      </i>
      <a href="{{ url($admin_panel_slug.'/dashboard') }}" class="call_loader"> {{translation("dashboard")}}
      </a>
    </li>
    <span class="divider">
      <i class="fa fa-angle-right">
      </i>
      <i class="fa {{$module_icon or ''}}">
      </i>
      <a href="{{ url($module_url_path) }}" class="call_loader"> {{ $module_title or ''}}
      </a>
    </span>
  </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">
<div class="page-title new-agetitle">
    <div>
        <h1><i class="fa {{$module_icon or ''}}"></i> {{ isset($module_title)?str_singular($module_title):"" }}</h1>
    </div>
</div>
<!-- END Page Title -->

<!-- BEGIN Main Content -->
<div class="row">
  <div class="col-md-12">
    <div class="box ">
      <div class="box-title">
        <h3>
          <i class="fa fa-list"></i>
          {{ isset($page_title)?$page_title:"" }}
        </h3>
        <div class="box-tool">
          <a data-action="collapse" href="#">
          </a>
          <a data-action="close" href="#">
          </a>
          <a title="{{translation("refresh")}}"  href="javascript:void(0)" onclick="javascript:location.reload();" style="text-decoration:none;">
                <i class="fa fa-repeat">
                </i>
              </a> 
        </div>
      </div>
      
      
      <div class="box-content studt-padding">
        


        @include('admin.layout._operation_status') 


        <form class="form-horizontal" id="frm_manage" method="POST" action="{{ url($module_url_path.'/multi_action') }}">
          {{ csrf_field() }}
          
          <div class="btn-toolbar pull-right clearfix">
            <div class="btn-group">
              <!-- 
              <a href="{{ url($module_url_path.'/create') }}" class="btn btn-primary btn-add-new-records" title="Add New Category/Sub Category">Add New Category/Sub Category
              </a>  -->
            </div>
<!--
            <div class="btn-group"> 
              <a class="btn btn-circle btn-to-success btn-bordered btn-fill show-tooltip call_loader refrash-btns" 
                 title="{{translation("refresh")}}" 
                 href="javascript:void(0)"
                 onclick="javascript:location.reload();" 
                 style="text-decoration:none;">
                <i class="fa fa-repeat">
                </i>
              </a> 
            </div>
-->
          </div>
          <br/>
          <br/>
          <br/>
          <div class="clearfix">
          </div>
           <div class="row">
                  <div class="block-new-block">
                  <div class="col-sm-4 col-lg-2 controls">
                  </div>
                  <div class="col-sm-4 col-lg-2 controls">
                  </div>
                  <div class="col-sm-4 col-lg-2 controls">
                  </div>
                  <div class="col-sm-4 col-lg-2 controls">
                  </div>
                  <div class="col-sm-4 col-lg-2 controls">
                    
                  </div>
                  <div class="col-sm-4 col-lg-2 controls">
                    
                  </div>
                    <div class="clearfix"></div>
                  </div>
                </div>
          <div class="border-box">   
          <div class="table-responsive" style="border:0">
            <input type="hidden" name="multi_action" value="" />
            <table class="table table-advance"   id="table_module">
              <thead>
                <tr>
                    <th>
                      <a class="sort-descs"> {{translation('date')}} </a>
                    </th>
                    <th>
                      <a class="sort-descs"> {{translation('module_name')}}</a>
                    </th>
                    <th>
                      <a class="sort-descs">{{translation('user_name')}}</a> 
                    </th>
                    <th><a class="sort-descs">{{translation('action_performed')}}</a>
                    </th>
                </tr>
              </thead>
           </table>
        </div>
     
    </div>
  </form>
</div>
</div>
</div>
<!-- END Main Content -->
<script type="text/javascript">
  
  $('#date').datepicker({ 
  dateFormat: "yy-mm-dd"
  });
  
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
      ordering:false,
      bFilter: true ,
      ajax: {
      'url':'{{ $module_url_path.'/get_records'}}',
      'data': function(d)
        {
         
        }
      },
      columns: [
      
      {data: 'date', "orderable": true, "searchable":false},
      {data: 'module_name', "orderable": true, "searchable":false},
      {data: 'user_name', "orderable": true, "searchable":false},
      
      {data: 'action', "orderable": false, "searchable":false},
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

  function confirm_delete()
  {
    if(confirm('Are you sure to delete this record?'))
    {
      return true;
    }
    return false;
  }

  function filterData()
  {
    table_module.draw();
  }

  /*function ratingsfilter(ref)
  {
      $('#frm_star_filter').submit();
  }*/
</script>
  @stop                    
