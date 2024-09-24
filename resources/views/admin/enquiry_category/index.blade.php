@extends('admin.layout.master')                
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
    <li>
      <i class="fa fa-home"></i>
      <a href="{{ url($admin_panel_slug.'/dashboard') }}"> {{translation('dashboard')}} </a>
    </li>
    <span class="divider">
      <i class="fa fa-angle-right"></i>
      <i class="fa fa-envelope"></i>   
    </span> 
    <li class="active"> {{ $module_title or ''}} </li>
   
  </ul>
</div>
    <!-- END Page Title -->


<!-- BEGIN Page Title -->
<link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">
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
                @if(array_key_exists('enquiry_category.create', $arr_current_user_access))             
                  <a href="{{ $module_url_path.'/create' }}">{{translation('add')}} {{ str_singular($module_title) }}</a> 
                @endif      
            
                @if(array_key_exists('enquiry_category.update', $arr_current_user_access))                 
                   
                    <a title="{{translation('multiple_delete')}}" 
                       href="javascript:void(0);" 
                        onclick='javascript : return check_multi_action("frm_manage","{{translation('are_you_sure')}}","{{translation('do_you_really_want_to_delete_this_record').'?'}}","{{translation('yes')}}","{{translation('no')}}","{{translation('oops')}}","{{translation('please_select_the_record_to_perform_this_action')}}","delete");'
                       style="text-decoration:none;">
                       <i class="fa fa-trash-o"></i>
                    </a>
                @endif
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
            

            <div id="ajax_op_status">
                
            </div>
            <div class="alert alert-danger" id="no_select" style="display:none;"></div>
            <div class="alert alert-warning" id="warning_msg" style="display:none;"></div>
          </div>          
          <br/>
          <div class="clearfix"></div>
          <div class="table-responsive" style="border:0">

            <input type="hidden" name="multi_action" value="" />

            <table class="table table-advance"  id="table4" >
              <thead>
                <tr>

                  @if(array_key_exists('enquiry_category.update', $arr_current_user_access)) 
                  <th style="width:18px"> 
                    <div class="check-box">
                        <input type="checkbox" class="filled-in" name="selectall" id="selectall" />
                            <label for="selectall"></label>
                    </div>
                  </th>
                  @endif
                  <th>{{ translation('enquiry_category')}}</th> 
                  @if(array_key_exists('enquiry_category.update', $arr_current_user_access) || array_key_exists('enquiry_category.delete', $arr_current_user_access) )
                   <th>{{translation('action')}}</th>
                  @endif
                </tr>
              </thead>
              <tbody>
          
                @if(sizeof($arr_data)>0)
                  @foreach($arr_data as $data)
                  <tr>
                    @if(array_key_exists('enquiry_category.update', $arr_current_user_access))                 
                    <td> 
                     
                    <div class="check-box">
                    <input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_'{{base64_encode($data['id']) }}'" value="'{{base64_encode($data['id']) }}'" />
                    <label for="mult_change_'{{base64_encode($data['id']) }}'"></label></div>
                     
                    </td>
                    @endif

                    <td> {{ isset($data['title'])?$data['title']:'' }} </td> 
                    <td style="width: 100px;"> 
                        @if(array_key_exists('enquiry_category.update', $arr_current_user_access))                
                         <a class="orange-color" href="{{ $module_url_path.'/edit/'.base64_encode($data['id']) }}" title="{{translation('edit')}}">
                          <i class="fa fa-edit" ></i>
                        </a>  
                        @endif
                        
                        @if(array_key_exists('enquiry_category.delete', $arr_current_user_access))               
                        <a class="red-color" href="{{$module_url_path.'/delete/'.base64_encode($data['id'])}}" title="{{translation('delete')}}" onclick="return confirm_action(this,event,'{{translation('do_you_really_want_to_delete_this_record')}}','{{translation("are_you_sure")}}','{{translation('yes')}}','{{translation("no")}}')"><i class="fa fa-trash" ></i></a>
                        @endif
                    </td>
                  </tr>
                  @endforeach
                @endif
                 
              </tbody>
            </table>
          </div>
        <div> </div>
         
          {!! Form::close() !!}
      </div>
  </div>
</div>

<script type="text/javascript">

      /*Script to show table data*/
          var table_module = false;
          $(document).ready(function()
          {
            table_module = $('#table4').DataTable({
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
            });
            $.fn.dataTable.ext.errMode = 'none';
          });
  </script>

@stop                    


