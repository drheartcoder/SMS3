@extends('schooladmin.layout.master')                
@section('main_content')

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
          <a href="{{$module_url_path}}">{{$module_title}}</a>
        </li>

        <span class="divider">
            <i class="fa fa-angle-right"></i>
        </span>
        <li> 
          <i class="{{$module_icon}}"></i>
          <a href="{{$module_url_path1}}">{{translation('return_book')}}</a>
        </li>

        <span class="divider">
          <i class="fa fa-angle-right"></i>
        </span>
            <i class="fa fa-eye"></i>
            <li class="active">{{$page_title}}</li>
    </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
    <div>
        <h1>{{$page_title}}</h1>

    </div>
</div>
<!-- END Page Title -->

<!-- BEGIN Main Content -->
{{-- <div class="row"> --}}

   <div class="box {{ $theme_color }}">
      <div class="box-title">
         <h3>
            <i class="fa fa-list"></i>
           {{str_singular(translation('reissuereturn_books'))}} {{translation('details')}}  
         </h3>
         <div class="box-tool">
         </div>
      </div>
      
          <div class="clearfix"></div>
          <div class="box-content view-details-seciton-main">
            <div class="row">
              <input type="hidden" name="type" id="type" value="{{isset($arr_data['library_content']['type']) ? $arr_data['library_content']['type'] : ''}}">
              <div id="book">
                    <div class="col-md-12">
                        <div class="form-group">
                           <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('title')}} </b>: </label>
                           <div class="col-sm-9 col-lg-4 controls">
                             {{isset($arr_data['book_details']['title']) ? ucfirst($arr_data['book_details']['title']) : '-'}}
                           </div>
                        </div><div class="clearfix"></div>

                        <div class="form-group">
                           <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('author')}} </b>: </label>
                           <div class="col-sm-9 col-lg-4 controls">
                              {{isset($arr_data['book_details']['author']) ? ucfirst($arr_data['book_details']['author']) : '-'}}
                           </div>
                        </div><div class="clearfix"></div>

                        <div class="form-group">
                           <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('isbn_number')}}  </b>: </label>
                           <div class="col-sm-9 col-lg-4 controls">
                              {{isset($arr_data['book_details']['ISBN_no']) ? $arr_data['book_details']['ISBN_no'] : '-'}}
                           </div>
                        </div><div class="clearfix"></div>

                        <div class="form-group">
                           <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('book_number')}}  </b>: </label>
                           <div class="col-sm-9 col-lg-4 controls">
                              {{isset($arr_data['book_details']['book_no']) ? $arr_data['book_details']['book_no'] : '-'}}
                           </div>
                        </div><div class="clearfix"></div>

                        <div class="form-group">
                           <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('user_type')}}  </b>: </label>
                           <div class="col-sm-9 col-lg-4 controls">
                              {{isset($arr_data['user_type']) ? ucfirst($arr_data['user_type']) : '-'}}
                           </div>
                        </div><div class="clearfix"></div>
                       
                        <div class="form-group">
                           <label class="col-sm-4 col-lg-4 control-label"> <b>{{translation('user_id')}}</b> :</label>
                           <div class="col-sm-9 col-lg-4 controls">
                              {{isset($arr_data['user_id']) ? $arr_data['user_id'] : '-'}}
                           </div>
                        </div><div class="clearfix"></div>

                        <div class="form-group">
                           <label class="col-sm-4 col-lg-4 control-label"> <b>{{translation('issue_date')}}</b> :</label>
                           <div class="col-sm-9 col-lg-4 controls">
                              {{isset($arr_data['issue_date']) ? getDateFormat($arr_data['issue_date']) : '-'}}
                           </div>
                        </div><div class="clearfix"></div>

                         <div class="form-group">
                           <label class="col-sm-4 col-lg-4 control-label"> <b>{{translation('due_date')}}</b> :</label>
                           <div class="col-sm-9 col-lg-4 controls">
                              {{isset($arr_data['due_date']) ? getDateFormat($arr_data['due_date']) : '-'}}
                           </div>
                        </div><div class="clearfix"></div>

                        @if(isset($arr_data['status']) && $arr_data['status']=='RETURNED')
                            <div class="form-group">
                               <label class="col-sm-4 col-lg-4 control-label"> <b>{{translation('return_date')}}</b> :</label>
                               <div class="col-sm-9 col-lg-4 controls">
                                  {{isset($arr_data['return_date']) ? getDateFormat($arr_data['return_date']) : '-'}}
                               </div>
                            </div><div class="clearfix"></div>
                        @endif 

                        @if(isset($arr_data['status']) && $arr_data['status']=='REISSUE')
                            <div class="form-group">
                               <label class="col-sm-4 col-lg-4 control-label"> <b>{{translation('no_of_reissued')}}</b> :</label>
                               <div class="col-sm-9 col-lg-4 controls">
                                  {{isset($arr_data['no_of_reissued']) ? $arr_data['no_of_reissued'] : '-'}}
                               </div>
                            </div><div class="clearfix"></div>
                        @endif 
                    </div>
                </div>

                <div id="dissertation" hidden="true">
                </div>

                <div id="cd" hidden="true">
                </div>
              </div>
              <div class="form-group">
                   <div class="col-sm-9 col-lg-12 controls">
                      <a href="{{ $module_url_path1 }}" class="btn btn-primary" style="float: right;" > <i class="fa fa-arrow-left"></i> Back </a>
                   </div>
              </div><div class="clearfix"></div>
          </div>
          </div>
      </div>
      </div>
    
{{-- </div> --}}
<script>
  $(document).ready(function(){
    var type =  $('#type').val();

        if(type == "CD")
        {
          $('#cd').show();
          $('#book').hide();
          $('#dissertation').hide();
        }
        else if(type == "DISSERTATION")
        {
          $('#dissertation').show();
          $('#book').hide();
          $('#cd').hide();
        }
        else if(type == "BOOK")
        {
          $('#book').show();
          $('#dissertation').hide();
          $('#cd').hide(); 
        }
  });
</script>
<!-- END Main Content -->
@stop

