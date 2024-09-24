@extends('parent.layout.master')    
@section('main_content')

     
        <div id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <a href="{{ url($parent_panel_slug.'/dashboard') }}">{{translation('dashboard')}}</a>
                </li>
                <span class="divider">
                    <i class="fa fa-angle-right"></i>
                </span> 
                <li class="active">
                  {{$module_title or translation('leave_application')}}
                </li>
            </ul>
        </div>
<!-- BEGIN Page Title -->
 <div class="page-title new-agetitle">
    <div>
        <h1><i class="fa {{$module_icon}}"></i>{{$module_title}}</h1>
    </div>
</div>

<!-- BEGIN Main Content -->
<div class="row">
<div class="col-md-12">
   <div class="box {{ $theme_color }}">
      <div class="box-title">
         <h3>
            <i class="fa {{$module_icon}}">
            </i>{{ isset($page_title)?$page_title:"" }} 
         </h3>
         <div class="box-tool">
         </div>
      </div>
      <div class="box-content edit-btns">
         @include('schooladmin.layout._operation_status')
         <div class="tobbable">
         {!! Form::open([ 'url' => $module_url_path.'/store',
         'method'=>'POST',
         'id'=>'validation-form1',
         'name'=>'validation-form1',
         'class'=>'form-horizontal', 
         'enctype'=>'multipart/form-data'
         ]) !!}
        
            <div class="col-md-12 ajax_messages">
              <div class="alert alert-danger" id="error" style="display:none;"></div>
              <div class="alert alert-success" id="success" style="display:none;"></div>
            </div>
           <div>
                  
                        <div >
                          <div class="form-group">
                                <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('reason_category')}} <i class="red">*</i></label>
                                <div class="col-sm-9 col-md-8 col-lg-4 controls">
                                   
                                   <select name="category" class="form-control" data-rule-required='true'>
                                     <option value="">{{translation('select_category')}}</option>
                                     <option value="illness">{{translation('illness')}}</option>
                                     <option value="travel">{{translation('travel')}}</option>
                                     <option value="other">{{translation('other')}}</option>
                                   </select>
                                    <span class='help-block'>{{ $errors->first('category') }}</span>
                                </div>
                          </div>

                          <div class="form-group">
                              <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('start_date')}} 
                                <i class="red">*</i></label>
                              <div class="col-sm-9 col-md-8 col-lg-4 controls">
                                    <input class="form-control datepikr"  data-rule-required='true' name="start_date" type="text" id="start_date" value="{{old('start_date')}}" placeholder="{{translation('enter_start_date')}}" style="cursor: pointer;" readonly />
                                    <span class='help-block'>{{ $errors->first('start_date') }}</span>
                              </div>
                          </div>

                          <div class="form-group">
                              <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('end_date')}} </label>
                              <div class="col-sm-9 col-md-8 col-lg-4 controls">
                                  <input class="form-control datepikr" name="end_date"
                                   id="end_date" placeholder="{{translation('enter_end_date')}}"  type="text" value="{{old('end_date')}}" readonly style="cursor: pointer;" />
                                  <span class='help-block'>{{ $errors->first('end_date') }}</span>
                                  <span  id="err_end_date" style="color: red;font-size: 10px"></span>
                              </div>
                          </div>  

                          <div class="form-group">
                              <label class="col-sm-3 col-md-4 col-lg-3 control-label">{{translation('reason')}} <i class="red">*</i></label>
                              <div class="col-sm-9 col-md-8 col-lg-4 controls">
                                  <textarea name="reason" class="form-control" data-rule-required='true' placeholder="{{translation('enter_reason')}}" rows="4">{{old('reason')}}</textarea>
                                  <span class='help-block'>{{ $errors->first('reason') }}</span>
                              </div>
                          </div>
                        </div>
           </div>
           <div class="form-group">
            <div class="col-sm-3 col-md-4 col-lg-3" >
            </div>
            <div class="col-sm-9 col-lg-4 controls">
               {{-- <a href="{{ $module_url_path }}" class="btn btn-primary">{{translation('back')}}</a>  --}}
               <input type="submit" name="save" value="{{translation('save')}}" class="btn btn-primary">
            </div>

          </div>
         {!! Form::close() !!}
      </div>
      </div>
   </div>
</div>
</div>



    <script>
        <?php 
            if(isset($academic_year) && !empty($academic_year))
            {
              $start_date = isset($academic_year['start_date'])?$academic_year['start_date']:'';
              $end_date   = isset($academic_year['end_date'])?$academic_year['end_date']:'';
            } 
        ?>

        var today = new Date();
        $(function() {
            $("#start_date").datepicker({
                todayHighlight: true,
                autoclose: true,
                format:'yyyy-mm-dd',
                startDate: today,
                endDate: "{{date($end_date)}}"
            });
        });   

        $(function() {
            $("#end_date").datepicker({
                todayHighlight: true,
                autoclose: true,
                format:'yyyy-mm-dd',
                startDate: "{{date($start_date)}}",
                endDate: "{{date($end_date)}}"
            });
        });   

        $("#validation-form1").on('submit',function(){
     
            var startDate = new Date($('#start_date').val());
            var endDate = new Date($('#end_date').val());

            if(startDate > endDate)
            {
              $('#err_end_date').text('{{translation('end_date_must_be_greater_than_or_equal_to_start_date')}}');
              return false; 
            }
            else
            {
              $('#err_end_date').text(''); 
              
            }
          });
    </script> 

    
<!-- END Main Content --> 
@endsection

