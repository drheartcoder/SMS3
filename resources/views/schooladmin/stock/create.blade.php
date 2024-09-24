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
                            <h3><i class="fa {{$create_icon}}"></i>{{translation('add')}} {{translation('stock')}}</h3>
                            <div class="box-tool">
                            </div>
                        </div>
                        <div class="box-content">
                            @include('schooladmin.layout._operation_status')
                            <form method="POST" action="{{$module_url_path}}/store" accept-charset="UTF-8" class="form-horizontal" id="validation-form1" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                 
                                <div class="form-group">
                                    <label class="col-sm-3 col-lg-2 control-label">{{translation('product_id')}} <i class="red">*</i></label>
                                    <div class="col-sm-9 col-lg-4 controls">
                                        <input class="form-control" name="product_id" id="product_id" placeholder="{{translation('enter')}} {{translation('product_id')}}" type="text" data-rule-required="true" pattern="^[a-zA-ZàâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ0-9 ]+$">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 col-lg-2 control-label">{{translation('product_name')}} <i class="red">*</i></label>
                                    <div class="col-sm-9 col-lg-4 controls">
                                        <input class="form-control" name="product_name" id="product_name" pattern="^[a-zA-ZàâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ0-9 \-]+$" placeholder="{{translation('enter')}} {{translation('product_name')}}" type="text" data-rule-required="true">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 col-lg-2 control-label">{{translation('product_image')}}</label>
                                    <div class="col-sm-9 col-lg-4 controls">
                                        <div class="fileupload fileupload-new" data-provides="fileupload">
                                            <div class="fileupload-new img-thumbnail profile-img img">
                                                      <img src="{{url('/')}}/images/default-old.png" height="100px" width="150px" border="3">
                                            </div>
                                            <div class="fileupload-preview fileupload-exists img-thumbnail profile-img" ></div>
                                            <div>
                                              <span class="btn btn-default btn-file" style="height:32px;">
                                              <span class="fileupload-new">{{translation('select_image')}}</span>
                                              <span class="fileupload-exists">{{translation('change')}}</span>
                                              <input type="file"  data-validation-allowing="jpg, png, gif" class="file-input news-image validate-image" name="product_image" id="image"  /><br>
                                              </span>
                                              <a href="#" id="remove" class="btn btn-default fileupload-exists" data-dismiss="fileupload">{{translation('remove')}}</a>
                                            </div>
                                            <i class="red"> {!! image_validate_note(250,250,2000,2000) !!} </i>
                                            <span for="product_image" id="err-image" class="help-block">{{ $errors->first('product_image') }}</span>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-sm-6 col-lg-5 control-label help-block-red" style="color:#b94a48;" id="err_logo"></div>
                                        <br/>
                                        <div class="col-sm-6 col-lg-5 control-label help-block-green" style="color:#468847;" id="success_logo"></div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 col-lg-2 control-label">{{translation('quantity')}} <i class="red">*</i></label>
                                    <div class="col-sm-9 col-lg-4 controls">
                                        <input class="form-control" name="quantity" id="quantity" placeholder="{{translation('enter')}} {{translation('quantity')}}" type="text" data-rule-required="true"  onKeyUp="totalValue();" data-rule-digits="true" data-rule-min="0">
                                    </div>
                                </div>                                        

                                <div class="form-group">
                                    <label class="col-sm-3 col-lg-2 control-label">{{translation('unit_price')}} <i class="red">*</i></label>
                                    <div class="col-sm-9 col-lg-4 controls">
                                        <input class="form-control" name="unit_price" id="unit_price" placeholder="{{translation('enter')}} {{translation('unit_price')}}" type="text" data-rule-required="true" onKeyUp="totalValue();" data-rule-number="true" data-rule-min="0">
                                    </div>
                                </div>     

                                <div class="form-group">
                                    <label class="col-sm-3 col-lg-2 control-label">{{translation('total_price')}} <i class="red">*</i></label>
                                    <div class="col-sm-9 col-lg-4 controls">
                                        <input class="form-control" name="total_price" id="total_price" placeholder="{{translation('total_price')}}" type="text" data-rule-required="true" readonly style="cursor: pointer" data-rule-required="true">
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

   function totalValue()
   {
     $('#total_price').val();
      var quantity   = $('#quantity').val();
      var unit_price = $('#unit_price').val();

      if(isNaN(quantity))
      {
        quantity = 0;
      }
      if(isNaN(unit_price))
      {
        unit_price = 0;
      }
      var total = quantity * unit_price;
      $('#total_price').val(total);
   }
 </script>
      
@endsection
