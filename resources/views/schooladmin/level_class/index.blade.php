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
            {{ isset($page_title)?str_plural($page_title):"" }}
         </h3>
         <div class="box-tool">
          @if(array_key_exists('level_class.create', $arr_current_user_access)) 
            <a href="{{$module_url_path}}/create" >{{translation('add')}} {{translation('level_class')}}</a> 
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
            <table class="table table-advance"  id="table_module">
               <thead>
                  <tr>
                      <th>{{translation('sr_no')}}.</th>  
                     <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('level_name')}} </a>
                     </th>
                     <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('class_name')}} </a>
                     </th>

                     @if(array_key_exists('level_class.update', $arr_current_user_access) || array_key_exists('level_class.delete', $arr_current_user_access))     
                      <th width="200px">{{translation('action')}}</th>
                     @endif
                  </tr>
               </thead>
               <tbody>
                  <?php $no=0; ?>
                  @if(isset($details) && sizeof($details)>0 && isset($arr_level) && sizeof($arr_level)>0)

                    @foreach($arr_level as $level_key => $level)
                      
                                  <tr>
                                    <td width="70px">{{++$no}}</td>
                                    <td>{{$level['level_details']['level_name']}}</td>

                                  <td> 
                                    <?php $i = 0 ?>
                                    @foreach($details as $key => $detail)
                                        @if($detail['level_id'] == $level['level_id'])
                                           
                                            {{$detail['class_details']['class_name']}}<br/>
                                           
                                        @endif
                                    @endforeach
                                  </td>
                                  @if(array_key_exists('level_class.update', $arr_current_user_access) || array_key_exists('level_class.delete', $arr_current_user_access))     
                                  <td>
                                    
                                      @if(array_key_exists('level_class.update', $arr_current_user_access))     
                                        <a class="orange-color" href="{{ $module_url_path.'/edit/'.base64_encode($level['level_id']) }}" title="{{translation("edit")}}">
                                            <i class="fa fa-edit" >
                                            </i>
                                        </a>  
                                      @endif
                                      @if(array_key_exists('level_class.delete', $arr_current_user_access))               
                                        <a class="red-color" href="{{$module_url_path.'/delete_class/'.base64_encode($level['level_id'])}}" title="{{translation("delete")}}" onclick="return confirm_action(this,event,'{{translation('do_you_really_want_to_delete_this_record')}}','{{translation("are_you_sure")}}','{{translation('yes')}}','{{translation("no")}}')"><i class="fa fa-trash" ></i></a>
                                      @endif  
                                    
                                  </td>
                                  @endif
                              </tr>
                          
                    @endforeach
                  @endif
               </tbody>
            </table>
         </div>
         {!! Form::close() !!}
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
          fitering:false,
          sorting:false,
          ordering:false     
        });
        $.fn.dataTable.ext.errMode = 'none';
      });
 </script> 

<!-- END Main Content -->
@stop