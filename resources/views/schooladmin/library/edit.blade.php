@extends('schooladmin.layout.master')                
@section('main_content')

<style type="text/css">
 .profile-img{width: 120px;
height: 120px;
border-radius: 50% !important;
overflow: hidden;
padding: 0;}
.profile-img img{height: 100% !important;width: 100% ;}
</style>

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="{{url('/')}}/school_admin/dashboard">{{translation('dashboard')}}</a>
        </li>
        <span class="divider">
            <i class="fa fa-angle-right"></i>
        </span>
        <li>
          <i class="{{$module_icon}}"></i>
          <a href="{{$module_url_path.'/manage_library_contents'}}">{{$module_title}}</a>
        </li>
        <span class="divider">
            <i class="fa fa-angle-right"></i>
        </span>
        
        <li class="active">{{$page_title}}</li>
    </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="fa fa-book"></i>{{$module_title}}</h1>

    </div>
</div>
<!-- END Page Title -->
<!-- BEGIN Main Content -->

<div class="row">
<div class="col-md-12">
   <div class="box box-navy_blue">
      <div class="box-title">
         <h3>
            <i class="fa {{$create_icon}}">
            </i>{{ isset($page_title)?$page_title:"" }} 
         </h3>
         <div class="box-tool">
         </div>
      </div>
      <div class="box-content">
         @include('schooladmin.layout._operation_status')
         <div class="tobbable">
           
               <div  class="tab-content">
                  <div class="row">
                          <div class="col-sm-12 col-md-12 col-lg-6">
                              
                        @if(isset($content['type']) && $content['type'] == "BOOK")      
                        <form method="post" onsubmit="return addLoader()"  action="{{$module_url_path}}/update_content/{{isset($details['id'])?base64_encode($details['id']):0}}"  class="form-horizontal" id="validation-form1" enctype ='multipart/form-data'>
                        {{ csrf_field() }}
                        <div id="book">
                            
                           <input type="hidden" value="BOOK" name="type">
                           <div class="form-group">
                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('purchase_date')}} <i class="red">*</i>
                              </label>
                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                                 <input class="form-control datepikr" name="purchase_date" id="datepicker" data-rule-required="true" placeholder="{{translation('select')}} {{translation('purchase_date')}}" pattern="^[0-9 \-]*$" type="text" value="{{isset($content['purchase_date'])?$content['purchase_date']:''}}" readonly />
                                 <span class='help-block'>{{ $errors->first('purchase_date') }}
                                 </span>
                              </div>
                           </div>
                           <div class="form-group">
                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('bill_number')}}
                              <i class="red">*</i>
                              </label>
                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                                 <input class="form-control" name="book_bill_no" id="book_bill_no" data-rule-required="true" placeholder="{{translation('enter')}} {{translation('bill_number')}}" type="text" pattern="[a-zA-Z0-9\- àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" value="{{isset($content['bill_no'])?$content['bill_no']:''}}">
                                 <span class='help-block'>{{ $errors->first('book_bill_no') }}
                                 </span>
                              </div>
                           </div>
                           <div class="form-group">
                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('isbn_number')}}
                              <i class="red">*</i>
                              </label>
                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                                 <input type="text" name="book_ISBN_no" class="form-control" data-rule-required="true" id="book_ISBN_no" value="{{isset($details['ISBN_no'])?$details['ISBN_no']:''}}" placeholder="{{translation('enter')}} {{translation('isbn_number')}}" pattern="^[\d -]+$" data-rule-minlength='13' data-rule-maxlength='17' >
                                 <span class='help-block'>{{ $errors->first('book_ISBN_no') }}
                                 </span>
                              </div>
                           </div>
                           
                           <div class="form-group">
                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('title')}}  
                              <i class="red">*</i> 
                              </label>
                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">        
                                 <input type="text" name="book_title"  id="book_title" class="form-control" data-rule-required="true" value="{{isset($details['title'])?$details['title']:''}}" placeholder="{{translation('enter')}} {{translation('title')}}">
                                 <span class="help-block">{{ $errors->first('book_title') }}</span>
                              </div>
                           </div>
                           <div class="form-group">
                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('author')}}  
                              <i class="red">*</i> 
                              </label>
                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">        
                                 <input type="text" name="book_author" id="book_author" pattern="[a-zA-Z\. àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" class="form-control" data-rule-required="true" value="{{isset($details['author'])?$details['author']:''}}" placeholder="{{translation('enter')}} {{translation('author_name')}}"/>
                                 <span class="help-block">{{ $errors->first('book_author') }}</span>
                              </div>
                           </div>
                           <div class="form-group">
                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('edition')}}  
                              <i class="red">*</i> 
                              </label>
                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">        
                                 <input type="text" name="book_edition" id="book_edition" class="form-control" pattern="[a-zA-Z0-9\- àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" data-rule-required="true" value="{{isset($details['edition'])?$details['edition']:''}}" placeholder="{{translation('enter')}} {{translation('edition')}}"/>
                                 <span class="help-block">{{ $errors->first('book_edition') }}</span>
                              </div>
                           </div>
                           <div class="form-group">
                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('book_category')}}   <i class="red">*</i>
                              </label>
                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                                 <select class="form-control" name="book_category" id="book_category" data-rule-required="true">
                                    <option value="">{{translation('select_book_category')}}</option>
                                    @if(isset($categories) && !empty($categories))
                                    @foreach($categories as $key => $category)
                                    <option value="{{$category['id']}}" @if(isset($content['category_id']) && $content['category_id'] == $category['id']) selected @endif>{{$category['category_name']}}</option>
                                    @endforeach
                                    @endif
                                 </select>
                                 <span class="help-block">{{ $errors->first('book_category') }}</span>
                              </div>
                           </div>
                           <div class="form-group">
                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('publisher')}}  
                              <i class="red">*</i> 
                              </label>
                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">        
                                 <input type="text" name="book_publisher"  id="book_publisher" class="form-control" data-rule-required="true" pattern="[a-zA-Z\. àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" value="{{isset($details['publisher'])?$details['publisher']:''}}" placeholder="{{translation('enter')}} {{translation('publisher')}}" value="{{old('book_publisher')}}">
                                 <span class="help-block">{{ $errors->first('book_publisher') }}</span>
                              </div>
                           </div>
                           <div class="form-group">
                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('no_of_copies')}} <i class="red">*</i>    
                              </label>
                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">        
                                 <input type="text" name="no_of_copies" value='{{isset($details['no_of_books'])?$details['no_of_books']:''}}' class="form-control" data-rule-required="true" data-rule-digits="true" data-rule-required="true" id="no_of_copies" placeholder="{{translation('enter')}} {{translation('no_of_copies')}}"  >
                                 <span class="help-block">{{ $errors->first('no_of_copies') }}</span>
                              </div>
                           </div>
                           <div class="form-group">
                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('shelf_no')}}  
                              <i class="red">*</i> 
                              </label>
                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">        
                                 <input type="text" name="book_shelf_no"  id="book_shelf_no" class="form-control" data-rule-required="true" pattern="[a-zA-Z0-9\- àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" value="{{isset($details['shelf_no'])?$details['shelf_no']:''}}" placeholder="{{('enter')}} {{translation('shelf_no')}}">
                                 <span class="help-block">{{ $errors->first('book_shelf_no') }}</span>
                              </div>
                           </div>
                           <div class="form-group">
                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('book_position')}}
                              <i class="red">*</i>
                              </label>
                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                                 <input type="text" name="book_position" id="book_position" class="form-control"  data-rule-required="true" pattern="[a-zA-Z0-9\- àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" value="{{isset($details['book_position'])?$details['book_position']:''}}" placeholder="{{translation('enter')}} {{translation('book_position')}}">
                                 <span class='help-block'>{{ $errors->first('book_position') }}
                                 </span>
                              </div>
                           </div>
                           <div class="form-group">
                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('book_cost')}}({{config('app.project.currency')}})
                              <i class="red">*</i>
                              </label>
                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                                 <input type="text" name="book_cost" id="book_cost" class="form-control" data-rule-required="true" data-rule-number='true'  value="{{isset($details['cost'])?$details['cost']:''}}" placeholder="{{translation('enter')}} {{translation('book_cost')}}" data-rule-min="1" data-rule-maxlength="10">
                                 <span class='help-block'>{{ $errors->first('book_cost') }}
                                 </span>
                              </div>
                           </div>
                            <div class="form-group">
                              <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                                 <a href="{{ url($module_url_path.'/manage_library_contents') }}" class="btn btn-primary">{{translation('back')}}</a> 
                                 <button type="submit" id="submit_button" class="btn btn-primary">{{translation('update')}}</button>
                              </div>
                           </div>
                        </div>
                     </form>
                     @endif
                     @if(isset($content['type']) && $content['type'] == "DISSERTATION")
                     <form method="post" onsubmit="return addLoader()"  action="{{$module_url_path}}/update_content/{{isset($details['id'])?base64_encode($details['id']):0}}"  class ="form-horizontal" id="validation-form2" enctype ='multipart/form-data'>
                        {{ csrf_field() }}
                        <div id="dissertation">
                           <input type="hidden" value="DISSERTATION" name="type">
                           <div class="form-group">
                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('title')}}  
                              <i class="red">*</i> 
                              </label>
                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">        
                                 <input type="text" name="diss_title"  id="diss_title" class="form-control" data-rule-required="true" value="{{isset($details['title'])?$details['title']:''}}" placeholder="{{translation('enter')}} {{translation('title')}}">
                                 <span class="help-block">{{ $errors->first('diss_title') }}</span>
                              </div>
                           </div>
                           <div class="form-group">
                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('author')}}  
                              <i class="red">*</i> 
                              </label>
                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">        
                                 <input type="text" name="diss_author" id="diss_author" pattern="[a-zA-Z\. àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" class="form-control" data-rule-required="true" value="{{isset($details['author'])?$details['author']:''}}" placeholder="{{translation('enter')}} {{translation('author_name')}}"/>
                                 <span class="help-block">{{ $errors->first('diss_author') }}</span>
                              </div>
                           </div>
                           <div class="form-group">
                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('book_category')}}   <i class="red">*</i>
                              </label>
                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                                 <select class="form-control" name="diss_category" id="diss_category" data-rule-required="true">
                                    <option value="">{{translation('select_book_category')}}</option>
                                    @if(isset($categories) && !empty($categories))
                                    @foreach($categories as $key => $category)
                                    <option value="{{$category['id']}}"  @if(isset($content['category_id']) && $content['category_id'] == $category['id']) selected @endif>{{$category['category_name']}}</option>
                                    @endforeach
                                    @endif
                                 </select>
                                 <span class="help-block">{{ $errors->first('diss_category') }}</span>
                              </div>
                           </div>
                           <div class="form-group">
                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('no_of_copies')}} <i class="red">*</i> 
                              </label>
                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">        
                                 <input type="text" name="diss_no_of_copies" value='{{isset($details['no_of_books'])?$details['no_of_books']:''}}' class="form-control" data-rule-digits="true" data-rule-required="true" id="diss_no_of_copies" placeholder="{{translation('enter')}} {{translation('no_of_copies')}}"    >
                                 <span class="help-block">{{ $errors->first('diss_no_of_copies') }}</span>
                              </div>
                           </div>
                           <div class="form-group">
                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('academic_year')}}   <i class="red">*</i>
                              </label>
                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                                 <select class="form-control" name="academic_year" id="academic_year" data-rule-required="true">
                                    <option value="">{{translation('select_academic_year')}}</option>
                                    @if(isset($arr_academic_years) && !empty($arr_academic_years))
                                    @foreach($arr_academic_years as $value)
                                    <option value="{{$value['id']}}"@if(isset($details['academic_year_id']) && $details['academic_year_id'] == $value['id']) selected @endif>{{$value['academic_year']}}</option>
                                    @endforeach
                                    @endif
                                 </select>
                                 <span class="help-block">{{ $errors->first('academic_year') }}</span>
                              </div>
                           </div>
                           <div class="form-group">
                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('shelf_no')}}  
                              <i class="red">*</i> 
                              </label>
                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">        
                                 <input type="text" name="diss_book_shelf_no"  id="diss_book_shelf_no" class="form-control" pattern="[a-zA-Z0-9\- àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" value="{{isset($details['shelf_no'])?$details['shelf_no']:''}}" placeholder="{{('enter')}} {{translation('shelf_no')}}" data-rule-required="true">
                                 <span class="help-block">{{ $errors->first('diss_book_shelf_no') }}</span>
                              </div>
                           </div>
                           <div class="form-group">
                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('book_position')}}
                              <i class="red">*</i>
                              </label>
                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                                 <input type="text" name="diss_position" id="diss_position" class="form-control" pattern="[a-zA-Z0-9\- àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" value="{{isset($details['book_position'])?$details['book_position']:''}}" placeholder="{{translation('enter')}} {{translation('book_position')}}" data-rule-required="true">
                                 <span class='help-block'>{{ $errors->first('diss_position') }}
                                 </span>
                              </div>
                           </div>
                            <div class="form-group">
                              <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                                 <a href="{{ url($module_url_path.'/manage_library_contents') }}" class="btn btn-primary">{{translation('back')}}</a> 
                                 <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('update')}}</button>
                              </div>
                           </div>
                        </div>
                     </form>
                     @endif
                     @if(isset($content['type']) && $content['type'] == "CD")
                     <form method="post" onsubmit="return addLoader()"  action="{{$module_url_path}}/update_content/{{isset($details['id'])?base64_encode($details['id']):0}}"  class="form-horizontal" id="validation-form3" enctype ='multipart/form-data'>
                        {{ csrf_field() }}
                        <div id="cd">
                           <input type="hidden" value="CD" name="type">
                           <div class="form-group">
                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('title')}}  
                              <i class="red">*</i> 
                              </label>
                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">        
                                 <input type="text" name="cd_title"  id="cd_title" class="form-control" data-rule-required="true" value="{{isset($details['title'])?$details['title']:''}}" placeholder="{{translation('enter')}} {{translation('title')}}">
                                 <span class="help-block">{{ $errors->first('cd_title') }}</span>
                              </div>
                           </div> 
                           <div class="form-group">
                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('cd_category')}}<i class="red">*</i>
                              </label>
                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                                 <select class="form-control" name="cd_category" id="cd_category">
                                    <option value="">{{translation('select_cd_category')}}</option>
                                    @if(isset($categories) && !empty($categories))
                                    @foreach($categories as $key => $category)
                                    <option value="{{$category['id']}}" @if(isset($content['category_id']) && $content['category_id'] == $category['id']) selected @endif>{{$category['category_name']}}</option>
                                    @endforeach
                                    @endif
                                 </select>
                                 <span class="help-block">{{ $errors->first('cd_category') }}</span>
                              </div>
                           </div>

                           <div class="form-group">
                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('type')}}   <i class="red">*</i>
                              </label>
                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                                 <select class="form-control" name="cd_type" id="cd_type" data-rule-required="true">
                                    <option value="audio" @if(isset($details['cd_type']) && $details['cd_type'] == "AUDIO") selected @endif>{{translation('audio')}}</option>
                                    <option value="video" @if(isset($details['cd_type']) && $details['cd_type'] == "VIDEO") selected @endif>{{translation('video')}}</option>
                                 </select>
                                 <span class="help-block">{{ $errors->first('cd_type') }}</span>
                              </div>
                           </div>
                           <div class="form-group">
                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('no_of_copies')}} <i class="red">*</i>    
                              </label>
                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">        
                                 <input type="text" name="cd_no_of_copies" value='{{isset($details['no_of_books'])?$details['no_of_books']:''}}' class="form-control" data-rule-digits="true" data-rule-required="true" id="cd_no_of_copies" placeholder="{{translation('enter')}} {{translation('no_of_copies')}}"    >
                                 <span class="help-block">{{ $errors->first('cd_no_of_copies') }}</span>
                              </div>
                           </div>
                           <div class="form-group">
                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('shelf_no')}}  
                              <i class="red">*</i> 
                              </label>
                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">        
                                 <input type="text" name="cd_shelf_no"  id="cd_shelf_no" class="form-control" data-rule-required="true" pattern="[a-zA-Z0-9\- àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" value="{{isset($details['shelf_no'])?$details['shelf_no']:''}}" placeholder="{{('enter')}} {{translation('shelf_no')}}">
                                 <span class="help-block">{{ $errors->first('cd_shelf_no') }}</span>
                              </div>
                           </div>
                           <div class="form-group">
                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('cd_position')}}
                              <i class="red">*</i>
                              </label>
                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                                 <input type="text" name="cd_position" id="cd_position" class="form-control" data-rule-required="true" pattern="[a-zA-Z0-9\- àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" value="{{isset($details['book_position'])?$details['book_position']:''}}" placeholder="{{translation('enter')}} {{translation('cd_position')}}">
                                 <span class='help-block'>{{ $errors->first('cd_position') }}
                                 </span>
                              </div>
                           </div>
                           <div class="form-group">
                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('purchase_date')}}
                              </label>
                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                                 <input class="form-control datepikr" name="purchase_date" id="datepicker2" placeholder="{{translation('select')}} {{translation('purchase_date')}}" pattern="^[0-9 \-]*$" type="text" value="{{isset($content['purchase_date'])?$content['purchase_date']:''}}" readonly />
                                 <span class='help-block'>{{ $errors->first('purchase_date') }}
                                 </span>
                              </div>
                           </div>
                           <div class="form-group">
                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('amount')}} ({{config('app.project.currency')}})
                              </label>
                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                                 <input type="text" name="cd_cost" id="cd_cost" class="form-control" data-rule-number='true'  value="{{isset($details['cost'])?$details['cost']:''}}" placeholder="{{translation('enter')}} {{translation('amount')}}" data-rule-min="1" data-rule-maxlength="10">
                                 <span class='help-block'>{{ $errors->first('cd_cost') }}
                                 </span>
                              </div>
                           </div>
                           <div class="form-group">
                              <label class="col-xs-12 col-sm-4 col-md-4 col-lg-4 control-label">{{translation('bill_number')}}
                              </label>
                              <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                                 <input class="form-control" name="cd_bill_no" id="cd_bill_no" placeholder="{{translation('enter')}} {{translation('bill_number')}}" type="text" pattern="[a-zA-Z0-9\- àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ]+$" value="{{isset($content['bill_no'])?$content['bill_no']:''}}">
                                 <span class='help-block'>{{ $errors->first('cd_bill_no') }}
                                 </span>
                              </div>
                           </div>
                            <div class="form-group">
                              <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">
                                 <a href="{{ url($module_url_path.'/manage_library_contents') }}" class="btn btn-primary">{{translation('back')}}</a> 
                                 <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('update')}}</button>
                              </div>
                           </div>
                        </div>
                     </form>
                     @endif
                            </div>
                        </div> 
                      </div>

                     
              </div>

             </div>
    </div>
</div>
</div>  
<script>
 $(function () {
        $("#datepicker").datepicker({
            todayHighlight: true,
            autoclose: true,
            format:'yyyy-mm-dd',
            
        });
        $("#datepicker2").datepicker({
            todayHighlight: true,
            autoclose: true,
            format:'yyyy-mm-dd',
            
        });
        
    });
</script>
<!-- END Main Content --> 
@endsection