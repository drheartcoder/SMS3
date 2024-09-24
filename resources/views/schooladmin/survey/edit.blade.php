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
    <i class="{{$edit_icon}}"></i>
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
        <h3><i class="{{$edit_icon}}"></i>{{$page_title}}</h3>
        <div class="box-tool">
        </div>
      </div>  
      <div class="box-content">
       @include('schooladmin.layout._operation_status')

       <form method="POST" action="{{$module_url_path}}/update/{{$enc_id}}" accept-charset="UTF-8" class="form-horizontal"  enctype="multipart/form-data"  id="validation-form2" onsubmit="return addLoader2()">
        {{ csrf_field() }}

        <div class="row">
        <div class="form-group-nms form-group">
          <label class="col-sm-3 col-lg-2 control-label">{{translation('role')}}</label>
          <div class="col-sm-12 col-lg-4 controls">
           <select name="user_role[]" id="user_role" class="form-control js-example-basic-multiple" data-rule-required='true' multiple="multiple" >
            @if(!empty($role_user))
            @foreach($role_user as $key => $value)
            <option value="{{$key}}"  @if(in_array($key,$arr_selected_user)) selected="selected" @endif >{{$value}}</option>
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
            <span data-multiupload-holder></span>
            <span class="upload-photo">
              <img src="{{url('/')}}/images/school_admin/plus-img.jpg" alt="plus img">
              <input data-multiupload-src class="upload_pic_btn" type="file" multiple="" name="survey_images[]"     >
              <span data-multiupload-fileinputs></span>
            </span>
          </span>
        </div><div class="clearfix"></div>
      </div>

      <div class="form-group">
        <label class="col-sm-3 col-lg-2 control-label">{{ translation('previous') }} {{translation('header_images')}} <i class="red">*</i></label>
        <div class="col-sm-9 col-lg-4 controls">
          <div class="row">

            @if(!empty($arr_survey_data['get_survey_images']))
            @foreach($arr_survey_data['get_survey_images'] as $key => $images)
            <div class="col-lg-3 removeOptionImage{{$images['id']}} survey-images-section" >
                <div class="survey-images">
              <?php
              if(isset($images['survey_image']) && ($images['survey_image'])!='') 
              {

                $fileURL = '';
                $fileURL = $surveyUploadImagePath.'/'.$images['survey_image'];

                if(file_exists($fileURL))
                {
                  $survey_img = $surveyUploadImageBasePath.'/'.$images['survey_image']; 
                }
                else
                {
                  $survey_img  = url('/').'/uploads/default.png'; 
                }
              }else{
               $survey_img  = url('/').'/uploads/default.png'; 
             }

             ?>
             <img src="{{ $survey_img }}" alt="" class="img-responsive" />
             <a data-image-cls-id="{{$images['id']}}" onclick="return confirm_action(this,event,'{{translation('do_you_really_want_to_delete_this_image')}}')" class="delete_survey_image"><i class="fa fa-trash-o" ></i></a>
                </div>
           </div>
           @endforeach
           @endif
         </div>       
       </div><div class="clearfix"></div>
     </div>





     <div class="form-group">
      <label class="col-sm-3 col-lg-2 control-label">{{translation('survey_title')}}<i class="red">*</i></label>
      <div class="col-sm-9 col-lg-4 controls">
        <input class="form-control" name="survey_title" id="survey_title" placeholder="{{translation('enter')}} {{translation('survey_title')}}" type="text" data-rule-required='true'  value="{{$arr_survey_data['survey_title']}}">
      </div><div class="clearfix"></div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-lg-2 control-label">{{translation('survey_description')}} <i class="red">*</i></label>
      <div class="col-sm-9 col-lg-4 controls">
        <textarea name="survey_description" id="survey_description" class="form-control" placeholder="{{translation('enter')}}  {{translation('survey_description')}}" data-rule-required='true' data-rule-maxlength="500"  >{{$arr_survey_data['survey_description']}}</textarea>                                        
      </div><div class="clearfix"></div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-lg-2 control-label">{{translation('survey_date')}}</label>
      <div class="col-sm-9 col-lg-4 controls">
        <div class="row">
          <div class="col-sm-6 col-lg-6 controls">
            <input class="form-control datepikr" name="start_date" id="start_date"  placeholder="{{ translation('enter') }} {{ translation('start_date') }}" type="text" data-rule-required='true' @if($arr_survey_data['start_date']) value="<?php echo getDateFormateForEdit($arr_survey_data['start_date']); ?>" @endif  readonly/>
          </div>
          <div class="col-sm-6 col-lg-6 controls">
            <input class="form-control datepikr" name="end_date" id="end_date" placeholder="{{ translation('enter') }} {{ translation('end_date') }}" type="text"  data-rule-required='true' @if($arr_survey_data['end_date']) value="<?php echo getDateFormateForEdit($arr_survey_data['end_date']); ?>" @endif  readonly/>
          </div>
        </div>
      </div><div class="clearfix"></div>
    </div> 
    <div class="clearfix"></div>                                                      
    <div class="form-group">
      <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
        <a href="{{$module_url_path}}" class="btn btn-primary">{{translation('back')}}</a>
        <button type="submit"  id="submit_button2" class="btn btn-primary">{{translation('update')}}</button>
      </div>
    </div>
           </div>
  </form>
 

<form method="POST" action="{{$module_url_path}}/store_questions_in_survey/{{$enc_id}}" accept-charset="UTF-8" class="form-horizontal"  enctype="multipart/form-data"  id="validation-form1">
   {{ csrf_field() }}
  <div class="row">
                <div class="col-lg-4 col-lg-offset-2">
                    <div class="pull-right">
                        <a class="addMoreQuestion btn btn-primary" >{{translation('add_question')}}</a>
                    </div>                       
                    <div class="clearfix"></div>
                 </div>
                 
        <div class="survey-create-section">
            <div class="main-body-options">             
                 <div class="col-md-12" id="mainOptionDiv">
                    
                </div>
            <input type="hidden" name="count" id="count" value="0">
            <input type="hidden" name="QuestoinCount" id="QuestoinCount" value="0">    

          </div>
        </div>
  
</div>
<div class="clearfix"></div>

<div class="form-group addNewQuestionSubmit"  style="display: none;">
    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
      <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('save')}}</button>
  </div>
</div>
</form>


  <div class="clearfix"></div>
  <div class="row">
    <div class="col-lg-4 col-lg-offset-2">
        <h3 class="pull-left" style="margin-bottom: 15px;">{{translation('survey_questions')}}</h3>     
    </div>
    <div class="col-sm-9 col-lg-8 col-sm-offset-3 col-lg-offset-2 survey-create-section">
    <div class="main-body-options">
     <div class="row">
       <div class="col-md-8">
         @if(!empty($arr_survey_data['get_questions']))
         <div class="" id="accordion1">
          @foreach($arr_survey_data['get_questions'] as $key => $arr_dataRs)
           <div class="xpanel xpanel-info">
              <div class="panel-heading">
                  <h4 class="panel-title">
                      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion1" href="#collapse{{$arr_dataRs['id']}}" aria-expanded="true"> {{translation('question') }} {{ $key+1 }}   </a>

                      <a   class="icon-btn"
                          href="{{$module_url_path}}/delete_question/{{ base64_encode($arr_dataRs['id']) }}/{{ $enc_id }}" onclick="return confirm_action_delete_survey_question(this,event,'{{translation('do_you_really_want_to_delete_this_survey_question')}}')"
                                                          >
                    <i class="fa fa-trash-o"></i> </a>
                  </h4>
              </div>
              <div id="collapse{{$arr_dataRs['id']}}" class="panel-collapse collapse @if($key==0) in @endif" aria-expanded="true" style="">
                  <div class="panel-body">

                      <form method="POST" action="{{$module_url_path}}/update_question/{{ base64_encode($arr_dataRs['id']) }}/{{ $enc_id }}" accept-charset="UTF-8" class="form-horizontal validation-form-new"  enctype="multipart/form-data" data-form-validate="true"   >
                      {{ csrf_field() }}
                       <div class="row" >
                            <div class="col-sm-12 col-md-12">
                                <div class="form-group">
                                    <label class="control-label">{{translation('survey_questions')}}<i class="red">*</i></label>
                                    <div class=" controls">
                                        <input class="form-control" name="survey_question" id="survey_question" placeholder="{{translation('enter')}} {{translation('survey')}} {{ translation('question')}}" type="text" data-rule-required='true' value="{{$arr_dataRs['survey_question']}}">
                                    </div>
                                    <span class="help-block" for="survey_question"></span>
                                </div>      
                            </div>
                            <div class="col-sm-12 col-md-12">
                                <div class="form-group-nms form-group">
                                   <label class="control-label">{{translation('question_type')}} <i class="red">*</i></label>
                                   <div class=" controls">
                                    <input type="hidden" name="question_category_id" value="{{$arr_dataRs['question_category_id']}}">
                                     <select name="question_category_id_display" id="question_category_id" class="form-control question_categories"  data-option-count="0" disabled="disabled">
                                      <option value="">{{translation('select')}} {{translation('question_type')}}</option>

                                      @if(!empty($option_arr))
                                      @foreach($option_arr as $arr_data)
                                      <option value="{{$arr_data['id']}}" @if($arr_dataRs['question_category_id']==$arr_data['id']) selected="selected" @endif>{{$arr_data['name']}}</option>
                                      @endforeach
                                      @endif
                                  </select>
                              </div>
                              <div class="clearfix"></div>
                          </div>
                      </div>


                         <?php

                        $arr_options = isset($arr_dataRs['question_options'])&&!empty($arr_dataRs['question_options'])?json_decode($arr_dataRs['question_options'],true):array();
                       
                        if(!empty($arr_options)){
                            foreach($arr_options as $key2 => $value ){ ?>
                        <div class="col-sm-12 col-md-12">
                                <div class="form-group">
                                    <label class="control-label">{{translation('survey_questions')}}<i class="red">*</i></label>
                                    <div class=" controls">
                                        <input class="form-control" name="option[]" id="option" placeholder="{{translation('enter')}} {{translation('survey')}} {{ translation('option')}}" type="text" data-rule-required='true' value="{{$value}}">
                                    </div>

                                </div>      
                      </div>
                      <?php } } ?>

                      </div>
                        <div class="clearfix"></div>

                        <div class="form-group">
                        <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                          <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('update')}}</button>
                        </div>
                      </div>
                    </form>

                     


                  </div>
              </div>
          </div>
          @endforeach
         </div>
         @endif


         <div class="clearfix"></div>
       </div>
     
      
    </div>
  </div>
</div>
          </div>
<div class="clearfix"></div>

</div>
</div>
</div>
</div>
<script type="text/javascript">
  $(".js-example-basic-multiple").select2();
</script>     
<script type="text/javascript">
  $(document).ready(function(){
      var removeSuccessClass = function(e) {
            $(e).closest('.form-group').removeClass('has-success');
        }
      $('form').each(function() {   // <- selects every <form> on page

        $(this).validate({        // <- initialize validate() on each form
          errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            errorPlacement: function(error, element) {
                if(element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else if (element.next('.chosen-container').length) {
                    error.insertAfter(element.next('.chosen-container'));
                } else {
                    error.insertAfter(element);
                }
            },
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",

            invalidHandler: function (event, validator) { //display error alert on form submit   
                var el = $(validator.errorList[0].element);
                if ($(el).hasClass('chosen')) {
                    $(el).trigger('chosen:activate');
                } else {
                    $(el).focus();
                }
            },

            highlight: function (element) { // hightlight error inputs
                $(element).closest('.form-group').removeClass('has-success').addClass('has-error'); // set error class to the control group
            },

            unhighlight: function (element) { // revert the change dony by hightlight
                $(element).closest('.form-group').removeClass('has-error'); // set error class to the control group
                setTimeout(function(){removeSuccessClass(element);}, 3000);
            },

            success: function (label) {
                label.closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
            }
       });
    });
 });
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
              var SITE_URL  = "{{ url('/') }}/school_admin";
              var csrf_token = "{{ csrf_token() }}";
              function confirm_action(ref,evt,msg)
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
                    // swal("Performed!", "Your Action has been performed on that file.", "success");
                    var surveyId = $(ref).attr('data-image-cls-id');

                    $.ajax({
                      url: SITE_URL+'/survey/delete_survey_image',
                      type:'POST',
                      data:{
                        '_token' : csrf_token,
                        'surveyId' : surveyId
                      },
                      success: function( res ) {
                        if(res=='done'){
                          $('.removeOptionImage'+surveyId).remove();
                        }

                      },
                      error: function( res ){
                        swal(translation('something_went_wrong_please_try_again_later'));
                      }
                    });

                  }
                });
             } 
              function confirm_action_delete_survey_question(ref,evt,msg)
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
                    if(isConfirm==true){ 
                      window.location = $(ref).attr('href');
                    }
                  });
            }

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
                $('.addNewQuestionSubmit').show();

                $('#mainOptionDiv .deleteQuestion').last().hide();
                
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
              QuestoinCount++;
              $('#QuestoinCount').val(QuestoinCount);
                if(QuestoinCount>{{$totalAddNew}}){
                    $('.addMoreQuestion').hide();
                }else{
                    $('.addMoreQuestion').show();
                }
  
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
                          if(QuestoinCount>{{$totalAddNew}}){
                              $('.addMoreQuestion').hide();
                          }else{
                              $('.addMoreQuestion').show();
                          }
                         $('#mainOptionDiv .deleteQuestion').last().show();
                         if($('.deleteQuestion').length == 0){
                              $('.addNewQuestionSubmit').hide();
                         }
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


        function addLoader2(){   
                $('#validation-form2').submit(function(event) {
                    if($('.has-error').length > 0){
                       event.preventDefault();
                    }else{
                        $("#submit_button2").html("<b><i class='fa fa-spinner fa-spin'></i></b> Processing...");
                        $("#submit_button2").attr('disabled', true);
                    }
                });
            }

          </script>
 @endsection
