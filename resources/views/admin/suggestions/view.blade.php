@extends('admin.layout.master')                
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
      <li>
          <i class="fa fa-home"></i>
          <a href="{{ url($admin_panel_slug.'/dashboard') }}">{{translation('dashboard')}}</a>
      </li>
      {{-- <span class="divider">
          <i class="fa fa-angle-right"></i>
          <i class="fa fa-thumbs-up"></i>                
      </span> 
      <li class="active">{{ isset($module_title)?$module_title:"" }}</li> --}}

      <span class="divider">
      <i class="fa fa-angle-right">
      </i>
      <i class="fa fa-thumbs-up">
      </i>
      
      <a href="{{ url($module_url_path) }}" class="call_loader">{{ $module_title or ''}}
      </a>
    </span> 
    <span class="divider">
      <i class="fa fa-angle-right">
      </i>
      <i class="fa fa-eye">
      </i>
    </span> 
    <li class="active">   {{ $page_title or '' }}
    </li>
  </ul>
</div>
<!-- END Breadcrumb -->


<!-- BEGIN Page Title -->
<link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">
<div class="page-title new-agetitle">
    <div>
        <h1><i class="fa fa-thumbs-up"></i> {{ isset($module_title)?$module_title:"" }}</h1>
    </div>
</div>
<!-- END Page Title -->

<!-- BEGIN Main Content -->
<div class="box {{ $theme_color }}">
  <div class="box-title">
     <h3>
        <i class="fa fa-eye"></i>
       {{$module_title}}  
     </h3>
     <div class="box-tool">
     </div>
  </div>

  <div class="clearfix"></div>
  <div class="box-content view-details-seciton-main details-section-main-block">
    <div class="row">
      <div class="col-md-12">
          <div class="details-infor-section-block">
              {{translation('suggestion_details')}}
          </div>

          <div class="form-group">
               <label class="col-sm-4 col-lg-4 control-label"><b>{{translation('suggestion_from_school')}}  </b>: </label>
               <div class="col-sm-9 col-lg-4 controls">
                  {{isset($school_name)?ucfirst($school_name):''}}
               </div>
               <div class="clearfix"></div>
          </div>

          <div class="form-group">
             <label class="col-sm-4 col-lg-4 control-label"><b>{{translation('subject')}}</b>: </label>
             <div class="col-sm-9 col-lg-4 controls">
                {{isset($arr_data['suggestions']['subject'])?ucfirst($arr_data['suggestions']['subject']):''}}
             </div>
             <div class="clearfix"></div>
          </div>

          <div class="form-group">
             <label class="col-sm-4 col-lg-4 control-label"><b>{{translation('description')}} </b>: </label>
             <div class="col-sm-9 col-lg-4 controls">
                {{isset($arr_data['suggestions']['description'])?ucfirst($arr_data['suggestions']['description']):''}}
             </div>
             <div class="clearfix"></div>
          </div>

          <div class="form-group">
             <label class="col-sm-4 col-lg-4 control-label"><b>{{translation('date')}}</b>: </label>
             <div class="col-sm-9 col-lg-4 controls">
                    {{isset($arr_data['suggestions']['suggestion_date'])?getDateFormat($arr_data['suggestions']['suggestion_date']):''}}
             </div>
             <div class="clearfix"></div>
          </div>

          <div class="form-group">
             <label class="col-sm-4 col-lg-4 control-label"><b>{{translation('duration')}} </b>: </label>
             <div class="col-sm-9 col-lg-4 controls">
                {{isset($arr_data['suggestions']['duration'])?$arr_data['suggestions']['duration']:0}} Days
             </div>
             <div class="clearfix"></div>
          </div>
          <div class="clearfix"></div><br/>
          @if(isset($arr_data['pollingUsers']) && count($arr_data['pollingUsers'])>0)
            <div class="table-responsive" style="border:0">
              <table class="table table-advance"  id="table_module">
                   <thead>
                      <tr>
                         <th>
                              <a class="sort-descs" href="#" style="color:#dedede;">{{translation('name')}} </a><br />
                         </th>
                         <th>
                              <a class="sort-descs" href="#" style="color:#dedede;">{{translation('vote')}} </a><br />
                         </th>
                      </tr>
                   </thead>
                   <tbody>
                      @foreach ($arr_data['pollingUsers']  as $key => $value)
                          <tr>
                            <td> {{ucfirst($value['user_name'][0]['first_name']).' '.ucfirst($value['user_name'][0]['last_name'])}}</td>
                            <td width="200px"> 
                                @if($value['vote'] == 'LIKE')
                                    <div class="st-alphabet clr-lamtr4">
                                          <i class="fa fa-thumbs-up"></i>
                                    </div>
                                @else
                                    <div class="st-alphabet clr-lamtr">
                                          <i class="fa fa-thumbs-down"></i>
                                    </div>
                                @endif
                            </td>
                          </tr>
                      @endforeach
                   </tbody>
              </table>
            </div>
          @endif
      </div>

       <div class="form-group back-btn-form-block">
         <div class="controls">
            <a href="{{ $module_url_path}}" class="btn btn-primary" style="float: right;margin-top: 20px;" > <i class="fa fa-arrow-left"></i> {{translation('back')}} </a>
         </div>
      </div>
    </div>
  </div>

 
 <script type="text/javascript">

      /*Script to show table data*/
          var table_module = false;
          $(document).ready(function()
          {
            table_module = $('#table_module').DataTable({
              <?php  
                $current_locale = Session::get('locale');
                if($current_locale == 'fr'){ ?>  
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
      });
 </script> 
@stop