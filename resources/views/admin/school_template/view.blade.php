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
        <?php $count=0; 
         
        ?>
                  {!! Form::open([ 'url' => "",
                                 'method'=>'POST',   
                                 'class'=>'form-horizontal', 
                                 'id'=>'validation-form' ,
                                 'enctype'=>'multipart/form-data'
                                ]) !!}
                        @if(isset($arr_template) && count($arr_template)>0)        
                          @foreach($arr_template as $template)

                          @if(isset($template['is_active']) && $template['is_active']==1)
                          <div class="form-group">
                            <label class="col-sm-3 col-lg-2 control-label" for="state">{{$template['title']}} 
                              @if($template['is_required']==1)
                                <i class="red">*</i> 
                              @endif     
                            </label>
                            <div class="col-sm-4 col-lg-4 controls">        
                              @if($template['get_question_category']['slug'] == 'multiple')
                                <?php 
                                    $options = $template['options'];  
                                    $arr_options = explode(",",$options);
                                ?>
                                @foreach($arr_options as $option)
                                    <input type="checkbox" name="checked_record[]" id="dummy_{{$count++}}" value="dummy" readonly/>
                                    {{$option}} &nbsp; &nbsp;

                                @endforeach
                              @elseif($template['get_question_category']['slug'] == 'single') 
                                  <?php 
                                    $options = $template['options'];  
                                    $arr_options = explode(",",$options);
                                  ?>
                                  @foreach($arr_options as $option) 
                                    <input type="radio" name="required" value="1"> {{$option}} &nbsp;&nbsp;
                                  @endforeach  
                              @elseif($template['get_question_category']['slug'] == 'short')
                                <input type="text" name="dummy" class="form-control add-stundt" readonly/>  
                              @elseif($template['get_question_category']['slug'] == 'address')
                                <input type="text" name="dummy" class="form-control  add-stundt" readonly/> 
                              @elseif($template['get_question_category']['slug'] == 'latitude')
                                <input type="text" name="dummy" class="form-control  add-stundt" readonly />
                              @elseif($template['get_question_category']['slug'] == 'longitude')
                                <input type="text" name="dummy" class="form-control  add-stundt" readonly />
                              @elseif($template['get_question_category']['slug'] == 'long') 
                                <textarea class="form-control" readonly></textarea> 
                              @elseif($template['get_question_category']['slug'] == 'browse_image')  
                                <img src="{{url('/')}}/images/school-default-logo.png" height="150px" width="50%"> 
                              @elseif($template['get_question_category']['slug'] == 'dropdown')   
                                <select class="form-control" name="q_category"> 
                                  <option value="{{$count}}">Default</option>
                                  {{$template['options']}}
                                  <?php 
                                    $options = $template['options'];  
                                    $arr_options = explode(",",$options);
                                  ?>
                                  @foreach($arr_options as $option)
                                    <option value="{{$count}}">{{$option}}</option>   
                                  @endforeach
                                </select>  
                              @endif
                            </div> 
                          </div>
                          <div class="clearfix"></div>
                          @endif
                          @endforeach
                        @else
                            <div style="padding-top:70px;font-weight:bold;font-size:20px" align="center">{{translation("no_data_available")}}</div>  
                        @endif      
            
                <br>
               
              {!! Form::close() !!}
      </div>
    </div>
  </div>
  <!-- END Main Content -->  
 <script>
 var count=2;
 function getOptions(locale)
 {
    var q_category = $("#q_category").val();

    if(q_category=='3' || q_category=='4')
    {
      <?php foreach($arr_lang as $lang)
         {
      ?>
        $(".options_{{$lang['locale']}}").css("display","block"); 
        $("")
      <?php
        }
      ?>  
    }
    else
    {
      $(".options_"+locale).css("display","none"); 
    }
 }
 function addOption(locale,enter,options)
 {  

  <?php foreach($arr_lang as $key=>$lang)
    {
  ?>
      var str = "<div class='clearfix'></div><div class='form-group option_"+count+"_{{$lang['locale']}}'><label class='col-sm-3 col-lg-2 control-label'></label><div class='col-sm-4 col-lg-4 controls'><input type='text' name='options_"+count+"_{{$lang['locale']}}' class='form-control' placeholder='"+enter+" "+options+"' data-rule-required='true'/>";

      <?php if($key==0) {?>
            str = str+"<i class='fa fa-minus option_"+count+"_{{$lang['locale']}}' onclick=\"remove('"+count+"','"+locale+"')\"></i>";
      <?php } ?>

      str = str+"<span class='help-block'>{{$errors->first('options_"+count+"_$lang[\"locale\"]')}}</span></div></div><div class='clearfix'></div>";

      $(".options_{{$lang['locale']}}").append(str);  
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
 
@endsection
