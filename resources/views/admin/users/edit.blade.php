    @extends('admin.layout.master')                

    @section('main_content')
    <!-- BEGIN Page Title -->

    {{-- <link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/data-tables/latest/dataTables.bootstrap.min.css"> --}}
    <div class="page-title">
        <div>

        </div>
    </div>
    <!-- END Page Title -->

    <!-- BEGIN Breadcrumb -->
    <div id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url($admin_panel_slug.'/dashboard') }}">{{translation('dashboard')}}</a>
            </li>
            <span class="divider">
                <i class="fa fa-angle-right"></i>
                <i class="fa fa-question-circle"></i>
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
                <a data-action="collapse" href="#"></a>
                <a data-action="close" href="#"></a>
            </div>
        </div>
        <div class="box-content">

          @include('admin.layout._operation_status')  
          
          <div class="tabbable">
                <form class="form-horizontal" id="validation-form" method="POST" action="{{ $module_url_path }}/update/{{$enc_id}}" enctype="multipart/form-data">
                  {{ csrf_field() }}
                <ul  class="nav nav-tabs">
                   @include('admin.layout._multi_lang_tab')
                </ul>

                  
                <div  class="tab-content">
                  
                  @if(isset($arr_lang) && sizeof($arr_lang)>0)
                    @foreach($arr_lang as $lang_key => $lang)

                      <div class="tab-pane fade {{ $lang['locale']=='en'?'in active':'' }}"  id="{{ $lang['locale'] }}">

                            @if($lang_key==0)
                              <div class="form-group">
                                 <label class="col-sm-3 col-lg-2 control-label">{{translation('profile_image',$lang['locale'])}} <i class="red">*</i> </label>
                                 <div class="col-sm-9 col-lg-10 controls">
                                    <div class="fileupload fileupload-new" data-provides="fileupload">
                                       <div class="fileupload-new img-thumbnail" style="width: 200px; height: 150px;">
                                          @if(isset($arr_data['user']['profile_image']) && !empty($arr_data['user']['profile_image']))
                                          <img src="{{$user_profile_public_img_path.$arr_data['user']['profile_image'] }}">
                                          @else
                                          <img src="{{url('/').'/uploads/default.png' }}">
                                          @endif
                                       </div>
                                       <div class="fileupload-preview fileupload-exists img-thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                                       <div>
                                          <span class="btn btn-default btn-file" style="height:32px;">
                                          <span class="fileupload-new">Select Image</span>
                                          <span class="fileupload-exists">Change</span>
                                          <input type="file"  data-validation-allowing="jpg, png, gif" class="file-input news-image validate-image" name="image" id="image"  /><br>
                                          <input type="hidden" class="file-input " name="oldimage" id="oldimage"  
                                             value="{{ $arr_data['user']['profile_image'] }}"/>
                                          </span>
                                          <a href="#" id="remove" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
                                       </div>
                                       <i class="red"> {!! image_validate_note(250,250) !!} </i>
                                       <span for="image" id="err-image" class="help-block">{{ $errors->first(' image') }}</span>
                                    </div>
                                 </div>
                                 <div class="clearfix"></div>
                                 <div class="col-sm-6 col-lg-5 control-label help-block-red" style="color:#b94a48;" id="err_logo"></div>
                                 <br/>
                                 <div class="col-sm-6 col-lg-5 control-label help-block-green" style="color:#468847;" id="success_logo"></div>
                              </div>
                            @endif
                              
                              <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label">{{translation('first_name',$lang['locale'])}} 
                                    <i class="red">*</i> 
                                </label>
                                <div class="col-sm-4 col-lg-4 controls">
                                    <input type="text" name="first_name_{{$lang['locale']}}" data-rule-required='true' data-rule-pattern="^[a-zA-Z0-9 àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" class="form-control" value="{{$arr_data['user_details'][$lang_key]['first_name']}}" />
                                    <span class='help-block'>{{ $errors->first('first_name_'.$lang['locale']) }}</span>
                                </div>
                              </div>
                              <div class="clearfix"></div>

                              <div class="form-group">
                                <label class="col-sm-3 col-lg-2 control-label">{{translation('last_name',$lang['locale'])}} 
                                  <i class="red">*</i> 
                                </label>
                                <div class="col-sm-4 col-lg-4 controls">        
                                    <input type="text" name="last_name_{{$lang['locale']}}" class="form-control"  value="{{$arr_data['user_details'][$lang_key]['last_name']}}" data-rule-pattern="^[a-zA-Z0-9 àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$"  data-rule-required="true"/>  
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
                                    <input type="text" name="email" class="form-control"  value="{{$arr_data['user']['email']}}" data-rule-required="true" data-rule-email="true" id="email" readonly="true" />  
                                    <span class='help-block'>{{ $errors->first('email') }}</span>
                                    <span id="err_email" style="color: red"></span>
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
                                    <input type="text" name="mobile_no" class="form-control"  value="{{$arr_data['user']['mobile_no']}}" data-rule-required="true" data-rule-digits='true' data-rule-minlength='10' data-rule-maxlength='12'/>  
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
                                    <input type="text" name="telephone_no" class="form-control"  value="{{$arr_data['user']['telephone_no']}}" placeholder="{{translation('enter',$lang['locale'])}} {{translation('telephone_number',$lang['locale'])}}" />  
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
                                    <input type="text" name="national_id" class="form-control"  value="{{$arr_data['user']['national_id']}}" placeholder="{{translation('enter',$lang['locale'])}} {{translation('national_id',$lang['locale'])}}" data-rule-required="true"/>  
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
                                    <input type="date" name="birth_date" class="form-control"  value="{{$arr_data['user']['birth_date']}}" placeholder="{{translation('enter',$lang['locale'])}} {{translation('birth_date',$lang['locale'])}}" data-rule-required="true"/>  
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
                                  <input type="hidden" name="latitude"  id="latitude" class="field" value="{{$arr_data['user']['latitude']}}">
                                <input type="hidden" name="longitude"  id="longitude" class="field"  value="{{$arr_data['user']['longitude']}}" >
                                <input type="text" name="address" id="location" value="{{$arr_data['user']['address']}}" class="form-control" />
                                
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
                                    <input type="text" name="city"  id="city" class="form-control"  value="{{$arr_data['user']['city']}}" placeholder="{{translation('enter')}} {{translation('city',$lang['locale'])}}" />
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
                                    <input type="text" name="country"  id="country" class="form-control"  value="{{$arr_data['user']['country']}}" placeholder="{{translation('enter')}} {{translation('country',$lang['locale'])}}"/>
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
                          <button type="submit" class="btn btn-primary">{{translation('update')}}</button> 
                       </div>
                    </div>
                </form>
            </div>

         
</div>
</div>
</div>

<!-- END Main Content -->
<script type="text/javascript">
    function saveTinyMceContent()
    {
      tinyMCE.triggerSave();
    }
    $(document).ready(function()
    {
            tinymce.init({
              selector: 'textarea',
              height:350,
              plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table contextmenu paste code'
              ],
              valid_elements : '*[*]',
              toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image ',
              content_css: [
                '//fast.fonts.net/cssapi/e6dc9b99-64fe-4292-ad98-6974f93cd2a2.css',
                '//www.tinymce.com/css/codepen.min.css'
              ]
            });
    });
  </script>

  <script type="text/javascript">
   $(document).on("change",".validate-image", function()
    {            
        var file=this.files;
        validateImage(this.files, 250,250);
    });
  </script>

@stop                    
