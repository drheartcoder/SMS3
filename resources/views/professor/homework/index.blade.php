@extends('professor.layout.master') @section('main_content')
<!-- BEGIN Breadcrumb -->

<div id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="{{ url($professor_panel_slug.'/dashboard') }}">{{translation('dashboard')}}</a>
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
        <h1><i class="fa fa-book"></i>{{$page_title}}</h1>
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

                    <div class="dropup-down-uls">
                        <a href="javascript:void(0)" class="export-lists"><i class="fa fa-upload"></i> {{ translation('export')}} </a>
                        <div class="export-content-links">
                            <div class="li-list-a">
                                <a href="javascript:void(0)" onclick="exportForm('pdf');">{{translation('pdf')}}</a>
                            </div>
                            <div class="li-list-a">
                                <a href="javascript:void(0)" onclick="exportForm('{{config("app.project.export_file_formate")}}');">{{translation('excel')}}</a>
                            </div>
                        </div>
                    </div>

                    @if (array_key_exists('homework.create', $arr_current_user_access))
                        <a href="{{ $module_url_path.'/create' }}">{{translation('add')}} {{ $page_title }}</a>
                    @endif
                    <a title="{{translation('refresh')}}" href="javascript:void(0)" onclick="javascript:location.reload();" style="text-decoration:none;">
                        <i class="fa fa-repeat"></i>
                    </a>
                </div>
            </div>
            <div class="box-content studt-padding">
                @include('professor.layout._operation_status') {!! Form::open([ 'url' => $module_url_path.'/multi_action', 'method'=>'POST', 'enctype' =>'multipart/form-data', 'class'=>'form-horizontal', 'id'=>'frm_manage' ]) !!} {{ csrf_field() }}             

                <div class="clearfix"></div>

                    <div class="table-responsive" style="border:0">
                        
                        <input type="hidden" name="multi_action" value="" />
                        <input type="hidden" name="search" id="search" value="" />
                        <input type="hidden" name="file_format" id="file_format" value="" />
                        
                        <table class="table table-advance" id="table_module">
                            <thead>
                                <tr> 
                                    <th class="sorting_disabled">
                                        <a class="sort-descs" >{{translation('level')}}</a><br>
                                        
                                    </th>
                                    <th class="sorting_disabled">
                                        <a class="sort-descs" href="#" style="color:#dedede;">{{translation('class')}}</a><br>
                                        
                                    </th>
                                    <th class="sorting_disabled">
                                        <a class="sort-descs" href="#" style="color:#dedede;">{{translation('course')}}</a><br>
                                        
                                    </th>
                                    <th class="sorting_disabled">
                                        <a class="sort-descs" href="#" style="color:#dedede;">{{translation('homework_details')}}</a><br>
                                        
                                    </th>
                                    
                                    <th class="sorting_disabled">
                                        <a class="sort-descs" href="#">{{translation('added_date')}}</a><br>
                                        
                                    </th>

                                    <th class="sorting_disabled">
                                        <a class="sort-descs" href="#">{{translation('due_date')}}</a><br>
                                        
                                    </th>
                                    <th class="sorting_disabled">
                                        <a class="sort-descs" href="#">{{translation('status')}}</a><br>
                                    </th>
                                    <th class="sorting_disabled">
                                        <a class="sort-descs" href="#">{{translation('action')}}</a><br>
                                    </th>
                                </tr>
                            </thead>

                        </table>
                    </div>
<!--                </div>-->
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <!-- END Main Content -->
</div>

<script type="text/javascript">
    function show_details(url) {
        window.location.href = url;
    }
    var table_module = false;
    $(document).ready(function () {
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
                        "sFirst": "Premier",
                        "sLast": "Dernier",
                        "sNext": "Suivant",
                        "sPrevious": "Précédent"
                    },
                    "oAria": {
                        "sSortAscending": ": Trier par ordre croissant",
                        "sSortDescending": ": Trier par ordre décroissant"
                    }
                },
                <?php } ?>
                processing: true,
                serverSide: true,
                autoWidth: false,
                bFilter: true,
                ordering:false,
                ajax: {
                    'url': '{{ $module_url_path.'/get_records'}}'
                    
                },
                columns: [
        {
            data: 'level_name',
            "orderable": false,
            "searchable": false
        },
        {
            data: 'class_name',
            "orderable": false,
            "searchable": false
        },
        {
            data: 'course_name',
            "orderable": false,
            "searchable": false
        },
        {
            data: 'description',
            "orderable": false,
            "searchable": false
        },
        {
            render: function (data, type, row, meta) {
                return row.added_date;

            },
            "orderable": false,
            "searchable": false
        },
        {
            render: function (data, type, row, meta) {
                return row.due_date;

            },
            "orderable": false,
            "searchable": false
        },
        
        {
            render: function (data, type, row, meta) {
                return row.build_status;

            },
            "orderable": false,
            "searchable": false
        },
        {
            render: function (data, type, row, meta) {
                return row.build_action;

            },
            "orderable": false,
            "searchable": false
        }
      ]
        });
        $('input.column_filter').on('keyup click', function () {
            filterData();
        });

        $('#table_module').on('draw.dt', function (event) {
            var oTable = $('#table_module').dataTable();
            var recordLength = oTable.fnGetData().length;
            $('#record_count').html(recordLength);
        });
        $.fn.dataTable.ext.errMode = 'none';
    });

    function filter_result_by_type() {
        filterData();
    }

    function filterData() {
        table_module.draw();
    }

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


@endsection