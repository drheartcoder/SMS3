    @extends('schooladmin.layout.master')
    @section('main_content')
    <!-- BEGIN Breadcrumb -->
    <div id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url('/'.$school_admin_panel_slug.'/dashboard') }}">{{translation('dashboard')}}</a>
            </li>
            <span class="divider">
                <i class="fa fa-angle-right"></i>
            </span>
            <i class="fa fa-pencil"></i>
            <li class="active">{{'Change Passowrd'}}</li>
        </ul>
    </div>
    <!-- END Breadcrumb -->
<!-- BEGIN Page Title -->
<div class="page-title new-agetitle">
    <div>
        <h1><i class="{{$module_icon}}"></i>{{translation('change_password')}}</h1>

    </div>
</div>
<!-- END Page Title -->
    

    <!-- BEGIN Main Content -->
    
    
    <div class="row">
        <div class="col-md-12">
            <div class="box {{ $theme_color }}">
                <div class=" box-title">
                    <h3><i class="fa fa-file"></i>{{translation('change_password')}}</h3>
                    <div class="box-tool">
                    </div>
                </div>
                <div class="box-content">

                    @include('admin.layout._operation_status')  

                    
                    {!! Form::open([ 'url' => $school_admin_panel_slug.'/update_password',
                                 'method'=>'POST',
                                 'id'=>'validation-form1',
                                 'name'=>'validation-form1',
                                 'class'=>'form-horizontal',
                                 'onsubmit'=>"return addLoader()"
                                ]) !!} 
                                    
                            {{ csrf_field() }}
                        <div class="form-group">
                            <label class="col-sm-3 col-lg-2 control-label">{{translation('current_password')}}<i class="red">*</i></label> 
                            <div class="col-sm-9 col-lg-4 controls">

                                {!! Form::password('current_password',['class'=>'form-control',
                                        'data-rule-required'=>'true',
                                        'id'=>'current_password',
                                        'placeholder'=>translation('current_password')]) !!}
                                
                                <span class='help-block'>{{ $errors->first('current_password') }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 col-lg-2 control-label">{{translation('new_password')}}<i class="red">*</i></label>
                            <div class="col-sm-9 col-lg-4 controls">
                                
                                {!! Form::password('new_password',['class'=>'form-control',
                                        'data-rule-required'=>'true',
                                        'data-rule-minlength'=>'6',
                                        'id'=>'new_password',
                                        'placeholder'=>translation('new_password')]) !!}

                                <span class='help-block'>{{ $errors->first('new_password') }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 col-lg-2 control-label">{{translation('re_type_new_password')}}<i class="red">*</i></label>
                            <div class="col-sm-9 col-lg-4 controls">
                                

                                {!! Form::password('new_password_confirmation',['class'=>'form-control',
                                        'data-rule-required'=>'true',
                                        'data-rule-equalto'=>'#new_password',
                                        'id'=>'new_password_confirmation',
                                        'placeholder'=>translation('re_type_new_password')]) !!}


                                <span class='help-block'>{{ $errors->first('new_password_confirmation') }}</span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2">

                                <button type="submit"  id="submit_button" class="btn btn-primary">{{translation('update')}}</button>

                            </div>
                       </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>


    <!-- END Main Content -->


@stop