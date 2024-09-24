@extends('admin.layout.master')    
@section('main_content')

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
      <i class="fa {{$create_icon}}">
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
          <i class="fa {{$create_icon}}">
          </i> {{ isset($page_title)?$page_title:"" }} 
        </h3>
        <div class="box-tool">
        </div>
      </div>
      <div class="box-content">
        @include('admin.layout._operation_status')
          
          <form method="POST" id="validation-form1"  onsubmit="return addLoader()"  class="form-horizontal" action="{{ $module_url_path}}/store">
            {{ csrf_field() }}              
        
            <div class="row">
              <div class="col-lg-6">
                <div class="row">
                    <input type="hidden" name="option_count" class="option_count">
                    <div class="form-group">
                      <label class="col-sm-3 col-lg-4 control-label" for="state">{{translation('field_type')}} 
                          <i class="red">*</i> 
                      </label>
                      <div class="col-sm-4 col-lg-8 controls">
                          <select class="form-control" id="q_category" name="q_category" onchange='getOptions();'  data-rule-required='true'>
                            <option value="">{{translation('select_field_type')}}</option>
                            @if(isset($arr_question_category) && count($arr_question_category)>0)
                              @foreach($arr_question_category as $key => $value)
                                @if(!($value['slug'] == 'browse_image') && !($value['slug'] == 'address'))
                                  <option value="{{$value['slug']}}">{{ $value['name'] }}</option>
                                @endif  
                              @endforeach
                            @endif
                          </select>
                      </div>
                    </div>
                    <div class="clearfix"></div>

                    <div class="form-group">
                      <label class="col-sm-3 col-lg-4 control-label" for="state">{{translation('title')}} 
                        <i class="red">*</i> 
                      </label>
                      <div class="col-sm-4 col-lg-8 controls">        
                          <input type="text" name="title" class="form-control"  value="{{old('title')}}" placeholder="{{translation('enter_title')}}"  data-rule-required="true"/>  
                          <span class='help-block'>{{ $errors->first('title') }}</span>
                      </div> 
                    </div>
                    <div class="clearfix"></div>

                    <div class="form-group validations" style="display: none;">
                        <label class="col-sm-3 col-lg-4 control-label">{{translation('validation_type')}} 
                        </label>
                        <div class="col-sm-4 col-lg-8 controls">
                            <div class="assignment-gray-main">
                                <select class="js-example-basic-multiple form-control " multiple="multiple" name="validations[]">

                                    <option value="dot"> {{translation('dot')}} </option>
                                    <option value="digits"> {{translation('digits')}} </option>
                                    <option value="email"> {{translation('email')}} </option>
                                    <option value="hyphen"> {{translation('hyphen')}} </option>
                                    <option value="letters"> {{translation('letters')}} </option>
                                    <option value="mobile_no"> {{translation('mobile_no')}} </option>
                                    <option value="special_symbols"> {{translation('special_symbols')}} </option>
                                    <option value="telephone_no"> {{translation('telephone_no')}} </option>
                                    <option value="white_space"> {{translation('white_space')}} </option>
                                 </select>
                                 <span class='help-block'>{{ $errors->first('validations')}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>

                    <div class="form-group options"  style="display:none">
                      <label class="col-sm-3 col-lg-4 control-label" for="state">{{translation('options')}} 
                      </label>
                      <div class="col-sm-4 col-lg-8 controls">        
                          <input type="text" name="options_1" class="form-control"  value="{{old('options_1')}}" placeholder="{{translation('enter_options')}}" />
                          {{-- <i class="fa fa-plus" onclick="addOption('{{translation('enter_options')}}')"></i>   --}}
                           <button class="btn btn-success add-remove-btn" type="button" onclick="addOption('{{translation('enter_options')}}')"> <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> </button>

                          <span class='help-block'>{{ $errors->first('options_1') }}</span>
                      </div> 
                    </div>
                    <div class="clearfix"></div>

                     <div class="form-group">
                      <label class="col-sm-3 col-lg-4 control-label" for="state">{{translation('is_required')}}  
                      </label>
                      <div class="col-sm-4 col-lg-8 controls">        
                       
                          <div class="radio-btns">
                            <div class="radio-btn">
                                 <input type="radio" id="a-male" name="required" value="1">
                                <label for="a-male">{{translation('yes')}}</label>
                                <div class="check"></div>
                            </div>
                            <div class="radio-btn">
                                <input type="radio" id="b-female" name="required" value="0">
                                <label for="b-female">{{translation('no')}} </label>
                                <div class="check">
                                    <div class="inside"></div>
                                </div>
                            </div>
                        </div>
                            
                      </div> 
                      
                      
                      
                    </div>
                    <div class="clearfix"></div>
                 </div>
              </div>
             </div>         
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
  <!-- END Main Content -->  

 <script>
var count=2;
function getOptions()
{
    var q_category = $("#q_category").val();

    if(q_category=='multiple' || q_category=='single' || q_category=='dropdown')
    {
        $(".options").css("display","block");
        $('input[name="options_1"]').attr("data-rule-required","true");
        $(".validations").css("display","none");
        $('input[name="validations"]').attr("data-rule-required","false");
    }
    else if(q_category=='short' || q_category=='long' || q_category=='latitude' || q_category=='longitude')
    {
        $(".validations").css("display","block");
        $('input[name="validations"]').attr("data-rule-required","true");
        $(".options").css("display","none");
        $('input[name="options_1"]').removeAttr("data-rule-required");
    }
    else
    {
        $(".options").css("display","none");
        $('input[name="options_1"]').removeAttr("data-rule-required");
        $('input[name="options_1"]').attr("value","");
        $(".option").remove();
        $(".validations").css("display","none");
        $('input[name="validations"]').removeAttr("data-rule-required");
    }
}
function addOption(enter_options)
{

    
    var str = "<div class='clearfix'></div><div class='form-group option_"+count+" option'><label class='col-sm-4 col-lg-4 control-label'></label><div class='col-sm-8 col-lg-8 controls'><input type='text' name='options_"+count+"' class='form-control' placeholder='"+enter_options+"' data-rule-required='true'/>";

    str = str+"<button class='btn btn-danger remove-btn-block option_"+count+"' onclick=\"remove('"+count+"')\"' type='button'><span class='glyphicon glyphicon-minus' aria-hidden='true'></span></button>";

    str = str+"<span class='help-block'>{{$errors->first('options_"+count+"')}}</span></div></div><div class='clearfix'></div>";

    $(".options").after(str);

    $(".option_count").val(count);
    count++;
}

function remove(count)
{
  $(".option_"+count).remove();
}

</script> 
 
 <!--multi selection-->  
<script type="text/javascript">
   $(".js-example-basic-multiple").select2();
</script> 
@endsection
