@extends('student.layout.master') @section('main_content')
<!-- BEGIN Breadcrumb -->

<div id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="{{ url($student_panel_slug.'/dashboard') }}">{{translation('dashboard')}}</a>
        </li>
        <span class="divider">
                    <i class="fa fa-angle-right"></i>
                </span>
        <li class="active">{{ isset($module_title)?$module_title:"" }}</li>
    </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="{{$module_icon}}"></i>{{translation('survey')}}</h1>
    </div>
</div>
<!-- END Page Title -->

<link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">

<!-- BEGIN Tiles -->
<div class="row">
    <div class="col-md-12">
        <div class="box  box-navy_blue">
            <div class="box-title">
                <h3><i class="fa fa-list"></i>{{ isset($page_title)?$page_title:"" }}</h3>
                <div class="box-tool">
                   <a title="{{translation('refresh')}}" href="javascript:void(0)" onclick="javascript:location.reload();" style="text-decoration:none;">
                        <i class="fa fa-repeat"></i>
                    </a>
                </div>
            </div>
            <div class="box-content studt-padding">
                @include('professor.layout._operation_status')  
                <div class="clearfix"></div>

                    <div class="table-responsive" style="border:0">
                        <input type="hidden" name="multi_action" value="" />
                        <input type="hidden" name="search" id="search" value="" />
                        <table class="table table-advance" id="table_module">
                            <thead>
                                 <tr>
                                     <th><a  class="sort-descs" href="#" >{{translation('survey_title')}} </a></th>
                                     <th><a  class="sort-descs" href="#" >{{translation('start_date')}} </a></th>
                                     <th><a  class="sort-descs" href="#" >{{translation('end_date')}} </a></th>
                                     @if(array_key_exists('survey.update', $arr_current_user_access) || array_key_exists('survey.delete', $arr_current_user_access))     
                                      <th><a  class="sort-descs" href="#" >{{translation('action')}}</a></th>
                                     @endif
                                  </tr>
                            </thead>

                        </table>
                    </div>

              
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
              },
           columns: [
          {data: 'survey_title', "orderable": false, "searchable":false},
          {data: 'start_date', "orderable": false, "searchable":false},
          {data: 'end_date', "orderable": false, "searchable":false}
            @if(array_key_exists('survey.delete', $arr_current_user_access))    
           ,{
              render : function(data, type, row, meta) 
               {
                 return row.build_action_btn;
         
               },
               "orderable": false, "searchable":false
           }
           @endif
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


@endsection