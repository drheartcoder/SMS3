@extends('schooladmin.layout.master')    
@section('main_content')
<link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/chosen-bootstrap/chosen.min.css" />



<style type="text/css">
 .profile-img{width: 130px;
height: 130px;
border-radius: 50% !important;
overflow: hidden;
padding: 0;}
.profile-img img{height: 100% !important;width: 100% ;}
</style>


<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
  <ul class="breadcrumb">
    <li>
      <i class="fa fa-home">
      </i>
      <a href="{{ url($school_admin_panel_slug.'/dashboard') }}"> {{translation('dashboard')}}
      </a>
    </li>

    <span class="divider">
      <i class="fa fa-angle-right">
      </i>
      <i class="fa {{$create_icon}}">
      </i>
    </span> 
    <li ><a href="{{$module_url_path}}">{{ isset($module_title)?$module_title:"" }}</a>
    </li>

    <span class="divider">
      <i class="fa fa-angle-right">
      </i>
      <i class="fa {{$module_icon}}">
      </i>
    </span> 
    <li class="active">{{ isset($page_title)?$page_title:"" }}
    </li>
    
  </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
 <div class="page-title new-agetitle">
    <div>
        <h1><i class="fa {{$module_icon}}"></i>{{$module_title}}</h1>
    </div>
</div>

<!-- END Page Title -->

<!-- BEGIN Tiles -->
            <div class="row">
                <div class="col-md-12">
                    <div class="box  box-navy_blue">
                        <div class="box-title">
                            <h3><i class="fa {{$create_icon}}"></i>{{translation('add')}} {{translation('product')}}</h3>
                            <div class="box-tool">
                            </div>
                        </div>
                        <div class="box-content">
                            @include('schooladmin.layout._operation_status')
                            <form method="POST" onsubmit="return addLoader()" action="{{$module_url_path}}/store" accept-charset="UTF-8" class="form-horizontal" id="validation-form1" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label class="col-sm-3 col-md-4 col-lg-2 control-label">{{translation('product_type')}} <i class="red">*</i></label>
                                    <div class="col-sm-9 col-md-8 col-lg-4 controls">
                                        <select class="form-control chosen" data-placeholder="{{translation('select')}} {{translation('product_type')}}" tabindex="1" data-rule-required="true" name="product_type">
                                            <option value=""> </option>
                                              @if(isset($arr_types) && count($arr_types)>0)
                                                @foreach($arr_types as $key => $types)
                                                  <option value="{{isset($types['id'])?$types['id']:0}}">{{isset($types['type'])?ucwords($types['type']):''}}</option>
                                                @endforeach
                                              @endif
                                         </select>
                                         <span class="help-block">{{ $errors->first('product_type') }}</span>
                                    </div>
                                    
                                    
                                </div>                                
                                <div class="form-group">
                                    <label class="col-sm-3 col-lg-2 control-label">{{translation('product_image')}}</label>
                                    <div class="col-sm-9 col-lg-4 controls">
                                        <div class="fileupload fileupload-new" data-provides="fileupload">
                                            <div class="fileupload-new img-thumbnail profile-img img">
                                                      <img src="{{url('/')}}/images/default_food.jpg" height="100px" width="150px" border="3">
                                            </div>
                                            <div class="fileupload-preview fileupload-exists img-thumbnail profile-img" ></div>
                                            <div>
                                              <span class="btn btn-default btn-file" style="height:32px;">
                                              <span class="fileupload-new">Select Image</span>
                                              <span class="fileupload-exists">Change</span>
                                              <input type="file"  data-validation-allowing="jpg, png, gif" class="file-input news-image validate-image" name="product_image" id="image"  /><br>
                                              </span>
                                              <a href="#" id="remove" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
                                            </div>
                                            <i class="red"> {!! image_validate_note(250,250,2000,2000) !!} </i>
                                            <span for="image" id="err-image" class="help-block">{{ $errors->first(' image') }}</span>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-sm-6 col-lg-5 control-label help-block-red" style="color:#b94a48;" id="err_logo"></div>
                                        <br/>
                                        <div class="col-sm-6 col-lg-5 control-label help-block-green" style="color:#468847;" id="success_logo"></div>
                                    </div>
                                </div>                               
                                
                                <div class="form-group">
                                    <label class="col-sm-3 col-lg-2 control-label">{{translation('product_id')}}<i class="red">*</i></label>
                                    <div class="col-sm-9 col-lg-4 controls">
                                        <input class="form-control" name="product_id" id="product_id" placeholder="{{translation('enter')}} {{translation('product_id')}}" type="text" data-rule-required="true" data-rule-alphanumeric="true"
                                        data-msg-alphanumeric="{{translation('letters_and_numbers_only')}}"
                                       value="{{old('product_id')}}">
                                        <span class="help-block">{{ $errors->first('product_id')}}</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 col-lg-2 control-label">{{translation('product_name')}}<i class="red">*</i></label>
                                    <div class="col-sm-9 col-lg-4 controls">
                                        <input class="form-control" name="product_name" id="product_name" placeholder="{{translation('enter')}} {{translation('product_name')}}" type="text" data-rule-required="true" value="{{old('product_name')}}">
                                        <span class="help-block">{{ $errors->first('product_name') }}</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 col-lg-2 control-label">{{translation('product_description')}}</label>
                                    <div class="col-sm-9 col-lg-4 controls">
                                        <textarea name="product_description" placeholder="{{translation('enter')}} {{translation('product_description')}}" class="form-control" rows="3"> {{old('product_description')}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 col-lg-2 control-label">{{translation('product_price')}}(MAD)<i class="red">*</i></label>
                                    <div class="col-sm-9 col-lg-4 controls">
                                        <input class="form-control commonNumber" name="product_price" placeholder="{{translation('product_price')}}" type="text" data-rule-required="true"   data-rule-number="true" min="1"  value="{{old('product_price')}}">
                                        <span class="help-block">{{ $errors->first('product_price') }}</span>
                                    </div>
                                </div>
                                     
                                                                
                                <div class="form-group">
                                    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                                        <a href="{{$module_url_path}}" class="btn btn btn-primary">{{translation('back')}}</a>
                                          <input class="btn btn btn-primary" value="{{translation('save')}}" type="submit">
                                    </div>
                                </div>
                                                                        
                            </form>
                        </div>
                    </div>
                </div>  
            </div>    


 <script>
   $(document).on("change",".validate-image", function()
    {            
        var file=this.files;
        validateImage(this.files, 250,250);
    });
 </script>
      
@endsection
