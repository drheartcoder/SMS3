@extends('schooladmin.layout.master') @section('main_content')
<!-- BEGIN Breadcrumb -->

<div id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="{{ url($school_admin_panel_slug.'/dashboard') }}">{{translation('dashboard')}}</a>
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

                <div class="row module-permission-radio-section">
                    <div class="col-sm-12 col-lg-12">
                      <div class="form-group">
                            <label class="col-sm-4 col-lg-4 control-label" style="text-align: right;font-size:17px;line-height: 17px;">{{translation('module_permission')}}</label>
                            <div class="col-sm-8 col-lg-8 controls">
                                @if (array_key_exists('claim.update', $arr_current_user_access))
                                <a class="label label-info enable-button-section @if(isset($permission['is_active']) && $permission['is_active']==1) active-labl @endif" title="{{translation('enable')}}" href="{{$module_url_path.'/enable'}}"  
                                              onclick="return confirm_action(this,event,'{{translation('do_you_really_want_to_enable_this_module')}}','{{translation("are_you_sure")}}','{{translation('yes')}}','{{translation("no")}}')" >{{translation('enable')}}</a>
                                  &nbsp;&nbsp;  
                                 <a class="label label-warning disable-button-section  @if(isset($permission['is_active']) && $permission['is_active']==0) active-labl @endif" title="{{translation('disable')}}" href="{{$module_url_path.'/disable'}}" 
                                              onclick="return confirm_action(this,event,'{{translation('do_you_really_want_to_disable_this_module')}}','{{translation("are_you_sure")}}','{{translation('yes')}}','{{translation("no")}}')" >{{translation('disable')}}</a>
                                @endif              
                            </div>
                      </div>  
                    </div>
                  </div>  
                <div class="box-tool">  
                    @if (array_key_exists('claim.delete', $arr_current_user_access))
                        <a title="{{translation('multiple_delete')}}" href="javascript:void(0);" onclick='javascript : return check_multi_action("frm_manage","{{translation('are_you_sure')}}","{{translation('do_you_really_want_to_delete_this_record').'?'}}","{{translation('yes')}}","{{translation('no')}}","{{translation('oops')}}","{{translation('please_select_the_record_to_perform_this_action')}}","delete");' style="text-decoration:none;">
                            <i class="fa fa-trash-o"></i>
                        </a>
                    @endif
                    <a title="{{translation('refresh')}}" href="javascript:void(0)" onclick="javascript:location.reload();" style="text-decoration:none;">
                        <i class="fa fa-repeat"></i>
                    </a>
                </div>
            </div>
            <div class="box-content studt-padding">
                @include('schooladmin.layout._operation_status') {!! Form::open([ 'url' => $module_url_path.'/multi_action', 'method'=>'POST', 'enctype' =>'multipart/form-data', 'class'=>'form-horizontal', 'id'=>'frm_manage' ]) !!} {{ csrf_field() }}             
                <div id="loader" hidden="true">
                   <img src="{{url('/')}}/images/loader1.gif" width="300px">
                </div>
                <div class="clearfix"></div>

                    <div class="table-responsive" style="border:0">
                        <input type="hidden" name="multi_action" value="" />
                        <input type="hidden" name="search" id="search" value="" />
                        <table class="table table-advance" id="table_module">
                            <thead>
                                <tr>
                                @if (array_key_exists('claim.update', $arr_current_user_access) || array_key_exists('claim.delete', $arr_current_user_access))
                                       
                                    <th style="width: 18px; vertical-align: initial;">
                                        <div class="check-box">
                                            <input type="checkbox" class="filled-in" name="selectall" id="selectall" />
                                            <label for="selectall"></label>
                                        </div>
                                    </th>
                                 @endif   
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
                                    <th class="sorting_disabled">
                                        <a class="sort-descs" href="#" style="color:#dedede;">{{translation('status')}}</a><br>
                                        
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
              ordering:false,
              ajax: {
              'url':'{{ $module_url_path.'/get_records'}}',
              'data': function(d)
                {
                  
                }
              },
           columns: [
           @if(array_key_exists('claim.update',$arr_current_user_access) || array_key_exists('claim.delete',$arr_current_user_access) )
           {

              render : function(data, type, row, meta) 
               {
                 return row.build_checkbox;
         
               },
               "orderable": false, "searchable":false
           },
           @endif

          {data: 'level_name', "orderable": false, "searchable":false},
          {data: 'class_name', "orderable": false, "searchable":false},
          {data: 'student_name', "orderable": false, "searchable":false},
          {data: 'national_id', "orderable": false, "searchable":false},
          {data: 'title', "orderable": false, "searchable":false},
          {data: 'build_status', "orderable": false, "searchable":false},

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

function changePermisiion()
{
  var permission = $('input[name=permission]:checked').val();

  if(permission != '')
  {
     $.ajax({
          url  :"{{ $module_url_path }}/changePermission",
          type :'get',
          data :{'_token':'<?php echo csrf_token();?>','permission':permission},
          success:function(data){
          }
    });
  }
}

 </script> 


@endsection