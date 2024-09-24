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
                <i class="fa fa-book"></i>
                <a href="{{ $module_url_path }}">{{ $module_title or ''}}</a>
            </span> 
            <span class="divider">
                <i class="fa fa-angle-right"></i>
                  <i class="fa fa-edit"></i>
            </span>
            <li class="active">{{ $page_title or ''}}</li>
        </ul>
      </div>
    <!-- END Breadcrumb -->

    <!-- BEGIN Page Title -->
    <div class="page-title new-agetitle">
    <div>
        <h1>{{$module_title}}</h1>
    </div>
</div>
    <!-- END Page Title -->

    <!-- BEGIN Main Content -->
    <div class="row">
      <div class="col-md-12">

          <div class="box">
            <div class="box-title">
              <h3>
                <i class="fa fa-edit"></i>
                {{ isset($page_title)?$page_title:"" }}
            </h3>
            <div class="box-tool">
            </div>
        </div>
        <div class="box-content">

          @include('admin.layout._operation_status')  
          
          <div class="tabbable">
                <form class="form-horizontal" onsubmit="return addLoader()" id="validation-form1" method="POST" action="{{ $module_url_path }}/update/{{$enc_id}}">
                  {{ csrf_field() }}
                <ul  class="nav nav-tabs">
                   @include('admin.layout._multi_lang_tab')
                </ul>

                  
                <div  class="tab-content">
                  
                  @if(isset($arr_lang) && sizeof($arr_lang)>0)
                    @foreach($arr_lang as $lang)

                    <?php  
                        $locale_level = "";
                        $locale_position   = "";
                        
                        if(isset($arr_data[$lang['locale']]))
                        {
                           $locale_exam_type     = $arr_data['translations'][$lang['locale']]['exam_type'];
                        }
                    ?>
                      <div class="tab-pane fade {{ $lang['locale']=='en'?'in active':'' }}" 
                               id="{{ $lang['locale'] }}">

                          <div class="form-group"> 
                                  <label class="col-sm-3 col-lg-2 control-label" for="state"> 
                                    {{translation('exam_type',$lang['locale'])}}
                                    <i class="red">*</i>
                                  </label>
                                <div class="col-sm-6 col-lg-8 controls">
                                    <input type="text" name="exam_type_{{$lang['locale']}}" value="{{$arr_data['translations'][$lang['locale']]['exam_type']}}" class="form-control add-stundt" data-rule-required='true' placeholder="{{translation('exam_type',$lang['locale'])}}">
                                     
                                    <span class='help-block'>{{ $errors->first('exam_type_'.$lang['locale']) }}</span>  
                                </div>
                          </div>
                        </div>
                      @endforeach
                  @endif
                  </div><br/>
                    <div class="form-group">
                                <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                                  <a href="{{$module_url_path}}" class="btn btn-primary">{{translation('back')}}</a>
                                  <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('update')}}</button>
                                </div>
                    </div>
                </form>
            </div>     
</div>
</div>
</div>
</div>
<!-- END Main Content -->
@stop                    
