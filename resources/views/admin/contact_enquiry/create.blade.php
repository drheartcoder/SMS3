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
      <i class="fa {{$create_icon}}">
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
          <i class="fa {{$create_icon}}">
          </i> {{ isset($page_title)?$page_title:"" }} 
        </h3>
        <div class="box-tool">
        </div>
      </div>
      <div class="box-content">
      

        @include('admin.layout._operation_status')
   
          <div class="tabbable">

          <form method="POST" id="form_validation" class="form-horizontal" data-parsley-validate="" action="{{ $module_url_path}}/store">
                
                {{ csrf_field() }}              

                <ul  class="nav nav-tabs">
                    @include('admin.layout._multi_lang_tab')
                </ul>

                <div  class="tab-content">

                  @if(isset($arr_lang) && sizeof($arr_lang)>0)
                        @foreach($arr_lang as $lang)

                            <div class="tab-pane fade {{ $lang['locale']=='en'?'in active':'' }}"  id="{{ $lang['locale'] }}">


                                <div class="form-group">
                                      @if($lang['locale'] == 'en')
                                        <label class="col-sm-3 col-lg-2 control-label" for="state">Fees Name 
                                      @else
                                          <label class="col-sm-3 col-lg-2 control-label" for="state">{{translation('fee_name')}}  
                                      @endif      
                                      <i class="red">*</i>
                                      
                                       </label>
                                        <div class="col-sm-4 col-lg-4 controls">
                                          
                                          @if($lang['locale'] == 'en')   
                                            <input type="text" name="name_{{$lang['locale']}}" class="form-control add-stundt" value="{{old('name_'.$lang['locale'])}}" placeholder="Enter fees" required=""  />
                                          @else
                                            <input type="text" name="name_{{$lang['locale']}}" class="form-control add-stundt"  value="{{old('name_'.$lang['locale'])}}" placeholder="{{translation('enter')}} {{translation('fee_name')}}"  />
                                          @endif  
                                          
                                          <span class='help-block'>{{ $errors->first('name_'.$lang['locale']) }}</span>

                                        </div> 
                                </div>
                         </div>     
                      @endforeach
                  @endif

                </div>
                
                <br>

                <div class="form-group">
                    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">

                        <a href="{{ url($module_url_path) }}" class="btn btn-primary">{{translation('back')}}</a> 
                        <input type="submit" id="save_btn" class="btn btn btn-primary" value="{{translation('save')}} "/>
                   
                    </div>
                </div>
                
              </form>
                
            </div>  
  

      </div>
    </div>
  </div>
  <!-- END Main Content --> 

  
  <script type="text/javascript">

  $(document).ready(function()
  {
    $('#form_validation').parsley().on('field:validated', function() 
    {
      var ok = $('.parsley-error').length === 0;
    })
    .on('form:submit', function() 
    {
       return true;
    });  

    

  }); 


 </script>

@endsection
