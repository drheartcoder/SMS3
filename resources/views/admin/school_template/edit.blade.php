@extends('admin.layout.master')    
@section('main_content')

<!-- BEGIN Page Title -->
<div class="page-title">
  <div>
  </div>
</div>
<!-- END Page Title -->
<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
    <li>
      <i class="fa fa-home">
      </i>
      <a href="{{ url($admin_panel_slug.'/dashboard') }}"> {{translation('dashboard')}}
      </a>
    </li>
    <span class="divider">
      <i class="fa fa-angle-right">
      </i>
      <i class="fa {{$module_icon}}">
      </i>
    </span> 
    <li >  <a href="{{$module_url_path}}"> {{ isset($module_title)?$module_title:"" }}</a>
    </li>
    <span class="divider">
      <i class="fa fa-angle-right">
      </i>
      <i class="fa {{$edit_icon}}">
      </i>
    </span> 
    <li class="active">  {{ isset($page_title)?$page_title:"" }}
    </li>
  </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
    <div class="page-title new-agetitle">
        <div>
            <h1><i class="fa {{$module_icon}}">
          </i> {{ isset($module_title)?$module_title:"" }} </h1>
        </div>
    </div>
<!-- END Page Title -->

<!-- BEGIN Main Content -->
<div class="row">
  <div class="col-md-12">
    <div class="box {{ $theme_color }}">
      <div class="box-title">
        <h3>
          <i class="fa {{$edit_icon}}">
          </i> {{ isset($page_title)?$page_title:"" }} 
        </h3>
        <div class="box-tool">
        </div>
      </div>
      <div class="box-content">
        @include('admin.layout._operation_status')
          
          <form method="POST" id="validation-form1"  onsubmit="return addLoader()"  class="form-horizontal" action="{{ $module_url_path}}/update">
          <input type="hidden" name="id" value="{{isset($obj_template->id) ? base64_encode($obj_template->id) : ''}}">
                {{ csrf_field() }}              
                
                              <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label" for="state">{{translation('field_type')}} 
                                    <i class="red">*</i> 
                                </label>
                                
                                <div class="col-sm-4 col-lg-4 controls">
                                    <select class="form-control" id="q_category" name="q_category" onchange='getOptions()'>
                                      @if(isset($arr_question_category) && count($arr_question_category)>0)
                                        @foreach($arr_question_category as $key => $value)
                                          @if(!($value['slug'] == 'browse_image') && !($value['slug'] == 'address'))
                                            <option value="{{$value['slug']}}" @if(isset($obj_template->question_category_id) && $obj_template->question_category_id==$value['id']) selected @endif>{{ $value['name'] }}</option>
                                          @endif
                                        @endforeach
                                      @endif
                                    </select>
                                </div>
                              </div>
                              <div class="clearfix"></div>

                              <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label" for="state">{{translation('title')}} 
                                  <i class="red">*</i> 
                                </label>
                                <div class="col-sm-4 col-lg-4 controls">        
                                    <input type="text" name="title" class="form-control add-stundt"  value="@if(isset($obj_template->title)){{$obj_template->title}}@endif" placeholder="{{translation('enter_title')}}"  data-rule-required="true"/>  
                                    <span class='help-block'>{{ $errors->first('title') }}</span>
                                </div> 
                              </div>
                              <div class="clearfix"></div>


                              <?php 
                                $validations = [];
                                if($obj_template->validations)
                                {
                                    $validations = explode(',', $obj_template->validations);
                                }
                              ?>
                              <div class="form-group validations"  style="display: none;">
                                  <label class="col-sm-3 col-lg-2 control-label">{{translation('validation_type')}} 
                                  </label>
                                  <div class="col-sm-4 col-lg-4 controls">
                                      <div class="assignment-gray-main ">
                                          <select class="js-example-basic-multiple form-control " multiple="multiple" name="validations[]">
                                              <option value="dot" @if(isset($validations) && in_array('dot',$validations)) selected @endif> {{translation('dot')}} </option>
                                              <option value="digits" @if(isset($validations) && in_array('digits',$validations)) selected @endif> {{translation('digits')}} </option>
                                              <option value="email" @if(isset($validations) && in_array('email',$validations)) selected @endif> {{translation('email')}} </option>
                                              <option value="hyphen" @if(isset($validations) && in_array('hyphen',$validations)) selected @endif> {{translation('hyphen')}} </option>
                                              <option value="letters" @if(isset($validations) && in_array('letters',$validations)) selected @endif> {{translation('letters')}} </option>
                                              <option value="mobile_no" @if(isset($validations) && in_array('mobile_no',$validations)) selected @endif> {{translation('mobile_no')}} </option>
                                              <option value="special_symbols"  @if(isset($validations) && in_array('special_symbols',$validations)) selected @endif> {{translation('special_symbols')}} </option>
                                              <option value="telephone_no"  @if(isset($validations) && in_array('telephone_no',$validations)) selected @endif> {{translation('telephone_no')}} </option>
                                              <option value="white_space"   @if(isset($validations) && in_array('white_space',$validations)) selected @endif> {{translation('white_space')}} </option>
                                           </select>
                                           <span class='help-block'>{{ $errors->first('validations')}}</span>
                                      </div>
                                  </div>
                              </div>
                              <div class="clearfix"></div>
                              
                              <?php $arr_options=[]; ?>
                              @if(isset($obj_template->options) && $obj_template->options!="")
                                <?php $arr_options = explode(',',$obj_template->options); ?>

                                @foreach($arr_options as $key=>$option)  
                                <?php $count = $key+1; ?>
                                  @if($key==0)
                                  <div class="form-group options option">
                                    <label class="col-sm-3 col-lg-2 control-label" for="state">{{translation('options')}}  
                                    </label>
                                  @else
                                  <div class='form-group option_{{$count}} option'>
                                    <label class='col-sm-3 col-lg-2 control-label'></label>
                                  @endif    
                                  <div class='col-sm-4 col-lg-4 controls'> 
                                      <input type="text" name="options_{{$count}}" class="form-control"  value="{{$option}}" placeholder="{{translation('enter_options')}}" />
                                        @if($key==0)
<!--                                          <i class="fa fa-plus" onclick="addOption('{{translation('enter_options')}}')"></i>-->
                                          <button class="btn btn-success add-remove-btn" type="button" onclick="addOption('{{translation('enter_options')}}')"> <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> </button>
                                        @else
<!--                                          <i class='fa fa-minus option_{{$count}}' onclick="remove('{{$count}}')"></i>-->
                                          
                                          <button class="btn btn-danger add-remove-btn" type="button" onclick="addOption('{{translation('enter_options')}}')"> <span class="glyphicon glyphicon-minus" aria-hidden="true"></span> </button>
                                          
                                        @endif
                                      
                                      
                                      
                                      <span class='help-block'>{{ $errors->first('options_'.$count) }}</span>
                                  </div> 
                                </div>
                                <div class="clearfix"></div>
                                @endforeach
                              @else
                              <div class="form-group options"  style="display:none">
                                <label class="col-sm-3 col-lg-2 control-label" for="state">{{translation('options')}} 
                                 
                                </label>
                                <div class="col-sm-4 col-lg-4 controls">        
                                    <input type="text" name="options_1" class="form-control add-stundt"  value="{{old('options_1')}}" placeholder="{{translation('enter_options')}}" />
<!--                                    <i class="fa fa-plus" onclick="addOption('{{translation('enter_options')}}')"></i> -->
                                    
                                     <button class="btn btn-success add-remove-btn" type="button" onclick="addOption('{{translation('enter_options')}}')"> <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> </button>
                                    
                                    <span class='help-block'>{{ $errors->first('options_1') }}</span>
                                </div> 
                              </div>
                              <div class="clearfix"></div>  
                              @endif
                              
                              <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label" for="state">{{translation('is_required')}}  
                                </label>
                                <div class="col-sm-4 col-lg-4 controls">        
<!--
                                    <input type="radio" name="required" value="1" @if(isset($obj_template->is_required) && $obj_template->is_required=='1') checked @endif> {{translation('yes')}} &nbsp;&nbsp;
                                    <input type="radio" name="required" value="0" @if(isset($obj_template->is_required) && $obj_template->is_required=='0') checked @endif> {{translation('no')}} 
                                    
-->
                                    
                                     <div class="radio-btns">
                                        <div class="radio-btn">
                                             <input type="radio" id="a-male" name="required" value="1" @if(isset($obj_template->is_required) && $obj_template->is_required=='1') checked @endif> 
                                             
                                            <label for="a-male"> {{translation('yes')}}</label>
                                            <div class="check"></div>
                                        </div>
                                        <div class="radio-btn">
                                           <input type="radio" id="b-female" name="required" value="0" @if(isset($obj_template->s_required) && $obj_template->is_required=='0') checked @endif> 
                                            <label for="b-female">{{translation('no')}} </label>
                                            <div class="check">
                                                <div class="inside"></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div> 
                              </div>
                              <div class="clearfix"></div>
                <br>
                <div class="form-group">
                    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">                     
                        <a href="{{$module_url_path}}" class="btn btn-primary">{{translation('back')}}</a>
                        <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('update')}}</button>
                    </div>
                </div>
                <input type="hidden" value="1" name="option_count" class="option_count">
              </form>  
      </div>
    </div>
  </div>
  <!-- END Main Content -->  
 <script>
 var count="<?php echo count($arr_options)+2; ?>";
 $("document").ready(function(){
    $(".option_count").val(count);
    var q_category = $("#q_category").val();

    if(q_category=='multiple' || q_category=='single' || q_category=='dropdown')
    {
        $(".options").css("display","block");
        $('input[name="options_1"]').attr("data-rule-required","true"); 
    }
    else if(q_category=='short' || q_category=='long' || q_category=='latitude' || q_category=='longitude')
    {
        $(".validations").css("display","block");
        $('input[name="validations"]').attr("data-rule-required","true"); 
    }
    else
    {
        $(".options").css("display","none");
        $('input[name="options_1"]').attr("data-rule-required","false");
          <?php foreach($arr_options as $key=>$option)
          { 
              $count_var  = $key+1;
          ?>
              $(".option_{{$count_var}}").css("display","none");
              $('input[name="option_{{$count_var}}"]').attr("data-rule-required","false");

          <?php
            }
          ?>  
    }

 });
 function getOptions()
 {
    var q_category = $("#q_category").val();

    if(q_category=='multiple' || q_category=='single' || q_category=='dropdown')
    {
        $(".options").css("display","block");
        $('input[name="options_1"]').attr("data-rule-required","true");
        $(".validations").css("display","none");
        $('input[name="validations"]').attr("data-rule-required","false");
        $('input[name="validations"]').val();
    }
    else if(q_category=='short' || q_category=='long')
    {
        $(".validations").css("display","block");
        $('input[name="validations"]').attr("data-rule-required","true");
        $(".options").css("display","none");
        $('input[name="options_1"]').removeAttr("data-rule-required");
        <?php foreach($arr_options as $key=>$option)
          { 
              $count_var  = $key+1;
          ?>
              $(".option_{{$count_var}}").css("display","none");
              $('input[name="option_{{$count_var}}"]').attr("data-rule-required","false");

          <?php
            }
          ?>  
    }
    else
    {
        $(".options").css("display","none");
        $('input[name="options_1"]').attr("data-rule-required","false");
        $(".option").remove();
        $(".validations").css("display","none");
        $('input[name="validations"]').attr("data-rule-required","false");
        count=2;
    }
 }
 function addOption(enter_options)
{

    
    var str = "<div class='clearfix'></div><div class='form-group option_"+count+" option'><label class='col-sm-2 col-lg-2 control-label'></label><div class='col-sm-4 col-lg-4 controls'><input type='text' name='options_"+count+"' class='form-control' placeholder='"+enter_options+"' data-rule-required='true'/>";

    str = str+"<button class='btn btn-danger remove-btn-block option_"+count+"' onclick=\"remove('"+count+"')\"' type='button'><span class='glyphicon glyphicon-minus' aria-hidden='true'></span></button>";

    str = str+"<span class='help-block'>{{$errors->first('options_"+count+"')}}</span></div></div><div class='clearfix'></div>";

    $(".options").after(str);

    $(".option_count").val(count);
    count++;
}
  function remove(param_count)
  {
      $(".option_"+param_count).remove();
  }
 </script>

  <!--multi selection-->  
<script type="text/javascript">
   $(".js-example-basic-multiple").select2();
</script> 
 
@endsection
