<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="author" content="" />
    <title>School Management</title>
    <!-- ======================================================================== -->
<!--    <link rel="icon" type="image/png" sizes="16x16" href="favicon.ico">-->
    <!-- Bootstrap CSS -->
    <!--base css styles-->
    <link rel="stylesheet" href="{{url('/')}}/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.min.css">
    <!--flaty css styles-->
    <link rel="stylesheet" href="{{url('/')}}/css/project-custome-css.css">
<!--    <link rel="stylesheet" href="{{url('/')}}/css/yogesh.css">-->
    <link rel="stylesheet" href="{{url('/')}}/css/admin/flaty.css">
    <link rel="stylesheet" href="{{url('/')}}/css/admin/flaty-responsive.css">
    <link rel="stylesheet" type="text/css" href="assets/bootstrap-datepicker/css/datepicker.css" />
    <link rel="stylesheet" href="{{url('/')}}/css/admin/select2.min.css">
    <script src="{{url('/')}}/js/admin/jquery-1.11.3.min.js"></script>
    <script src="{{url('/')}}/assets/bootstrap/js/bootstrap.min.js"></script>
    <!-- DatePicker -->
    <script type="text/javascript" src="{{url('/')}}/assets/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>

</head>

<body class="skin-navy_blue">
    <div class="student-panel-main">
        <div class="flex-main login-pgs-stdent">
            <div class="login-container">
                <div class="login-wrapper">
                    <div class="main-loginpage">
                        <div class="logo-content d-flex align-items-center justify-content-center">
                            <div class="masterset-uplogo">
                                <div class="logo-img-loign">
                                    <img src="{{url('/')}}/images/admin/school-logo-color.png" alt="" />
                                </div>
                                    {{translation('school_management_system',$locale)}}
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <!-- BEGIN Main Content -->
                        <div class="login-content">
                            <!-- BEGIN Change Email Form -->

                            {!! Form::open([ 'url' => 'login/role_login',
                                 'method'=>'GET',
                                 'id'=>'validation-form1',
                                 'name'=>'validation-form1',
                                 'class'=>'form-horizontal'
                                ]) !!}   
                           

                        @if (Session::has('flash_notification.message'))
                                    <div class="alert alert-{{ Session::get('flash_notification.level') }}">
                                        {!! Session::get('flash_notification.message') !!}
                                    </div>
                                @endif
                                
                                <h3>{{translation('select_role',$locale)}}</h3>
                                <hr>

                                <div class="" id="message"></div>
                                 <div class="form-group">
                                    <label for="role">{{translation('role',$locale)}}</label>
                                    <select name="role" class="form-control" data-rule-required="true">
                                        @if(!empty($roles))
                                        
                                            <option value="">{{translation('select_role',$locale)}}</option>
                                            @if(isset($roles)&&$roles!='')
                                                @foreach($roles as $key => $value)
                                                    <option value="{{$value}}">
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            @endif
                                         @endif
                                    </select>
                                    <div class="error">{{ $errors->first('role') }}</div>
                                    <div class="icn-login-stn">
                                        <i class="fa fa-home"></i>
                                    </div>

                                </div> 
                                <div class="form-group">
                                    <div class="controls btn-loginpg">
                                        <button type="submit" class="btn btn-primary" id="submit_button">{{translation('go')}}</button>
                                    </div>
                                </div>

                                <input type="hidden" name="locale" value="{{$locale}}">
                        
                    {!! Form::close() !!}

                            <!-- END Change Email Form -->
                        </div>
                        <!-- END Main Content -->
                    </div>
                </div>
            </div>
        </div>
    </div>

        <script>window.jQuery || document.write('<script src="{{ url('/') }}/assets/jquery/2.1.4/jquery.min.js"><\/script>')</script>
       

        <script>window.jQuery || document.write('<script src="{{ url('/') }}/assets/jquery/jquery-2.1.4.min.js"><\/script>')</script>
        <script src="{{ url('/') }}/assets/bootstrap/js/bootstrap.min.js"></script>
        <script src="{{ url('/') }}/assets/jquery-slimscroll/jquery.slimscroll.min.js"></script>
        <script src="{{ url('/') }}/assets/jquery-cookie/jquery.cookie.js"></script>


        <script type="text/javascript" src="{{ url('/') }}/assets/jquery-validation/dist/jquery.validate.min.js"></script>
        <script type="text/javascript" src="{{ url('/') }}/assets/jquery-validation/dist/additional-methods.min.js"></script>
        <script type="text/javascript" src="{{ url('/') }}/assets/chosen-bootstrap/chosen.jquery.min.js"></script>
        


    <!--flaty scripts-->
        <script src="{{ url('/') }}/js/admin/flaty.js"></script>
        <script src="{{ url('/') }}/js/admin/flaty-demo-codes.js"></script>
        <script src="{{ url('/') }}/js/admin/validation.js"></script>

      

</body>

</html>