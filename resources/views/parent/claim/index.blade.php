@extends('parent.layout.master') @section('main_content')
<!-- BEGIN Breadcrumb -->

<div id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="{{ url($parent_panel_slug.'/dashboard') }}">{{translation('dashboard')}}</a>
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
        <h1><i class="fa fa-file"></i>{{translation('claim')}}</h1>
    </div>
</div>
<!-- END Page Title -->

<link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">

<!-- BEGIN Tiles -->
<div class="row">
    <div class="col-md-12">
        <div class="box  box-navy_blue">
            <div class="box-title">
                <h3><i class="fa fa-list"></i>{{ isset($module_title)?$module_title:"" }}</h3>
                <div class="box-tool">
                    <a title="{{translation('refresh')}}" href="javascript:void(0)" onclick="javascript:location.reload();" style="text-decoration:none;">
                        <i class="fa fa-repeat"></i>
                    </a>
                </div>
            </div>
            <div class="box-content studt-padding">
                @include('parent.layout._operation_status') {!! Form::open([ 'url' => $module_url_path.'/multi_action', 'method'=>'POST', 'enctype' =>'multipart/form-data', 'class'=>'form-horizontal', 'id'=>'frm_manage' ]) !!} {{ csrf_field() }}             

                <div class="clearfix"></div>

                    <div class="table-responsive" style="border:0">
                        <input type="hidden" name="multi_action" value="" />
                        <input type="hidden" name="search" id="search" value="" />
                        <table class="table table-advance" id="table_module">
                            <thead>
                                <tr>  
                                    <th class="sorting_disabled">
                                        <a class="sort-descs" href="#" style="color:#dedede;">{{translation('level')}}</a><br>
                                        
                                    </th>
                                    <th class="sorting_disabled">
                                        <a class="sort-descs" href="#" style="color:#dedede;">{{translation('class')}}</a><br>
                                        
                                    </th>
                                    <th class="sorting_disabled">
                                        <a class="sort-descs" href="#" style="color:#dedede;">{{translation('student_name')}}</a><br>
                                        
                                    </th>
                                    <th class="sorting_disabled">
                                        <a class="sort-descs" href="#" style="color:#dedede;">{{translation('national_id')}}</a><br>
                                        
                                    </th>
                                    <th class="sorting_disabled">
                                        <a class="sort-descs" href="#" style="color:#dedede;">{{translation('claim_title')}}</a><br>
                                        
                                    </th>
                                    <th class="sorting_disabled"><a href="#" class="sort-descs"> {{translation('action')}} </a><br> </th>
                                </tr>
                            </thead>

                        </table>
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
              'data': function(d)
                {
                  
                }
              },
           columns: [

          {data: 'level_name', "orderable": false, "searchable":false},
          {data: 'class_name', "orderable": false, "searchable":false},
          {data: 'student_name', "orderable": false, "searchable":false},
          {data: 'national_id', "orderable": false, "searchable":false},
          {data: 'title', "orderable": false, "searchable":false},

          {
            render : function(data, type, row, meta) 
            {
              return row.build_action_btn;
            },
            "orderable": false, "searchable":false
          }
          ]
        });
        $.fn.dataTable.ext.errMode = 'none';
     });


 </script> 


@endsection