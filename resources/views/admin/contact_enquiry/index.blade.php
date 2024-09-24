@extends('admin.layout.master')                
@section('main_content')
<style>
.red-color{margin-bottom: 5px;}
.orange-color{margin-bottom: 5px;}
.green-color{margin-bottom: 5px;}
</style>
<!-- BEGIN Page Title -->
<link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="{{ url($admin_panel_slug.'/dashboard') }}"> {{translation('dashboard')}} </a>
        </li>
        <span class="divider">
            <i class="fa fa-angle-right"></i>
            <i class="fa fa-info-circle"></i>   
        </span> 
        <li class="active"> {{ $module_title or ''}} </li>

    </ul>
</div>


<div class="page-title new-agetitle">
    <div>
        <h1><i class="fa fa-info-circle"></i> {{ isset($module_title)?$module_title:"" }}</h1>
    </div>
</div>
<!-- END Page Title -->

<!-- BEGIN Main Content -->
<div class="row">
    <div class="col-md-12">

        <div class="box">
            <div class="box-title">
                <h3>
                    <i class="fa fa-list"></i>
                    {{ isset($page_title)?$page_title:"" }}
                </h3>
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

                    <a title="{{translation('multiple_delete')}}" 
                        href="javascript:void(0);" 
                        onclick='javascript : return check_multi_action("frm_manage","{{translation('are_you_sure')}}","{{translation('do_you_really_want_to_delete_this_record').'?'}}","{{translation('yes')}}","{{translation('no')}}","{{translation('oops')}}","{{translation('please_select_the_record_to_perform_this_action')}}","delete");' 
                        style="text-decoration:none;">
                        <i class="fa fa-trash-o"></i>
                    </a>

                    <a title="{{translation('refresh')}}" 
                        href="javascript:void(0)"
                        onclick="javascript:location.reload();" 
                        style="text-decoration:none;">
                        <i class="fa fa-repeat"></i>
                    </a>
                </div>
            </div>
            <div class="box-content">

                @include('admin.layout._operation_status')  

                {!! Form::open([ 'url' => $module_url_path.'/multi_action',
                'method'=>'POST',
                'enctype' =>'multipart/form-data',   
                'class'=>'form-horizontal', 
                'id'=>'frm_manage' 
                ]) !!} 

                {{ csrf_field() }}

                <div class="col-md-10">
                    <div id="ajax_op_status"></div>
                    <div class="alert alert-danger" id="no_select" style="display:none;"></div>
                    <div class="alert alert-warning" id="warning_msg" style="display:none;"></div>
                </div>          
                <br/>
                <div class="clearfix"></div>
                <div class="table-responsive" style="border:0">

                    <input type="hidden" name="multi_action" value="" />
                    <input type="hidden" name="search" id="search" value="" />
                    <input type="hidden" name="file_format" id="file_format" value="" />

                    <table class="table table-advance"  id="table4" >
                        <thead>
                            <tr>
                                <th style="width:18px">
                                    <div class="check-box">
                                        <input type="checkbox" class="filled-in" name="selectall" id="selectall" />
                                        <label for="selectall"></label>
                                    </div>
                                </th>
                                <th>{{translation('sr_no')}}</th>
                                <th>{{translation('contact_enquiry')}}</th> 
                                <th>{{translation('subject')}}</th>
                                <th>{{translation('school_name')}}</th>  
                                <th>{{translation('email')}}</th> 
                                <th>{{translation('phone')}}</th>
                                <th>{{translation('enquiry_number')}}</th> 

                                <th style="width: 450px;">{{translation('description')}}</th>

                                <th style="width: 130px;">{{translation('action')}}</th>

                            </tr>
                        </thead>

                        <tbody>
                            @if(sizeof($arr_contact_enquiry)>0)

                            @foreach($arr_contact_enquiry as $key=> $contact_enquiry)

                            <tr>

                                <td>
                                    <div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_{{base64_encode($contact_enquiry['id'])}}" value="{{base64_encode($contact_enquiry['id'])}}" /><label for="mult_change_{{base64_encode($contact_enquiry['id'])}}"></label></div>

                                </td>

                                <td > {{ $key+1 }} </td> 
                                <td > {{ isset($contact_enquiry['enquiry_category']['title'])?$contact_enquiry['enquiry_category']['title']:'' }} </td>  
                                <td > {{ isset($contact_enquiry['subject'])?$contact_enquiry['subject']:'' }} </td>  
                                <td > {{ isset($contact_enquiry['get_school_admin']['school_admin']['school_id'])?get_school_name($contact_enquiry['get_school_admin']['school_admin']['school_id']):'' }} </td> 
                                <td > {{ isset($contact_enquiry['email'])?$contact_enquiry['email']:'' }} </td> 
                                <td > {{ isset($contact_enquiry['contact_number'])?$contact_enquiry['contact_number']:'' }} </td> 
                                <td > {{ isset($contact_enquiry['enquiry_no'])?$contact_enquiry['enquiry_no']:'' }} </td>
                                <td > {{ isset($contact_enquiry['description'])?str_limit($contact_enquiry['description'],125):'' }} </td>   

                                <td width="150px"> 
                                    <a  class="green-color" href="{{ $module_url_path.'/view/'.base64_encode($contact_enquiry['id']) }}" 
                                        title="{{translation('view')}}">
                                        <i class="fa fa-eye" ></i>
                                    </a>

                                    <a class="orange-color" href="{{ $module_url_path.'/reply/'.base64_encode($contact_enquiry['id'])}}" title="{{translation('reply')}}"><i class="fa fa-reply" ></i>
                                    </a>                        

                                    <a class="red-color" href="{{$module_url_path.'/delete/'.base64_encode($contact_enquiry['id'])}}" title="{{translation('delete')}}" onclick="return confirm_action(this,event,'{{translation('do_you_really_want_to_delete_this_record')}}','{{translation("are_you_sure")}}','{{translation('yes')}}','{{translation("no")}}')"><i class="fa fa-trash" ></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                            @endif

                        </tbody>
                    </table>
                </div>
                <div></div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>


<!-- END Main Content -->
<script type="text/javascript">
/*$(document).ready(function() {
 var oTable = $('#table4').dataTable({
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
  "aoColumnDefs": [
                  { 
                    "bSortable": false, 
                    "aTargets": [0,1,3,4,5,6] // <-- gets last column and turns off sorting
                   } 
                ]
    "ordering":false            
    });        
 $.fn.dataTable.ext.errMode = 'none';
});*/

$(document).ready(function() {
    var oTable = $('#table4').dataTable({
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
        "aoColumnDefs": [
        { 
            "bSortable": false

        } 
        ]
    });

});
</script>

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

@stop                    


