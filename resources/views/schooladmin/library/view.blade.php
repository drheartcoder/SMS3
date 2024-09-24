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
            <i class="fa fa-eye"></i>
            <li class="active">{{$page_title}}</li>
    </ul>
</div>
<!-- END Breadcrumb -->


<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="{{$module_icon}}"></i>{{$module_title}}</h1>

    </div>
</div>
<!-- END Page Title -->

<!-- BEGIN Main Content -->
{{-- <div class="row"> --}}

   <div class="box {{ $theme_color }}">
      <div class="box-title">
         <h3>
            <i class="fa fa-eye"></i>
           {{translation('library_content_details')}}  
         </h3>
         <div class="box-tool">
         </div>
      </div>
      
          <div class="clearfix"></div>
          <div class="box-content view-details-seciton-main details-section-main-block">
            <div class="row">
              <input type="hidden" name="type" id="type" value="{{isset($arr_data['library_content']['type']) ? $arr_data['library_content']['type'] : ''}}">
              <div id="book">
                    <div class="col-md-12">
                         <div class="details-infor-section-block">
                            {{translation('details')}}
                        </div>
                        <div class="form-group">
                           <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('title')}} </b>: </label>
                           <div class="col-sm-9 col-lg-4 controls">
                             {{isset($arr_data['title']) ? $arr_data['title'] : '-'}}
                           </div>
                           <div class="clearfix"></div>
                        </div>

                        <div class="form-group">
                           <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('author')}} </b>: </label>
                           <div class="col-sm-9 col-lg-4 controls">
                              {{isset($arr_data['author']) ? $arr_data['author'] : '-'}}
                           </div>
                           <div class="clearfix"></div>
                        </div>

                        <div class="form-group">
                           <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('edition')}} </b>: </label>
                           <div class="col-sm-9 col-lg-4 controls">
                              {{isset($arr_data['edition']) ? $arr_data['edition'] : '-'}}
                           </div>
                           <div class="clearfix"></div>
                        </div>

                        <div class="form-group">
                           <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('isbn_number')}}  </b>: </label>
                           <div class="col-sm-9 col-lg-4 controls">
                              {{isset($arr_data['ISBN_no']) ? $arr_data['ISBN_no'] : '-'}}
                           </div>
                           <div class="clearfix"></div>
                        </div>

                        <div class="form-group">
                           <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('book_number')}}  </b>: </label>
                           <div class="col-sm-9 col-lg-4 controls">
                              {{isset($arr_data['book_no']) ? $arr_data['book_no'] : '-'}}
                           </div>
                           <div class="clearfix"></div>
                        </div>

                        <div class="form-group">
                           <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('bill_number')}}  </b>: </label>
                           <div class="col-sm-9 col-lg-4 controls">
                              {{isset($arr_data['library_content']['bill_no']) ? $arr_data['library_content']['bill_no'] : '-'}}
                           </div>
                           <div class="clearfix"></div>
                        </div>

                        <div class="form-group">
                           <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('publisher')}}  </b>: </label>
                           <div class="col-sm-9 col-lg-4 controls">
                              {{isset($arr_data['publisher']) ? $arr_data['publisher'] : '-'}}
                           </div>
                           <div class="clearfix"></div>
                        </div>

                        <div class="form-group">
                           <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('book_category')}}  </b>: </label>
                           <div class="col-sm-9 col-lg-4 controls">
                              {{isset($category_name) ? $category_name: '-'}}
                           </div>
                           <div class="clearfix"></div>
                        </div>

                        <div class="form-group">
                           <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('purchase_date')}}  </b>: </label>
                           <div class="col-sm-9 col-lg-4 controls">
                              {{isset($arr_data['library_content']['purchase_date']) ? $arr_data['library_content']['purchase_date'] : '-'}}
                           </div>
                           <div class="clearfix"></div>
                        </div>

                        <div class="form-group">
                           <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('no_of_copies')}}  </b>: </label>
                           <div class="col-sm-9 col-lg-4 controls">
                              {{isset($arr_data['no_of_books']) ? $arr_data['no_of_books'] : '-'}}
                           </div>
                           <div class="clearfix"></div>
                        </div>
                       
                        <div class="form-group">
                           <label class="col-sm-4 col-lg-4 control-label"> <b>{{translation('shelf_no')}}</b> :</label>
                           <div class="col-sm-9 col-lg-4 controls">
                              {{isset($arr_data['shelf_no']) ? $arr_data['shelf_no'] : '-'}}
                           </div>
                           <div class="clearfix"></div>
                        </div>

                        <div class="form-group">
                           <label class="col-sm-4 col-lg-4 control-label"> <b>{{translation('book_position')}}</b> :</label>
                           <div class="col-sm-9 col-lg-4 controls">
                              {{isset($arr_data['book_position']) ? $arr_data['book_position'] : '-'}}
                           </div>
                           <div class="clearfix"></div>
                        </div>

                        <div class="form-group">
                           <label class="col-sm-4 col-lg-4 control-label"> <b>{{translation('book_cost')}}</b> :</label>
                           <div class="col-sm-9 col-lg-4 controls">
                              {{isset($arr_data['cost']) ? $arr_data['cost'] : '-'}} {{config('app.project.currency')}}
                           </div>
                           <div class="clearfix"></div>
                        </div>
                    </div>
                </div>

                <div id="dissertation" hidden="true">
                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('title')}} </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                       {{isset($arr_data['title']) ? $arr_data['title'] : '-'}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                           <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('author')}} </b>: </label>
                           <div class="col-sm-9 col-lg-4 controls">
                              {{isset($arr_data['author']) ? $arr_data['author'] : '-'}}
                           </div>
                           <div class="clearfix"></div>
                        </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('dissertation_number')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{isset($arr_data['book_no']) ? $arr_data['book_no'] : '-'}}
                     </div>
                     <div class="clearfix"></div>
                  </div> 

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('book_category')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{isset($category_name) ? $category_name: '-'}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"> <b>{{translation('shelf_no')}}</b> :</label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{isset($arr_data['shelf_no']) ? $arr_data['shelf_no'] : '-'}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"> <b>{{translation('book_position')}}</b> :</label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{isset($arr_data['book_position']) ? $arr_data['book_position'] : '-'}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"> <b>{{translation('academic_year')}}</b> :</label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{isset($arr_data['academic_year']['academic_year']) ? $arr_data['academic_year']['academic_year'] : '-'}}
                     </div>
                     <div class="clearfix"></div>
                  </div>


                </div>

                <div id="cd" hidden="true">

                <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('title')}} </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                       {{isset($arr_data['title']) ? $arr_data['title'] : '-'}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('cd_number')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{isset($arr_data['book_no']) ? $arr_data['book_no'] : '-'}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('cd_category')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{isset($category_name) ? $category_name: '-'}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"> <b>{{translation('shelf_no')}}</b> :</label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{isset($arr_data['shelf_no']) ? $arr_data['shelf_no'] : '-'}}
                     </div>
                     <div class="clearfix"></div>
                  </div>

                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"> <b>{{translation('cd_position')}}</b> :</label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{isset($arr_data['book_position']) ? $arr_data['book_position'] : '-'}}
                     </div>
                     <div class="clearfix"></div>
                  </div>
                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('bill_number')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{isset($arr_data['library_content']['bill_no']) ? $arr_data['library_content']['bill_no'] : '-'}}
                     </div>
                     <div class="clearfix"></div>
                  </div>
                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"><b> {{translation('purchase_date')}}  </b>: </label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{isset($arr_data['library_content']['purchase_date']) ? $arr_data['library_content']['purchase_date'] : '-'}}
                     </div>
                     <div class="clearfix"></div>
                  </div>
                  <div class="form-group">
                     <label class="col-sm-4 col-lg-4 control-label"> <b>{{translation('amount')}}</b> :</label>
                     <div class="col-sm-9 col-lg-4 controls">
                        {{isset($arr_data['cost']) ? $arr_data['cost'] : '-'}} {{config('app.project.currency')}}
                     </div>
                     <div class="clearfix"></div>
                  </div>
                </div>

                </div>
                <div class="form-group back-btn-form-block">
                   <div class="controls">
                      <a href="{{ $module_url_path }}" class="btn btn-primary" style="float: right;margin-top: 20px;" > <i class="fa fa-arrow-left"></i> {{translation('back')}} </a>
                   </div>
              </div><div class="clearfix"></div>
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

