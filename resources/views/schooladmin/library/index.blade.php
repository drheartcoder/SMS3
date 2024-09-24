@extends('schooladmin.layout.master')                
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="{{url('/')}}/school_admin/dashboard">{{translation('dashboard')}}</a>
        </li>
        
        <span class="divider">
            <i class="fa fa-angle-right"></i>
        </span>
        <li>

          <i class="{{$module_icon}}"></i>
          <li class="active">{{$module_title}}</li>
        </li>
        <span class="divider">
            <i class="fa fa-angle-right"></i>
        </span>
      
        <li>
          <i class="fa fa-list"></i>
          {{$page_title}}          

        </li>
       
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

<!-- BEGIN Tiles -->
           <div class="row">
                <div class="col-md-12">
                    <div class="box  box-navy_blue">
                        <div class="box-title">
                            <h3><i class="fa fa-list"></i>{{$page_title}}</h3>
                            <div class="box-tool">
                            </div>
                        </div>
                        <div class="box-content">
                        @include('schooladmin.layout._operation_status')  
                        <div class="tobbable">
                        @if(array_key_exists('library.create', $arr_current_user_access) || array_key_exists('library.update', $arr_current_user_access))
                            @if(isset($edit_categories) && count($edit_categories)>0)
                                <form method="post" onsubmit="return addLoader()" action="{{$module_url_path}}/update/{{$enc_id}}"  class="form-horizontal" id="validation-form1">
                            @else
                                <form method="post" onsubmit="return addLoader()" action="{{$module_url_path}}/store"  class="form-horizontal" id="validation-form1">
                            @endif 
                                {{ csrf_field() }}                        


                                            
                                          <br>
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-12 col-lg-6">
                                                        <div class="row">
                                                        <div class="form-group">
                                                            <label class="col-sm-3 col-lg-4 control-label">{{translation('book_category')}} <i class="red">*</i></label>
                                                            <div class="col-sm-9 col-lg-8 controls">
                                                                <input type="text" name="book_category" class="form-control" id="book_category" placeholder="{{translation('enter')}} {{translation('book_category')}}" data-rule-required="true"
                                                                    @if(isset($edit_categories['category_name'])) value="{{$edit_categories['category_name']}}"  
                                                                    @endif  pattern="^[a-zA-Z ]*$">
                                                                <span class='help-block'>{{ $errors->first('book_category') }}</span> 
                                                                <span style="color: red;display: none" id="err_category"></span>   
                                                            </div>
                                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                     

                                        
                             

                                <div class="row">
                                <div class="form-group">
                                    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                                        @if(isset($edit_categories) && count($edit_categories)>0)

                                           <a href="{{ url($module_url_path.'/books_category') }}" class="btn btn-primary">{{translation('back')}}</a>

                                            <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('update')}}</button>
                                        @else
                                            <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('add')}}</button>
                                        @endif    
                                        
                                    </div>
                                </div>
                                    </div>
                            </form>
                            @endif  
                            
                      


                        <div class="table-responsive library-table-main" style="border:0">
                            <input type="hidden" name="multi_action" value="" />
                            <table class="table table-advance" id="table_module">
                                <thead>
                                    <tr>
                                        <th>{{translation('sr_no')}}</th>
                                        <th>
                                              {{translation('book_category')}}
                                        </th>
                                        @if(array_key_exists('library.update', $arr_current_user_access) || array_key_exists('library.delete', $arr_current_user_access))
                                            <th>
                                               {{translation('action')}}                   
                                            </th>
                                        @endif                                            
                                    </tr>
                                </thead>
                                <tbody>
                                  <?php $no = 0; ?>
                                    @if(isset($categories) && !empty($categories))
                                        @foreach($categories as $key =>$category)
                                            <tr>
                                                <td width="70px">{{++$no}}</td>
                                                <td>
                                                    {{$category['category_name']}}
                                                </td>

                                                <td width="200px">
                                                    
                                                      @if(array_key_exists('library.update', $arr_current_user_access))     
                                                        <a class="orange-color" href="{{ $module_url_path.'/books_category/'.base64_encode($category['id']) }}" title="{{translation("edit")}}">
                                                            <i class="fa fa-edit" >
                                                            </i>
                                                        </a>  
                                                      @endif
                                                      @if(array_key_exists('library.delete', $arr_current_user_access))
                                                        @if(!$category['get_book_contents'])         
                                                          <a class="red-color" href="{{$module_url_path.'/delete_category/'.base64_encode($category['id'])}}" title="{{translation("delete")}}" onclick="return confirm_action(this,event,'{{translation('do_you_really_want_to_delete_this_record')}}','{{translation("are_you_sure")}}','{{translation('yes')}}','{{translation("no")}}')"><i class="fa fa-trash" ></i></a>
                                                        @else
                                                          <a style="position: relative;" class="red-color" href="javascript:void(0)" title="{{translation('access_denied')}}" ><i class="fa fa-trash" ></i><i class="fa fa-ban fa-stack-2x text-danger"></i></a>  
                                                        @endif

                                                      @endif  
                                                   
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>                            
                        </div>
                    </div>
                </div>
               </div> 
  </div>
        </script>
        <!-- END Main Content -->
        <script type="text/javascript">
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
            });        
            $.fn.dataTable.ext.errMode = 'none';
        });

        $('#book_category').on('blur',function(){
          var category   =   $('#book_category').val();
          if(category != '')
          {
           $.ajax({
                  url  :"{{ $module_url_path }}/checkCategory",
                  type :'POST',
                  data :{'category':category ,'_token':'<?php echo csrf_token();?>'},
                  success:function(data){
                    if(data.status=='success')
                      {
                        $('#err_category').text();
                      }
                      if(data.status=='error')
                      {
                        $('#err_category').show();
                        $('#err_category').text('This category is already exist');
                      }
                  }
                });
          }
        });

        $('#book_category').on('keyup',function(){
          $('#err_category').css('display','none');
        });
        </script>
 
@endsection