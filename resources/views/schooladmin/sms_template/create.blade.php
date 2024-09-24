@extends('admin.layout.master')            
@section('main_content')

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
    <li>
      <i class="fa fa-home"></i>
      <a href="{{ url($admin_panel_slug.'/dashboard') }}"> Dashboard </a>
    </li>
    <span class="divider">
      <i class="fa fa-angle-right"></i>
      <i class="fa {{$module_icon or ''}}"></i>
      <a href="{{ $module_url_path }}"> Manage {{ $module_title or ''}} </a>
    </span> 
    <span class="divider">
      <i class="fa fa-angle-right"></i>
      <i class="fa fa-plus-square-o"></i>
    </span>
    <li class="active"> {{ $page_title or ''}} </li>
  </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<div class="page-title">
  <div>
  </div>
</div>
<!-- END Page Title -->

<!-- BEGIN Main Content -->
<div class="row">
  <div class="col-md-12">
    <div class="box {{ $theme_color }}">
      <div class="box-title">
        <h3>
          <i class="fa fa-plus-square-o"></i>
          {{ isset($page_title)?$page_title:"" }}
        </h3>
        <div class="box-tool">
          <a data-action="collapse" href="#"></a>
          <a data-action="close" href="#"></a>
        </div>
      </div>
     <div class="box-content">
        
        @include('admin.layout._operation_status')
   
          <div class="tabbable">
          <form method="POST" id="validation-form1" class="form-horizontal" action="{{$module_url_path}}/store" enctype="multipart/form-data">
                
                {{ csrf_field() }}              

                <ul  class="nav nav-tabs">
                    @include('admin.layout._multi_lang_tab')
                </ul>

                <div  class="tab-content">


                  @if(isset($arr_lang) && sizeof($arr_lang)>0)
                        @foreach($arr_lang as $lang)

                            <div class="tab-pane fade {{ $lang['locale']=='en'?'in active':'' }}"     id="{{ $lang['locale'] }}">
                            <div class="form-group">
                              <label class="col-sm-3 col-lg-2 control-label" for="email">  {{translation('sms_template_name',$lang['locale'])}} 
                              <i class="red">*</i>
                              </label>
                              <div class="col-sm-6 col-lg-4 controls">
                              @if($lang['locale'] == 'en')       
                                <input type="text" name="template_subject_{{$lang['locale']}}"  placeholder="SMS Template Subject" value="{{old('template_subject_'.$lang['locale']) or ''}}"  class="form-control add-stundt" data-rule-required="true"/>
                              @else
                                <input type="text" name="template_subject_{{$lang['locale']}}" placeholder="{{ translation('enter')}} {{ translation('sms_template_subject') }}" value="{{old('template_subject_'.$lang['locale'])}}"  class="form-control add-stundt" data-rule-required="true" />
                              @endif
                              </div>
                              <span class='help-block'> {{ $errors->first('template_subject_'.$lang['locale']) }} </span>  
                            </div>

                            <div class="form-group">
                              <label class="col-sm-3 col-lg-2 control-label" for="email"> {{translation('sms_template_body',$lang['locale'])}} <i class="red">*</i>
                              </label>
                              <div class="col-sm-6 col-lg-7 controls">   
                                @if($lang['locale'] == 'en')  
                                  <textarea name="template_html_{{$lang['locale']}}"   class="form-control"
                                    value="{{old('template_html_'.$lang['locale'])}}" rows="10"  placeholder="SMS Template Body" data-rule-required="true"></textarea>
                                @else
                                  <textarea name="template_html_{{$lang['locale']}}" class="form-control " value="{{old('template_html_'.$lang['locale']) or ''}}" rows="10"  placeholder="{{ translation('enter')}} {{ translation('sms_template_body') }}"  data-rule-required="true"></textarea>
                                @endif
                              </div>
                              <span class='help-block'> {{ $errors->first('template_html_'.$lang['locale']) }} </span>  
                            </div>

                           @if($lang['locale'] == 'en')
                            <div class="form-group">
                              <label class="col-sm-3 col-lg-2 control-label" for="email"> Variables 
                                <i class="red">*</i> 
                              </label>
                              <div class="col-sm-6 col-lg-7 controls">   
                                {!! Form::text('variables[]',old('variables[]'),['class'=>'form-control','-required'=>'true','data-rule-maxlength'=>'500', 'placeholder'=>'Variables' ,'data-rule-required'=>'true']) !!}  
                              </div>
                              <a class="btn btn-primary" href="javascript:void(0)" onclick="add_text_field()">
                                <i class="fa fa-plus"></i>
                              </a>
                              <a class="btn btn-danger" href="javascript:void(0)" onclick="remove_text_field(this)">
                                <i class="fa fa-minus"></i>
                              </a>
                              <span class='help-block'> {{ $errors->first('variables[]') }} </span>  
                            </div>
                            
                            <div id="append_variables"></div>
                            <br>
                          @endif    

                         </div>     
                      @endforeach
                  @endif

                </div>
                <br>
                <div class="form-group">
                      <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                      
                      <input type="submit" id="save_btn" class="btn btn btn-primary"  value="{{ translation('save')}}"/>
                  
                    </div>
                </div>
                
              </form>
                
            </div>  
  

      </div>



    </div>
  </div>
  <!-- END Main Content -->

  <script type="text/javascript">
   function add_text_field() 
   {
       var html = "<div class='form-group appended' id='appended'><label class='col-sm-3 col-lg-2 control-label'></label><div class='col-sm-6 col-lg-4 controls'><input class='form-control' name='variables[]' data-rule-required='true' placeholder='Variables' /></div><div id='append_variables'></div></div>";
       jQuery("#append_variables").append(html);
   }

   function remove_text_field(elem)
   {
      $( ".appended:last" ).remove();
   }
  </script>
@stop