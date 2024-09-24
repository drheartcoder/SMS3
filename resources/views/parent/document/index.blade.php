@extends('parent.layout.master')                
@section('main_content')
        <!-- BEGIN Breadcrumb -->
        <div id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="fa fa-home"></i>
                    <a href="{{ url($parent_panel_slug.'/dashboard') }}">{{translation('dashboard')}}</a>
                </li>
                <span class="divider">
                    <i class="fa fa-angle-right"></i>
                </span>
                <li class="active">{{ isset($module_title)?$module_title:"" }}</li>                
            </ul>
        </div>
        <!-- END Breadcrumb -->
        
        <div class="page-title new-agetitle">
            <div>
                <h1><i class="{{$module_icon}}"></i> {{ translation('document') }}</h1>
            </div>
        </div>

        <!-- BEGIN Tiles -->
        <div class="row">
            <div class="col-md-12">
                <div class="box  box-navy_blue">
                    <div class="box-title">
                        <h3><i class="fa fa-list"></i> {{ isset($module_title)?$module_title:"" }}</h3>
                    </div>
                    <div class="box-content studt-padding">
                     @include('parent.layout._operation_status')

                     @if(array_key_exists('document.create',$arr_current_user_access ))
                     <form method="POST" action="{{ $module_url_path }}/store" accept-charset="UTF-8" class="form-horizontal"  enctype="multipart/form-data"  id="validation-form1" onsubmit="return addLoader()">
                      
                         {{ csrf_field() }}  
                         <div class="row">
                            <div class="col-md-6">
                              <div class="col-md-12">
                                   <div class="form-group">
                                        <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('document')}} {{translation('title')}}<i class="red">*</i></label>
                                        <div class="col-sm-9 col-md-8 col-lg-9 controls">
                                             <input type="text" name="document_title" id="document_title" class="form-control" 
                                             data-rule-required='true'
                                              placeholder="{{translation('enter')}} {{translation('document')}} {{translation('title')}}" maxlength="100"  pattern="^[A-Za-z0-9 ]*$" />
                                              
                                            
                                           <span class='help-block' for="document_title">{{ $errors->first('document_title')}}</span>
                                        </div>
                                    </div>
                              </div>
                                
                            
                            </div>
                            <div class="col-md-6">  
                               <div class="col-md-12 upload-section-block">                                    
                                  <div class="main-col-block">
                                      <div class="form-group">
                                          <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('document')}} <i class="red">*</i></label>
                                          <div class="col-sm-9 col-md-8 col-lg-9 controls input-group-block">
                                                    <div class="upload-block-clone">
                                                        <input type="file" id="pdffile_0" class="hidden-input-block" name="document" onchange="Changefilename(this)" 
                                                       data-rule-required='true' >
                                                        <div class="input-group">
                                                            <input type="text" class="form-control file-caption  kv-fileinput-caption" id="subfile" readonly="" />
                                                            <div class="btn btn-primary btn-file"><a class="file" onclick="$('#pdffile_0').click();">Browse...</a></div>
                                                         </div>
                                                    </div>
                                                <span class='help-block' for="document">{{ $errors->first('document')}}</span>
                                          </div>
                                      </div>                                      
                                      
                                      <div class="clearfix"></div>
                                  </div>
                                  
                                  <div class="clearfix"></div>
                              </div>  
                              
                              
                            </div>
                              
                        <div class="col-sm-8 col-sm-offset-4 col-lg-9 col-lg-offset-3">
                          <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('save')}}</button>
                        </div>
                    </form>
                    @endif

                        <div class="btn-toolbar pull-right clearfix">

                        <div class="box-tool"> 
                            @if(array_key_exists('document.delete',$arr_current_user_access ))
                            <a 
                               title="{{translation('multiple_delete')}}" 
                               href="javascript:void(0);" 
                               onclick='javascript : return check_multi_action("frm_manage","{{translation('are_you_sure')}}","{{translation('do_you_really_want_to_delete_this_record').'?'}}","{{translation('yes')}}","{{translation('no')}}","{{translation('oops')}}","{{translation('please_select_the_record_to_perform_this_action')}}","delete");'
                               style="text-decoration:none;">
                               <i class="fa fa-trash-o"></i>
                            </a>
                            @endif
                            <a 
                               title="{{translation('refresh')}}" 
                               href="{{$module_url_path}}"
                               style="text-decoration:none;">
                               <i class="fa fa-repeat"></i>
                            </a> 
                        </div>
                      </div>   


                        {!! Form::open([ 'url' => $module_url_path.'/multi_action',
                         'method'=>'POST',
                         'enctype' =>'multipart/form-data',   
                         'class'=>'form-horizontal', 
                         'id'=>'frm_manage' 
                         ]) !!}
                         {{ csrf_field() }}
                            
                         
                            <div class="clearfix"></div>                            
                            <div class="border-box table-action-btns">
                                                             
                                <div class="table-responsive" style="border:0">
                                    <div class="col-lg-12">
                                    <input type="hidden" name="multi_action" value="" />
                                    <table class="table table-advance" id="table_module">
                                        <thead>
                                            <tr>
                                                @if(array_key_exists('document.delete',$arr_current_user_access ) )
                                                <th style="width: 18px; vertical-align: initial;">
                                                  <div class="check-box">
                                                      <input type="checkbox" class="filled-in" name="selectall" id="selectall" />
                                                      <label for="selectall"></label>
                                                  </div>
                                               </th>
                                               @endif
                                                <th>{{translation('document')}}  {{translation('title')}}</th>
                                                <th>{{translation('added_date')}}</th>    
                                                <th>{{translation('action')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                          
                                        @foreach($arr_document as $data_rs)
                                          <tr>
                                          @if(array_key_exists('document.delete',$arr_current_user_access ))
                                              <td style="position: relative">
                                             
                                              <div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_{{base64_encode($data_rs['id'])}}" value="{{base64_encode($data_rs['id'])}}" /><label for="mult_change_{{base64_encode($data_rs['id'])}}"></label></div>
                                             
                                            
                                              </td>
                                          @endif
                                          <td>{{ $data_rs['document_title'] or ''}}</td> 
                                         
                                          <td>{{isset($data_rs['created_at']) ? getDateFormat($data_rs['created_at']) : '-'}}</td>
                                          
                                          
                                           <td style="position: relative;">
                                            

                                            <?php
                                            $document_name = '';
                                            if(isset($data_rs['document_name']) && ($data_rs['document_name'])!='') 
                                              {
                                                  $fileURL = '';
                                                  $fileURL = $student_document_base_img_path.'/'.$data_rs['document_name'];

                                                  if(file_exists($fileURL))
                                                  {
                                                      $document_name = $data_rs['document_name'];
                                                  }
                                                  
                                              } 

                                            ?>
                                            @if(!empty($document_name))
                                                <a 
                                                href='{{$module_url_path.'/download_document/'.base64_encode($data_rs['id'])}}'
                                                  title="{{translation('download_document')}}"   ><i class="fa fa-download" ></i></a>
                                            @endif
                                            @if(array_key_exists('document.delete',$arr_current_user_access ) )
                                                  <a class="red-color" href="{{$module_url_path.'/delete/'.base64_encode($data_rs['id'])}}" title="{{translation('delete')}}" onclick="return confirm_action(this,event,'{{translation('do_you_really_want_to_delete_this_record')}}','{{translation("are_you_sure")}}','{{translation('yes')}}','{{translation("no")}}')"><i class="fa fa-trash" ></i></a>
                                          @endif    
                                            
                                           </td>
                                          </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    </div>
                                </div>
                            </div>
                         {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
      </div>
<script type="text/javascript">

function Changefilename(event){
    console.log($(event).next().children('input'));
    var file = event.files;
    validateDocument(event.files,'Doc',null);
    $(event).next().children('input').val($(event).val());
}
 
$(document).ready(function() {


$(document).on("change",".validateDoc", function()
 {       
    var file = this.files;
    validateDocument(this.files,null);

 });
  
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
          "pageLength": 10,
          "searching":false,      
          "aoColumnDefs": [
                          { 
                            "bSortable": false, 
                            "aTargets": [0,1,3] // <-- gets last column and turns off sorting
                           } 
                        ]
            });  
            $.fn.dataTable.ext.errMode = 'none';
}); 

function validateDocument(files,element_id) 
 {
    
    if (typeof files !== "undefined") 
    {
      for (var i=0, l=files.length; i<l; i++) 
      {
            var blnValid = false;
            var ext = files[i]['name'].substring(files[i]['name'].lastIndexOf('.') + 1);
          
            if(ext=="pdf" || ext=='jpg' || ext=='jpeg' || ext=="PDF" || ext=='JPG' || ext=='JPEG')
            {
                  blnValid = true;
            }  
            
            if(blnValid ==false) 
            {
              showAlert("Sorry, " + files[0]['name'] + " is invalid, allowed extensions are: pdf, jpg, jpeg","error");
              $("#pdffile_0").val('');
              return false;
            }
            else
            {              
                
                if(files[0].size>10485760)
                {
                   showAlert("File size should be less than 10 MB","error");
                   $("#pdffile_0").val('');
                }
            }                
        }
    }
    else
    {
      showAlert("No support for the File API in this web browser" ,"error");
      $("#pdffile_0").val('');
    } 
  }
    
</script> 
@endsection