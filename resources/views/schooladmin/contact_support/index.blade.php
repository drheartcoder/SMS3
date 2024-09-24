@extends('schooladmin.layout.master')                
@section('main_content')
<!-- BEGIN Page Title -->
<link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
    <li>
      <i class="fa fa-home"></i>
      <a href="{{ url($school_admin_panel_slug.'/dashboard') }}"> {{translation('dashboard')}} </a>
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
                @if(array_key_exists('contact_support.create', $arr_current_user_access))  
                  <a href="{{$module_url_path}}/create" >{{translation('add')}} {{translation('contact_support')}}</a> 
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
        
          @include('schooladmin.layout._operation_status')  

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

            <table class="table table-advance"  id="table4" >
              <thead>
                <tr>
                  
                  
                  <th>{{translation('sr_no')}}</th>
                  <th>{{translation('enquiry_number')}}</th>
                  <th>{{translation('contact_enquiry')}}</th> 
                  <th>{{translation('subject')}}</th> 
                  
                  <th style="width: 450px;">{{translation('description')}}</th> 

                 
                   <th style="width: 130px;">{{translation('action')}}</th>
                 
                </tr>
              </thead>
              <tbody>
                @if(sizeof($arr_contact_enquiry)>0)
                  @foreach($arr_contact_enquiry as $key=> $contact_enquiry)
              
                  <tr>
                   
                    <td > {{ $key+1 }} </td> 
                    <td > {{ isset($contact_enquiry['enquiry_no'])?$contact_enquiry['enquiry_no']:'' }} </td> 
                    <td > {{ isset($contact_enquiry['enquiry_category']['category_name'])?$contact_enquiry['enquiry_category']['category_name']:'' }} </td>  
                    <td > {{ isset($contact_enquiry['subject'])?$contact_enquiry['subject']:'' }} </td>  
                    <td > {{ isset($contact_enquiry['description'])?str_limit($contact_enquiry['description'],125):'' }} </td>
                    <td>  <a  class="green-color" href="{{ $module_url_path.'/view/'.base64_encode($contact_enquiry['id']) }}" 
                        title="{{translation('view')}}">
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


<!-- END Main Content -->
<script>
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
        ordering:false
    });        
 $.fn.dataTable.ext.errMode = 'none';
});
</script>

@stop                    


