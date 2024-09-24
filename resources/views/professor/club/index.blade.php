@extends('professor.layout.master')                
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
    <li>
      <i class="fa fa-home"></i>
      <a href="{{ url($professor_panel_slug.'/dashboard') }}"> {{translation('dashboard')}} </a>
    </li>
    <span class="divider">
      <i class="fa fa-angle-right"></i>
      <i class="{{$module_icon}}"></i>   
    </span> 
    <li class="active"> {{ $module_title or ''}} </li>
   
  </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="{{$module_icon}}"></i>{{$page_title}}</h1>
    </div>
</div>
<!-- END Page Title -->

<!-- BEGIN Main Content -->
<div class="row">
  <div class="col-md-12">
    <div class="box {{ $theme_color }}">
      <div class="box-title">
        <h3>
          <i class="fa fa-list"></i>
          {{ isset($module_title)?$module_title:"" }}
        </h3>
        <div class="box-tool">
               <a 
                  title="{{translation('refresh')}}" 
                  href="{{ $module_url_path }}"
                  style="text-decoration:none;">
               <i class="fa fa-repeat"></i>
               </a> 
           
            </div>
      </div>
      <div class="box-content">  
          @include('professor.layout._operation_status') 
          {!! Form::open([ 'url' => $module_url_path.'/multi_action',
                                 'method'=>'POST',
                                 'enctype' =>'multipart/form-data',   
                                 'class'=>'form-horizontal', 
                                 'id'=>'frm_manage' 
                                ]) !!} 

            {{ csrf_field() }}
            
          
          <div class="table-responsive" style="border:0">

            <table class="table table-advance"  id="table_module" >
              <thead>
                <tr>
                  
                  <th>{{ translation('club_name')}}</th>
                  <th>{{ translation('club_id')}}</th> 
                  <th>{{translation('supervisor')}}</th>
                  <th>{{translation('club_fees')}}</th>
                  <th>{{translation('action')}}</th>
                </tr>
              </thead>
              <tbody>
                @if(sizeof($arr_clubs)>0)
                
                  @foreach($arr_clubs as $data)
                      <tr>
                        
                        <td>{{ isset($data['club_name']) ? $data['club_name'] : '' }}</td>
                        <td>{{ isset($data['club_no']) ? $data['club_no'] : '' }}</td>
                        <td>{{ isset($data['get_supervisor']['first_name']) ? ucfirst($data['get_supervisor']['first_name']) : '' }} {{ isset($data['get_supervisor']['last_name']) ? ucfirst($data['get_supervisor']['last_name']) : '' }}</td>
                        <td>{{ isset($data['club_fee']) ? $data['club_fee'] : '0' }} {{config('app.project.currency')}}</td>
                        <td>
                          <a class="green-color" href="{{$module_url_path.'/view/'.base64_encode($data['id'])}}" title="{{translation('view')}}">
                            <i class="fa fa-eye" ></i>
                          </a>
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
</div>

<script>

$(document).ready(function() {
  var oTable = $('#table_module').dataTable({
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
          
          "searching": true,
          "pageLength": 10,   
          "sortable":false
            });  
           $.fn.dataTable.ext.errMode = 'none';
           $(".dataTables_filter").css("float",'right');
});
</script>
@stop                    


