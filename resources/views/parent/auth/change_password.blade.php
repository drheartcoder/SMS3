    @extends('parent.layout.master')                
    @section('main_content')

   <!-- BEGIN Page Title -->
    <div class="page-title new-agetitle">
        <div>
            <h1><i class="fa fa-pencil"></i>{{translation('change_password')}}</h1>
        </div>
    </div>
    <!-- END Page Title -->

    <!-- BEGIN Breadcrumb -->
    <div id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url('/'.$parent_panel_slug.'/dashboard') }}">{{translation('dashboard')}}</a>
            </li>
            <span class="divider">
                <i class="fa fa-angle-right"></i>
            </span>
            <i class="fa fa-pencil"></i>
            <li class="active">{{translation('change_password')}}</li>
        </ul>
    </div>
    <!-- END Breadcrumb -->

    <!-- BEGIN Tiles -->
        <div class="row">
            <div class="col-md-12">
                <div class="box  box-navy_blue">
                    <div class="box-title">
                        <h3><i class="fa fa-wrench"></i> {{translation('change_password')}}</h3>
                        <div class="box-tool">
                        </div>
                    </div>
                    <div class="box-content">

                    @include('admin.layout._operation_status')  

                    {!! Form::open([ 'url' => $parent_panel_slug.'/update_password',
                                 'method'=>'POST',
                                 'id'=>'validation-form1',
                                 'name'=>'validation-form1',
                                 'class'=>'form-horizontal'
                                ]) !!}   
                            {{ csrf_field() }}

                        <div class="form-group-nms">
                            <div class="col-sm-3 col-lg-2"></div>
                            <div class="col-sm-12 col-lg-8"> Password Details</div>
                            <div class="clearfix"></div>
                        </div>

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
                               <!-- <a class="btn btn btn-primary" href="{{ url('/'.$parent_panel_slug.'/change_password') }}">Cancel</a> -->
                                {!! Form::Submit(translation('save'),['class'=>'btn btn-primary']) !!}        
                            </div>
                       </div>
                    {!! Form::close() !!}

                    </div>
                </div>
            </div>
        </div>
        <!-- END Main Content -->


@stop