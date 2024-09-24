@extends('student.layout.master')                
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
      <li>
          <i class="fa fa-home"></i>
          <a href="{{ url($student_panel_slug.'/dashboard') }}">{{translation('dashboard')}}</a>
      </li>
      {{-- <span class="divider">
          <i class="fa fa-angle-right"></i>
          <i class="fa fa-thumbs-up"></i>                
      </span> 
      <li class="active">{{ isset($module_title)?$module_title:"" }}</li> --}}

      <span class="divider">
      <i class="fa fa-angle-right">
      </i>
      <i class="fa fa-thumbs-up faa-vertical animated-hover">
      </i>
      
      <a href="{{ url($module_url_path) }}/{{$status}}" class="call_loader">{{ $module_title or ''}}
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
        <h1><i class="fa fa-dropbox"></i> {{ isset($module_title)?$module_title:"" }}</h1>
    </div>
</div>
<!-- END Page Title -->

<!-- BEGIN Main Content -->
  <div class="box {{ $theme_color }}">
      <div class="box-title">
         <h3>
            <i class="fa fa-eye"></i>
           {{$page_title}}  
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
                     <label class="col-sm-4 col-lg-4 control-label"><b>{{translation('subject')}} </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{isset($suggestions['subject'])?ucfirst($suggestions['subject']):'-'}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b>{{translation('description')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{isset($suggestions['description'])?$suggestions['description']:'-'}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b>{{translation('date')}} </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{isset($suggestions['suggestion_date'])?getDateFormat($suggestions['suggestion_date']):'-'}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                @if(isset($suggestions['poll_raised']) && $suggestions['poll_raised'] == 1)
                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b>{{translation('duration')}} </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{isset($suggestions['duration'])?$suggestions['duration']:'-'}} Days
                     </div>
                     <div class="clearfix"></div>
                  </div>
                @endif

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b>{{translation('poll_raised')}} </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        @if(isset($suggestions['poll_raised']))
                          @if($suggestions['poll_raised'] == 0)
                            No
                          @else
                            Yes
                          @endif
                        @endif
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  @if(isset($suggestions['poll_raised']) && $suggestions['poll_raised'] == 1)
                   <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b>{{translation('poll_count')}} </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        @if(isset($suggestions['like_count']) && isset($suggestions['dislike_count']))
                          <?php $count =  $suggestions['like_count'] + $suggestions['dislike_count'];?>
                          {{$count}}
                        @endif
                     </div>
                     <div class="clearfix"></div>
                   </div>
                  

                   <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"></label>
                     <div class="col-sm-7 col-lg-2 controls">
                        <div class="school-like-dislike">
                          <a href="javascript:void(0)" class="like-ic-sgn">{{isset($suggestions['like_count'])?$suggestions['like_count']:0}}  <i class="fa fa-thumbs-up"></i></a>
                          <a href="javascript:void(0)" class="dilike-ic-sgn">{{isset($suggestions['dislike_count'])?$suggestions['dislike_count']:0}} <i class="fa fa-thumbs-down"></i></a>
                        </div>
                     </div>
                     <div class="clearfix"></div>
                  </div>
                  @endif
         <br/>
         <div class="clearfix"></div>
         

        @if(isset($suggestions['poll_raised']) && $suggestions['poll_raised'] == 1)
         @if(isset($suggestions['get_polling_details']) && $suggestions['get_polling_details']>0)

         <div class="details-infor-section-block">
                      {{translation('polling_details')}}
         </div>
         <div class="clearfix"></div><br/>

             <div class="table-responsive" style="border:0">
                <input type="hidden" name="multi_action" value="" />
                <table class="table table-advance"  id="table_module">
                   <thead>
                      <tr>
                          <th>{{translation('sr_no')}}.</th>  
                         <th>
                              <a class="sort-descs" href="#" style="color:#dedede;">{{translation('name')}} </a><br />
                         </th>
                         <th>
                              <a class="sort-descs" href="#" style="color:#dedede;">{{translation('vote')}} </a><br />
                         </th>
                      </tr>
                   </thead>
                   <tbody>
                    
                      @foreach ($suggestions['get_polling_details']  as $key => $value)
                          <tr>
                            <td>{{($key+1)}}</td>
                            <td> {{ucfirst($value['user_name'][0]['first_name']).' '.ucfirst($value['user_name'][0]['last_name'])}}</td>
                            <td> 
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
            @endif

            <div class="form-group back-btn-form-block">
               <div class="controls">
                  <a href="{{ $module_url_path}}/{{$status}}" class="btn btn-primary" style="float: right;margin-top: 20px;" > <i class="fa fa-arrow-left"></i> {{translation('back')}} </a>
               </div>
            </div>
         {!! Form::close() !!}
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