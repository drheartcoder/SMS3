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
          <a href="{{$module_url_path}}">{{$module_title}}</a>
      </li>
      <span class="divider">
        <i class="fa fa-angle-right"></i>
    </span>
    <i class="{{$create_icon}}"></i>
    <li class="active">{{$page_title}}</li>
</ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="{{$module_icon}}"></i>{{str_plural($module_title)}}</h1>

    </div>
</div>
<!-- END Page Title -->


<!-- BEGIN Tiles -->
<div class="row">
    <div class="col-md-12">

        <div class="box  box-navy_blue">
            <div class="box-title">
                <h3><i class="{{$create_icon}}"></i>{{$page_title}}</h3>
                <div class="box-tool">
                </div>
            </div>  
            <div class="box-content">
             @include('schooladmin.layout._operation_status')

             <form method="POST" action="{{$module_url_path}}/store" accept-charset="UTF-8" class="form-horizontal"  enctype="multipart/form-data"  id="validation-form1" onsubmit="return addLoader()" >
                {{ csrf_field() }}
                <div class="row">
                <div class="form-group-nms form-group">
                    <label class="col-sm-3 col-lg-2 control-label">{{translation('role')}}</label>
                    <div class="col-sm-12 col-lg-4 controls">
                     <select name="user_role[]" id="user_role" class="form-control js-example-basic-multiple" data-rule-required='true' multiple="multiple" >
                      @if(!empty($role_user))
                      @foreach($role_user as $key => $value)
                      <option value="{{$key}}">{{$value}}</option>
                      @endforeach
                      @endif

                  </select>
              </div>
              <div class="clearfix"></div>
          </div>
          <div class="form-group">
            <label class="col-sm-3 col-lg-2 control-label">{{translation('header_images')}} <i class="red">*</i></label>
            <div class="col-sm-9 col-lg-4 controls">
                <span data-multiupload="3">
                    <span data-multiupload-holder ></span>
                    <span class="upload-photo">
                        <img src="{{url('/')}}/images/school_admin/plus-img.jpg" alt="plus img">
                        <input data-multiupload-src class="upload_pic_btn validate-image" type="file" multiple=""  name="survey_images[]"  >
                        <span data-multiupload-fileinputs></span>
                    </span>
                </span>
            </div><div class="clearfix"></div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 col-lg-2 control-label">{{translation('survey_title')}} <i class="red">*</i></label>
            <div class="col-sm-9 col-lg-4 controls">
                <input class="form-control" name="survey_title" id="survey_title" placeholder="{{translation('enter')}} {{translation('survey_title')}}" type="text" data-rule-required='true' >
            </div><div class="clearfix"></div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 col-lg-2 control-label">{{translation('survey_description')}} <i class="red">*</i></label>
            <div class="col-sm-9 col-lg-4 controls">
                <textarea name="survey_description" id="survey_description" class="form-control" placeholder="{{translation('enter')}}  {{translation('survey_description')}}" data-rule-required='true' data-rule-maxlength="500"  ></textarea>                                        
            </div><div class="clearfix"></div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 col-lg-2 control-label">{{translation('survey_date')}}</label>
            <div class="col-sm-9 col-lg-4 controls">
                <div class="row">
                    <div class="col-sm-6 col-lg-6 controls">
                        <input class="form-control datepikr" name="start_date" id="start_date"  placeholder="{{ translation('enter') }} {{ translation('start_date') }}" type="text" data-rule-required='true' />
                    </div>
                    <div class="col-sm-6 col-lg-6 controls">
                        <input class="form-control datepikr" name="end_date" id="end_date" placeholder="{{ translation('enter') }} {{ translation('end_date') }}" type="text"  data-rule-required='true'/>
                    </div>
                </div>
            </div><div class="clearfix"></div>
        </div> 
                 </div>



      <div class="row">
                <div class="col-lg-4 col-lg-offset-2">
                    <h3 class="pull-left">{{translation('survey_questions')}}</h3>
                    <div class="pull-right">
                        <a class="addMoreQuestion btn btn-primary" >{{ translation('add_question') }}</a>
                    </div>                       
                    <div class="clearfix"></div>
                 </div>
                 
        <div class="survey-create-section">
            <div class="main-body-options">             
                 <div class="col-md-12" id="mainOptionDiv">
                    <div class="row survey_question">                            
                             <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label">{{translation('survey_questions')}}<i class="red">*</i></label>
                                <div class="col-sm-9 col-lg-4 controls">
                                    <input class="form-control" name="survey_question[]" id="survey_question0" placeholder="{{translation('enter')}} {{translation('survey')}} {{ translation('question')}}" type="text" data-rule-required='true'>
                                </div>
                              </div>                                                              
                                <div class="form-group-nms form-group">
                                   <label class="col-sm-3 col-lg-2 control-label">{{translation('question_type')}} <i class="red">*</i></label>
                                   <div class="col-sm-9 col-lg-4 controls" data-rule-required='true'>
                                     <select name="question_category_id[]" id="question_category_id" class="form-control question_categories" data-rule-required='true' data-option-count="0">
                                      <option value="">{{translation('select')}} {{translation('question_type')}}</option>

                                      @if(!empty($option_arr))
                                      @foreach($option_arr as $arr_data)
                                      <option value="{{$arr_data['id']}}">{{$arr_data['name']}}</option>
                                      @endforeach
                                      @endif
                                  </select>
                              </div>
                              <div class="clearfix"></div>
                          </div>                      
                      <div class="option_cls_div0 option-dropdown-section">
                      </div>
                </div>
              <div class="clearfix"></div>
        </div>
        <input type="hidden" name="count" id="count" value="0">
        <input type="hidden" name="QuestoinCount" id="QuestoinCount" value="0">    

</div>
</div>
                 </div>
<div class="clearfix"></div>                                                      

<div class="form-group">
    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
      <a href="{{$module_url_path}}" class="btn btn-primary">{{translation('back')}}</a>
      <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('save')}}</button>
  </div>
</div>
</form>
</div>
</div>
</div>
</div>
<script type="text/javascript">
    $(".js-example-basic-multiple").select2();
</script>               
<script>
    $(function() {
        $( "#start_date" ).datepicker({
            todayHighlight: true,
            autoclose: true,
            format:'yyyy-mm-dd',
            endDate: "{{\Session::get('end_date')}}",
            startDate: new Date()
        });
    }); 
    $(function() {
        $( "#end_date" ).datepicker({
            todayHighlight: true,
            autoclose: true,
            format:'yyyy-mm-dd',
            endDate: "{{\Session::get('end_date')}}",
            startDate: new Date()
        });
    }); 
    $( function() {    
        $('.timepicker-default').timepicker(); 
                //bootstrap timepicker
            });
</script> 
<script type="text/javascript">
            //dropzone script with multiple files
            (function($) {
               function readMultiUploadURL(input, callback) {

                   if (input.files) {
                      $.each(input.files, function(index, file) {


                      var blnValid = false;
                      var ext = input.files[0]['name'].substring(input.files[0]['name'].lastIndexOf('.') + 1);
                      if(ext == "JPEG" || ext == "jpeg" || ext == "jpg" || ext == "JPG" || ext == "png" || ext == "PNG")
                      {
                        blnValid = true;
                      }
                      
                      if(blnValid ==false) 
                      {
                          showAlert("Please select valid image to upload","error");
                          return false;
                            
                      }
                      else
                      {
                           var reader = new FileReader();
                           reader.onload = function(e) {

                               callback(false, e.target.result);
                           }
                           reader.readAsDataURL(file);

                      }


                       });

                   }
                   callback(true, false);
               }


               var arr_multiupload = $("span[data-multiupload]");


               if (arr_multiupload.length > 0) {
                   $.each(arr_multiupload, function(index, elem) {

                       console.log(elem);
                       var container_id = $(elem).attr("data-multiupload");


                       var id_multiupload_img = "multiupload_img_" + container_id + "_";
                       var id_multiupload_img_remove = "multiupload_img_remove" + container_id + "_";
                       var id_multiupload_file = id_multiupload_img + "_file";

                       var block_multiupload_src = "data-multiupload-src-" + container_id;
                       var block_multiupload_holder = "data-multiupload-holder-" + container_id;
                       var block_multiupload_fileinputs = "data-multiupload-fileinputs-" + container_id;


                       var input_src = $(elem).find("input[data-multiupload-src]");
                       $(input_src).removeAttr('data-multiupload-src')
                       .attr(block_multiupload_src, "");


                       var block_img_holder = $(elem).find("span[data-multiupload-holder]");
                       $(block_img_holder).removeAttr('data-multiupload-holder')
                       .attr(block_multiupload_holder, "");

                       var block_fileinputs = $(elem).find("span[data-multiupload-fileinputs]");
                       $(block_fileinputs).removeAttr('data-multiupload-fileinputs')
                       .attr(block_multiupload_fileinputs, "");

                       $(input_src).on('change', function(event) {

                           readMultiUploadURL(event.target, function(has_error, img_src) {
                                

                               if (has_error == false) {
                                   addImgToMultiUpload(img_src);
                               }
                           })
                       });

                       function addImgToMultiUpload(img_src) {
                           var id = Math.random().toString(36).substring(2, 10);

                           var html = '<div class="upload-photo" id="' + id_multiupload_img + id + '">' +
                           '<span class="upload-close">' +
                           '<a href="javascript:void(0)" id="' + id_multiupload_img_remove + id + '" ><i class="fa fa-trash-o"></i></a>' +
                           '</span>' +
                           '<img src="' + img_src + '" >' +
                           '</div>';

                           var file_input = '<input type="file" name="file[]" id="' + id_multiupload_file + id + '" style="display:none" />';
                           $(block_img_holder).append(html);
                           $(block_fileinputs).append(file_input);

                           bindRemoveMultiUpload(id);
                       }

                       function bindRemoveMultiUpload(id) {
                           $("#" + id_multiupload_img_remove + id).on('click', function() {
                               $("#" + id_multiupload_img + id).remove();
                               $("#" + id_multiupload_file + id).remove();
                           });
                       }


                   });
               }
           })(jQuery);
</script>
<script type="text/javascript">
        $(document).ready(function(){
                // This is the simple bit of jquery to duplicate the hidden field to subfile
                $('#pdffile').change(function(){
                    $('#subfile').val($(this).val());
                });

                // This bit of jquery will show the actual file input box
                $('#showHidden').click(function(){
                    $('#pdffile').css('visibilty','visible');
                });

                // This is the simple bit of jquery to duplicate the hidden field to subfile
                $('#pdffile1').change(function(){
                    $('#subfile1').val($(this).val());
                });

                // This bit of jquery will show the actual file input box
                $('#showHidden1').click(function(){
                    $('#pdffile1').css('visibilty','visible');
                });
            });
</script>

<script type="text/javascript">
            var count = 0;
            var QuestoinCount = $('#QuestoinCount').val();
         $(document).ready(function(){
           $('body').on('change','.question_categories',function(event){                
                var question_category_id = $(this).val();
                var optionCount = $(this).attr('data-option-count');
                if(question_category_id==1 || question_category_id==2  || question_category_id==5 ){
                    $('.option_cls_div'+optionCount).html('');

                }else{

                    $('.option_cls_div'+optionCount).html('<div class="form-group-nms form-group"><label class="col-sm-3 col-lg-2 control-label">{{translation('option')}}</label><div class="col-sm-9 col-lg-4 controls input-group-block"><input class="form-control option_cls input-group-block" name="option['+optionCount+'][]" id="option" placeholder="{{translation('enter')}} {{translation('option')}}" type="text" data-rule-required="true" ><button class="btn btn-success add-remove-btn" data-optionID ="'+optionCount+'" type="button" onclick="extra_options('+optionCount+');"> <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> </button></div></div><div id="extra_options'+optionCount+'"></div>');
                }
            });

            $('body').on('click','.addMoreQuestion',function(event){

                if(QuestoinCount>8){
                    $('.addMoreQuestion').hide();
                }else{
                    $('.addMoreQuestion').show();
                }
                $('#mainOptionDiv .deleteQuestion').last().hide();
                QuestoinCount++;
                 $('#QuestoinCount').val(QuestoinCount);
                 var str = '<div class="row survey_question'+QuestoinCount+'"><hr/><div class="form-group"><label class="col-sm-3 col-lg-2 control-label">{{translation('survey_questions')}}<i class="red">*</i></label><div class="col-sm-9 col-lg-4 controls"><input class="form-control" name="survey_question[]" id="survey_question'+QuestoinCount+'" placeholder="{{translation('enter')}} {{translation('survey')}} {{ translation('question')}}" type="text" required></div><a class="deleteQuestion" data-id="'+QuestoinCount+'" title="{{translation('delete_survey')}}"><i class="fa fa-trash"></i></a></div><div class="form-group-nms form-group"><label class="col-sm-3 col-lg-2 control-label">{{translation('question_type')}} <i class="red">*</i></label><div class="col-sm-9 col-lg-4 controls"  required><select name="question_category_id[]" id="question_category_id" class="form-control question_categories" required data-option-count="'+QuestoinCount+'" ><option value="">{{translation('select')}} {{translation('question_type')}}</option>';


                  @foreach($option_arr as $option)      
                   str = str+'<option value="{{$option['id']}}">';
                   str = str+'{{$option['name']}}</option>';
                  @endforeach

                  str += '</select></div><div class="clearfix"></div></div><div class="option_cls_div'+QuestoinCount+' option-dropdown-section"></div></div>';

               /* $('#QuestoinCount').val(QuestoinCount);
                var str = '<hr/><div class="row survey_question'+QuestoinCount+'" ><div class="form-group"><label class="col-sm-3 col-lg-2 control-label">{{translation('survey_questions')}}<i class="red">*</i></label><div class="col-sm-9 col-lg-4 controls"><input class="form-control" name="survey_question[]" id="survey_question " placeholder="{{translation('enter')}} {{translation('survey')}} {{ translation('question')}}" type="text" data-rule-required="true"> <a class="deleteQuestion" data-id="'+QuestoinCount+'" title="{{translation('delete_survey')}}"><i class="fa fa-trash"></i></a></div></div><div class="form-group-nms form-group"><label class="col-sm-3 col-lg-2 control-label">{{translation('question_type')}} <i class="red">*</i></label><div class="col-sm-9 col-lg-4 controls" data-rule-required="true"><select name="question_category_id[]" id="question_category_id" data-option-count="'+QuestoinCount+'" class="form-control question_categories" data-rule-required="true"><option value="">{{translation('select')}} {{translation('question_type')}}</option>';


                 @foreach($option_arr as $option)      
                   str = str+'<option value="{{$option['id']}}">';
                   str = str+'{{$option['name']}}</option>';
                @endforeach
             
*/

              $('#mainOptionDiv').append(str);
            });

            



        });
           
          function extra_options(optionCnt){
                var objTo = document.getElementById('extra_options'+optionCnt)
                var divtest = document.createElement("div");
                var divId = count;
                divtest.setAttribute("class", "form-group-nms form-group removeoption"+divId);
                
               
                var str  = '<label class="col-sm-3 col-lg-2 control-label">{{translation('option')}}</label><div class="col-sm-9 col-lg-4 controls  input-group-block"><input class="form-control option_cls input-group-block" name="option['+optionCnt+'][]" id="option" placeholder="{{translation('enter')}} {{translation('option')}}" type="text" data-rule-required="true" ><button class="btn btn-danger remove-btn-block" data-optionID ="'+optionCnt+'" type="button" onclick="remove_option('+divId+');"> <span class="glyphicon glyphicon-minus" aria-hidden="true"></span></button></div></div>';
                
                divtest.innerHTML = str;                
                count++;
                $("#count").val(count);
                objTo.appendChild(divtest)
            }

            function remove_option(optionId){
                $('.removeoption'+optionId).remove();
            }

             
            /* Delete Images */
            $('body').on('click','.deleteQuestion',function(event){   
              var questionID = $(this).attr('data-id');
              confirm_action_survey_question(questionID,this,event,"{{translation('do_you_really_want_to_delete_this_survey_question')}}");
            });
            /* Delete Images */
              function confirm_action_survey_question(questionID,ref,evt,msg)
              {
                   var msg = msg || false;

                   evt.preventDefault();  
                   swal({
                    title: "Are you sure ?",
                    text: msg,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    cancelButtonText: "No",
                    closeOnConfirm: true,
                    closeOnCancel: true
                  },
                  function(isConfirm)
                  {
                    if(isConfirm==true)
                    {
                          QuestoinCount--;
                          $('#QuestoinCount').val(QuestoinCount);
                          $('.survey_question'+questionID).remove();
                          if(QuestoinCount>8){
                              $('.addMoreQuestion').hide();
                          }else{
                              $('.addMoreQuestion').show();
                          }
                         $('#mainOptionDiv .deleteQuestion').last().show();
                    }
                  });
            } 


  
        $(document).on("change","#start_date",function()
        { 
             var start_date = $(this).val();  
             var end_date   = $("#end_date").val();  
               
            if (start_date != ''  && (end_date!='')) {
                if(start_date > end_date) 
                {
                    $("#start_date").val('');
                   swal("{{translation('start_date_can_not_be_greater_than_end_date')}}");
                }
             }  
               
        });
        $(document).on("change","#end_date",function()
        { 
             var end_date = $(this).val();  
             var start_date   = $("#start_date").val();  
               
            if (start_date != ''  && (end_date!='')) {
                if(start_date > end_date) 
                {
                    $("#end_date").val('');
                    swal("{{translation('end_date_can_not_be_less_than_start_date')}}");
                }
             }  
               
        });



    </script>
    @endsection
