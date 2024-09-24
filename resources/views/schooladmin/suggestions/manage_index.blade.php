@extends('schooladmin.layout.master')                
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
      <li>
          <i class="fa fa-home"></i>
          <a href="{{ url($school_admin_panel_slug.'/dashboard') }}">{{translation('dashboard')}}</a>
      </li>
      <span class="divider">
          <i class="fa fa-angle-right"></i>
          <i class="fa fa-thumbs-up"></i>                
      </span> 
      <li class="active">{{ isset($module_title)?$module_title:"" }}</li>
  </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css">
<div class="page-title new-agetitle">
    <div>
        <h1><i class="{{$module_icon}}"></i> {{ isset($module_title)?$module_title:"" }}</h1>
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
            {{ isset($page_title)?$page_title:"" }}
         </h3>
         <div class="box-tool">
            <a title="Refresh" 
                  href="{{ $module_url_path }}"
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
         'id'=>'validation-form1' 
         ]) !!}
         {{ csrf_field() }}
         <div class="col-md-10">
            <div id="ajax_op_status">
            </div>
            <div class="alert alert-danger" id="no_select" style="display:none;"></div>
            <div class="alert alert-warning" id="warning_msg" style="display:none;"></div>
         </div>
         <div class="btn-toolbar pull-right clearfix">
          
            <br>
         </div>
         <br/>
         <div class="clearfix"></div>
         <div class="table-responsive" style="border:0">
            <input type="hidden" name="multi_action" value="" />
            <table class="table table-advance"  id="table_module">
               <thead>
                     <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('suggestion_subject')}} </a><br />
                     </th>
                     <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('name')}} </a><br />
                     </th>
                     <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('suggestion_from')}}</a><br />
                     </th>
                      <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('suggestion_category')}}</a><br />
                     </th>
                      <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('suggestion_date')}}</a><br />
                     </th>
                     @if($status == 'poll_raised')
                      <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('duration')}}</a><br />
                      </th>
                     @endif
                       <th><a class="sort-descs" href="#" style="color:#dedede;">{{translation('status')}}</a><br />
                       </th>

                     @if(array_key_exists('suggestions.list', $arr_current_user_access)) 
                          <th>{{translation('action')}}</th>
                     @endif
                  </tr>
               </thead>
               <tbody>

                 @if(isset($arr_suggestions) && count($arr_suggestions)>0)
                  @foreach($arr_suggestions as $key => $suggestion)
                  
                    <?php
                          $roles = $user_ids = [];
                          $roles = isset($suggestion['assigned_roles'])?explode(',', $suggestion['assigned_roles']):'';

                           if(isset($suggestion['get_polling_details']) && count($suggestion['get_polling_details'])>0)
                          {
                            foreach($suggestion['get_polling_details'] as $key => $polling_details)
                            {
                              array_push($user_ids,$polling_details['from_user_id']); 
                            }
                          }
                    ?>
                    @if($status == 'poll_raised')
                        @if(in_array(config('app.project.role_slug.professor_role_slug'),$roles))
                          <tr>
                            <td>
                              {{isset($suggestion['subject'])?ucwords($suggestion['subject']):''}}
                            </td>

                            <td>
                              <?php
                                  $user_name  = '';
                                  $first_name = isset($suggestion['get_user_details']['first_name'])?ucwords($suggestion['get_user_details']['first_name']):'';

                                  $last_name = isset($suggestion['get_user_details']['last_name'])?ucwords($suggestion['get_user_details']['last_name']):'';

                                  $user_name = $first_name.' '.$last_name;
                              ?>
                              {{$user_name}}
                            </td>

                            <td>
                              {{isset($suggestion['user_role'])?strtoupper($suggestion['user_role']):''}}  
                            </td>

                            <td>
                              {{isset($suggestion['get_category']['category'])?ucwords($suggestion['get_category']['category']):''}}
                            </td>

                            <td>
                              {{isset($suggestion['suggestion_date'])?strtoupper($suggestion['suggestion_date']):''}}
                            </td>

                            
                            <td>
                              {{isset($suggestion['duration'])?strtoupper($suggestion['duration']):''}}
                            </td>

                            <td>

                                    @if(in_array($user_id,$user_ids))

                                        @if($polling_details['vote'] == 'LIKE')
                                          <label class="label label-success" ><i class="fa fa-thumbs-up"></i></label>
                                        @else
                                          <label class="label label-warning"><i class="fa fa-thumbs-down"></i></label>
                                        @endif
                                    @else
                                       <?php 
                                              $valid =    '';
                                              $date = $suggestion['poll_raised_date'];
                                              $duration = $suggestion['duration'];
                                              $date = date_create($date);
                                              date_add($date, date_interval_create_from_date_string($duration.' days'));
                                              $date1= date_format($date,'Y-m-d');
                                              
                                              $to_date = date_create();
                                             
                                              $date_diff = date_diff($to_date,$date);
                                        ?>           
                                        @if($date_diff->format('%R%d')>0)
                                          <div class="school-like-dislike">
                                            <a href="javascript:void(0)" onClick="addVote({{$suggestion['id']}},'like')" class="like-ic-sgn"><i class="fa fa-thumbs-up"></i></a>
                                            
                                            <a href="javascript:void(0)" onClick="addVote({{$suggestion['id']}},'dislike')" class="dilike-ic-sgn"><i class="fa fa-thumbs-down"></i></a>
                                          </div>
                                        @endif
                                    @endif
                            </td>

                            <td>
                              @if(in_array($user_id,$user_ids))
                                <?php
                                  $view_href =  $module_url_path.'/view/'.$status.'/'.base64_encode($suggestion['id']);
                                ?>
                                <a class="green-color" href="{{$view_href}}" title="{{translation('view')}}"><i class="fa fa-eye" ></i></a>
                              @else
                                <a class="green-color" style="position: relative;"  href="javascript:void(0)" title="{{translation('access_denied')}}"><i class="fa fa-eye" ></i><i class="fa fa-ban fa-stack-2x text-danger"></i></a>
                              @endif
                            </td>
                          </tr>
                        @endif
                    @else
                      <tr>
                            <td>
                              {{isset($suggestion['subject'])?ucwords($suggestion['subject']):''}}
                            </td>

                            <td>
                              <?php
                                  $user_name  = '';
                                  $first_name = isset($suggestion['get_user_details']['first_name'])?ucwords($suggestion['get_user_details']['first_name']):'';

                                  $last_name = isset($suggestion['get_user_details']['last_name'])?ucwords($suggestion['get_user_details']['last_name']):'';

                                  $user_name = $first_name.' '.$last_name;
                              ?>
                              {{$user_name}}
                            </td>

                            <td>
                              {{isset($suggestion['user_role'])?strtoupper($suggestion['user_role']):''}}  
                            </td>

                            <td>
                              {{isset($suggestion['get_category']['category'])?ucwords($suggestion['get_category']['category']):''}}
                            </td>

                            <td>
                              {{isset($suggestion['suggestion_date'])?strtoupper($suggestion['suggestion_date']):''}}
                            </td>

                            <td>
                              @if(isset($suggestion['status']) && $suggestion['status']!='')
                                @if($suggestion['status'] == 'REQUESTED')
                                  <label class="label label-info">{{translation('requested')}}</label>
                                @else
                                  <label class="label label-success">{{translation('approved')}}</label>
                                @endif
                              @endif
                            </td>

                            <td>
                                <?php
                                  $view_href =  $module_url_path.'/view/'.$status.'/'.base64_encode($suggestion['id']);
                                ?>
                                <a class="green-color" href="{{$view_href}}" title="{{translation('view')}}"><i class="fa fa-eye" ></i></a>
                            </td>
                          </tr>
                    @endif
                  @endforeach
                 @endif
               </tbody>
            </table>
         </div>
         {!! Form::close() !!}
      </div>
   </div>
</div>

<script>


      /*Script to show table data*/
          var table_module = false;
          $(document).ready(function()
          {
            table_module = $('#table_module').DataTable({
              ordering:false,
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

 function addVote(id,status)
 {
  $('#loader').fadeIn();
  $('body').addClass('loader-active');
    $.ajax({
                url  :"{{ $module_url_path }}/add_vote",
                type :'POST',
                data :{'status': status , 'id' : id , '_token':'<?php echo csrf_token();?>'},
                success:function(){
                  location.reload();
                }
              }); 
 }
</script>
@stop