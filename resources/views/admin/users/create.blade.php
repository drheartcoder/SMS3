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
      <i class="fa fa-users">
      </i>
    </span> 
    <li >  <a href="{{$module_url_path}}"> {{ isset($module_title)?$module_title:"" }}</a>
    </li>
    <span class="divider">
      <i class="fa fa-angle-right">
      </i>
      <i class="fa fa-plus">
      </i>
    </span> 
    <li class="active">  {{ isset($page_title)?$page_title:"" }}
    </li>
  </ul>
</div>
<!-- END Breadcrumb -->
<!-- BEGIN Main Content -->
<div class="row">
  <div class="col-md-12">
    <div class="box {{ $theme_color }}">
      <div class="box-title">
        <h3>
          <i class="fa fa-plus">
          </i> {{ isset($page_title)?$page_title:"" }} 
        </h3>
        <div class="box-tool">
        </div>
      </div>
      <div class="box-content">
        @include('admin.layout._operation_status')
          <div class="tabbable">
          <form method="POST" id="validation-form1" class="form-horizontal" action="{{ $module_url_path}}/store" enctype ='multipart/form-data'>
                {{ csrf_field() }}              
                <ul  class="nav nav-tabs">
                    @include('admin.layout._multi_lang_tab')
                </ul>
                <div  class="tab-content">
                  @if(isset($arr_lang) && sizeof($arr_lang)>0)
                        @foreach($arr_lang as $lang_key=>$lang)
                            <div class="tab-pane fade {{ $lang['locale']=='en'?'in active':'' }}"  id="{{ $lang['locale'] }}">
                              
                              <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label">{{translation('first_name',$lang['locale'])}} 
                                    <i class="red">*</i> 
                                </label>
                                <div class="col-sm-4 col-lg-4 controls">
                                    <input type="text" name="first_name_{{$lang['locale']}}" data-rule-required='true' data-rule-pattern="^[a-zA-Z0-9 àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" class="form-control" value="{{old('first_name_'.$lang['locale'])}}" placeholder="{{translation('enter',$lang['locale'])}} {{translation('first_name',$lang['locale'])}}"/>
                                    <span class='help-block'>{{ $errors->first('first_name_'.$lang['locale']) }}</span>
                                </div>
                              </div>
                              <div class="clearfix"></div>

                              <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label">{{translation('last_name',$lang['locale'])}} 
                                  <i class="red">*</i> 
                                </label>
                                <div class="col-sm-4 col-lg-4 controls">        
                                    <input type="text" name="last_name_{{$lang['locale']}}" class="form-control"  value="{{old('last_name_'.$lang['locale'])}}" placeholder="{{translation('enter',$lang['locale'])}} {{translation('last_name',$lang['locale'])}}"  data-rule-required="true" data-rule-pattern="^[a-zA-Z0-9 àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" />  
                                    <span class='help-block'>{{ $errors->first('last_name_'.$lang['locale']) }}</span>
                                </div> 
                              </div>
                              <div class="clearfix"></div>

                              @if($lang_key==0)
                              <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label">{{translation('email',$lang['locale'])}} 
                                  <i class="red">*</i> 
                                </label>
                                <div class="col-sm-4 col-lg-4 controls">        
                                    <input type="text" name="email" class="form-control"  value="{{old('email')}}" placeholder="{{translation('enter',$lang['locale'])}} {{translation('email',$lang['locale'])}}"  data-rule-required="true" data-rule-email="true" id="email" />  
                                    <span class='help-block'>{{ $errors->first('email') }}</span>
                                    <span id="err_email" style="color: red"></span>
                                </div> 
                              </div>
                              @endif
                              <div class="clearfix"></div>

                              @if($lang_key==0)
                              <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label">{{translation('password',$lang['locale'])}} 
                                  <i class="red">*</i> 
                                </label>
                                <div class="col-sm-4 col-lg-4 controls">        
                                    <input type="password" name="password" class="form-control"  value="{{old('password')}}" placeholder="{{translation('enter',$lang['locale'])}} {{translation('password',$lang['locale'])}}"  data-rule-required="true" data-rule-minlength='6' id="password" />  
                                    <span class='help-block'>{{ $errors->first('password') }}</span>
                                </div> 
                              </div>
                              @endif
                              <div class="clearfix"></div>

                              @if($lang_key==0)
                              <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label">{{translation('confirm_password',$lang['locale'])}} 
                                  <i class="red">*</i> 
                                </label>
                                <div class="col-sm-4 col-lg-4 controls">        
                                    <input type="password" name="confirm_password" class="form-control"  value="{{old('confirm_password')}}" placeholder="{{translation('enter',$lang['locale'])}} {{translation('confirm_password',$lang['locale'])}}"  data-rule-required="true" data-rule-minlength='6' data-rule-equalto='#password' data-msg-equalto="Password and confirm password must be same"/>  
                                </div> 
                              </div>
                              @endif
                              <div class="clearfix"></div>

                              @if($lang_key==0)
                              <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label">{{translation('mobile_no',$lang['locale'])}} 
                                  <i class="red">*</i> 
                                </label>
                                <div class="col-sm-4 col-lg-4 controls">        
                                    <input type="text" name="mobile_no" class="form-control"  value="{{old('mobile_no')}}" placeholder="{{translation('enter',$lang['locale'])}} {{translation('mobile_no',$lang['locale'])}}"  data-rule-required="true" data-rule-digits='true' data-rule-minlength='10' data-rule-maxlength='12'/>  
                                    <span class='help-block'>{{ $errors->first('mobile_no') }}</span>
                                </div> 
                              </div>
                              @endif
                              <div class="clearfix"></div>

                              @if($lang_key==0)
                              <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label">{{translation('telephone_number',$lang['locale'])}} 
                                </label>
                                <div class="col-sm-4 col-lg-4 controls">        
                                    <input type="text" name="telephone_no" class="form-control"  value="{{old('telephone_no')}}" placeholder="{{translation('enter',$lang['locale'])}} {{translation('telephone_number',$lang['locale'])}}" />  
                                    <span class='help-block'>{{ $errors->first('telephone_no') }}</span>
                                </div> 
                              </div>
                              @endif
                              <div class="clearfix"></div>

                              @if($lang_key==0)
                              <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label">{{translation('national_id',$lang['locale'])}} 
                                  <i class="red">*</i> 
                                </label>
                                <div class="col-sm-4 col-lg-4 controls">        
                                    <input type="text" name="national_id" class="form-control"  value="{{old('national_id')}}" placeholder="{{translation('enter',$lang['locale'])}} {{translation('national_id',$lang['locale'])}}" data-rule-required="true"/>  
                                    <span class='help-block'>{{ $errors->first('national_id') }}</span>
                                </div> 
                              </div>
                              @endif
                              <div class="clearfix"></div> 

                              @if($lang_key==0)
                              <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label">{{translation('birth_date',$lang['locale'])}} 
                                  <i class="red">*</i> 
                                </label>
                                <div class="col-sm-4 col-lg-4 controls">        
                                    <input type="date" name="birth_date" class="form-control"  value="{{old('birth_date')}}" placeholder="{{translation('enter',$lang['locale'])}} {{translation('birth_date',$lang['locale'])}}" data-rule-required="true"/>  
                                    <span class='help-block'>{{ $errors->first('birth_date') }}</span>
                                </div> 
                              </div>
                              @endif
                              <div class="clearfix"></div>

                              @if($lang_key==0)
                              <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label">{{translation('gender',$lang['locale'])}}  
                                  <i class="red">*</i> 
                                </label>
                                <div class="col-sm-4 col-lg-4 controls">        
                                    <input type="radio" name="gender" value="MALE" checked="true"> {{translation('male',$lang['locale'])}} &nbsp;&nbsp;
                                    <input type="radio" name="gender" value="FEMALE"> {{translation('female',$lang['locale'])}} 
                                </div> 
                              </div>
                              @endif
                              <div class="clearfix"></div>  

                              @if($lang_key==0)
                              <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label">{{translation('address',$lang['locale'])}}  
                                  <i class="red">*</i> 
                                </label>
                                <div class="col-sm-4 col-lg-4 controls">        
                                  <input type="hidden" name="latitude"  id="latitude" class="field" value="{{ old('latitude') }}">
                                <input type="hidden" name="longitude"  id="longitude" class="field"  value="{{ old('longitude') }}" >
                                <input type="text" name="address" id="location" value="{{ old('address') }}" class="form-control" data-rule-required='true'/>
                                
                              <span class="help-block">{{ $errors->first('address') }}</span>
                                </div> 
                              </div>
                              @endif
                              <div class="clearfix"></div>  

                              @if($lang_key==0)
                              <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label">{{translation('city',$lang['locale'])}}  
                                </label>
                                <div class="col-sm-4 col-lg-4 controls">   
                                    <input type="text" name="city"  id="city" class="form-control"  value="{{old('city')  }}" placeholder="{{translation('enter')}} {{translation('city',$lang['locale'])}}" />
                                    <span class="help-block">{{ $errors->first('city') }}</span>
                                </div> 
                              </div>
                              @endif
                              <div class="clearfix"></div>  

                              @if($lang_key==0)
                              <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label">{{translation('country',$lang['locale'])}}  
                                </label>
                                <div class="col-sm-4 col-lg-4 controls">   
                                    <input type="text" name="country"  id="country" class="form-control"  value="{{old('country') }}" placeholder="{{translation('enter')}} {{translation('country',$lang['locale'])}}"/>
                                    <span class="help-block">{{ $errors->first('country') }}</span>
                                </div> 
                              </div>
                              @endif
                              <div class="clearfix"></div>  

                              @if($lang_key==0)
                              <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label">{{translation('profile_image',$lang['locale'])}}  
                                </label>
                                <div class="col-sm-4 col-lg-4 controls">   
                                    <input type="file" name="profile_image"  id="profile_image" class=""  value="{{old('profile_image')}}"/>
                                    <span class="help-block">{{ $errors->first('country') }}</span>
                                </div> 
                              </div>
                              @endif
                              <div class="clearfix"></div> 
                            </div>
                      @endforeach
                  @endif
                </div><br/>
                <div class="form-group">
                    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">        
                        <a href="{{ url($admin_panel_slug.'/users/school_admin') }}" class="btn">{{translation('back')}}</a> 
                        <input type="submit" id="save_btn" class="btn btn btn-primary" value="{{translation('save')}} "/>                  
                    </div>
                </div> 
              </form>     
            </div>  
      </div>
    </div>
  </div>
  <!-- END Main Content -->  
 <script>
 var count=2;
 function getOptions(locale)
 {
    var q_category = $("#q_category").val();

    if(q_category=='multiple' || q_category=='single' || q_category=='dropdown')
    {
      <?php foreach($arr_lang as $lang)
         {
      ?>
        $(".options_{{$lang['locale']}}").css("display","block");
        $('input[name="options_1_{{$lang["locale"]}}"]').attr("data-rule-required","true");

      <?php
        }
      ?>  
    }
    else
    {
      <?php foreach($arr_lang as $lang)
         {
      ?>
        $(".options_{{$lang['locale']}}").css("display","none");
        $('input[name="options_1_{{$lang["locale"]}}"]').attr("data-rule-required","false");
        $('input[name="options_1_{{$lang["locale"]}}"]').attr("value","");
        $(".option").remove();
      <?php
        }
      ?>  
    }
 }
 function addOption(locale,enter,options)
 {  

  <?php foreach($arr_lang as $key=>$lang)
    {
  ?>
      var str = "<div class='clearfix'></div><div class='form-group option_"+count+"_{{$lang['locale']}} option'><label class='col-sm-3 col-lg-2 control-label'></label><div class='col-sm-4 col-lg-4 controls'><input type='text' name='options_"+count+"_{{$lang['locale']}}' class='form-control' placeholder='"+enter+" "+options+"' data-rule-required='true'/>";

      <?php if($key==0) {?>
            str = str+"<i class='fa fa-minus option_"+count+"_{{$lang['locale']}}' onclick=\"remove('"+count+"','"+locale+"')\"></i>";
      <?php } ?>

      str = str+"<span class='help-block'>{{$errors->first('options_"+count+"_$lang[\"locale\"]')}}</span></div></div><div class='clearfix'></div>";

      $(".options_{{$lang['locale']}}").after(str);  
  <?php
  }
  ?>
    $(".option_count").val(count);    
    count++;
  }
  function remove(count,locale)
  {
    <?php foreach($arr_lang as $lang)
    {
    ?>
      $(".option_"+count+"_{{$lang['locale']}}").remove();
    <?php
    }
    ?>
  }
 </script>
 <script type="text/javascript">
   $(document).ready(function(){
    $('#email').on('blur',function(){
      var email   =   $('#email').val();
       $.ajax({
              url  :"{{ $module_url_path }}/checkEmail",
              type :'POST',
              data :{'email':email ,'_token':'<?php echo csrf_token();?>'},
              success:function(data){
                if(data.status=='success')
                  {
                    $('#err_email').text();
                  }
                  if(data.status=='error')
                  {
                    $('#err_email').show();
                    $('#err_email').text('This email is already exist');
                  }
              }
            });
    });

    $('#email').on('keyup',function(){
      $('#err_email').text('');
    });
 });
 </script>
 
@endsection
